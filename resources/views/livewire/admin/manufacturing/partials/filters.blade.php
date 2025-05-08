<div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins w-100">

        @if (!$showDetails)
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

        <div class="input-group w-auto">
            <span class="input-group-text bg--primary">
                <i class="fas fa-search text-white"></i>
            </span>
            <input
                type="text"
                class="form-control"
                placeholder="Search by Flow"
                wire:model.live="searchTerm">
        </div>
        {{-- Clear All Button --}}
        @if($searchTerm || $startDate || $endDate)
        <button class="btn btn-outline--primary" wire:click="clearFilters">
            <i class="fas fa-times me-1"></i> Clear All
        </button>
        @endif
        <button type="button" wire:click.prevent="confirmAddNew" class="btn btn-sm btn-outline--primary m-2">
            <i class="las la-plus"></i>
            {{ __('Add New') }}
        </button>
        @endif

    </div>
</div>