<div>
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Invoice #{{ $sale->invoice_no ?? 'N/A' }}</h4>
            <button class="btn btn-sm btn-primary" onclick="window.print()">
                <i class="la la-print"></i> Print Receipt
            </button>
        </div>

        <div class="card-body" id="printArea">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Customer Info</h6>
                    <p class="mb-1"><strong>Name:</strong> {{$sale->customer_name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $sale->customer_phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="fw-bold">Sale Info</h6>
                    <p class="mb-1"><strong>Date:</strong> {{ $sale->created_at}}</p>
                    <p class="mb-1"><strong>Branch:</strong> {{ $sale->warehouse->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Status:</strong>
                        <span class="badge bg-success text-uppercase">{{ $sale->status }}</span>
                    </p>
                </div>
            </div>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Tax</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleDetails as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->product->category?->name ?? '-' }}</td>
                                <td>{{ $item->product->brand?->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 2) }}</td>
                                <td>{{ number_format($item->discount ?? 0, 2) }}</td>
                                <td>{{ number_format($item->tax ?? 0, 2) }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <div class="text-end">
                    <h5><strong>Subtotal:</strong> {{ number_format($sale->saleDetails->sum('total'), 2) }}</h5>
                    <h4 class="mt-2"><strong>Total:</strong> {{ number_format($sale->total_price, 2) }}</h4>
                </div>
            </div>

            <hr>

            <div class="text-center mt-4">
                <p class="mb-0">Thank you for your purchase!</p>
                
            </div>
        </div>
    </div>
</div>


    <style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .btn, .card-header {
        display: none !important;
    }
}
</style>

</div>