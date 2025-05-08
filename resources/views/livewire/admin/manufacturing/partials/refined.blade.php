<div>

    <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h5 class="card-title mb-0">{{ $flow->tracking_id }}</h5>
            <div class="d-flex justify-content-end">
                <button type="button" wire:click="cancel" class="btn btn--dark me-2">@lang('Cancel')</button>
                <button type="button" wire:click="saveFlowRefined" class="btn btn--primary">@lang('Submit')</button>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    <div class="card-body p-2 ">
                        <div class="d-flex justify-content-between bg--primary p-2 text-light rounded align-items-center mb-3 flex-wrap">
                            <h5 class="card-title text-light mb-0">@lang('Refined Items')</h5>
                        </div>
                        <div class="table-responsive--md table-responsive">
                            <div class="row">
                                @forelse($refinedItems as $index => $item)
                                <div class="col-md-12 mb-4">
                                    <div class="card border shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-end">
                                                @if($refinedItems[$index]['status'] == 'refined' )
                                                <!-- //// Add tot stock  -->
                                                <button type="button" wire:click="confirmAddToStock({{ $index }})" class="btn btn--primary">@lang('Add to stock')</button>
                                                @endif
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-2">
                                                        <label>@lang('Product')</label>
                                                        <x-select2
                                                            id="product-select-{{ $index }}-select"
                                                            dataArray="productOptions"
                                                            wire:model="refinedItems.{{ $index }}.product_id"
                                                            placeholder="Select a product"
                                                            />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-2">
                                                        <label>@lang('Quantity')</label>
                                                        <input type="number" class="form-control" min="0" wire:model.live="refinedItems.{{ $index }}.quantity" placeholder="@lang('Quantity')" required>
                                                        @error("refinedItems.{$index}.quantity")
                                                        <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                @if($loop->first)
                                                <button type="button" wire:click="addRefinedItem" class="btn btn--primary">+</button>
                                                @elseif($loop->last)
                                                <button type="button" wire:click="removeRefinedItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                <button type="button" wire:click="addRefinedItem" class="btn btn--primary mx-1">+</button>
                                                @else
                                                <button type="button" wire:click="removeRefinedItem({{ $index }})" class="btn btn-danger"><i class="las la-times"></i></button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center text-muted">{{ __($emptyMessage) }}</div>
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