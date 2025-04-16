<?php

namespace App\Livewire\Admin\SaleManagement;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\ProductStock;
use App\Models\SaleDetail;
use App\Lib\Action;
use App\Models\Action as ModelsAction;
use App\Models\SaleDetails;

class CreateSale extends Component
{
    public $invoice_no;
    public $customer_id;
    public $sale_date;
    public $warehouse_id;
    public $note;

    public $searchQuery = '';
    public $searchResults = [];
    public $products = [];

    public function mount()
    {
        $this->invoice_no = 'INV-' . time(); // Or however you want to generate it
        $this->sale_date = now()->format('Y-m-d');
    }

    public function getProducts()
    {
        $this->searchResults = Product::where('name', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('sku', 'like', '%' . $this->searchQuery . '%')
            ->limit(5)
            ->get();
    }
    public function addProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product) return;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price ?? 0,
            'quantity' => 1,
            'stock' => $product->alert_quantity ?? 0,
        ];

        $this->searchQuery = '';
        $this->searchResults = [];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products); // reindex
    }

    public function getTotalPrice()
    {
        return collect($this->products)->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });
    }



    public function saveSale()
    {

        $this->validate([
            'invoice_no'    => 'required',
            'customer_id'   => 'required',
            'sale_date'     => 'required',
            'warehouse_id'  => 'required',
            'products'      => 'required',
            'products.*.id' => 'nullable',
            'products.*.quantity'   => 'nullable',
            'products.*.price'      => 'nullable',

        ]);

        $totalPrice = $this->getTotalPrice();

        // Optional: check discount logic if it's part of your component
        if (!empty($this->discount) && $this->discount > $totalPrice) {
            $this->addError('discount', 'Discount must not be greater than total price.');
            return;
        }

        // Check warehouse stock
        $productIds = collect($this->products)->pluck('id')->toArray();
        $productStocks = \App\Models\ProductStock::where('warehouse_id', $this->warehouse_id)
            ->whereIn('id', $productIds)
            ->get();


        // Proceed to save
        DB::beginTransaction();
        try {
            $sale = new Sale();
            $sale->invoice_no = $this->invoice_no;
            $sale->customer_id = $this->customer_id;
            $sale->warehouse_id = $this->warehouse_id;
            $sale->sale_date = $this->sale_date;
            $sale->note = $this->note ?? null;
            $sale->total_price = $totalPrice;
            $sale->save();

            foreach ($this->products as $product) {
                SaleDetails::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['price'],
                ]);

            }


            DB::commit();

            $this->emit('saleSaved', 'Sale saved successfully!');

        } catch (\Exception $e) {
            dd($e);
        }
    }


    public function render()
    {
        return view('livewire.admin.sale-management.create-sale', [
            'customers' => Customer::all(),
            'warehouses' => Warehouse::all(),
        ]);
    }
}
