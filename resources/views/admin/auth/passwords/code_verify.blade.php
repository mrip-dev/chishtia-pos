@extends('admin.layouts.master')
@section('content')
   <link rel="stylesheet" href="{{asset('assets/admin/css/login.css?v=1')}}">
 <div class="wrapper">
    <div class="logo">
        <img src="{{ siteLogo('light') }}" alt="Logo">
    </div>

    <div class="name">@lang('Verify Code')</div>
    <p class="text-muted small">@lang('Please check your email and enter the verification code you got in your email.')</p>

    <form class="p-3 mt-3 login-form" action="{{ route('admin.password.verify.code') }}" method="POST">
        @csrf

        <div class="form-field">
            <i class="fas fa-key"></i>
            <input type="text" name="code" maxlength="6" placeholder="@lang('Enter Verification Code')" required>
        </div>

        <button class="btn mt-4" type="submit">@lang('Submit')</button>
    </form>

    <div class="text-center links mt-3">
        <a href="{{ route('admin.password.reset') }}">@lang('Try to send again')</a>
        <br>
        <a href="{{ route('admin.login') }}">
            <i class="las la-sign-in-alt"></i> @lang('Back to Login')
        </a>
    </div>
</div>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/verification_code.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';
            $('[name=code]').on('input', function() {

                $(this).val(function(i, val) {
                    if (val.length == 6) {
                        $('form').find('button[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                        $('form').find('button[type=submit]').removeClass('disabled');
                        $('form')[0].submit();
                    } else {
                        $('form').find('button[type=submit]').addClass('disabled');
                    }
                    if (val.length > 6) {
                        return val.substring(0, val.length - 1);
                    }
                    return val;
                });

                for (let index = $(this).val().length; index >= 0; index--) {
                    $($('.boxes span')[index]).html('');
                }
            });

        })(jQuery)
    </script>
@endpush
@push('style')
    <style>
        .cmn-btn.disabled,
        .cmn-btn:disabled {
            color: #fff;
            background-color: #3d2bfb;
            border-color: #3d2bfb;
            opacity: 0.7;
        }
    </style>
@endpush
