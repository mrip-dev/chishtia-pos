<div>
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-end align-items-start gap-2">

            {{-- Date: Start --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date" class="form-control custom-date-input" wire:model.live="startDate" placeholder="Start Date">
            </div>

            {{-- Date: End --}}
            <div class="input-group w-auto">
                <span class="input-group-text bg--primary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </span>
                <input type="date"  class="form-control custom-date-input" wire:model.live="endDate" placeholder="End Date">
            </div>


            {{-- Search Input --}}
            <div class="input-group w-50">
                <span class="input-group-text bg--primary">
                    <i class="fas fa-search text-white"></i>
                </span>
                <input
                    type="text"
                    class="form-control"
                    placeholder="Search by Product, Supplier, or Customer"
                    wire:model.live="search"
                >
            </div>

            {{-- Clear All --}}
            @if($search || $startDate || $endDate)
                <button class="btn btn-outline--primary" wire:click="clearFilters">
                    <i class="fas fa-times me-1"></i> Clear All
                </button>
            @endif
        </div>
    </div>



    <!-- Table to display warehouse history details -->
    <table class="table table--dark style--two bg-white">
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
                    <td>{!! $detail->supplier->name ?? '<span class="badge bg-warning text-dark">No Supplier</span>' !!}</td>
                    <td>{!! $detail->customer?->name ?? '<span class="badge bg-success text-white">No Customer</span>' !!}</td>
                    <td>{{ $detail->date }}</td>
                    <td>{{ $detail->stock_in }}</td>
                    <td>{{ $detail->stock_out }}</td>
                    <td>{{ $detail->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $warehouseDetails->links() }}
    </div>
</div>
