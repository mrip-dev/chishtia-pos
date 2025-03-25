@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Date')</th>
                <th>@lang('Reason')</th>
                <th>@lang('Amount')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ showDateTime($expense->date_of_expense, 'm/d/Y') }}</td>
                    <td>{{ $expense->expenseType->name }}</td>
                    <td>{{ showAmount($expense->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
