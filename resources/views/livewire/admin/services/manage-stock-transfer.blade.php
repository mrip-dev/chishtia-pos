<div>
    <div class="d-flex mb-30 flex-wrap justify-content-end align-items-center">
        <div class="d-flex justify-content-end align-items-center gap-3 w-100">

            {{-- Date: Start --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="start_date"
                    placeholder="Start Date"
                >
            </div>

            {{-- Date: End --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="end_date"
                    placeholder="End Date"
                >
            </div>

            {{-- Search Input --}}
            <div class="input-group w-25">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by From/To User"
                    wire:model.live="searchTerm"
                >
            </div>

            {{-- Clear All Button --}}
            @if($searchTerm || $start_date || $end_date)
                <button class="btn btn-outline--primary" wire:click="clearFilters">
                    <i class="fas fa-times me-1"></i> Clear All
                </button>
            @endif

            {{-- Add New Button --}}
            <button type="button" wire:click.prevent="createStock" class="btn btn-sm btn-outline--primary m-2">
                <i class="las la-plus"></i> {{ $isCreating ? __('Close') : __('Add New') }}
            </button>

        </div>
    </div>


    @if (!$isCreating && !$showDetails)
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg--transparent">
                    <div class="card-body p-0 ">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('From') | @lang('User')</th>
                                        <th>@lang('From') | @lang('Warehouse')</th>
                                        <th>@lang('To') | @lang('User')</th>
                                        <th>@lang('To') | @lang('Warehouse')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $item)
                                    <tr>

                                        <td>
                                            <span class="fw-bold">{{ $item->fromUser?->name }}</span>
                                        </td>
                                        <td>
                                            {{ $item->fromWarehouse?->name }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->toUser?->name }}</span>
                                        </td>
                                        <td> {{ $item->toWarehouse?->name }}
                                        </td>
                                        <td>
                                            {{ $item->created_at->format('d M, Y') }}

                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <button wire:click="viewDetails({{ $item->id }})" class="btn btn-sm btn-outline--info ms-1 " type="button"
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
    @else
    @if($showDetails && $selectedStock)
    <div class="card mt-4">

        <div class="card-header">
            <div class="d-flex justify-content-end">
                <button wire:click="$set('showDetails', false)" class="btn btn-sm btn-secondary"><i class="las la-times"></i> Close</button>
            </div>
            <div class="justify-content-between align-items-start">
                <div class="row">
                    <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                        <p class="mb-1"><strong>From :</strong> {{ $selectedStock->fromUser?->name }}</p>
                        <p class="mb-1"><strong>Warehouse:</strong> {{ $selectedStock->fromWarehouse?->name }}</p>

                    </div>
                    <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                        <p class="mb-1"><strong>To:</strong> {{ $selectedStock->toUser?->name }}</p>
                        <p class="mb-1"><strong>Warehouse:</strong> {{ $selectedStock->toWarehouse?->name }}</p>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4 col-xl-4">
                        <h3 class="mb-1 "><strong>Total :</strong> {{ number_format($this->stockTotalAmount(),2) }}</h3>

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
                        <th>Unit Price</th>
                        <th>Total Amount</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($selectedStock->stockTransferDetails as $entry)
                    <tr>
                        <td>{{ $entry->product?->name }}</td>
                        <td>{{ $entry->quantity }}</td>
                        <td>{{ $entry->unit_price }}</td>
                        <td>{{ $entry->total_amount }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="row gy-3">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="saveStock">
                        <div class="row mb-3">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group" id="Users-wrapper">
                                    <label class="form-label">@lang('From User')</label>
                                    <select class="select2 form-control" wire:model.live="from_user_id" required>
                                        <option value="" selected>@lang('Select One')</option>
                                        @foreach ($users as $index => $user)
                                        <option value="{{ $index }}" @selected($index==@$item->from_user_id)>
                                            {{ $user['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group" id="Users-wrapper">
                                    <label class="form-label">@lang('To User')</label>
                                    <select class="select2 form-control" wire:model.live="to_user_id" required>
                                        <option value="" selected>@lang('Select One')</option>
                                        @foreach ($users as $index => $user)
                                        <option value="{{ $index }}" @selected($index==@$item->to_user_id)>
                                            {{ $user['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('From Warehouse')</label>
                                    <select class="form-control select2" wire:model.live="from_warehouse_id" data-minimum-results-for-search="-1" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$item->from_warehouse_id)>
                                            {{ __($warehouse->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('To Warehouse')</label>
                                    <select class="form-control select2" wire:model.live="to_warehouse_id" data-minimum-results-for-search="-1" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$item->to_warehouse_id)>
                                            {{ __($warehouse->name) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        @foreach ($stockItems as $index => $item)
                        <div class="card shadow-sm mt-1">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Product')</label>
                                            <select class="form-control select2" wire:model="stockItems.{{ $index }}.product_id" data-minimum-results-for-search="-1" required>
                                                <option value="">@lang('Select One')</option>
                                                @foreach ($products as $product)
                                                <option value="{{ $product->id }}" @selected($product->id == @$item->product_id)>
                                                    {{ __($product->name) }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Quantity')</label>
                                            <input type="number" class="form-control" min="0" wire:model.live="stockItems.{{ $index }}.quantity" placeholder="@lang('Quantity')" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Unit Price')</label>
                                            <input type="number" class="form-control" min="0" wire:model.live="stockItems.{{ $index }}.unit_price" placeholder="@lang('Unit Price')" required>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label">@lang('Amount')</label>
                                            <input type="text" class="form-control" wire:model.live="stockItems.{{ $index }}.total_amount" readonly placeholder="@lang('Total Amount')">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-3 mt-3 mx-4">

                                    <h5>Grand Total : {{ number_format($this->recalculateTotalAmount(),2) }}</h5>

                                </div>
                                <div class="d-flex justify-content-end mt-2">
                                    @if ($loop->last)
                                    <button type="button" wire:click="addItem" class="btn btn--primary">Add More</button>
                                    @else
                                    <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach



                        {{-- Submit --}}
                        <div class="mt-4">
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <button class="btn btn--primary" type="submit">@lang('Save')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif





</div>