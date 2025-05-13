@extends('pdf.layouts.master')

@section('main-content')
    <div class="list--row mb-15px">
        <div class="float-left">
            <p class="mb-5px">@lang('Tracking No.'): {{ $transfer->tracking_no }}</p>
            <p class="mb-5px">@lang('From'): {{ $transfer->warehouse->name }}</p>
            <p class="mb-5px">@lang('To'): {{ $transfer->toWarehouse->name }}</p>
            <p class="mb-5px">@lang('Date'): {{ showDateTime($transfer->transfer_date, 'm/d/Y') }}</p>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Product')</th>
                <th>@lang('Quantity')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transfer->transferDetails as $item)
                <tr>
                    <td>{{ $loop->iteration }} </td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }} {{ $item->product->unit->name }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
