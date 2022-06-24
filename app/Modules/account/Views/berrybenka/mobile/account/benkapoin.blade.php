@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
    	<div class="account-header">
        	<h1>{{ $user->customer_fname.' '.$user->customer_lname }}</h1>
            <a href="#">Point Anda : IDR {{ number_format($user->customer_credit,0,".",".") }} </a>
        </div>
        <div class="account-body">
        	<div class="benka-wrapper">
            	<ul>
<!--                	<li>
                    	<h1 class="border-bot b-gratis">
                    		<a href="/user/referral_program">belanja gratis berrybenka</a>
                    	</h1>
                    </li>-->
                    <li>
                    	<h1 class="border-bot">
                    		<a href="/user/order_history">Daftar Pemesanan</a>
                    	</h1>
                    </li>
                    <li>
                    	<h1 class="border-bot">
                    		<a href="/user/change_password">Ganti Password</a>
                    	</h1>
                    </li>
                </ul>
            </div>
<?php /*
            <div class="bb-app">
            	<p>Download Aplikasi Berrybenka di Smartphone Anda</p>
                <ul class="clear">
                	<li><a href="https://itunes.apple.com/id/app/berrybenka/id961924940?l=id&mt=8"><img src="{{ asset('berrybenka/desktop/img/apple.gif') }}" alt=""></a></li>
                    <li><a href="https://play.google.com/store/apps/details?id=com.berrybenka.android&hl=en"><img src="{{ asset('berrybenka/desktop/img/google.gif') }}" alt=""></a></li>
                </ul>
            </div>*/?>
            <a href="/logout" class="logout">Logout</a>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection
