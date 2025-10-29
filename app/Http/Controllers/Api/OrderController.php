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

class OrderController extends Controller
{
    public $warehouse_id = 1;

    public function getAllOrders()
    {
        $orders = Sale::with('saleDetails')->get();

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ]);
    }




    public function saveOrder(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable',
            'warehouse_id' => 'nullable',
            'customer_name' => 'nullable',
            'customer_phone' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable',
            'items.*.tax' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Generate unique invoice number
            $today = now()->format('Ymd');
            $lastSale = Sale::whereDate('created_at', now()->toDateString())->latest('id')->first();
            $nextNumber = $lastSale ? ((int) Str::afterLast($lastSale->invoice_no, '-') + 1) : 1;
            $invoiceNo = 'INV-' . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // 🔹 Calculate total
            $total = collect($request->items)->sum(function ($item) {
                $discount = $item['discount'] ?? 0;
                $tax = $item['tax'] ?? 0;
                return ($item['quantity'] * $item['price']) - $discount + $tax;
            });

            // 🔹 Create Sale
            $sale = Sale::create([
                'invoice_no' => $invoiceNo,
                'customer_id' => $request->customer_id ?? Customer::first()?->id ?? 1,
                'warehouse_id' => $request->warehouse_id ?? Warehouse::first()?->id ?? 1,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'total_price' => $total,
                'status' => 'pending',
                'sale_date' => now(),
            ]);

            // 🔹 Create Sale Details
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
                    'invoice_no' => $sale->invoice_no,
                    'customer_id' => $sale->customer_id,
                    'warehouse_id' => $sale->warehouse_id,
                    'customer_name' => $sale->customer_name,
                    'customer_phone' => $sale->customer_phone,
                    'total_price' => $sale->total_price,
                    'status' => $sale->status,
                    'items' => collect($saleDetails)->map(fn($item) => [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'discount' => $item['discount'],
                        'tax' => $item['tax'],
                        'total' => $item['total'],
                    ]),
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


    public function updateOrder(Request $request, $id)
    {

        $request->validate([
            'customer_id' => 'nullable|numeric|min:1',
            'warehouse_id' => 'nullable|numeric|min:1',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            // Recalculate total
            $total = collect($request->items)->sum(function ($item) {
                $discount = $item['discount'] ?? 0;
                $tax = $item['tax'] ?? 0;
                $subtotal = ($item['quantity'] * $item['price']) - $discount + $tax;
                return $subtotal;
            });

            // Update sale
            $sale->update([
                'customer_id' => $request->customer_id,
                'warehouse_id' => $request->warehouse_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'total_price' => $total,

                'sale_date' => now(),
            ]);

            // Delete old items
            $sale->saleDetails()->delete();

            // Insert new items
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
                'message' => 'Order updated successfully',
                'data' => [
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'warehouse_id' => $sale->warehouse_id,
                    'customer_name' => $sale->customer_name,
                    'customer_phone' => $sale->customer_phone,
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
                'message' => 'Failed to update order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
