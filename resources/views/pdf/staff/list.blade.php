@extends('pdf.layouts.master')

@section('main-content')
    <table class="table table--light style--two">
        <thead>
            <tr>
                <th>@lang('S.N.')</th>
                <th>@lang('Name')</th>
                <th>@lang('Username')</th>
                <th>@lang('E-mail')</th>
                <th>@lang('Mobile')</th>
                <th>@lang('Status')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffs as $staff)
                <tr>
                    <td> {{ $loop->iteration }} </td>
                    <td> {{ $staff->name }} </td>
                    <td> <span class="fw-bold"> {{ $staff->username }}</span></td>
                    <td> {{ $staff->email }} </td>
                    <td>+{{ $staff->mobile }} </td>
                    <td>
                        @php
                            echo $staff->statusBadge;
                        @endphp
                    </td>

                </tr>
            @empty
                <tr>
                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
