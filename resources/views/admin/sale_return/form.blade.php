@extends('admin.layouts.app')
@section('panel')
    @if(isset($saleReturn))
        @livewire('admin.sale-management.all-return-sales', ['saleId' => $sale->id, 'saleReturnId' => $saleReturn->id])
    @else
        @livewire('admin.sale-management.all-return-sales', ['saleId' => $sale->id])
    @endif
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.sale.return.index') }}" />
@endpush