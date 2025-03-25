<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $pageTitle  = 'Categories';
        $categories = Category::searchable(['name'])->withCount('products')->latest()->paginate(getPaginate());
        return view('admin.category.index', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:40|unique:categories,name,' . $id,
        ]);

        if ($id) {
            $category     = Category::findOrFail($id);
            $notification = 'Category updated successfully';
        } else {
            $category     = new Category();
            $notification = 'Category added successfully';
        }

        $category->name = $request->name;
        $category->save();
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function remove($id)
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count) {
            $notify[] = ['error', 'You can\'t delete this category because, it has some products'];
            return back()->withNotify($notify);
        }
        $category->delete();
        $notify[] = ['success', 'Category deleted successfully'];
        return back()->withNotify($notify);
    }

    public function import(Request $request)
    {
        $reqHeader    = ['name'];
        $importResult = importCSV($request, Category::class, $reqHeader, $unique = "name");

        if ($importResult['data']) {
            $notify[] = ['success', $importResult['notify']];
        } else {
            $notify[] = ['error', 'No new data imported'];
        }
        return back()->withNotify($notify);
    }
}
