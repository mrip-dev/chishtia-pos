@extends('pdf.layouts.master2')


@section('content')
<!-- Customer Info -->
<div class="top-section">
    <div class="customer-info">
        Customer: {{ $selectedUser?->name ?? 'N/A' }}
    </div>
    <div class="customer-date">
        Date: {{ now()->format('d-m-Y') }}
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="p-3">Stock In/Out</h4>
        <table class="table table--light table-bordered style--two bg--white">
            <thead>
                <tr>
                    <th>@lang('Title') | @lang('CTN NO')</th>
                    <th>@lang('Warehouse') | @lang('Type')</th>
                    <th>@lang('Date')</th>
                    <th>@lang('Service Charges')</th>
                    <th>@lang('Products')</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientStocks as $item)
                <tr @include('partials.bank-history-color', ['id'=> $item->id])>

                    <td>
                        <span class="text--primary fw-bold"> {{ $item->title }}</span>
                        <br>
                        <small>{{ $item->tracking_id }}</small>
                    </td>
                    <td>
                        <span class="text--primary fw-bold"> {{ $item->warehouse->name }}</span>
                        <br>
                        <small>{{ $item->stock_type ? $item->stock_type== 'in' ? 'Stock In' : 'Stock Out' : '' }}</small>
                    </td>

                    <td>
                        {{ $item->created_at->format('d M, Y') }}
                    </td>

                    <td>
                        <span class="text--primary fw-bold">@lang('Received'):  {{ number_format($item->total_amount) }} {{ gs('cur_sym') }}</span>
                        <br>
                        <small>{{number_format( $item->recieved_amount) }} {{ gs('cur_sym') }}</small>
                        <br>
                        <span class="text--primary fw-bold"> {{ number_format($item->due_amount) }} {{ gs('cur_sym') }}</span>

                    </td>
                    <td>
                        <span class="text--primary fw-bold"> {{ number_format($item->due_amount) }} {{ gs('cur_sym') }}</span>
                    </td>

                </tr>
                @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table><!-- table end -->
    </div>

    <div class="col-md-12">
        <h4 class="p-3">Stock Transfer</h4>
        <div class="table-responsive--md table-responsive">
            <table class="table table--light table-bordered style--two bg--white">
                <thead>
                    <tr>
                        <th>@lang('To') | @lang('User')</th>
                        <th>@lang('To') | @lang('Warehouse')</th>
                        <th>@lang('Date')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientStocktransfers as $item)
                    <tr>
                        <td>
                            <span class="fw-bold">{{ $item->toUser?->name }}</span>
                        </td>
                        <td> {{ $item->toWarehouse?->name }}
                        </td>
                        <td>
                            {{ $item->created_at->format('d M, Y') }}

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table><!-- table end -->
        </div>
    </div>
</div>

<h4 class="p-3">Available Stock</h4>
<table class="table table--light table-bordered style--two bg--white">
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

@endsection