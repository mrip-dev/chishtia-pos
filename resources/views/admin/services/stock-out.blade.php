@extends('admin.layouts.app')
@section('panel')
@livewire('admin.services.manage-stock', ['type' => 'out'])
@endsection


