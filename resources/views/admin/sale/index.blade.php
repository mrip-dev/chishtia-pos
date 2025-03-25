@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg--transparent">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two bg-white">
                            <thead>
                                <tr>
                                    <th>@lang('Invoice No.') | @lang('Date')</th>
                                    <th>@lang('Customer') | @lang('Mobile')</th>
                                    <th>@lang('Warehouse') | @lang('Total Amount')</th>
                                    <th>@lang('Discount') | @lang('Receivable')</th>
                                    <th>@lang('Received') | @lang('Due')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            @if ($sale->return_status == 1)
                                                <small><i class="fa fa-circle text--danger" title="@lang('Returned')"
                                                        aria-hidden="true"></i></small>
                                            @endif
                                            <span class="fw-bold"> {{ $sale->invoice_no }}</span>
                                            <br>
                                            <small>{{ showDateTime($sale->sale_date, 'd M, Y') }}</small>
                                        </td>

                                        <td>
                                            <span class="text--primary fw-bold"> {{ $sale->customer->name }}</span>
                                            <br>
                                            +{{ $sale->customer->mobile }}
                                        </td>

                                        <td>
                                            {{ $sale->warehouse->name }}
                                            <br>
                                            <span
                                                class="fw-bold">{{  showAmount($sale->total_price) }}</span>
                                        </td>

                                        <td>{{  showAmount($sale->discount_amount) }}
                                            <br>
                                            <span class="fw-bold">
                                                {{  showAmount($sale->receivable_amount) }}</span>
                                        </td>

                                        <td>
                                            {{  showAmount($sale->received_amount) }}
                                            <br>
                                            <span
                                                @if ($sale->due_amount < 0) class="text--danger" title="@lang('Give Payment To Customer')" @endif
                                                class="fw-bold">
                                                {{  showAmount($sale->due_amount) }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="button--group">

                                                <a href="{{ route('admin.sale.edit', $sale->id) }}"
                                                    class="btn btn-sm btn-outline--primary ms-1">
                                                    <i class="la la-pen"></i> @lang('Edit')
                                                </a>

                                                <button type="button"
                                                    class="btn btn-sm btn-outline--info ms-1 dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="la la-ellipsis-v"></i>@lang('More')
                                                </button>

                                                <div class="dropdown-menu">
                                                   @permit('admin.customer.payment.store')
                                                        <a href="javascript:void(0)" class="dropdown-item paymentModalBtn"
                                                            data-customer_id="{{ $sale->customer_id }}"
                                                            data-customer="{{ $sale->customer->name }}"
                                                            data-invoice="{{ $sale->invoice_no }}"
                                                            data-id="{{ $sale->id }}"
                                                            data-due_amount="{{ $sale->due_amount }}">

                                                            @if ($sale->due_amount > 0)
                                                                <i class="la la-hand-holding-usd"></i>
                                                                @lang('Receive Payment')
                                                            @elseif($sale->due_amount < 0)
                                                                <i class="la la-money-bill-wave"></i>
                                                                @lang('Give Payment')
                                                            @endif
                                                        </a>
                                                    @endpermit

                                                   @permit('admin.sale.return.items')
                                                        @if ($sale->return_status == 0 && $sale->due_amount > 0)
                                                            <li>
                                                                <a href="{{ route('admin.sale.return.items', $sale->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="la la-redo"></i> @lang('Return Sale')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endpermit
                                                   @permit('admin.sale.return.edit')
                                                        @if ($sale->return_status)
                                                            <li>
                                                                <a href="{{ route('admin.sale.return.edit', $sale->saleReturn->id) }}"
                                                                    class="dropdown-item editBtn">
                                                                    <i class="la la-undo"></i> @lang('View Return Details')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endpermit
                                                   @permit('admin.sale.invoice.pdf')
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('admin.sale.invoice.pdf', $sale->id) }}/?print=true">
                                                                <i class="la la-download"></i> @lang('Download Invoice')
                                                            </a>
                                                        </li>
                                                    @endpermit
                                                </div>
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
                @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($sales) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
    <!-- Start Receive Payment Modal  -->
    <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Invoice No.')</label>
                            <input type="text" class="form-control invoice-no" readonly>
                        </div>
                        <div class="form-group">
                            <label>@lang('Customer')</label>
                            <input type="text" class="form-control customer-name" readonly>
                        </div>
                        <div class="form-group">
                            <label class="amountType"></label>
                            <div class="input-group">
                                <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                <input type="text" class="form-control receivable_amount" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="payment-type"></label>
                            <div class="input-group">
                                <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                <input type="number" step="any" name="amount" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100 permit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Start Payment Modal  -->
@endsection

@push('style')
    <style>
        .table-responsive {
            min-height: 400px;
            background: transparent
        }

        .card {
            box-shadow: none;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' />
   @permit('admin.sale.create')
        <a href="{{ route('admin.sale.create') }}" class="btn btn-outline--primary h-45">
            <i class="la la-plus"></i>@lang('Add New')
        </a>
    @endpermit
    @php
        $params = request()->all();
    @endphp
    @permit(['admin.sale.pdf', 'admin.sale.csv'])
        <div class="btn-group">
            <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                @lang('Action')
            </button>
            <ul class="dropdown-menu">
                @permit('admin.sale.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sale.pdf', $params) }}"><i
                               class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                @endpermit
                @permit('admin.sale.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sale.csv', $params) }}"><i
                               class="la la-download"></i>@lang('Download CSV')</a>
                    </li>
                @endpermit
               
            </ul>
        </div>
    @endpermit
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.paymentModalBtn', function() {
                var modal = $('#paymentModal');

                let data = $(this).data();
                var due = parseFloat(Math.abs(data.due_amount)).toFixed(2);

                let amountTypeLabel = modal.find('.amountType')
                let payingReceivingLabel = modal.find('.payment-type')

                if (parseFloat(data.due_amount).toFixed(2) > 0) {
                    amountTypeLabel.text('Receivable Amount');
                    payingReceivingLabel.text('Receiving Amount');
                } else {
                    amountTypeLabel.text('Payable Amount');
                    payingReceivingLabel.text('Paying Amount');
                }

                modal.find('.invoice-no').val(`${data.invoice}`);
                modal.find('.customer-name').val(`${data.customer}`);
                modal.find('.receivable_amount').val(`${due}`);
                let form = modal.find('form')[0];
                form.action = `{{ route('admin.customer.payment.store', '') }}/${data.id}`

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
