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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Address')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td> {{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->address }} </td>
                                        <td>
                                            @php
                                                echo $warehouse->statusBadge;
                                            @endphp
                                        </td>
                                        <td>

                                            <div class="button--group">
                                                @permit('admin.warehouse.store')
                                                    <button class="btn btn-sm btn-outline--primary editBtn cuModalBtn" data-resource="{{ $warehouse }}" data-modal_title="@lang('Edit warehouse')" type="button">
                                                        <i class="la la-pencil"></i>@lang('Edit')
                                                    </button>
                                                @endpermit
                                                @permit('admin.warehouse.status')
                                                    @if ($warehouse->status == Status::DISABLE)
                                                        <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-action="{{ route('admin.warehouse.status', $warehouse->id) }}" data-question="@lang('Are you sure to enable this warehouse')?" type="button">
                                                            <i class="la la-eye"></i> @lang('Enabled')
                                                        </button>
                                                    @else
                                                        <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.warehouse.status', $warehouse->id) }}" data-question="@lang('Are you sure to disable this warehouse')?" type="button">
                                                            <i class="la la-eye-slash"></i> @lang('Disabled')
                                                        </button>
                                                    @endif
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
                @if ($warehouses->hasPages())
                    <div class="card-footer d-flex justify-content-center py-4">
                        @php echo paginateLinks($warehouses) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div class="modal fade" id="importModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Warehouse')</h4>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>
                <form id="importForm" method="post" action="{{ route('admin.warehouse.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-warning p-3" role="alert">
                                <p>
                                    - @lang('Format your CSV the same way as the sample file below.') <br>
                                    - @lang('Valid fields Tip: make sure name of fields must be following: name, address')<br>
                                    - @lang('Required (name, address) , Unique (name)')<br>
                                    - @lang('When an error occurs download the error file and correct the incorrect cells and import that file again through format.')<br>
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
                                    <a class="text--primary" href="{{ asset('assets/files/sample/warehouse.csv') }}" title="@lang('Download csv file')" download>
                                        <b>@lang('warehouse.csv')</b>
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

    <!--Cu Modal -->
    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.warehouse.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input class="form-control" name="name" type="text" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label>@lang('Address')</label>
                            <input class="form-control" name="address" type="text" value="{{ old('address') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />

    @permit('admin.warehouse.store')
        <button class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Warehouse')" type="button">
            <i class="la la-plus"></i>@lang('Add New')
        </button>
    @endpermit
    @permit('admin.warehouse.import')
        <button class="btn btn-sm btn-outline--info importBtn" type="button">
            <i class="las la-cloud-upload-alt"></i>@lang('Import CSV')
        </button>
    @endpermit
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            $(".importBtn").on('click', function(e) {
                let importModal = $("#importModal");
                importModal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
