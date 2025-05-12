@if (request()->warehouse)
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two custom-data-table table">
                            <thead>
                                <tr>
                                    <th>@lang('S.N.')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('SKU')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Brand')</th>
                                    <th>@lang('Stock')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocksByWarehouse as $stock)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $stock->product->name }}</td>
                                        <td>{{ $stock->product->sku }}</td>
                                        <td>{{ $stock->product->category->name }} </td>
                                        <td>{{ $stock->product->brand->name }} </td>
                                        <td>{{ $stock->quantity . ' ' . $stock->product->unit->name }} </td>
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
            </div><!-- card end -->
        </div>
    </div>
@endif
