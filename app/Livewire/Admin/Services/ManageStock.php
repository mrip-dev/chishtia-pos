<?php

namespace App\Livewire\Admin\Services;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceStockDetail;
use App\Models\Stock;
use App\Models\StockInOut;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Traits\DailyBookEntryTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Traits\HandlesBankPayments;
use Livewire\Component;

class ManageStock extends Component
{
    use HandlesBankPayments;
    use DailyBookEntryTrait;

    public $stocks = [];
    public $users = [];
    public $products = [];
    public $suppliers = [];
    public $clients = [];
    public $warehouses = [];

    public $searchTerm = '';
    public $startDate = '';
    public $endDate = '';
    public $isCreating = false;
    public $editMode = false;
    public $warehouse_id;

    public $stockItems = [];
    public $title;
    public $stock_type;

    public $selectedStock = null;
    public $showDetails = false;

    public $user_id;
    public $driver_name;
    public $driver_contact;
    public $vehicle_number;
    public $labour;

    public $searchTermDetails = '';
    public $startDateDetails = '';
    public $endDateDetails = '';
    public $selected_stock_id = null;


    public $banks = [];
    public $paymentStock = null;
    public $modal_rec_amount;
    public $bankId;
    public $modal_payment_method = '';
    public $modal_rec_bank;
    public $modal_receivable_amount;
    public $modal_title = '';



    protected function rules()
    {
        $rules = [
            'stock_type' => 'required|in:in,out',
            'product_id' => 'required|integer',

        ];
        return $rules;
    }

    protected function rulesPayment()
    {
        return [
            'modal_payment_method' => 'required|string',

            'modal_rec_amount' => [
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

    public function mount($type)
    {
        $this->stock_type = $type;
        $this->loadStocks();
    }
    public function loadStocks()
    {

        $this->stocks = Stock::with(['warehouse', 'user'])->where('stock_type', $this->stock_type)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%');
                $query->orWhereHas('warehouse', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                })->orWhereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $startDate = Carbon::parse($this->startDate)->startOfDay();
                $endDate = Carbon::parse($this->endDate)->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();
    }
    public function createStock()
    {
        $this->isCreating = !$this->isCreating;
        $this->showDetails = false;
        $this->users = [];
        $this->stockItems = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0]
        ];
        $suppliers = Supplier::select('id', 'name', 'mobile')->get();
        $clients = Customer::select('id', 'name', 'mobile')->get();
        foreach ($suppliers as $supplier) {
            $this->users[] = [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'model' => 'Supplier',
            ];
        }
        foreach ($clients as $client) {
            $this->users[] = [
                'id' => $client->id,
                'name' => $client->name,
                'model' => 'Customer',
            ];
        }

        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
    }



    public function updated($name, $value)
    {
        if ($name === 'searchTerm' || $name === 'startDate' || $name === 'endDate') {
            $this->loadStocks();
        }
        if ($name === 'searchTermDetails' || $name === 'startDateDetails' || $name === 'endDateDetails') {
            $this->viewDetails($this->selected_stock_id);
        }
        if ($name === 'warehouse_id') {
            $this->getProducts();
        }
        if (str_starts_with($name, 'stockItems.')) {

            $index = explode('.', $name)[1];
            $quant = (float)$this->stockItems[$index]['quantity'];
            $unit_price = (float)$this->stockItems[$index]['unit_price'];
            $this->stockItems[$index]['total_amount'] = $quant * $unit_price;
            $this->recalculateTotalAmount();
        }
    }
    public function getProducts()
    {
        $warehouse = $this->warehouse_id;
        if (!$warehouse) {
            $this->products = [];
            $this->dispatch('notify', status: 'error', message: 'Select Whereouse First');
        }
        $this->products = Product::all();
    }
    public function addItem()
    {
        $this->stockItems[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0];
    }

    public function removeItem($index)
    {
        unset($this->stockItems[$index]);
        $this->stockItems = array_values($this->stockItems); // reindex
    }
    public function saveStock()
    {

        $this->validate([
            'title' => 'required|string',
            'stock_type' => 'required|in:in,out',
            'warehouse_id' => 'required|integer',
            'user_id' => 'required|integer',
            'labour' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'driver_name' => 'nullable|string',
            'driver_contact' => 'nullable|string',
            'stockItems.*.product_id' => 'required|integer',
            'stockItems.*.quantity' => 'required|numeric|min:1',
        ]);
        $selecteduserId = $this->users[$this->user_id]['id'];
        $selecteduserModel = $this->users[$this->user_id]['model'];
        /////////////////  Check Stock Availability for all Products
        foreach ($this->stockItems as $item) {
            $availableStock = $this->checkAvailableStock($item['product_id'], $this->warehouse_id, $selecteduserId, $selecteduserModel);
            if ($this->stock_type == 'out' && $availableStock < $item['quantity']) {
                ///// Product
                $product = Product::find($item['product_id']);
                if ($product) {
                    $productName = $product->name;
                } else {
                    $productName = 'Unknown Product';
                }
                $this->dispatch('notify', status: 'error', message: 'Insufficient stock for product: ' . $productName);
                return;
            }
        }

        $stock = Stock::create([
            'title' => $this->title,
            'stock_type' => $this->stock_type,
            'warehouse_id' => $this->warehouse_id,
            'user_id' => $selecteduserId,
            'user_model'   => $selecteduserModel,
            'labour' => $this->labour,
            'vehicle_number' => $this->vehicle_number,
            'driver_name' => $this->driver_name,
            'driver_contact' => $this->driver_contact,
        ]);

        foreach ($this->stockItems as $item) {
            StockInOut::create([
                'stock_id' => $stock->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $item['total_amount'],

            ]);
            ///////////   Update Service Stock Detail
            $stockdetail = $this->updateServiceStock($item['product_id'], $item['quantity'], $this->stock_type, $this->warehouse_id, $selecteduserId, $selecteduserModel);
        }
        $stock->total_amount = $this->recalculateTotalAmount();
        $stock->due_amount = $this->recalculateTotalAmount();
        $stock->save();
        ///////////   Update Service Stock Detail
        $this->dispatch('notify', status: 'success', message: 'Stock saved successfully');

        $this->isCreating = false;
        $this->loadStocks(); // reload stocks
    }


    public function viewDetails($stockId)
    {
        $this->selected_stock_id = $stockId;

        $searchTermDetails = '%' . $this->searchTermDetails . '%'; // assuming this is coming from Livewire or input
        $start = $this->startDateDetails ? Carbon::parse($this->startDateDetails)->startOfDay() : null;
        $end = $this->endDateDetails ? Carbon::parse($this->endDateDetails)->endOfDay() : null;

        $this->selectedStock = Stock::with(['stockInOuts' => function ($query) use ($searchTermDetails, $start, $end) {
            $query->whereHas('product', function ($q) use ($searchTermDetails) {
                $q->where('name', 'like', $searchTermDetails);
            })->with('product');
            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            } elseif ($start) {
                $query->where('created_at', '>=', $start);
            } elseif ($end) {
                $query->where('created_at', '<=', $end);
            }
        }])->find($this->selected_stock_id);

        $this->showDetails = true;
        $this->isCreating = false;
    }

    public function recalculateTotalAmount()
    {

        $total = 0;
        foreach ($this->stockItems as $item) {
            $total += $item['total_amount'];
        }
        return $total;
    }
    public function stockTotalAmount()
    {
        $stock = Stock::with('stockInOuts')->find($this->selectedStock->id);
        if (!$stock) {
            return 0;
        }
        $total = 0;
        foreach ($stock->stockInOuts as $item) {
            $total += $item->total_amount;
        }
        return $total;
    }

    public function updateServiceStock($product_id, $quantity, $stock_type, $warehouse_id, $user_id, $user_model)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            if ($stock_type == 'in') {
                $stockDetail->increment('quantity', $quantity);
            } else {
                $stockDetail->decrement('quantity', $quantity);
            }
        } else {
            ServiceStockDetail::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'user_id' => $user_id,
                'user_model' => $user_model,
                'quantity' => ($stock_type == 'in') ? $quantity : -$quantity,
            ]);
        }
    }
    public function checkAvailableStock($product_id, $warehouse_id, $user_id, $user_model)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            return $stockDetail->quantity;
        }
        return 0;
    }
    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->loadStocks();
    }
    public function clearFiltersDetails()
    {
        $this->searchTermDetails = '';
        $this->startDateDetails = '';
        $this->endDateDetails = '';
        $this->viewDetails($this->selected_stock_id);
    }
    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedStock = null;
        $this->startDate = '';
        $this->startDateDetails = '';
        $this->endDate = '';
        $this->endDateDetails = '';
        $this->searchTerm = '';
        $this->searchTermDetails = '';
        $this->selected_stock_id = null;
        $this->loadStocks();
    }
    public function stockPDF()
    {

        $directory = 'stock_' . $this->stock_type . '_pdf';
        // Generate PDF
        $pdf = Pdf::loadView('pdf.services.stock-in-out', [
            'pageTitle' => 'Stock ' . $this->stock_type . ' Invoice',
            'selectedStock' => $this->selectedStock,
            'stockTotalAmount' => $this->stockTotalAmount(),
        ])->setOption('defaultFont', 'Arial');

        // Ensure the directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $filename = 'stock_' . $this->stock_type . '_invoice_' . now()->format('Ymd_His') . '.pdf'; // Unique filename
        $filepath = $directory . '/' . $filename;

        // Save the PDF to storage
        Storage::disk('public')->put($filepath, $pdf->output());

        $this->dispatch('notify', status: 'success', message: 'PDF generated successfully!');
        return response()->download(storage_path('app/public/' . $filepath), $filename);
    }
    public function payMentModal($id)
    {
        $this->banks = Bank::all();
        $this->paymentStock = Stock::find($id);
        if (!$this->paymentStock) {
            $this->dispatch('notify', status: 'error', message: 'Stock not found');
            return;
        }
        $this->modal_title = $this->paymentStock->title;
        $this->modal_receivable_amount = $this->paymentStock->due_amount;
        $this->modal_payment_method = 'cash';
        $this->modal_rec_bank = 0.00;
        $this->modal_rec_amount = 0.00;
        $this->dispatch('openPaymentModal');
    }


    public function submitPayment()
    {

        $this->validate($this->rulesPayment());

        $stock = $this->paymentStock;

        $amount_cash = $this->modal_rec_amount;
        $amount_bank = $this->modal_rec_bank;
        $amount = $this->modal_payment_method == 'cash' ? $amount_cash : $amount_bank;
        $amount = $this->modal_payment_method == 'bank' ? $amount_bank : $amount_cash;
        $amount = $this->modal_payment_method == 'both' ? $amount_cash + $amount_bank : $amount;
        $amount = abs($amount);

        $stock->recieved_amount += $amount;
        $stock->due_amount -= $amount;
        $stock->cash_amount += $amount_cash;
        $stock->bank_amount += $amount_bank;
        $stock->bank_id = $this->bankId;
        $stock->save();

         $this->handlePaymentTransaction(
            $this->modal_payment_method,
            $amount_cash,
            $amount_bank,
            $this->bankId,
            $stock->id,
            'Stock',
            'debit'
        );

        $this->handleDailyBookEntries($amount_cash,$amount_bank,'debit',$this->modal_payment_method,'Stock',$stock->id);

        session()->flash('success', 'Payment updated successfully!');
        $this->dispatch('notify', status: 'success', message: 'Payment updated successfully!');
        $this->loadStocks();
        $this->paymentStock = null;
        $this->modal_rec_amount = 0.00;
        $this->modal_rec_bank = 0.00;
        $this->modal_payment_method = '';
        $this->modal_rec_bank = 0.00;
        $this->modal_receivable_amount = 0.00;
        $this->modal_title = '';
        $this->bankId = null;

        $this->dispatch('closePaymentModal');
    }
    public function render()
    {
        return view('livewire.admin.services.manage-stock');
    }
}
