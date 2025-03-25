<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PurchaseController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Purchases';
    }

    protected function getPurchases()
    {
        return Purchase::searchable(['invoice_no', 'due_amount', 'paid_amount', 'supplier:name,mobile', 'warehouse:name'])->dateFilter('purchase_date')->with('supplier', 'warehouse', 'purchaseReturn')->orderBy('id', 'desc');;
    }

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $purchases = $this->getPurchases()->paginate(getPaginate());
        return view('admin.purchase.index', compact('pageTitle', 'purchases'));
    }

    public function purchasePDF()
    {
        $pageTitle = $this->pageTitle;
        $purchases = $this->getPurchases()->get();
        return downloadPDF('pdf.purchase.list', compact('pageTitle', 'purchases'));
    }

    public function purchaseCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getPurchases()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Invoice No.,Date,Supplier,Mobile,Total Amount,Warehouse,Discount,Payable,Paid,Due\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $purchase) {
            $date           = showDateTime(@$purchase->purchase_date, 'd-m-Y');
            $supplier       = $purchase->supplier->name;
            $supplierMobile = $purchase->supplier->mobile;
            $totalAmount    = $curSym . getAmount($purchase->total_price);
            $warehouse      = $purchase->warehouse->name;
            $discount       = $curSym . getAmount($purchase->discount_amount);
            $payable        = $curSym . getAmount($purchase->payable_amount);
            $paid           = $curSym . getAmount($purchase->paid_amount);
            $due            = $curSym . getAmount($purchase->due_amount);

            $column .= "$purchase->invoice_no,$date,$supplier,$supplierMobile,$totalAmount,$warehouse,$discount,$payable,$paid,$due \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function addNew()
    {
        $pageTitle  = 'Add Purchase';
        $suppliers  = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::active()->orderBy('name')->get();
        return view('admin.purchase.form', compact('pageTitle', 'suppliers', 'warehouses'));
    }

    public function downloadDetails($id)
    {
        $pageTitle = "Purchase Details";
        $purchase  = Purchase::where('id', $id)->with('supplier', 'purchaseDetails', 'purchaseDetails.product', 'purchaseDetails.product.unit')->whereHas('purchaseDetails')->firstOrFail();
        $supplier  = $purchase->supplier;
        return downloadPDF('pdf.purchase.details', compact('pageTitle', 'purchase', 'supplier'));
    }

    public function edit($id)
    {
        $purchase = Purchase::where('id', $id)
            ->whereHas('purchaseDetails')
            ->with('purchaseDetails', 'purchaseDetails.product', 'purchaseDetails.product.unit')->firstOrFail();

        $pageTitle  = 'Edit Purchase';
        $suppliers  = Supplier::orderBy('id', 'desc')->get();
        $warehouses = Warehouse::active()->get();
        return view('admin.purchase.form', compact('pageTitle', 'purchase', 'suppliers', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $this->products   = collect($request->products);
        $this->productIds = $this->products->pluck('product_id')->toArray();
        $this->totalPrice = $this->getTotalPrice();

        if ($request->discount > $this->totalPrice) {
            $notify[] = ['error', 'Discount amount mustn\'t be greater than total price'];
            return back()->withNotify($notify)->withInput();
        }
        $purchase = $this->savePurchaseData(new Purchase());
        $this->storeStock($request);
        $this->storePurchaseDetails($purchase);

        Action::newEntry($purchase, 'CREATED');

        $notify[] = ['success', 'Purchase data added successfully'];
        return to_route('admin.purchase.edit', $purchase->id)->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);
        $this->products   = collect($request->products);
        $this->totalPrice = $this->getTotalPrice();
        $this->productIds = $this->products->pluck('product_id')->toArray();

        if ($request->discount > $this->totalPrice) {
            $notify[] = ['error', 'Discount amount mustn\'t greater than total price'];
            return back()->withNotify($notify)->withInput();
        }

        $purchase = Purchase::with('purchaseDetails')->findOrFail($id);

        if ($purchase->return_status) {
            $notify[] = ['error', 'You can\'t update this purchase'];
            return back()->withNotify($notify);
        }

        $this->productStocks = ProductStock::where('warehouse_id', $request->warehouse_id)->whereIn('product_id', $this->productIds)->get();
        $this->oldStocks     = collect([]);

        if ($request->warehouse_id != $purchase->warehouse_id) {
            $this->oldStocks = ProductStock::where('warehouse_id', $purchase->warehouse_id)->whereIn('product_id', $this->productIds)->get();
        }

        $this->purchaseDetails = $purchase->purchaseDetails;
        $this->oldWarehouseId  = $purchase->warehouse_id;

            // If reduce the quantity then we need to check the current stock.
        $checkStock = $this->checkStockAvailability();

        if (!empty($checkStock)) {
            return $checkStock;
        }

        $purchase = $this->savePurchaseData($purchase);
        $this->updateStock($purchase);
        $this->storePurchaseDetails($purchase);
        Action::newEntry($purchase, 'UPDATED');
        $notify[] = ['success', 'Purchase data updated successfully'];
        return back()->withNotify($notify);
    }

    protected function updateStock($purchase)
    {
        foreach ($this->products as $product) {
            $product        = (object) $product;
            $purchaseDetail = $this->purchaseDetails->where('product_id', $product->product_id)->first();
            $quantity       = $product->quantity - @$purchaseDetail->quantity ?? 0;
            $newStock       = $this->productStocks->where('product_id', $product->product_id)->first();

            if ($this->oldStocks->count()) {
                $oldStock = $this->oldStocks->where('product_id', $product->product_id)->first();
                if ($oldStock) {
                    $quantity            = $product->quantity;
                    $oldStock->quantity -= @$purchaseDetail->quantity ?? 0;
                    $oldStock->save();
                }
            }

            if ($newStock) {
                $newStock->quantity += $quantity;
            } else {
                $newStock           = new ProductStock();
                $newStock->quantity = $quantity;
            }

            $newStock->warehouse_id = $purchase->warehouse_id;
            $newStock->product_id   = $product->product_id;
            $newStock->save();
        }
    }

    protected function storeStock($request)
    {
        $productStock = ProductStock::where('warehouse_id', $request->warehouse_id)->whereIn('product_id', $this->productIds)->get();

        foreach ($this->products as $product) {
            $product       = (object) $product;
            $previousStock = $productStock->where('product_id', $product->product_id)->first();

            if ($previousStock) {
                $previousStock->quantity += $product->quantity;
                $previousStock->save();
            } else {
                $stock               = new ProductStock();
                $stock->warehouse_id = $request->warehouse_id;
                $stock->product_id   = $product->product_id;
                $stock->quantity     = $product->quantity;
                $stock->save();
            }
        }
    }

    protected function storePurchaseDetails($purchase)
    {
        $purchaseDetails = $this->purchaseDetails ?? null;

        foreach ($this->products as $product) {
            $product        = (object) $product;
            $purchaseDetail = new PurchaseDetails();

            if ($purchaseDetails) {
                $purchaseDetail = $purchaseDetails->where('product_id', $product->product_id)->first() ?? new PurchaseDetails();
            }

            $purchaseDetail->purchase_id = $purchase->id;
            $purchaseDetail->product_id  = $product->product_id;
            $purchaseDetail->quantity    = $product->quantity;
            $purchaseDetail->price       = $product->price;
            $purchaseDetail->total       = $product->quantity * $product->price;
            $purchaseDetail->save();
        }
    }

    protected function savePurchaseData($purchase)
    {
        $request   = request();
        $discount  = $request->discount ?? 0;
        $payable   = $this->totalPrice - $discount;
        $dueAmount = $payable - $purchase->paid_amount ?? 0;

        $purchase->invoice_no      = $request->invoice_no;
        $purchase->supplier_id     = $request->supplier_id;
        $purchase->warehouse_id    = $request->warehouse_id;
        $purchase->purchase_date   = Carbon::parse($request->purchase_date);
        $purchase->total_price     = $this->totalPrice;
        $purchase->discount_amount = $discount;
        $purchase->payable_amount  = $payable;
        $purchase->due_amount      = $dueAmount;
        $purchase->note            = $request->note;
        $purchase->save();

        return $purchase;
    }

    protected function validation($request)
    {
        $request->validate([
            'invoice_no'            => 'required',
            'supplier_id'           => 'required|exists:suppliers,id',
            'warehouse_id'          => 'required|exists:warehouses,id',
            'purchase_date'         => 'required|date_format:Y-m-d',
            'products'              => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|gt:0',
            'products.*.quantity'   => 'required|integer|gt:0',
            'products.*.price'      => 'required|numeric|gt:0',
            'discount'              => 'nullable|numeric|gte:0',
            'note'                  => 'nullable|string',
        ]);
    }

    protected function checkStockAvailability()
    {
        $products = $this->products;
        $notify   = [];

        foreach ($products as $product) {
            $product = (object) $product;

            $purchaseDetail = $this->purchaseDetails->where('product_id', $product->product_id)->first();

            if ($purchaseDetail) {
                $quantity     = $product->quantity - @$purchaseDetail->quantity ?? 0;
                $productStock = $this->productStocks->where('product_id', $product->product_id)->first();

                if ($this->oldStocks->count()) {
                    $oldStock = $this->oldStocks->where('product_id', $product->product_id)->first();

                    if ($oldStock) {
                        $newStock = $oldStock->quantity - $product->quantity;
                        if ($newStock < 0) {
                            $notify[] = ['error', 'You can\'t decrease the quantity because this product may already be sold'];
                            return back()->withNotify($notify)->withInput();
                        }
                    }
                }

                if ($productStock) {
                    $newStock = $productStock->quantity + $quantity;
                    if ($newStock < 0) {
                        $notify[] = ['error', 'You can\'t decrease the quantity because this product may already be sold'];
                        return back()->withNotify($notify)->withInput();
                    }
                }
            }
        }

        return $notify;
    }

    public function productSearch()
    {
        $products = Product::select('id', 'name', 'sku', 'unit_id')->searchable(['name', 'sku'])->with('unit:id,name')->get();
        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }


    protected function getTotalPrice()
    {
        return $this->products->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        }, 1);
    }
}
