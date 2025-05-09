@extends('pdf.layouts.master2')


@section('content')
<!-- Customer Info -->
<div class="top-section">
    <table style="width: 100%; margin-top: 50px; font-size: 13px;">
        <tr>
            <td style="text-align: left;border:none;">
                <div class="customer-info">
                    Customer: {{ $selectedUser?->name ?? 'N/A' }}
                </div>
            </td>
            <td style="text-align: right;border:none;">
                <div class="customer-date">
                    Date: {{ now()->format('d-m-Y') }}
                </div>
            </td>
        </tr>
    </table>
</div>
<div class="row">
    <div class="col-md-12">
        <h4 class="p-3">Stock In/Out</h4>
        <table class="table table--light table-bordered style--two bg--white">
            <thead>
                <tr>
                    <th>@lang('Title') | @lang('CTN NO') | @lang('Warehouse')</th>
                    <th>@lang('Type') | @lang('Date')</th>
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
                        <br>
                        <span class="text--primary"> {{ $item->warehouse->name }}</span>

                    </td>
                    <td>

                        <small>{{ $item->stock_type ? $item->stock_type== 'in' ? 'Stock In' : 'Stock Out' : '' }}</small>
                        <br>
                        <small>{{ $item->created_at->format('d M, Y') }}</small>
                    </td>



                    <td class="text-start">
                        <span class="text--primary"> @lang('Total') : </span> <span>{{ number_format($item->total_amount) }} {{ gs('cur_sym') }}</span>
                        <br>
                        <span class="text--primary"> @lang('Received') : </span> <span> {{number_format( $item->recieved_amount) }} {{ gs('cur_sym') }}</span>
                        <br>
                        <span class="text--primary"> @lang('Remaining') : </span> <span> {{ number_format($item->due_amount) }} {{ gs('cur_sym') }}</span>
                    </td>
                    <td class="text-start">
                        @foreach ($item->stockInOuts as $product)
                        <span class="text--primary"> {{ $product->product?->name }} : {{ $product->quantity }}</span>
                        <br>
                        @endforeach

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
        <h4 class="p-3">Stock Transfered</h4>
        <div class="table-responsive--md table-responsive">
            <table class="table table--light table-bordered style--two bg--white">
                <thead>
                    <tr>
                        <th>@lang('User') | @lang('Warehouse')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Products')</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($clientStocktransfersSent as $item)
                    <tr>
                        <td>
                            <span class="text--primary">{{ $item->toUser?->name }}</span>
                            <br>
                            <span>{{ $item->toWarehouse?->name }}</span>
                        </td>
                        <td>
                            {{ $item->created_at->format('d M, Y') }}
                        </td>
                        <td>

                            @foreach ($item->stockTransferDetails as $product)
                            <span class="text--primary"> {{ $product->product?->name }} : {{ $product->quantity }}</span>
                            <br>
                            @endforeach
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
    <div class="col-md-12">
        <h4 class="p-3">Stock Received</h4>
        <div class="table-responsive--md table-responsive">
            <table class="table table--light table-bordered style--two bg--white">
                <thead>
                    <tr>
                        <th>@lang('User') | @lang('Warehouse')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Products')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientStocktransfersReceived as $item)
                    <tr>
                        <td>
                            <span class="text--primary">{{ $item->fromUser?->name }}</span>
                            <br>
                            <span>{{ $item->fromWarehouse?->name }}</span>
                        </td>
                        <td>
                            {{ $item->created_at->format('d M, Y') }}
                        </td>
                        <td>

                            @foreach ($item->stockTransferDetails as $product)
                            <span class="text--primary"> {{ $product->product?->name }} : {{ $product->quantity }}</span>
                            <br>
                            @endforeach
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