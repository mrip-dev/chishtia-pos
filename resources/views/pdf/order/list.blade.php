@extends('pdf.layouts.master2')

@section('content')


    <table>
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Customer')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Driver Name')<br>@lang('Contact')</th>
                <th>@lang('Vehicle No')<br>@lang('Fare')</th>
                <th>@lang('Loading')<br>@lang('Unloading')</th>
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
                    <td>{{ $sale->customer?->name }}</td>
                    <td>{{ $sale->warehouse->name }}</td>
                    <td>
                        <strong>{{ $sale->driver_name }}</strong><br>
                        +{{ $sale->driver_contact }}
                    </td>
                    <td>
                        {{ $sale->vehicle_number }}<br>
                        {{ $sale->fare }}
                    </td>
                    <td>{{ $sale->loading }}</td>
                    <td>{{ showAmount($sale->receivable_amount) }}</td>
                    <td>
                        @if ($sale->due_amount < 0) - @endif
                        {{ showAmount(abs($sale->due_amount)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
