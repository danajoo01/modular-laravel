<?php 
$generate_uri_segment = generate_uri_segment();
?>
<footer>
    <div class="top-footer">
        <div class="wrapper">
            <div class="email-gather">
                <h2 style="text-align: center;font-size: 30px;font-weight: bold;">Subscribe Newsletter</h2>
                <p style="text-align: center;font-size: 13px;font-weight: bold;">Stay updated for new collection & special offer </p>
                <form name="subscribe" method="POST" action="/newsubcriber">
                    {!! csrf_field() !!}
                    <input type="hidden" value="Newsletter" name="referrer">
                    <input type="hidden" value="hijabenka.com" name="host_name">
                    <input type="hidden" value="" name="utm_source">
                    <input type="hidden" value="" name="utm_campaign">
                    <input type="hidden" value="" name="utm_medium">
                    <input type="hidden" value="newsletter" name="form_location">
                    <input type="hidden" value="" id="subscriber_gender" name="subscriber_gender">
                    <input type="text" name="subscriber_email" placeholder="TYPE YOUR EMAIL">
                    @if ($generate_uri_segment['gender'] == 'men')
                        <input type="submit" id="subscribe_men" value="Pria" style="display: none;">
                    @else
                        <input type="submit" id="subscribe_women" value="wanita" style="display: none;">
                    @endif
                </form>
            </div>
            <div class="footer-link">
                <ul>
                    <li>
                        <ul>
                            <li><a href="#">informasi</a></li>
                            <li><a href="/home/about">tentang kami</a></li>
                            <li><a href="/home/term_condition">syarat penggunaan</a></li>
                            <li><a href="/home/privacy">ketentuan privasi</a></li>
                            <li><a href="/home/contact">kontak kami</a></li>
                            <li><a href="/promo/special_deals">Promo Bank</a></li>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li><a href="/home/faq">bantuan</a></li>
                            <li><a href="/home/cod">bayar di tempat</a></li>
                            <li><a href="/home/how_to_order">cara pemesanan</a></li>
                            <li><a href="/home/help_return">ketentuan pengembalian</a></li>
                            <li><a href="/home/help_return_watch">ketentuan retur produk jam</a></li>
                            <li><a href="/home/shipping_handling">ketentuan pengiriman</a></li>
                            <?php /*<li><a href="/home/same-day">ketentuan pengiriman sameday</a></li>*/?>
                        </ul>
                    </li>
                    <li>
                        <ul>
                            <li><a href="#">toko online lainnya</a></li>
                            <li><a href="http://www.berrybenka.com">berrybenka</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/pl.css') }}">

            <div class="berrybenka-app">
                <h1>download hijabenka app</h1>
                <p>shop anytime, easier than ever!</p>
                <ul>
                    <li><a href="https://hijabenka.onelink.me/804628454/31b1d8e7"><img src="{{ asset('hijabenka/desktop/img/Assets-appstore-desktop.jpg') }}"></a></li>
                    <li><a href="https://hijabenka.onelink.me/804628454/31b1d8e7"><img src="{{ asset('hijabenka/desktop/img/Assets-googleplay-desktop.jpg') }}"></a></li>
                </ul>
            </div>

            <?php /* Backup social footer
            <div class="berrybenka-app">
                <h1>belanja melalui Hijabenka app</h1>
                <ul>
                    <li><a href="https://itunes.apple.com/us/app/hijabenka/id1113374396"><img src="https://hijabenka.com/hijabenka/desktop/img/apple.gif"></a></li>
                    <li><a href="https://play.google.com/store/apps/details?id=com.hijabenka.android"><img src="https://hijabenka.com/hijabenka/desktop/img/google.gif"></a></li>
                </ul>
            </div>
            */?>

            <div class="support">
                <div class="social">
                    <p>Discover more of our world on:</p>
                    <ul>
                        <li><a href="https://www.facebook.com/hijabenka/"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://twitter.com/Hijabenkacom"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.instagram.com/hijabenka/"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        <!-- <li><a href="#"><img src="{{ asset('berrybenka/desktop/img/icon/line.svg') }}"></a></li> -->
                    </ul>
                </div>
                <div class="cod">
                    <ul>
                        <li><a href="/home/shipping_handling"><img src="{{ asset('hijabenka/desktop/img/icon/truck.svg') }}"><p>gratis ongkir*</p></a></li>
                        <li><a href="/home/cod"><img src="{{ asset('hijabenka/desktop/img/icon/rupiah.svg') }}"><p>bayar ditempat</p></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer">
        <div class="footer-left">
            all rights reserved © 2018 hijabenka.com
        </div>  
        <div class="footer-right">
            <span><a href="#" target="_blank"><i class="fa fa-cc-visa"></i></a></span>
            <span><a href="#"><i class="fa fa-cc-mastercard"></i></a></span>
        </div>
    </div>  
</footer>