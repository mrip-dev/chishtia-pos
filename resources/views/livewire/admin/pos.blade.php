<div class="container-fluid py-3">
    <div class="mt-2">
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show p-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show p-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if (session()->has('info'))
        <div class="alert alert-info alert-dismissible fade show p-4" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
    </div>

    <div class="row">
        <!-- 8-Column Product Grid -->
        <div class="col-md-8">
            <div class="row row-cols-2 row-cols-md-4 g-3">
                @foreach ($products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm" wire:click="addToCart({{ $product->id }})" style="cursor: pointer;">
                        <img src="{{ asset('assets/images/product/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height:120px; object-fit:cover;">
                        <div class="card-body text-center">
                            <h6 class="mb-0">{{ $product->name }} {{ $product->category?->name }}</h6>
                            <small class="text-muted">Rs {{ number_format($product->price) }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- 4-Column Cart -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>Cart</strong>
                </div>
                <div class="card-body p-2" style="max-height: 65vh; overflow-y: auto;">
                    @forelse ($cart as $item)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $item['name'] }}</strong><br>
                            <small>Rs {{ number_format($item['price']) }}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-secondary" wire:click="decreaseQty({{ $item['id'] }})">-</button>
                            <span class="mx-2">{{ $item['quantity'] }}</span>
                            <button class="btn btn-sm btn-outline-secondary" wire:click="increaseQty({{ $item['id'] }})">+</button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $item['id'] }})">&times;</button>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted my-3">No items in cart.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>Rs {{ number_format($this->total, 2) }}</strong>
                    </div>
                    <button wire:click="saveOrder" class="btn btn-success w-100 mt-2" @if($orderStatus=='saved' ) disabled @endif>
                        {{ $orderStatus == 'draft' ? 'Save Order' : 'Order Saved' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:navigated', () => {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        });
    </script>

</div>