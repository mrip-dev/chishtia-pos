@extends('pdf.layouts.master2')

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Customer')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Driver Name') | @lang('Contact')</th>
                <th>@lang('Vehicle No') | @lang('Fare')</th>
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
                    <td>
                        <span class="text--success fw-bold"> {{ $sale->driver_name }}</span>
                        <br>
                        +{{ $sale->driver_contact }}
                    </td>
                    <td>
                        {{ $sale->vehicle_number }}
                        <br>
                        {{ $sale->fare }}
                    </td>
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
