<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title"  content="Berrybenka">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#f0f0f0">
<title>{{ $title }}</title>
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/reset.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/core.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/login.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/script/accordion-nav/accordion.css') }}">

</head>

<body>
<div class="login-wrapper">
	<div class="mid-login">
    	<div class="mid-login-left">
        	<div class="login-logo">
                <img class="circle-logo" src="{{ $logo_path }}" alt="">
                <img src="{{ $title_path }}" alt="">
            </div>
            <div class="form-login">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                    {!! csrf_field() !!}

                    @if(!empty(Session::get('login_error')))
                        <div class="error-msg-login">{{ Session::get('login_error') }}</div>
                    @endif

                    @if(!empty(Session::get('error_message')))
                        <span class='error-msg-login'>
                            <i aria-hidden='true' class='fa fa-bell'></i>
                            <i aria-hidden='true' class='fa fa-times'></i>
                            {{ session('error_message') }}
                        </span>
                    @endif
                    @if(!empty(Session::get('success_message')))
                        <span class='error-msg-login' style="background:#B3E0B8 !important; border:1px solid #7CAD81 !important; color:#027C0E !important;">
                            <i aria-hidden='true' class='fa fa-bell' style="color:#027C0E !important;"></i>
                            <i aria-hidden='true' class='fa fa-times' style="color:#027C0E !important;"></i>
                            {{ session('success_message') }}
                        </span>
                    @endif

                    @if(!empty($continue)) 
                        <input type="hidden" name="continue" value="{{ $continue }}">
                    @endif
                    <span>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="Email" required name="customer_email">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Password" required name="password">
                    </span>
                    <input type="submit" value="Login">
                  
                    
                    <?php 
                    $get_domain     = get_domain();      
                    $domain_name    = isset($get_domain['domain_name']) ? $get_domain['domain_name'] : 'Berrybenka';
                    if(isset($get_domain['domain_id'])){
                        switch($get_domain['domain_id']){
                            case 3 :
                                echo '';
                                break;
                            default :
                                echo '<div class="fb-login"><a href="/auth/facebook"><input type="button" value="Login Dengan Facebook"></a></div>  ';                                
                        }
                    }else{
                        echo '<div class="fb-login"><a href="/auth/facebook"><input type="button" value="Login Dengan Facebook"></a></div> ';
                    }
                    
                    ?>
                    
                    <a href="{{ URL::to('/forgot_password') }}" class="forgot-pass">Lupa Password Anda ?</a>
                    <a href="#" class="new-cust"><p>Belum Punya Akun {{ ucfirst($domain_name) }}?</p><p>Buat Baru</p></a>
                </form>
            </div>
        </div>
        
        <div class="mid-login-right">
        	<div class="login-logo">
                <img class="circle-logo" src="{{ $logo_path }}" alt="">
                <img src="{{ $title_path }}" alt="">
            </div>
            <div class="form-login form-sign-up">
                <form id="form-register" class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                    {!! csrf_field() !!}
                    @if(!empty(Session::get('error')))
                        {!! error_message(Session::get('error')) !!}
                    @endif
                    @if(!empty($continue)) 
                        <input type="hidden" name="continue" value="{{ $continue }}">
                    @endif
                    
                    <span>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="Nama Depan" required name="customer_fname">
                    </span>
                    <span>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <input type="text" placeholder="Nama Belakang" required name="customer_lname">
                    </span>
                    <span>
                        <i class="fa fa-envelope" aria-hidden="true"></i>
                        <input type="text" placeholder="Email" required name="customer_email">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Password (Min. 8 Karakter)" required name="password">
                    </span>
                    <span>
                        <i class="fa fa-lock" aria-hidden="true"></i>
                        <input type="password" placeholder="Ketik Ulang Password" name="password_confirmation">
                    </span>
                    <input id="btn-register" type="submit" value="Buat Akun">
                </form>
                <a href="#" class="b2log"><p>Kembali Ke Halaman Login</p></a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<script src="//code.jquery.com/jquery-1.10.2.js"></script> 
<script src="{{ asset('berrybenka/mobile/script/jquery.cookie.js') }}"></script> 
<!-- <script src="{{ asset('berrybenka/theme/script/sidebar-fixed.js') }}"></script> -->

<!-- BB JS -->

<script src="{{ asset('berrybenka/mobile/script/accordion-nav/accordion.js') }}"></script>
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/script/flexslider/flexslider.css') }}">
<script src="{{ asset('berrybenka/mobile/script/flexslider/jquery.flexslider.js') }}"></script>
<script src="{{ asset('berrybenka/mobile/script/core.js') }}"></script>
<script src="{{ asset('js/mobile/app.js') }}"></script>

<script>
  $(document).ready(function () {
    $("#form-register").submit(function () {
      $("#btn-register").attr("disabled", true);
      return true;
    });
  });
</script>
