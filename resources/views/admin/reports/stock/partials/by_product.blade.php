@if (request()->product)
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--dark style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Warehouse')</th>
                                <th>@lang('Current Stock')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocksByProduct as $stock)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="fw-bold">{{ $stock->warehouse->name }}</span></td>
                                <td>@php
                                    $unitName = strtolower($stock->product->unit->name);
                                    $stockWeight = $stock->net_weight ?? 0;
                                    @endphp
                                    @if($unitName == 'kg')

                                    <span class="fw-bold">QTY: {{ $stock->quantity }}</span>
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