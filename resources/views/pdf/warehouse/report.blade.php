@extends('pdf.layouts.master')

@section('main-content')

<div class="list--row mb-15px">
    <div class="float-left">
        <p class="mb-5px">@lang('Warehouse'): {{ $warehouse->name }}</p>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>@lang('S.N.')</th>
            <th>@lang('Name')</th>
            <th>@lang('SKU')</th>
            <th>@lang('Stock')</th>
        </tr>
    </thead>
    <tbody>

        @forelse($stocksByWarehouse as $details)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $details->product->name }}</td>
            <td>{{ $details->product->sku }}</td>
            <td> <span class="fw-bold text--primary">QTY : {{ $details->product->totalInStock() }}</span>

                @if($details->product->unit && ($details->product->unit->name == 'kg' || $details->product->unit->name == 'KG' || $details->product->unit->name == 'Kg'))
                <br>
                <span class="text--small ">Weight : {{ $details->product->totalWeightInStock() }} {{ $details->product->unit?->name }}</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
        </tr>
        @endforelse
    </tbody>
</table><!-- table end -->
@endsection