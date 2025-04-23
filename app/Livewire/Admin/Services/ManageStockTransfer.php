<?php

namespace App\Livewire\Admin\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockInOut;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageStockTransfer extends Component
{
    public $stocks = [];
    public $products = [];
    public $suppliers = [];
    public $clients = [];
    public $users = [];
    public $warehouses = [];

    public $searchTerm = '';
    public $searchByDate = '';
    public $isCreating = false;
    public $editMode = false;
    public $warehouse_id;

    public $stockItems = [];
    public $title;
    public $stock_type;

    public $selectedStock = null;
    public $showDetails = false;

    public $client_id;





    protected function rules()
    {
        $rules = [
            'stock_type' => 'required|in:in,out',
            'product_id' => 'required|integer',

        ];

        if ($this->stock_type === 'in') {
            $rules['supplier_id'] = 'required|integer';
        }

        if ($this->stock_type === 'out') {
            $rules['client_id'] = 'required|integer';
        }

        return $rules;
    }

    public function mount()
    {
        $this->loadStocks();
    }
    public function loadStocks()
    {



    }
    public function createStock()
    {
        $this->isCreating = !$this->isCreating;
        $this->stockItems = [
            ['from_user_id' => null, 'from_warehouse_id' => null,'to_user_id' => null, 'to_warehouse_id' => null, 'product_id' => null, 'quantity' => 1]
        ];
        $suppliers = Supplier::select('id', 'name', 'mobile')->get();
        $clients = Customer::select('id', 'name', 'mobile')->get();
        foreach ($suppliers as $supplier) {
            $this->users[] =[
                'id' => $supplier->id,
                'name' => $supplier->name,
                'model' => 'Supplier',
            ];
        }
        foreach ($clients as $client) {
            $this->users[] = [
                'id' => $client->id,
                'name' => $client->name,
                'model' => 'Customer',
            ];
        }

        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
    }



    public function updated($name, $value)
    {
        if ($name === 'searchTerm') {
            $this->loadStocks();
        }
        if ($name === 'searchByDate') {
            $this->loadStocks();
        }
        if ($name === 'warehouse_id') {
            $this->getProducts();
        }
    }
    public function getProducts()
    {
        $warehouse = $this->warehouse_id;
        if (!$warehouse) {
            $this->products = [];
            $this->dispatch('notify', status: 'error', message: 'Select Whereouse First');
        }
        $this->products = Product::all();
    }


    public function render()
    {
        return view('livewire.admin.services.manage-stock-transfer');
    }
}
