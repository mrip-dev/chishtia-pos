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
                <th>@lang('Receivable')</th>
                <th>@lang('Due')</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($purchaseReturn as $return)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $return->purchase->invoice_no }}</td>
                    <td>{{ showDateTime($return->return_date, 'm/d/Y') }}</td>
                    <td>{{ $return->purchase->supplier->name }}</td>
                    <td>{{ $return->purchase->warehouse->name }}</td>
                    <td>{{ showAmount($return->receivable_amount) }}</td>
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
