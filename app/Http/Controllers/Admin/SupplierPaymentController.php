<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Supplier;
use App\Models\SupplierPayment;

use Illuminate\Http\Request;

class SupplierPaymentController extends Controller
{
 
    public function index($id)
    {
        $supplier  = Supplier::find($id);
        $pageTitle = 'Unsettled Payments with ' . $supplier->name;
        $purchases = Purchase::where('supplier_id', $id)->with('purchaseReturn')->get();
        $pdfButton = true;
        $pageTitle = 'Unsettled Payments with ' . $supplier->name;
        $routePDF  = route('admin.supplier.payment.pdf', ['id' => $id]);
        return view('admin.payment.supplier.list', compact('pageTitle', 'purchases', 'supplier', 'pdfButton', 'routePDF'));
    }


    public function customerPayPDF($id)
    {
        $supplier  = Supplier::find($id);
        $pageTitle = 'Unsettled Payments with ' . $supplier->name;
        $purchases = Purchase::where('supplier_id', $id)->with('purchaseReturn')->get();
        return downloadPDF('pdf.supplier.payments', compact('pageTitle', 'supplier', 'purchases'));
    }

    public function clearPayment($id)
    {
        $purchases = Purchase::where('supplier_id', $id)->with('purchaseReturn')->get();

        foreach ($purchases as $purchase) {
            $amount = $purchase->due_amount;
            if ($amount == 0) {
                continue;
            }

            $purchase->paid_amount += $amount;
            $purchase->due_amount   = 0;
            $purchase->save();

            $remark = $amount > 0 ? 'PAID_FOR_PURCHASE' : 'RECEIVED_EXTRA_PAYMENT_FROM_PURCHASE';

            $payment              =  new SupplierPayment();
            $payment->supplier_id = $purchase->supplier_id;
            $payment->purchase_id = $purchase->id;
            $payment->amount      = abs($amount);
            $payment->trx         = getTrx();
            $payment->remark      = $remark;
            $payment->save();
            Action::newEntry($payment, 'CREATED');

            $return = $purchase->purchaseReturn;

            if ($return) {
                $amount = $return->due_amount;
                if ($amount == 0) {
                    continue;
                }
                $remark = $amount > 0 ? 'RECEIVED_FOR_PURCHASE_RETURN' : 'RETURN_EXTRA_PAYMENT_FROM_PURCHASE_RETURN';

                $return->received_amount += $amount;
                $return->due_amount = 0;
                $return->save();

                $payment                     =  new SupplierPayment();
                $payment->supplier_id        = $return->supplier_id;
                $payment->purchase_return_id = $return->id;
                $payment->amount             = abs($amount);
                $payment->trx                = getTrx();
                $payment->remark             = $remark;
                $payment->save();
                Action::newEntry($payment, 'CREATED');
            }
        }

        $notify[] = ['success', 'Payment completed successfully'];
        return back()->withNotify($notify);
    }


    public function purchasePayment(Request $request, $id)
    {
        $purchase  = Purchase::findOrFail($id);

        $this->validation($request, abs($purchase->due_amount));


        $isGiving = $purchase->due_amount > 0;
        $amount   = $request->amount;

        if ($isGiving) {
            $amount *= -1;
            $remark = 'PAID_FOR_PURCHASE';
            $notification = 'Payment given successfully';
        } else {
            $remark   = 'RECEIVED_EXTRA_PAYMENT_FROM_PURCHASE';
            $notification = 'Payment received successfully';
        }

        $purchase->paid_amount -= $amount;
        $purchase->due_amount += $amount;
        $purchase->save();

        $payment              =  new SupplierPayment();
        $payment->supplier_id = $purchase->supplier_id;
        $payment->purchase_id = $purchase->id;
        $payment->amount      = $request->amount;
        $payment->trx         = getTrx();
        $payment->remark      = $remark;
        $payment->save();

        Action::newEntry($payment, 'CREATED');

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function purchaseReturnPayment(Request $request, $id)
    {
        $purchaseReturn  = PurchaseReturn::findOrFail($id);
        $this->validation($request, abs($purchaseReturn->due_amount));

        $isReceiving = $purchaseReturn->due_amount > 0;
        $amount      = $request->amount;

        if ($isReceiving) {
            $amount *= -1;
            $remark       = 'RECEIVED_FOR_PURCHASE_RETURN';
            $notification = 'Payment received successfully';
        } else {
            $remark       = 'RETURNED_EXTRA_PAYMENT_FROM_PURCHASE_RETURN';
            $notification = 'Payment given successfully';
        }

        $purchaseReturn->received_amount -= $amount;
        $purchaseReturn->due_amount += $amount;
        $purchaseReturn->save();

        $payment                     =  new SupplierPayment();
        $payment->supplier_id        = $purchaseReturn->supplier_id;
        $payment->purchase_return_id = $purchaseReturn->id;
        $payment->amount             = $request->amount;
        $payment->trx                = getTrx();
        $payment->remark             = $remark;
        $payment->save();
        Action::newEntry($payment, 'CREATED');

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
