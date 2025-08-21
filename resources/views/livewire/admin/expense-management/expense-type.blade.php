<div>
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"></h6>
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn mb-2"
            wire:click="expenseTypeModal"
            data-modal_title="@lang('Add New Expense Type')">
            <i class="la la-plus"></i> @lang('Add New')
        </button>
    </div>
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--dark style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($types as $type)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>
                                            <button wire:click="edit({{ $type->id }})" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </button>

                                            @permit('admin.expense.type.delete')
                                                <button wire:click="confirmDelete({{ $type->id }})"
                                                    class="btn btn-sm btn-outline-danger {{ $type->expenses_count ? 'disabled' : '' }}">
                                                    <i class="la la-trash"></i> @lang('Delete')
                                                </button>
                                            @endpermit
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($types->hasPages())
                    <div class="card-footer d-flex justify-content-center py-4">
                        {{ $types->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div wire:ignore.self class="modal fade" id="expenseTypeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form wire:submit.prevent="save" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Name')</label>
                        <input wire:model.defer="name" type="text" class="form-control" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Are you sure?</h5>
            </div>
            <div class="modal-body">
              Do you really want to delete this expense type? This action cannot be undone.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" wire:click="delete">Yes, Delete</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
          </div>
        </div>
      </div>

    {{-- Delete Confirmation --}}
    @if ($confirmingDelete)
        <script>
            if (confirm("Are you sure to delete this expense type?")) {
                @this.call('delete');
            }
        </script>
    @endif
    @push('script')
    <script>
        window.addEventListener('show-expense-type-modal', event => {
            $('#expenseTypeModal').modal('show');
        });

       // Modal Close
    window.addEventListener('close-modal', () => {
        $('#expenseTypeModal').modal('hide');
    });
    window.addEventListener('show-delete-modal', () => {
        // Show delete confirmation modal
        $('#deleteConfirmationModal').modal('show');
    });


    window.addEventListener('close-modal', () => {
        // Close all modals
        $('.modal').modal('hide');
    });
    </script>
    @endpush

</div>
