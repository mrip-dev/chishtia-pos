@extends('pdf.layouts.master')


@section('main-content')
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
    <table class="table table--light style--two bg--white">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($selectedStock as $entry)
            <tr>
                <td>{{ $entry->product?->name }}</td>
                <td>{{ $entry->quantity }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection