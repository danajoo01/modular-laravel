<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title"  content="berrybenka">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#f0f0f0">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('berrybenka/mobile/img/favicon.png') }}" />
    <title>{{ $title }}</title>

    <link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/core.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/mobile/script/accordion-nav/accordion.css') }}">
</head>

<body>
<div class="login-wrapper">
    <div class="mid-login">
        <div class="mid-login-left">
            <div class="login-logo">
                <img class="circle-logo" src="{{ $logo_path }}" alt="">
                <img src="{{ $title_path }}" alt="">
            </div>
            <div class="form-login">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/auth_cs') }}">
                    {!! csrf_field() !!}

                    @if(!empty(Session::get('login_error')))
                        <div class="error-msg-login">{{ Session::get('login_error') }}</div>
                    @endif

                    @if(!empty($continue))
                        <input type="hidden" name="continue" value="{{ $continue }}">
                    @endif
                    <span>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="Email" required name="customer_email">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Password" required name="password">
                    </span>
                    <input type="submit" value="Login">
                    {{--<a href="{{ URL::to('/forgot_password') }}" class="forgot-pass">Lupa Password Anda ?</a>
                    <a href="#" class="new-cust"><p>Belum Punya Akun Berrybenka?</p><p>Buat Baru</p></a>--}}
                </form>
            </div>
        </div>

        <div class="mid-login-right">
            <div class="login-logo">
                <img class="circle-logo" src="{{ $logo_path }}" alt="">
                <img src="{{ $title_path }}" alt="">
            </div>
            <div class="form-login form-sign-up">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                    {!! csrf_field() !!}
                    @if(!empty(Session::get('error')))
                        {!! error_message(Session::get('error')) !!}
                    @endif
                    <span>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="Nama" required name="customer_fname">
                        <input type="hidden" name="customer_lname" value="-">
                    </span>
                    <span>
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <input type="text" placeholder="Email" required name="customer_email">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Password" required name="password">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Ketik Ulang Password" name="password_confirmation">
                    </span>
                    <input type="submit" value="Buat Akun">
                </form>
                <a href="#" class="b2log"><p>Kembali Ke Halaman Login</p></a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="{{ asset('berrybenka/mobile/script/jquery.cookie.js') }}"></script>
<!-- <script src="{{ asset('berrybenka/theme/script/sidebar-fixed.js') }}"></script> -->

<!-- BB JS -->

<script src="{{ asset('berrybenka/mobile/script/accordion-nav/accordion.js') }}"></script>
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/script/flexslider/flexslider.css') }}">
<script src="{{ asset('berrybenka/mobile/script/flexslider/jquery.flexslider.js') }}"></script>
<script src="{{ asset('berrybenka/mobile/script/core.js') }}"></script>
<script src="{{ asset('js/mobile/app.js') }}"></script>
