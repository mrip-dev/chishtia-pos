<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $pageTitle = 'Units';
        $units     = Unit::searchable(['name'])->withCount('products')->latest()->paginate(getPaginate());
        return view('admin.unit.index', compact('pageTitle', 'units'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:40|unique:units,name,' . $id,
        ]);

        if ($id) {
            $unit         = Unit::findOrFail($id);
            $notification = $request->name . ' unit updated successfully';
        } else {
            $unit         = new Unit();
            $notification = $request->name . ' unit added successfully';
        }

        $unit->name = $request->name;
        $unit->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        $unit = Unit::withCount('products')->findOrFail($id);

        if ($unit->products_count) {
            $notify[] = ['error', 'You can\'t delete this unit because, it has some products'];
            return back()->withNotify($notify);
        }

        $unit->delete();
        $notify[] = ['success', 'Unit deleted successfully'];
        return back()->withNotify($notify);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name'];
        $importResult = importCSV($request, Unit::class, $reqHeader, $unique = "name");
        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
