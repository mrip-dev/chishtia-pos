<?php

namespace App\Livewire\Admin\SaleManagement;

use App\Models\Sale;
use Livewire\Component;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\ProductStock;
use App\Models\SaleDetail;
use App\Lib\Action;
use App\Models\Action as ModelsAction;
use App\Models\SaleDetails;

class AllSales extends Component
{
    public $sales = [];
    public $selectedSale = null;
    public $saleDetails = [];
    public $isEditing = false;
    public $isCreating = false;
    public $isDeleting = false;
    public $searchTerm = '';
    public $perPage = 10;

    public $successMessage;

    public $invoice_no;
    public $customer_id;
    public $sale_date;
    public $warehouse_id;


    public $searchQuery = '';
    public $searchResults = [];
    public $products = [];

    public $customers = [];
    public $warehouses = [];

    public $discount = 0;
    public $total_price = 0;
    public $note = '';

    public $received_amount = 0;
    public $due_amount = 0;
    public $receivable_amount = 0;

    public $editMode = false;
    public $saleId = null;



    public function mount()
    {
        $this->loadSales();
    }
    public function loadSales()
    {
        $this->sales = Sale::with(['customer', 'warehouse'])
            ->where('invoice_no', 'like', '%' . $this->searchTerm . '%')
            ->orWhereHas('customer', function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->orWhereHas('warehouse', function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%');
            })
            ->get();
    }


    public function createSale()
    {
        $this->isCreating = !$this->isCreating;
        $this->selectedSale = null;
        $this->saleDetails = [];

        $this->sale_date = now()->format('Y-m-d');
        $this->customers = Customer::select('id', 'name', 'mobile')->get();
        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
        $lastSale      = Sale::orderBy('id', 'DESC')->first();
        $lastInvoiceNo = $lastSale->invoice_no ?? 0;
        $this->invoice_no = generateInvoiceNumber($lastInvoiceNo);
    }
    public function editSale($id)
    {
        $this->saleId = $id ?? null;
        $this->isCreating = !$this->isCreating;
        $this->editMode = true;
        $this->saleId = $id;
        $this->loadSale($id);
        $this->selectedSale = Sale::find($id);
        $this->customers = Customer::select('id', 'name', 'mobile')->get();
        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
    }
    public function loadSale($id)
    {
        $sale = Sale::with('saleDetails')->findOrFail($id);

        $this->invoice_no = $sale->invoice_no;
        $this->customer_id = $sale->customer_id;
        $this->warehouse_id = $sale->warehouse_id;
        $this->sale_date = $sale->sale_date;
        $this->note = $sale->note;
        $this->discount = $sale->discount_amount;
        $this->total_price = $sale->total_price;
        $this->received_amount = $sale->received_amount;
        $this->receivable_amount = $sale->receivable_amount;
        $this->due_amount = $sale->due_amount;
        $this->products = [];

        foreach ($sale->saleDetails as $item) {
            $this->products[] = [
                'id'       => $item->product->id,
                'name'     => $item->product->name,
                'sku'      => $item->product->sku,
                'stock'    => $item->product->productStock->first()->quantity ?? 0,
                'quantity' => $item->quantity,
                'price'    => $item->price,
                'total'    => $item->price * $item->quantity,
            ];
        }

        $this->recalculateTotals();
        $this->getTotalPrice();
    }
    public function getProducts()

    {
        $warehouse = $this->warehouse_id;
        if (!$warehouse) {
            $this->searchResults = [];
            $this->dispatch('notify', status: 'error', message: 'Select Whereouse First');
        }
        $products  = Product::query()->whereHas('productStock', function ($q) use ($warehouse) {
            $q->where('warehouse_id', $warehouse)->where('quantity', '>', 0);
        });

        $products = $products->with('productStock')->where(function ($query) {
            $query->searchable(['name', 'sku']);
        });

        $this->searchResults = $products->with('unit')->get();
    }

    public function updated($name, $value)
    {
        if ($name === 'searchQuery') {
            $this->getProducts();
        }
        if ($name === 'received_amount') {
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
        if ($name === 'discount') {
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
        if (str_contains($name, 'quantity')) {
            $this->checkStockAvailability();
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
        if (str($name)->contains(['price'])) {
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
    }
    public function recalculateTotals()
    {

        foreach ($this->products as $index => $product) {
            $price = $product['price'] ?? 0;
            $quantity = $product['quantity'] ?? 0;
            $this->products[$index]['total'] = (float)$price * (float)$quantity;
        }
    }
    public function resetForm()
    {
        $this->reset([
            'products',
            'customer_id',
            'warehouse_id',
            'note',
            'discount',
            'searchQuery',
            'searchResults',
        ]);
    }

    public function addProduct($productId)
    {
        $product = Product::find($productId);

        if (!$product || !$product->productStock) return;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price ?? 0,
            'quantity' => 1,
            'stock' => $product->productStock->first()->quantity ?? 0,
            'total' => $product->price ?? 0,
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
        $this->total_price = collect($this->products)->sum('total');
        $this->receivable_amount = (float)$this->total_price - (float)$this->discount;
        $this->due_amount = (float)$this->receivable_amount - (float)$this->received_amount;
    }
    public function saveSale()
    {
        $this->validate([
            'invoice_no'    => 'required',
            'customer_id'   => 'required',
            'sale_date'     => 'required',
            'warehouse_id'  => 'required',
            'products'      => 'required|array',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.price'    => 'required|numeric|min:0',
        ]);

        // Calculate total price
        $totalPrice = collect($this->products)->sum(function ($product) {
            return $product['quantity'] * $product['price'];
        });

        // Discount validation
        if (!empty($this->discount) && $this->discount > $totalPrice) {
            $this->addError('discount', 'Discount must not be greater than total price.');
            return;
        }

        // Fetch product stock information for the warehouse
        $productIds = collect($this->products)->pluck('id')->toArray();
        $productStocks = \App\Models\ProductStock::where('warehouse_id', $this->warehouse_id)
            ->whereIn('product_id', $productIds)
            ->get();

        // Stock validation

        foreach ($this->products as $product) {
            $productId = $product['id'];
            $newQty = $product['quantity'];

            $stock = $productStocks->firstWhere('product_id', $productId);

            // If editing, get the old quantity to calculate the difference
            $oldQty = 0;
            if (!empty($this->saleId)) {
                $oldDetail = SaleDetails::where('sale_id', $this->saleId)
                    ->where('product_id', $productId)
                    ->first();
                $oldQty = $oldDetail ? $oldDetail->quantity : 0;
            }

            // Only check if new quantity is greater than old quantity
            $additionalQtyNeeded = $newQty - $oldQty;

            if ($additionalQtyNeeded > 0) {
                if (!$stock || $stock->quantity < $additionalQtyNeeded) {
                    $this->addError('products.' . $productId, 'Insufficient stock for product ID ' . $productId);
                    return;
                }
            }
        }




        DB::beginTransaction();
        try {
            // If editing, fetch the existing sale, else create a new one
            $sale = $this->saleId ? Sale::find($this->saleId) : new Sale();

            // Update sale details or create a new one
            $sale->invoice_no = $this->invoice_no;
            $sale->customer_id = $this->customer_id;
            $sale->warehouse_id = $this->warehouse_id;
            $sale->sale_date = $this->sale_date;
            $sale->note = $this->note ?? null;
            $sale->total_price = $totalPrice;
            $sale->discount_amount = $this->discount ?? 0;
            $sale->receivable_amount = $this->receivable_amount;
            $sale->due_amount = $this->due_amount;
            $sale->received_amount = $this->received_amount ?? 0;
            $sale->save();

            // Prepare sale details data
            $saleDetailsData = collect($this->products)->map(function ($product) use ($sale) {
                return [
                    'sale_id' => $sale->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['quantity'] * $product['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
            // Update product stock (assuming you have logic for this)
            $this->updateProductStock($this->saleId ?? null);
            // Delete existing sale details if editing (avoid duplicates)
            if ($this->saleId) {
                SaleDetails::where('sale_id', $sale->id)->delete();
            }

            // Insert new sale details
            SaleDetails::insert($saleDetailsData);

            DB::commit();


            // Notify the user of success
            $this->dispatch('notify', status: 'success', message: 'Sale saved successfully');

            // Reset state and reload sales
            $this->isCreating = !$this->isCreating;
            $this->resetForm();
            $this->loadSales();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('form', 'An error occurred: ' . $e->getMessage());
        }
    }

    protected function checkStockAvailability()
    {
        $products = $this->products;
        $notify = [];

        foreach ($products as $index => $product) {
            $product = (object) $product;

            $productStock = ProductStock::where('product_id', $product->id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            // Calculate old quantity if in edit mode
            $oldQty = 0;
            if (!empty($this->saleId)) {
                $oldDetail = SaleDetails::where('sale_id', $this->saleId)
                    ->where('product_id', $product->id)
                    ->first();
                $oldQty = $oldDetail ? $oldDetail->quantity : 0;
            }

            // How much more stock is needed?
            $additionalQtyNeeded = $product->quantity - $oldQty;

            // Check if the additional quantity can be fulfilled
            if ($additionalQtyNeeded > 0 && (!$productStock || $additionalQtyNeeded > $productStock->quantity)) {
                $maxQty = $oldQty + ($productStock ? $productStock->quantity : 0);

                // Update product quantity to maximum allowed
                $this->products[$index]['quantity'] = $maxQty;
                $this->products[$index]['total'] = $product->price * $maxQty;

                $notify[] = [
                    'product' => $product->name ?? 'Unnamed Product',
                    'available' => $productStock ? $productStock->quantity : 0,
                    'requested' => $additionalQtyNeeded,
                ];
            }
        }

        if (count($notify) > 0) {
            $message = 'Stock not available for the following products: ';
            foreach ($notify as $item) {
                $message .= $item['product'] . ' (Available: ' . $item['available'] . ', Requested: ' . $item['requested'] . ') ';
            }
            $this->dispatch('notify', status: 'error', message: $message);
            return false;
        }

        return true;
    }

    protected function updateProductStock($saleId = null)
    {
        if ($saleId) {
            // Restore old stock before updating
            $oldSaleDetails = SaleDetails::where('sale_id', $saleId)->get();

            foreach ($oldSaleDetails as $detail) {
                $productStock = ProductStock::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $this->warehouse_id)
                    ->first();

                if ($productStock) {
                    $productStock->quantity += $detail->quantity; // Restore stock
                    $productStock->save();
                }
            }
        }

        // Deduct new product quantities
        foreach ($this->products as $product) {
            $product = (object) $product;

            if (empty($product->quantity) || $product->quantity <= 0) continue;

            $productStock = ProductStock::where('product_id', $product->id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            if ($productStock) {
                $productStock->quantity -= $product->quantity;
                $productStock->save();
            }
        }
    }
    public function payMentModal($id)
    {
       dd('id');
    }

    public function render()
    {
        return view('livewire.admin.sale-management.all-sales');
    }
}
