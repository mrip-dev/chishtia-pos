@extends('admin.layouts.app')
@section('panel')
    @livewire('admin.customer-transactions.customer-transaction' ,['customerId' => $customer->id])
@endsection