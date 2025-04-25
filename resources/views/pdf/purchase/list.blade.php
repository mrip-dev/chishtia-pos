@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Invoice')</th>
                <th>@lang('Date')</th>
                <th>@lang('Supplier')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Driver Name')</th>
                <th>@lang('Driver Phone')</th>
                <th>@lang('V.No')</th>
                <th>@lang('Fare')</th>
                <th>@lang('Payable')</th>
                <th>@lang('Due')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                <tr>
                    <td> {{ $loop->iteration }} </td>
                    <td> {{ $purchase->invoice_no }} </td>
                    <td> {{ showDateTime($purchase->purchase_date, 'm/d/Y') }} </td>
                    <td> {{ $purchase->supplier->name }} </td>
                    <td> {{ $purchase->warehouse->name }} </td>
                    <td> {{ $purchase->driver_name }} </td>
                    <td> {{ $purchase->driver_phone }} </td>
                    <td> {{ $purchase->vehicle_no }} </td>
                    <td>
                        {{  $purchase->fare  }}
                    <td>
                    </td>
                    <td> {{ showAmount($purchase->payable_amount) }} </td>
                    <td>
                        @if ($purchase->due_amount < 0)
                            -
                        @endif
                        {{ showAmount(abs($purchase->due_amount)) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
