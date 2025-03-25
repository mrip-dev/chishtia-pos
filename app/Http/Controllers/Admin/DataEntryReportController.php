<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Adjustment;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Transfer;

class DataEntryReportController extends Controller
{
    private $relations = [];

    public function product()
    {
        $this->pageTitle = 'Products Entry Report';
        $this->model = Product::class;
        return $this->entries('product');
    }

    public function customer()
    {
        $this->pageTitle = 'Customer Entry Report';
        $this->model = Customer::class;
        return $this->entries('customer');
    }

    public function supplier()
    {
        $this->pageTitle = 'Supplier Entry Report';
        $this->model = Supplier::class;
        return $this->entries('supplier');
    }

    public function purchase()
    {
        $this->pageTitle = 'Purchase Entry Report';
        $this->model = Purchase::class;
        return $this->entries('purchase');
    }

    public function purchaseReturn()
    {
        $this->pageTitle = 'Purchase Return Entry Report';
        $this->model = PurchaseReturn::class;
        $this->relations = ['actionable.purchase'];
        return $this->entries('purchase_return');
    }

    public function sale()
    {
        $this->pageTitle = 'Sale Entry Report';
        $this->model = Sale::class;
        return $this->entries('sale');
    }

    public function saleReturn()
    {
        $this->pageTitle = 'Sale Return Entry Report';
        $this->model = SaleReturn::class;
        $this->relations = ['actionable.sale'];
        return $this->entries('sale_return');
    }

    public function adjustment()
    {
        $this->pageTitle = 'Adjustment Entry Report';
        $this->model = Adjustment::class;
        return $this->entries('adjustment');
    }

    public function transfer()
    {
        $this->pageTitle = 'Transfer Entry Report';
        $this->model = Transfer::class;
        return $this->entries('transfer');
    }

    public function expense()
    {
        $this->pageTitle = 'Expense Entry Report';
        $this->model = Expense::class;
        $this->relations = ['actionable.expenseType'];
        return $this->entries('expense');
    }

    public function supplierPayment()
    {
        $this->pageTitle = 'Supplier Payment Entry Report';
        $this->model = SupplierPayment::class;
        $this->relations = ['actionable.supplier'];
        return $this->entries('supplier_payment');
    }

    public function customerPayment()
    {
        $this->pageTitle = 'Customer Payment Entry Report';
        $this->model = CustomerPayment::class;
        $this->relations = ['actionable.customer'];
        return $this->entries('customer_payment');
    }

    private function entries($type)
    {
        $pageTitle  = $this->pageTitle;
        $entries    = Action::where('actionable_type', $this->model)->with('actionable', 'admin');
        if (count($this->relations)) {
            $entries->with($this->relations);
        }
        $entries = $entries->latest()->paginate(getPaginate());
        return view('admin.reports.data_entry.' . $type, compact('entries', 'pageTitle', 'type'));
    }
}
