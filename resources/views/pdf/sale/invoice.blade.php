@extends('pdf.layouts.master2')

@section('content')
    <style>
        .flex-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .summary-content {
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
        }
        .summary-content p {
            margin: 0 0 5px;
        }
        .summary-content .clearfix {
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        tr {
            page-break-inside: avoid;
        }
    </style>

    <div class="flex">
        <div>
            <h6 class="title">@lang('Bill To')</h6>
            <p class="mb-5px">@lang('Name'): {{ $customer->name }}</p>
            <p class="mb-5px">@lang('Mobile'): {{ $customer->mobile }}</p>
            <p class="mb-5px">@lang('Email'): {{ $customer->email }}</p>
            <p class="mb-5px">@lang('Address'): {{ $customer->address }}</p>
        </div>

        <div style="text-align: right;">
            <h6 class="mb-5px">@lang('Bill From')</h6>
            <p class="strong">{{ __(gs('site_name')) }}</p>
            <p class="mb-5px">@lang('Invoice No.'): #<b>{{ $sale->invoice_no }}</b></p>
            <p class="mb-5px">@lang('Date'): {{ showDateTime($sale->sale_date, 'd F Y') }}</p>
            <p class="mb-5px">@lang('Warehouse'): {{ $sale->warehouse->name }}</p>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Name')</th>
                <th>@lang('SKU')</th>
                <th>@lang('Quantity')</th>
                <th>@lang('Unit Price')</th>
                <th>@lang('Total')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sale->saleDetails as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $item->product->name }}</td>
                    <td>{{ $item->product->sku }}</td>
                    <td>{{ $item->quantity . ' ' . $item->product->unit->name }}</td>
                    <td>{{ showAmount($item->price) }}</td>
                    <td>{{ showAmount($item->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex-row" style="margin-top: 30px;">
        <div></div> <!-- Spacer -->
        <div class="summary-content">
            <div class="clearfix border-bottom">
                <p>@lang('Subtotal')</p>
                <p>{{ showAmount($sale->total_price) }}</p>
            </div>

            <div class="clearfix border-bottom">
                <p>@lang('Discount')</p>
                <p>{{ showAmount($sale->discount_amount) }}</p>
            </div>

            <div class="clearfix border-bottom">
                <p>@lang('Grand Total')</p>
                <p>{{ showAmount(abs($sale->receivable_amount)) }}</p>
            </div>

            <div class="clearfix border-bottom">
                <p>@lang('Received')</p>
                <p>{{ showAmount($sale->received_amount) }}</p>
            </div>

            <div class="clearfix strong">
                <p>
                    @if ($sale->due_amount >= 0)
                        @lang('Receivable')
                    @else
                        @lang('Payable')
                    @endif
                </p>
                <p>{{ showAmount(abs($sale->due_amount)) }}</p>
            </div>
        </div>
    </div>
@endsection
