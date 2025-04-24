<?php

namespace App\Livewire\Admin\Services;

use App\Models\Customer;
use App\Models\ServiceStockDetail;
use App\Models\StockTransfer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageStockDetails extends Component
{

    public $stocks = [];
    public $searchTerm = '';
    public $searchByDate = '';
    public $selectedStock = null;
    public $showDetails = false;

    public function mount()
    {
        $this->loadStockDetails();
    }
    public function loadStockDetails()
    {
        $this->stocks = ServiceStockDetail::where('quantity', '>', 0)
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('warehouse', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
                });
            })
            ->when($this->searchByDate, function ($query) {
                $query->whereDate('created_at', Carbon::parse($this->searchByDate));
            })
            ->with(['product', 'warehouse'])
            ->groupBy(['user_id','user_model'])
            ->orderBy('created_at', 'desc')
            ->selectRaw('sum(quantity) as quantity, user_id, user_model')
            ->get();
    }
    public function updated($name, $value)
    {
        if ($name === 'searchTerm') {
            $this->loadStockDetails();
        }
        if ($name === 'searchByDate') {
            $this->loadStockDetails();
        }
    }
    public function viewDetails($stockId)
    {
        $this->selectedStock = StockTransfer::with('stockTransferDetails')->find($stockId);
        $this->showDetails = true;

    }
    public function render()
    {
        return view('livewire.admin.services.manage-stock-details');
    }
}
