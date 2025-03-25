<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $pageTitle  = 'All Warehouses';
        $warehouses = Warehouse::searchable(['name', 'address'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.warehouse.index', compact('pageTitle', 'warehouses'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'    => 'required|string|max:40|unique:warehouses,name,' . $id,
            'address' => 'required|string|max:500',
        ]);

        if ($id) {
            $warehouse    = Warehouse::findOrFail($id);
            $notification = 'Warehouse updated successfully';
        } else {
            $warehouse    = new Warehouse();
            $notification = 'Warehouse added successfully';
        }
        $warehouse->name    = $request->name;
        $warehouse->address = $request->address;
        $warehouse->save();
        $notify[] = ['success', $notification];
        return to_route('admin.warehouse.index')->withNotify($notify);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name', 'address'];
        $importResult = importCSV($request, Warehouse::class, $reqHeader, $unique = "name");

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported!'];
        }
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Warehouse::changeStatus($id);
    }
}
