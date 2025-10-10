@extends('admin.layouts.app')
@section('panel')
        <livewire:admin.sale-management.view-sale :sale-id="$saleId" />
@endsection


