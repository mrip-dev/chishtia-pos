<?php

namespace App\Livewire\Admin\Services;

use App\Models\Customer;
use App\Models\ServiceStockDetail;
use App\Models\StockTransfer;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Livewire\Component;

class ManageStockDetails extends Component
{

    public $stocks = [];
    public $searchTerm = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedStock = null;
    public $selectedUser = null;
    public $showDetails = false;

    public function mount()
    {
        $this->loadStockDetails();
    }
    public function loadStockDetails()
    {
        $this->stocks = ServiceStockDetail::where('quantity', '>', 0)
            ->when($this->searchTerm, function ($query) {
                $query->where(function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('warehouse', function ($q) {
                        $q->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
                });
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('created_at', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            })
            ->when($this->startDate && !$this->endDate, function ($query) {
                $query->whereDate('created_at', '>=', Carbon::parse($this->startDate)->startOfDay());
            })
            ->when(!$this->startDate && $this->endDate, function ($query) {
                $query->whereDate('created_at', '<=', Carbon::parse($this->endDate)->endOfDay());
            })
            ->with(['product', 'warehouse'])
            ->groupBy(['user_id','user_model'])
            ->orderBy('created_at', 'desc')
            ->selectRaw('sum(quantity) as quantity,count(id) as product_count, user_id, user_model')
            ->get();
    }
    public function updated($name, $value)
    {
        if ($name === 'searchTerm' || $name === 'startDate' || $name === 'endDate') {
            $this->loadStockDetails();
        }
        if ($name === 'showDetails') {
            $this->loadStockDetails();
        }
    }
    public function viewDetails($user_id, $user_model)
    {
        // Fetch records
        $this->selectedStock = ServiceStockDetail::where('user_id', $user_id)
            ->where('user_model', $user_model)
            ->get();

        // Check if any record was found
        if ($this->selectedStock->isEmpty()) {
            // Handle the case where no data is found
            session()->flash('error', 'No stock details found for the given user.');
            $this->showDetails = false;
            return;
        }

        // Proceed safely now that we know at least one record exists
        $this->selectedUser = $this->selectedStock[0]->user ?? null;

        // Validate if user exists
        if (!$this->selectedUser) {
            session()->flash('error', 'User associated with the stock could not be found.');
            $this->showDetails = false;
            return;
        }

        $this->showDetails = true;
    }
    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->loadStockDetails();
    }

    public function stockPDF()
    {

        $directory = 'stock_details_pdf';
        // Generate PDF
        $pdf = Pdf::loadView('pdf.services.stock-details', [
            'pageTitle' => 'Stock Detail Invoice',
            'selectedUser' => $this->selectedUser,
            'selectedStock' => $this->selectedStock,
        ])->setOption('defaultFont', 'Arial');

        // Ensure the directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $filename = 'stock_details_invoice_' . now()->format('Ymd_His') . '.pdf'; // Unique filename
        $filepath = $directory . '/' . $filename;

        // Save the PDF to storage
        Storage::disk('public')->put($filepath, $pdf->output());
        // ServiceStockDetail::where('customer_id', $customerId)
        // ->update(['pdf_path' => $filepath]);

        $this->dispatch('notify', status: 'success', message: 'PDF generated successfully!');
         return response()->download(storage_path('app/public/' . $filepath), $filename);
    }

    public function render()
    {
        return view('livewire.admin.services.manage-stock-details');
    }
}
