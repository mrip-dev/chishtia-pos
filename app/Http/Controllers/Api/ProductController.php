<?php

namespace App\Http\Controllers\Api;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\Brand;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SaleReturn;
use App\Models\Supplier;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    public $warehouse_id = 1;
    public function getAllProducts()
    {
        $products = Product::with(['brand:id,name', 'category:id,name', 'unit:id,name'])
            ->select('id', 'name', 'sku', 'price', 'image', 'brand_id', 'category_id', 'unit_id')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image,
                    'price' => $product->price,
                    'brand' => $product->brand->name ?? null,
                    'category' => $product->category->name ?? null,
                    'unit' => $product->unit->name ?? null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $products,
        ]);
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

    public function saveOrder(Request $request)
    {

        $request->validate([
            'customer_id' => 'nullable|numeric|min:1',
            'warehouse_id' => 'nullable|numeric|min:1',
            'customer_name' => 'nullable',
            'customer_phone' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calculate grand total
            $total = collect($request->items)->sum(function ($item) {
                $discount = $item['discount'] ?? 0;
                $tax = $item['tax'] ?? 0;
                $subtotal = ($item['quantity'] * $item['price']) - $discount + $tax;
                return $subtotal;
            });

            // Create Sale (acts as Order)
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'total_price' => $total,
                'status' => 'pending',
                'sale_date' => now(),
            ]);

            // Insert Sale Details (acts as Order Items)
            $saleDetails = [];
            foreach ($request->items as $item) {
                $discount = $item['discount'] ?? 0;
                $tax = $item['tax'] ?? 0;
                $subtotal = ($item['quantity'] * $item['price']) - $discount + $tax;

                $saleDetails[] = [
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $discount,
                    'tax' => $tax,
                    'total' => $subtotal,

                ];
            }

            SaleDetails::insert($saleDetails);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Order saved successfully',
                'data' => [
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'warehouse_id' => $sale->warehouse_id,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'total_price' => $sale->total_price,
                    'status' => $sale->status,
                    'items' => collect($saleDetails)->map(function ($item) {
                        return [
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'discount' => $item['discount'],
                            'tax' => $item['tax'],
                            'total' => $item['total'],
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
