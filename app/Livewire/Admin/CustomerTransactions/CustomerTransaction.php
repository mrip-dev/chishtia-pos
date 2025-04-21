<?php

namespace App\Livewire\Admin\CustomerTransactions;
use App\Models\CustomerTransaction as ModalCustomerTransaction;
use App\Models\Customer;
use Livewire\Component;

class CustomerTransaction extends Component
{

    public $customerId;
    public $customer;



    public function mount($customerId)
    {

        $this->customerId = $customerId;

        $this->customer = Customer::findOrFail($customerId);
    }
    public function render()
    {
        $transactions =ModalCustomerTransaction::with(['customer', 'bank'])->where('customer_id', $this->customerId)->latest()->paginate(20);
        return view('livewire.admin.customer-transactions.customer-transaction', [
            'transactions' => $transactions,
        ]);
    }
}
