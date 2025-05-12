@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Products')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                    <tr>
                                        <td>{{ $categories->firstItem() + $loop->index }}</td>
                                        <td>{{ __($category->name) }}</td>
                                        <td>{{ $category->products_count }}</td>
                                        <td>
                                            <div class="button--group">

                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-resource="{{ $category }}"
                                                    data-modal_title="@lang('Edit Category')">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @permit('admin.product.category.delete')
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger @if ($category->products_count) disabled @endif confirmationBtn"
                                                        data-question="@lang('Are you sure to delete this category?')"
                                                        data-action="{{ route('admin.product.category.delete', $category->id) }}">
                                                        <i class="la la-trash"></i>@lang('Delete')
                                                    </button>
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
                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        @php echo  paginateLinks($categories) @endphp
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <!--Add Modal -->
    <div id="cuModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><span class="type"></span> <span>@lang('Add Category')</span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.product.category.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    @permit('admin.product.category.store')
                        <div class="modal-footer">
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    @endpermit
                </form>
            </div>
        </div>
    </div>
    <!-- IMPORT MODAL -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Import Category')</h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times" aria-hidden="true"></i>
                    </button>
                </div>
                <form method="post" action="{{ route('admin.product.category.import') }}" id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="alert alert-warning p-3" role="alert">
                                <p>
                                    - @lang('Format your CSV the same way as the sample file below.') <br>
                                    - @lang('Valid fields Tip: make sure name of fields must be following: name')<br>
                                    - @lang("Required And Unique field's (name)")<br>
                                    - @lang('When an error occurs download the error file and correct the incorrect cells and import that file again through format.')<br>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="fw-bold">@lang('Select File')</label>
                            <input type="file" class="form-control" name="file" accept=".csv" required>
                            <div class="mt-1">
                                <small class="d-block">
                                    @lang('Supported files:') <b class="fw-bold">@lang('csv')</b>
                                </small>
                                <small>
                                    @lang('Download sample template file from here')
                                    <a href="{{ asset('assets/files/sample/category.csv') }}" title="@lang('Download csv file')" class="text--primary" download>
                                        <b>@lang('category.csv')</b>
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="Submit" class="btn btn--primary w-100 h-45">@lang('Import')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
    @permit('admin.product.category.store')
        <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn" data-modal_title="@lang('Add New Category')">
            <i class="las la-plus"></i>@lang('Add New')
        </button>
    @endpermit
    @permit('admin.product.category.import')
        <button type="button" class="btn btn-sm btn-outline--info importBtn">
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
