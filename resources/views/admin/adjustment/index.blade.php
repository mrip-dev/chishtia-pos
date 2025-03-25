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
                                    <th>@lang('Tracking No.')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Warehouse')</th>
                                    <th>@lang('Products')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjustments as $adjustment)
                                    <tr>
                                        <td>{{ $adjustments->firstItem() + $loop->index }}</td>
                                        <td>{{ $adjustment->tracking_no }}</td>
                                        <td>{{ showDateTime($adjustment->adjust_date, 'd M, Y') }}</td>
                                        <td>{{ $adjustment->warehouse->name }} </td>
                                        <td>{{ $adjustment->adjustmentDetails->count() }} </td>
                                        <td>
                                            <div class="button--group">
                                                @permit('admin.adjustment.edit')
                                                    <a class="btn btn-sm btn-outline--primary ms-1" href="{{ route('admin.adjustment.edit', $adjustment->id) }}"><i class="las la-pen"></i>
                                                        @lang('Edit')
                                                    </a>
                                                @endpermit
                                                @permit('admin.adjustment.details.pdf')
                                                    <a class="btn btn-sm  btn-outline--info" href="{{ route('admin.adjustment.details.pdf', $adjustment->id) }}">
                                                        <i class="fa fa-download"></i> @lang('Download')
                                                    </a>
                                                @endpermit
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
                @if ($adjustments->hasPages())
                    <div class="card-footer py-4">
                        @php echo  paginateLinks($adjustments) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' />
    @permit('admin.adjustment.create')
        <a class="btn btn-outline--primary" href="{{ route('admin.adjustment.create') }}"><i
               class="las la-plus"></i>@lang('Add New')</a>
    @endpermit
    @php
        $params = request()->all();
    @endphp
    @permit(['admin.adjustment.pdf', 'admin.adjustment.csv'])
        <div class="btn-group">
            <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                @lang('Action')
            </button>
            <ul class="dropdown-menu">
                @permit('admin.adjustment.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.adjustment.pdf', $params) }}"><i
                               class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                @endpermit
                @permit('admin.adjustment.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.adjustment.csv', $params) }}"><i
                               class="la la-download"></i>@lang('Download CSV')</a>
                    </li>
                @endpermit

            </ul>
        </div>
    @endpermit
@endpush
