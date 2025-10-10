@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product.store', @$product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 col-sm-6">
                            <div class="form-group">
                                <x-image-uploader class="w-100" type="product" image="{{ @$product->image }}" :required=false />
                            </div>
                        </div>

                        <div class="col-md-8 col-sm-12">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('Name')</label>
                                        <select class="form-control select2" name="name" required>
                                            <option value="">@lang('Select Product')</option>
                                            @foreach(gourmet_cola_products() as $name)
                                            <option value="{{ $name }}" {{ old('name', @$product->name) == $name ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>@lang('Variant')</label>
                                        <select class="form-control select2" name="category_id" required>
                                            <option value="" selected disabled>@lang('Select One')</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" @selected($category->id == @$product->category_id)>
                                                {{ __($category->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class=" col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('Brand')</label>
                                        <select class="form-control select2" name="brand_id">
                                            <!-- <option value="" selected disabled>@lang('Select One')</option> -->
                                            @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}" @selected($brand->id == @$product->brand_id)>
                                                {{ __($brand->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>@lang('SKU')</label>
                                        <input class="form-control " name="sku" type="text" value="{{ old('sku', @$product->sku) }}">
                                    </div>
                                </div> -->

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('Unit(UoM)')</label>
                                        <select class="form-control select2" onchange="selectUnit(this)" name="unit_id">
                                            <option value="" selected disabled>@lang('Select One')</option>
                                            @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" @selected($unit->id == @$product->unit_id)>
                                                {{ __($unit->name) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>@lang('Alert Quantity')</label>
                                        <input class="form-control" name="alert_quantity" type="number"
                                            value="{{ old('alert_quantity', @$product->alert_quantity) }}">
                                    </div>
                                </div>
                                
                                <div class="col-sm-6" id="net_weight" style="display: none;">
                                    <div class="form-group">
                                        <label>@lang('Net Weight')</label>
                                        <input class="form-control" name="net_weight" type="number"
                                            value="{{ old('net_weight', @$product->net_weight) }}">
                                    </div>
                                </div>
                               <div class="col-sm-6" id="price">
                                    <div class="form-group">
                                        <label>@lang('Price')</label>
                                        <input class="form-control" name="price" type="number"
                                            value="{{ old('price', @$product->price) }}">
                                    </div>
                                </div>
                                 <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>@lang('Note')</label>
                                        <textarea class="form-control" name="note">{{ old('note', @$product->note) }}</textarea>
                                    </div>
                                </div>

                                @if(!@$product->id)

                                <fieldset class="card p-3 mb-3 border d-none">
                                    <legend class="form-label mb-2">@lang('Product Stock')</legend>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="form-label">@lang('Warehouse')</label>
                                                <select class="form-control select2" name="warehouse_id" data-minimum-results-for-search="-1">
                                                    <option value="" selected disabled>@lang('Select One')</option>
                                                    @foreach ($warehouses as $warehouse)
                                                    <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$purchase->warehouse_id)>
                                                        {{ __($warehouse->name) }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label>@lang('Product Quantity')</label>
                                                <input class="form-control" name="stock_quantity" type="number" min="1">
                                            </div>
                                        </div>

                                    </div>
                                </fieldset>
                                @endif
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
    function selectUnit(selectElement) {
        const selectedText = selectElement.options[selectElement.selectedIndex].text.trim();

        if (selectedText === 'KG' || selectedText === 'kg' || selectedText === 'Kg') {
            // Do something when "KG" is selected
            document.getElementById('net_weight').style.display = 'block';
        } else {
            // Hide if not KG
            document.getElementById('net_weight').style.display = 'none';
        }
    }

    // Optional: Trigger on page load if editing product
    document.addEventListener("DOMContentLoaded", function() {
        const unitSelect = document.querySelector('select[name="unit_id"]');
        if (unitSelect) {
            selectUnit(unitSelect);
        }
    });
</script>

@endsection

@push('breadcrumb-plugins')
<x-back route="{{ route('admin.product.index') }}" />
@endpush