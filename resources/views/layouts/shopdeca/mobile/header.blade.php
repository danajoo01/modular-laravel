<?php 
$totalcarts = Cart::count();
$carts = Cart::content();
?>

<!--<div id="loading" style="display: none; z-index: 999">
	<div class="load-icon"><img src="{{ asset('berrybenka/mobile/img/loading.gif') }}"></div>
</div>-->

<header>
    <div class="menu">
        <a href="javascript:void(0);" class="ssm-toggle-nav left"><i class="fa fa-bars"></i></a>
        <a href="#" class="left search-head"><i class="fa fa-search"></i></a>
    </div>
    <div class="shop-chart">
        <a href="/checkout/cart" class="right">
			<i class="fa fa-shopping-bag"></i>
			<div class="notif-circle"><span id="tot_itm">{{ $totalcarts }}</span></div>
			<input type="hidden" name="itm_tot" id="itm_tot" value="{{ $totalcarts }}">
		</a>
        @if(!empty(Auth::user()))
            <a href="/user/account_dashboard" class="right"><i class="fa fa-user"></i></a> 
        @else
            @if(get_uri() !== FALSE)
                <a href="/login?continue={{ urlencode(get_uri()) }}" class="right"><i class="fa fa-user"></i></a>
            @else
                <a href="/login" class="right"><i class="fa fa-user"></i></a>
            @endif 
        @endif
    </div>
    <a href="{{ url('/') }}" class="header-logo"><img src="{{ asset('shopdeca/mobile/img/shopdeca.png') }}"></a>
    <div class="search">
        <div class="search-wrapper clear">
            <form id="searching" action="/search" method="POST">
                <input type="text" placeholder="Cari Produk" id="keyword" name="keyword" onfocus="searchSolr(this.id,'searching')" onkeyup="search_bb($(this).val())" url="/home/search">
                <input type="hidden" name="type" id="type"/>
                <input type="hidden" name="filter" id="filter"/>
                <input type="hidden" name="url" id="url"/>
                <input type="hidden" name="parent" id="parent"/>
                <input type="hidden" name="keywords" id="keywords"/>
                <input type="hidden" name="names" id="names"/>
                <input type="hidden" name="gender" id="gender"/>
                {!! csrf_field() !!}
            </form>
            <a href="#s" class="search-cancel">batal</a>
        </div>
    </div>
    <nav class="nav">
        <div class="nav-wrapper">
            <!--div class="gender-tab clear">
                <ul class="tabs">
                    <li><a href="#cssmenu">Wanita</a></li>
                    <li><a href="#cssmenu2">Pria</a></li>
                </ul>
            </div-->
            <div class="tab-menu-content">
            <div id="cssmenu" class="nav-menu">
                <ul>
                    <li><a href="{{ url('/new-arrival') }}">New Arrival</a></li>
                    @foreach(menuMweb(['gender' => 'women']) as $menu)
                        @if(!empty($menu->child))
                        <li class="has-sub">
                            <a>
                                <span>{{ $menu->type_name_bahasa }}</span><span class="holder"></span>
                            </a>
                            <ul style="display: none;">
                                @foreach($menu->child  as $child)
                                <li>
                                    <a href="{{ url('/'. $menu->type_url .'/'. $child->type_url) }}">
                                        <span>{{ $child->type_name_bahasa }}</span>
                                    </a>
                                </li>
                                @endforeach
                                <li>
                                  <a href="{{ url('/'. $menu->type_url) }}" style="font-style: italic">
                                    <span>View all</span>
                                  </a>
                                </li>
                            </ul>
                        </li>
                        @else
                        <li><a href="{{ url('/'. $menu->type_url) }}">{{ $menu->type_name_bahasa }}</a></li>
                        @endif
                    @endforeach
                    <li><a href="{{ url('/sale') }}">Sale</a></li>
                </ul>
            </div>
            
            <!--div id="cssmenu2" class="nav-menu">
                <ul>
                    <li><a href="{{ url('/new-arrival') }}/men">Produk Baru</a></li>
                    @foreach(menuMweb(['gender' => 'men']) as $menu)
                        @if(!empty($menu->child))
                        <li class="has-sub">
                            <a>
                                <span>{{ $menu->type_name_bahasa }}</span><span class="holder"></span>
                            </a>
                            <ul style="display: none;">
                                @foreach($menu->child  as $child)
                                <li>
                                    <a href="{{ url('/'. $menu->type_url .'/'. $child->type_url) }}/men">
                                        <span>{{ $child->type_name_bahasa }}</span>
                                    </a>
                                </li>
                                @endforeach
                                <li>
                                  <a href="{{ url('/'. $menu->type_url) }}/men" style="font-style: italic">
                                    <span>Lihat Semua</span>
                                  </a>
                                </li>
                            </ul>
                        </li>
                        @else
                        <li><a href="{{ url('/'. $menu->type_url) }}/men">{{ $menu->type_name_bahasa }}</a></li>
                        @endif
                    @endforeach
                    <li><a href="{{ url('/sale') }}/men">Sale</a></li>
                </ul>
            </div-->
            
            </div>
            <!--
            <div class="footer-nav">
                <div class="social-icon">
                    <ul>
                        <li><a href="https://www.facebook.com/BerrybenkaShop"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/Berrybenka"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="https://www.instagram.com/berrybenka/"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="https://id.pinterest.com/berrybenkaID/"><i class="fa fa-pinterest"></i></a></li>
                        <li><a href="https://www.youtube.com/user/BERRYBENKADOTCOM"><i class="fa fa-youtube-play"></i></a></li>
                    </ul>
                </div>
                <a href="http://m.hijabenka.com/" class="more-shop"><span>More Shopping At</span><img src="{{ asset('berrybenka/mobile/img/logo.gif') }}"></a>
            </div>
        </div>
        -->
    </nav>
    <div class="ssm-overlay"></div>
</header>