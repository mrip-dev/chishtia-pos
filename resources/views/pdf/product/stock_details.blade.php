@extends('pdf.layouts.master2')

@section('content')
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
            <td class="text--primary"> <span class="fw-bold text--primary">QTY : {{ $product->totalInStock() }}</span>

                @if($product->unit && ($product->unit->name == 'kg' || $product->unit->name == 'KG' || $product->unit->name == 'Kg'))
                <br>
                <span class="text--small ">Weight : {{ $product->totalWeightInStock() }} {{ $product->unit?->name }}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table><!-- table end -->
@endsection