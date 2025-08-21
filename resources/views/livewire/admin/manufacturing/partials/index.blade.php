<div>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg--transparent">
                    <div class="card-body p-0 ">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--dark style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('Flow Id')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Actions')</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($flows as $item)
                                    <tr>
                                        <td>{{ $item->tracking_id }}</td>
                                        <td>{{ $item->date }}</td>
                                        <td>
                                            <a wire:click.prevent="viewRefined({{ $item->id }})"
                                                class="btn btn-sm btn--primary ms-1">
                                                <i class="la la-chart"></i> @lang('Refined Items')
                                            </a>
                                            <a wire:click.prevent="viewRaw({{ $item->id }})"
                                                class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-chart"></i> @lang('Details')
                                            </a>
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

                </div>
                <!-- card end -->
            </div>
        </div>
    </div>
</div>