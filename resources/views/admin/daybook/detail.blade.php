@extends('admin.layouts.app')
@section('panel')
        @livewire('admin.day-book.day-book-detail-component', ['date' => request()->route('date')])
@endsection


