@extends('pdf.layouts.master2')

@section('content')
<table class="table table-striped">
    <thead>
        <tr>
            <th>@lang('S.N.')</th>
            <th>@lang('SKU')</th>
            <th>@lang('Name')</th>
            <th>@lang('Brand')</th>
            <th>@lang('Stock')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td> {{ $product->sku }} </td>
            <td>
                {{ __($product->name) }}
            </td>
            <td>{{ __(@$product->brand->name) }} </td>
            <td>
                <span class="fw-bold text--primary">QTY : {{ $product->totalInStock() }}</span>
                @if($product->unit && ($product->unit->name == 'kg' || $product->unit->name == 'KG' || $product->unit->name == 'Kg'))
                <br>
                <span class="text--small ">Weight : {{ $product->totalWeightInStock() }} {{ $product->unit?->name }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection