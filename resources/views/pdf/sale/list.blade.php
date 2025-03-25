@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Customer')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Receivable')</th>
                <th>@lang('Due')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ showDateTime($sale->sale_date, 'm/d/Y') }}</td>
                    <td>{{ $sale->customer->name }} </td>
                    <td>{{ $sale->warehouse->name }} </td>
                    <td>{{ showAmount($sale->receivable_amount) }}</td>
                    <td>
                        @if ($sale->due_amount < 0)
                            -
                        @endif
                        {{ showAmount(abs($sale->due_amount)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
