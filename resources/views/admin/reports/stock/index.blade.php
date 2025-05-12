@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="">
                        <div class="row gy-4 justfi-conent-end align-items-end">
                            <div class="col-lg-4">
                                <label class="required">@lang('Filter By')</label>
                                <select class="form-control select2" name="type" data-minimum-results-for-search="-1">
                                    <option value="warehouse" @selected(request()->type == 'warehouse')>@lang('Warehouse')</option>
                                    <option value="product" @selected(request()->type == 'product')>@lang('Product')</option>
                                </select>
                            </div>

                            <div class="col-lg-4">
                                <div class="warehouse-field @if (request()->product) d-none @endif">
                                    <label class="required">@lang('Warehouse')</label>
                                    <select class="form-control select2" name="warehouse">
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected(request()->warehouse == $warehouse->id)>
                                                {{ __($warehouse->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="product-field position-relative @if (!request()->product) d-none @endif">
                                    <label class="required">@lang('Product')</label>
                                    <select class="form-control " id="product" name="product">
                                        @if (request()->product)
                                            <option value="{{ request()->product }}"> {{ $productName }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <button class="btn btn--primary h-45 w-100" type="submit">
                                    <i class="la la-filter"></i>@lang('Filter')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- card end -->
        </div>
    </div>
    @include('admin.reports.stock.partials.by_product')
    @include('admin.reports.stock.partials.by_warehouse')
@endsection
@push('breadcrumb-plugins')
    @php
        $params = request()->all();
    @endphp
    @if (!blank($params))
        @permit(['admin.report.stock.csv', 'admin.report.stock.pdf'])
            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.report.stock.pdf')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.report.stock.pdf', $params) }}"><i class="la la-download"></i>@lang('Download PDF')</a>
                        </li>
                    @endpermit
                    @permit('admin.report.stock.csv')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.report.stock.csv', $params) }}"><i class="la la-download"></i>@lang('Download CSV')</a>
                        </li>
                    @endpermit
                </ul>
            </div>
        @endpermit
    @endif
@endpush

@push('script')
    @push('script')
        <script>
            (function($) {
                "use strict";
                $('[name=type]').on('change', function() {

                    if ($(this).val() === 'warehouse') {
                        $('.warehouse-field').removeClass('d-none');
                        $('.product-field').addClass('d-none');
                        $('[name=product]').val('');
                    } else {
                        $('.warehouse-field').addClass('d-none');
                        $('.product-field').removeClass('d-none');
                        $('[name=warehouse]').val('');
                    }
                });

                $('#product').select2({
                    ajax: {
                        url: '{{ route('admin.product.list') }}',
                        type: "get",
                        dataType: 'json',
                        delay: 1000,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page, // Page number, page breaks
                            };
                        },
                        processResults: function(response, params) {
                            params.page = params.page || 1;
                            let data = response.products.data;
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: `${item.name} (${item.sku})`,
                                        id: item.id
                                    }
                                }),
                                pagination: {
                                    more: response.more
                                }
                            };
                        },
                        cache: false
                    },
                    dropdownParent: $(".product-field")
                }); //end here


            })(jQuery);
        </script>
    @endpush
@endpush
