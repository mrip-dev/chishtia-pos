<?php

namespace App\Livewire\Admin\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceStockDetail;
use App\Models\Stock;
use App\Models\StockInOut;
use App\Models\Supplier;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageStock extends Component
{
    public $stocks = [];
    public $users = [];
    public $products = [];
    public $suppliers = [];
    public $clients = [];
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

    public $user_id;
    public $driver_name;
    public $driver_contact;
    public $vehicle_number;
    public $labour;





    protected function rules()
    {
        $rules = [
            'stock_type' => 'required|in:in,out',
            'product_id' => 'required|integer',

        ];
        return $rules;
    }

    public function mount($type)
    {
        $this->stock_type = $type;
        $this->loadStocks();
    }
    public function loadStocks()
    {

        $this->stocks = Stock::with(['warehouse','user'])->where('stock_type', $this->stock_type)
            ->where(function ($query) {
                $query->orWhereHas('warehouse', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                })->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->searchByDate, function ($query) {
                // Parse the date range
                [$start, $end] = explode(' - ', $this->searchByDate);
                $startDate = Carbon::parse($start)->startOfDay();
                $endDate = Carbon::parse($end)->endOfDay();

                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->get();
    }
    public function createStock()
    {
        $this->isCreating = !$this->isCreating;
        $this->showDetails = false;
        $this->users = [];
        $this->stockItems = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0]
        ];
        $suppliers = Supplier::select('id', 'name', 'mobile')->get();
        $clients = Customer::select('id', 'name', 'mobile')->get();
        foreach ($suppliers as $supplier) {
            $this->users[] = [
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
        if (str_starts_with($name, 'stockItems.')) {

            $index = explode('.', $name)[1];
            $quant = (float)$this->stockItems[$index]['quantity'];
            $unit_price = (float)$this->stockItems[$index]['unit_price'];
            $this->stockItems[$index]['total_amount'] = $quant * $unit_price;
            $this->recalculateTotalAmount();
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
    public function addItem()
    {
        $this->stockItems[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0];
    }

    public function removeItem($index)
    {
        unset($this->stockItems[$index]);
        $this->stockItems = array_values($this->stockItems); // reindex
    }
    public function saveStock()
    {

        $this->validate([
            'title' => 'required|string',
            'stock_type' => 'required|in:in,out',
            'warehouse_id' => 'required|integer',
            'user_id' => 'required|integer',
            'labour' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_contact' => 'nullable|string',
            'stockItems.*.product_id' => 'required|integer',
            'stockItems.*.quantity' => 'required|numeric|min:1',
        ]);
        $selecteduserId = $this->users[$this->user_id]['id'];
        $selecteduserModel = $this->users[$this->user_id]['model'];
        /////////////////  Check Stock Availability for all Products
        foreach ($this->stockItems as $item) {
            $availableStock = $this->checkAvailableStock($item['product_id'], $this->warehouse_id, $selecteduserId, $selecteduserModel);
            if ($this->stock_type == 'out' && $availableStock < $item['quantity']) {
                ///// Product
                $product = Product::find($item['product_id']);
                if ($product) {
                    $productName = $product->name;
                } else {
                    $productName = 'Unknown Product';
                }
                $this->dispatch('notify', status: 'error', message: 'Insufficient stock for product: ' . $productName);
                return;

            }
        }

        $stock = Stock::create([
            'title' => $this->title,
            'stock_type' => $this->stock_type,
            'warehouse_id' => $this->warehouse_id,
            'user_id' => $selecteduserId,
            'user_model'   => $selecteduserModel,
            'labour' => $this->labour,
            'vehicle_number' => $this->vehicle_number,
            'driver_name' => $this->driver_name,
            'driver_contact' => $this->driver_contact,
        ]);

        foreach ($this->stockItems as $item) {
            StockInOut::create([
                'stock_id' => $stock->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $item['total_amount'],

            ]);
            ///////////   Update Service Stock Detail
            $stockdetail = $this->updateServiceStock($item['product_id'], $item['quantity'], $this->stock_type, $this->warehouse_id, $selecteduserId, $selecteduserModel);
        }

        $this->dispatch('notify', status: 'success', message: 'Stock saved successfully');

        $this->isCreating = false;
        $this->loadStocks(); // reload stocks
    }


    public function viewDetails($stockId)
    {
        $this->selectedStock = Stock::with('stockInOuts')->find($stockId);
        $this->showDetails = true;
        $this->isCreating = false;
    }
    public function recalculateTotalAmount()
    {

        $total = 0;
        foreach ($this->stockItems as $item) {
            $total += $item['total_amount'];
        }
        return $total;
    }
    public function stockTotalAmount()
    {
        $stock = Stock::with('stockInOuts')->find($this->selectedStock->id);
        if (!$stock) {
            return 0;
        }
        $total = 0;
        foreach ($stock->stockInOuts as $item) {
            $total += $item->total_amount;
        }
        return $total;
    }

    public function updateServiceStock($product_id, $quantity, $stock_type, $warehouse_id, $user_id, $user_model)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            if ($stock_type == 'in') {
                $stockDetail->increment('quantity', $quantity);
            } else {
                $stockDetail->decrement('quantity', $quantity);
            }
        } else {
            ServiceStockDetail::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'user_id' => $user_id,
                'user_model' => $user_model,
                'quantity' => ($stock_type == 'in') ? $quantity : -$quantity,
            ]);
        }
    }
    public function checkAvailableStock($product_id, $warehouse_id, $user_id, $user_model)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            return $stockDetail->quantity;
        }
        return 0;
    }


    public function render()
    {
        return view('livewire.admin.services.manage-stock');
    }
}
