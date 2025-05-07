<div>
    <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h5 class="card-title mb-0">{{ $flow->tracking_id }}</h5>
            <div class="d-flex justify-content-end">
                <button type="button" wire:click="cancel" class="btn btn--dark me-2">@lang('Cancel')</button>
                <button type="button" wire:click="saveFlow" class="btn btn--primary">@lang('Submit')</button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card ">
                    <div class="card-body p-2">
                        <!-- //// Heading For Expenses -->
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <h5 class="card-title mb-0">@lang('Expenses')</h5>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('Expense Type')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Actions')</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expenceItems as $index => $item)
                                    <tr>
                                        <td>
                                            <select class="form-control select2" wire:model="expenceItems.{{ $index }}.expense_type_id" required>
                                                <option value="">@lang('Select One')</option>
                                                @foreach ($expenses as $exp)
                                                <option value="{{ $exp->id }}" @selected($exp->id == @$item->expense_type_id)>
                                                    {{ __($exp->name) }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('expenceItems.{{ $index }}.expense_type_id')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control" min="0" wire:model.live="expenceItems.{{ $index }}.amount" placeholder="@lang('Quantity')" required>
                                                @error('expenceItems.{{ $index }}.amount')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                @if($loop->first)
                                                <button type="button" wire:click="addExpenceItem" class="btn btn--primary">+</button>
                                                @elseif($loop->last)
                                                <button type="button" wire:click="removeExpenceItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                <button type="button" wire:click="addExpenceItem" class="btn btn--primary mx-1">+</button>
                                                @else
                                                <button type="button" wire:click="removeExpenceItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                @endif
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
            <div class="col-lg-6">
                <div class="card ">
                    <div class="card-body p-2 ">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                            <h5 class="card-title mb-0">@lang('Stock')</h5>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('Product')</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Actions')</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stockItems as $index => $item)
                                    <tr>

                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" wire:model.live="stockItems.{{ $index }}.product" placeholder="@lang('Product')" required>
                                                @error('stockItems.{{ $index }}.product')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="number" class="form-control" min="0" wire:model.live="stockItems.{{ $index }}.quantity" placeholder="@lang('Quantity')" required>
                                                @error('stockItems.{{ $index }}.quantity')
                                                <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end">
                                                @if($loop->first)
                                                <button type="button" wire:click="addStockItem" class="btn btn--primary">+</button>
                                                @elseif($loop->last)
                                                <button type="button" wire:click="removeStockItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                <button type="button" wire:click="addStockItem" class="btn btn--primary mx-1">+</button>
                                                @else
                                                <button type="button" wire:click="removeStockItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                @endif
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
</div>