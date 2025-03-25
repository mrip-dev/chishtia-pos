<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use Carbon\Carbon;

use Illuminate\Http\Request;

class SaleReturnController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Sales Return';
    }

    protected function getSaleReturn()
    {
        return SaleReturn::searchable(['due_amount', 'customer:name,mobile', 'sale:invoice_no'])->dateFilter('return_date')->with('sale.warehouse', 'customer')->orderBy('id', 'desc');
    }

    public function index()
    {
        $pageTitle   = $this->pageTitle;
        $saleReturns = $this->getSaleReturn()->paginate(getPaginate());

        return view('admin.sale_return.index', compact('pageTitle', 'saleReturns'));
    }

    public function saleReturnPDF()
    {
        $pageTitle   = $this->pageTitle;
        $saleReturns = $this->getSaleReturn()->get();
        return downloadPDF('pdf.sale_return.list', compact('pageTitle', 'saleReturns'));
    }

    public function saleReturnCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getSaleReturn()->get());
        return response()->download(...$filename);
    }


    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Invoice No.,Date,Customer,Mobile,Total Amount,Warehouse,Discount,Receivable,Paid,Due\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $return) {
            $invoice        = $return->sale->invoice_no;
            $date           = showDateTime(@$return->return_date, 'd-m-Y');
            $customer       = $return->customer->name;
            $customerMobile = $return->customer->mobile;
            $warehouse      = $return->sale->warehouse->name;
            $totalAmount    = $curSym . getAmount($return->total_price);
            $discount       = $curSym . getAmount($return->discount_amount);
            $payable        = $curSym . getAmount($return->payable_amount);
            $paid           = $curSym . getAmount($return->paid_amount);
            $due            = $curSym . getAmount($return->due_amount);

            $column .= "$invoice,$date,$customer,$customerMobile,$totalAmount,$warehouse,$discount,$payable,$paid,$due \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function downloadInvoice($id)
    {
        $pageTitle  = "Return INVOICE";
        $saleReturn = SaleReturn::where('id', $id)->with('customer', 'details', 'details.product', 'details.product.unit')->whereHas('details')->firstOrFail();

        $customer = $saleReturn->customer;
        $sale     = $saleReturn->sale;
        return downloadPDF('pdf.sale_return.invoice', compact('pageTitle', 'saleReturn', 'customer', 'sale'));
    }

    public function newReturn($id)
    {
        $sale = Sale::where('id', $id)->where('return_status', Status::NO)->whereHas('saleDetails')
            ->with('saleDetails.product.productStock.warehouse', 'saleDetails.product.unit', 'customer:id,name', 'warehouse:id,name')->firstOrFail();
        $pageTitle = 'Sale Return';

        $route       = route('admin.sale.return.store', $sale->id);
        $detailsData = $sale->saleDetails;
        $edit        = 0;
        return view('admin.sale_return.form', compact('pageTitle', 'sale', 'detailsData', 'edit', 'route'));
    }

    public function edit($id)
    {
        $saleReturn = SaleReturn::where('id', $id)
            ->whereHas('details')
            ->with('details.product.productStock.warehouse', 'details.product.unit', 'sale.customer:id,name', 'sale.warehouse:id,name', 'sale.saleDetails')->firstOrFail();

        $pageTitle   = 'Edit Sale Return';
        $route       = route('admin.sale.return.update', $id);
        $sale        = $saleReturn->sale;
        $detailsData = $saleReturn->details;
        $edit        = 1;

        return view('admin.sale_return.form', compact('pageTitle', 'saleReturn', 'sale', 'detailsData', 'edit', 'route'));
    }

    public function store(Request $request, $id)
    {
        $this->validation($request);

        $this->sale       = Sale::findOrFail($id);
        $details          = $this->sale->saleDetails;
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

        if ($request->discount > $this->totalPrice) {
            $notify[] = ['error', 'Discount amount mustn\'t be greater than total price'];
            return back()->withNotify($notify)->withInput();
        }

        if ($this->totalPrice) {
            $this->productStocks = ProductStock::where('warehouse_id',  $this->sale->warehouse_id)->whereIn('product_id', $this->productIds)->get();
            $saleReturn          = $this->saveSaleReturnData(new SaleReturn());

            $this->updateStock($saleReturn->id);
            $this->storeSaleReturnDetails($saleReturn->id);
        } else {
            $notify[] = ['error', 'Sale return Quantity items is empty'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Sale return data added successfully'];

        Action::newEntry($saleReturn, 'CREATED');
        return to_route('admin.sale.return.edit', $saleReturn->id)->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $this->validation($request);
        $this->products   = collect($request->products);
        $this->saleReturn = SaleReturn::with('sale.saleDetails', 'details')->findOrFail($id);


        $this->productIds = $this->products->pluck('product_id')->toArray();
        $details          = $this->saleReturn->details;

        foreach ($this->productIds as $pId) {
            $priceData    = (object) collect($request->products)->where('product_id', $pId)->first();
            $requestPrice = $priceData->price;
            $actualPrice  = $details->where('product_id', $pId)->first()->price ?? 0;
            if ($requestPrice != $actualPrice) {
                $notify[] = ['error', 'You can\'t change the price of any product'];
                return back()->withNotify($notify);
            }
        }

        $this->totalPrice    = $this->getTotalPrice();
        $this->sale          = Sale::where('id', $this->saleReturn->sale_id)->first();
        $this->productStocks = ProductStock::where('warehouse_id',  $this->sale->warehouse_id)->whereIn('product_id', $this->productIds)->get();

          // If reduce the quantity then we need to check the current stock.
        $checkStock = $this->checkStockAvailability();
        if (!empty($checkStock)) {
            return $checkStock;
        }

        $saleReturn = $this->saveSaleReturnData($this->saleReturn);
        $this->updateStock($saleReturn->id);
        $this->storeSaleReturnDetails($saleReturn->id);

        Action::newEntry($saleReturn, 'UPDATED');

        $notify[] = ['success', 'Sale Return data updated successfully'];
        return back()->withNotify($notify);
    }

    protected function updateStock($returnId)
    {
        foreach ($this->products as $product) {
            $product       = (object) $product;
            $returnDetails = SaleReturnDetails::where('sale_return_id', $returnId)->where('product_id', $product->product_id)->first();

            $quantity = $product->quantity - @$returnDetails->quantity ?? 0;
            $stock    = $this->productStocks->where('product_id', $product->product_id)->first();

            $stock->quantity   += $quantity;
            $stock->product_id  = $product->product_id;
            $stock->save();

            $product              = Product::find($product->product_id);
            $product->total_sale -= $quantity;
            $product->save();
        }
    }

    protected function storeSaleReturnDetails($returnId)
    {
        foreach ($this->products as $product) {
            $product = (object) $product;
            if ($product->quantity > 0) {
                $returnDetails                 = SaleReturnDetails::where('sale_return_id', $returnId)->where('product_id', $product->product_id)->first() ?? new SaleReturnDetails();
                $returnDetails->sale_return_id = $returnId;
                $returnDetails->product_id     = $product->product_id;
                $returnDetails->quantity       = $product->quantity;
                $returnDetails->price          = $product->price;
                $returnDetails->total          = $product->quantity * $product->price;
                $returnDetails->save();
            }
        }
    }

    protected function saveSaleReturnData($saleReturn)
    {
        $request                     = request();
        $discount                    = $request->discount ?? 0;
        $payable                     = $this->totalPrice - $discount;
        $dueAmount                   = $payable - $saleReturn->paid_amount ?? 0;
        $saleReturn->sale_id         = $this->sale->id;
        $saleReturn->customer_id     = $this->sale->customer_id;
        $saleReturn->return_date     = Carbon::parse($request->return_date);
        $saleReturn->total_price     = $this->totalPrice;
        $saleReturn->discount_amount = $discount;
        $saleReturn->payable_amount  = $payable;
        $saleReturn->due_amount      = $dueAmount;
        $saleReturn->note            = $request->note;

        if ($this->sale->return_status == 0) {
            $sale                = $this->sale->where('id', $this->sale->id)->first();
            $sale->return_status = 1;
            $sale->save();
        }

        $saleReturn->save();
        return $saleReturn;
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
        $products          = $this->products;
        $saleReturnDetails = $this->saleReturn->details;
        $saleDetails       = $this->saleReturn->sale->saleDetails;
        $notify            = [];

        foreach ($products as $product) {
            $product      = (object) $product;
            $returnDetail = $saleReturnDetails->where('product_id', $product->product_id)->first();
            $saleDetail   = $saleDetails->where('product_id', $product->product_id)->first();

            if ($saleDetail->quantity < $product->quantity) {
                $notify[] = ['error', 'Return quantity exceeds sale quantity'];
                return back()->withNotify($notify)->withInput();
            }

            $productStock = $this->productStocks->where('product_id', $product->product_id)->first();
            $oldQuantity  = $returnDetail->quantity;
            $quantityDiff = $oldQuantity - $product->quantity;

            if ($oldQuantity > $product->quantity && $quantityDiff > $productStock->quantity) {
                $notify[] = ['error', 'Reduced quantity exceeds stock quantity'];
                return back()->withNotify($notify)->withInput();
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
