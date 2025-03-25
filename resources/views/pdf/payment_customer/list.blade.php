@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Customer')</th>
                <th>@lang('Trx')</th>
                <th>@lang('Reason')</th>
                <th>@lang('Amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentLogs as $log)
                <tr>
                    <td>
                        @if ($log->sale_id)
                            {{ @$log->sale->invoice_no }}
                        @else
                            {{ @$log->saleReturn->sale->invoice_no }}
                        @endif
                    </td>
                    <td> {{ showDateTime($log->created_at, 'm/d/Y') }} </td>
                    <td> {{ $log->customer->name }} </td>
                    <td> {{ $log->trx }} </td>
                    <td> {{ ucfirst(strtolower(keyToTitle($log->remark))) }} </td>
                    <td> {{ showAmount($log->amount) }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
