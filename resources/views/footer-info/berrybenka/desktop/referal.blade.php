
@extends('layouts.berrybenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/error.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/about.css') }}">
@endsection

@section('content')

<div class="error-content thx-wrapper">
	<div class="error-overlay">
        <div class="error-inside error-inside-fix ref-wrap" style="position:static;">
            <div class="about-wrapper tnc-wrapper">
            	<div class="referral-header"><img src="http://im.berrybenka.biz/assets/landing_page/referral-newjpg_GGQI7.jpg"></div>
                <div class="referral-title">
                    <h1>Undang Teman untuk Belanja Gratis di Berrybenka</h1>
                    <p>Kini Berrybenka hadir dengan fitur baru yaitu Referral Program. Dengan fitur ini, Anda bisa membagikan referral code Anda kepada teman atau keluarga. Jika referral code Anda digunakan oleh teman untuk belanja di Berrybenka, Anda akan langsung mendapatkan Benka Poin senilai IDR 25.000!</p>
                </div>
                <div class="referral-howto">
                    <ul>
                        <li><span>1</span>Undang teman Anda untuk mendownload dan mendaftar Aplikasi Berrybenka.</li>
                        <li><span>2</span>Teman yang Anda undang akan mendapatkan Benka Poin senilai Rp 25.000.</li>
                        <li><span>3</span>Benka Poin dapat digunakan sebagai alat bayar dan diskon di Berrybenka dan Hijabenka.</li>
                        <li><span>4</span>Anda akan mendapatkan Benka Poin senilai Rp 25.000 ketika teman yang anda undang melakukan pembelian pertama dan produk yang dipesan telah diterima</li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="full-width relative text-center mb20 syaratketentuan">
                    <div class="section-title">Anda sebagai pengundang</div>
                    <div class="clear"></div>
                </div>
                <div class="referral-howto">
                    <div style="margin-bottom:40px;"><img src="http://im.berrybenka.biz/assets/landing_page/manpng_0LLTS.png"></div>
                    <ul>
                        <li class="img"><span>1</span><img src="http://im.berrybenka.biz/assets/landing_page/rev1jpg_YV3JE.jpg">Buka Aplikasi Berrybenka dari iPhone atau Android dan login dengan email anda</li>
                        <li class="img"><span>2</span><img src="http://im.berrybenka.biz/assets/landing_page/rev2jpg_PXW7L.jpg">Klik Menu "Belanja Gratis" di layar Akun Saya </li>
                        <li class="img"><span>3</span><img src="http://im.berrybenka.biz/assets/landing_page/rev3jpg_5UF0O.jpg">Bagikan link referral program via Email, SMS maupun Social Media ke teman anda sebanyak-banyaknya.</li>
                        <li class="img"><span>4</span><img src="http://im.berrybenka.biz/assets/landing_page/rev4jpg_UKD1W.jpg">Anda akan mendapatkan Benka Poin senilai Rp 25.000 ketika teman yang anda undang melakukan pembelian pertama dan produk yang dipesan telah diterima</li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="full-width relative text-center mb20 syaratketentuan">
                    <div class="section-title">Anda sebagai teman yang diundang </div>
                    <div class="clear"></div>
                </div>
                <div class="referral-howto">
                    <div style="margin-bottom:40px;"><img src="http://im.berrybenka.biz/assets/landing_page/womanpng_5OEMB.png"></div>
                    <ul>
                        <li class="img"><span>1</span><img src="http://im.berrybenka.biz/assets/landing_page/rev3jpg_5UF0O.jpg">Klik link yang diberikan oleh teman anda, anda akan langsung dibawa ke halaman Berrybenka di Android Play Store atau di iOS App Store</li>
                        <li class="img"><span>2</span><img src="http://im.berrybenka.biz/assets/landing_page/rev5jpg_N07UD.jpg">Install Aplikasi Berrybenka dari Android Play Store ataupun dari iOS App Store tersebut</li>
                        <li class="img"><span>3</span><img src="http://im.berrybenka.biz/assets/landing_page/rev6jpg_TSMZ0.jpg">Buka Aplikasi Berrybenka dan Daftar menjadi pengguna</li>
                        <li class="img"><span>4</span><img src="http://im.berrybenka.biz/assets/landing_page/rev7jpg_DRKQ4.jpg">Anda akan mendapatkan Benka Poin senilai Rp 25.000</li>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div class="full-width relative text-center mb20 syaratketentuan">
                    <div class="section-title">*Syarat &amp; ketentuan:</div>
                    <div class="clear"></div>
                </div>
                <div class="syaratketentuan">
                    <ul>
                        <li>1 Benka Poin setara dengan Rp. 1,- (1 Rupiah)</li>
                        <li>Benka Poin hanya bisa didapatkan apabila anda mengunduh dan mendaftar akun baru di Aplikasi Berrybenka </li>
                        <li>Benka Poin tidak dapat diuangkan</li>
                        <li>Satu (1) perangkat (handphone) hanya bisa digunakan 1 kali untuk mendapatkan Benka Poin sebagai pihak yang diundang
                        </li><li>Voucher tertentu tidak bisa digabung dengan Benka Poin</li>
                        <li><strong>Berrybenka berhak menolak dan membatalkan segala bentuk transaksi apabila ditemukan kecurangan yang dilakukan oleh pengguna.</strong></li>
                    </ul>
                </div>
                <!--<a class="ref-invite" href="https://berrybenka.com/user/referral_program">Undang Teman Sekarang</a>-->
            </div>
        </div>
    </div>
</div>

@endsection



