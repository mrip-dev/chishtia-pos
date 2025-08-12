<?php

namespace App\Livewire\Banks;

use App\Models\Bank;
use App\Models\BankTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\DailyBookEntryTrait;
use App\Traits\HandlesBankPayments;

class BankTransactionDetails extends Component
{
    use WithPagination;
    use HandlesBankPayments;
    use DailyBookEntryTrait;

    public $bankId;
    public $bank;
    public $perPage = 20;


    public $search = '';
    public $startDate = null;
    public $endDate = null;

    public $banks = [];
    public $fromBank = null;
    public $fromBankName = null;
    public $fromBankBalance = null;
    public $toBank = null;
    public $amount = null;




    public function mount($bankId)
    {

        $this->bankId = $bankId;

        $this->bank = Bank::findOrFail($bankId);
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
        $transactions = BankTransaction::where('bank_id', $this->bankId)
            ->where(function ($query) {
                $query->when($this->search, function ($q) {
                    $q->where('source', 'like', '%' . $this->search . '%')
                        ->orWhere('data_model', 'like', '%' . $this->search . '%');
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

        return view('livewire.banks.bank-transaction-details', [
            'transactions' => $transactions,
        ]);
    }

    public function redirectDataModel($id, $dataModel)
    {

        switch ($dataModel) {

            case 'Sale':
                return redirect()->to('/admin/manage/sale/?module_id=' . $id . '#module_id_' . $id);
                break;
            case 'Purchase':
                return  redirect()->to('/admin/manage/purchase/?module_id=' . $id . '#module_id_' . $id);
                break;
            case 'Expense':
                return redirect()->to('/admin/manage/expense/?module_id=' . $id . '#module_id_' . $id);
                break;
            case 'Stock':
                return redirect()->to('/admin/services/stock-in/?module_id=' . $id . '#module_id_' . $id);
                break;
        }
    }
    public function newBankToBank()
    {

        $this->reset(['fromBank', 'toBank', 'amount']);
        $this->banks = Bank::all();


        $this->dispatch('open-modal-bank');
        $this->fromBankName = $this->bank->name;
        $this->fromBankBalance = $this->bank->current_balance;
        $this->fromBank = $this->bankId;
    }
    public function saveTransfer()
    {

        $this->validate([
            'fromBank' => 'required',
            'toBank' => 'required',
            'amount' => 'required',
        ]);
        //// Can Not transfer to the same bank
        if ($this->fromBank == $this->toBank) {
             $this->dispatch('notify', status: 'error', message: 'You can not transfer to the same bank.');

            return;
        }
        // Check if the amount is greater than 0
        if ($this->amount <= 0) {
            $this->dispatch('notify', status: 'error', message: 'Amount must be greater than 0.');
            return;
        }
        // Check if the fromBank has sufficient balance
        $fromBankBalance = Bank::find($this->fromBank)->current_balance;
        if ($fromBankBalance < $this->amount) {

            $this->dispatch('notify', status: 'error', message: 'Insufficient balance in the from bank.');
            return;
        }

        $this->handlePaymentTransaction(
            'transfer',
            0,
            $this->amount,
            $this->toBank,
            $this->fromBank,
            'Bank Transfer',
            'debit'
        );

        $this->handleDailyBookEntries(0, $this->amount, 'debit', 'bank', 'Bank Transfer', $this->fromBank);
        $this->handleDailyBookEntries(0, $this->amount, 'credit', 'bank', 'Bank Transfer', $this->toBank);

        $this->reset(['fromBank', 'toBank', 'amount']);
        $this->dispatch('close-modal-bank');
    }
}
