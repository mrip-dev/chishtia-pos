@extends('admin.layouts.app')
@section('panel')
@push('style')
<style>
    .permission-item {
        background: #fdfdfd;
        border: 1px solid #eaeaea;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        height: 100%;
    }

    .permission-item h6 {
        font-weight: 600;
        color: #333;
    }

    .permission-item .form-check-label {
        margin-left: 0.25rem;
    }

    .group-permissions .custom-control {
        margin-bottom: 0.3rem;
    }
</style>
@endpush

<form action="{{ route('admin.roles.save', @$role->id) }}" method="post">
    @csrf
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">@lang('Name')</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', @$role->name) }}">
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('Set Permissions')</h5>
                </div>
                <div class="card-body">
                    <div class="">

                        <div class="row gy-4">
                            @foreach ($permissionGroups as $key => $permissionGroup)
                            <div class="col-md-12 col-lg-12 rounded">
                                <div class="permission-item h-100">
                                    <div class="d-flex justify-content-between text--primary align-items-center mb-3">
                                        <h6 class="m-0 text--primary">{{ Str::replaceLast('Controller', '', $key) }}</h6>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input select-all-group" data-group="{{ $key }}" id="selectAll{{ $loop->index }}">
                                            <label class="form-check-label small" for="selectAll{{ $loop->index }}">@lang('Select All')</label>
                                        </div>
                                    </div>
                                    <div class="row group-permissions" data-group="{{ $key }}">
                                        @foreach ($permissionGroup as $permission)
                                        <div class="col-md-4 col-lg-4">
                                            <div class="custom-control custom-checkbox form-check-primary w-100">
                                                <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="customCheck{{ $permission->id }}">
                                                <label class="custom-control-label" for="customCheck{{ $permission->id }}">{{ $permission->name }}</label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
        </div>
    </div>
</form>
@push('script')
<script>
    (function ($) {
        "use strict";

        // Handle select all toggle
        $('.select-all-group').on('change', function () {
            let groupKey = $(this).data('group');
            let checked = $(this).is(':checked');
            $(`.group-permissions[data-group="${groupKey}"] .permission-checkbox`).prop('checked', checked);
        });

        // Sync select-all if all permissions in group are selected
        $('.permission-checkbox').on('change', function () {
            let groupWrap = $(this).closest('.group-permissions');
            let groupKey = groupWrap.data('group');
            let all = groupWrap.find('.permission-checkbox').length;
            let checked = groupWrap.find('.permission-checkbox:checked').length;
            $(`.select-all-group[data-group="${groupKey}"]`).prop('checked', all === checked);
        });

        // Pre-select permissions (when editing)
        @isset($permissions)
            let selected = @json($permissions);
            selected.forEach(id => {
                $('#customCheck' + id).prop('checked', true).trigger('change');
            });
        @endisset
    })(jQuery);
</script>
@endpush


@endsection

@push('style')
<style>
    .permission-item {
        background: #fafafa;
        border: 1px solid #f7f7f7;
        padding: 1rem;
    }
</style>
@endpush

@push('script')
@push('script')
<script>
    (function($) {
        "use strict";
        @isset($permissions)
        $('input[name="permissions[]"]').val(@json($permissions));
        @endif
    })(jQuery);
</script>
@endpush
@endpush



