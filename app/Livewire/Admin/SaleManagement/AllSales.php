<?php

namespace App\Livewire\Admin\SaleManagement;

use Livewire\Component;

class AllSales extends Component
{
    public $sales = [];
    public $selectedSale = null;
    public $saleDetails = [];
    public $isEditing = false;
    public $isCreating = false;
    public $isDeleting = false;
    public $searchTerm = '';
    public $perPage = 10;   
    
    
    public function mount()
    {
        $this->loadSales();
    }
    public function loadSales()
    {
        // Load sales from the database or any other source
        $this->sales = []; // Replace with actual data fetching logic
    }
    public function createSale()
    {
        $this->isCreating = true;
        $this->selectedSale = null;
        $this->saleDetails = [];
    }
    public function render()
    {
        return view('livewire.admin.sale-management.all-sales');
    }
}
