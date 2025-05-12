@extends('admin.layouts.app')
@section('panel')
@livewire('admin.supplier-transactions.supplier-transaction' , ['id' => $supplier->id])
@endsection