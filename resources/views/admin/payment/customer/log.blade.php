@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Invoice No.')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Customer')</th>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('Reason')</th>
                                    <th>@lang('Amount')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentLogs as $log)
                                    <tr>
                                        <td>{{ $paymentLogs->firstItem() + $loop->index }}</td>
                                        <td class="fw-bold">
                                            @if ($log->sale_id)
                                                <span class="text--primary"> {{ @$log->sale->invoice_no }}</span>
                                            @else
                                                <span class="text--danger"> {{ @$log->saleReturn->sale->invoice_no }}</span>
                                            @endif
                                        </td>
                                        <td>{{ showDateTime($log->created_at, 'd M, Y') }}</td>
                                        <td>{{ $log->customer->name }} </td>
                                        <td>{{ $log->trx }}</td>
                                        <td>{{ ucwords(strtolower(keyToTitle($log->remark))) }}</td>
                                        <td class="fw-bold">{{ showAmount($log->amount) }}</td>
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
                @if ($paymentLogs->hasPages())
                    <div class="card-footer py-4">
                        @php echo  paginateLinks($paymentLogs) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <form action="" method="GET" class="d-flex flex-wrap justify-content-end gap-2">
        <div class="input-group w-auto">
            <select name="remark" class="form-control select2" data-minimum-results-for-search="-1">
                <option value="" selected>@lang('All')</option>
                @foreach ($remarks as $remark)
                    <option value="{{ $remark }}" @selected($remark == request()->remark)>
                        {{ ucwords(strtolower(keyToTitle($remark))) }}</option>
                @endforeach
            </select>
            <button class="btn btn--primary input-group-text"><i class="la la-search"></i></button>
        </div>
        <x-search-date-field />
        <x-search-key-field />
    </form>

    @php
        $params = request()->all();
    @endphp
    @permit(['admin.customer*'])
        <div class="btn-group">
            <button type="button" class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown"
                aria-expanded="false">
                @lang('Action')
            </button>
            <ul class="dropdown-menu">
               @permit('admin.report.payment.customer.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.report.payment.customer.pdf', $params) }}"><i
                                class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                @endpermit
               @permit('admin.report.payment.customer.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.report.payment.customer.csv', $params) }}"><i
                                class="la la-download"></i>@lang('Download CSV')</a>
                    </li>
                @endpermit
            </ul>
        </div>
    @endpermit
@endpush


@push('style')
    <style>
        .select2-container{
            min-width: 200px;
        }
    </style>
@endpush