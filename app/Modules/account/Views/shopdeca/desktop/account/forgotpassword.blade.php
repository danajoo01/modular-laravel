@extends('layouts.shopdeca.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/cart.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/product-detail.css') }}">
<style>
	.error-content{width:100%;background:url(<?php echo asset('shopdeca/desktop/img/errorbg.gif'); ?>) center repeat;}
	.error-overlay{background:rgba(255,255,255,.9);width:100%;z-index:1;}
	.error-inside{position:relative;z-index:2;padding:110px 0;text-align:center;color:#999;}
	.error-logo{text-align:center;}
	.error-logo img,.error-inside img{display:inline-block;}
	.error-inside h1{font-size:30px;font-weight:lighter;line-height:40px;letter-spacing:2px;}
	.error-inside p{font-size:22px;line-height:30px;margin:30px 0;}
	.error-inside img{margin-top:40px;padding:20px 0;}
	.error-bottom-img{width:25%;}
</style>
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
		<!--
            <span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>
Maaf, email anda belum terdaftar</span>
			<span class="success-msg"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>
Password anda berhasil di reset.</span>
        -->
		@if(!empty(Session::get('message')))
			<span class="success-msg"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>{{ Session::get('message') }}</span>
		@endif
		
		@if(!empty(Session::get('error_message')))
			<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>{{ Session::get('error_message') }}</span>
		@endif
			
		{!! Form::open(array('url' => '/forgot_password/post')) !!}
			<div class="forgot" style="font-size:28px; font-weight:bold;">
                <h1>Lupa Password Anda ?</h1>
                <input type="text" name="customer_email" required placeholder="Masukan Email Anda disini">
                <input type="submit" value="Proses">
            </div>
		{!! Form::close() !!}
    </div>
</div>

@endsection
