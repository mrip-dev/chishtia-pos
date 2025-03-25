<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $pageTitle = 'Brands';
        $brands    = Brand::searchable(['name'])->withCount('products')->latest()->paginate(getPaginate());
        return view('admin.brand.index', compact('pageTitle', 'brands'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:40|unique:brands,name,' . $id,
        ]);

        if ($id) {
            $brand        = Brand::findOrFail($id);
            $notification = $request->name . ' brand updated successfully';
        } else {
            $brand        = new Brand();
            $notification = $request->name . ' brand added successfully';
        }

        $brand->name = $request->name;
        $brand->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        $brand = Brand::withCount('products')->findOrFail($id);

        if ($brand->products_count) {
            $notify[] = ['error', 'You can\'t delete this brand because, it has some products'];
            return back()->withNotify($notify);
        }
        $brand->delete();
        $notify[] = ['success', 'Brand deleted successfully'];
        return back()->withNotify($notify);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name'];
        $importResult = importCSV($request, Brand::class, $reqHeader, $unique = "name");
        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
