@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Tracking No.')</th>
                <th>@lang('Date')</th>
                <th>@lang('Warehouse')</th>
                <th>@lang('Products')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($adjustments as $adjustment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $adjustment->tracking_no }}</td>
                    <td>{{ showDateTime($adjustment->adjust_date, 'm/d/Y') }}</td>
                    <td>{{ $adjustment->warehouse->name }}</td>
                    <td>{{ $adjustment->adjustmentDetails->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
