<?php

namespace App\Livewire\Admin\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceStockDetail;
use App\Models\Stock;
use App\Models\StockInOut;
use App\Models\StockTransfer;
use App\Models\StockTransferDetail;
use App\Models\Supplier;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageStockTransfer extends Component
{
    public $stocks = [];
    public $products = [];
    public $suppliers = [];
    public $clients = [];
    public $users = [];
    public $warehouses = [];

    public $searchTerm = '';
    public $start_date = '';
    public $end_date = '';

    public $isCreating = false;
    public $editMode = false;
    public $warehouse_id;

    public $stockItems = [];
    public $title;
    public $stock_type;

    public $selectedStock = null;
    public $showDetails = false;

    public $client_id;

    public $from_user_id;
    public $from_warehouse_id;

    public $to_user_id;
    public $to_warehouse_id;

    public $fromUserId;
    public $fromUserModel;
    public $toUserId;
    public $toUserModel;


    public $searchTermDetails = '';
    public $startDateDetails = '';
    public $endDateDetails = '';
    public $selected_stock_id = null;





    public function mount()
    {
        $this->loadStocks();
    }
    public function loadStocks()
    {
        $this->stocks = StockTransfer::with([
            'fromWarehouse',
            'toWarehouse',
            'fromUser',
            'toUser',
        ])
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('fromUser', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                        ->orWhereHas('toUser', function ($q) {
                            $q->where('name', 'like', '%' . $this->searchTerm . '%');
                        });
                });
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->start_date));
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->end_date));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function createStock()
    {
        $this->resetForm();
        $this->isCreating = !$this->isCreating;
        $this->showDetails = false;

        $this->getProducts();
        $this->stockItems = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0, 'net_weight' => 0, 'is_kg' => false]
        ];

        $this->warehouses =  Warehouse::active()->orderBy('name')->get();
        $this->users = [];
        $this->users = $this->loadUsers();
    }

    private function loadUsers(): array
    {
        $formattedUsers = [];

        // Get data from models
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();
        $clients = Customer::select('id', 'name')->orderBy('name')->get();

        // Add Suppliers to the array with a unique identifier
        foreach ($suppliers as $supplier) {
            // Key: "Supplier-12", Value: "Supplier Name (Vendor)"

            $formattedUsers[] = [
                'id'   => 'Supplier-' . $supplier->id,
                'text' => $supplier->name . ' (Supplier)',
            ];
        }

        // Add Clients to the array with a unique identifier
        foreach ($clients as $client) {
            // Key: "Customer-8", Value: "Client Name (Client)"
            $formattedUsers[] = [
                'id'   => 'Customer-' . $client->id,
                'text' => $client->name . ' (Customer)',
            ];
        }

        return $formattedUsers;
    }
    public function updated($name, $value)
    {

        if (in_array($name, ['searchTerm', 'start_date', 'end_date'])) {
            $this->loadStocks();
        }
        if ($name === 'searchTermDetails' || $name === 'startDateDetails' || $name === 'endDateDetails') {
            $this->viewDetails($this->selected_stock_id);
        }
        if ($name === 'from_user_id' || $name === 'to_user_id') {

            //// Check from and to user are not same
            if ($this->from_user_id == $this->to_user_id) {
                $this->dispatch('notify', status: 'error', message: 'From and To user cannot be same');
                $this->from_user_id = null;
                $this->to_user_id = null;
            } else {
                if ($name === 'from_user_id') {
                    list($this->fromUserModel, $this->fromUserId) = explode('-', $value, 2);
                }
                if ($name === 'to_user_id') {
                    list($this->toUserModel, $this->toUserId) = explode('-', $value, 2);
                }
            }
        }

        if (str_starts_with($name, 'stockItems.')) {

            $index = explode('.', $name)[1];
            $quant = (float)$this->stockItems[$index]['quantity'];
            $product_id = (float)$this->stockItems[$index]['product_id'];
            $net_weight = (float)$this->stockItems[$index]['net_weight'];
            $unit_price = (float)$this->stockItems[$index]['unit_price'];
            if ($product_id) {
                $product = Product::find($product_id);
                $productName = $product->name ?? 'Unknown Product';
                $availableStock = $this->checkAvailableStock($product_id, $this->from_warehouse_id, $this->fromUserId, $this->fromUserModel);
                if ($availableStock < $quant) {
                    $this->dispatch('notify', status: 'error', message: 'Insufficient Quantity for product: ' . $productName);
                    $this->stockItems[$index]['quantity'] = 1;
                    $this->stockItems[$index]['unit_price'] = 0;
                }
                if ($product && $product->unit && strtolower($product->unit->name) == 'kg') {
                    $availableStockWeight = $this->checkAvailableStockWeight($product_id, $this->from_warehouse_id, $this->fromUserId, $this->fromUserModel);
                    if ($availableStockWeight < $net_weight) {
                        $this->dispatch('notify', status: 'error', message: 'Insufficient Weight for product: ' . $productName);
                        $this->stockItems[$index]['quantity'] = 1;
                        $this->stockItems[$index]['unit_price'] = 0;
                    } else {
                        $this->stockItems[$index]['is_kg'] = false;
                        $this->stockItems[$index]['total_amount'] = $quant * $unit_price;
                    }
                    $this->stockItems[$index]['is_kg'] = true;
                    $this->stockItems[$index]['total_amount'] = $net_weight * $unit_price;
                } else {

                    $this->stockItems[$index]['is_kg'] = false;
                    $this->stockItems[$index]['total_amount'] = $quant * $unit_price;
                }
            }

            $this->recalculateTotalAmount();
        }
    }
    public function getProducts()
    {

        $this->products = Product::all()->map(fn($p) => [
            'id' => $p->id,
            'text' => $p->name,
        ])->toArray();
    }
    public function addItem()
    {
        $this->stockItems[] = ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0, 'net_weight' => 0, 'is_kg' => false];
    }

    public function removeItem($index)
    {
        unset($this->stockItems[$index]);
        $this->stockItems = array_values($this->stockItems); // reindex
    }
    public function saveStock()
    {

        $this->validate([
            'from_warehouse_id' => 'required|integer',
            'to_warehouse_id' => 'required|integer',
            'from_user_id' => 'required',
            'to_user_id' => 'required',
            'stockItems.*.product_id' => 'required|integer',
            'stockItems.*.quantity' => 'required|numeric|min:1',
        ]);

        /////////////////  Check Stock Availability for all Products
        foreach ($this->stockItems as $item) {
            $product = Product::find($item['product_id']);
            $productName = $product->name ?? 'Unknown Product';
            $availableStock = $this->checkAvailableStock($item['product_id'], $this->from_warehouse_id, $this->fromUserId, $this->fromUserModel);
            if ($availableStock < $item['quantity']) {
                $this->dispatch('notify', status: 'error', message: 'Insufficient stock for product: ' . $productName);
                return;
            }
            if ($product && $product->unit && strtolower($product->unit->name) == 'kg') {
                $availableStockWeight = $this->checkAvailableStockWeight($item['product_id'], $this->from_warehouse_id, $this->fromUserId, $this->fromUserModel);
                if ($availableStockWeight < $item['net_weight']) {
                    $this->dispatch('notify', status: 'error', message: 'Insufficient Weight for product: ' . $productName);
                }
            }
        }

        $stock = StockTransfer::create([
            'from_user_id' => $this->fromUserId,
            'from_user_model'   => $this->fromUserModel,
            'to_user_id' => $this->toUserId,
            'to_user_model' => $this->toUserModel,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id' => $this->to_warehouse_id,
        ]);

        foreach ($this->stockItems as $item) {
            StockTransferDetail::create([
                'stock_transfer_id' => $stock->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'net_weight' => $item['net_weight'],
                'unit_price' => $item['unit_price'],
                'total_amount' => $item['total_amount'],

            ]);

            //////  update Stoc for from user
            $this->updateServiceStock($item['product_id'], $item['quantity'], 'out', $this->from_warehouse_id, $this->fromUserId, $this->fromUserModel,$item['net_weight']);
            //////  update Stoc for to user
            $this->updateServiceStock($item['product_id'], $item['quantity'], 'in', $this->to_warehouse_id, $this->toUserId, $this->toUserModel,$item['net_weight']);
        }

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

        $this->selectedStock = StockTransfer::with(['stockTransferDetails' => function ($query) use ($searchTermDetails, $start, $end) {
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
    public function closeDetails()
    {
        $this->showDetails = false;
        $this->selectedStock = null;
        $this->start_date = '';
        $this->startDateDetails = '';
        $this->end_date = '';
        $this->endDateDetails = '';
        $this->searchTerm = '';
        $this->searchTermDetails = '';
        $this->selected_stock_id = null;
        $this->loadStocks();
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
        $stock =  StockTransfer::with('stockTransferDetails')->find($this->selectedStock->id);
        if (!$stock) {
            return 0;
        }
        $total = 0;
        foreach ($stock->stockTransferDetails as $item) {
            $total += $item->total_amount;
        }
        return $total;
    }
    public function updateServiceStock($product_id, $quantity, $stock_type, $warehouse_id, $user_id, $user_model, $net_weight)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            if ($stock_type == 'in') {
                $stockDetail->increment('quantity', $quantity);
                $stockDetail->increment('net_weight', $net_weight);
            } else {
                $stockDetail->decrement('quantity', $quantity);
                $stockDetail->decrement('net_weight', $net_weight);
            }
        } else {
            ServiceStockDetail::create([
                'product_id' => $product_id,
                'warehouse_id' => $warehouse_id,
                'user_id' => $user_id,
                'user_model' => $user_model,
                'quantity' => ($stock_type == 'in') ? $quantity : -$quantity,
                'net_weight' => ($stock_type == 'in') ? $net_weight : -$net_weight,
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
    public function checkAvailableStockWeight($product_id, $warehouse_id, $user_id, $user_model)
    {
        $stockDetail = ServiceStockDetail::where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->first();

        if ($stockDetail) {
            return $stockDetail->net_weight ?? 0;
        }
        return 0;
    }
    public function clearFilters()
    {
        $this->searchTerm = '';  // Reset search term
        $this->start_date = '';   // Reset start date
        $this->end_date = '';     // Reset end date
        $this->startDateDetails = '';   // Reset start date
        $this->endDateDetails = '';     // Reset end date
        $this->searchTermDetails = '';  // Reset search term
        $this->selected_stock_id = null; // Reset selected stock id
        $this->loadStocks();
    }
    public function clearFiltersDetails()
    {
        $this->searchTermDetails = '';
        $this->startDateDetails = '';
        $this->endDateDetails = '';
        $this->viewDetails($this->selected_stock_id);
    }
    public function stockPDF()
    {

        $directory = 'stock_transfer_pdf';
        // Generate PDF
        $pdf = Pdf::loadView('pdf.services.stock_transfer', [
            'pageTitle' => 'Stock Transfer Invoice',
            'selectedStock' => $this->selectedStock,
            'stockTotalAmount' => $this->stockTotalAmount(),
        ])->setOption('defaultFont', 'Arial');

        // Ensure the directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }
        $filename = 'stock_transfer_invoice_' . now()->format('Ymd_His') . '.pdf'; // Unique filename
        $filepath = $directory . '/' . $filename;

        // Save the PDF to storage
        Storage::disk('public')->put($filepath, $pdf->output());

        $this->dispatch('notify', status: 'success', message: 'PDF generated successfully!');
        return response()->download(storage_path('app/public/' . $filepath), $filename);
    }
    private function filteredStocksQuery()
    {
        $startDate = $this->startDate ? Carbon::parse($this->startDate)->startOfDay() : null;
        $endDate = $this->endDate ? Carbon::parse($this->endDate)->endOfDay() : null;

        return Stock::with(['warehouse', 'user'])
            ->where('stock_type', $this->stock_type)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('warehouse', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            });
    }

    public function resetForm()
    {

        $this->selected_stock_id = null;
        $this->title = '';
        $this->from_user_id = '';
        $this->to_user_id = null;
        $this->from_warehouse_id = null;
        $this->to_warehouse_id = '';

        $this->stockItems = [
            ['product_id' => null, 'quantity' => 1, 'unit_price' => 0, 'total_amount' => 0, 'net_weight' => 0, 'is_kg' => false]
        ];
    }
    public function render()
    {
        return view('livewire.admin.services.manage-stock-transfer');
    }
}
