@extends('pdf.layouts.master2')

@section('content')
<div class="list--row mb-15px">
    <div class="float-left">
        <h6>@lang('Customer Info')</h6>
        <p class="mb-5px">@lang('Name'): {{ $customer->name }}</p>
        <p class="mb-5px">@lang('Email'): {{ $customer->email }}</p>
        <p class="mb-5px">@lang('Mobile'): +{{ $customer->mobile }}</p>
    </div>

    <div class="float-right">
        <h6>@lang('Payment Info')</h6>
        <p class="mb-5px">@lang('Receivable'): {{ showAmount($customer->totalReceivableAmount()) }}</p>
        <p class="mb-5px">@lang('Payable'): {{ showAmount($customer->totalPayableAmount()) }}</p>
    </div>
</div>

<!-- Table -->
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Opening</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Closing</th>
                <th>Source</th>
                <th>Bank</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ number_format($transaction->opening_balance, 2) }}</td>
                <td class="text-success">{{ number_format($transaction->credit_amount, 2) }}</td>
                <td class="text-danger">{{ number_format($transaction->debit_amount, 2) }}</td>
                <td>{{ number_format($transaction->closing_balance, 2) }}</td>
                <td>{{ $transaction->source ?? '-' }}</td>
                <td>{{ $transaction->bank->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d-m-Y h:i A') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>


@endsection