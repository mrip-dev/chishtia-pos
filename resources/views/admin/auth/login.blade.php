@extends('admin.layouts.master')
@section('content')
{{-- <div class="login-main">
    <div class="container custom-container">
        <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-5 col-lg-6 col-md-8 col-sm-11">
                <div class="login-area">
                    <div class="login-wrapper">
                            <div class="login-wrapper__top">
                            <h3 class="title text-white">@lang('Welcome to') <img width="400px" src="{{ siteLogo('dark') }}" alt=""></h3>
                            <i class="text-white">Where Chemistry Meets Commitment!</i>
                        </div>
                        <div class="login-wrapper__body">
                            <form class="cmn-form mt-30 verify-gcaptcha login-form" action="{{ route('admin.login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>@lang('Username')</label>
                                    <input class="form-control" name="username" type="text" value="{{ old('username') }}" required>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between">
                                        <label>@lang('Password')</label>
                                        <a class="forget-text" href="{{ route('admin.password.reset') }}">@lang('Forgot Password?')</a>
                                    </div>
                                    <input class="form-control" name="password" type="password" required>
                                </div>
                                <x-captcha />

                                <button class="btn cmn-btn w-100" type="submit">@lang('LOGIN')</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="container-fluid auth-container">
  <div class="row h-100">
    <div class="col-lg-6 left-side">
      <div class="logo">
        <img src="{{asset('assets/images/logo_icon/logo_light.png')}}" width="140px" alt="">
      </div>
      <img src="{{asset('assets/images/logo_icon/dashboard.png')}}" class="dashboard" alt="Dashboard Mockup">
      <!-- <h2>Easy to use Dashboard</h2>
      <p>Choose the best of product/services and get a bare metal server at the lowest prices.</p> -->
    </div>
    <div class="col-md-6 right-side">
      <div class="form-box">
        <h2 class="mb-4">Welcome To Chishtia Balochi Saggi</h2>
        <form class="cmn-form mt-30 verify-gcaptcha login-form" action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>@lang('Username')</label>
                <input class="form-control" name="username" type="text" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-between">
                    <label>@lang('Password')</label>
                    <a class="forget-text" href="{{ route('admin.password.reset') }}">@lang('Forgot Password?')</a>
                </div>
                <input class="form-control" name="password" type="password" required>
            </div>
            <x-captcha />
            <button type="submit" class="btn cmn-btn w-100">@lang('LOGIN')</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
