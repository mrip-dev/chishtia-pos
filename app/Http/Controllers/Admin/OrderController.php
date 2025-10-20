<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Bank;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\Customer;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $pageTitle;

    public function __construct()
    {
        $this->pageTitle = 'All Orders';
    }

    protected function getSales()
    {
        $query = Sale::searchable(['invoice_no', 'due_amount', 'customer:name,mobile', 'status'])
            ->dateFilter('sale_date')
            ->with('customer', 'saleReturn')
            // ->where('status', 'pending')
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

        // ADDED for payment status
        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
            // Add more as needed
        ];

        return view('admin.order.index', compact('pageTitle', 'sales', 'pdfButton', 'routePDF', 'routeCSV', 'banks', 'paymentMethods'));
    }

    // --- Existing methods (salePDF, saleCSV, downloadCsv, downloadInvoice) go here ---

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
        $column   = "Order No.,Date,Customer,Mobile,Total Amount,Status,Discount,Receivable,Paid,Due\n";
        $curSym   = gs('cur_sym');

        foreach ($data as $sale) {
            $invoice        = $sale->invoice_no;
            $date           = showDateTime(@$sale->sale_date, 'd-m-Y');
            $customer       = $sale->customer?->name;
            $customerMobile = $sale->customer?->mobile;
            $status         = ucfirst($sale->status);
            $totalAmount    = $curSym . getAmount($sale->total_price);
            $discount       = $curSym . getAmount($sale->discount_amount);
            $receivable     = $curSym . getAmount($sale->receivable_amount);
            $received       = $curSym . getAmount($sale->received_amount);
            $due            = $curSym . getAmount($sale->due_amount);

            $column .= "$invoice,$date,$customer,$customerMobile,$totalAmount,$status,$discount,$receivable,$received,$due \n";
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

    // ---

    public function create()
    {
        $pageTitle     = 'New Order';
        $lastSale      = Sale::orderBy('id', 'DESC')->first();
        $lastInvoiceNo = @$lastSale->invoice_no;
        $categories    = Category::all();
        $invoiceNumber = generateInvoiceNumber($lastInvoiceNo);
        $customers     = Customer::select('id', 'name', 'mobile')->get();

        // FIX: Include 'category_id' in the select statement and eager load 'category'
        $products      = Product::with(['unit', 'category'])
            ->select('id', 'name', 'sku', 'selling_price', 'image', 'unit_id', 'category_id', 'brand_id') // ADDED 'category_id' and 'brand_id'
            ->orderBy('name')
            ->get();

        // ADDED: Payment methods
        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
        ];
        $banks = Bank::all(); // Used for bank payment method

        // Add image URL and other details to each product
        foreach ($products as $product) {
            $product->image_url = getImage(getFilePath('product') . '/' . $product->image, getFileSize('product'));
            $product->display_title = getProductTitle($product->id);
            // The relationship data is available here thanks to eager loading
            $product->brand_name = $product->brand->name ?? 'No Brand';
            $product->category_name = $product->category->name ?? 'No Category'; // Ensure it doesn't crash if null
        }

        return view('admin.order.form', compact('pageTitle', 'invoiceNumber', 'customers', 'products', 'paymentMethods', 'banks', 'categories'));
    }

    public function edit($id)
    {
        $sale      = Sale::where('id', $id)
            ->with('saleDetails', 'saleDetails.product', 'saleDetails.product.unit', 'customer')
            ->whereHas('saleDetails')
            ->firstOrFail();

        $pageTitle = 'Edit Order';
        $customers = Customer::select('id', 'name', 'mobile')->get();
        $products  = Product::with('unit')
            ->select('id', 'name', 'sku', 'selling_price', 'image', 'unit_id')
            ->orderBy('name')
            ->get();

        // ADDED: Payment methods
        $paymentMethods = [
            'cash'    => 'Cash',
            'bank'    => 'Bank Transfer',
            'mobile'  => 'Mobile Money',
        ];
        $banks = Bank::all();

        // Add image URL to each product
        foreach ($products as $product) {
            $product->image_url = getImage(getFilePath('product') . '/' . $product->image, getFileSize('product'));
            $product->display_title = getProductTitle($product->id);
            // Assuming brand and category relationships exist on Product model
            $product->brand_name = $product->brand->name ?? 'No Brand';
            $product->category_name = $product->category->name ?? 'No Category';
        }

        return view('admin.order.form', compact('pageTitle', 'sale', 'customers', 'products', 'paymentMethods', 'banks'));
    }

    public function store(Request $request)
    {
        $this->validation($request, 'store'); // Updated validation call

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
        $sale->user_id        = Auth::guard('admin')->id(); // Assigning the user who created the order

        $sale = $this->saveSaleData($sale);

        $this->storeSaleDetails($sale);

        Action::newEntry($sale, 'CREATED');
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
    }

    public function update(Request $request, $id)
    {
        $this->validation($request, 'update'); // Updated validation call

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
            return back()->withNotify($notify);
        }

        $this->saleDetails = $sale->saleDetails;

        $sale = $this->saveSaleData($sale);
        $this->storeSaleDetails($sale);

        Action::newEntry($sale, 'UPDATED');

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

        $sale->received_amount += $paymentAmount;
        $sale->due_amount      = $sale->receivable_amount - $sale->received_amount;

        $isFullPayment = $sale->due_amount <= 0;

        // Update status if fully paid and not already 'delivered' or 'cancelled'
        if ($isFullPayment && $sale->status == 'pending') {
            $sale->status = 'confirmed';
        }

        $sale->save();

        // You would typically log this as a separate transaction/payment model
        // For simplicity, we'll log an action entry here
        Action::newEntry($sale, 'PAYMENT_RECEIVED', [
            'amount' => $paymentAmount,
            'method' => $request->payment_method,
            'details' => $request->transaction_id
        ]);

        $notify[] = ['success', 'Payment of ' . gs('cur_sym') . getAmount($paymentAmount) . ' recorded successfully. ' . ($isFullPayment ? 'Order is now fully paid.' : '')];
        return back()->withNotify($notify);
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

        $sale->status = $request->status;
        $sale->save();

        Action::newEntry($sale, 'STATUS_UPDATED');

        $notify[] = ['success', 'Order status updated to ' . ucfirst($sale->status) . ' successfully'];
        return back()->withNotify($notify);
    }

    protected function saveSaleData($sale)
    {
        $request    = request();
        $discount   = $request->discount ?? 0;
        $totalReceived = $request->received_amount ?? 0; // The amount paid during order creation/edit
        $receivable = $this->totalPrice - $discount;

        // If editing an existing order, maintain the previously received amount unless a new one is provided.
        // For simplicity, for store/update, we'll assume the 'received_amount' from the request is only what's being paid *now*.
        // If it's a new order, it's the initial payment. If it's an update, it's an adjustment/new payment for now.
        // A dedicated 'makePayment' is better, but to save the initial payment:

        if ($sale->exists) {
            // For update, the received_amount in the request is the new payment
            $sale->received_amount = $sale->received_amount + $totalReceived;
        } else {
            // For store, the received_amount in the request is the initial payment
            $sale->received_amount = $totalReceived;
        }

        $dueAmount  = $receivable - $sale->received_amount;

        // Initial payment method for new orders
        $paymentMethod = $request->payment_method ?? null;
        $bankId = $request->bank_id ?? null;

        // Only update these fields if a payment was made during order creation/edit
        if ($totalReceived > 0) {
            $sale->payment_method = $paymentMethod;
            $sale->bank_id = $bankId;
        }

        // Fallback for customer and warehouse
        $defaultCustomer = Customer::first()->id ?? 1;
        $defaultWarehouse = Warehouse::first()->id ?? 1;

        $sale->customer_id       = $request->customer_id ?? $defaultCustomer;
        $sale->customer_name       = $request->customer_name ?? 'Walkin-customer';
        $sale->warehouse_id      = $request->warehouse_id ?? $defaultWarehouse; // No warehouse needed
        $sale->sale_date         = Carbon::parse($request->sale_date);
        $sale->status            = $request->status ?? 'pending';
        $sale->total_price       = $this->totalPrice;
        $sale->discount_amount   = $discount;
        $sale->receivable_amount = $receivable;
        $sale->due_amount        = $dueAmount;
        $sale->note              = $request->note;

        // Status check for full payment on creation/update
        if ($sale->due_amount <= 0 && $sale->status == 'pending') {
            $sale->status = 'confirmed';
        }

        $sale->save();

        return $sale;
    }

    // --- Existing methods (storeSaleDetails, getTotalPrice, validation, searchProduct, lastInvoice) go here ---

    protected function storeSaleDetails($sale)
    {
        $saleDetails = @$this->saleDetails ?? null;

        // Remove old details if updating
        if ($saleDetails) {
            $existingProductIds = $saleDetails->pluck('product_id')->toArray();
            $newProductIds = $this->products->pluck('product_id')->toArray();

            // Delete products that are no longer in the order
            $toDelete = array_diff($existingProductIds, $newProductIds);
            if (!empty($toDelete)) {
                SaleDetails::where('sale_id', $sale->id)
                    ->whereIn('product_id', $toDelete)
                    ->delete();
            }
        }

        foreach ($this->products as $product) {
            $product    = (object) $product;
            $saleDetail = new SaleDetails();

            if ($saleDetails) {
                $saleDetail = $saleDetails->where('product_id', $product->product_id)->first() ?? new SaleDetails();
            }

            $saleDetail->sale_id    = $sale->id;
            $saleDetail->product_id = $product->product_id;
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
            'customer_name'         => 'required_without:customer_id|string',
            'sale_date'             => 'required|date_format:Y-m-d',
            'status'                => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'products'              => 'required|array|min:1',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity'   => 'required|integer|gt:0',
            'products.*.price'      => 'required|numeric|gte:0',
            'discount'              => 'nullable|numeric|gte:0',
            'note'                  => 'nullable|string',
            // Payment fields (only required when an amount is received)
            'received_amount'       => 'nullable|numeric|gte:0',
            'payment_method'        => 'nullable|required_with:received_amount|in:cash,bank,mobile',
            'bank_id'               => 'nullable|required_if:payment_method,bank|exists:banks,id',
        ];

        // Conditional validation for customer_name might need adjustment based on your front-end logic (creating a new customer)

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
