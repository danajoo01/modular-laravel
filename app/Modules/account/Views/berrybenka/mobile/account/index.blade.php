@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        <div class="account-body">
        	<div class="benka-wrapper">
            	<ul>
		<?php /*
                    <li>
                    	<h1 class="border-bot">
                    		<a href="/user/stamp/history">Loyalty Program</a>
                    	</h1>
                    </li>*/?>
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
