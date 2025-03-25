<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Transfer;
use App\Models\TransferDetails;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Transfers';
    }

    protected function getTransfers()
    {
        return Transfer::searchable(['tracking_no', 'warehouse:name'])->dateFilter('transfer_date')->with('warehouse', 'toWarehouse')->withCount('transferDetails')->orderBy('id', 'desc');
    }

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $transfers = $this->getTransfers()->paginate(getPaginate());
        return view('admin.transfer.index', compact('pageTitle', 'transfers'));
    }

    public function transferPDF()
    {
        $pageTitle = $this->pageTitle;
        $transfers = $this->getTransfers()->get();
        return downloadPDF('pdf.transfer.list', compact('pageTitle', 'transfers'));
    }

    public function transferCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getTransfers()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Tracking No.,Date,from Warehouse,To Warehouse,Product Items\n";
        foreach ($data as $transfer) {
            $date            = showDateTime(@$transfer->transfer_date, 'd-m-Y');
            $totalProductQty = $transfer->transfer_details_count;
            $warehouse       = $transfer->warehouse->name;
            $toWarehouse     = $transfer->toWarehouse->name;

            $column .= "$transfer->tracking_no,$date,$warehouse,$toWarehouse,$totalProductQty \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function detailsPDF($id)
    {
        $pageTitle = "Transfer Details";
        $transfer  = Transfer::where('id', $id)
            ->with('warehouse', 'toWarehouse', 'transferDetails.product.unit')->whereHas('transferDetails')->firstOrFail();
        return downloadPDF('pdf.transfer.details', compact('pageTitle', 'transfer'));
    }

    public function create()
    {
        $pageTitle  = 'New Transfer';
        $warehouses = Warehouse::active()->orderBy('name')->get();
        return view('admin.transfer.form', compact('pageTitle', 'warehouses'));
    }

    public function edit($id)
    {
        $transfer   = Transfer::where('id', $id)->with('transferDetails.product.productStock', 'transferDetails.product.unit')->whereHas('transferDetails')->firstOrFail();
        $pageTitle  = 'Edit Transfer';
        $warehouses = Warehouse::active()->get();
        return view('admin.transfer.form', compact('pageTitle', 'transfer', 'warehouses'));
    }

    public function store(Request $request)
    {
        $this->validation($request);

        $this->products          = collect($request->products);
        $productIds              = collect($request->products)->pluck('product_id')->toArray();
        $this->fromProductStocks = ProductStock::where('warehouse_id', $request->from_warehouse_id)->whereIn('product_id', $productIds)->get();
        $this->toProductStocks   = ProductStock::where('warehouse_id', $request->to_warehouse_id)->whereIn('product_id',   $productIds)->get();

        foreach ($this->fromProductStocks as $key => $stock) {
            if ($stock->quantity <  $this->products->pluck('quantity')->toArray()[$key]) {
                $notify[] = ['error', 'Insufficient product in the warehouse!'];
                return back()->withNotify($notify)->withInput();
            }
        }

        $transfer              = new Transfer();
        $transfer->tracking_no = getTrx(8);
        $transfer              = $this->saveTransferData($transfer);

        $this->updateStock($request, $transfer);
        $this->storeTransferDetails($transfer->id);

        Action::newEntry($transfer, 'CREATED');

        $notify[] = ['success', 'Transfer data added successfully'];
        return to_route('admin.transfer.edit', $transfer->id)->withNotify($notify);
    }


    public function update(Request $request, $id)
    {
        $this->validation($request, true);

        $this->products   = collect($request->products);
        $this->productIds = $this->products->pluck('product_id')->toArray();

        $transfer = Transfer::with('transferDetails')->findOrFail($id);

        if (($transfer->from_warehouse_id != $request->from_warehouse_id) || ($transfer->to_warehouse_id != $request->to_warehouse_id)) {
            $notify[] = ['error', 'Warehouse can\'t be changed'];
            return back()->withNotify($notify)->withInput();
        }

        $this->fromProductStocks = ProductStock::where('warehouse_id', $request->from_warehouse_id)->whereIn('product_id',   $this->productIds)->get();
        $this->toProductStocks   = ProductStock::where('warehouse_id', $request->to_warehouse_id)->whereIn('product_id',   $this->productIds)->get();

        // If reduce the quantity then we need to check the current stock.
        $check = $this->checkStockAvailability($transfer->transferDetails);

        if (!empty($check)) {
            return $check;
        }

        $this->transfer = $this->saveTransferData($transfer);
        $this->updateStock($request, $transfer, true);
        $this->storeTransferDetails($transfer->id);

        Action::newEntry($transfer, 'UPDATED');

        $notify[] = ['success', 'Transfer data updated successfully'];

        if ($this->transfer->transferDetails()->count() == 0) {
            $this->transfer->delete();
            return to_route('admin.transfer.index')->withNotify($notify);
        }

        return back()->withNotify($notify);
    }

    protected function saveTransferData($transfer)
    {
        $request                     = request();
        $transfer->from_warehouse_id = $request->from_warehouse_id;
        $transfer->to_warehouse_id   = $request->to_warehouse_id;
        $transfer->transfer_date     = Carbon::parse($request->transfer_date)->format('Y-m-d');
        $transfer->note              = $request->note;
        $transfer->save();
        return  $transfer;
    }

    protected function updateStock($request, $transfer, $isUpdate = false)
    {
        foreach ($this->products as $product) {

            $product = (object) $product;

            $fromPreviousStock = $this->fromProductStocks->where('product_id', $product->product_id)->first();
            $toPreviousStock   = $this->toProductStocks->where('product_id', $product->product_id)->first();
            $oldTransferDetail = null;
            $oldQty            = 0;

            if ($isUpdate && $transfer->transferDetails) {
                $oldTransferDetail = $transfer->transferDetails->where('product_id', $product->product_id)->first();
                $oldQty            = $oldTransferDetail->quantity ?? 0;
            }

            $quantity = $product->quantity - $oldQty;

            if ($quantity == 0) {
                continue;
            }

            if ($fromPreviousStock) {
                $fromPreviousStock->quantity -= $quantity;
                $fromPreviousStock->save();
            }

            if (!$toPreviousStock) {
                $toPreviousStock               = new ProductStock;
                $toPreviousStock->product_id   = $product->product_id;
                $toPreviousStock->warehouse_id = $request->to_warehouse_id;
                $toPreviousStock->quantity     = 0;
            }

            $toPreviousStock->quantity += $quantity;
            $toPreviousStock->save();
        }
    }

    protected function storeTransferDetails($transferId)
    {

        foreach ($this->products as $product) {
            $product = (object) $product;

            $transferDetails = TransferDetails::where('transfer_id', $transferId)->where('product_id', $product->product_id)->first() ?? new TransferDetails();

            if ($product->quantity == 0) {
                $transferDetails->delete();
                continue;
            } else {
                $transferDetails->transfer_id = $transferId;
                $transferDetails->product_id  = $product->product_id;
                $transferDetails->quantity    = $product->quantity;
                $transferDetails->save();
            }
        }
    }

    protected function checkStockAvailability($transferDetails)
    {
        $notify = [];
        foreach ($this->products as $product) {
            $product        = (object) $product;
            $transferDetail = $transferDetails->where('product_id', $product->product_id)->first();

            if ($transferDetail) {
                $stock        = $product->quantity - $transferDetail->quantity;
                $productStock = $this->fromProductStocks->where('product_id', $product->product_id)->first();
                $newStock     = $productStock->quantity - $stock;

                if ($newStock < 0) {
                    $notify[] = ['warning', 'Wrong quantity provided'];
                    return back()->withNotify($notify)->withInput();
                }
            } else {

                $stock = $this->fromProductStocks->where('product_id', $product->product_id)->first();

                if ($stock->quantity <  $product->quantity) {
                    $notify[] = ['warning', 'Wrong quantity provided'];;
                    return back()->withNotify($notify)->withInput();
                }
            }
        }
        return $notify;
    }

    protected function validation($request, $isUpdate = false)
    {
        $qtyValidation = $isUpdate ? 'gte:0' : 'gt:0';
        $request->validate([
            'from_warehouse_id'     => 'required|exists:warehouses,id',
            'to_warehouse_id'       => 'required|different:from_warehouse_id|exists:warehouses,id',
            'transfer_date'         => 'required|date_format:Y-m-d',
            'products'              => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|' . $qtyValidation,
            'note'                  => 'nullable|string',
        ]);
    }

    public function searchProduct(Request $request)
    {
        $fromWarehouse = $request->from_warehouse;
        $products      = Product::searchable(['name', 'sku']);

        $products = $products->whereHas('productStock', function ($q) use ($fromWarehouse) {
            $q->where('warehouse_id', $fromWarehouse)->where('quantity', '>', 0);
        })->with('unit', 'productStock')->get();

        if ($products) {
            return response()->json([
                'success' => true,
                'data'    => $products,
            ]);
        }
    }
}
