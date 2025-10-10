<?php

namespace App\Livewire\Admin\SaleManagement;

use Livewire\Component;
use App\Models\Sale;

class ViewSale extends Component
{
    public $sale;

    public function mount($saleId)
    {
        $this->sale = Sale::with([
            'saleDetails.product.brand',
            'saleDetails.product.category',
            'customer:id,name',
            'warehouse:id,name'
        ])->findOrFail($saleId);
    }

    public function render()
    {
        return view('livewire.admin.sale-management.view-sale');
           
    }
}
