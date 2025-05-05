<div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins w-100">
            @if(!$isCreating)
            @if (!$showDetails)
            <!-- Date: Start -->
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

            <!-- Date: End -->
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

            <!-- Search Input -->
            <div class="input-group w-50">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by Title/Warehouse/Vendor"
                    wire:model.live="searchTerm">
            </div>
            <!-- Clear All Button -->
            @if($searchTerm || $startDate || $endDate)
            <button class="btn btn-outline--primary" wire:click="clearFilters">
                <i class="fas fa-times me-1"></i> Clear All
            </button>
            @endif
            @else
            @if($showDetails && $selectedStock)
            <!-- Date: Start -->
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="startDateDetails"
                    placeholder="Start Date">
            </div>

            <!-- Date: End -->
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input
                    type="date"
                    class="form-control custom-date-input"
                    wire:model.live="endDateDetails"
                    placeholder="End Date">
            </div>

            <!-- Search Input -->
            <div class="input-group w-50">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by Product Name"
                    wire:model.live="searchTermDetails">
            </div>
            <!-- Clear All Button -->
            @if($searchTermDetails || $startDateDetails || $endDateDetails)
            <button class="btn btn-outline--primary" wire:click="clearFiltersDetails">
                <i class="fas fa-times me-1"></i> Clear All
            </button>
            @endif
            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.purchase.pdf')
                    <li wire:click="stockPDF" style="cursor: pointer;">
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
            @endif


            @endif
            @endif
            @if (!$showDetails)
            <!-- Add New/Close Button -->
            <button type="button" wire:click.prevent="createStock" class="btn btn-sm btn-outline--primary m-2">
                @if(!$isCreating)
                <i class="las la-plus"></i>
                @else
                <i class="las la-times"></i>
                @endif
                {{ $isCreating ? __('Close') : __('Add New') }}
            </button>
            @endif

        </div>
    </div>