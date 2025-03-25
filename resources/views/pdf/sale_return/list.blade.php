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
            @foreach ($saleReturns as $return)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $return->sale->invoice_no }}</td>
                    <td>{{ showDateTime($return->return_date, 'm/d/Y') }}</td>
                    <td>{{ $return->customer->name }}</td>
                    <td>{{ $return->sale->warehouse->name }} </td>
                    <td>{{ showAmount($return->payable_amount) }}</td>
                    <td>
                        @if ($return->due_amount < 0)
                            -
                        @endif

                        {{ showAmount(abs($return->due_amount)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
