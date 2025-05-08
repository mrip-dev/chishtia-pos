<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #97ca9c;
            color: white;
        }
    </style>
</head>
<body>

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

</body>
</html>
