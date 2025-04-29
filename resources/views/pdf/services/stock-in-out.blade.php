@extends('pdf.layouts.master')


@section('main-content')
<div class="card mt-4">

<div class="card-header">

    <div class="justify-content-between align-items-start">
        <div class="row">
            <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                <p class="mb-1"><strong>Title:</strong> {{ $selectedStock->title }}</p>
                <p class="mb-1"><strong>Warehouse:</strong> {{ $selectedStock->warehouse->name }}</p>
                <p class="mb-1"><strong>@lang('Vendor / Client'):</strong> {{ $selectedStock->user->name }}</p>
            </div>
            <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                <p class="mb-1"><strong>Labour:</strong> {{ $selectedStock->labour }}</p>
                <p class="mb-1"><strong>Vehicle Number:</strong> {{ $selectedStock->vehicle_number }}</p>
                <p class="mb-1"><strong>Driver Name:</strong> {{ $selectedStock->driver_name }}</p>
            </div>
            <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                <h3 class="mb-1 "><strong>Total :</strong> {{ number_format($stockTotalAmount,2) }}</h3>

            </div>
        </div>
    </div>
</div>

<div class="card-body">
    <table class="table table--light style--two bg--white">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Amount</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($selectedStock->stockInOuts as $entry)
            <tr>
                <td>{{ $entry->product?->name }}</td>
                <td>{{ $entry->quantity }}</td>
                <td>{{ $entry->unit_price }}</td>
                <td>{{ $entry->total_amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection