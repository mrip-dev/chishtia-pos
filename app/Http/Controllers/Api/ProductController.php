<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Order;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public $warehouse_id = 1;
    public function create()
    {
        $pageTitle = 'Create Order';
        $invoiceNumber = $this->generateInvoiceNumber(); // Your invoice generation logic

        // Get products with category relationship
        $products = Product::with(['category', 'unit', 'brand'])
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'display_title' => $product->name, // Or your custom title logic
                    'category_name' => $product->category->name ?? 'Uncategorized',
                    'brand_name' => $product->brand->name ?? '',
                    'unit' => [
                        'id' => $product->unit->id,
                        'name' => $product->unit->name
                    ],
                    'selling_price' => $product->selling_price,
                    'image_url' => $product->image ? getImage(imagePath()['product']['path'] . '/' . $product->image) : getImage('assets/images/default.png')
                ];
            });

        $customers = Customer::active()
            ->orderBy('name')
            ->get(['id', 'name', 'mobile']);

        return view('admin.order.form', compact('pageTitle', 'products', 'customers', 'invoiceNumber'));
    }

    public function edit($id)
    {
        $pageTitle = 'Edit Order';
        $sale = Sale::with(['customer', 'saleDetails.product.category', 'saleDetails.product.unit', 'saleReturn'])
            ->findOrFail($id);

        // Get products with category relationship
        $products = Product::with(['category', 'unit', 'brand'])
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'display_title' => $product->name,
                    'category_name' => $product->category->name ?? 'Uncategorized',
                    'brand_name' => $product->brand->name ?? '',
                    'unit' => [
                        'id' => $product->unit->id,
                        'name' => $product->unit->name
                    ],
                    'selling_price' => $product->selling_price,
                    'image_url' => $product->image ? getImage(imagePath()['product']['path'] . '/' . $product->image) : getImage('assets/images/default.png')
                ];
            });

        $customers = Customer::active()
            ->orderBy('name')
            ->get(['id', 'name', 'mobile']);

        return view('admin.order.form', compact('pageTitle', 'sale', 'products', 'customers'));
    }

    // Helper method for invoice generation
    private function generateInvoiceNumber()
    {
        $lastSale = Sale::latest('id')->first();
        $lastNumber = $lastSale ? intval(substr($lastSale->invoice_no, -6)) : 0;
        return 'INV-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function getAllCategories()
    {
        $categories = Category::all();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }
    public function getAllBrands()
    {
        $brands = Brand::all();

        return response()->json([
            'status' => 'success',
            'data' => $brands,
        ]);
    }
    public function getAllUnits()
    {
        $units = Unit::all();

        return response()->json([
            'status' => 'success',
            'data' => $units,
        ]);
    }
}
