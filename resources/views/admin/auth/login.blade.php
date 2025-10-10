@extends('admin.layouts.master')
@section('content')
      <link rel="stylesheet" href="{{asset('assets/admin/css/login.css?v=1')}}">
    <div class="wrapper">
        <div class="logo">
            <img src="{{ siteLogo('light') }}" alt="Logo">
        </div>
        <div class="name">Admin Login</div>

        <form class="p-3 mt-3 verify-gcaptcha login-form" action="{{ route('admin.login') }}" method="POST">
            @csrf

            <div class="form-field">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
            </div>

            <div class="form-field">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            {{-- Captcha --}}
            <div class="mt-3">
                <x-captcha />
            </div>

            <button class="btn mt-4" type="submit">Login</button>
        </form>

        <div class="text-center links">
            <a href="{{ route('admin.password.reset') }}">Forgot Password?</a>
        </div>
    </div>




@endsection