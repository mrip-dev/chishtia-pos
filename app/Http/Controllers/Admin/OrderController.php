<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale; // Assuming Sale model is used for orders
use App\Models\SaleDetails;
use App\Models\Customer;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added for potential transaction

class OrderController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Orders';
    }

    protected function getSales()
    {
        $query = Sale::searchable(['invoice_no', 'due_amount', 'customer:name,mobile', 'status','servic_type'])
            ->dateFilter('sale_date')
            ->with('customer', 'saleReturn')
            ->orderBy('id', 'desc');

        $user = Auth::guard('admin')->user();
        if ($user && $user->role_id !== 0) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public function index()
    {
        $pageTitle = $this->pageTitle;
        $sales     = $this->getSales()->paginate(getPaginate());
        $banks     = Bank::all();
        $pdfButton = false;
        $routePDF  = route('admin.order.pdf') . "?";
        $routeCSV  = route('admin.order.csv') . "?";

        if (request()->search) {
            $routePDF .= "search=" . request()->search . "&";
            $routeCSV .= "search=" . request()->search . "&";
        }
        if (request()->date) {
            $routePDF .= "date=" . request()->date;
            $routeCSV .= "date=" . request()->date;
        }

        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
        ];

        return view('admin.order.index', compact('pageTitle', 'sales', 'pdfButton', 'routePDF', 'routeCSV', 'banks', 'paymentMethods'));
    }

    public function salePDF()
    {
        $pageTitle = $this->pageTitle;
        $sales     = $this->getSales()->get();
        return downloadPDF('pdf.order.list', compact('pageTitle', 'sales'));
    }

    public function saleCSV()
    {
        $pageTitle = $this->pageTitle;
        $filename  = $this->downloadCsv($pageTitle, $this->getSales()->get());
        return response()->download(...$filename);
    }

    protected function downloadCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Order No.,Date,Customer,Mobile,Service Type,Table No.,Table Man,Total Amount,Status,Discount,Receivable,Paid,Due\n"; // Updated CSV header
        $curSym   = gs('cur_sym');

        foreach ($data as $sale) {
            $invoice        = $sale->invoice_no;
            $date           = showDateTime(@$sale->sale_date, 'd-m-Y');
            $customer       = $sale->customer_name; // Use customer_name from Sale
            $customerMobile = $sale->customer_phone; // Use customer_phone from Sale
            $serviceType    = ucfirst(str_replace('_', ' ', $sale->service_type)); // New
            $tableNo        = $sale->table_no ?? 'N/A'; // New
            $tableMan       = $sale->table_man ?? 'N/A'; // New
            $status         = ucfirst($sale->status);
            $totalAmount    = $curSym . getAmount($sale->total_price);
            $discount       = $curSym . getAmount($sale->discount_amount);
            $receivable     = $curSym . getAmount($sale->receivable_amount);
            $received       = $curSym . getAmount($sale->received_amount);
            $due            = $curSym . getAmount($sale->due_amount);

            $column .= "$invoice,$date,$customer,$customerMobile,$serviceType,$tableNo,$tableMan,$totalAmount,$status,$discount,$receivable,$received,$due \n";
        }

        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    public function downloadInvoice($id)
    {
        $pageTitle = "INVOICE";

        $sale = Sale::where('id', $id)
            ->with('customer', 'saleDetails', 'saleDetails.product', 'saleDetails.product.unit')
            ->whereHas('saleDetails')
            ->firstOrFail();

        $pdf = PDF::loadView('pdf.order.invoice', compact('pageTitle', 'sale'));

        $invoiceNo = $sale->invoice_no ?? 'INV-00';
        $date = now()->format('Y-m-d');
        $fileName = "Order_Inv_{$invoiceNo}_{$date}.pdf";

        // Define save path — use public/invoices
        $path = public_path("invoices/{$fileName}");

        // Ensure directory exists
        if (!file_exists(public_path('invoices'))) {
            mkdir(public_path('invoices'), 0777, true);
        }

        // Save PDF file
        $pdf->save($path);

        // Optional: Save path in database (if needed)
        // $sale->invoice_path = "invoices/{$fileName}";
        // $sale->save();

        // Return file as download
        return response()->download($path);
    }

    public function create()
    {
        $pageTitle     = 'New Order';
        $lastSale      = Sale::orderBy('id', 'DESC')->first();
        $lastInvoiceNo = @$lastSale->invoice_no;
        $categories    = Category::all();
        $invoiceNumber = generateInvoiceNumber($lastInvoiceNo);
        $customers     = Customer::select('id', 'name', 'mobile')->get();

        $products      = Product::with(['unit', 'category', 'brand']) // Eager load 'brand' as well
            ->select('id', 'name', 'sku', 'selling_price', 'image', 'unit_id', 'category_id', 'brand_id')
            ->orderBy('name')
            ->get();

        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
        ];
        $banks = Bank::all();

        foreach ($products as $product) {
            $product->image_url = getImage(getFilePath('product') . '/' . $product->image, getFileSize('product'));
            $product->display_title = getProductTitle($product->id);
            $product->brand_name = $product->brand->name ?? 'No Brand';
            $product->category_name = $product->category->name ?? 'No Category';
        }

        return view('admin.order.form', compact('pageTitle', 'invoiceNumber', 'customers', 'products', 'paymentMethods', 'banks', 'categories'));
    }

    public function edit($id)
    {
        $sale      = Sale::where('id', $id)
            ->with('saleDetails', 'saleDetails.product', 'saleDetails.product.unit', 'saleDetails.product.category', 'saleDetails.product.brand', 'customer')
            ->whereHas('saleDetails')
            ->firstOrFail();

        $pageTitle = 'Edit Order';
         $categories    = Category::all();
        $customers = Customer::select('id', 'name', 'mobile')->get();
        $products  = Product::with(['unit', 'category', 'brand']) // Eager load 'brand' and 'category' for edit as well
            ->select('id', 'name', 'sku', 'selling_price', 'image', 'unit_id', 'category_id', 'brand_id') // Select 'category_id' and 'brand_id'
            ->orderBy('name')
            ->get();

        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
        ];
        $banks = Bank::all();

        foreach ($products as $product) {
            $product->image_url = getImage(getFilePath('product') . '/' . $product->image, getFileSize('product'));
            $product->display_title = getProductTitle($product->id);
            $product->brand_name = $product->brand->name ?? 'No Brand';
            $product->category_name = $product->category->name ?? 'No Category';
        }

        return view('admin.order.form', compact('pageTitle', 'sale', 'customers', 'products', 'paymentMethods', 'banks', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validation($request, 'store');

        DB::beginTransaction(); // Start database transaction

        try {
            $this->products   = collect($request->products);
            $this->productIds = $this->products->pluck('product_id')->toArray();
            $this->totalPrice = $this->getTotalPrice();

            if ($request->discount > $this->totalPrice) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Discount amount mustn\'t be greater than total price'
                    ], 422);
                }
                $notify[] = ['error', 'Discount amount mustn\'t be greater than total price'];
                return back()->withNotify($notify)->withInput();
            }

            $lastSale      = Sale::orderBy('id', 'DESC')->first();
            $lastInvoiceNo = @$lastSale->invoice_no;

            $sale                 = new Sale();
            $sale->invoice_no     = generateInvoiceNumber($lastInvoiceNo);
            $sale->user_id        = Auth::guard('admin')->id();

            $sale = $this->saveSaleData($sale);
            $this->storeSaleDetails($sale);

            Action::newEntry($sale, 'CREATED');

            DB::commit(); // Commit the transaction

            $notify[] = ['success', 'Order created successfully'];

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'redirect' => route('admin.order.index'),
                    'data' => [
                        'order_id' => $sale->id,
                        'invoice_no' => $sale->invoice_no
                    ]
                ]);
            }

            return redirect()->route('admin.order.index')->withNotify($notify);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            dd($e);
            // Log the error for debugging
            logger()->error('Order creation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            $notify[] = ['error', 'Failed to create order. An unexpected error occurred.'];
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create order. An unexpected error occurred.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withNotify($notify)->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, 'update');

        DB::beginTransaction(); // Start database transaction

        try {
            $this->products   = collect($request->products);
            $this->totalPrice = $this->getTotalPrice();
            $this->productIds = $this->products->pluck('product_id')->toArray();

            if ($request->discount > $this->totalPrice) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Discount amount mustn\'t be greater than total price'
                    ], 422);
                }
                $notify[] = ['error', 'Discount amount mustn\'t be greater than total price'];
                return back()->withNotify($notify)->withInput();
            }

            $sale = Sale::with('saleDetails')->findOrFail($id);

            if ($sale->return_status) {
                $notify[] = ['error', 'You can\'t update this order'];
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'You can\'t update this order'], 400);
                }
                return back()->withNotify($notify);
            }

            $this->saleDetails = $sale->saleDetails;

            $sale = $this->saveSaleData($sale);
            $this->storeSaleDetails($sale);

            Action::newEntry($sale, 'UPDATED');

            DB::commit(); // Commit the transaction

            $notify[] = ['success', 'Order updated successfully'];
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully',
                    'redirect' => route('admin.order.index'),
                    'data' => [
                        'order_id' => $sale->id,
                        'invoice_no' => $sale->invoice_no
                    ]
                ]);
            }

            return redirect()->route('admin.order.index')->withNotify($notify);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            // Log the error for debugging
            logger()->error('Order update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            $notify[] = ['error', 'Failed to update order. An unexpected error occurred.'];
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update order. An unexpected error occurred.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return back()->withNotify($notify)->withInput();
        }
    }

    /**
     * Handles adding a new payment to an existing order.
     * @param Request $request
     * @param int $id Sale ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function makePayment(Request $request, $id)
    {
        $request->validate([
            'payment_amount'   => 'required|numeric|gt:0',
            'payment_method'   => 'required|in:cash,bank,mobile',
            'bank_id'          => 'nullable|required_if:payment_method,bank|exists:banks,id',
            'transaction_id'   => 'nullable|string|max:255',
        ]);

        $sale = Sale::findOrFail($id);

        $paymentAmount = $request->payment_amount;

        if ($paymentAmount > $sale->due_amount) {
            $notify[] = ['error', 'Payment amount cannot be greater than the due amount.'];
            return back()->withNotify($notify);
        }

        // Use database transaction for payment to ensure atomicity
        DB::beginTransaction();
        try {
            $sale->received_amount += $paymentAmount;
            $sale->due_amount      = $sale->receivable_amount - $sale->received_amount;

            $isFullPayment = $sale->due_amount <= 0;

            // Update status if fully paid and not already 'delivered' or 'cancelled'
            if ($isFullPayment && ($sale->status == 'pending' || $sale->status == 'processing')) {
                $sale->status = 'confirmed'; // Or 'delivered' based on your workflow
            }

            $sale->save();

            // Log this as a separate transaction/payment if you have a Payment model
            // For now, adding more details to Action entry
            Action::newEntry($sale, 'PAYMENT_RECEIVED', [
                'amount' => $paymentAmount,
                'method' => $request->payment_method,
                'bank_id' => $request->bank_id,
                'transaction_id' => $request->transaction_id,
                'new_due_amount' => $sale->due_amount,
                'new_status' => $sale->status,
            ]);

            DB::commit();

            $notify[] = ['success', 'Payment of ' . gs('cur_sym') . getAmount($paymentAmount) . ' recorded successfully. ' . ($isFullPayment ? 'Order is now fully paid.' : '')];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Payment failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $notify[] = ['error', 'Failed to record payment. An unexpected error occurred.'];
            return back()->withNotify($notify)->withInput();
        }
    }

    /**
     * Updates the status of an existing order.
     * @param Request $request
     * @param int $id Sale ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $sale = Sale::findOrFail($id);

        if ($sale->return_status) {
            $notify[] = ['error', 'Order cannot be updated due to a return process.'];
            return back()->withNotify($notify);
        }

        DB::beginTransaction(); // Transaction for status update
        try {
            $sale->status = $request->status;
            $sale->save();

            Action::newEntry($sale, 'STATUS_UPDATED', ['new_status' => $sale->status]);

            DB::commit();

            $notify[] = ['success', 'Order status updated to ' . ucfirst($sale->status) . ' successfully'];
            return back()->withNotify($notify);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Order status update failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $notify[] = ['error', 'Failed to update order status. An unexpected error occurred.'];
            return back()->withNotify($notify)->withInput();
        }
    }

    protected function saveSaleData($sale)
    {
        $request    = request();
        $discount   = $request->discount ?? 0;
        $totalReceived = $request->received_amount ?? 0;
        $receivable = $this->totalPrice - $discount;

        if ($sale->exists) {

            $sale->received_amount = ($sale->received_amount + $totalReceived); // Accumulate received
        } else {
            $sale->received_amount = $totalReceived;
        }

        $dueAmount  = $receivable - $sale->received_amount;

        $paymentMethod = $request->payment_method ?? null;
        $bankId = $request->bank_id ?? null;

        if ($totalReceived > 0) {
            $sale->payment_method = $paymentMethod;
            $sale->bank_id = $bankId;
        }

        $defaultCustomer = Customer::first()->id ?? 1; // Use null if no default customer exists
        $defaultWarehouse = Warehouse::first()->id ?? 1; // Use null if no default warehouse exists

        $sale->customer_id       = $request->customer_id ?? $defaultCustomer;
        $sale->customer_name     = $request->customer_name ?? 'Walkin-customer';
        $sale->customer_phone    = $request->customer_phone; // New field
        $sale->customer_address  = $request->customer_address; // New field
        $sale->service_type      = $request->service_type ?? 'takeaway'; // New field, default to takeaway
        $sale->table_no          = $request->table_no; // New field
        $sale->table_man         = $request->table_man; // New field
        $sale->warehouse_id      = $request->warehouse_id ?? $defaultWarehouse;
        $sale->sale_date         = Carbon::parse($request->sale_date);
        $sale->status            = $request->status ?? 'pending';
        $sale->total_price       = $this->totalPrice;
        $sale->discount_amount   = $discount;
        $sale->receivable_amount = $receivable;
        $sale->due_amount        = $dueAmount;
        $sale->note              = $request->note;

        if ($sale->due_amount <= 0 && ($sale->status == 'pending' || $sale->status == 'processing')) {
            $sale->status = 'confirmed'; // Automatically confirm if fully paid on creation/update
        }

        $sale->save();

        return $sale;
    }

    protected function storeSaleDetails($sale)
    {
        $saleDetails = @$this->saleDetails ?? null;

        if ($saleDetails) {
            $existingProductIds = $saleDetails->pluck('product_id')->toArray();
            $newProductIds = $this->products->pluck('product_id')->toArray();

            $toDelete = array_diff($existingProductIds, $newProductIds);
            if (!empty($toDelete)) {
                SaleDetails::where('sale_id', $sale->id)
                    ->whereIn('product_id', $toDelete)
                    ->delete();
            }
        }

        foreach ($this->products as $product) {
            $product    = (object) $product;
            $saleDetail = SaleDetails::firstOrNew(['sale_id' => $sale->id, 'product_id' => $product->product_id]);

            $saleDetail->quantity   = $product->quantity;
            $saleDetail->price      = $product->price;
            $saleDetail->total      = $product->quantity * $product->price;
            $saleDetail->save();
        }
    }

    protected function getTotalPrice()
    {
        return $this->products->sum(function ($item) {
            return $item['quantity'] * $item['price'];
        });
    }

    protected function validation($request, $context)
    {
        $rules = [
            'customer_id'           => 'nullable|exists:customers,id',
            'customer_name'         => 'required_without:customer_id|string|max:255',
            'sale_date'             => 'required|date_format:Y-m-d',
            'status'                => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'service_type'          => 'required|in:takeaway,delivery,dine_in', // New field
            'products'              => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity'   => 'required|integer|gt:0',
            'products.*.price'      => 'required|numeric|gte:0',
            'discount'              => 'nullable|numeric|gte:0',
            'note'                  => 'nullable|string|max:1000',

            // Payment fields (only required when an amount is received)
            'received_amount'       => 'nullable|numeric|gte:0',
            'payment_method'        => 'nullable|required_with:received_amount|in:cash,bank,mobile',
            'bank_id'               => 'nullable|required_if:payment_method,bank|exists:banks,id',
        ];

        // Conditional validation based on service_type
        if ($request->service_type === 'delivery') {
            $rules['customer_phone'] = 'required|string|max:20';
            $rules['customer_address'] = 'required|string|max:500';
            $rules['table_no'] = 'nullable'; // Must be nullable if not dine_in
            $rules['table_man'] = 'nullable'; // Must be nullable if not dine_in
        } elseif ($request->service_type === 'dine_in') {
            $rules['table_no'] = 'required|string|max:50';
            $rules['table_man'] = 'required|string|max:255';
            $rules['customer_phone'] = 'nullable'; // Must be nullable if not delivery
            $rules['customer_address'] = 'nullable'; // Must be nullable if not delivery
        } else { // Take Away
            $rules['customer_phone'] = 'nullable';
            $rules['customer_address'] = 'nullable';
            $rules['table_no'] = 'nullable';
            $rules['table_man'] = 'nullable';
        }

        $request->validate($rules);
    }

    public function searchProduct(Request $request)
    {
        $search = $request->search;

        $products = Product::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%");
        })
            ->with('unit')
            ->limit(20)
            ->get();

        if ($products) {
            return response()->json([
                'success' => true,
                'data'    => $products,
            ]);
        }

        return response()->json([
            'success' => false,
            'data'    => [],
        ]);
    }

    public function lastInvoice()
    {
        $lastInvoiceNo = Sale::latest()->first()->invoice_no ?? 'ORD-0000';

        return response()->json([
            'status' => true,
            'data'   => $lastInvoiceNo,
        ]);
    }
}