<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Action;
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
}
