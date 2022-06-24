<?php 
$totalcarts = Cart::count();
$carts = Cart::content();
$generate_uri_segment = generate_uri_segment();



//********* START Cookies involve-asia ***********//
//** Created by Effendy. 
//** Seo Helper	utmInvolveAsia(); 
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

<header>
    <div class="top-header">
        <div class="wrapper">
            <a class="logo" href="{{ URL::to('/') }}"><img src="{{ asset('shopdeca/desktop/img/shopdeca.png') }}"></a>
            <div class="top-right-header right">
                <ul>
                    <li class="user-dd">
                        @if(!empty(Auth::user()))
                            <a href="#">{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</a>
                            <div class="user-wrappers">
                                <ul>
                                    <!--<li><a href="/user/account_dashboard">Halaman Akun</a></li>-->
                                    <!--<li><a href="/user/referral_program">Belanja Gratis</a></li>-->
                                    <li><a href="/user/wishlist">Wishlist</a></li>
                                    <li><a href="/user/order_history">Order Anda</a></li>
                                    <li><a href="/user/setting">Pengaturan</a></li>
                                    <li><a href="/logout">Logout</a></li>
                                </ul>
                            </div>
                        @else
							<!-- <a href="/login">Masuk / Daftar</a> -->
                            <a href="#" class="q-log-triger">Masuk / Daftar</a>
                            <div class="q-login">
                                <h1>Login</h1>
                                @if(get_uri() !== FALSE)
                                    <form id="form-login-route" class="form-horizontal" role="form" method="POST" action="{{ url('/login?continue=' . urlencode(get_uri())) }}">
                                @else
                                    <form id="form-login-route" class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
                                @endif
                                 {!! csrf_field() !!}
                                <div class="q-login-content">
                                        <input type="text" name="customer_email" placeholder="Email / Username">
                                        <input type="password" name="password" placeholder="Password" class="q-logpass">
                                        <!--<input type="checkbox" name="remember" id="rememberme"><label for="rememberme" class="remember"><p>Ingatkan Saya</p></label>-->
                                        <div class="clear"></div>
                                        <input type="submit" value="masuk" class="qlogin-btn">
<!--                                        <a href="/auth/facebook" class="fb-a">
                                            <input type="button" value="Masuk dengan Facebook" class="q-fb">
                                        </a>-->
                                        <a href="/forgot_password">Lupa Password Anda ?</a>
                                        @if(get_uri() !== FALSE)
                                            <a id="register-route" href="/login?continue={{ urlencode(get_uri()) }}" class="q-regis">Belum Punya Akun Shopdeca? <span>Buat Baru.</span></a>
                                        @else
                                            <a id="register-route" href="/login" class="q-regis">Belum Punya Akun Shopdeca? <span>Buat Baru.</span></a>
                                        @endif  
                                </div>
                                </form>
                            </div>
                        @endif
                    </li>
                    <li>|</li>
                    <li>
                        <a href="/checkout/cart/">
                            <i class="fa fa-shopping-bag"></i>
                            <span class="tas-belanja">Tas Belanja Saya</span>
                            <span class="total-checkout" id="tot_itm">{{ $totalcarts }}</span>
                            <input type="hidden" name="itm_tot" id="itm_tot" value="{{ $totalcarts }}">
                        </a>
                    </li>
                    <li>
                        <a class="chekout-arrow" href="#"><i class="fa fa-angle-down"></i></a>
                        <div class="nav-checkout">                      
                            <ul id="bags-list">
                            @if ($totalcarts > 0) 
                                @foreach ($carts as $row)
                                    <li id="bags-item">
                                        <div class="nav-check-img left"><img src="{{ IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH . $row->options->image }}"></div>
                                        <div class="nav-check-detail left">
                                            <div class="item-info">
                                                <h4>{{ $row->name }}</h4>
                                                <small>{{ $row->options->brand_name }}</small>
                                                <div class="detail">
                                                    <b>Color</b><p>: {{ $row->options->color_name }}</p>
                                                </div>
                                                <div class="detail">
                                                    <b>Size</b><p>: {{ $row->options->size }}</p>
                                                </div>
                                                <div class="detail">
                                                    <b>QTY</b><p>: {{ $row->qty }}</p>
                                                </div>
                                                <div class="detail">
                                                    <strong>IDR {{ number_format(($row->price), 0, '.', '.') }}</strong>
                                                </div>
                                             </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li id="bags-item">Tas Belanja Anda Kosong</li>
                            @endif  
                            </ul>
                            <a href="/checkout/cart" class="gotocheck">pembayaran</a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bottom-header">
        <div class="wrapper">
            <nav id="tabs" style='padding: 20px 0px;'>
                <a href="{{ URL::to('/') }}" class="logo-small left"><img class="bb-logo" src="{{ asset('shopdeca/desktop/img/shopdeca-logo.png') }}"></a>
                <div class="menu-switcher left" style="display:none;">
                    <ul>
					@if ($generate_uri_segment['gender'] == 'men')
						<li><a href="#menu-man" class="menu" data-toggle="tab"><span>Pria</span><i class="fa fa-mars none"></i></a></li>
						<li><a href="#menu-woman" class="menu" data-toggle="tab"><span>Wanita</span><i class="fa fa-venus none"></i></a></li>
                    @else 
                        <li><a href="#menu-woman" class="menu" data-toggle="tab"><span>Wanita</span><i class="fa fa-venus none"></i></a></li>
                        <li><a href="#menu-man" class="menu" data-toggle="tab"><span>Pria</span><i class="fa fa-mars none"></i></a></li>
                    @endif
						<div class="clear"></div>
                    </ul>
                </div>
                <div class="right-item-wrapper">
                    <div class="right-item">
                        <ul>
                            <li class="user-dd">
                                @if(!empty(Auth::user()))
                                    <a href="javascript:void(0);"><i class="fa fa-user"></i></a>
                                    <div class="user-wrappers user-wrapeprs-alternate">
                                        <ul>
                                            <li><a href="/user/account_dashboard">Halaman Akun</a></li>
                                            <!--<li><a href="/user/referral_program">Belanja Gratis</a></li>-->
                                            <li><a href="/user/wishlist">Wishlist</a></li>
                                            <li><a href="/user/order_history">Order Anda</a></li>
                                            <li><a href="/user/setting">Pengaturan</a></li>
                                            <li><a href="/logout">Logout</a></li>
                                        </ul>
                                    </div>
                                @else
                                    <a href="/login"><i class="fa fa-user"></i></a>
                                @endif
                            </li>
                            <li><a href="#" class="checkout-dd"><i class="fa fa-shopping-bag"></i></a>
                                <div class="nav-checkout">
                                    <ul id="bags-list-float">
                                    @if ($totalcarts > 0) 
                                        @foreach ($carts as $row)
                                            <li id="bags-item">
                                                <div class="nav-check-img left"><img src="{{ IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH . $row->options->image }}"></div>
                                                <div class="nav-check-detail left">
                                                    <div class="item-info">
                                                        <h4>{{ $row->name }}</h4>
                                                        <small>{{ $row->options->brand_name }}</small>
                                                        <div class="detail">
                                                            <b>Color</b><p>: {{ $row->options->color_name }}</p>
                                                        </div>
                                                        <div class="detail">
                                                            <b>Size</b><p>: {{ $row->options->size }}</p>
                                                        </div>
                                                        <div class="detail">
                                                            <b>QTY</b><p>: {{ $row->qty }}</p>
                                                        </div>
                                                        <div class="detail">
                                                            <strong>IDR {{ number_format(($row->price), 0, '.', '.') }}</strong>
                                                        </div>
                                                     </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li id="bags-item">Tas Belanja Anda Kosong</li>
                                    @endif 
                                    </ul>
                                    <a href="/checkout/cart" class="gotocheck">pembayaran</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="nav-search right">
                        <form id="searching" action="/search" method="POST">
                            <input type="text" placeholder="Cari Produk" id="keyword" name="keyword" onfocus="searchSolr(this.id,'searching')" onkeyup="search_bb($(this).val())" url="/home/search"><i class="fa fa-search"></i>
                            <input type="hidden" name="type" id="type"/>
                            <input type="hidden" name="filter" id="filter"/>
                            <input type="hidden" name="url" id="url"/>
                            <input type="hidden" name="parent" id="parent"/>
                            <input type="hidden" name="keywords" id="keywords"/>
                            <input type="hidden" name="names" id="names"/>
                            <input type="hidden" name="gender" id="gender"/>
                            {!! csrf_field() !!}
                        </form>
                    </div>
                </div>
                <div id='menu-man' class='man-menu menu-list'>
                    {!! mega_menu('men') !!}
                </div>
                <div id='menu-woman' class='man-menu menu-list'>
                    {!! mega_menu('women') !!}
                </div>
            </nav>
        </div>
    </div>
</header>