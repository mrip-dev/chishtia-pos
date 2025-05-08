<?php

namespace App\Livewire\Admin\Manufacturing;

use App\Models\Brand;
use App\Models\Category;
use App\Models\ExpenseType;
use App\Models\ManufacturingFlow;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Unit;
use Carbon\Carbon;
use Livewire\Component;

class Flow extends Component
{
    public $flows;
    public $showDetails = false;
    public $showType = 'raw';
    public $flowId = null;
    public $flow = null;
    public $expenceItems = [];
    public $stockItems = [];
    public $refinedItems = [];
    public $expenses = [];

    public $products = [];
    public $productOptions = [];
    public $selectedProduct;

    public $searchTerm = '';
    public $startDate = null;
    public $endDate = null;

    protected $listeners = ['addNewSelectOption' => 'handleAddNewSelectOption'];

    public function handleAddNewSelectOption($text, $model, $list)
    {

        $newItemText = trim($text);
        if (preg_match('/^expenceItems\.\d+\.expense_type_id$/', $model)) {
            $newItemText = str_replace(' ', '-', $newItemText);
            $newItem = ExpenseType::create([
                'name' => $newItemText,
            ]);
        } else {
            $newItem = collect();
        }

        if ($newItem != null) {
            data_set($this, $model, $newItem->id);

            $array = data_get($this, $list, []);
            $array[] = ['id' => $newItem->id, 'text' => $newItem->name];
            data_set($this, $list, $array);
        }


        $this->dispatch('re-init-select-2-component');
    }


    public function confirmAddNew()
    {
        $this->dispatch('confirmNewFlow');
    }
    public function createFlow()
    {
        $flow = ManufacturingFlow::create([
            'title' => 'New Flow',
            'description' => 'Auto-created flow',
            'date' => now()->toDateString(),
        ]);
        $this->loadFlows();

        session()->flash('success', 'Flow created successfully!');
    }
    public function viewRefined($id)
    {

        $this->flowId = $id;
        $this->flow = ManufacturingFlow::find($this->flowId);
        $this->expenses = ExpenseType::all();
        $this->expenceItems = [];
        $this->stockItems = [];
        $this->loadRefinedFlowDetails();
        $this->showType = 'refined';
        $this->showDetails = true;
    }
    public function viewRaw($id)
    {
        $this->flowId = $id;
        $this->flow = ManufacturingFlow::find($this->flowId);
        $this->expenses = ExpenseType::all()->map(fn($p) => [
            'id' => $p->id,
            'text' => $p->name,
        ])->toArray();;
        $this->expenceItems = [];
        $this->stockItems = [];
        $this->loadRawFlowDetails();
        $this->showType = 'raw';
        $this->showDetails = true;
    }
    public function loadRawFlowDetails()
    {

        if ($this->flow) {
            $this->expenceItems = $this->flow->expenses()->get()->toArray();
            $this->stockItems = $this->flow->stocks()->get()->toArray();
        }
        if (empty($this->expenceItems)) {
            $this->expenceItems[] = ['expense_type_id' => null, 'amount' => 0];
        }
        if (empty($this->stockItems)) {
            $this->stockItems[] = ['product_id' => null, 'quantity' => 1];
        }
    }
    public function loadRefinedFlowDetails()
    {

        if ($this->flow) {
            $this->refinedItems = $this->flow->refinedItems()
                ->get(['product_id', 'quantity', 'status']) // explicitly select fields
                ->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'status' => $item->status, // ensure status is included
                    ];
                })
                ->toArray();
        }
        if (empty($this->refinedItems)) {
            $this->refinedItems[] = ['product_id' => null, 'quantity' => 1, 'status' => null];
        }
    }



    public function updated($propertyName)
    {
        if ($propertyName == 'searchTerm' || $propertyName == 'startDate' || $propertyName == 'endDate') {
            $this->loadFlows();
        }
    }
    public function loadFlows()
    {

        $this->flows = ManufacturingFlow::where(function ($query) {
            $query->where('title', 'like', '%' . $this->searchTerm . '%');
            $query->orWhere('tracking_id', 'like', '%' . $this->searchTerm . '%');
        })->when($this->startDate && $this->endDate, function ($query) {
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
            ->get();
    }
    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->startDate = null;
        $this->endDate = null;
        $this->loadFlows();
    }
    public function mount()
    {
        $this->loadFlows();
        $this->productOptions = Product::all()->map(fn($p) => [
            'id' => $p->id,
            'text' => $p->name,
        ])->toArray();
    }
    public function addExpenceItem()
    {
        $this->expenceItems[] = ['expense_type_id' => null, 'amount' => 0];
    }
    public function addStockItem()
    {
        $this->stockItems[] = ['product_id' => null, 'quantity' => 1];
    }
    public function addRefinedItem()
    {
        $this->refinedItems[] = ['product_id' => null, 'quantity' => 1, 'status' => null];
    }
    public function removeRefinedItem($index)
    {
        unset($this->refinedItems[$index]);
        $this->refinedItems = array_values($this->refinedItems); // reindex
    }
    public function removeStockItem($index)
    {
        unset($this->stockItems[$index]);
        $this->stockItems = array_values($this->stockItems); // reindex
    }
    public function removeExpenceItem($index)
    {
        unset($this->expenceItems[$index]);
        $this->expenceItems = array_values($this->expenceItems); // reindex
    }
    public function saveFlow()
    {


        $flow = ManufacturingFlow::find($this->flowId);
        // Sync Expenses
        $flow->expenses()->delete();
        foreach ($this->expenceItems as $item) {
            $flow->expenses()->create([
                'expense_type_id' => $item['expense_type_id'],
                'amount' => $item['amount'],
            ]);
        }

        // Sync Stocks
        $flow->stocks()->delete();
        foreach ($this->stockItems as $item) {
            $flow->stocks()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }


        $this->dispatch('notify', status: 'success', message: 'Flow saved successfully!');
        $this->showDetails = false;
    }
    public function saveFlowRefined()
    {
        $flow = ManufacturingFlow::find($this->flowId);
        $existingStatuses = $flow->refinedItems->pluck('status', 'product_id')->toArray();
        $flow->refinedItems()->delete();

        foreach ($this->refinedItems as $item) {
            $status = $existingStatuses[$item['product_id']] ?? 'refined';
            $flow->refinedItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'status' => $status,
            ]);
        }

        $this->dispatch('notify', status: 'success', message: 'Flow saved successfully!');
        $this->cancel();
    }

    public function cancel()
    {
        $this->showDetails = false;
        $this->flowId = null;
        $this->expenceItems = [];
        $this->stockItems = [];
        $this->refinedItems = [];
        $this->expenses = [];
        $this->flow = null;
        $this->products = [];

        $this->loadFlows();
    }
    public function confirmAddToStock($index)
    {

        $this->dispatch('confirmAddToStock', index : $index);
    }
    public function addToStock($index)
    {
        // Example: If you're using an array of items like $this->items
        if (!isset($this->refinedItems[$index])) {
            $this->dispatch('notify', status: 'error', message: 'Item not found at index: ' . $index);
            return;
        }
        $item = $this->refinedItems[$index];
        $quantity = $item['quantity'];
        $product = Product::find($item['product_id']);
        if (!$product) {
            $this->dispatch('notify', status: 'error', message: 'Product not found for item at index: ' . $index);
            return;
        }
        $this->updateProductStock($product, $quantity);
        /// Upade status of this item to Saved
        $flow = ManufacturingFlow::find($this->flowId);
        $flow->refinedItems()->where('product_id', $item['product_id'])->update(['status' => 'saved']);
        $this->refinedItems[$index]['status'] = 'saved';
        $this->dispatch('notify', status: 'success', message: 'Product added to stock successfully!');
    }
    protected function updateProductStock($product, $quantity)
    {

        $product = (object) $product;
        $productStock = ProductStock::where('product_id', $product->id)->first();
        if ($productStock) {
            $productStock->quantity += $quantity;
            $productStock->save();
        } else {
            $stock = new ProductStock();
            $stock->warehouse_id = 1;
            $stock->product_id   = $product->id;
            $stock->quantity     = $quantity;
            $stock->save();
        }
    }

    public function render()
    {
        $this->dispatch('re-init-select-2-component');
        return view('livewire.admin.manufacturing.flow');
    }
}
