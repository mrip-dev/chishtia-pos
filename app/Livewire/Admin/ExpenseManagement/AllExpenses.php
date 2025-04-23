<?php

namespace App\Livewire\Admin\ExpenseManagement;

use Livewire\Component;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\Bank;
use App\Models\Action;
use App\Models\BankTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;

class AllExpenses extends Component
{
    use WithPagination;

    public $pageTitle = 'All Expenses';
    public $expense_type_id, $date_of_expense, $amount, $note, $bank_id, $expense_id;
    public $categories = [], $banks = [];
    public $deleteId = null;
    public $selected_id ;
    public $confirmingDelete = false;

    protected $rules = [
        'expense_type_id' => 'required|exists:expense_types,id',
        'date_of_expense' => 'required|date',
        'amount'          => 'required|numeric|gte:0',
        'note'            => 'nullable|string',
        'bank_id'         => 'nullable|exists:banks,id',
    ];

    public function mount()
    {
        $this->categories = ExpenseType::orderBy('name')->get();
        $this->banks = Bank::all();
    }
    public function openModal()
    {
        $this->dispatch('open-modal');
    }

    public function render()
    {
        $expenses = $this->getExpense()->paginate(10);
        return view('livewire.admin.expense-management.all-expenses', compact('expenses'));
    }

    public function getExpense()
    {
        return Expense::with('expenseType')->latest()->dateFilter('date_of_expense');
    }

    public function store()
    {
        $this->validate();

        $expense = $this->expense_id ? Expense::findOrFail($this->expense_id) : new Expense();
        $notification = $this->expense_id ? 'Expense updated successfully' : 'Expense added successfully';

        $expense->expense_type_id = $this->expense_type_id;
        $expense->date_of_expense = Carbon::parse($this->date_of_expense);
        $expense->amount          = $this->amount;
        $expense->note            = $this->note;
        $expense->bank_id         = $this->bank_id;
        $expense->save();

        $bank = Bank::findOrFail($this->bank_id);

        // Step 1: Check if this is the first transaction for this bank
        $lastTransaction = BankTransaction::where('bank_id', $this->bank_id)->latest()->first();

        if ($lastTransaction) {
            // Not first transaction, use current_balance
            $openingBalance = $bank->current_balance;
        } else {
            // First transaction, use bank's opening_balance
            $openingBalance = $bank->opening_balance;
        }

        // Step 2: Credit and Debit (assuming expense)
        $creditAmount = $this->amount;
        $debitAmount = null;

        // Step 3: Closing balance
        $closingBalance = $openingBalance - $creditAmount;

        // Step 4: Create BankTransaction
        $bankTransaction = new BankTransaction();
        $bankTransaction->opening_balance = $openingBalance;
        $bankTransaction->closing_balance = $closingBalance;
        $bankTransaction->bank_id = $this->bank_id;
        $bankTransaction->transactable_id = $expense->id;
        $bankTransaction->debit = $debitAmount;
        $bankTransaction->credit = $creditAmount;
        $bankTransaction->amount = $this->amount;
        $bankTransaction->module_id = $expense->id;
        $bankTransaction->data_model = 'Expense';
        $bankTransaction->source = 'Expense';
        $bankTransaction->save();

        // Step 5: Update current_balance in bank table
        $bank->current_balance = $closingBalance;
        $bank->save();




        Action::newEntry($expense, $this->expense_id ? 'UPDATED' : 'CREATED');

        $this->dispatch('close-modal');
        $this->dispatch('notify', status: 'success', message: $notification);

        $this->reset(['expense_type_id', 'date_of_expense', 'amount', 'note', 'bank_id', 'expense_id']);
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $this->expense_type_id = $expense->expense_type_id;
        $this->date_of_expense = $expense->date_of_expense;
        $this->amount = $expense->amount;
        $this->note = $expense->note;
        $this->bank_id = $expense->bank_id;
        $this->selected_id = $expense->id;

        $this->dispatch('open-modal');
    }
    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->dispatch('show-delete-modal');
    }

    public function delete()
    {
        $expense = Expense::findOrFail($this->deleteId);



        $expense->delete();

        $this->dispatch('close-modal');
        $this->dispatch('notify', status: 'success', message: 'Expense deleted successfully!');
        $this->deleteId = null;
    }

    public function expenseCSV()
    {
        $expenses = $this->getExpense()->get();
        $filename = "assets/files/csv/expenses.csv";
        $file = fopen($filename, 'w');

        $column = "S.N.,Reason,Date,Amount\n";
        $curSym = gs('cur_sym');

        foreach ($expenses as $key => $expense) {
            $column .= ($key + 1) . "," . $expense->expenseType->name . "," . showDateTime($expense->date_of_expense, 'd-m-Y') . "," . $curSym . getAmount($expense->amount) . "\n";
        }

        fwrite($file, $column);
        fclose($file);

        return response()->download($filename);
    }

    public function expensePDF()
    {
        $expenses = $this->getExpense()->get();
        return downloadPDF('pdf.expense.list', [
            'pageTitle' => $this->pageTitle,
            'expenses' => $expenses
        ]);
    }

    public function import(Request $request)
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $csvData = file_get_contents($file->getRealPath());
        $rows = array_map('str_getcsv', explode("\n", $csvData));
        array_shift($rows); // Remove header
        array_pop($rows);   // Remove empty last row if exists

        $header = ['expense_type_id', 'date_of_expense', 'amount', 'note'];

        foreach ($rows as $row) {
            if (count($row) === count($header)) {
                $data = array_combine($header, $row);

                if (in_array(null, $data, true)) {
                    continue;
                }

                $type = strtolower(trim($data['expense_type_id']));
                $expenseType = ExpenseType::whereRaw('LOWER(name) = ?', [$type])->first();

                if (!$expenseType) continue;

                Expense::create([
                    'expense_type_id'  => $expenseType->id,
                    'date_of_expense'  => Carbon::parse($data['date_of_expense']),
                    'amount'           => $data['amount'],
                    'note'             => $data['note'],
                ]);
            }
        }

        $this->dispatch('notify', status: 'success', message: 'Expenses imported successfully');
    }
}