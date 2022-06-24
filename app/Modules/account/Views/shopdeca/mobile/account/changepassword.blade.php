@extends('layouts.shopdeca.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/account.css') }}">
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
                    <li>
                        <h1 class="border-bot b-gratis">
                            <a href="#">Ubah Password</a>
                            <i aria-hidden="true" class="fa fa-angle-down"></i>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/account_dashboard">Akun Saya</a>
                            </h1>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/order_history">Daftar Pemensanan</a>
                            </h1>
<!--                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/referral_program">Belanja Gratis Shopdeca</a>
                            </h1>-->
                        </h1>
                    </li>
                </ul>
                <div class="change-pass">

                    @if(!empty(Session::get('message')))
                        <div class="success-msg-login">{{ Session::get('message') }}</div>
                    @endif
                
                    @if(!empty(Session::get('error_message')))
                        <div class="error-msg-login"> {{ Session::get('error_message') }}</div>
                    @endif

                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/user/update_password') }}">
                        {!! csrf_field() !!}
                        {!! Form::password('password', array('required'=>'required', 'placeholder'=>'Masukan Password Baru')) !!}
                        {!! Form::password('password_confirmation', array('placeholder'=>'Ketik Ulang Password Baru')) !!}
                        <input type="submit" value="Ubah Password">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!--<script src="{{ asset('shopdeca/desktop/theme/script/tab.js') }}"></script>-->
@endsection