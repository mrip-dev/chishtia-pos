@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table--light style--two">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Current Stock')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stocksByProduct as $product)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><span class="fw-bold">{{ $product->warehouse->name }}</span></td>
                    <td class="text--primary">{{ $product->quantity }} {{ $product->product->unit->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table><!-- table end -->
@endsection
