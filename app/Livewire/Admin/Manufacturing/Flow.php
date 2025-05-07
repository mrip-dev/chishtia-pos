<?php

namespace App\Livewire\Admin\Manufacturing;

use App\Models\ExpenseType;
use App\Models\ManufacturingFlow;
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
    public $expenses = [];


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
        $this->showType = 'refined';
        $this->showDetails = true;
    }
    public function viewRaw($id)
    {
        $this->flowId = $id;
        $this->flow = ManufacturingFlow::find($this->flowId);
        $this->expenses = ExpenseType::all();
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
            $this->stockItems[] = ['product' => null, 'quantity' => 1];
        }
    }

    public function loadFlows()
    {
        $this->flows = ManufacturingFlow::all();
    }
    public function mount()
    {
        $this->loadFlows();
    }
    public function addExpenceItem()
    {
        $this->expenceItems[] = ['expense_type_id' => null, 'amount' => 0];
    }
    public function addStockItem()
    {
        $this->stockItems[] = ['product' => null, 'quantity' => 1];
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
                'product' => $item['product'],
                'quantity' => $item['quantity'],
            ]);
        }


        $this->dispatch('notify', status: 'success', message: 'Flow saved successfully!');
        $this->showDetails = false;
    }
    public function cancel()
    {
        $this->showDetails = false;
        $this->flowId = null;
        $this->expenceItems = [];
        $this->stockItems = [];
    }
    public function render()
    {
        return view('livewire.admin.manufacturing.flow');
    }
}
