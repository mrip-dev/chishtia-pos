@extends('pdf.layouts.master2')

@section('content')

    <h1>Day Book Report for {{ $dailyBookDate }}</h1>

    <p><strong>Opening Balance:</strong> {{ number_format($opening_balance, 2) }}</p>
    <p><strong>Closing Balance:</strong> {{ number_format($closing_balance, 2) }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Source</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($detail->date)->format('d-m-Y') }}</td>
                    <td>{{ $detail->source }}</td>
                    <td>{{ number_format($detail->debit, 2) }}</td>
                    <td>{{ number_format($detail->credit, 2) }}</td>
                    <td>{{ number_format($detail->balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
