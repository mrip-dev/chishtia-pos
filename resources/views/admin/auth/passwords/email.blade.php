@extends('admin.layouts.master')
@section('content')
   <link rel="stylesheet" href="{{asset('assets/admin/css/login.css?v=1')}}">
<div class="wrapper">
    <div class="logo">
        <img src="{{ siteLogo('light') }}" alt="Logo">
    </div>

    <div class="name">@lang('Recover Account')</div>

    <form class="p-3 mt-3 login-form verify-gcaptcha" action="{{ route('admin.password.reset') }}" method="POST">
        @csrf

        <div class="form-field">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="@lang('Email')" value="{{ old('email') }}" required>
        </div>

        {{-- Captcha --}}
        <div class="mt-3">
            <x-captcha />
        </div>

        <button class="btn mt-4" type="submit">@lang('Submit')</button>
    </form>

    <div class="text-center links">
        <a href="{{ route('admin.login') }}">
            <i class="las la-sign-in-alt"></i> @lang('Back to Login')
        </a>
    </div>
</div>
@endsection


