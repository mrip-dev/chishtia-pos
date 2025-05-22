<div>
    <div class="d-flex mb-30 flex-wrap gap-3 justify-content-end align-items-center">
        <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
            <x-search-form dateSearch='yes' />
            @permit('admin.purchase.create')
            <button type="button" wire:click.prevent="createPurchase" class="btn btn-sm btn-outline--primary m-2">
                <i class="las la-plus"></i>{{ $isCreating ? __('Close') : __('Add New') }}
            </button>
            @endpermit
            @permit(['admin.purchase.pdf', 'admin.purchase.csv'])

            @php
            $params = request()->all();
            @endphp
            <div class="btn-group">
                <button class="btn btn-outline--success dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    @lang('Action')
                </button>
                <ul class="dropdown-menu">
                    @permit('admin.purchase.pdf')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.purchase.pdf', $params) }}"><i
                                class="la la-download"></i>@lang('Download PDF')</a>
                    </li>
                    @endpermit
                    @permit('admin.purchase.csv')
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.purchase.csv', $params) }}"><i
                                class="la la-download"></i>@lang('Download CSV')</a>
                    </li>
                    @endpermit

                </ul>
            </div>
            @endpermit
        </div>
    </div>

    @if (!$isCreating)
       <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card bg--transparent">
                    <div class="card-body p-0 ">
                        <div class="table-responsive--md table-responsive">
                            <table class="table table--light style--two bg--white">
                                <thead>
                                    <tr>
                                        <th>@lang('Invoice No.') | @lang('Date')</th>
                                        <th>@lang('Supplier') | @lang('Mobile')</th>
                                        <th>@lang('Driver Name') | @lang('Contact')</th>
                                        <th>@lang('Vehicle No') | @lang('Fare')</th>
                                        <th>@lang('Total Amount') | @lang('Warehouse')</th>
                                        <th>@lang('Discount') | @lang('Payable') </th>
                                        <th>@lang('Paid') | @lang('Due')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchases as $purchase)
                                        <tr @include('partials.bank-history-color', ['id' => $purchase->id])>
                                            <td>
                                                @if ($purchase->return_status == 1)
                                                    <small><i class="fa fa-circle text--danger" title="@lang('Returned')" aria-hidden="true"></i></small>
                                                @endif
                                                <span class="fw-bold">
                                                    {{ $purchase->invoice_no }}
                                                </span>
                                                <br>
                                                <small>{{ showDateTime($purchase->purchase_date, 'd M, Y') }}</small>
                                            </td>

                                            <td>
                                                <span class="text--primary fw-bold"> {{ $purchase->supplier?->name }}</span>
                                                <br>
                                                +{{ $purchase->supplier?->mobile }}
                                            </td>
                                            <td>
                                                <span class="text--success fw-bold"> {{ $purchase->driver_name }}</span>
                                                <br>
                                                +{{ $purchase->driver_contact }}
                                            </td>
                                            <td>
                                                {{ $purchase->vehicle_number }}
                                                <br>
                                                {{ $purchase->fare }}
                                            </td>

                                            <td>
                                                <span class="fw-bold">{{ showAmount($purchase->total_price) }}</span>
                                                <br>
                                                {{ $purchase->warehouse->name }}
                                            </td>
                                            <td>
                                                {{ showAmount($purchase->discount_amount) }}
                                                <br>
                                                <span class="fw-bold">{{ showAmount($purchase->payable_amount) }}</span>
                                            </td>
                                            <td>
                                                {{ showAmount($purchase->paid_amount) }}

                                                <br>

                                                @if ($purchase->due_amount < 0)
                                                    <span class="text--danger fw-bold" title="@lang('Receivable from Supplier')">
                                                        - {{ showAmount(abs($purchase->due_amount)) }}
                                                    </span>
                                                @else
                                                    <span class="fw-bold" title="@lang('Payable to Supplier')">
                                                        {{ showAmount($purchase->due_amount) }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="button--group">
                                                    @permit('admin.purchase.edit')
                                                        <a class="btn btn-sm btn-outline--primary ms-1 editBtn"
                                                            href="{{ route('admin.purchase.edit', $purchase->id) }}">
                                                            <i class="la la-pen"></i> @lang('Edit')
                                                        </a>
                                                    @endpermit
                                                    <button class="btn btn-sm btn-outline--info ms-1 dropdown-toggle" data-bs-toggle="dropdown" type="button"
                                                        aria-expanded="false">
                                                        <i class="la la-ellipsis-v"></i>@lang('More')
                                                    </button>

                                                    <div class="dropdown-menu">
                                                        @permit('admin.supplier.payment.store')
                                                            <a class="dropdown-item paymentModalBtn"  wire:click="payMentModal({{$purchase->id}})" data-supplier="{{ $purchase->supplier?->name }}"
                                                                data-invoice="{{ $purchase->invoice_no }}" data-id="{{ $purchase->id }}"
                                                                data-due_amount="{{ $purchase->due_amount }}" href="javascript:void(0)">
                                                                @if ($purchase->due_amount < 0)
                                                                    <i class="la la-hand-holding-usd"></i>
                                                                    @lang('Receive Payment')
                                                                @elseif($purchase->due_amount > 0)
                                                                    <i class="la la-money-bill-wave"></i>
                                                                    @lang('Give Payment')
                                                                @endif
                                                            </a>
                                                        @endpermit

                                                        @permit('admin.purchase.return.items')
                                                            @if ($purchase->return_status == 0 && $purchase->due_amount > 0)
                                                                <li>
                                                                    <a class="dropdown-item editBtn"
                                                                        href="{{ route('admin.purchase.return.items', $purchase->id) }}">
                                                                        <i class="la la-undo"></i> @lang('Return Purchase')
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endpermit
                                                        @permit('admin.purchase.return.edit')
                                                            @if ($purchase->return_status)
                                                                <li>
                                                                    <a class="dropdown-item editBtn"
                                                                        href="{{ route('admin.purchase.return.edit', $purchase->purchaseReturn->id) }}">
                                                                        <i class="la la-undo"></i> @lang('View Return Details')
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endpermit
                                                        @permit('admin.purchase.invoice.pdf')
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('admin.purchase.invoice.pdf', $purchase->id) }}">
                                                                    <i class="la la-download"></i> @lang('Download Details')
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
                    {{-- @if ($purchases->hasPages())
                        <div class="card-footer py-4">
                            @php echo  paginateLinks($purchases) @endphp
                        </div>
                    @endif --}}
                </div>
                <!-- card end -->
            </div>
        </div>
       </div>
    @else
    <div class="row gy-3">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form  wire:submit.prevent="savePurchase">
                     <div class="row mb-3">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Invoice No:')</label>
                                    <input class="form-control" name="invoice_no" type="text" wire:model="invoice_no"
                                        required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group" id="supplier-wrapper">
                                    <label class="form-label">@lang('Supplier')</label>
                                    <select class="select2 form-control" wire:model="supplier_id" required>
                                        <option value="" selected >@lang('Select One')</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Date')</label>
                                    <input class="form-control timepicker" wire:model="purchase_date" type="text" required>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group">
                                    <label class="form-label">@lang('Warehouse')</label>
                                    <select class="form-control select2" wire:model="warehouse_id" data-minimum-results-for-search="-1" required>
                                        <option value="" >@lang('Select One')</option>
                                        @foreach ($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected($warehouse->id == @$purchase->warehouse_id)>
                                                {{ __($warehouse->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div>
                                    <div class="form-group position-relative">
                                        <label>Search Product</label>
                                        <input type="text" class="form-control" wire:model.live="searchQuery" placeholder="Product Name or SKU">

                                        @if(!empty($searchResults))
                                        <div class="card position-absolute w-100 z-50" style="max-height: 200px; overflow-y: auto;">
                                            <ul class="list-group">
                                                @foreach ($searchResults as $product)
                                                    <li class="list-group-item list-group-item-action" wire:click="addProduct({{ $product->id }})" style="cursor: pointer;">
                                                        {{ $product->name }} ({{ $product->sku }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="table-responsive table-responsive--lg">
                                <table class="productTable table border">
                                    <thead class="border bg--dark">
                                        <tr>
                                            <th>@lang('Name')</th>
                                            <th>@lang('Quantity')<span class="text--danger">*</span></th>
                                            <th>@lang('Price')<span class="text--danger">*</span></th>
                                            <th>@lang('Total')</th>
                                            <th>@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        @foreach ($products as $index => $product)
                                        <tr>
                                            <td>{{ $product['name'] }}</td>
                                            {{-- <td>{{ $product['stock'] }}</td> --}}
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

                            </div>
                        </div>
                        <div class="row">
                            {{-- Vehicle & Driver Info Row --}}
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Vehicle Number')</label>
                                    <input class="form-control" wire:model.defer="vehicle_number" type="text">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Driver Name')</label>
                                    <input class="form-control" wire:model.defer="driver_name" type="text">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Driver Contact')</label>
                                    <input class="form-control" wire:model.defer="driver_contact" type="text">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Fare')</label>
                                    <input class="form-control" wire:model.defer="fare" type="number" step="any">
                                </div>
                            </div>

                            {{-- Note & Pricing Row --}}
                            <div class="col-md-7 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Note')</label>
                                    <textarea class="form-control" wire:model.defer="note" rows="8">{{ old('note', @$purchase->note) }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-5 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Total Price')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control total_price" type="number" wire:model="total_price" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Discount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control" type="number" step="any" wire:model.live="discount">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Payable Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input class="form-control" type="number" wire:model="payable_amount" disabled>
                                    </div>
                                </div>

                                @isset($purchase)
                                    <div class="form-group">
                                        <label>@lang('Paid Amount')</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                            <input class="form-control" wire:model.live="paid_amount" type="number" disabled>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>@lang('Due Amount')</label>
                                        <div class="input-group">
                                            <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                            <input class="form-control" wire:model="due_amount" type="number" disabled>
                                        </div>
                                    </div>
                                @endisset
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
                            <button class="btn btn--primary" type="submit">@lang('Save Purchase')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="modal fade" wire:ignore.self id="paymentModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Payment')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form wire:submit.prevent="submitPayment">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Left Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Invoice No.')</label>
                                    <input class="form-control " wire:model="modal_invoice_no" type="text" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="amountType"></label>
                                    <div class="input-group">
                                        <button class="input-group-text" type="button">{{ gs('cur_sym') }}</button>
                                        <input class="form-control" wire:model="modal_payable_amount" type="text" readonly>
                                    </div>
                                </div>
                                @if($modal_payment_method=='bank' || $modal_payment_method=='both')
                                <div class="form-group mb-3" id="bankNameField">
                                    <label for="bank_id">@lang('Bank Name')</label>
                                    <select name="bank_id" id="bank_id" class="form-control" wire:model="bankId">
                                        <option value="" selected>@lang('Select Bank')</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <!-- Right Column -->
                                <div class="form-group mb-3">
                                    <label>@lang('Supplier')</label>
                                    <input class="form-control" wire:model="modal_supplier_name" type="text" readonly>
                                </div>

                                <div class="form-group mb-3">
                                    <label>@lang('Payment Method')</label>
                                    <select wire:model.live="modal_payment_method" class="form-control" id="paymentMethodSelect" required>
                                        <option value="" disabled selected>@lang('Select Payment Method')</option>
                                        <option value="">----- choose -----</option>
                                        <option value="cash">@lang('Cash')</option>
                                        <option value="bank">@lang('Bank')</option>
                                        <option value="both">@lang('Both')</option>
                                    </select>
                                </div>
                                @if($modal_payment_method=='bank' || $modal_payment_method=='both')
                                <div class="form-group mb-3" id="receivedAmountField">
                                    <label>@lang('Paid Amount Bank')</label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                        <input type="number" step="any"
                                        wire:model="modal_rec_bank" name="received_amount_bank" class="form-control">
                                    </div>
                                    @error('modal_rec_bank')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                @endif
                                @if($modal_payment_method == 'cash' || $modal_payment_method == 'both')
                                <div class="form-group mb-3" id="receivedAmount">
                                    <label>@lang('Paid Amount Cash')</label>
                                    <div class="input-group">
                                        <button class="input-group-text" type="button">{{ gs('cur_sym') }}</button>
                                        <input class="form-control" wire:model="modal_paid_amount" type="number" step="any" required>
                                    </div>
                                    @error('modal_paid_amount')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary h-45 w-100 permit" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


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
