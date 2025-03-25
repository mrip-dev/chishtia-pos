@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Tracking No.')</th>
                <th>@lang('Date')</th>
                <th>@lang('From')</th>
                <th>@lang('To')</th>
                <th>@lang('Products')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfers as $transfer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $transfer->tracking_no }} </td>
                    <td>{{ showDateTime($transfer->sale_date, 'm/d/Y') }}</td>
                    <td>{{ $transfer->warehouse->name }} </td>
                    <td>{{ $transfer->toWarehouse->name }} </td>
                    <td>{{ $transfer->transfer_details_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
