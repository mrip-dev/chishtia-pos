<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFTPOS Invoice</title>
    <style>
        /* === EFTPOS RECEIPT STYLE === */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 80mm;
            margin: 0 auto;
            color: #000;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        .mb-5 { margin-bottom: 5px; }
        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }

        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 2px 0;
        }

        th {
            border-bottom: 1px dashed #000;
            font-weight: bold;
        }

        .totals td {
            padding: 2px 0;
        }

        .totals .label {
            text-align: left;
        }

        .totals .value {
            text-align: right;
        }

        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 11px;
        }

        .eftpos-box {
            margin-top: 10px;
            padding: 5px 0;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            text-align: center;
        }

        .eftpos-box p {
            margin: 3px 0;
            font-size: 11px;
        }

        .receipt-end {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px dashed #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <div class="text-center">
        <h3 class="bold" style="margin:0;">{{ __(gs('site_name')) }}</h3>
        <p style="margin:2px 0;">INVOICE #{{ $sale->invoice_no }}</p>
        <p style="margin:2px 0;">{{ showDateTime($sale->sale_date, 'd M Y - h:i A') }}</p>
    </div>

    <div class="divider"></div>

    <div class="text-left mb-10">
        <p class="mb-5"><strong>Customer:</strong> {{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="width: 40%">Item</th>
                <th style="width: 20%; text-align:center;">Qty</th>
                <th style="width: 20%; text-align:right;">Price</th>
                <th style="width: 20%; text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sale->saleDetails as $item)
                <tr>
                    <td>{{ getProductTitle($item->product->id) }}</td>
                    <td style="text-align:center;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">{{ showAmount($item->price) }}</td>
                    <td style="text-align:right;">{{ showAmount($item->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No Items</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">{{ showAmount($sale->total_price) }}</td>
        </tr>
        <tr>
            <td class="label">Discount</td>
            <td class="value">-{{ showAmount($sale->discount_amount) }}</td>
        </tr>
        <tr>
            <td class="label bold">Grand Total</td>
            <td class="value bold">{{ showAmount($sale->receivable_amount) }}</td>
        </tr>
        <tr>
            <td class="label">Received</td>
            <td class="value">{{ showAmount($sale->received_amount) }}</td>
        </tr>
        <tr>
            <td class="label">{{ $sale->due_amount >= 0 ? 'Receivable' : 'Payable' }}</td>
            <td class="value">{{ showAmount(abs($sale->due_amount)) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- EFTPOS Section -->
    <!-- <div class="eftpos-box">
        <p class="bold">EFTPOS PAYMENT</p>
        <p>Card Type: VISA</p>
        <p>Txn #: {{ strtoupper(Str::random(8)) }}</p>
        <p>APPROVED ✅</p>
    </div> -->

    <div class="footer">
        <p>Thank you for shopping with us!</p>
        <p>Powered by {{ __(gs('site_name')) }}</p>
    </div>

    <div class="receipt-end">
        <p>*** CUSTOMER COPY ***</p>
    </div>

</body>
</html>
