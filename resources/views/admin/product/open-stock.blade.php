@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product.store-stock') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">

                        <div class="col-md-12 col-sm-12">
                            <div class="row">

                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>@lang('Product')</label>
                                        <select class="form-control select2" name="product_id" required onchange="checkUnit(this)">
                                            <option value="" selected disabled>@lang('Select One')</option>
                                            @foreach ($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-unit="{{ strtolower($product->unit->name) }}"
                                                @selected($product->id == @$product->product_id)>
                                                {{ __($product->name) }} ({{ __($product->category?->name) }})
                                            </option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <fieldset class="card p-3 mb-3 border">
                                    <legend class="form-label mb-2">@lang('Product Stock')</legend>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Warehouse')</label>
                                                <select class="form-control select2" name="warehouse_id" data-minimum-results-for-search="-1" required>
                                                    <!-- <option value="" selected disabled>@lang('Select One')</option> -->
                                                    @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$purchase->warehouse_id)>
                                                        {{ __($warehouse->name) }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>@lang('Product Quantity')</label>
                                                <input class="form-control" name="stock_quantity" type="number" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="net_weight" style="display: none;">
                                            <div class="form-group">
                                                <label>@lang('Net Weight')</label>
                                                <input class="form-control" name="net_weight" type="number"
                                                    placeholder="@lang('Net Weight')" step="any" min="0">
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>

                            </div>
                        </div>
                    </div>
                    @permit('admin.product.store')
                    <div class="form-group">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function checkUnit(select) {
        const selectedOption = select.options[select.selectedIndex];
        const unit = selectedOption.getAttribute('data-unit');

        if (unit === 'kg') {
            document.getElementById('net_weight').style.display = 'block';
        } else {
            document.getElementById('net_weight').style.display = 'none';
        }
    }

    // On page load (in case of edit)
    document.addEventListener("DOMContentLoaded", function () {
        const productSelect = document.querySelector('select[name="product_id"]');
        if (productSelect) {
            checkUnit(productSelect);
        }
    });
</script>


@endsection

@push('breadcrumb-plugins')
<x-back route="{{ route('admin.product.index') }}" />
@endpush