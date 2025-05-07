<div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins w-100">

        @if (!$showDetails)
        <button type="button" wire:click.prevent="confirmAddNew" class="btn btn-sm btn-outline--primary m-2">
            <i class="las la-plus"></i>
            {{ __('Add New') }}
        </button>

        @endif

    </div>
</div>