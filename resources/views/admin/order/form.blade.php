@extends('admin.layouts.app')
@section('panel')
<div class="row" id="orderApp">
    <div class="col-lg-8 mb-30">
        <div class="card h-100">

            <div class="card-body">
                <div class="category-tabs-container mb-3">
                    <ul class="nav nav-pills product-category-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item me-2" role="presentation">
                            <a class="nav-link" :class="{ 'active': activeCategory === null }"
                                @click="setActiveCategory(null)" role="tab" aria-selected="true">
                                @lang('All')
                            </a>
                        </li>
                        <li v-for="category in categories" :key="category.id" class="nav-item me-2" role="presentation">
                            <a class="nav-link" :class="{ 'active': activeCategory === category.id }"
                                @click="setActiveCategory(category.id)" role="tab" :aria-selected="activeCategory === category.id">
                                @{{ category.name }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text"><i class="las la-search"></i></span>
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="form-control"
                            placeholder="@lang('Search products...')">
                    </div>
                </div>

                <div class="product-catalog p-4">
                    <div class="row">
                        <div
                            v-for="product in filteredProducts"
                            :key="product.id"
                            class="col-xl-3 col-lg-4 col-md-4 col-sm-6 mb-3 px-2"
                            @click="addProduct(product)">

                            <div class="product-item h-100">
                                <div class="product-image">
                                    <img :src="getProductImage(product)" :alt="product.name">
                                </div>
                                <div class="product-details">
                                    <h6 class="product-name">@{{ product.display_title }}</h6>
                                    <div class="product-price">
                                        <strong>{{ gs('cur_sym') }}@{{ formatPrice(product.selling_price) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div v-if="filteredProducts.length === 0" class="empty-catalog text-center py-5">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty" style="width: 60px;">
                        <p class="mt-3 text-muted">@lang('No products found')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-30">
        <div class="card">
            <div class="card-body">
                <form @submit.prevent="submitOrder" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-xl-6 col-sm-6">
                            <div class="form-group">
                                <label>@lang('Order No.')</label>
                                <input
                                    class="form-control"
                                    v-model="orderData.invoice_no"
                                    type="text"
                                    readonly>
                            </div>
                        </div>

                        <div class="col-xl-6 col-sm-6">
                            <div class="form-group">
                                <label>@lang('Customer') <span class="text--danger">*</span></label>
                                <input
                                    class="form-control"
                                    v-model="orderData.customer_name"
                                    type="text">
                            </div>
                        </div>

                        <div class="col-xl-6 col-sm-6">
                            <div class="form-group">
                                <label>@lang('Date') <span class="text--danger">*</span></label>
                                <input
                                    class="form-control"
                                    v-model="orderData.sale_date"
                                    type="date"
                                    required>
                            </div>
                        </div>

                        <div class="col-xl-6 col-sm-6">
                            <div class="form-group">
                                <label>@lang('Status') <span class="text--danger">*</span></label>
                                <select
                                    class="form-control"
                                    v-model="orderData.status"
                                    required>
                                    <option value="pending">@lang('Pending')</option>
                                    <option value="confirmed">@lang('Confirmed')</option>
                                    <option value="processing">@lang('Processing')</option>
                                    <option value="shipped">@lang('Shipped')</option>
                                    <option value="delivered">@lang('Delivered')</option>
                                    <option value="cancelled">@lang('Cancelled')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table border">
                                    <thead class="border bg--dark">
                                        <tr>
                                            <th>@lang('Product')</th>
                                            <th width="150">@lang('Quantity')</th>
                                            <th width="150">@lang('Price')</th>
                                            <th width="150">@lang('Total')</th>
                                            <th width="100">@lang('Action')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item, index) in selectedProducts" :key="item.product_id">
                                            <td>
                                                <strong>@{{ item.display_title }}</strong>
                                            </td>

                                            <td>

                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="width:100px"
                                                    v-model.number="item.quantity"
                                                    @input="calculateTotal(index)"
                                                    min="1"
                                                    required>


                                            </td>
                                            <td>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    style="width:100px"
                                                    v-model.number="item.price"
                                                    @input="calculateTotal(index)"
                                                    step="0.01"
                                                    min="0"
                                                    required>

                                            </td>
                                            <td>
                                                <span>@{{formatPrice(item.total)}}</span>
                                            </td>
                                            <td>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline--danger"
                                                    @click="removeProduct(index)">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="selectedProducts.length === 0">
                                            <td colspan="6" class="text-center text-muted">
                                                @lang('No products added. Select products from catalog.')
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="order-summary">
                                <div class="form-group">
                                    <label>@lang('Total Price')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="formatPrice(totalPrice)"
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Discount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model.number="orderData.discount"
                                            @input="validateDiscount"
                                            step="0.01"
                                            min="0">
                                    </div>
                                    <small v-if="discountError" class="text--danger">@{{ discountError }}</small>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Receivable Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="formatPrice(receivableAmount)"
                                            readonly>
                                    </div>
                                </div>

                                @isset($sale)
                                <div class="form-group">
                                    <label>@lang('Received Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ getAmount(@$sale->received_amount) }}"
                                            disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Due Amount')</label>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ getAmount(@$sale->due_amount) }}"
                                            disabled>
                                    </div>
                                </div>
                                @endisset

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Note')</label>
                                <textarea
                                    class="form-control"
                                    v-model="orderData.note"
                                    rows="4"></textarea>
                            </div>
                        </div>

                    </div>

                    @if (isset($sale) && $sale->return_status == 1)
                    <div class="alert alert-danger p-3 mb-3" role="alert">
                        <h6 class="text--danger">
                            <i class="fa fa-exclamation-circle"></i>
                            @lang('Some products have been returned from this order')
                        </h6>
                        <p class="mb-0">
                            @lang('You cannot edit an order after returning products.')
                            <a class="text--primary" href="{{ route('admin.sale.return.edit', $sale->saleReturn->id) }}">
                                @lang('View Return Details')
                            </a>
                        </p>
                    </div>
                    @endif

                    <button
                        type="submit"
                        class="btn btn--primary w-100 h-45"
                        :disabled="!canSubmit || isSubmitting"
                        @if (isset($sale) && $sale->return_status == 1) disabled @endif
                        >
                        <span v-if="isSubmitting">
                            <i class="las la-spinner la-spin"></i> @lang('Submitting...')
                        </span>
                        <span v-else>@lang('Submit Order')</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<x-back route="{{ route('admin.order.index') }}" />
@endpush

@push('style')
<style>
    /* Corrected max-height */
    .product-catalog {
        max-height: calc(100vh - 350px);
        overflow-y: scroll;
        padding-right: 5px;
    }

    .product-item {
        display: flex; /* Added display flex to align items correctly */
        flex-direction: column; /* Stacks image and details */
        align-items: center;
        text-align: center;
        padding: 12px;
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%; /* Ensure h-100 works */
    }

    .product-item:hover {
        border-color: var(--primary);
        background-color: #f8f9fa;
        transform: translateY(-5px); /* Changed to Y-axis for better visual */
    }

    .product-image {
        width: 100%;
        max-width: 150px; /* Constrain image width */
        height: 120px; /* Reduced height */
        flex-shrink: 0;
        border-radius: 6px;
        overflow: hidden;
        background: #f5f5f5;
        margin-bottom: 10px;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-details {
        flex: 1;
        width: 100%;
    }

    .product-name {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #333;
        /* overflow: hidden; */
        /* white-space: nowrap; */
        text-overflow: ellipsis;
    }

    .product-price {
        margin-top: 4px;
        color: var(--primary);
    }

    .order-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }

    .empty-catalog {
        padding: 60px 20px;
    }

    /* Scrollbar styles */
    .product-catalog::-webkit-scrollbar {
        width: 6px;
    }

    .product-catalog::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }

    .product-catalog::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Category Tabs Styles */
    .category-tabs-container {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 10px;
    }

    .product-category-tabs {
        flex-wrap: nowrap;

    }

    .product-category-tabs .nav-link {
        border-radius: 5px;
        padding: 5px 15px;
        color: var(--dark);
        border: 1px solid #ddd;
        transition: all 0.2s;
        cursor: pointer;
    }

    .product-category-tabs .nav-link.active {
        background-color: #a0522d;

        color: #fff;
        border-color: var(--primary);
    }
</style>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/vue@3.3.4/dist/vue.global.js"></script>
<script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                searchQuery: '',
                // Ensure these variables are defined in your PHP controller if they are not defined they will break the page
                products: @json($products ?? []),
                categories: @json($categories ?? []),
                customers: @json($customers ?? []),
                selectedProducts: [],
                orderData: {
                    invoice_no: @json($sale->invoice_no ?? $invoiceNumber ?? 'NEW-ORDER-000'),
                    customer_name: @json($sale->customer_name?? ''),
                    sale_date: @json($sale->sale_date ?? date('Y-m-d')),
                    status: @json($sale->status ?? 'pending'),
                    discount: @json($sale->discount_amount ?? 0),
                    note: @json($sale->note ?? '')
                },
                discountError: '',
                isSubmitting: false,
                isEdit: @json(isset($sale)),
                activeCategory: null
            }
        },
        computed: {
            filteredProducts() {
    // Log initial state for debugging
    console.log('=== FILTER START ===');
    console.log('Search Query:', this.searchQuery);
    console.log('Active Category:', this.activeCategory);
    console.log('Total Products Loaded:', this.products ? this.products.length : 0);

    // Safety check
    if (!Array.isArray(this.products)) {
        console.warn('Products data is not an array:', this.products);
        return [];
    }

    let filtered = [...this.products]; // create a copy
    const query = this.searchQuery ? this.searchQuery.toLowerCase().trim() : '';

    // 1. Filter by Category
    if (this.activeCategory !== null && this.activeCategory !== '' && this.activeCategory !== undefined) {
        const activeCat = Number(this.activeCategory);
        filtered = filtered.filter(product => {
            const productCat = Number(product.category_id);
            const match = productCat === activeCat;
            if (!match) {
                console.log(`Product ${product.id || 'N/A'} skipped (Cat: ${productCat}, Expected: ${activeCat})`);
            }
            return match;
        });
        console.log('Filtered by Category Count:', filtered.length);
    } else {
        console.log('No active category filter applied');
    }

    // 2. Filter by Search Query
    if (query) {
        filtered = filtered.filter(product => {
            const name = product.name?.toString().toLowerCase() || '';
            const title = product.display_title?.toString().toLowerCase() || '';
            const sku = product.sku?.toString().toLowerCase() || '';
            return name.includes(query) || title.includes(query) || sku.includes(query);
        });
        console.log('Filtered by Search Query Count:', filtered.length);
    } else {
        console.log('No search query filter applied');
    }

    // Final logs
    console.log('Final Filtered Product Count:', filtered.length);
    console.log('Filtered Products:', filtered);
    console.log('=== FILTER END ===');

    return filtered;
},

            totalPrice() {
                // Ensure item.total is calculated and is a number
                return this.selectedProducts.reduce((sum, item) => sum + (item.total || 0), 0);
            },
            receivableAmount() {
                return Math.max(0, this.totalPrice - (this.orderData.discount || 0));
            },
            canSubmit() {
                return this.selectedProducts.length > 0 &&
                    this.orderData.customer_name &&
                    !this.discountError;
            }
        },
        methods: {
            setActiveCategory(categoryId) {
                this.activeCategory = categoryId;
                this.searchQuery = ''; // Clear search when switching categories
            },
            addProduct(product) {
                const existingIndex = this.selectedProducts.findIndex(
                    item => item.product_id === product.id
                );

                if (existingIndex !== -1) {
                    this.selectedProducts[existingIndex].quantity++;
                    this.calculateTotal(existingIndex);
                } else {
                    this.selectedProducts.push({
                        product_id: product.id,
                        name: product.name,
                        display_title: product.display_title || product.name,
                        category_name: product.category_name,
                        unit: (product.unit && product.unit.name) ? product.unit.name : 'Pcs',
                        quantity: 1,
                        price: parseFloat(product.selling_price || 0),
                        total: parseFloat(product.selling_price || 0)
                    });
                }
            },
            removeProduct(index) {
                this.selectedProducts.splice(index, 1);
            },
            calculateTotal(index) {
                const item = this.selectedProducts[index];
                // Ensure quantity and price are numbers before multiplication
                const quantity = parseFloat(item.quantity || 0);
                const price = parseFloat(item.price || 0);

                // Update the total and force reactivity
                item.total = quantity * price;

                // Re-validate discount since total price might change
                this.validateDiscount();
            },
            validateDiscount() {
                // Wait for the next tick to ensure totalPrice is updated
                this.$nextTick(() => {
                    if (this.orderData.discount < 0) {
                        this.discountError = 'Discount cannot be negative';
                    } else if (this.orderData.discount > this.totalPrice) {
                        this.discountError = `Discount cannot exceed total price (${this.formatPrice(this.totalPrice)})`;
                    } else {
                        this.discountError = '';
                    }
                });
            },
            formatPrice(value) {
                return parseFloat(value || 0).toFixed(2);
            },
            getProductImage(product) {
                // Assuming 'image_url' is available on the product object
                return product.image_url || '{{ getImage("assets/images/default.png") }}';
            },
            submitOrder() {
                if (!this.canSubmit) return;
                this.isSubmitting = true;

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('customer_name', this.orderData.customer_name);
                formData.append('sale_date', this.orderData.sale_date);
                formData.append('status', this.orderData.status);
                formData.append('discount', this.orderData.discount || 0);
                formData.append('note', this.orderData.note || '');
                formData.append('total_amount', this.totalPrice);
                formData.append('receivable_amount', this.receivableAmount);

                this.selectedProducts.forEach((product, index) => {
                    formData.append(`products[${index}][product_id]`, product.product_id);
                    formData.append(`products[${index}][quantity]`, product.quantity);
                    formData.append(`products[${index}][price]`, product.price);
                    formData.append(`products[${index}][total]`, product.total);
                });

                const url = this.isEdit ?
                    '{{ isset($sale) ? route("admin.order.update", $sale->id) : "" }}' :
                    '{{ route("admin.order.store") }}';

                fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            // Laravel often requires method override for PUT/PATCH/DELETE
                            ...(this.isEdit ? {
                                'X-HTTP-Method-Override': 'PUT'
                            } : {})
                        }
                    })
                    .then(response => {
                        if (response.status === 403) {
                             return Promise.reject({message: 'Forbidden: CSRF token mismatch or unauthorized action.'});
                        }
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            this.showNotification('success', data.message);
                            setTimeout(() => {
                                window.location.href = data.redirect || '{{ route("admin.order.index") }}';
                            }, 1000);
                        } else {
                            this.isSubmitting = false;
                            this.showNotification('error', data.message || 'An unexpected error occurred');
                        }
                    })
                    .catch(error => {
                        this.isSubmitting = false;
                        console.error('Error:', error);
                        if (error.errors) {
                            const firstError = Object.values(error.errors)[0][0];
                            this.showNotification('error', firstError);
                        } else if (error.message) {
                            this.showNotification('error', error.message);
                        } else {
                            this.showNotification('error', 'An error occurred while saving the order. Check the console for details.');
                        }
                    });
            },
            showNotification(type, message) {
                // Assumes iziToast is available globally
                if (typeof iziToast !== 'undefined') {
                    iziToast[type]({
                        message: message,
                        position: 'topRight'
                    });
                } else {
                    alert(`${type.toUpperCase()}: ${message}`);
                }
            }
        },
        mounted() {
            // Log to confirm data presence
            console.log('Products loaded:', this.products.length);
            console.log('Categories loaded:', this.categories.length);

            // Logic for pre-loading sale details in edit mode
            @if(isset($sale))
            console.log('Editing mode - Loading sale details');
            this.selectedProducts = [
                @foreach($sale->saleDetails as $detail) {
                    product_id: {{$detail->product_id}},
                    name: '{{ addslashes($detail->product->name) }}',
                    display_title: '{{ getProductTitle($detail->product->id) }}',
                    sku: '{{ $detail->product->sku }}',
                    category_name: '{{ addslashes($detail->product->category->name ?? "No Category") }}',
                    brand_name: '{{ addslashes($detail->product->brand->name ?? "No Brand") }}',
                    unit: '{{ $detail->product->unit->name }}',
                    quantity: {{$detail->quantity}},
                    price: {{$detail->price}},
                    total: {{$detail->total}}
                }
                @if(!$loop->last), @endif
                @endforeach
            ];

            this.$nextTick(() => {
                this.selectedProducts.forEach((item, index) => {
                    this.calculateTotal(index);
                });
                this.validateDiscount(); // Initial discount validation
            });
            @endif
        }
    }).mount('#orderApp');
</script>
@endpush