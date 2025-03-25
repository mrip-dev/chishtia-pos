@extends('pdf.layouts.master')

@section('main-content')
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
                        {{ $product->totalInStock() . ' ' . $product->unit->name }}
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
