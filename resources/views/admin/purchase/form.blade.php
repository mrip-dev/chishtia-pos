@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-3">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form
                        action="@isset($purchase) {{ route('admin.purchase.update', @$purchase->id) }} @else {{ route('admin.purchase.store') }} @endisset"
                        method="POST">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Invoice No:')</label>
                                    <input class="form-control" name="invoice_no" type="text" value="{{ old('invoice_no', @$purchase->invoice_no) }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group" id="supplier-wrapper">
                                    <label class="form-label">@lang('Supplier')</label>
                                    <select class="select2 form-control" name="supplier_id" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" @selected($supplier->id == @$purchase->supplier_id)>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Date')</label>
                                    <input class="form-control timepicker" name="purchase_date" type="text"
                                        value="{{ old('purchase_date', @$purchase->purchase_date) }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Warehouse')</label>
                                    <select class="form-control select2" name="warehouse_id" data-minimum-results-for-search="-1" required>
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$purchase->warehouse_id)>
                                                {{ __($warehouse->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group products-container position-relative">
                                    <label> @lang('Product')<span class="text--danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="las la-search"></i></span>
                                            <input class="form-control" name="search" type="search" placeholder="@lang('Product Name or SKU')">
                                        </div>
                                    </div>
                                    <ul class="products">
                                        <!-- Product data will append here after search -->
                                    </ul>
                                    <span class="text--danger error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="table-responsive table-responsive--lg">
                                <table class="productTable table border">
                                    <thead class="border bg--dark">
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Quantity')<span class="text--danger">*</span></th>
                                            <th>@lang('Price')<span class="text--danger">*</span></th>
                                            <th>@lang('Total')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @isset($purchase)
                                            @foreach ($purchase->purchaseDetails as $item)
                                                <tr class="product-row" data-product_id="{{ $item->product->id }}">

                                                    <td class="fw-bold">
                                                        <input class="form-control" type="text" value="{{ $item->product->name }}" readonly required>
                                                        <input name="products[{{ $loop->index }}][product_id]" type="hidden"
                                                            value="{{ $item->product->id }}" />
                                                    </td>

                                                    <td>
                                                        <div class="input-group">
                                                            <input class="form-control quantity" name="products[{{ $loop->index }}][quantity]"
                                                                data-id="{{ $item->product->id }}" type="number" value="{{ $item->quantity }}" required>

                                                            <span class="input-group-text">{{ $item->product->unit->name }}</span>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                            <input class="form-control purchase-price" name="products[{{ $loop->index }}][price]"
                                                                data-id="{{ $item->product->id }}" type="number" value="{{ $item->price }}" step="any"
                                                                required>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                            <input class="form-control total" type="number" value="{{ $item->price * $item->quantity }}"
                                                                readonly>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <button class="btn btn-outline--danger disabled h-45" type="button">
                                                            <i class="la la-trash"></i> @lang('Remove')
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endisset
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Note')</label>
                                    <textarea class="form-control" name="note">{{ old('note', @$purchase->note) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label> @lang('Total Price')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input class="form-control total_price" type="number" value="{{ @$purchase->payable_amount }}" required
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label> @lang('Discount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input class="form-control" name="discount" type="number"
                                                    value="{{ old('discount', getAmount(@$purchase->discount_amount)) }}" step="any">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Payable Amount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                <input class="form-control payable_amount" type="number"
                                                    value="{{ getAmount(@$purchase->payable_amount) }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    @isset($purchase)
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Paid Amount')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                    <input class="form-control" name="paid_amount" type="number"
                                                        value="{{ getAmount(@$purchase->paid_amount) }}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Due Amount')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                    <input class="form-control due_amount" type="number" value="{{ getAmount(@$purchase->due_amount) }}"
                                                        disabled>
                                                </div>
                                            </div>
                                        </div>
                                    @endisset

                                </div>
                            </div>
                        </div>

                        @if (isset($purchase) && $purchase->return_status == 1)
                            <div class="alert alert-danger p-3 d-flex flex-column" role="alert">
                                <h4 class="text--danger text-center"> <i class="fa fa-exclamation-circle" aria-hidden="true"></i> @lang('Some products has returned from this purchase')
                                </h4>

                                <p class="text--danger text-center">
                                    @lang('You can\'t edit a purchase after return any quantity from it.')
                                    <a class="text--primary text-decoration-underline"
                                        href="{{ route('admin.purchase.return.edit', $purchase->purchaseReturn->id) }}">@lang('View Return Details')</a>
                                </p>
                            </div>
                        @endif

                        <button class="btn btn--primary w-100 h-45" type="submit"
                            @if (isset($purchase) && $purchase->return_status == 1) disabled @endif>@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.purchase.index') }}" />
@endpush

@push('style')
    <style>
        .table td {
            padding: 15px 6px !important;
        }

        .empty-notification img {
            width: 30px;
            padding-top: 12px;
        }
    </style>
@endpush
@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.timepicker').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                timePicker: false,
                timePicker24Hour: false,
                autoUpdateInput: true,
                timePickerSeconds: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('.timepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            $('.timepicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });


            let productArray = [];
            @if (@$purchase)
                productArray = @json($purchase->purchaseDetails->pluck('product_id')->toArray());
            @endif

            calculateGrandTotal();

            $("[name='search']").on('input', function() {
                $('.products-container .error-message').empty();
                let data = {};
                data.search = $(this).val();
                if (data.search) {
                    $.ajax({
                        url: "{{ route('admin.purchase.product.search') }}",
                        method: 'GET',
                        data: data,
                        success: function(response) {
                            var products = '';
                            $(".products").html('');
                            if (response.data.length) {
                                $.each(response.data, function(index, product) {
                                    products +=
                                        `<li class="products__item productItem pt-2" data-id="${product.id}" data-name="${product.name}" data-unit="${product.unit.name}">
                                            <h6>${product.name}</h6>
                                            <small>SKU: ${product.sku}</small>
                                        </li>`;
                                });
                            } else {
                                $('.products-container .error-message').html(`
                                <div class="empty-notification text-center">
                                    <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                    <p class="mt-3">@lang('No product found')</p>
                                </div>
                            `);
                            }
                            $(".products").html(products);
                        },
                    });
                } else {
                    $(".products").empty();
                    $('.products-container .error-message').empty();
                }
            });


            $('body').on('click', '.productItem', function() {
                let index = $('.product-row ').length + 1;

                $(".no-data").addClass('d-none');
                var data = $(this).data();
                let productId = data.id;


                if (!productArray.includes(productId)) {
                    productArray.push(productId);

                    $(".productTable tbody").append(`
                        <tr data-product_id="${data.id}" class="product-row">
                            <td data-label="@lang('Name')" class="fw-bold">
                                <input type="text" class="form-control" value="${data.name}" readonly required>
                                <input type="hidden" class="product_id" name="products[${index}][product_id]" value="${data.id}"/>
                            </td>

                            <td data-label="@lang('Quantity')">
                                <div class="input-group">
                                    <input type="number" name="products[${index}][quantity]" value="1"  class="form-control quantity" data-id="${data.id}" required>
                                    <span class="input-group-text">${data.unit}</span>

                                </div>
                            </td>

                            <td data-label="@lang('Price')">
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input type="number" name="products[${index}][price]" class="form-control purchase-price" data-id="${data.id}" value="0" step="any" required>
                                </div>
                            </td>

                            <td data-label="@lang('Total')">
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input type="number" value="0" class="form-control total" step="any" readonly>
                                </div>
                            </td>

                            <td data-label="@lang('Action')">
                                <button type="button" class="btn btn-outline--danger removeBtn h-45" >
                                    <i class="la la-trash"></i> @lang('Remove')
                                </button>
                            </td>
                        </tr>
                    `);

                } else {
                    let quantityField = $(`[data-product_id=${productId}]`).find('.quantity');
                    quantityField.val(Number(quantityField.val()) + 1);
                    let target = $(`[data-product_id=${productId}]`).find('.quantity');
                    calculateProductData(target)
                }

                $(".products").empty();
                $("[name='search']").val("");
            });


            $("[name=discount]").on('input', function() {
                calculateGrandTotal();
            });

            // Remove the product row from table
            $(".productTable").on('click', '.removeBtn', function() {
                let productId = Number($(this).parents('tr').find('.product_id').val());
                let indexToRemove = productArray.indexOf(productId);
                productArray.splice(indexToRemove, 1)
                $(this).parents('tr').remove();
                calculateGrandTotal();
            });

            $(".productTable").on('input', '.quantity', function() {
                calculateProductData($(this));
            });

            $(".productTable").on('input', '.purchase-price', function() {
                calculateProductData($(this));
            });

            function calculateProductData($this) {
                let qty = $this.parents('tr').find('.quantity').val();
                let purchasesPrice = $this.parents('tr').find('.purchase-price').val();

                let total = qty * purchasesPrice;
                $this.parents('tr').find('.total').val(total.toFixed(2))
                calculateGrandTotal();
            }

            $('[name=paid_amount]').on('input', function() {
                calculateGrandTotal();
                let payableAmount = Number($('.payable_amount').val());
                let payingAmount = Number($(this).val());

                if (payableAmount < payingAmount) {
                    $(this).val(payableAmount);
                    $(".due_amount").val(0);
                }
            });

            function calculateGrandTotal() {
                var total = 0;
                $(".productTable .total").each(function(index, element) {
                    total = total + parseFloat($(element).val());
                });

                var discount = parseFloat($("[name=discount]").val() * 1);
                $(".total_price").val(total.toFixed(2));
                var payableAmount = total - discount;

                $(".payable_amount").val(payableAmount.toFixed(2));
                let payingAmount = $('[name=paid_amount]').val();
                $(".due_amount").val(payableAmount - payingAmount);
            }



        })(jQuery);
    </script>
@endpush
