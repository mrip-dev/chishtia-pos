<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseType;
use Illuminate\Http\Request;

class ExpenseTypeController extends Controller
{
    public function index()
    {
        $pageTitle = 'Expense Types';
        $types     = ExpenseType::withCount('expenses')->latest()->paginate(getPaginate());
        return view('admin.expense.type', compact('pageTitle', 'types'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:40|unique:expense_types,name,' . $id,
        ]);

        if ($id) {
            $type         = ExpenseType::findOrFail($id);
            $notification = 'Expense type updated successfully';
        } else {
            $type         = new ExpenseType();
            $notification = 'Expense type added successfully';
        }

        $type->name = $request->name;
        $type->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        $type = ExpenseType::withCount('expenses')->findOrFail($id);

        if ($type->expenses_count) {
            $notify[] = ['error', 'You can\'t delete this expense type because, it has some expenses'];
            return back()->withNotify($notify);
        }

        $type->delete();
        $notify[] = ['success', 'Expense type deleted successfully'];
        return back()->withNotify($notify);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name'];
        $importResult = importCSV($request, ExpenseType::class, $reqHeader, $unique = "name");

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
