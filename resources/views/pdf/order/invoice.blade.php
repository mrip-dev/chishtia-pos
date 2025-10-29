<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFTPOS Invoice</title>
    <style>
        /* === EFTPOS RECEIPT STYLE (Adjusted to match sample, with financial details) === */
        @page {
            size: 80mm auto; /* Standard thermal printer width */
            margin: 0;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 11px; /* Slightly smaller for compactness */
            width: 80mm;
            margin: 0 auto;
            color: #000;
            line-height: 1.2; /* Tighter line spacing */
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }

        .mb-2 { margin-bottom: 2px; } /* Smaller margins */
        .mb-5 { margin-bottom: 5px; }
        .mt-5 { margin-top: 5px; }
        .mt-10 { margin-top: 10px; }

        .line {
            border-top: 1px solid #000; /* Solid line instead of dashed */
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        th, td {
            padding: 2px 0; /* Tight padding */
        }

        th {
            border-bottom: 1px solid #000; /* Solid line */
            font-weight: bold;
            padding-bottom: 4px; /* Slightly more space below header */
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
            margin-top: 10px;
            text-align: center;
            font-size: 10px; /* Smaller footer font */
        }

        .receipt-end {
            text-align: center;
            margin-top: 10px;
            font-size: 10px;
            border-top: 1px solid #000; /* Solid line */
            padding-top: 5px;
        }

        /* Specific styles for customer info block */
        .customer-info-block {
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #000;
        }
        .customer-info-block p {
            margin: 0; /* Remove default paragraph margins */
            padding: 1px 0;
        }

    </style>
</head>
<body>

    <div class="text-center">
        <h3 class="bold" style="margin: 0; font-size: 16px;">{{ __(gs('site_name')) }}</h3>
        <!-- <p style="margin: 0;">Free Home Delivery</p> -->
        <p style="margin: 0;">{{ gs('contact_phone_1') }}</p> {{-- Assuming you have site settings for contact numbers --}}
        <p style="margin: 0;">{{ gs('contact_phone_2') }}</p> {{-- Add a second phone if available in settings --}}
        <p style="margin: 0 0 5px 0;">{{ gs('address_line_1') }}</p> {{-- Assuming you have site settings for address --}}
    </div>

    <div class="line"></div>

    <p class="mb-2">Invoice: {{ $sale->invoice_no }}</p>
    <p class="mb-2">Service: {{ ucfirst($sale->service_type) }}</p>
    <p class="mb-2">{{ showDateTime($sale->sale_date, 'M d Y h:iA') }}</p> {{-- Changed format to match sample --}}

    @if ($sale->table_no && $sale->service_type == 'dine-in') {{-- Show table_no only if dine-in --}}
        <p class="mb-2">Table No: {{ $sale->table_no }}</p>
    @endif
    @if ($sale->table_man && $sale->service_type == 'dine-in') {{-- Show server only if dine-in --}}
        <p class="mb-2">Server: {{ $sale->table_man }}</p>
    @endif

    <div class="line"></div>

    <table >
        <thead>
            <tr>
                <th class="text-left" style="width: 50%">Name</th>
                <th class="text-center" style="width: 15%">QTY</th>
                <th class="text-right" style="width: 15%">Price</th>
                <th class="text-right" style="width: 20%">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $itemCount = 0; @endphp
            @forelse($sale->saleDetails as $item)
                @php $itemCount += $item->quantity; @endphp
                <tr>
                    <td class="text-left">{{ getProductTitle($item->product->id) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ showAmount($item->price,1, false, false, false) }}</td>
                    <td class="text-right">{{ showAmount($item->total,1, false, false, false) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No Items</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="line"></div>

    <table class="totals">
        <tr>
            <td class="label">Items: {{ $itemCount }}</td> {{-- Display total number of items --}}
            <td class="value">Total Amount: {{ showAmount($sale->total_price,1,true,false, false) }}</td>
        </tr>
        <tr>
            <td class="label">Discount:</td>
            <td class="value">{{ showAmount($sale->discount_amount,1,true,false, false) }}</td>
        </tr>
        <tr>
            <td class="label bold">Net Amount:</td> {{-- Changed from Grand Total to Net Amount --}}
            <td class="value bold">{{ showAmount($sale->receivable_amount,1,true,false, false) }}</td>
        </tr>
        {{-- RESTORED: Received and Receivable/Payable --}}
        <tr>
            <td class="label">Received:</td>
            <td class="value">{{ showAmount($sale->received_amount,1,true,false, false) }}</td>
        </tr>
        <tr>
            <td class="label bold">{{ $sale->due_amount >= 0 ? 'Receivable:' : 'Payable:' }}</td>
            <td class="value bold">{{ showAmount(abs($sale->due_amount),1,true,false, false) }}</td>
        </tr>
    </table>



    <div class="customer-info-block text-center">
        <p class="bold">Customer Info</p>
        @if ($sale->customer_phone)
            <p>{{ $sale->customer_phone }}</p>
        @endif
        <p>{{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
        @if ($sale->customer_address && $sale->service_type == 'delivery') {{-- Show address only if delivery --}}
            <p>{{ $sale->customer_address }}</p>
        @endif
    </div>

    <div class="line"></div>

    <div class="text-center mt-10">
        <p style="margin:0;">Thank you for shopping with us!</p>
    </div>

    <div class="receipt-end">
        <p>*** CUSTOMER COPY ***</p>
    </div>

</body>
</html>