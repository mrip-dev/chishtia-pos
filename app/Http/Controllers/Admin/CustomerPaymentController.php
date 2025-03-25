<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\SaleReturn;

use Illuminate\Http\Request;

class CustomerPaymentController extends Controller
{
    public function index($id)
    {
        $customer  = Customer::findOrFail($id);
        $pageTitle = 'Unsettled Payments with ' . $customer->name;
        $sales     = Sale::where('customer_id', $id)->with('saleReturn')->get();
        $pdfButton = true;

        $pageTitle = 'Unsettled Payments with ' . $customer->name;
        $routePDF  = route('admin.customer.payment.pdf', ['id' => $id]);
        return view('admin.payment.customer.list', compact('pageTitle', 'sales', 'customer', 'pdfButton', 'routePDF'));
    }


    public function customerPayPDF($id)
    {
        $customer  = Customer::find($id);
        $pageTitle = 'Unsettled Payments with ' . $customer->name;
        $sales     = Sale::where('customer_id', $id)->with('saleReturn')->get();
        return downloadPDF('pdf.customer.payments', compact('pageTitle', 'customer', 'sales'));
    }

    public function clearPayment($id)
    {
        $sales = Sale::where('customer_id', $id)->with('saleReturn')->get();

        foreach ($sales as $sale) {
            $amount = $sale->due_amount;

            if ($amount == 0) {
                continue;
            }

            $sale->received_amount += $amount;
            $sale->due_amount   = 0;
            $sale->save();

            $remark = $amount > 0 ? 'RECEIVED_PAYMENT_FOR_SALE' : 'RETURNED_EXTRA_PAYMENT_FROM_SALE';

            $payment              = new CustomerPayment();
            $payment->customer_id = $sale->customer_id;
            $payment->sale_id     = $sale->id;
            $payment->amount      = abs($amount);
            $payment->trx         = getTrx();
            $payment->remark      = $remark;
            $payment->save();

            Action::newEntry($payment, 'CREATED');

            $return = $sale->saleReturn;

            if ($return) {
                $amount = $return->due_amount;
                if ($amount == 0) {
                    continue;
                }
                $remark = $amount > 0 ? 'RETURNED_PAYMENT_FOR_SALE_RETURN' : 'RECEIVED_EXTRA_PAYMENT_FROM_SALE_RETURN';

                $return->paid_amount += $amount;
                $return->due_amount = 0;
                $return->save();

                $payment                 = new CustomerPayment();
                $payment->customer_id     = $return->customer_id;
                $payment->sale_return_id = $return->id;
                $payment->amount         = abs($amount);
                $payment->trx            = getTrx();
                $payment->remark         = $remark;
                $payment->save();
                Action::newEntry($payment, 'CREATED');
            }
        }

        $notify[] = ['success', 'Payment completed successfully'];
        return back()->withNotify($notify);
    }

    public function salePayment(Request $request, $id)
    {
        $sale  = Sale::findOrFail($id);

        $this->validation($request, abs($sale->due_amount));

        $isPaying = $sale->due_amount < 0;
        $amount   = $request->amount;

        if ($isPaying) {
            $amount *= -1;
            $remark = 'RETURNED_EXTRA_PAYMENT_FROM_SALE';
            $notification = 'Payment completed successfully';
        } else {
            $remark   = 'RECEIVED_PAYMENT_FOR_SALE';
            $notification = 'Payment received successfully';
        }

        $sale->received_amount += $amount;
        $sale->due_amount -= $amount;
        $sale->save();

        $received =  new CustomerPayment();
        $received->customer_id = $sale->customer_id;
        $received->sale_id     = $sale->id;
        $received->amount      = $request->amount;
        $received->trx         = getTrx();
        $received->remark      = $remark;
        $received->save();

        Action::newEntry($received, 'CREATED');

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }


    public function storeCustomerPayablePayment(Request $request, $id)
    {
        $saleReturn  = SaleReturn::findOrFail($id);

        $this->validation($request, abs($saleReturn->due_amount));

        $isReceiving = $saleReturn->due_amount < 0;
        $amount      = $request->amount;

        if ($isReceiving) {
            $amount *= -1;
            $remark       = 'RECEIVED_EXTRA_PAYMENT_FROM_SALE_RETURN';
            $notification = 'Payment received successfully';
        } else {
            $remark       = 'RETURNED_FOR_SALE_RETURN';
            $notification = 'Payment received successfully';
        }


        $saleReturn->paid_amount += $amount;
        $saleReturn->due_amount -= $amount;
        $saleReturn->save();

        $customerPayment =  new CustomerPayment();
        $customerPayment->customer_id    = $saleReturn->customer_id;
        $customerPayment->sale_return_id = $saleReturn->id;
        $customerPayment->amount         = $request->amount;
        $customerPayment->trx            = getTrx();
        $customerPayment->remark         = $remark;
        $customerPayment->save();
        Action::newEntry($customerPayment, 'CREATED');
        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }


    private function validation($request, $dueAmount)
    {
        $request->validate([
            'amount' => "required|numeric|gt:0|lte:$dueAmount",
        ]);
    }
}
