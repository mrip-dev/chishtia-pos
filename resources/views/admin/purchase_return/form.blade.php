@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-3">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form action=" {{ $route }}" method="POST">
                        @csrf
                        <div class="row mb-3">

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Invoice No:')</label>
                                    <input class="form-control" type="text" value="{{ @$purchase->invoice_no }}" required disabled>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Supplier')</label>
                                    <input class="form-control" type="text" value="{{ $purchase->supplier->name }}" required disabled>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Date')</label>
                                    <input class="form-control timepicker" name="return_date" type="text" value="{{ old('return_date', @$purchaseReturn->return_date) }}" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Warehouse')</label>
                                    <input class="form-control" type="text" value="{{ $purchase->warehouse->name }}" required disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="table-responsive">
                                <table class="productTable table border">
                                    <thead class="border bg--dark">
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Purchase Qty')</th>
                                            <th>@lang('Stock Qty')</th>
                                            <th>@lang('Return Qty')<span class="text--danger">*</span></th>
                                            <th>@lang('Price')</th>
                                            <th>@lang('Total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $grandTotal = 0;
                                        @endphp

                                        @foreach ($detailsData as $return)
                                            <tr>
                                                <td>{{ $return->product->name }}</td>
                                                <td>
                                                    @php
                                                        $purchaseQuantity = $purchase->purchaseDetails->where('product_id', $return->product_id)->first()->quantity;
                                                    @endphp

                                                    <span class="purchase-qty">{{ $purchaseQuantity }}</span> {{ $return->product->unit->name }}
                                                </td>

                                                <td>
                                                    @php
                                                        $quantity = $return->product->productStock->where('warehouse_id', $purchase->warehouse_id)->first()->quantity;
                                                    @endphp

                                                    <span class="stock-qty">{{ $quantity }}</span> {{ $return->product->unit->name }}
                                                </td>

                                                <td class="text-start" width="20%">
                                                    <input name="products[{{ $loop->index }}][product_id]" type="hidden" value="{{ $return->product->id }}" required>
                                                    <input name="products[{{ $loop->index }}][price]" type="hidden" value="{{ $return->price }}" required>

                                                    <div class="input-group">
                                                        <input class="form-control quantity" name="products[{{ $loop->index }}][quantity]" type="number" value="{{ $edit ? $return->quantity : 0 }}" required>

                                                        <span class="input-group-text">{{ $return->product->unit->name }}</span>
                                                    </div>

                                                    <span class="error-message text--danger"></span>
                                                </td>

                                                <td>
                                                    <span class="purchase-price">{{ showAmount($return->price) }}</span>
                                                </td>

                                                @php
                                                    $totalPrice = $edit ? $return->quantity * $return->price : 0;
                                                    $grandTotal += $totalPrice;
                                                @endphp

                                                <td>
                                                    <span class="total-price">{{ showAmount($totalPrice) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if (!blank($detailsData))
                                            <tr>
                                                <td class="text-end fw-bold" colspan="100%">
                                                    @lang('Total Price'): <span class="grand-total">{{ showAmount($grandTotal) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Return Note')</label>
                                    <textarea class="form-control" name="note">{{ old('note', @$purchaseReturn->note) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label> @lang('Discount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                @php $discount = $edit ? showAmount(@$purchaseReturn->discount_amount) : 0; @endphp
                                                <input class="form-control" name="discount" type="number" value="{{ old('discount', $discount) }}" step="any">
                                            </div>
                                            <span class="error-message text--danger"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>@lang('Receivable Amount')</label>
                                            <div class="input-group">
                                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                @php $receivableAmount = $edit ? getAmount(@$purchaseReturn->receivable_amount) : 0; @endphp
                                                <input class="form-control receivable_amount" type="number" value="{{ old('receivable_amount', $receivableAmount) }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($edit)
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Received Amount')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                    <input class="form-control" name="received_amount" type="number" value="{{ old('received_amount', getAmount(@$purchaseReturn->received_amount)) }}" disabled>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>@lang('Due Amount')</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                                    <input class="form-control due_amount" type="number" value="{{ old('due_amount', getAmount(@$purchaseReturn->due_amount)) }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45 submit-btn" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.purchase.return.index') }}" />
@endpush

@push('style')
    <style>
        .table td {
            padding: 10px 15px !important;
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

@push('style')
    <style>
        .table td {
            white-space: unset;
        }
    </style>
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



            let isEdit = {{ $edit }};

            $("[name=discount]").on('input', function() {
                let grandTotal = $('.grand-total').text() * 1;

                if (this.value < 0) {
                    this.value = '';
                    $(this).parent().siblings('.error-message').text(`Discount amount must not be less than 0`);
                    error = false;
                } else if (this.value > grandTotal) {
                    $(this).parent().siblings('.error-message').text(`Discount amount must not be greater than Total Price`);
                    error = true;
                } else {
                    $(this).parent().siblings('.error-message').empty();
                    error = false;
                }
                calculateGrandTotal();
            });

            let error = false;

            $(".productTable").on('input', '.quantity', function() {
                let purchaseQty = $(this).parents('tr').find('.purchase-qty').text();
                let qty = parseFloat($(this).val());
                let stockQty = $(this).parents('tr').find('.stock-qty').text();

                if (qty <= purchaseQty && qty <= stockQty) {
                    $(this).parent().siblings('.error-message').empty();
                    error = false;
                } else if ($(this).val() == '') {
                    $(this).parent().siblings('.error-message').text('This field cannot be left empty.');
                    error = true;
                } else {
                    $(this).parent().siblings('.error-message').text('Must not be greater than Purchase/Stock Qty');
                    error = true;
                }

                if (isEdit && qty < 1) {
                    $(this).parent().siblings('.error-message').text('Return Qty must be greater than 0');
                    error = true;
                }

                calculateProductData($(this));
            });

            function manageSubmitButton() {
                if (error) {
                    $('.submit-btn').attr('disabled', 'disabled');
                } else {
                    $('.submit-btn').removeAttr('disabled');
                }
            }

            function calculateProductData($this) {
                let qty = $this.val() || 0;
                let purchasesReturnPrice = $this.parents('tr').find('.purchase-price').text();
                purchasesReturnPrice = extractAmount(purchasePrice);
                let total = qty * purchasesReturnPrice;
                var curSym = `{{ gs('cur_sym') }}`;
                $this.parents('tr').find('.total-price').text(curSym + total.toFixed(2))
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                var curSym = `{{ gs('cur_sym') }}`;
                let total = 0;
                $('.total-price').each(function(index, element) {
                    total = total + parseFloat(extractAmount($(element).text()));
                });

                let discount = $("[name=discount]").val() * 1;
                $('.grand-total').text(curSym + total.toFixed(2))

                let receivableAmount = total - discount;
                $(".receivable_amount").val(receivableAmount);
                let receivedAmount = Number($('[name=received_amount]').val());
                $('.due_amount').val(receivableAmount - receivedAmount);
                manageSubmitButton();
            }

            function extractAmount(value) {
                let stringValue = String(value);
                let cleanedValue = stringValue.replace(/,/g, '').replace(/[\$\s]|USD/g, '');
                return parseFloat(cleanedValue);
            }


        })(jQuery);
    </script>
@endpush
