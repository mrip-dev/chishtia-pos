<?php

namespace App\Livewire\Admin\SupplierTransactions;

use App\Models\Supplier;
use App\Models\SupplierTransaction as ModalSupplierTransaction;
use Livewire\Component;
use Livewire\WithPagination;

class SupplierTransaction extends Component
{
    use WithPagination;

    public $supplierId;
    public $supplier;

    public $search = '';
    public $startDate = null;
    public $endDate = null;

    public function mount($id)
    {
        $this->supplierId = $id;
        $this->supplier = Supplier::findOrFail($id);
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
        $transactions = ModalSupplierTransaction::with(['supplier', 'bank'])
            ->where('supplier_id', $this->supplierId)
            ->when($this->search, function ($query) {
                $query->whereHas('bank', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('supplier', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->startDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->endDate);
            })
            ->latest()
            ->paginate(20);

        return view('livewire.admin.supplier-transactions.supplier-transaction', [
            'transactions' => $transactions,
        ]);
    }
}
