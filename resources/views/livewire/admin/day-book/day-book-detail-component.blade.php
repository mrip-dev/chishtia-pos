<div class="container py-4">
    <!-- Page Title -->




    <!-- Summary Cards Centered -->
    <div class="d-flex justify-content-center flex-wrap gap-4 mb-4">
        <!-- Opening Balance -->
        <div class="card shadow border-start border-4 border-success" style="min-width: 260px;">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Opening Balance</h6>
                <h4 class="text-success fw-bold mb-0">{{ number_format($opening_balance, 2) }}</h4>
            </div>
        </div>

        <!-- Closing Balance -->
        <div class="card shadow border-start border-4 border-danger" style="min-width: 260px;">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Closing Balance</h6>
                <h4 class="text-danger fw-bold mb-0">{{ number_format($closing_balance, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mb-3">
        <div class="input-group w-auto">
            <span class="input-group-text bg--primary text-white">Search Source</span>

            <input type="text"
                   wire:model.live="search"
                   class="form-control"
                   placeholder="Type here...">

            @if($search)
                <button wire:click="$set('search', '')" class="btn btn-outline-secondary">
                    Clear
                </button>
            @endif
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table tale-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="bg--primary">#</th>
                            <th class="bg--primary">Date</th>
                            <th class="bg--primary">Source</th>
                            <th class="bg--primary">Credit</th>
                            <th class="bg--primary">Debit</th>
                            <th class="bg--primary">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookDetails as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($detail->date)->translatedFormat('l, j F Y') }}</td>
                                <td>
                                    <a href="#" wire:click="redirectDataModel({{ $detail->module_id }}, '{{ $detail->data_model }}')">
                                        {{ $detail->source }}
                                    </a>
                                </td>
                                <td class="text-danger">{{ $detail->credit ?? 0 }}</td>
                                <td class="text-success">{{ $detail->debit ?? 0 }}</td>
                                <td>{{ $detail->closing_balance ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No Records Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
