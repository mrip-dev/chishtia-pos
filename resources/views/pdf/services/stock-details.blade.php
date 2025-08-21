@extends('pdf.layouts.master2')


@section('content')
<div class="card-header">
    <div class="justify-content-between align-items-start">
        <div class="row">
            <div class="justify-content-between align-items-start">
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                        <p class="mb-1"><strong>User :</strong> {{ $selectedUser?->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-body">
    <table class="table table--dark style--two bg--white">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Weight</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedStock as $entry)
            <tr>
                <td>{{ $entry->product?->name }}</td>
                <td>{{ $entry->quantity }}</td>
                  <td>{{ $entry->net_weight ?? 0 }} @if($entry->product->unit->name && strtolower($entry->product->unit->name)=='kg') {{ $entry->product->unit->name}} @endif</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection