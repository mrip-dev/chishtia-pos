<?php

namespace App\Livewire\Admin\WareHouse;

use App\Models\Warehouse;
use Livewire\Component;

class WareHouseDetail extends Component
{
    public $warehouse;  // Property to store the warehouse object
    public $warehouseDetails;
    public $id;         // Property to store the passed warehouse ID

    public function mount($id)  // The ID is passed when the component is initialized
    {
        $this->id = $id;
        $this->warehouse = Warehouse::findOrFail($this->id);  // Find warehouse by ID
        $this->warehouseDetails = $this->warehouse->wareHouseDetailHistory;
    }

    public function render()
    {
        return view('livewire.admin.ware-house.ware-house-detail');
    }
}
