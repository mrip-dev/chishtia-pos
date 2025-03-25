@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Name')</th>
                <th>@lang('Mobile')</th>
                <th>@lang('Email')</th>
                <th>@lang('Receivable')</th>
                <th>@lang('Payable')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>+{{ $customer->mobile }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ showAmount($customer->totalReceivableAmount()) }}</td>
                    <td>{{ showAmount($customer->totalPayableAmount()) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
