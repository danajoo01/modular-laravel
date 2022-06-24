<?php 
$totalcarts = Cart::count();
$carts = Cart::content();
?>
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
            <a href="/login" class="right"><i class="fa fa-user"></i></a>
        @endif
    </div>
    <a href="{{ url('/') }}" class="header-logo"><img src="{{ asset('hijabenka/mobile/img/hb-new.gif') }}"></a>
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
            <div class="gender-tab clear">
                <ul class="tabs">
                    <li><a href="#cssmenu">Wanita</a></li>
                    <li><a href="#cssmenu2">Pria</a></li>
                </ul>
            </div>
            <div class="tab-menu-content">
            <div id="cssmenu" class="nav-menu">
                <ul>
                    <li><a href="{{ url('/new-arrival') }}/women">New Arrival</a></li>
                    @foreach(menuMweb(['gender' => 'women']) as $menu)
                        @if(!empty($menu->child))
                        <li class="has-sub">
                            <a>
                                <span>{{ $menu->type_name }}</span><span class="holder"></span>
                            </a>
                            <ul style="display: none;">
                                @foreach($menu->child  as $child)
                                <li>
                                    <a href="{{ url('/'. $menu->type_url .'/'. $child->type_url) }}/women">
                                        <span>{{ $child->type_name }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li><a href="{{ url('/'. $menu->type_url) }}/women">{{ $menu->type_name }}</a></li>
                        @endif
                    @endforeach
                    <li><a href="{{ url('/sale') }}/women">Sale</a></li>
                </ul>
            </div>
            
            <div id="cssmenu2" class="nav-menu">
                <ul>
                    <li><a href="{{ url('/new-arrival') }}/women">New Arrival</a></li>
                    @foreach(menuMweb(['gender' => 'men']) as $menu)
                        @if(!empty($menu->child))
                        <li class="has-sub">
                            <a>
                                <span>{{ $menu->type_name }}</span><span class="holder"></span>
                            </a>
                            <ul style="display: none;">
                                @foreach($menu->child  as $child)
                                <li>
                                    <a href="{{ url('/'. $menu->type_url .'/'. $child->type_url) }}/men">
                                        <span>{{ $child->type_name }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li><a href="{{ url('/'. $menu->type_url) }}/men">{{ $menu->type_name }}</a></li>
                        @endif
                    @endforeach
                    <li><a href="{{ url('/sale') }}/men">Sale</a></li>
                </ul>
            </div>
            
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
                <a href="#" class="more-shop"><span>More Shopping At</span><img src="{{ asset('hijabenka/mobile/img/logo.gif') }}"></a>
            </div>
        </div>
    </nav>
    <div class="ssm-overlay"></div>
</header>