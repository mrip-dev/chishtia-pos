<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Expense;
use App\Models\ExpenseType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Expenses';
    }

    protected function getExpense()
    {
        return  Expense::dateFilter('date_of_expense')->with('expenseType')->latest();
    }

    public function index($id = 0)
    {
        $pageTitle  = $this->pageTitle;
        $categories = ExpenseType::orderBy('name')->get();
        $expenses   = $this->getExpense();
        if ($id) {
            $expenses->where('id', $id);
        }
        $expenses = $expenses->paginate(getPaginate());
        return view('admin.expense.index', compact('pageTitle', 'expenses', 'categories'));
    }

    public function expensePDF()
    {
        $pageTitle = $this->pageTitle;
        $expenses  = $this->getExpense()->get();
        return downloadPDF('pdf.expense.list', compact('pageTitle', 'expenses'));
    }
    public function expenseCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getExpense()->get());
        return response()->download(...$filename);
    }

    protected function downloadCSV($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "S.N.,Reason,Date,Amount\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $key => $expense) {
            $reason = $expense->expenseType->name;
            $date   = showDateTime(@$expense->date_of_expense, 'd-m-Y');
            $amount = $curSym . getAmount($expense->amount);
            $sn     = $key + 1;

            $column .= "$sn,$reason,$date,$amount \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'expense_type_id' => 'required|string|max:40|exists:expense_types,id',
            'date_of_expense' => 'required|date',
            'amount'          => 'required|numeric|gte:0',
            'note'            => 'nullable|string',
        ]);

        if ($request->id) {
            $expense      = Expense::findOrFail($id);
            $notification = 'Expense updated successfully';
        } else {
            $expense      = new Expense();
            $notification = 'Expense added successfully';
        }
        $expense->expense_type_id = $request->expense_type_id;
        $expense->date_of_expense = Carbon::parse($request->date_of_expense);
        $expense->amount          = $request->amount;
        $expense->note            = $request->note;
        $expense->save();
        $notify[] = ['success', $notification];
        Action::newEntry($expense, $id ? 'UPDATED' : 'CREATED');
        return back()->withNotify($notify);
    }

    public function import(Request $request)
    {
        importFileValidation($request);

        $file    = $request->file('file');
        $csvData = file_get_contents($file->getRealPath());
        $rows    = array_map('str_getcsv', explode("\n", $csvData));
        array_shift($rows);
        array_pop($rows);
        $header      = ['expense_type_id', 'date_of_expense', 'amount', 'note'];
        $expenseData = [];
        foreach ($rows as $row) {
            if (count($header) === count($row)) {
                $data = array_combine($header, $row);
                if (in_array(null, $data) || $data === null) {
                    continue;
                }
                $type      = strtolower($data['expense_type_id']);
                $checkType = ExpenseType::where('name', $type)->first();
                if (!$checkType) {
                    $notify[] = ['error', 'Mismatch in expense type: ' . $type];
                    return back()->withNotify($notify);
                }
                $data['expense_type_id']      = $checkType->id;
                $data['date_of_expense']      = $row[1];
                $data['amount']               = $row[2];
                $data['note']                 = $row[3];
                $expenseData[$data['amount']] = $data;
            }
        }
        if (count($expenseData) > 0) {
            Expense::insert($expenseData);
            $notify[] = ['success', 'CSV imported successfully'];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
