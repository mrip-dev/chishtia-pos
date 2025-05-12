@extends('admin.layouts.app')
@section('panel')
@livewire('admin.ware-house.ware-house-detail', ['id' => $warehouse->id])
@endsection