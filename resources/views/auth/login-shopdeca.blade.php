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
                    If you need help, please contact our customer service<br><strong>Working Hours, Monday-Friday(9.00-18.00)</strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-phone"></i>
                    </span> Contact <br> <strong>021-2520555</strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-envelope"></i>
                    </span> Email <br> <strong><a href="mailto:cs@berrybenka.com">cs@berrybenka.com</a></strong>
                </li>
                <li>
                    <span class="icon"> <i class="fa fa-comments"></i>
                    </span> SMS <br> <strong>0812 8880 9992</strong>
                </li>
            </ul>
        </div>

        <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
        {!! csrf_field() !!}
        
        @if(!empty(Session::get('login_error')))
            {!! show_message(Session::get('login_error'),1) !!}
        @endif
        @if(!empty(Session::get('error')))
            {!! error_message(Session::get('error')) !!}
        @endif

        @if(!empty($continue)) 
            <input type="hidden" name="continue" value="{{ $continue }}">
        @endif

        <div class="login-inside">
            <h1> Pengunjung terdaftar? Silahkan masuk dengan Shopdeca Account </h1>
            <div class="login">
                <div class="username">
                    <label>Email</label>
                    <input type="text" placeholder="Alamat Email" required name="customer_email">
                </div>
                <div class="password">
                    <label>Password</label>
                    <input type="password" placeholder="Password" required name="password">
                </div>
                <a class="forgot-pass" href="{{ URL::to('/forgot_password') }}">Lupa Password Anda ?</a>
                <div class="submit-login">
                    <div class="log-button"><input type="submit" value="Masuk" name="login"></div>
<!--                    <div class="log-fb">
                        <a href="/auth/facebook"> 
                            <input type="button" value="Masuk dengan Facebook" name="fb_login"> 
                        </a>
                    </div>-->
                </div>
            </div>
        </div>
        </form>

        <div class="or-wrapper"><div class="or-line"><div class="or"><span>Atau</span></div></div></div>
        <div class="login-inside">
            <form id="form-register" class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
            {!! csrf_field() !!}
                <h1> Daftar Akun Baru</h1>
                <div class="login">
                    @if(!empty($continue)) 
                        <input type="hidden" name="continue" value="{{ $continue }}">
                    @endif
                    <div class="username regis-name">
                        <label>Nama*</label>
                        <input class="nama-depan" type="text" placeholder="Nama Depan" required name="customer_fname">
                        <input class="nama-depan" type="text" placeholder="Nama Belakang" required name="customer_lname">
                    </div>
                    <div class="password regis-email">
                        <label>Email</label>
                        <input type="text" placeholder="email@contoh.com / email.anda@contoh.com" required name="customer_email">
                    </div>
                    <div class="password regis-email">
                        <label>Password</label>
                        <input type="password" placeholder="Min 8 Character" required name="password">
                    </div>
                    <div class="password regis-email" required>
                        <label>Ketik Ulang Password</label>
                        <input type="password" name="password_confirmation">
                    </div>
                    <div class="submit-login">
                        <div class="log-button"><input id="btn-register" type="submit" value="Buat Akun" name="register"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
  $(document).ready(function () {
    $("#form-register").submit(function () {
      $("#btn-register").attr("disabled", true);
      return true;
    });
  });
// $("input[name='fb_login']").click(function() {
//     url = window.location.origin + 'facebook';

//     window.location.replace(url);
// });
</script>
@endsection
