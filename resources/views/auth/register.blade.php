@extends('layouts.berrybenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/theme/css/login.css') }}">
@endsection

@section('content')
<style>
.error-content{width:100%;background:url(assets/img/errorbg.gif) center repeat;}
.error-overlay{background:rgba(255,255,255,.9);width:100%;z-index:1;}
.error-inside{position:relative;z-index:2;padding:110px 0;text-align:center;color:#999;}
.error-logo{text-align:center;}
.error-logo img,.error-inside img{display:inline-block;}
.error-inside h1{font-size:30px;font-weight:lighter;line-height:40px;letter-spacing:2px;}
.error-inside p{font-size:22px;line-height:30px;margin:30px 0;}
.error-inside img{margin-top:40px;padding:20px 0;}
.error-bottom-img{width:25%;}
</style>

<div class="login-wrapper">
    <div class="wrapper">
        <div class="login-wrapper clearfix">
            <div class="login-left register-acc">
                <h1>Baru di Berrybenka ?</h1>
                <form id="form-register" class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                {!! csrf_field() !!}
                    <input class="nama-depan" type="text" placeholder="Nama Depan" required name="customer_fname">
                    <input class="nama-depan" type="text" placeholder="Nama Belakang" required name="customer_lname">
                    <input type="text" placeholder="Alamat Email" required name="customer_email">
                    <input type="password" placeholder="Password (Min. 8 Karakter)" required name="password">
                    <input type="password" placeholder="Ketik Ulang Password" required name="password_confirmation">
                    <p>Password Anda harus terdiri dari 6 sampai 20 karakter.</p>
                    <div class="submit-button"><input id="btn-register" type="submit" value="Buat Akun"></div>
                    <a class="disclaimer" href="#">Dengan membuat akun ini berarti Anda telah setuju dengan <span>Syarat Penggunaan Berrybenka.com</span></a>
                </form>
            </div>
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
</script>
@endsection
