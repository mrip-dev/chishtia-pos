@extends('admin.layouts.app')
@section('panel')
   @livewire('admin.services.manage-stock-details',['modelType' => 'stock'])
@endsection


