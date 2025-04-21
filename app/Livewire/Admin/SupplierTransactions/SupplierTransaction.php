<?php

namespace App\Livewire\Admin\SupplierTransactions;

use App\Models\Supplier;
use App\Models\SupplierTransaction as ModalSupplierTransaction;
use Livewire\Component;

class SupplierTransaction extends Component
{

    public $supplierId;
    public $supplier;
    public $transactions;

    public function mount($id)
    {
        $this->supplierId = $id;
        $this->supplier = Supplier::findOrFail($id);

        $this->transactions = ModalSupplierTransaction::where('supplier_id', $id)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function render()
    {
        // Fetch Supplier Transactions along with the related Supplier and Bank
        $transactions = ModalSupplierTransaction::with(['supplier', 'bank'])->latest()->paginate(20);

        // Return the view with data
        return view('livewire.admin.supplier-transactions.supplier-transaction', [
            'transactions' => $transactions,
        ]);
    }
}
