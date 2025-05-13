@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card  mb-3">
                <div class="card-body d-flex justify-content-between flex-wrap gap-3 align-items-center">
                    <div class="d-flex gap-3">
                        <div class="d-flex flex-column flex-wrap">
                            <span> <i class="fa fa-circle text--success"></i> @lang('Receivable')</span>
                            <span> <i class="fa fa-circle text--danger"></i> @lang('Payable')</span>
                        </div>

                        <div class="d-flex flex-column flex-wrap">
                            <span class="text-end">
                                {{ showAmount($customer->totalReceivableAmount()) }}
                            </span>
                            <span class="text-end">
                                {{ showAmount($customer->totalPayableAmount()) }}
                            </span>
                        </div>
                    </div>

                    @php
                        $totalReceivable = $customer->totalReceivableAmount() - $customer->totalPayableAmount();
                    @endphp

                    @if ($totalReceivable != 0)
                        <button class="btn btn-outline--primary paymentBtn">
                            <i class="la la-money-bill-alt"></i> @lang('Clear Payment')
                        </button>
                    @endif
                </div>
            </div>

            <div class="card">

                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Invoice No.')</th>
                                    <th>@lang('Reason')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 0;
                                @endphp

                                @forelse($sales as $sale)
                                    @php
                                        if ($sale->due_amount == 0) {
                                            continue;
                                        }
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $sale->invoice_no }} </td>

                                        <td>@lang('Sale')</td>

                                        <td>
                                            @if ($sale->due_amount < 0)
                                                <span class="text--danger">{{ showAmount(abs($sale->due_amount)) }}</span>
                                            @else
                                                <span class="text--success">
                                                    {{ showAmount($sale->due_amount) }}</span>
                                            @endif
                                        </td>
                                    </tr>

                                    @if ($sale->saleReturn)
                                        @php
                                            if ($sale->saleReturn->due_amount == 0) {
                                                continue;
                                            }
                                        @endphp
                                        <tr>
                                            <td> {{ ++$i }} </td>
                                            <td>{{ $sale->invoice_no }} </td>
                                            <td>@lang('Sale Return')</td>
                                            <td>
                                                @if ($sale->saleReturn->due_amount < 0)
                                                    <span class="text--success">{{ showAmount(abs($sale->saleReturn->due_amount)) }}</span>
                                                @else
                                                    <span class="text--danger">
                                                        {{ showAmount($sale->saleReturn->due_amount) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if ($totalReceivable < 0)
                                <tr>
                                    <td class="fw-bold" colspan="3">
                                        @lang('Total Payable')
                                    </td>

                                    <td class="fw-bold">
                                        <span class="text--danger">{{ showAmount(abs($totalReceivable)) }}</span>
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="fw-bold" colspan="3">
                                        @lang('Total Receivable')
                                    </td>

                                    <td class="fw-bold">
                                        <span class="text--success">{{ showAmount(abs($totalReceivable)) }}</span>
                                    </td>
                                </tr>
                            @endif
                        </table><!-- table end -->
                    </div>
                </div>

            </div><!-- card end -->
        </div>
    </div>
    <!-- Start Payment Modal  -->
    <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span>@lang('Clear Full Payment with') {{ __($customer->name) }} </span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.customer.payment.clear', $customer->id) }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex border-0 justify-content-between flex-wrap">
                                <span>
                                    @lang('Receivable Amount')
                                </span>
                                {{ showAmount($customer->totalReceivableAmount()) }}
                            </li>

                            <li class="list-group-item d-flex justify-content-between flex-wrap">
                                <span>
                                    @lang('Payable Amount')
                                </span>
                                {{ showAmount($customer->totalPayableAmount()) }}
                            </li>

                            <li class="list-group-item d-flex justify-content-between flex-wrap fw-bold">
                                <span>

                                    @if ($totalReceivable < 0)
                                        @lang('Payable Amount')
                                    @else
                                        @lang('Receivable Amount')
                                    @endif
                                </span>
                                <span>
                                    {{ showAmount(abs($totalReceivable)) }}
                                </span>
                            </li>
                        </ul>

                        <div class="alert alert-warning p-2 mt-3 mb-0" role="alert">
                            <div class="d-flex align-items-center gap-2">
                                <div>
                                    <i class="la la-exclamation-circle la-3x"></i>
                                </div>
                                <em>
                                    @lang('If you click on "Clear Payment" button, all receivable and payable dues will be cleared. So be sure before click.')
                                </em>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Clear Payment')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Start Payment Modal  -->
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.customer.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.paymentBtn').on('click', function() {
                var modal = $('#paymentModal');
                modal.modal('show');
            });

        })(jQuery);
    </script>
@endpush
