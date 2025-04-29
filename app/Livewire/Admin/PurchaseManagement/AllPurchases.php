<?php

namespace App\Livewire\Admin\PurchaseManagement;

use App\Models\Bank;
use App\Models\BankTransaction;
use App\Models\SupplierTransaction as ModelsSupplierTransaction;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Warehouse;
use App\Models\WareHouseDetailHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Livewire\Component;

class AllPurchases extends Component
{   public $purchases = [];
    public $banks = [];
    public $selectedPurchase = null;
    public $purchaseDetails = [];
    public $isEditing = false;
    public $isCreating = false;
    public $isDeleting = false;
    public $searchTerm = '';
    public $searchByDate;
    public $perPage = 10;
    public $successMessage;
    public $invoice_no;
    public $supplier_id;
    public $purchase_date;
    public $warehouse_id;
    public $searchQuery = '';
    public $searchResults = [];
    public $products = [];
    public $suppliers = [];
    public $warehouses = [];
    public $discount = 0;
    public $total_price = 0;
    public $note = '';
    public $paid_amount = 0;
    public $due_amount = 0;
    public $payable_amount = 0;
    public $editMode = false;
    public $purchaseId = null;
    public $searchInput = '';
    public $paymentPurchase;
    public $modal_invoice_no;
    public $modal_supplier_name;
    public $modal_paid_amount;
    public $bankId;
    public $modal_payment_method = '';
    public $modal_rec_bank;
    public $modal_payable_amount;
    public $vehicle_number = '';
    public $driver_name = '';
    public $driver_contact = '';
    public $fare = 0;

    protected function rules()
    {
        return [
            'modal_payment_method' => 'required|string',

            'modal_paid_amount' => [
                Rule::requiredIf(function () {
                    return in_array($this->modal_payment_method, ['cash', 'both']);
                }),


            ],

            'modal_rec_bank' => [
                Rule::requiredIf(function () {
                    return in_array($this->modal_payment_method, ['bank', 'both']);
                }),


            ],
        ];
    }
    protected function messages()
    {
        return [
            'modal_rec_amount.required' => 'Cash amount is required when payment method is cash or both.',
            'modal_rec_bank.required'   => 'Bank amount is required when payment method is bank or both.',
        ];
    }
    public function mount()
    {
        $this->loadPurchases();
    }
    public function loadPurchases()
    {
        $this->purchases = Purchase::with(['supplier', 'warehouse'])
            ->where(function ($query) {
                $query->where('invoice_no', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('warehouse', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            })
            ->when($this->searchByDate, function ($query) {
                // Parse the date range
                [$start, $end] = explode(' - ', $this->searchByDate);
                $startDate = Carbon::parse($start)->startOfDay();
                $endDate = Carbon::parse($end)->endOfDay();

                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->get();
    }
    public function createPurchase()
    {
        $this->isCreating = !$this->isCreating;
        $this->selectedPurchase = null;
        $this->purchaseDetails = [];
        $this->supplier_id = null;
        $this->purchase_date = now()->format('Y-m-d');
        $this->suppliers = Supplier::select('id', 'name', 'mobile')->get();
        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
        $lastPurchase      = Purchase::orderBy('id', 'DESC')->first();
        $lastInvoiceNo = $lastPurchase->invoice_no ?? 0;


        $this->invoice_no = generateInvoiceNumberP($lastInvoiceNo);
    }
    public function editPurchase($id)
    {
        $this->purchaseId = $id ?? null;

        $this->isCreating = !$this->isCreating;
        $this->editMode = true;
        $this->purchaseId = $id;
        $this->loadPurchase($id);
        $this->selectedPurchase = Purchase::find($id);
        $this->suppliers = Supplier::select('id', 'name', 'mobile')->get();
        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
    }
    public function loadPurchase($id)
    {
        $purchase = Purchase::with('purchaseDetails')->findOrFail($id);

        $this->invoice_no = $purchase->invoice_no;
        $this->supplier_id = $purchase->supplier_id;
        $this->warehouse_id = $purchase->warehouse_id;
        $this->purchase_date = $purchase->purchase_date;
        $this->note = $purchase->note;
        $this->discount = $purchase->discount_amount;
        $this->total_price = $purchase->total_price;
        $this->paid_amount = $purchase->paid_amount;
        $this->payable_amount = $purchase->payable_amount;
        $this->due_amount = $purchase->due_amount;
        $this->vehicle_number = $purchase->vehicle_number;
        $this->driver_name = $purchase->driver_name;
        $this->driver_contact = $purchase->driver_contact;
        $this->fare = $purchase->fare;
        $this->products = [];

        foreach ($purchase->purchaseDetails as $item) {
            $this->products[] = [
                'id'       => $item->product->id,
                'name'     => $item->product->name,
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
        $products  = Product::query();

        $products = $products->with('productStock')->where(function ($query) {
            $query->searchable(['name', 'sku']);
        });

        $this->searchResults = $products->with('unit')->get();
    }
    public function updated($name, $value)
    {
        if ($name === 'searchTerm') {
            $this->loadPurchases();
        }
        if ($name === 'searchByDate') {
            $this->loadPurchases();
        }
        if ($name === 'searchQuery') {

            $this->getProducts();
        }
        if ($name === 'paid_amount') {
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
        if ($name === 'discount') {
            $this->recalculateTotals();
            $this->getTotalPrice();
        }
        if (str_contains($name, 'quantity')) {

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
            'supplier_id',
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

        if (!$product) return;

        $this->products[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price ?? 0,
            'quantity' => 1,
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
        $this->payable_amount = (float)$this->total_price - (float)$this->discount;
        $this->due_amount = (float)$this->payable_amount - (float)$this->paid_amount;
    }
    public function savePurchase()
    {
        $this->validate([
            'invoice_no'    => 'required',
            'supplier_id'   => 'required',
            'purchase_date'     => 'required',
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
            if (!empty($this->purchaseId)) {
                $oldDetail = PurchaseDetails::where('purchase_id', $this->saleId)
                    ->where('product_id', $productId)
                    ->first();
                $oldQty = $oldDetail ? $oldDetail->quantity : 0;
            }

            // Only check if new quantity is greater than old quantity
            $additionalQtyNeeded = $newQty + $oldQty;

            // if ($additionalQtyNeeded > 0) {
            //     if (!$stock || $stock->quantity < $additionalQtyNeeded) {
            //         $this->addError('products.' . $productId, 'Insufficient stock for product ID ' . $productId);
            //         return;
            //     }
            // }
        }




        DB::beginTransaction();
        try {
            // If editing, fetch the existing Purchase, else create a new one
            $purchase = $this->purchaseId ? Purchase::find($this->purchaseId) : new Purchase();

            // Update Purchase details or create a new one
            $purchase->invoice_no = $this->invoice_no;
            $purchase->supplier_id = $this->supplier_id;
            $purchase->warehouse_id = $this->warehouse_id;
            $purchase->purchase_date = $this->purchase_date;
            $purchase->note = $this->note ?? null;
            $purchase->total_price = $totalPrice;
            $purchase->discount_amount = $this->discount ?? 0;
            $purchase->payable_amount = $this->payable_amount;
            $purchase->due_amount = $this->due_amount;
            $purchase->paid_amount = $this->paid_amount ?? 0;
            $purchase->vehicle_number = $this->vehicle_number;
            $purchase->driver_name = $this->driver_name;
            $purchase->driver_contact = $this->driver_contact;
            $purchase->fare = $this->fare;
            $purchase->save();

            // Prepare sale details data
            $purchaseDetailData = collect($this->products)->map(function ($product) use ($purchase) {
                return [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total' => $product['quantity'] * $product['price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();
            // Update product stock (assuming you have logic for this)
            $this->updateProductStock($this->purchaseId ?? null);
            // Delete existing sale details if editing (avoid duplicates)
            if ($this->purchaseId) {
                PurchaseDetails::where('purchase_id', $purchase->id)->delete();
            }

            // Insert new sale details
            PurchaseDetails::insert($purchaseDetailData);

            DB::commit();

            $wareHouseDetail = new WareHouseDetailHistory();
            $wareHouseDetail->ware_house_id = $this->warehouse_id;
            $wareHouseDetail->product_id = $product['id'];
            $wareHouseDetail->supplier_id = $this->supplier_id;
            $wareHouseDetail->customer_id = 0;
            $wareHouseDetail->date = now();
            $wareHouseDetail->stock_in = $product['quantity'];
            $wareHouseDetail->stock_out = 0;
            $wareHouseDetail->amount = $product['quantity'] * $product['price'];
            $wareHouseDetail->save();




            // Notify the user of success
            $this->dispatch('notify', status: 'success', message: 'Purchase saved successfully');

            // Reset state and reload purchases
            $this->isCreating = !$this->isCreating;
            $this->resetForm();
            $this->loadPurchases();
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
            if (!empty($this->purchaseId)) {
                $oldDetail = PurchaseDetails::where('purchase_id', $this->purchaseId)
                    ->where('product_id', $product->id)
                    ->first();
                $oldQty = $oldDetail ? $oldDetail->quantity : 0;
            }

            // How much more stock is needed?
            $additionalQtyNeeded = $product->quantity + $oldQty;

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

        // if (count($notify) > 0) {
        //     $message = 'Stock not available for the following products: ';
        //     foreach ($notify as $item) {
        //         $message .= $item['product'] . ' (Available: ' . $item['available'] . ', Requested: ' . $item['requested'] . ') ';
        //     }
        //     $this->dispatch('notify', status: 'error', message: $message);
        //     return false;
        // }

        return true;
    }
    protected function updateProductStock($purchaseId = null)
    {
        if ($purchaseId) {
            // Restore old stock before updating
            $oldPurchaseDetails = PurchaseDetails::where('purchase_id', $purchaseId)->get();

            foreach ($oldPurchaseDetails as $detail) {
                $productStock = ProductStock::where('product_id', $detail->product_id)
                    ->where('warehouse_id', $this->warehouse_id)
                    ->first();

                if ($productStock) {
                    $productStock->quantity -= $detail->quantity; // Restore stock
                    $productStock->save();
                }
            }
        }

        // ADD new product quantities
        foreach ($this->products as $product) {
            $product = (object) $product;

            if (empty($product->quantity) || $product->quantity <= 0) continue;

            $productStock = ProductStock::where('product_id', $product->id)
                ->where('warehouse_id', $this->warehouse_id)
                ->first();

            if ($productStock) {
                $productStock->quantity += $product->quantity;
                $productStock->save();
            }else {
                $stock= new ProductStock();
                $stock->warehouse_id = $this->warehouse_id;
                $stock->product_id   = $product->id;
                $stock->quantity     = $product->quantity;
                $stock->save();
            }
        }
    }
    public function payMentModal($id)
    {
        $this->banks = Bank::all();
        $this->paymentPurchase = Purchase::with('supplier')->find($id);
        $this->modal_invoice_no = $this->paymentPurchase->invoice_no;
        $this->modal_supplier_name = $this->paymentPurchase->supplier()->first()->name;
        $this->modal_payable_amount = $this->paymentPurchase->due_amount;
        $this->modal_payment_method = $this->paymentPurchase->payment_method;
        $this->modal_rec_bank = 0.00;
        $this->modal_paid_amount = 0.00;
    }
    public function submitPayment()
    {
        $this->validate();

        $purchase = $this->paymentPurchase;
        $isPaying = $purchase->due_amount < 0;
        $amount_cash = $this->modal_paid_amount;
        $amount_bank = $this->modal_rec_bank;
        $amount = $this->modal_payment_method == 'cash' ? $amount_cash : $amount_bank;
        $amount = $this->modal_payment_method == 'bank' ? $amount_bank : $amount_cash;
        $amount = $this->modal_payment_method == 'both' ? $amount_cash + $amount_bank : $amount;
        $amount = abs($amount);

        $purchase->paid_amount += $amount;
        $purchase->due_amount -= $amount;
        $purchase->payment_method = $this->modal_payment_method;
        $purchase->received_amount_cash += $amount_cash;
        $purchase->received_amount_bank += $amount_bank;
        $purchase->bank_id = $this->bankId;
        $purchase->save();

        if ($isPaying) {
            $remark = 'PAID_FOR_PURCHASE'; // Money going out
            $amount *= -1; // Deduct from bank
            $notification = 'Payment sent successfully';
        } else {
            $remark = 'RETURNED_EXTRA_PAYMENT_FROM_PURCHASE'; // Money coming in (return)
            $notification = 'Payment refunded successfully';
        }
        $payment = new SupplierPayment();
        $payment->supplier_id = $purchase->supplier_id;
        $payment->purchase_id = $purchase->id;
        $payment->amount = $amount;
        $payment->trx = getTrx();
        $payment->remark = $remark;

        $payment->save();


        // Get last transaction
        $lastTransaction = ModelsSupplierTransaction::where('supplier_id', $purchase->supplier_id)
            ->orderBy('id', 'desc')
            ->first();

        // If no previous transaction, use supplier's opening balance
        if ($lastTransaction) {
            $openingBalance = $lastTransaction->closing_balance;
        } else {
            // Supplier table se opening_balance uthao
            $supplier = Supplier::findOrFail($purchase->supplier_id);
            $openingBalance = $supplier->opening_balance ?? 0.00;
        }

        // Credit amount (from purchase)
        $creditAmount = $amount;
        $debitAmount = 0.00;

        // Subtract credit from opening to get closing
        $closingBalance = $openingBalance - $creditAmount;

        // Save transaction
        $supplierTransaction = new ModelsSupplierTransaction();
        $supplierTransaction->supplier_id      = $purchase->supplier_id;
        $supplierTransaction->credit_amount    = $creditAmount;
        $supplierTransaction->debit_amount     = $debitAmount;
        $supplierTransaction->opening_balance  = $openingBalance;
        $supplierTransaction->closing_balance  = $closingBalance;
        $supplierTransaction->source           = 'Purchase Transaction';
        $supplierTransaction->bank_id          = $purchase->bank_id;
        $supplierTransaction->save();


    //Bank History ....
    // --- CASH ONLY ---
if ($this->modal_payment_method === 'cash') {
    $amount = $this->modal_paid_amount;
    $bank = Bank::where('name', 'Cash')->first();

    if ($bank && $amount > 0) {
        $lastTransaction = BankTransaction::where('bank_id', $bank->id)->latest()->first();
        $openingBalance = $lastTransaction ? $lastTransaction->closing_balance : ($bank->opening_balance ?? 0.00);
        $closingBalance = $openingBalance - $amount;

        $bankTransaction = new BankTransaction();
        $bankTransaction->bank_id = $bank->id;
        $bankTransaction->opening_balance = $openingBalance;
        $bankTransaction->closing_balance = $closingBalance;
        $bankTransaction->debit = 0.00;
        $bankTransaction->credit = $amount;
        $bankTransaction->amount = $amount;
        $bankTransaction->module_id = $purchase->id;
        $bankTransaction->data_model = 'Purchase';
        $bankTransaction->source = 'Cash Payment  Purchase';
        $bankTransaction->save();

        $bank->current_balance = $closingBalance;
        $bank->save();
    }
}

// --- BANK ONLY ---
if ($this->modal_payment_method === 'bank') {
    $amount = $this->modal_rec_bank;
    $bank = Bank::find($this->bankId);

    if ($bank && $amount > 0) {
        $lastTransaction = BankTransaction::where('bank_id', $bank->id)->latest()->first();
        $openingBalance = $lastTransaction ? $lastTransaction->closing_balance : ($bank->opening_balance ?? 0.00);
        $closingBalance = $openingBalance - $amount;

        $bankTransaction = new BankTransaction();
        $bankTransaction->bank_id = $bank->id;
        $bankTransaction->opening_balance = $openingBalance;
        $bankTransaction->closing_balance = $closingBalance;
        $bankTransaction->debit = 0.00;
        $bankTransaction->credit = $amount;
        $bankTransaction->amount = $amount;
        $bankTransaction->module_id = $purchase->id;
        $bankTransaction->data_model = 'Purchase';
        $bankTransaction->source = 'Bank Payment  Purchase';
        $bankTransaction->save();

        $bank->current_balance = $closingBalance;
        $bank->save();
    }
}

// --- BOTH PAYMENT ---
if ($this->modal_payment_method === 'both') {
    // --- Cash Part ---
    $cashAmount = $this->modal_paid_amount;
    $cashBank = Bank::where('name', 'Cash')->first();

    if ($cashBank && $cashAmount > 0) {
        $lastCashTransaction = BankTransaction::where('bank_id', $cashBank->id)->latest()->first();
        $cashOpening = $lastCashTransaction ? $lastCashTransaction->closing_balance : ($cashBank->opening_balance ?? 0.00);
        $cashClosing = $cashOpening - $cashAmount;

        $cashTransaction = new BankTransaction();
        $cashTransaction->bank_id = $cashBank->id;
        $cashTransaction->opening_balance = $cashOpening;
        $cashTransaction->closing_balance = $cashClosing;
        $cashTransaction->debit = 0.00;
        $cashTransaction->credit = $cashAmount;
        $cashTransaction->amount = $cashAmount;
        $bankTransaction->module_id = $purchase->id;
        $bankTransaction->data_model = 'Purchase';
        $cashTransaction->source = 'Cash Payment for Purchase (Both)';
        $cashTransaction->save();

        $cashBank->current_balance = $cashClosing;
        $cashBank->save();
    }

    // --- Bank Part ---
    $bankAmount = $this->modal_rec_bank;
    $selectedBank = Bank::find($this->bankId);

    if ($selectedBank && $bankAmount > 0) {
        $lastBankTransaction = BankTransaction::where('bank_id', $selectedBank->id)->latest()->first();
        $bankOpening = $lastBankTransaction ? $lastBankTransaction->closing_balance : ($selectedBank->opening_balance ?? 0.00);
        $bankClosing = $bankOpening - $bankAmount;

        $bankTransaction = new BankTransaction();
        $bankTransaction->bank_id = $selectedBank->id;
        $bankTransaction->opening_balance = $bankOpening;
        $bankTransaction->closing_balance = $bankClosing;
        $bankTransaction->debit = 0.00;
        $bankTransaction->credit = $bankAmount;
        $bankTransaction->amount = $bankAmount;
        $bankTransaction->module_id = $purchase->id;
        $bankTransaction->data_model = 'Purchase';
        $bankTransaction->source = 'Bank Payment for Purchase (Both)';
        $bankTransaction->save();

        $selectedBank->current_balance = $bankClosing;
        $selectedBank->save();
    }
}






        session()->flash('success', $notification);
        $this->resetExcept('purchaseId');
        $this->loadPurchases();
        $this->dispatch('notify', status: 'success', message: $notification);
    }



    public function render()
    {
        return view('livewire.admin.purchase-management.all-purchases');
    }
}
