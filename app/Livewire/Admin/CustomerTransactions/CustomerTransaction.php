<?php

namespace App\Livewire\Admin\CustomerTransactions;

use App\Models\CustomerTransaction as ModalCustomerTransaction;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerTransaction extends Component
{
    use WithPagination;

    public $customerId;
    public $customer;
    public $search = '';
    public $perPage = 20;
    public $startDate;
    public $endDate;



    public function mount($customerId)
    {

        $this->customerId = $customerId;

        $this->customer = Customer::findOrFail($customerId);
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }
        public function clearFilters()
    {
        $this->search = '';
        $this->startDate = null;
        $this->endDate = null;
    }


    public function generateInvoice($startDate = null, $endDate = null, $search = null)
    {
        $directory = 'customer_pdf';

        // Set 'Arial' as the default font to avoid font-related issues
        $pdf = Pdf::loadView('partials.customer-pdf', [
            'pageTitle' => 'TEST'
        ])
        ->setOption('defaultFont', 'Arial');  // Set default font


        // Ensure the directory exists, if not, create it
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $filename = 'customer_invoice.pdf';
        $filepath = $directory . '/' . $filename;

        // Save the PDF to storage
        Storage::disk('public')->put($filepath, $pdf->output());

        // Optionally, you can return the file path to update your model or for other operations
        return $filepath;
    }

    public function render()
    {
        $transactions = ModalCustomerTransaction::with(['customer', 'bank'])
            ->where('customer_id', $this->customerId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('bank', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.customer-transactions.customer-transaction', [
            'transactions' => $transactions,
        ]);
    }

}
