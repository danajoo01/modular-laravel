@extends('layouts.hijabenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/user.css?t=').date('YmdHis') }}">
@endsection

@section('content')
<div id="fb-root"></div>
<div class="user-wrapper clearfix">
    <div class="wrapper">
    	<div class="user-wrap">
        	{!! get_view('account', 'account.leftmenu', array('page'=>'setting','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-cog" aria-hidden="true"></i>Pengaturan
                    </h1>
                    <div class="order-content">
                    	<ul class="tabs">
                        	<li><a href="{{ URL::to('/user/setting') }}"><i class="fa fa-map-marker" aria-hidden="true"></i>Ubah Alamat</a></li>
                            <li><a href="{{ URL::to('/user/change_password') }}" class="active"><i class="fa fa-lock" aria-hidden="true"></i>Ubah Password</a></li>
                            <li><a href="{{ URL::to('/user/edit_personal_detail') }}"><i class="fa fa-user" aria-hidden="true"></i>Ubah Data</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">						
						{!! Form::open(array('url' => '/user/update_password')) !!}
                        <div class="pass-change" id="ubah-password">
							<div class="pass-change-wrapper">
								@if(!empty(Session::get('message')))
									{!! show_message(Session::get('message')) !!}
								@endif
		
								@if(!empty(Session::get('error')))
									{!! error_message(Session::get('error')) !!}
								@endif
                            	
								<label for="">Password Baru*</label>
								{!! Form::password('password', array('required')) !!}<br>
								<label for="">Ketik Ulang Password Baru</label>
                                {!! Form::password('password_confirmation', array()) !!}<br>
								<input type="submit" value="Ubah Password">
                            </div>
                        </div>
                       {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!--<script src="{{ asset('hijabenka/desktop/theme/script/tab.js') }}"></script>-->
@endsection