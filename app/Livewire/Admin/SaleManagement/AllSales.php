<?php

namespace App\Livewire\Admin\SaleManagement;

use App\Models\Sale;
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

    public $successMessage;

    // Listening for the event
    protected $listeners = ['saleSaved' => 'handleSaleSaved'];
    public function handleSaleSaved($message)
    {
        $this->successMessage = $message;
        $this->loadSales();
        session()->flash('success', $message);
    }
    public function mount()
    {
        $this->loadSales();
    }
    public function loadSales()
    {
        $this->sales = Sale::with(['customer', 'warehouse'])
            ->where('invoice_no', 'like', '%' . $this->searchTerm . '%')
            ->orWhereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->orWhereHas('warehouse', function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->get();
    }
    public function createSale()
    {
        $this->isCreating = !$this->isCreating;
        $this->selectedSale = null;
        $this->saleDetails = [];
    }
    public function render()
    {
        return view('livewire.admin.sale-management.all-sales');
    }
}
