<div>
    <div class="d-flex justify-content-end align-items-center gap-3 mb-30">
        <div class="d-flex align-items-center gap-3">

            {{-- Date: Start --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="startDate"
                    placeholder="Start Date">
            </div>

            {{-- Date: End --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="endDate"
                    placeholder="End Date">
            </div>

            {{-- Search Input --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by From/To User"
                    wire:model.live="searchTerm">
            </div>

            {{-- Clear All --}}
            @if($searchTerm || $startDate || $endDate)
            <button class="btn btn-outline--primary" wire:click="clearFilters">
                <i class="fas fa-times me-1"></i> Clear All
            </button>
            @endif


            {{-- CSV AND PDF --}}
            @if($showDetails && $selectedStock)
            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.purchase.pdf')
                    <li wire:click="stockPDF"  style="cursor: pointer;">
                        <a class="dropdown-item" ><i
                                class="la la-download"  ></i>@lang('Download PDF')</a>
                    </li>
                    @endpermit


                </ul>
            </div>
            @endif
        </div>
    </div>



    @if($showDetails && $selectedStock)
    <div class="card mt-4">

        <div class="card-header">
            <div class="d-flex justify-content-end">
                <button wire:click="$set('showDetails', false)" class="btn btn-sm btn-secondary"><i class="las la-times"></i> Close</button>
            </div>
            <div class="justify-content-between align-items-start">
                <div class="row">


                    <div class="justify-content-between align-items-start">
                        <div class="row">
                            <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                                <p class="mb-1"><strong>User :</strong> {{ $selectedUser?->name }}</p>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table table--light style--two bg--white">
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
                                        <th>@lang('Product Count')</th>
                                        <th>@lang('Total Quantity')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $item->user->name }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->product_count }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button wire:click="viewDetails({{ $item->user_id }},'{{ $item->user_model }}')" class="btn btn--sm btn-outline--info ms-1 " type="button"
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
                    {{-- @if ($stocks->hasPages())
                        <div class="card-footer py-4">
                            @php echo  paginateLinks($stocks) @endphp
                        </div>
                    @endif --}}
                </div>
                <!-- card end -->
            </div>
        </div>
    </div>
    @endif
</div>