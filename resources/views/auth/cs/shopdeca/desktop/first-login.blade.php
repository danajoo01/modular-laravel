@extends('layouts.shopdeca.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/cart.css') }}">
@endsection

@section('content')
<div class="login-wrapper">
    <div class="wrapper">
        <div class="cart-help">
            <ul class="need-help">
                <li>
                    <span class="icon"> <i class="fa fa-info-circle"></i></span>
                    Butuh Bantuan? Hubungi Customer Service Kami<br><strong>Jam Kerja, Senin - Jumat (9.00 - 18.00) / Sabtu - Minggu (8.00 - 17.00)</strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-phone"></i>
                    </span> Kontak Kami <br> <strong>021 2520555</strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-envelope"></i>
                    </span> Email Kami <br> <strong><a href="mailto:cs@berrybenka.com">cs@berrybenka.com</a></strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-comments"></i>
                    </span> SMS Kami <br> <strong>0812 8880 9992</strong>
                </li>
            </ul>
        </div>

        <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/auth_cs') }}">
        {!! csrf_field() !!}

        @if(!empty(Session::get('login_error')))
            {!! show_message(Session::get('login_error')) !!}
        @endif
        @if(!empty(Session::get('error')))
            {!! error_message(Session::get('error')) !!}
        @endif

        @if(!empty($continue))
            <input type="hidden" name="continue" value="{{ $continue }}">
        @endif

        <div class="login-inside">
            <h1> Silahkan Masuk Dengan Account User Yang Telah Disediakan </h1>
            <div class="login">
                <div class="username">
                    <label>Email</label>
                    <input type="text" placeholder="Alamat Email" required name="customer_email">
                </div>

                <div class="password">
                    <label>Password</label>
                    <input type="password" placeholder="Password" required name="password">
                </div>

                <div class="submit-login">
                    <div class="log-button"><input type="submit" value="Masuk" name="login"></div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

@endsection
