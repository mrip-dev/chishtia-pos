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
                                <!-- <th>@lang('SKU')</th> -->
                                <th>@lang('Variant')</th>
                                <th>@lang('Brand')</th>
                                <th>@lang('Stock')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocksByWarehouse as $stock)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ getProductTitle($stock->product->id) }}</td>
                                <!-- <td>{{ $stock->product->sku }}</td> -->
                                <td>{{ $stock->product->category->name }} </td>
                                <td>{{ $stock->product?->brand?->name }} </td>
                                <td>
                                    @php
                                    $unitName = strtolower($stock->product->unit->name);
                                    $stockWeight = $stock->net_weight ?? 0;
                                    @endphp
                                    @if($unitName == 'kg')

                                    <span class="fw-bold">QTY:  {{ $stock->quantity }}</span>
                                    <br>
                                    <small>Weight: {{ $stockWeight . ' ' . $stock->product->unit->name }}</small>
                                    @else
                                    {{ $stock->quantity . ' ' . $stock->product->unit->name }}
                                    @endif
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
        </div><!-- card end -->
    </div>
</div>
@endif