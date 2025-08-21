@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg--transparent">
                <div class="card-body p-0 ">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--dark style--two bg--white">
                            <thead>
                                <tr>
                                    <th>@lang('Invoice No.') | @lang('Date')</th>
                                    <th>@lang('Supplier') | @lang('Mobile')</th>
                                    <th>@lang('Total Amount') | @lang('Warehouse')</th>
                                    <th>@lang('Discount') | @lang('Payable') </th>
                                    <th>@lang('Paid') | @lang('Due')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                    <tr>
                                        <td>
                                            @if ($purchase->return_status == 1)
                                                <small><i class="fa fa-circle text--danger" title="@lang('Returned')" aria-hidden="true"></i></small>
                                            @endif
                                            <span class="fw-bold">
                                                {{ $purchase->invoice_no }}
                                            </span>
                                            <br>
                                            <small>{{ showDateTime($purchase->purchase_date, 'd M, Y') }}</small>
                                        </td>

                                        <td>
                                            <span class="text--primary fw-bold"> {{ $purchase->supplier?->name }}</span>
                                            <br>
                                            +{{ $purchase->supplier?->mobile }}
                                        </td>

                                        <td>
                                            <span class="fw-bold">{{ showAmount($purchase->total_price) }}</span>
                                            <br>
                                            {{ $purchase->warehouse->name }}
                                        </td>
                                        <td>
                                            {{ showAmount($purchase->discount_amount) }}
                                            <br>
                                            <span class="fw-bold">{{ showAmount($purchase->payable_amount) }}</span>
                                        </td>
                                        <td>
                                            {{ showAmount($purchase->paid_amount) }}

                                            <br>

                                            @if ($purchase->due_amount < 0)
                                                <span class="text--danger fw-bold" title="@lang('Receivable from Supplier')">
                                                    - {{ showAmount(abs($purchase->due_amount)) }}
                                                </span>
                                            @else
                                                <span class="fw-bold" title="@lang('Payable to Supplier')">
                                                    {{ showAmount($purchase->due_amount) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                @permit('admin.purchase.edit')
                                                    <a class="btn btn-sm btn-outline--primary ms-1 editBtn"
                                                        href="{{ route('admin.purchase.edit', $purchase->id) }}">
                                                        <i class="la la-pen"></i> @lang('Edit')
                                                    </a>
                                                @endpermit
                                                <button class="btn btn-sm btn-outline--info ms-1 dropdown-toggle" data-bs-toggle="dropdown" type="button"
                                                    aria-expanded="false">
                                                    <i class="la la-ellipsis-v"></i>@lang('More')
                                                </button>

                                                <div class="dropdown-menu">
                                                    @permit('admin.supplier.payment.store')
                                                        <a class="dropdown-item paymentModalBtn" data-supplier="{{ $purchase->supplier?->name }}"
                                                            data-invoice="{{ $purchase->invoice_no }}" data-id="{{ $purchase->id }}"
                                                            data-due_amount="{{ $purchase->due_amount }}" href="javascript:void(0)">
                                                            @if ($purchase->due_amount < 0)
                                                                <i class="la la-hand-holding-usd"></i>
                                                                @lang('Receive Payment')
                                                            @elseif($purchase->due_amount > 0)
                                                                <i class="la la-money-bill-wave"></i>
                                                                @lang('Give Payment')
                                                            @endif
                                                        </a>
                                                    @endpermit

                                                    @permit('admin.purchase.return.items')
                                                        @if ($purchase->return_status == 0 && $purchase->due_amount > 0)
                                                            <li>
                                                                <a class="dropdown-item editBtn"
                                                                    href="{{ route('admin.purchase.return.items', $purchase->id) }}">
                                                                    <i class="la la-undo"></i> @lang('Return Purchase')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endpermit
                                                    @permit('admin.purchase.return.edit')
                                                        @if ($purchase->return_status)
                                                            <li>
                                                                <a class="dropdown-item editBtn"
                                                                    href="{{ route('admin.purchase.return.edit', $purchase->purchaseReturn->id) }}">
                                                                    <i class="la la-undo"></i> @lang('View Return Details')
                                                                </a>
                                                            </li>
                                                        @endif
                                                    @endpermit
                                                    @permit('admin.purchase.invoice.pdf')
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.purchase.invoice.pdf', $purchase->id) }}">
                                                                <i class="la la-download"></i> @lang('Download Details')
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
                @if ($purchases->hasPages())
                    <div class="card-footer py-4">
                        @php echo  paginateLinks($purchases) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <!-- Start Payment Modal  -->
    <div class="modal fade" id="paymentModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Left Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Invoice No.')</label>
                                    <input class="form-control invoice-no" type="text" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="amountType"></label>
                                    <div class="input-group">
                                        <button class="input-group-text" type="button">{{ gs('cur_sym') }}</button>
                                        <input class="form-control payable_amount" type="text" readonly>
                                    </div>
                                </div>

                                <div class="form-group mb-3" id="bankNameField" style="display:none;">
                                    <label for="bank_id">@lang('Bank Name')</label>
                                    <select name="bank_id" id="bank_id" class="form-control">
                                        <option value="" disabled selected>@lang('Select Bank')</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Right Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Supplier')</label>
                                    <input class="form-control supplier-name" type="text" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label>@lang('Payment Method')</label>
                                    <select name="payment_method" class="form-control" id="paymentMethodSelect" required>
                                        <option value="" disabled selected>@lang('Select Payment Method')</option>
                                        <option value="cash">@lang('Cash')</option>
                                        <option value="bank">@lang('Bank')</option>
                                        <option value="both">@lang('Both')</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3" id="receivedAmountField" style="display:none;">
                                    <label>@lang('Received Amount Bank')</label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                        <input type="number" step="any" name="received_amount_bank" class="form-control">
                                    </div>
                                </div>

                                <div class="form-group mb-3"  id="receivedAmount">
                                    <label class="payingReceiving"></label>
                                    <div class="input-group">
                                        <button class="input-group-text" type="button">{{ gs('cur_sym') }}</button>
                                        <input class="form-control" name="amount" type="number" step="any" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100 permit" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Start Payment Modal  -->
    <!-- IMPORT MODAL -->
    <div class="modal fade" id="importModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Product')</h4>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>
                <form id="importForm" method="post" action="{{ route('admin.product.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-warning p-3" role="alert">
                                <p>
                                    - @lang('Format your CSV the same way as the sample file below.') <br>
                                    - @lang('The number of columns in your CSV should be the same as the example below.')<br>
                                    - @lang('Valid fields Tip: make sure name of fields must be following: name, category, sku, brand, unit,alert_quantity and note.')<br>
                                    - @lang("Required field's (name, category, sku, brand, unit,alert_quantity), Unique Field's (name, sku) column cell must not be empty.")<br>
                                    - @lang('When an error occurs download the error file and correct the incorrect cells and import that file again through format.')<br>
                                    - @lang('Recommendation for importing huge data, you have to increase your server execution time and memory limit.')<br>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Select File')</label>
                            <input class="form-control" name="file" type="file" accept=".csv" required>
                            <div class="mt-1">
                                <small class="d-block">
                                    @lang('Supported files:') <b class="fw-bold">@lang('csv')</b>
                                </small>
                                <small>
                                    @lang('Download sample template file from here')
                                    <a class="text--primary" href="{{ asset('assets/files/sample/product.csv') }}" title="@lang('Download csv file')"
                                        download>
                                        <b>@lang('csv')</b>
                                    </a>

                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="Submit">@lang('Import')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    @permit('admin.purchase.new')
        <a class="btn btn-outline--primary h-45" href="{{ route('admin.purchase.new') }}">
            <i class="la la-plus"></i>@lang('Add New')
        </a>
    @endpermit

    @php
        $params = request()->all();
    @endphp
    @permit(['admin.purchase.pdf', 'admin.purchase.csv'])
        <div class="btn-group">
            <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                @lang('Action')
            </button>
            <ul class="dropdown-menu">
                @permit('admin.purchase.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.purchase.pdf', $params) }}"><i class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                @endpermit
                @permit('admin.purchase.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.purchase.csv', $params) }}"><i class="la la-download"></i>@lang('Download CSV')</a>
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
                let payingReceivingLabel = modal.find('.payingReceiving')


                if (parseFloat(data.due_amount).toFixed(2) > 0) {
                    amountTypeLabel.text(`@lang('Payable Amount')`);
                    payingReceivingLabel.text(`@lang('Paying Amount')`);
                } else {
                    amountTypeLabel.text(`@lang('Receivable Amount')`);
                    payingReceivingLabel.text(`@lang('Receiving Amount')`);
                }
                modal.find('.payable_amount').val(`${due}`);
                modal.find('.supplier-name').val(`${data.supplier}`);
                modal.find('.invoice-no').val(`${data.invoice}`);
                let form = modal.find('form')[0];
                form.action = `{{ route('admin.supplier.payment.store', '') }}/${data.id}`
                modal.modal('show');
            });

            //Import-Modal
            $(".importBtn").on('click', function(e) {
                let importModal = $("#importModal");
                importModal.modal('show');
            });

        })(jQuery);

    </script>
    <script>
        $(document).ready(function () {
            // Hide all fields initially
            $('#receivedAmount').hide();
            $('#receivedAmountField').hide();
            $('#bankNameField').hide();

            $('#paymentMethodSelect').on('change', function () {
                let method = $(this).val();

                if (method === 'cash') {
                    $('#receivedAmount').show();
                    $('#receivedAmountField').hide();
                    $('#bankNameField').hide();

                    $('input[name="received_amount_bank"]').val('');

                } else if (method === 'bank') {
                    $('#receivedAmount').hide();
                    $('#receivedAmountField').show();
                    $('#bankNameField').show();

                    $('input[name="amount"]').val('');

                } else if (method === 'both') {
                    $('#receivedAmount').show();
                    $('#receivedAmountField').show();
                    $('#bankNameField').show();

                } else {
                    // Hide everything if none selected
                    $('#receivedAmount').hide();
                    $('#receivedAmountField').hide();
                    $('#bankNameField').hide();

                    $('input[name="amount"]').val('');
                    $('input[name="received_amount_bank"]').val('');
                }
            });
        });
    </script>


@endpush
