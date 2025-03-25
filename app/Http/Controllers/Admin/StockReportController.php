<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockReportController extends Controller
{

    public function index(Request $request)
    {
        $pageTitle         = 'Product Stock Report';
        $warehouses        = Warehouse::orderBy('name')->get();
        $stocksByProduct   = collect([]);
        $stocksByWarehouse = collect([]);
        $productName       = null;

        if ($request->type) {
            $data = $this->getReport($request);
            extract($data);
            $pdfButton = true;
        }

        return view('admin.reports.stock.index', compact('pageTitle', 'warehouses', 'stocksByProduct', 'stocksByWarehouse', 'productName'));
    }

    public function stockPDF(Request $request)
    {
        $warehouses        = Warehouse::orderBy('name')->get();
        $stocksByProduct   = collect([]);
        $stocksByWarehouse = collect([]);
        $productName       = null;
        $pdfButton         = false;
        if ($request->type) {
            $data = $this->getReport($request);
            extract($data);
        }
        if ($request->type == 'warehouse') {
            $pageTitle = 'Products in ';
            return downloadPDF('pdf.warehouse.report', compact('pageTitle', 'stocksByWarehouse'));
        } else if (request()->type == 'product') {
            $pageTitle = 'Stock report of ';
            return downloadPDF('pdf.product.stock_details', compact('pageTitle', 'stocksByProduct'));
        }
    }
    public function stockCSV(Request $request)
    {
        $stocksByProduct   = collect([]);
        $stocksByWarehouse = collect([]);

        if ($request->type) {
            $data = $this->getReport($request);
            extract($data);
        }
        if ($request->type == 'warehouse') {
            $pageTitle = "Warehouse Stock Report";
            $filename  = $this->downloadWarehouseCSV($pageTitle, $stocksByWarehouse);
            return response()->download(...$filename);
        } else if (request()->type == 'product') {
            $pageTitle = "Product Stock Report";
            $filename  = $this->downloadProductCSV($pageTitle, $stocksByProduct);
            return response()->download(...$filename);
        }
    }


    protected function downloadWarehouseCSV($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Product Name,Warehouse,SKU,Category,Brand,Stock,Unit\n";
        foreach ($data as $item) {
            $warehouseName = $item->warehouse->name;
            $productName   = $item->product->name;
            $sku           = $item->product->sku;
            $category      = $item->product->category->name;
            $brand         = $item->product->brand->name;
            $brand         = $item->product->brand->name;
            $unit          = $item->product->unit->name;

            $column .= "$productName,$warehouseName,$sku,$category,$brand,$item->quantity, $unit \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    protected function downloadProductCSV($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Product Name,Warehouse,Stock,Unit\n";
        foreach ($data as $item) {
            $warehouseName = $item->warehouse->name;
            $productName   = $item->product->name;
            $unit          = $item->product->unit->name;

            $column .= "$productName,$warehouseName,$item->quantity, $unit \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    private function getReport($request)
    {

        $request->validate([
            'type'      => 'in:warehouse,product',
            'product'   => 'nullable|required_if:type,product',
            'warehouse' => 'nullable|required_if:type,warehouse',
        ]);

        if ($request->warehouse) {
            $data['stocksByWarehouse'] = ProductStock::where('warehouse_id', $request->warehouse)->where('quantity', '>', 0)->with('product.brand', 'product.category', 'product.unit')->get();
        }

        if ($request->product) {
            $data['productName']     = Product::find($request->product)->name;
            $data['stocksByProduct'] = ProductStock::where('product_id', $request->product)->with('warehouse', 'product.unit')->get();
        }

        return $data;
    }
}
