<div class="user-menu left">
    <div class="user-avatar">
        <span><img src='{!! GenerateQRCodeIMG($user->customer_email, 600, 'png') !!}' /></span>
        <h1>{{ $user->customer_fname.' '.$user->customer_lname }}</h1>
        <a href="{{ URL::to('/logout') }}"><i class="fa fa-sign-out" aria-hidden="true"></i>Keluar</a>
    </div>	
    <div class="user-link">
         <ul>
            <li @if ($page=="index") class="active" @endif><a href="{{ URL::to('/user/account_dashboard') }}"><i class="fa fa-dashboard"></i>Halaman Akun</a></li>
            <!--<li @if ($page=="referral") class="belanja-gratis active" @else class="belanja-gratis" @endif><a href="{{ URL::to('/user/referral_program') }}"><i class="fa fa-shopping-cart"></i>Belanja Gratis</a></li>-->
            <li @if ($page=="wishlist") class="active" @endif><a href="{{ URL::to('/user/wishlist') }}"><i class="fa fa-heart"></i>Wishlist</a></li>
            <li @if ($page=="order") class="active" @endif><a href="{{ URL::to('/user/order_history') }}"><i class="fa fa-shopping-cart"></i>Order Anda</a></li>
            <li @if ($page=="benkapoin")  class="active" @endif><a href="{{ URL::to('/user/benka_poin') }}"><i class="fa fa-gift"></i>Benka Point</a></li>
            <li @if ($page=="setting") class="active" @endif><a href="{{ URL::to('/user/setting') }}"><i class="fa fa-cog"></i>Pengaturan</a></li>
        </ul>
    </div>
</div>