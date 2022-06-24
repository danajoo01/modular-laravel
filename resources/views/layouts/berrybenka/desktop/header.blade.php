<?php 
$totalcarts = Cart::count();
$carts = Cart::content();
$generate_uri_segment = generate_uri_segment();
$date_logo = date('Y-m-d');


//********* START Cookies involve-asia ***********//
//** Created by Effendy. 
//** Seo Helper utmInvolveAsia(); 
utmInvolveAsia();
if(isset($_COOKIE['iasia_utmz'])){
  $iasia = json_decode($_COOKIE['iasia_utmz']);
}

utmHasoffers(); 
if(isset($_COOKIE['hasoffers_utmz'])){
  $hasoffers = json_decode($_COOKIE['hasoffers_utmz']);  
}
//********* END Cookies hasoffers ***********//
?>

<style>
.menu-aktif{
    color: #EC3D40;
}
.login-fb{
    background: #425F9C;
    color: #fff;
    width: 100%;
    text-transform: uppercase;
    width: 100%;
    padding: 15px 0;
    border: none;
    border-radius: 2px;
    margin: 10px 0;
}
</style>

<header>
    <div class="header-left">
        <a href="#" class="menu-mobile"><i class="fa fa-bars" aria-hidden="true"></i></a>
        <nav id="all-menu">

            <div class="menu-woman">
                {!! mega_menu('women') !!}
            </div>

            <?php /* div class="menu-men" style="display: none;">
                {!! mega_menu('men') !!}
            </div> */ ?>

        </nav>
    </div>
    <div class="header-mid">
        <a class="logo" href="{{ URL::to('/') }}">
           <?php /* 
	   @if($date_logo < "2018-01-01")
                <img class="black-logo" src="{{ asset('berrybenka/desktop/img/berrybenka.png') }}">
                <img class="white-logo" src="{{ asset('berrybenka/desktop/img/berrybenka-white.png') }}">
            @elseif($date_logo >= "2018-01-01" && $date_logo < "2018-01-09")
                <img class="black-logo" src="{{ asset('berrybenka/desktop/img/berrybenka.png') }}">
                <img class="white-logo" src="{{ asset('berrybenka/desktop/img/berrybenka-white.png') }}">
            @else
                <img class="black-logo" src="{{ asset('berrybenka/desktop/img/berrybenka.png') }}">
                <img class="white-logo" src="{{ asset('berrybenka/desktop/img/berrybenka-white.png') }}">
            @endif
*/?>
		<img class="black-logo" src="{{ asset('logo-s2g.png') }}">
                <img class="white-logo" src="{{ asset('logo-s2g.png') }}">
        </a>
    </div>


    <div class="header-right">
        <ul>
            <!-- @if ($generate_uri_segment['gender'] == 'men')
                <li><a href="#" gender="pria" onclick="ChangeMenu(this)" id="togle-pria" class="menu-aktif">Pria</a></li>
                <li><a href="#">|</a></li>
                <li><a href="#" gender="wanita" onclick="ChangeMenu(this)" id="togle-wanita">Wanita</a></li>
            @else 
                <li><a href="#" gender="pria" onclick="ChangeMenu(this)" id="togle-pria">Pria</a></li>
                <li><a href="#">|</a></li>
                <li><a href="#" gender="wanita" onclick="ChangeMenu(this)" id="togle-wanita" class="menu-aktif">Wanita</a></li>
            @endif -->

            @if(!empty(Auth::user()))
                <li><a href="/user/account_dashboard">{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</a></li>
            @else
                <li><a href="#" class="login-trigger">Masuk / daftar</a></li>
            @endif
            <li><a href="#search-wrapper" class="search-trigger"><i class="fa fa-search" aria-hidden="true"></i></a></li>
            <li><a href="#" class="add2cart"><i class="fa fa-shopping-bag"><span id="tot_itm">@if($totalcarts > 0) ({{ $totalcarts }}) @endif</span></i></a></li>
            <input type="hidden" name="itm_tot" id="itm_tot" value="{{ $totalcarts }}">

            <?php /*
            @if(!empty(Auth::user()))
                <li><a href="#" class="add2cart"><i class="fa fa-shopping-bag"><span id="tot_itm">@if($totalcarts > 0) ({{ $totalcarts }}) @endif</span></i></a></li>
                <input type="hidden" name="itm_tot" id="itm_tot" value="{{ $totalcarts }}">
            @else
                <li><a href="/checkout/cart/" class="add2cart"><i class="fa fa-shopping-bag"><span id="tot_itm">@if($totalcarts > 0) ({{ $totalcarts }}) @endif</span></i></a></li>
                <input type="hidden" name="itm_tot" id="itm_tot" value="{{ $totalcarts }}">
            @endif
            */ ?>
        </ul>
    </div>

    <nav class="mobile-menu-nav">
        <div class="menu-woman">
            {!! mega_menu('women') !!}
       </div>
       <?php /* <div class="menu-men" style="display: none;">
            {!! mega_menu('men') !!}
       </div> */ ?>
    </nav>
</header>

<?php // @if(empty(Auth::user())) ?>
<div class="login-wrapper">
    <div class="login-outer">
        <h1>login</h1>
        @if(get_uri() !== FALSE)
            <form id="form-login-route" class="form-horizontal" role="form" method="POST" action="{{ url('/login?continue=' . urlencode(get_uri())) }}">
        @else
            <form id="form-login-route" class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
        @endif
        {!! csrf_field() !!}
        <div class="login-body">
            <input class="text-field" type="email" name="customer_email" placeholder="email">
            <input class="text-field" type="password" name="password" placeholder="password">
            <input type="submit" value="masuk">
            <a href="/auth/facebook" class="login-fb">
                masuk dengan facebook
            </a>
            <a href="/forgot_password">Lupa Password Anda ?</a>
        </div>
        @if(get_uri() !== FALSE)
            <a href="/login?continue={{ urlencode(get_uri()) }}" class="register-link">belum punya akun berrybenka?? <span>buat baru.</span></a>
        @else
            <a href="/login" class="register-link">belum punya akun berrybenka?? <span>buat baru.</span></a>
        @endif 
        </form>
    </div>
</div>
<?php // @endif ?>

<div class="search-wrapper">
    <form id="searching" action="/search" method="POST">
        <div class="search-field">
            <input type="text" class="search-textfield" placeholder="cari produk" id="keyword" name="keyword" onfocus="searchSolr(this.id,'searching')" onkeyup="search_bb($(this).val())" url="/home/search">
            <input type="hidden" name="type" id="type"/>
            <input type="hidden" name="filter" id="filter"/>
            <input type="hidden" name="url" id="url"/>
            <input type="hidden" name="parent" id="parent"/>
            <input type="hidden" name="keywords" id="keywords"/>
            <input type="hidden" name="names" id="names"/>
            <input type="hidden" name="gender" id="gender"/>
            {!! csrf_field() !!}
            <a href="#" class="close-search"><i class="fa fa-times" aria-hidden="true"></i></a>
        </div>
    </form>
</div>

<div class="cart-list-wrapper">
    <div class="cart-list show-cart-list">
        <h1>Tas Belanja Anda</h1>
        <ul id="bags-list">
            @if ($totalcarts > 0)
                @foreach ($carts as $row)
                <li>
                    <a href="#">
                        <img src="{{ IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH . $row->options->image }}">
                        <div class="cart-list-detail">
                            <?php /*<h1>{{ $row->options->brand_name }}</h1> */?>
                            <h2>{{ $row->name }}</h2>
                            <p><span>color</span>: {{ $row->options->color_name }}</p>
                            <p><span>Size</span>: {{ $row->options->size }}</p>
                            <p><span>Quantity</span>: {{ $row->qty }}</p>
                            <p class="price">IDR {{ number_format(($row->price), 0, '.', '.') }}</p>
                        </div>
                    </a>
                </li>
                @endforeach
            @else 
                <li>Tas Belanja Anda Kosong.</li>
            @endif
        </ul>
        <a href="/checkout/cart" class="addtocart" @if ($totalcarts == 0) style="display: none;" @endif>pembayaran</a>
    </div>
</div>


<!-- <div class="bottom-header" style="position: absolute;width: 100%; top: 80px;">
    <div class="wrapper">
        <nav id="tabs">
            <div class="right-item-wrapper">
                
            </div>
        </nav>
    </div>
</div> -->
