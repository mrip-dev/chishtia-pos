<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\CustomerTransaction;
use App\Models\ProductStock;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\ServiceStockDetail;
use App\Models\StockTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PdfController extends Controller
{

    public function stockDetailPDF()
    {
        $data = session()->get('pdf_data');
        if (!$data) {
            return redirect()->back()->with('error', 'No data found for PDF');
        }
        return downloadPDF('pdf.services.stock-details', [
            'selectedUser' => $data['user'],
            'selectedStock' => $data['stock'],
            'pageTitle' => 'Stock Details'
        ]);
    }
    public function customersPdf(Request $request)
    {
        $pageTitle = 'Customer Transaction PDF';

        $transactions = CustomerTransaction::query()
            ->where('customer_id', $request->customer_id);

        if ($request->filled('search')) {
            $transactions->where('customer_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('start_date')) {
            $transactions->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $transactions->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $transactions->get();

        return view('admin.partials.customer-pdf', compact('pageTitle', 'transactions'));
    }

}
