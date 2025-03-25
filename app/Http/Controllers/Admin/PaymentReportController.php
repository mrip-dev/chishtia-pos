<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerPayment;
use App\Models\SupplierPayment;

class PaymentReportController extends Controller
{

    protected function getSupplierPayment()
    {
        return SupplierPayment::searchable(['trx', 'supplier:name', 'purchase:invoice_no', 'purchaseReturn.purchase:invoice_no'])->dateFilter()->filter(['remark'])->with('supplier:id,name', 'purchase:id,invoice_no', 'purchaseReturn.purchase:id,invoice_no');
    }
    public function supplierPaymentLogs()
    {
        $pageTitle   = 'Supplier Payments';
        $paymentLogs = $this->getSupplierPayment()->paginate(getPaginate());
        $remarks     = SupplierPayment::groupBy('remark')->get('remark')->pluck('remark');
        $pdfButton   = true;
        return view('admin.payment.supplier.log', compact('pageTitle', 'paymentLogs', 'remarks'));
    }


    public function supplierPaymentPDF()
    {
        $pageTitle   = 'Supplier Payments';
        $paymentLogs = $this->getSupplierPayment()->get();
        return downloadPDF('pdf.payment_supplier.list', compact('pageTitle', 'paymentLogs'));
    }
    public function supplierPaymentCSV()
    {
        $pageTitle = 'Supplier Payments';
        $filename  = $this->downloadSupplierCSV($pageTitle, $this->getSupplierPayment()->get());
        return response()->download(...$filename);
    }

    protected function downloadSupplierCsv($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Invoice No.,Date,Supplier,Trx.,Reason,Amount\n";
        $curSym   = gs('cur_sym');
        foreach ($data as $payment) {
            if ($payment->purchase_id) {
                $invoice = @$payment->purchase->invoice_no;
            } else {
                $invoice = @$payment->purchaseReturn->purchase->invoice_no;
            }
            $date     = showDateTime(@$payment->created_at, 'd-m-Y');
            $supplier = $payment->supplier->name;
            $reason   = ucwords(strtolower(keyToTitle($payment->remark)));
            $amount   = $curSym . getAmount($payment->amount);

            $column .= "$invoice,$date,$supplier,$payment->trx,$reason,$amount \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }

    protected function getCustomerPayment()
    {
        return CustomerPayment::searchable(['trx', 'customer:name', 'sale:invoice_no', 'saleReturn.sale:invoice_no'])->filter(['remark'])->dateFilter()->with('customer:id,name', 'sale:id,invoice_no', 'saleReturn.sale:id,invoice_no');
    }
    public function customerPaymentLogs()
    {
        $pageTitle   = 'Customer Payments';
        $paymentLogs = $this->getCustomerPayment()->paginate(getPaginate());
        $remarks     = CustomerPayment::groupBy('remark')->get('remark')->pluck('remark');
        return view('admin.payment.customer.log', compact('pageTitle', 'paymentLogs', 'remarks'));
    }

    public function customerPaymentPDF()
    {
        $pageTitle   = 'Customer Payments';
        $paymentLogs = $this->getCustomerPayment()->get();
        return downloadPDF('pdf.payment_customer.list', compact('pageTitle', 'paymentLogs'));
    }
    public function customerPaymentCSV()
    {
        $pageTitle = 'Customer Payments';
        $filename  = $this->downloadCustomerCSV($pageTitle, $this->getCustomerPayment()->get());
        return response()->download(...$filename);
    }

    protected function downloadCustomerCSV($pageTitle, $data)
    {
        $filename = "assets/files/csv/example.csv";
        $myFile   = fopen($filename, 'w');
        $column   = "Invoice No.,Date,Customer,Trx.,Reason,Amount\n";
        foreach ($data as $payment) {
            if ($payment->sale_id) {
                $invoice = @$payment->sale->invoice_no;
            } else {
                $invoice = @$payment->saleReturn->sale->invoice_no;
            }
            $date     = showDateTime(@$payment->created_at, 'd-m-Y');
            $customer = $payment->customer->name;
            $reason   = ucwords(strtolower(keyToTitle($payment->remark)));
            $amount   = gs('cur_sym') . getAmount($payment->amount);

            $column .= "$invoice,$date,$customer,$payment->trx,$reason,$amount \n";
        }
        fwrite($myFile, $column);
        $headers = [
            'Content-Type' => 'application/csv',
        ];
        $name  = $pageTitle . time() . '.csv';
        $array = [$filename, $name, $headers];
        return $array;
    }
}
