@extends('pdf.layouts.master2')
@section('content')
<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Opening Balance</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Closing Balance</th>
                <th>Source</th>
                <th>Bank</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->customer->name ?? 'N/A' }}</td>
                <td>{{ number_format($transaction->opening_balance, 2) }}</td>
                <td class="text-success">{{ number_format($transaction->credit_amount, 2) }}</td>
                <td class="text-danger">{{ number_format($transaction->debit_amount, 2) }}</td>
                <td>{{ number_format($transaction->closing_balance, 2) }}</td>
                <td>{{ $transaction->source ?? '-' }}</td>
                <td>{{ $transaction->bank->name ?? 'N/A' }}</td>
                <td>{{ $transaction->created_at->format('d-m-Y h:i A') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

