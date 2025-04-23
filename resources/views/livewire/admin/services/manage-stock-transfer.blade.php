<div>
    <div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
            <x-search-form dateSearch='yes' />

            <button type="button" wire:click.prevent="createStock" class="btn btn-sm btn-outline--primary m-2">
                <i class="las la-plus"></i>{{ $isCreating ? __('Close') : __('Add New') }}
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
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocks as $item)
                                    <tr>

                                        <td>
                                            <span class="fw-bold">{{ $item->supplier?->name }}</span>
                                        </td>
                                        <td>
                                            {{ $item->warehouse->name }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $item->supplier?->name }}</span>
                                        </td>
                                        <td> {{ $item->warehouse->name }}
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
            <strong>Title :</strong> {{ $selectedStock->title }}<br>
            <strong>Warehouse:</strong> {{ $selectedStock->warehouse->name }}
            <button wire:click="$set('showDetails', false)" class="btn btn-sm btn-secondary float-end">Close</button>
        </div>
        <div class="card-body">
            <table class="table table--light style--two bg--white">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>
                            @if ($stock_type === 'in')
                            Supplier
                            @elseif ($stock_type === 'out')
                            Customer
                            @endif
                        </th>
                        <th>Quantity</th>
                        <th>Driver</th>
                        <th>Vehicle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($selectedStock->stockInOuts as $entry)
                    <tr>
                        <td>{{ $entry->product?->name }}</td>
                        <td>
                            @if ($stock_type === 'in')
                            {{ $entry->supplier->name ?? '-' }}
                            @elseif ($stock_type === 'out')
                            {{ $entry->client->name ?? '-' }}
                            @endif
                        </td>
                        <td>{{ $entry->quantity }}</td>
                        <td>{{ $entry->driver_name }}</td>
                        <td>{{ $entry->vehicle_number }}</td>
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
                        @foreach ($stockItems as $index => $item)

                                <div class="row mb-3">

                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group" id="Users-wrapper">
                                            <label class="form-label">@lang('From User')</label>
                                            <select class="select2 form-control" wire:model="stockItems.{{ $index }}.from_user_id" required>
                                                <option value="" selected>@lang('Select One')</option>
                                                @foreach ($users as $index => $user)
                                                <option value="{{ $user['id'] }}">
                                                    {{ $user['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-sm-6">
                                        <div class="form-group" id="Users-wrapper">
                                            <label class="form-label">@lang('To User')</label>
                                            <select class="select2 form-control" wire:model="stockItems.{{ $index }}.to_user_id" required>
                                                <option value="" selected>@lang('Select One')</option>
                                                @foreach ($users as $index => $user)
                                                <option value="{{ $user['id'] }}">
                                                    {{ $user['name'] }}
                                                </option>
                                                @endforeach
                                            </select>
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