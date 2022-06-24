@extends('layouts.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/catalog-list.css') }}">
@endsection

@section('content')

<div class="user-wrapper clearfix">
    <div class="wrapper">
        <div class="user-wrap">
            {!! get_view('account', 'account.leftmenu', array('page'=>'index','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard">
                    <h1 class="clearfix">
                        <i class="fa fa-dashboard"></i>Halaman Akun
                        <div class="right last-login"><b>Terakhir Login : </b>{{ indonesian_date(strtotime($user->last_login_date),'l, j F Y H:i:s') }}</div>
                    </h1>

                    <div class="user-dashboard-content">
                        <div class="stamp-wrapper">
                            <div class="stamp-menu">
                                {!! get_view('account', 'account.stampmenu', array('page'=>'faq')) !!}                                                    
                            </div>
                            <div class="stamp-faq">
                            	<h2>Benka Stamp FAQ</h2>
                                <ul>
                                    @foreach($faq as $key => $val)
                                    <li>
                                        <a>{{ $key }}</a>
                                        <div>{{ $val[0] }}</div>
                                    </li>
                                    @endforeach
                                    
                                </ul>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection