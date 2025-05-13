@extends('admin.layouts.app')
@section('panel')
        @livewire('banks.bank-transaction-details' , ['bankId' => $bank->id])
@endsection


