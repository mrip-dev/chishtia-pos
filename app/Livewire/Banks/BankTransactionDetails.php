<?php

namespace App\Livewire\Banks;

use App\Models\Bank;
use App\Models\BankTransaction;
use Livewire\Component;

class BankTransactionDetails extends Component
{
    public $bankId;
    public $bank;
    public $transactions;
    public function mount($bankId)
    {

        $this->bankId = $bankId;

        $this->bank = Bank::findOrFail($bankId);
        $this->transactions = BankTransaction::where('bank_id', $bankId)->get();
    }

    public function render()
    {
        return view('livewire.banks.bank-transaction-details');
    }
}
