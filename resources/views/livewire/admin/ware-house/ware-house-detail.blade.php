<div>
    <h3>Warehouse History</h3>

    <!-- Table to display warehouse history details -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Warehouse</th>
                <th>Product</th>
                <th>Supplier</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Stock In</th>
                <th>Stock Out</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($warehouseDetails as $key => $detail)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $detail->wareHouse->name ?? 'N/A' }}</td>
                    <td>{{ $detail->product->name ?? 'N/A' }}</td>
                    <td>{{ $detail->supplier_id == 0 ? 'N/A' : $detail->supplier->name }}</td>
                    <td>{{ $detail->customer->name ?? 'N/A' }}</td>
                    <td>{{ $detail->date }}</td>
                    <td>{{ $detail->stock_in }}</td>
                    <td>{{ $detail->stock_out }}</td>
                    <td>{{ $detail->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    {{-- {{ $warehouseDetails->links() }} --}}
</div>
