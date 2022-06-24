<header>
    <div class="top-header">
        <div class="wrapper">
            <a class="logo" href="{{ URL::to('/brand_report') }}"><img src="{{ asset('berrybenka/desktop/img/bb-new.gif') }}"></a>
            <div class="top-right-header right">
                <ul>
                    <li class="user-dd">
                        <!-- @if(!empty(Auth::user())) -->
                            <a href="#">aa</a>
                            <div class="user-wrappers">
                                <ul>
                                    <li><a href="/user/account_dashboard">Halaman Akun</a></li>
                                    <!--<li><a href="/user/referral_program">Belanja Gratis</a></li>-->
                                    <li><a href="/user/wishlist">Wishlist</a></li>
                                    <li><a href="/user/order_history">Order Anda</a></li>
                                    <li><a href="/user/setting">Pengaturan</a></li>
                                    <li><a href="/logout">Logout</a></li>
                                </ul>
                            </div>
                        <!-- @else
							<a href="/brand_report/login" class="q-log-triger">Masuk</a>
                        @endif -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="bottom-header">
        <div class="wrapper">
            <nav style="padding: 0px;width: 100%;position: relative;">
                <a href="{{ URL::to('/brand_report') }}" class="logo-small left"><img class="bb-logo" src="{{ asset('berrybenka/desktop/img/b-logo.gif') }}"></a>
                <div class="right-item-wrapper">
                    <div class="right-item">
                        <ul>
                            <li class="user-dd">
                                <!-- @if(!empty(Auth::user())) -->
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
                                <!-- @else
                                    <a href="/brand_report/login"><i class="fa fa-user"></i></a>
                                @endif -->
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</header>