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
                        <div class="d-flex justify-content-between align-items-center mb-3 p-2 flex-wrap">
                            <h5 class="card-title mb-0">@lang('Expenses')</h5>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <div class="expense-list">
                                <div class="expense-header d-none d-md-flex bg--primary p-2 text-light rounded p-2 border-bottom fw-bold">
                                    <div class="col-md-4">@lang('Expense Type')</div>
                                    <div class="col-md-4">@lang('Amount')</div>
                                    <div class="col-md-4">@lang('Actions')</div>
                                </div>

                                @forelse($expenceItems as $index => $item)
                                <div class="expense-row d-flex flex-wrap align-items-start bg--white p-2 border-bottom">
                                    <div class="col-md-4 mb-2 p-1">
                                        <label class="d-md-none fw-bold">@lang('Expense Type')</label>
                                        <x-select3
                                            id="exp-select-{{ $index }}-exp"
                                            dataArray="expenses"
                                            wire:model="expenceItems.{{ $index }}.expense_type_id"
                                            placeholder="Choose"
                                            :allowAdd="true"
                                            />
                                        @error("expenceItems.{$index}.expense_type_id")
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-2 p-1">
                                        <label class="d-md-none fw-bold">@lang('Amount')</label>
                                        <div class="form-group">
                                            <input type="number" class="form-control" min="0"
                                                wire:model.live="expenceItems.{{ $index }}.amount"
                                                placeholder="@lang('Quantity')" required>
                                            @error("expenceItems.{$index}.amount")
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-1 d-flex align-items-center justify-content-md-end gap-2 mt-2 mt-md-0">
                                        @if($loop->first)
                                        <button type="button" wire:click="addExpenceItem" class="btn btn--primary">+</button>
                                        @elseif($loop->last)
                                        <button type="button" wire:click="removeExpenceItem({{ $index }})" class="btn btn-danger">
                                            <i class="las la-times"></i>
                                        </button>
                                        <button type="button" wire:click="addExpenceItem" class="btn btn--primary mx-1">+</button>
                                        @else
                                        <button type="button" wire:click="removeExpenceItem({{ $index }})" class="btn btn-danger">
                                            <i class="las la-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-muted text-center py-3">{{ __($emptyMessage) }}</div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                </div>
                <!-- card end -->
            </div>
            <div class="col-lg-6">
                <div class="card ">
                    <div class="card-body p-2 ">
                        <div class="d-flex justify-content-between p-2  align-items-center mb-3 flex-wrap">
                            <h5 class="card-title mb-0">@lang('Stock')</h5>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <div class="stock-list">
                                <div class="stock-header bg--primary p-2 text-light rounded d-none d-md-flex  p-2 border-bottom fw-bold">
                                    <div class="col-md-4">@lang('Product')</div>
                                    <div class="col-md-4">@lang('Quantity')</div>
                                    <div class="col-md-4">@lang('Actions')</div>
                                </div>

                                @forelse($stockItems as $index => $item)
                                <div class="stock-row d-flex flex-wrap align-items-start bg--white p-2 border-bottom">
                                    <div class="col-md-4 mb-2 p-1">
                                        <label class="d-md-none fw-bold">@lang('Product')</label>
                                        <div class="form-group">
                                            <x-select2
                                                id="product-select-{{ $index }}-select"
                                                dataArray="productOptions"
                                                wire:model="stockItems.{{ $index }}.product_id"
                                                placeholder="Select a product"

                                                />
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-2 p-1">
                                        <label class="d-md-none fw-bold">@lang('Quantity')</label>
                                        <div class="form-group">
                                            <input type="number" class="form-control" min="0"
                                                wire:model.live="stockItems.{{ $index }}.quantity"
                                                placeholder="@lang('Quantity')" required>
                                            @error("stockItems.{$index}.quantity")
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 p-1 d-flex align-items-center justify-content-md-end gap-2 mt-2 mt-md-0">
                                        @if($loop->first)
                                        <button type="button" wire:click="addStockItem" class="btn btn--primary">+</button>
                                        @elseif($loop->last)
                                        <button type="button" wire:click="removeStockItem({{ $index }})" class="btn btn-danger">
                                            <i class="las la-times"></i>
                                        </button>
                                        <button type="button" wire:click="addStockItem" class="btn btn--primary mx-1">+</button>
                                        @else
                                        <button type="button" wire:click="removeStockItem({{ $index }})" class="btn btn-danger">
                                            <i class="las la-times"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-muted text-center py-3">{{ __($emptyMessage) }}</div>
                                @endforelse
                            </div>

                        </div>
                    </div>

                </div>
                <!-- card end -->
            </div>

        </div>
    </div>
</div>