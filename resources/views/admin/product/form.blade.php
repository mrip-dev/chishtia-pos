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
                                        <div class="form-group ">
                                            <label>@lang('Name')</label>
                                            <input class="form-control" name="name" type="text" value="{{ old('name', @$product->name) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label>@lang('Category')</label>
                                            <select class="form-control select2" name="category_id" required>
                                                <option value="" selected disabled>@lang('Select One')</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}" @selected($category->id == @$product->category_id)>
                                                        {{ __($category->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class=" col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Brand')</label>
                                            <select class="form-control select2" name="brand_id" >
                                                <option value="" selected disabled>@lang('Select One')</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}" @selected($brand->id == @$product->brand_id)>
                                                        {{ __($brand->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label>@lang('SKU')</label>
                                            <input class="form-control " name="sku" type="text" value="{{ old('sku', @$product->sku) }}" >
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Unit(UoM)')</label>
                                            <select class="form-control select2" name="unit_id" >
                                                <option value="" selected disabled>@lang('Select One')</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}" @selected($unit->id == @$product->unit_id)>
                                                        {{ __($unit->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>@lang('Alert Quantity')</label>
                                            <input class="form-control" name="alert_quantity" type="number"
                                                value="{{ old('alert_quantity', @$product->alert_quantity) }}" >
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Note')</label>
                                            <textarea class="form-control" name="note">{{ old('note', @$product->note) }}</textarea>
                                        </div>
                                    </div>
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
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.product.index') }}" />
@endpush
