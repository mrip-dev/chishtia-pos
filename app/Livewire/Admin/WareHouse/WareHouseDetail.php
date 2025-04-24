<?php

namespace App\Livewire\Admin\WareHouse;

use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WareHouseDetailHistory;

class WareHouseDetail extends Component
{
    use WithPagination;
    public $warehouse;  // Property to store the warehouse object
    public $id;         // Property to store the passed warehouse ID
    public $perPage = 10; // Number of items per page
    public $search = ''; // Search term for filtering results
    public $startDate = null;
    public $endDate = null;

    public function mount($id)  // The ID is passed when the component is initialized
    {
        $this->id = $id;
        $this->warehouse = Warehouse::findOrFail($this->id);  // Find warehouse by ID

    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }
        public function clearFilters()
    {
        $this->search = '';
        $this->startDate = null;
        $this->endDate = null;
    }

    public function render()
    {
        $warehouseDetails = WareHouseDetailHistory::with(['product', 'supplier', 'customer'])
            ->where('ware_house_id', $this->id)
            ->where(function ($query) {
                // Search functionality for product, supplier, or customer
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('supplier', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            // Add date filtering if start and end dates are provided
            ->when($this->startDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->paginate(10);

        return view('livewire.admin.ware-house.ware-house-detail', [
            'warehouseDetails' => $warehouseDetails
        ]);
    }
}
