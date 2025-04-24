<div>
    <div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
            <x-search-form dateSearch='yes' />
            @permit('admin.sale.create')
            <button type="button" wire:click.prevent="createSale" class="btn btn-sm btn-outline--primary m-2">
                <i class="las la-plus"></i>{{ $isCreating ? __('Close') : __('Add New') }}
            </button>
            @endpermit
            @permit(['admin.sale.pdf', 'admin.sale.csv'])

            @php
            $params = request()->all();
            @endphp
            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.sale.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sale.pdf', $params) }}"><i
                                class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                    @endpermit
                    @permit('admin.sale.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.sale.csv', $params) }}"><i
                                class="la la-download"></i>@lang('Download CSV')</a>
                    </li>
                    @endpermit

                </ul>
            </div>
            @endpermit
        </div>
    </div>
    @if(!$isCreating)
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg--transparent">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two bg-white">
                            <thead>
                                <tr>
                                    <th>@lang('Invoice No.') | @lang('Date')</th>
                                    <th>@lang('Customer') | @lang('Mobile')</th>
                                    <th>@lang('Warehouse') | @lang('Total Amount')</th>
                                    <th>@lang('Discount') | @lang('Receivable')</th>
                                    <th>@lang('Received') | @lang('Due')</th>
                                    <th>@lang('Action')</th>
                            </thead>
                            <tbody>

                                @forelse($sales as $sale)
                                <tr @include('partials.bank-history-color', ['id' => $sale->id])>
                                    <td>
                                        @if ($sale->return_status == 1)
                                        <small><i class="fa fa-circle text--danger" title="@lang('Returned')"
                                                aria-hidden="true"></i></small>
                                        @endif
                                        <span class="fw-bold"> {{ $sale->invoice_no }}</span>
                                        <br>
                                        <small>{{ showDateTime($sale->sale_date, 'd M, Y') }}</small>
                                    </td>

                                    <td>
                                        <span class="text--primary fw-bold"> {{ $sale->customer->name }}</span>
                                        <br>
                                        +{{ $sale->customer->mobile }}
                                    </td>

                                    <td>
                                        {{ $sale->warehouse->name }}
                                        <br>
                                        <span
                                            class="fw-bold">{{ showAmount($sale->total_price) }}</span>
                                    </td>

                                    <td>{{ showAmount($sale->discount_amount) }}
                                        <br>
                                        <span class="fw-bold">
                                            {{ showAmount($sale->receivable_amount) }}</span>
                                    </td>

                                    <td>
                                        {{ showAmount($sale->received_amount) }}
                                        <br>
                                        <span
                                            @if ($sale->due_amount < 0) class="text--danger" title="@lang('Give Payment To Customer')" @endif
                                                class="fw-bold">
                                                {{ showAmount($sale->due_amount) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="button--group">

                                            <a wire:click.prevent="editSale({{ $sale->id }})"
                                                class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-pen"></i> @lang('Edit')
                                            </a>

                                            <button type="button"
                                                class="btn btn-sm btn-outline--info ms-1 dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="la la-ellipsis-v"></i>@lang('More')
                                            </button>

                                            <div class="dropdown-menu">
                                                @permit('admin.customer.payment.store')
                                                <a href="javascript:void(0)" wire:click="payMentModal({{$sale->id}})" class="dropdown-item paymentModalBtn"
                                                    data-customer_id="{{ $sale->customer_id }}"
                                                    data-customer="{{ $sale->customer->name }}"
                                                    data-invoice="{{ $sale->invoice_no }}"
                                                    data-id="{{ $sale->id }}"
                                                    data-due_amount="{{ $sale->due_amount }}">

                                                    @if ($sale->due_amount > 0)
                                                    <i class="la la-hand-holding-usd"></i>
                                                    @lang('Receive Payment')
                                                    @elseif($sale->due_amount < 0)
                                                        <i class="la la-money-bill-wave"></i>
                                                        @lang('Give Payment')
                                                        @endif
                                                </a>
                                                @endpermit

                                                @permit('admin.sale.return.items')
                                                @if ($sale->return_status == 0 && $sale->due_amount > 0)
                                                <li>
                                                    <a href="{{ route('admin.sale.return.items', $sale->id) }}"
                                                        class="dropdown-item">
                                                        <i class="la la-redo"></i> @lang('Return Sale')
                                                    </a>
                                                </li>
                                                @endif
                                                @endpermit
                                                @permit('admin.sale.return.edit')
                                                @if ($sale->return_status)
                                                <li>
                                                    <a href="{{ route('admin.sale.return.edit', $sale->saleReturn->id) }}"
                                                        class="dropdown-item editBtn">
                                                        <i class="la la-undo"></i> @lang('View Return Details')
                                                    </a>
                                                </li>
                                                @endif
                                                @endpermit
                                                @permit('admin.sale.invoice.pdf')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.sale.invoice.pdf', $sale->id) }}/?print=true">
                                                        <i class="la la-download"></i> @lang('Download Invoice')
                                                    </a>
                                                </li>
                                                @endpermit
                                            </div>
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
                {{-- @if ($sales->hasPages())
                    <div class="card-footer py-4">
                        @php echo paginateLinks($sales) @endphp
                    </div>
                @endif --}}
            </div><!-- card end -->
        </div>
    </div>
    @else
    <div class="row gy-3">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
          <form wire:submit.prevent="saveSale">
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
                <input type="text" class="form-control" wire:model.live="searchQuery" placeholder="@lang('Product Name or SKU')">
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

                <table class="table table--light style--two bg-white">
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
                        <tr >
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['stock'] }}</td>
                            <td>
                                <input type="number" wire:model.live="products.{{ $index }}.quantity" class="form-control">
                                @error("products.$index.quantity") <small class="text-danger">{{ $message }}</small> @enderror
                            </td>
                            <td>
                                <input type="text" wire:model.live="products.{{ $index }}.price" class="form-control">
                                @error("products.$index.price") <small class="text-danger">{{ $message }}</small> @enderror
                            </td>

                            <td>
                                {{ number_format($products[$index]['total'], 2) }}
                            </td>

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

            <div class="row mt-3">
                <div class="col-md-8 col-sm-6">
                    <div class="form-group">
                        <label>@lang('Note')</label>
                        <textarea class="form-control" wire:model.defer="note"></textarea>
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>@lang('Total Price')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" wire:model="total_price" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>@lang('Discount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" step="any" wire:model.live="discount">
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-sm-12">
                            <div class="form-group">
                                <label>@lang('Receivable Amount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" wire:model="receivable_amount" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>@lang('Received Amount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" wire:model.live="received_amount">
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>@lang('Due Amount')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" wire:model="due_amount" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <button class="btn btn--primary" type="submit">@lang('Save Sale')</button>
            </div>
        </form>

    </div>
    @endif
    <div id="paymentModal" wire:ignore.self class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form wire:submit.prevent="submitPayment">

                    <div class="modal-body">
                        <!-- Basic Information - Two columns in one row -->
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Left Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Invoice No.')</label>
                                    <input type="text" class="form-control invoice-no" wire:model="modal_invoice_no" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="amountType"></label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                        <input type="text" class="form-control receivable_amount" wire:model="modal_receivable_amount" readonly>
                                    </div>
                                </div>
                                @if($modal_payment_method=='bank' || $modal_payment_method=='both')
                                <div class="form-group mb-3" id="bankNameField">
                                    <label for="bank_id">@lang('Bank Name')</label>
                                    <select name="bank_id" id="bank_id" class="form-control" wire:model="bankId">
                                        <option value=""selected>@lang('Select Bank')</option>
                                        @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3" id="receivedAmountField">
                                    <label>@lang('Received Amount Bank')</label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                        <input type="number" step="any" name="received_amount_bank" wire:model="modal_rec_bank" class="form-control">
                                    </div>
                                    @error('modal_rec_bank')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <!-- Right Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Customer')</label>
                                    <input type="text" class="form-control customer-name" wire:model="modal_customer_name" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label>@lang('Payment Method')</label>
                                    <select name="payment_method" class="form-control" id="paymentMethodSelect" wire:model.live="modal_payment_method" required>
                                        {{-- <option value="" disabled selected>@lang('Select Payment Method')</option>
                                        <option value="">----- choose -----</option> --}}
                                        <option value="cash">@lang('Cash')</option>
                                        <option value="bank">@lang('Bank')</option>
                                        <option value="both">@lang('Both')</option>
                                    </select>
                                </div>

                                @if($modal_payment_method=='cash' || $modal_payment_method=='both')
                                <div class="form-group mb-3" id="receivedAmount">
                                    <label class="payment-type"></label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                        <input type="number" step="any" wire:model="modal_rec_amount" class="form-control" required>
                                    </div>
                                    @error('modal_rec_amount')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100 permit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @push('style')
    <style>
        .table-responsive {
            min-height: 400px;
            background: transparent
        }

        .card {
            box-shadow: none;
        }
    </style>
    @endpush

    @push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.paymentModalBtn', function() {
                var modal = $('#paymentModal');
                modal.modal('show');
            });
        })(jQuery);
    </script>
    @endpush
</div>