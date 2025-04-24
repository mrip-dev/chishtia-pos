<?php

namespace App\Livewire\Admin\CustomerTransactions;

use App\Models\CustomerTransaction as ModalCustomerTransaction;
use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTransaction extends Component
{
    use WithPagination;

    public $customerId;
    public $customer;
    public $search = '';
    public $perPage = 20;
    public $startDate;
    public $endDate;



    public function mount($customerId)
    {

        $this->customerId = $customerId;

        $this->customer = Customer::findOrFail($customerId);
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
        $transactions = ModalCustomerTransaction::with(['customer', 'bank'])
            ->where('customer_id', $this->customerId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('bank', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.customer-transactions.customer-transaction', [
            'transactions' => $transactions,
        ]);
    }

}
