<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title"  content="shopdeca">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#f0f0f0">
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('shopdeca/mobile/img/favicon.png') }}" />
<title>{{ ucwords($domain_name) }}</title>
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/reset.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/core.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/script/accordion-nav/accordion.css') }}">

</head>

<div class="login-wrapper">
	<div class="mid-login">
    	<div class="mid-login-left">
        	<div class="login-logo">
                <img class="circle-logo" src="{{ $logo_path }}" alt="">
                <img src="{{ $title_path }}" alt="">
            </div>
            <div class="form-login forgot-password">
			@if(!empty(Session::get('message')))
				<div class="success-msg-login">{{ Session::get('message') }}</div>
			@endif
		
			@if(!empty(Session::get('error_message')))
				<div class="error-msg-login"> {{ Session::get('error_message') }}</div>
			@endif
								
			{!! Form::open(array('url' => '/forgot_password/post')) !!}
                <span><i class="fa fa-user" aria-hidden="true"></i><input type="text" name="customer_email" required placeholder="Email"></span>
                <input type="submit" value="proses">
			{!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script src="//code.jquery.com/jquery-1.10.2.js"></script> 
<!-- <script src="{{ asset('shopdeca/theme/script/sidebar-fixed.js') }}"></script> -->

<!-- BB JS -->

<script src="{{ asset('shopdeca/mobile/script/accordion-nav/accordion.js') }}"></script>
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/script/flexslider/flexslider.css') }}">
<script src="{{ asset('shopdeca/mobile/script/flexslider/jquery.flexslider.js') }}"></script>
<script src="{{ asset('shopdeca/mobile/script/core.js') }}"></script>