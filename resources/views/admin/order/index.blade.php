@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order No.')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('Total Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Discount')</th>
                                    <th>@lang('Receivable')</th>
                                    <th>@lang('Paid')</th>
                                    <th>@lang('Due')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $sale->invoice_no }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($sale->sale_date, 'd M, Y') }}
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ __(@$sale->customer_name) }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($sale->total_price) }}</span>
                                        </td>

                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'warning',
                                                    'confirmed' => 'info',
                                                    'processing' => 'primary',
                                                    'shipped' => 'dark',
                                                    'delivered' => 'success',
                                                    'cancelled' => 'danger'
                                                ];
                                                $class = $statusClass[$sale->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge--{{ $class }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span>{{ gs('cur_sym') }}{{ showAmount($sale->discount_amount) }}</span>
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ gs('cur_sym') }}{{ showAmount($sale->receivable_amount) }}</span>
                                        </td>

                                        <td>
                                            <span class="text--success">{{ gs('cur_sym') }}{{ showAmount($sale->received_amount) }}</span>
                                        </td>

                                        <td>
                                            @if($sale->due_amount > 0)
                                                <span class="text--danger fw-bold">{{ gs('cur_sym') }}{{ showAmount($sale->due_amount) }}</span>
                                            @else
                                                <span class="text--success">{{ gs('cur_sym') }}0.00</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="button-group">
                                                <a class="btn btn-sm btn-outline--primary"
                                                   href="{{ route('admin.order.edit', $sale->id) }}"
                                                   title="@lang('Edit')">
                                                    <i class="la la-pen"></i> @lang('Edit')
                                                </a>

                                                <a class="btn btn-sm btn-outline--info"
                                                   href="{{ route('admin.order.invoice', $sale->id) }}"
                                                   title="@lang('Download Invoice')">
                                                    <i class="la la-download"></i> @lang('Invoice')
                                                </a>

                                                {{-- MAKE PAYMENT Button --}}
                                                @if($sale->due_amount > 0)
                                                    <button class="btn btn-sm btn-outline--success paymentBtn"
                                                            data-id="{{ $sale->id }}"
                                                            data-due_amount="{{ showAmount($sale->due_amount) }}"
                                                            title="@lang('Record Payment')">
                                                        <i class="las la-money-bill-wave"></i> @lang('Payment')
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-outline--dark statusBtn"
                                                        data-id="{{ $sale->id }}"
                                                        data-status="{{ $sale->status }}"
                                                        title="@lang('Update Status')">
                                                    <i class="la la-sync"></i> @lang('Status')
                                                </button>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($sales) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Status Update Modal --}}
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Order Status')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" id="statusForm">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Order Status')</label>
                            <select class="form-control" name="status" required>
                                <option value="pending">@lang('Pending')</option>
                                <option value="confirmed">@lang('Confirmed')</option>
                                <option value="processing">@lang('Processing')</option>
                                <option value="shipped">@lang('Shipped')</option>
                                <option value="delivered">@lang('Delivered')</option>
                                <option value="cancelled">@lang('Cancelled')</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Make Payment Modal --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Record New Payment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST" id="paymentForm">
                    @csrf
                    <div class="modal-body">
                        <p class="text--info">@lang('Order Due Amount'): **<span class="due-amount-text"></span>**</p>

                        <div class="form-group">
                            <label>@lang('Payment Amount')</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                <input type="number" step="any" name="payment_amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Payment Method')</label>
                            <select class="form-control" name="payment_method" id="paymentMethod" required>
                                <option value="">@lang('Select Method')</option>
                                @foreach($paymentMethods as $key => $value)
                                    <option value="{{ $key }}">{{ __($value) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group bank-field d-none">
                            <label>@lang('Select Bank')</label>
                            <select class="form-control" name="bank_id">
                                <option value="">@lang('Select One')</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}">{{ __($bank->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>@lang('Transaction ID / Note') (@lang('Optional'))</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="@lang('e.g. Bank Ref No., Mobile Txn ID')">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Record Payment')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by order no., customer name/mobile" />

    @if($pdfButton)
        <a class="btn btn-sm btn-outline--info" href="{{ $routePDF }}" target="_blank">
            <i class="las la-file-pdf"></i> @lang('PDF')
        </a>
        <a class="btn btn-sm btn-outline--success" href="{{ $routeCSV }}">
            <i class="las la-file-csv"></i> @lang('CSV')
        </a>
    @endif

    <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.order.create') }}">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            // --- Status Update Modal Logic ---
            $('.statusBtn').on('click', function() {
                let modal = $('#statusModal');
                let id = $(this).data('id');
                let currentStatus = $(this).data('status');

                modal.find('[name=status]').val(currentStatus);

                let action = "{{ route('admin.order.update.status', ':id') }}";
                action = action.replace(':id', id);

                $('#statusForm').attr('action', action);
                modal.modal('show');
            });

            // --- Payment Modal Logic ---
            $('.paymentBtn').on('click', function() {
                let modal = $('#paymentModal');
                let id = $(this).data('id');
                let dueAmount = $(this).data('due_amount');

                // Reset form fields
                $('#paymentForm')[0].reset();
                $('.bank-field').addClass('d-none');

                modal.find('.due-amount-text').text("{{ gs('cur_sym') }}" + dueAmount);
                modal.find('[name=payment_amount]').attr({
                    'max': dueAmount, // Set max attribute to prevent overpayment
                    'value': dueAmount // Suggest full payment by default
                });

                let action = "{{ route('admin.order.make_payment', ':id') }}";
                action = action.replace(':id', id);

                $('#paymentForm').attr('action', action);
                modal.modal('show');
            });

            // Toggle Bank Field
            $('#paymentMethod').on('change', function() {
                if ($(this).val() === 'bank') {
                    $('.bank-field').removeClass('d-none').find('[name=bank_id]').attr('required', true);
                } else {
                    $('.bank-field').addClass('d-none').find('[name=bank_id]').attr('required', false);
                }
            });

        })(jQuery);
    </script>
@endpush