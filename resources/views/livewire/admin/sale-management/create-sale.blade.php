<div>
    <div class="card p-4 shadow">
    <form >
    <div class="row mb-3">
        <div class="col-xl-3 col-sm-6">
            <label>@lang('Invoice No.')</label>
            <input type="text" class="form-control" wire:model="invoice_no" readonly>
            @error('invoice_no') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-xl-3 col-sm-6">
            <label>@lang('Customer')</label>
            <select class="form-control" wire:model="customer_id">
                <option value="">@lang('Select One')</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">
                        {{ $customer->name }} +{{ $customer->mobile }}
                    </option>
                @endforeach
            </select>
            @error('customer_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-xl-3 col-sm-6">
            <label>@lang('Date')</label>
            <input type="date" class="form-control" wire:model="sale_date">
            @error('sale_date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-xl-3 col-sm-6">
            <label>@lang('Warehouse')</label>
            <select class="form-control" wire:model="warehouse_id">
                <option value="">@lang('Select One')</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
            @error('warehouse_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
    </div>

    {{-- Product Search --}}
    <div class="form-group">
        <label>@lang('Search Product')</label>
        <input type="text" class="form-control" wire:model="searchQuery" wire:change="getProducts" placeholder="@lang('Product Name or SKU')">
        @error('products') <small class="text-danger">{{ $message }}</small> @enderror

        @if($searchResults)
        <div class="card p-2 shadow">
            <ul class="list-group mt-2">
                @foreach ($searchResults as $product)
                    <li class="list-group-item" wire:click="addProduct({{ $product->id }})">
                        {{ $product->name }} ({{ $product->sku }})
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    {{-- Product Table --}}
    <div class="table-responsive my-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>@lang('Product')</th>
                    <th>@lang('Stock')</th>
                    <th>@lang('Quantity')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Total')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $product)
                    <tr>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['stock'] }}</td>
                        <td>
                            <input type="number" wire:model="products.{{ $index }}.quantity" class="form-control">
                            @error("products.$index.quantity") <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            <input type="text" wire:model="products.{{ $index }}.price" class="form-control">
                            @error("products.$index.price") <small class="text-danger">{{ $message }}</small> @enderror
                        </td>
                        <td>
                            {{ $product['quantity'] * $product['price'] }}
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger" wire:click="removeProduct({{ $index }})">
                                @lang('Remove')
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Note and Total --}}
    <div class="row">
        <div class="col-md-8">
            <label>@lang('Note')</label>
            <textarea class="form-control" wire:model="note"></textarea>
            @error('note') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-4">
            <label>@lang('Total Price')</label>
            <input type="text" class="form-control" value="{{ $this->getTotalPrice() }}" readonly>
        </div>
    </div>

    {{-- Submit --}}
    <div class="mt-4">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
        <button class="btn btn-primary" wire:click="saveSale" type="submit">@lang('Save Sale')</button>
    </div>
</form>

    </div>

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.sale.index') }}" />
@endpush

@push('style')
    <style>
        .empty-notification img {
            width: 30px;
            padding-top: 12px;
        }
    </style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" rel="stylesheet">
@endpush
</div>
