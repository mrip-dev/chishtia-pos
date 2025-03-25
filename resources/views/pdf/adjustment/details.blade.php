@extends('pdf.layouts.master')

@section('main-content')
    <div class="list--row mb-15px">
        <div class="float-left">
            <p class="mb-5px">@lang('Tracking No.'): {{ $adjustment->tracking_no }}</p>
            <p class="mb-5px">@lang('Warehouse'): {{ $adjustment->warehouse->name }}</p>
            <p class="mb-5px">@lang('Date'): {{ $adjustment->adjust_date }}</p>
        </div>
    </div>


    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Product')</th>
                <th>@lang('Quantity')</th>
                <th>@lang('Type')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($adjustment->adjustmentDetails as $item)
                <tr>
                    <td> {{ $loop->iteration }} </td>
                    <td> {{ $item->product->name }} </td>
                    <td> {{ $item->quantity }} {{ $item->product->unit->name }} </td>
                    <td>
                        @if (@$item->adjust_type == Status::SUBTRACT)
                            @lang('Subtracted')
                        @else
                            @lang('Added')
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
