<?php

namespace App\Traits;

use App\Models\Expense;
use App\Models\Bank;
use App\Models\Action;
use App\Models\BankTransaction;
use Carbon\Carbon;

trait ManagesExpenseTransactions
{
    use DailyBookEntryTrait;

    /**
     * Creates an expense, updates bank balance, and logs daily book entry.
     *
     * @param int $expenseTypeId
     * @param string $dateOfExpense YYYY-MM-DD format
     * @param float $amount
     * @param int|null $bankId If null, no bank transaction or daily book entry related to bank.
     * @param string|null $note
     * @param int|null $existingExpenseId For updating an existing expense.
     * @return Expense The created or updated Expense model.
     */
    protected function createOrUpdateExpenseTransaction(
        int $expenseTypeId,
        string $dateOfExpense,
        float $amount,
        ?int $bankId, // This is the bank from which the expense is paid
        ?string $note = null,
        ?int $existingExpenseId = null,
        ?string $dataModel_name = null,
        ?int $dataModel_id = null
    ): Expense {
        // --- 1. Create or Update Expense ---
        $expense = $existingExpenseId ? Expense::findOrFail($existingExpenseId) : new Expense();
        $isUpdate = (bool)$existingExpenseId;

        $oldAmount = $isUpdate ? $expense->amount : 0;
        $oldBankId = $isUpdate ? $expense->bank_id : null;

        $expense->expense_type_id = $expenseTypeId;
        $expense->date_of_expense = Carbon::parse($dateOfExpense);
        $expense->amount          = $amount;
        $expense->note            = $note;
        $expense->bank_id         = $bankId;
        $expense->data_model      = $dataModel_name; // Assuming this is the data model name
        $expense->model_id       = $dataModel_id ; // Assuming module_id is the same as expense id
        $expense->save();

        Action::newEntry($expense, $isUpdate ? 'UPDATED' : 'CREATED');

        // --- 2. Handle Bank Transaction (if bank_id is provided) ---
        if ($bankId) {
            $bank = Bank::findOrFail($bankId);
            $lastBankTransaction = BankTransaction::where('bank_id', $bankId)->latest('id')->first();

            $openingBalanceForTx = $lastBankTransaction ? $lastBankTransaction->closing_balance : $bank->opening_balance;
            $creditAmountForBankTx = $amount; // Expense: money out of bank
            $closingBalanceForTx = $openingBalanceForTx - $creditAmountForBankTx;

            BankTransaction::create([
                'opening_balance'   => $openingBalanceForTx,
                'closing_balance'   => $closingBalanceForTx,
                'bank_id'           => $bankId,
                'transactable_id'   => $expense->id,
                'transactable_type' => Expense::class,
                'debit'             => null,
                'credit'            => $creditAmountForBankTx,
                'amount'            => $amount,
                'module_id'         => $expense->id,
                'data_model'        => 'Expense',
                'source'            => 'Expense (' . $bank->name . ')', // More descriptive source
                'transaction_date'  => $expense->date_of_expense,
            ]);

            $latestTxForBank = BankTransaction::where('bank_id', $bankId)->latest('id')->first();
            $bank->current_balance = $latestTxForBank ? $latestTxForBank->closing_balance : $bank->opening_balance;
            $bank->save();

            // --- 3. Handle Daily Book Entry using DailyBookEntryTrait ---

            $this->handleDailyBookEntries(
                0,              // $amountPaid (cash part, 0 for pure bank expense)
                $amount,        // $amountPaidBank (bank part)
                'credit',       // $transaction_type (expense is a credit/outgoing from daily book perspective)
                'bank',         // $payment_method
                'Expense',      // $dataModel_name
                $expense->id    // $dataModel_id
            );

        } elseif (!$bankId && $amount > 0) {
            // This is a cash expense (not paid from a specific bank account tracked in `banks` table)

            $this->handleDailyBookEntries(
                $amount,        // $amountPaid (cash part)
                0,              // $amountPaidBank (bank part, 0 for pure cash expense)
                'credit',       // $transaction_type (expense is a credit/outgoing)
                'cash',         // $payment_method
                'Expense',      // $dataModel_name
                $expense->id    // $dataModel_id
            );
        }


        return $expense;
    }


}