@extends('pdf.layouts.master2')


@section('content')
<div class="list--row mb-15px">
    <div class="float-left">
        <p class="mb-5px"><strong>From :</strong> {{ $selectedStock->fromUser?->name }}</p>
        <p class="mb-5px"><strong>Warehouse:</strong> {{ $selectedStock->fromWarehouse?->name }}</p>
    </div>
    <div class="float-right">
        <p class="mb-5px"><strong>To:</strong> {{ $selectedStock->toUser?->name }}</p>
        <p class="mb-5px"><strong>Warehouse:</strong> {{ $selectedStock->toWarehouse?->name }}</p>
        <p class="mb-5px "><strong>Total :</strong> {{ number_format($stockTotalAmount,2) }}</p>
    </div>
</div>
<div class="card mt-4">

    <div class="card-body">
        <table class="table table--dark style--two bg--white">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Service Charges</th>
                    <th>Date</th>
                    <th>Total Amount</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($selectedStock->stockTransferDetails as $entry)
                <tr>
                    <td>{{ $entry->product?->name }}</td>
                    <td>{{ $entry->quantity }}</td>
                    <td>{{ $entry->unit_price }}</td>
                    <td>{{ $entry->created_at }}</td>
                    <td>{{ $entry->total_amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection