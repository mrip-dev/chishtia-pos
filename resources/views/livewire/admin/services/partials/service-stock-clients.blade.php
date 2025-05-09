<div>
    <div class="d-flex justify-content-end align-items-center gap-3 mb-30">
        <div class="d-flex align-items-center gap-3">

            {{-- CSV AND PDF --}}
            @if($showDetails && $selectedStock)
            {{-- Search Input --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by Product"
                    wire:model.live="searchDetails">
            </div>

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
                    <table class="table table--light table-bordered style--two bg--white">
                        <thead>
                            <tr>
                                <th>@lang('Title') | @lang('CTN NO')</th>
                                <th>@lang('Warehouse') | @lang('Type')</th>
                                <th>@lang('Date')</th>
                                <th>@lang('Total Amount') | @lang('Received')</th>
                                <th>@lang('Remaining')</th>
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
                                    <span class="text--primary fw-bold"> {{ number_format($item->total_amount) }} {{ gs('cur_sym') }}</span>
                                    <br>
                                    <small>{{number_format( $item->recieved_amount) }} {{ gs('cur_sym') }}</small>

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
        </div>
    </div>
    @else
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg--transparent">
                    <div class="card-body p-0 ">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two bg--white">
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