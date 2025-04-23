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
    public function redirectDataModel($id, $dataModel)
    {

        switch ($dataModel) {

            case 'Sale':
                return redirect()->to('/admin/manage/sale/?module_id=' . $id.'#module_id_' . $id);
                break;
            case 'Purchase':
                return  redirect()->to('/admin/manage/purchase/?module_id=' . $id.'#module_id_' . $id);
                break;
            case 'Expense':
               return redirect()->to('/admin/manage/expense/?module_id=' . $id.'#module_id_' . $id);
                break;
        }
    }
}
