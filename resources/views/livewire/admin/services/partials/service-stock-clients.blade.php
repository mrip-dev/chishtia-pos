<div>
    <div class="d-flex justify-content-end align-items-center gap-3 mb-30">
        <div class="d-flex align-items-center gap-3">

            {{-- CSV AND PDF --}}
            @if($showDetails && $selectedStock)

            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.purchase.pdf')
                    <li wire:click="clientReportPDF" style="cursor: pointer;">
                        <a class="dropdown-item"><i
                                class="la la-download"></i>@lang('Download PDF')
                            <span wire:loading wire:target="stockPDF">
                                <i class="spinner-border  spinner-border-sm  text--primary"></i>

                            </span>
                        </a>
                    </li>
                    @endpermit


                </ul>
            </div>
            @else
            {{-- Search Input --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by User"
                    wire:model.live="searchTerm">
            </div>

            {{-- Clear All --}}
            @if($searchTerm || $startDate || $endDate)
            <button class="btn btn-outline--primary" wire:click="clearFilters">
                <i class="fas fa-times me-1"></i> Clear All
            </button>
            @endif


            @endif
        </div>
    </div>

    @if($showDetails && $selectedStock)

    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-end">
                <button wire:click="closeDetails()" class="btn btn-sm btn-secondary"><i class="las la-times"></i> Close</button>
            </div>
            <div class="justify-content-between align-items-start">
                <div class="row">
                    <div class="justify-content-between align-items-start">
                        <div class="row">
                            <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                                <p class="mb-1"><strong>User :</strong> {{ $selectedUser?->name }}</p>
                                <p class="mb-1"><strong>Contact :</strong> {{ $selectedUser?->mobile }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="p-3">Stock In/Out</h4>
                    <table class="table table--dark table-bordered style--two bg--white">
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

                                    <span class="text--primary"> {{ $product->product?->name }} <span class="text-dark"> QTY: {{ $product->quantity }}</span> Weight: {{ $product->net_weight ?? 0 }} @if($product->product->unit->name && strtolower($product->product->unit->name)=='kg') {{ $product->product->unit->name}} @endif</span>
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
                        <table class="table table--dark table-bordered style--two bg--white">
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
                                        <span class="text--primary"> {{ $product->product?->name }} <span class="text-dark"> QTY: {{ $product->quantity }}</span> Weight: {{ $product->net_weight ?? 0 }} @if($product->product->unit->name && strtolower($product->product->unit->name)=='kg') {{ $product->product->unit->name}} @endif</span>
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
                        <table class="table table--dark table-bordered style--two bg--white">
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
                                        <span class="text--primary"> {{ $product->product?->name }} <span class="text-dark"> QTY: {{ $product->quantity }}</span> Weight: {{ $product->net_weight ?? 0 }} @if($product->product->unit->name && strtolower($product->product->unit->name)=='kg') {{ $product->product->unit->name}} @endif</span>
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
            <table class="table table--dark table-bordered style--two bg--white">
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
    </div>
    @else
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg--transparent">
                    <div class="card-body p-0 ">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--dark style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('User')</th>
                                        <th>@lang('User Type')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $item['name'] }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item['model'] }}</span>
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <button wire:click="viewClientReport({{ $item['id'] }},'{{ $item['model'] }}')" class="btn btn--sm btn-outline--info ms-1 " type="button"
                                                    aria-expanded="false">
                                                    @lang('Details')
                                                </button>
                                            </div>
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
                <!-- card end -->
            </div>
        </div>
    </div>
    @endif
</div>