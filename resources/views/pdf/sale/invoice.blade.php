@extends('pdf.layouts.master')

@section('main-content')
    <div class="list--row mb-15px">
        <div class="float-left">
            <h6 class="title">@lang('Bill To')</h6>
            <p class="mb-5px">@lang('Name'): {{ $customer->name }}</p>
            <p class="mb-5px">@lang('Mobile'): {{ $customer->mobile }}</p>
            <p class="mb-5px">@lang('Email'): {{ $customer->email }}</p>
            <p class="mb-5px">@lang('Address'): {{ $customer->address }}</p>
        </div>

        <div class="float-right">
            <h6 class="mb-5px">@lang('Bill From') </h6>
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
                    <td>{{ $loop->iteration }} </td>
                    <td class="fw-bold">{{ $item->product->name }}</td>
                    <td>{{ $item->product->sku }} </td>
                    <td>{{ $item->quantity . ' ' . $item->product->unit->name }} </td>
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

    <div class="list--row mb-15px mt-3">
        <div class="float-right border list--row summary-content">
            <div class="border-bottom clearfix">
                <p class="float-left">@lang('Subtotal')</p>
                <p class="float-right">{{ showAmount($sale->total_price) }}</p>
            </div>

            <div class="border-bottom clearfix">
                <p class="float-left">@lang('Discount')</p>
                <p class="float-right">{{ showAmount($sale->discount_amount) }}</p>
            </div>

            <div class="border-bottom clearfix">
                <p class="float-left"> @lang('Grand Total')</p>
                <p class="float-right">{{ showAmount(abs($sale->receivable_amount)) }}</p>
            </div>

            <div class="border-bottom clearfix">
                <p class="float-left">
                    @lang('Received')
                </p>
                <p class="float-right">{{ showAmount($sale->received_amount) }}</p>
            </div>

            <div class="clearfix">
                <p class="float-left">
                    @if ($sale->due_amount >= 0)
                        @lang('Receivable')
                    @else
                        @lang('Payable')
                    @endif
                </p>

                <p class="float-right strong">{{ showAmount(abs($sale->due_amount)) }}</p>
            </div>
        </div>
    </div>
@endsection
