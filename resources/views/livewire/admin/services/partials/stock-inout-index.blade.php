<div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card bg--transparent">
                <div class="card-body p-0 ">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two bg--white">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Vendor / Client')</th>
                                    <th>@lang('Warehouse')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Total Amount')</th>
                                    <th>@lang('Received Amount')</th>
                                    <th>@lang('Remaining Amount')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stocks as $item)
                                <tr @include('partials.bank-history-color', ['id' => $item->id])>

                                    <td>
                                        <span class="text--primary fw-bold"> {{ $item->title }}</span>

                                    </td>
                                    <td>
                                        <span class="text--primary fw-bold"> {{ $item->user->name }}</span>

                                    </td>

                                    <td>

                                        {{ $item->warehouse->name }}
                                    </td>
                                    <td>
                                        {{ $item->stock_type ? $item->stock_type== 'in' ? 'Stock In' : 'Stock Out' : '' }}
                                    </td>
                                    <td>
                                        {{ $item->created_at->format('d M, Y') }}

                                    </td>

                                    <td>

                                        {{ number_format($item->total_amount) }} {{ gs('cur_sym') }}
                                    </td>

                                    <td>
                                        {{number_format( $item->recieved_amount) }} {{ gs('cur_sym') }}
                                    </td>
                                    <td>

                                        {{ number_format($item->due_amount) }} {{ gs('cur_sym') }}
                                    </td>
                                    <td>
                                        <div class="button--group">

                                            <a wire:click.prevent="viewDetails({{ $item->id }})"
                                                class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="la la-chart"></i> @lang('Details')
                                            </a>

                                            @if($stock_type=='in')
                                            <button type="button"
                                                class="btn btn-sm btn-outline--info ms-1 dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="la la-ellipsis-v"></i>@lang('More')
                                            </button>

                                            <div class="dropdown-menu">
                                                <a href="javascript:void(0)" wire:click="payMentModal({{$item->id}})" class="dropdown-item paymentModalBtn">
                                                    <i class="la la-money-bill-wave"></i>
                                                    @lang('Recieve Payment')
                                                </a>
                                            </div>
                                            @endif
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
                {{-- @if ($stocks->hasPages())
                        <div class="card-footer py-4">
                            @php echo  paginateLinks($stocks) @endphp
                        </div>
                    @endif --}}
            </div>
            <!-- card end -->
        </div>
    </div>
</div>
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
                        <div class="row">
                            <div class="col-12">
                                <!-- //// Show the selected stock details here -->
                                <div class="form-group mb-3">
                                    <label>@lang('Title')</label>
                                    <input type="text" class="form-control" wire:model="modal_title" readonly>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label>@lang('Due Amount')</label>
                                <div class="input-group">
                                    <button type="button" class="input-group-text">{{ gs('cur_sym') }}</button>
                                    <input type="text" class="form-control receivable_amount" wire:model="modal_receivable_amount" readonly>
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
                                <label>@lang('Received Amount Cash')</label>
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