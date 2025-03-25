@extends('pdf.layouts.master')

@section('main-content')
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
                    <td>{{ $details->quantity . ' ' . $details->product->unit->name }} </td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table><!-- table end -->
@endsection
