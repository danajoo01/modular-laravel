<?php 
$totalcarts = Cart::count();
$carts = Cart::content();
$date_logo = date('Y-m-d');
?>

<div id="loading" style="display: none; z-index: 999">
	<div class="load-icon"><img src="{{ asset('hijabenka/mobile/img/loading.gif') }}"></div>
</div>

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
    <a href="{{ url('/') }}" class="header-logo">
         @if($date_logo >= "2018-01-01" && $date_logo < "2018-01-09")
            <img src="{{ asset('hijabenka/mobile/img/hb-logo-xmas-nye-1-R1.gif') }}">
        @else
            <img src="{{ asset('hijabenka/mobile/img/hb-logo.png') }}">
        @endif
    </a>
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
            {{-- <div class="gender-tab clear">
                <ul class="tabs">
                    <li><a href="#cssmenu">Wanita</a></li>
                    <li><a href="#cssmenu2">Pria</a></li>
                </ul>
            </div> --}}
            <div class="tab-menu-content">
            <div id="cssmenu" class="nav-menu">
                <br>
                <ul>
                    <li>
                        <a href="https://m.hijabenka.com/exclusive-collections" onclick="ga('send','event','Menu Bar','click','exclusive-collections',1);" style="color: #ff0000;">Exclusive Collections</a>
                    </li>
                    <li>
                        <a href="https://m.hijabenka.com/clothing" onclick="ga('send','event','Menu Bar','click','Pakaian',1);">Clothing</a>
                    </li>
                    <li>
                        <a href="https://m.hijabenka.com/hijab-signature" onclick="ga('send','event','Menu Bar','click','hijab-signature',1);">Hijab Signature</a>
                    </li>
                    <li>
                        <a href="https://m.hijabenka.com/hijab-essential" onclick="ga('send','event','Menu Bar','click','hijab-essential',1);">Hijab Essential</a>
                    </li>
                    <li class="has-sub">
                        <a>
                            <span>Accessories</span><span class="holder"></span>
                        </a>
                        <ul style="display: none;">
                            <li>
                                <a href="https://m.hijabenka.com/shoes" onclick="ga('send','event','Menu Bar','click','Sepatu',1);">
                                    <span>Shoes</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://m.hijabenka.com/bags" onclick="ga('send','event','Menu Bar','click','Tas',1);">
                                    <span>Bags</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://m.hijabenka.com/mukena" onclick="ga('send','event','Menu Bar','click','Mukena',1);">
                                    <span>Mukena</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://m.hijabenka.com/accessories" style="font-style: italic" onclick="ga('send','event','Menu Bar','click','accessories',1);">
                                <span>view all</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <style>
                        .sfe-widget__header-avatar{border: none !important;}
                        .sfe-widget__toggle{box-shadow: none !important;}

                        .stamp-history-wrapper ul li{color: #000 !important;}
                        .stamp-history-wrapper .stamp-history-head ul li{color: #fff !important;}
                        .stamp-history-wrapper ul li:first-child, .stamp-history-wrapper ul li:last-child{color: #000 !important;}
                    </style>
                    <li>
                    <a href="https://m.hijabenka.com/sale/women" style="color: #ff0000;" onclick="ga('send','event','Menu Bar','click','Sale',1);">SALE</a>
                    </li>
                </ul>

                {{-- {!! mega_menu('women', 'mobile') !!} --}}
                {{-- <ul> --}}
                    {{-- <li><a href="{{ url('/new-arrival') }}/women">Produk Baru</a></li> --}}
                    {{-- <br>
                    <li><a href="{{ url('/clothing') }}/women?pn=desc">Produk Baru</a></li>
                    <li><a href="{{ url('/clothing') }}/women">Pakaian</a></li>
                    <li><a href="{{ url('/jilbab') }}/women">Jilbab</a></li>
                    <li><a href="{{ url('/mukena') }}/women">Mukena</a></li>
                    <li><a href="{{ url('/bags') }}/women">Tas</a></li>
                    <li><a href="{{ url('/shoes') }}/women">Sepatu</a></li> --}}
                    {{-- @foreach(menuMweb(['gender' => 'women']) as $menu)
                        @if(!empty($menu->child))
                        <li class="has-sub">
                            <a>
                                <span>{{ $menu->type_name_bahasa }}</span><span class="holder"></span>
                            </a>
                            <ul style="display: none;">
                                @foreach($menu->child  as $child)
                                <li>
                                    <a href="{{ url('/'. $menu->type_url .'/'. $child->type_url) }}/women">
                                        <span>{{ $child->type_name_bahasa }}</span>
                                    </a>
                                </li>
                                @endforeach
                                <li>
                                  <a href="{{ url('/'. $menu->type_url) }}/women" style="font-style: italic">
                                    <span>Lihat Semua</span>
                                  </a>
                                </li>
                            </ul>
                        </li>
                        @else
                        <li><a href="{{ url('/'. $menu->type_url) }}/women">{{ $menu->type_name_bahasa }}</a></li>
                        @endif
                    @endforeach --}}
                    {{-- <li><a href="{{ url('/sale') }}/women">Sale</a></li>
                </ul> --}}
            </div>
            
            {{-- <div id="cssmenu2" class="nav-menu">
                <ul>
                    <li><a href="{{ url('/new-arrival') }}/women">Produk Barus</a></li>
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
            </div> --}}
            
            </div>
            <div class="footer-nav">
                <div class="social-icon">
                    <ul>
                        <li><a href="https://www.facebook.com/hijabenka"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="https://twitter.com/Hijabenkacom"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="https://www.instagram.com/hijabenka/"><i class="fa fa-instagram"></i></a></li>
                        <!--<li><a href="#"><i class="fa fa-pinterest"></i></a></li>-->
                        <li><a href="https://www.youtube.com/channel/UCmLiO2tGyXIZW4geZ1yGDlg"><i class="fa fa-youtube-play"></i></a></li>
                    </ul>
                </div>
                <a href="http://m.berrybenka.com/" class="more-shop"><span>More Shopping At</span><img src="{{ asset('hijabenka/mobile/img/bb-logo.gif') }}"></a>
            </div>
        </div>
    </nav>
    <div class="ssm-overlay"></div>
</header>