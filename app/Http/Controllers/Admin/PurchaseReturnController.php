<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PurchaseReturnController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Purchase Return';
    }

    protected function getPurchaseReturn()
    {
        return PurchaseReturn::searchable(['due_amount', 'received_amount', 'supplier:name, mobile', 'purchase:invoice_no', 'purchase:invoice_no', 'purchase.warehouse:name'])->dateFilter('return_date')->with('purchase.supplier', 'supplier', 'purchase.warehouse')->orderBy('id', 'desc');
    }

    public function index()
    {
        $pageTitle      = $this->pageTitle;
        $purchaseReturn = $this->getPurchaseReturn()->paginate(getPaginate());
        return view('admin.purchase_return.index', compact('pageTitle', 'purchaseReturn'));
    }

    public function purchaseReturnPDF()
    {
        $pageTitle      = $this->pageTitle;
        $purchaseReturn = $this->getPurchaseReturn()->get();
        return downloadPDF('pdf.purchase_return.list', compact('pageTitle', 'purchaseReturn'));
    }

    public function purchaseReturnCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getPurchaseReturn()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Invoice No.,Date,Supplier,Mobile,Total Amount,Warehouse,Discount,Receivable,Paid,Due\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $return) {
            $invoice        = $return->purchase->invoice_no;
            $date           = showDateTime(@$return->return_date, 'd-m-Y');
            $supplier       = $return->purchase->supplier->name;
            $supplierMobile = $return->purchase->supplier->mobile;
            $warehouse      = $return->purchase->warehouse->name;
            $totalAmount    = $curSym . getAmount($return->total_price);
            $discount       = $curSym . getAmount($return->discount_amount);
            $receivable     = $curSym . getAmount($return->receivable_amount);
            $received       = $curSym . getAmount($return->received_amount);
            $due            = $curSym . getAmount($return->due_amount);

            $column .= "$invoice,$date,$supplier,$supplierMobile,$totalAmount,$warehouse,$discount,$receivable,$received,$due \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }


    public function downloadDetails($id)
    {
        $pageTitle      = "Purchase Return Details";
        $purchaseReturn = PurchaseReturn::where('id', $id)->with('purchase.supplier', 'details', 'details.product', 'details.product.unit')->whereHas('details')->firstOrFail();
        $supplier       = $purchaseReturn->purchase->supplier;
        $supplier       = $purchaseReturn->purchase->supplier;
        return downloadPDF('pdf.purchase_return.details', compact('pageTitle', 'purchaseReturn', 'supplier'));
    }

    public function newReturn($id)
    {
        $purchase = Purchase::where('id', $id)->where('return_status',  Status::NO)
            ->whereHas('purchaseDetails')
            ->with('purchaseDetails.product.productStock.warehouse:id,name', 'purchaseDetails.product.unit:id,name', 'supplier:id,name')->firstOrFail();

        $pageTitle   = 'Purchase Return';
        $route       = route('admin.purchase.return.store', $purchase->id);
        $detailsData = $purchase->purchaseDetails;
        $edit        = 0;
        return view('admin.purchase_return.form', compact('pageTitle', 'purchase', 'route', 'edit', 'detailsData'));
    }

    public function edit($id)
    {
        $purchaseReturn = PurchaseReturn::where('id', $id)
            ->whereHas('details')
            ->with('details.product.productStock', 'details.product.productStock.warehouse:id,name', 'details.product.unit:id,name', 'purchase.supplier:id,name', 'purchase.purchaseDetails', 'purchase.purchaseDetails.product')
            ->firstOrFail();

        $pageTitle = 'Edit Purchase Return';
        $route     = route('admin.purchase.return.update', $id);

        $purchase    = $purchaseReturn->purchase;
        $detailsData = $purchaseReturn->details;
        $edit        = 1;
        return view('admin.purchase_return.form', compact('pageTitle', 'purchaseReturn', 'route', 'purchase', 'edit', 'detailsData'));
    }

    public function store(Request $request, $id)
    {
        $this->validation($request);

        $this->purchase   = Purchase::where('id', $id)->firstOrFail();
        $details          = $this->purchase->purchaseDetails;
        $this->products   = collect($request->products);
        $this->productIds = $this->products->pluck('product_id')->toArray();

        foreach ($this->productIds as $pId) {
            $priceData    = (object) collect($request->products)->where('product_id', $pId)->first();
            $requestPrice = $priceData->price;
            $actualPrice  = $details->where('product_id', $pId)->first()->price;
            if ($requestPrice != $actualPrice) {
                $notify[] = ['error', 'You can\'t change the price of any product'];
                return back()->withNotify($notify);
            }
        }

        $this->totalPrice = $this->getTotalPrice();

        if ($this->totalPrice) {
            $this->productStocks = ProductStock::where('warehouse_id',  $this->purchase->warehouse_id)->whereIn('product_id', $this->productIds)->get();
            $purchaseReturn      = $this->savePurchaseReturnData(new PurchaseReturn());

            $this->updateStock($purchaseReturn->id);
            $this->storePurchaseReturnDetails($purchaseReturn->id);
        } else {
            $notify[] = ['error', 'Purchase return Quantity items is empty'];
            return back()->withNotify($notify);
        }

        Action::newEntry($purchaseReturn, 'CREATED');

        $notify[] = ['success', 'Purchase return data added successfully'];
        return to_route('admin.purchase.return.edit', $purchaseReturn->id)->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, true);
        $this->products       = collect($request->products);
        $this->purchaseReturn = PurchaseReturn::with('details')->findOrFail($id);
        $this->productIds     = $this->products->pluck('product_id')->toArray();
        $details              = $this->purchaseReturn->details;

        foreach ($this->productIds as $pId) {
            $priceData    = (object) collect($request->products)->where('product_id', $pId)->first();
            $requestPrice = $priceData->price;
            $actualPrice  = $details->where('product_id', $pId)->first()->price;
            if ($requestPrice != $actualPrice) {
                $notify[] = ['error', 'You can\'t change the price of any product'];
                return back()->withNotify($notify);
            }
        }

        $this->totalPrice = $this->getTotalPrice();

        if ($request->discount > $this->totalPrice) {
            $notify[] = ['error', 'Discount amount mustn\'t greater than total price'];
            return back()->withNotify($notify)->withInput();
        }

        $this->purchaseReturn = PurchaseReturn::with('details')->findOrFail($id);
        $this->purchase       = Purchase::where('id', $this->purchaseReturn->purchase_id)->first();
        $this->productStocks  = ProductStock::where('warehouse_id',  $this->purchase->warehouse_id)->whereIn('product_id', $this->productIds)->get();

        // If reduce the quantity then we need to check the current stock.
        $checkStock = $this->checkStockAvailability();

        if (!empty($checkStock)) {
            return $checkStock;
        }

        $purchaseReturn = $this->savePurchaseReturnData($this->purchaseReturn);

        $this->updateStock($purchaseReturn->id);
        $this->storePurchaseReturnDetails($purchaseReturn->id);

        Action::newEntry($purchaseReturn, 'UPDATED');

        $notify[] = ['success', 'Purchase Return data updated successfully'];
        return back()->withNotify($notify);
    }

    protected function updateStock($returnId)
    {

        foreach ($this->products as $product) {
            $product       = (object) $product;
            $returnDetails = PurchaseReturnDetails::where('purchase_return_id', $returnId)->where('product_id', $product->product_id)->first();

            $quantity = $product->quantity - @$returnDetails->quantity ?? 0;
            $stock    = $this->productStocks->where('product_id', $product->product_id)->first();

            $stock->quantity   -= $quantity;
            $stock->product_id  = $product->product_id;
            $stock->save();
        }
    }

    protected function storePurchaseReturnDetails($returnId)
    {
        foreach ($this->products as $product) {
            $product = (object) $product;
            if ($product->quantity > 0) {
                $returnDetails                     = PurchaseReturnDetails::where('purchase_return_id', $returnId)->where('product_id', $product->product_id)->first() ?? new PurchaseReturnDetails();
                $returnDetails->purchase_return_id = $returnId;
                $returnDetails->product_id         = $product->product_id;
                $returnDetails->quantity           = $product->quantity;
                $returnDetails->price              = $product->price;
                $returnDetails->total              = $product->quantity * $product->price;
                $returnDetails->save();
            }
        }
    }

    protected function savePurchaseReturnData($purchaseReturn)
    {
        $request                           = request();
        $discount                          = $request->discount ?? 0;
        $receivable                        = $this->totalPrice - $discount;
        $dueAmount                         = $receivable - $purchaseReturn->received_amount ?? 0;
        $purchaseReturn->purchase_id       = $this->purchase->id;
        $purchaseReturn->supplier_id       = $this->purchase->supplier_id;
        $purchaseReturn->return_date       = Carbon::parse($request->return_date);
        $purchaseReturn->total_price       = $this->totalPrice;
        $purchaseReturn->discount_amount   = $discount;
        $purchaseReturn->receivable_amount = $receivable;
        $purchaseReturn->due_amount        = $dueAmount;
        $purchaseReturn->note              = $request->note;

        if ($this->purchase->return_status == 0) {
            $purchase                = $this->purchase->where('id', $this->purchase->id)->first();
            $purchase->return_status = 1;
            $purchase->save();
        }

        $purchaseReturn->save();
        return $purchaseReturn;
    }

    protected function validation($request, $isUpdate = false)
    {
        $qtyValidation = $isUpdate ? 'gt:0' : 'gte:0';

        $request->validate([
            'return_date'           => 'required|date_format:Y-m-d',
            'products'              => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|gt:0',
            'products.*.quantity'   => "required|integer|$qtyValidation",
            'products.*.price'      => 'required|numeric|gte:0',
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

            $returnDetail   = PurchaseReturnDetails::where('product_id', $product->product_id)->first();
            $purchaseDetail = PurchaseDetails::where('purchase_id', $this->purchase->id)->where('product_id', $product->product_id)->first();

            if ($purchaseDetail->quantity < $product->quantity) {
                $notify[] = ['error', 'You can\'t return, because this product exceed your purchase quantity'];
                return back()->withNotify($notify)->withInput();
            }

            $productStock = $this->productStocks->where('product_id', $product->product_id)->first();
            if ($productStock) {
                $newStock = $returnDetail->quantity +  $productStock->quantity;
                if ($newStock < $product->quantity) {
                    $notify[] = ['error', 'You can\'t return the quantity because this product may already be sold'];
                    return back()->withNotify($notify)->withInput();
                }
            }
        }

        return $notify;
    }


    protected function getTotalPrice()
    {
        return $this->products->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        }, 1);
    }
}
