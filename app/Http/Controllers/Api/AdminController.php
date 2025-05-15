<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard(Request $request)
    {

        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date)->toDateString();
            $endDate = Carbon::parse($request->end_date)->toDateString();
        } elseif ($request->month) {
            $month = (int) $request->month;
            $year = $request->year ?? now()->year;
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        } else {
            $startDate = null;
            $endDate = null;
        }

        $widget = $this->getDashboardWidgets($startDate, $endDate);

        $startDate = $startDate ?? now()->subDays(14)->toDateString();
        $endDate =  $endDate ?? now()->toDateString();

        return response()->json([
            'widgets' => $widget,
            'alertProductsQty' => $this->alertProductsQty(),
            'topSellingProducts' => $this->topSellingProducts(),
            'saleReturns' => $this->saleReturns(),
            'purchaseAndSaleReport' => $this->purchaseAndSaleReport($startDate, $endDate),
            'saleAndSaleReturnReport' => $this->saleAndSaleReturnReport($startDate, $endDate),
            'purchaseAndPurchaseReturnReport' => $this->purchaseAndPurchaseReturnReport($startDate, $endDate),

        ]);
    }
    private function getDashboardWidgets($startDate = null, $endDate = null)
    {
        $filter = $startDate && $endDate;

        return [
            'total_customer' => Customer::count(),
            'total_product' => Product::count(),
            'total_category' => Category::count(),
            'total_supplier' => Supplier::count(),

            'total_purchase_count' => Purchase::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count(),
            'total_purchase' => Purchase::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->sum('payable_amount'),

            'total_purchase_return_count' => PurchaseReturn::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count(),
            'total_purchase_return' => PurchaseReturn::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->sum('receivable_amount'),

            'total_sale_count' => Sale::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count(),
            'total_sale' => Sale::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->sum('receivable_amount'),

            'total_sale_return_count' => SaleReturn::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->count(),
            'total_sale_return' => SaleReturn::when($filter, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))->sum('payable_amount'),
        ];
    }

    public function alertProductsQty()
    {

        $alertProductsQty = Product::select('products.id', 'products.name', 'units.name as unit_name', 'products.alert_quantity', 'product_stocks.quantity as quantity', 'warehouses.name as warehouse_name')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('warehouses', 'warehouses.id', '=', 'product_stocks.warehouse_id')
            ->whereRaw('products.alert_quantity >= product_stocks.quantity')
            ->orderBy('products.alert_quantity')->take(8)->get();

        return $alertProductsQty;
    }
    public function topSellingProducts()
    {

        $data =  Product::where('total_sale', '!=', 0)->with('unit:id,name')->orderBy('total_sale', 'desc')->limit(8)->get();
        return $data;
    }
    public function saleReturns()
    {

        $data = SaleReturn::with('sale.warehouse', 'customer')->orderBy('id', 'desc')->take(8)->get();

        return $data;
    }
    public function purchaseAndSaleReport($startDate, $endDate)
    {


        $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format  = $groupBy === 'months' ? '%M-%Y' : '%d-%M-%Y';

        $dates = $groupBy === 'days'
            ? $this->getAllDates($startDate, $endDate)
            : $this->getAllMonths($startDate, $endDate);

        $purchases = Purchase::whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(purchase_date, '{$format}') as created_on")
            ->groupBy('created_on')
            ->get();

        $sales = Sale::whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(sale_date, '{$format}') as created_on")
            ->groupBy('created_on')
            ->get();

        $data = [];
        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'purchases'  => getAmount($purchases->where('created_on', $date)->first()?->amount ?? 0),
                'sales'      => getAmount($sales->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);

        return [
            'created_on' => $data->pluck('created_on'),
            'data' => [
                ['name' => 'Purchases', 'data' => $data->pluck('purchases')],
                ['name' => 'Sales', 'data' => $data->pluck('sales')],
            ]
        ];
    }



    public function saleAndSaleReturnReport($startDate, $endDate)
    {

        $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($startDate, $endDate);
        } else {
            $dates = $this->getAllMonths($startDate, $endDate);
        }

        $saleData   = Sale::whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(sale_date, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $saleReturnData  = SaleReturn::whereDate('return_date', '>=', $startDate)
            ->whereDate('return_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(return_date, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();


        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'sales' => getAmount($saleData->where('created_on', $date)->first()?->amount ?? 0),
                'sales_return' => getAmount($saleReturnData->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);


        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Sales',
                'data' => $data->pluck('sales')
            ],
            [
                'name' => 'Sales Return',
                'data' => $data->pluck('sales_return')
            ]
        ];

        return $report;
    }


    public function purchaseAndPurchaseReturnReport($startDate, $endDate)
    {


        $diffInDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($startDate, $endDate);
        } else {
            $dates = $this->getAllMonths($startDate, $endDate);
        }

        $saleData   = Purchase::whereDate('purchase_date', '>=', $startDate)
            ->whereDate('purchase_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(purchase_date, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $saleReturnData  = PurchaseReturn::whereDate('return_date', '>=', $startDate)
            ->whereDate('return_date', '<=', $endDate)
            ->selectRaw('SUM(total_price) AS amount')
            ->selectRaw("DATE_FORMAT(return_date, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();


        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'purchases' => getAmount($saleData->where('created_on', $date)->first()?->amount ?? 0),
                'purchases_return' => getAmount($saleReturnData->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);

        // Monthly Deposit & Withdraw Report Graph
        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Purchases',
                'data' => $data->pluck('purchases')
            ],
            [
                'name' => 'Purchases Return',
                'data' => $data->pluck('purchases_return')
            ]
        ];

        return $report;
    }
    private function getAllDates($startDate, $endDate)
    {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function  getAllMonths($startDate, $endDate)
    {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $months = [];

        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }

        return $months;
    }
}
