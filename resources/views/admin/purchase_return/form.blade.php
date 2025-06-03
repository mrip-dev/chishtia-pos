@extends('admin.layouts.app')
@section('panel')
    @if(isset($purchaseReturn))
        @livewire('admin.purchase-management.all-return-purchases', ['purchaseId' => $purchase->id, 'purchaseReturnId' => $purchaseReturn->id])
    @else
        @livewire('admin.purchase-management.all-return-purchases', ['purchaseId' => $purchase->id])
    @endif
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.purchase.return.index') }}" />
@endpush
