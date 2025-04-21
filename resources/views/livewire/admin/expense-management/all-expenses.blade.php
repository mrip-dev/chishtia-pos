<div>
    <!-- Add New Expense Button -->
    <button class="btn btn-success mb-2" onclick="openAddModal()">Add New Expense</button>
     <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two custom-data-table table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Reason')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Note')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expenses->firstItem() + $loop->index }}</td>
                                        <td>{{ $expense->expenseType->name }}</td>
                                        <td>{{ showDateTime($expense->date_of_expense, 'd M, Y') }}</td>
                                        <td>{{ showAmount($expense->amount) }}</td>
                                        <td>{{ strLimit($expense->note, 35) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary"
                                            wire:click="edit({{ $expense->id }})">
                                            <i class="la la-pencil"></i> @lang('Edit')
                                        </button>
                                        <button class="btn btn-sm btn-outline--danger"
                                            wire:click="confirmDelete({{ $expense->id }})">
                                            <i class="la la-trash"></i> Delete
                                        </button>

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
                @if ($expenses->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($expenses) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="cuModal" tabindex="-1" role="dialog" aria-labelledby="cuModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form wire:submit.prevent="store">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="cuModalLabel">Add New Expense</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Expense Type --}}
                <div class="form-group">
                    <label>@lang('Type')</label>
                    <select class="form-control" wire:model="expense_type_id" required>
                        <option value="">@lang('Select One')</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}">{{ __($item->name) }}</option>
                        @endforeach
                    </select>
                    @error('expense_type_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Date --}}
                <div class="form-group">
                    <label>@lang('Date of Expense')</label>
                    <input type="date" class="form-control" wire:model="date_of_expense">
                    @error('date_of_expense') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Bank --}}
                <div class="form-group">
                    <label>@lang('Bank Name')</label>
                    <select class="form-control" wire:model="bank_id">
                        <option value="">@lang('Select Bank')</option>
                        @foreach($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                    @error('bank_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Amount --}}
                <div class="form-group">
                    <label>@lang('Amount')</label>
                    <div class="input-group">
                        <button class="input-group-text">{{ gs('cur_sym') }}</button>
                        <input type="number" class="form-control" wire:model="amount" step="any" required>
                    </div>
                    @error('amount') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- Note --}}
                <div class="form-group">
                    <label>@lang('Note')</label>
                    <textarea class="form-control" wire:model="note" rows="5"></textarea>
                    @error('note') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn--primary h-45 w-100 permit" type="submit">@lang('Submit')</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Table or other content for the expenses -->
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
</div>

@push('script')
@if ($confirmingDelete)
<script>
    if (confirm("Are you sure to delete this expense type?")) {
        @this.call('delete');
    }
</script>
@endif
<script>
    window.addEventListener('open-modal', () => {
        $('#cuModal').modal('show');
    });

    window.addEventListener('close-modal', () => {
        $('#cuModal').modal('hide');
    });
</script>

@endpush
