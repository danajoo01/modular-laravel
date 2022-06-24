<footer>
	 <div class="upper-footer">
    	<div class="wrapper">
        <p>hijabenka.com adalah situs belanja online fesyen dan kecantikan ternama di Indonesia. hijabenka menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label.hijabenka menawarkan kombinasi produk fesyen dan kecantikan terkini untuk setiap gaya personal yang beragam.</p><p>Kami menyediakan produk berkualitas terbaik untuk wanita dan pria, bervariasi dari pakaian, aksesori, sepatu, tas, produk olahraga dan kecantikan. Komitmen kami adalah memberikan pengalaman belanja online yang menyenangkan, mudah, dan terpercaya untuk memuaskan pelanggan dengan koleksi baru dan penawaran spesial setiap harinya, serta beragam keuntungan seperti kemudahan pengembalian produk hingga 30 hari setelah barang diterima, layanan bayar di tempat dan pengiriman gratis. </p>
        </div>
    </div>
	<div class="top-footer">
    	<div class="wrapper">
        	<div class="footer-item">
            	<p><a href="#">INFORMASI</a></p>
                <ul>
                    <li><a href="/home/about">Tentang Kami</a></li>
                    <li><a href="/home/term_condition">Syarat Penggunaan</a></li>
                    <li><a href="/home/privacy">Ketentuan Privasi</a></li>
                    <li><a href="/affiliate">Program Loyalitas</a></li>
                </ul>
            </div>
            <div class="footer-item">
            	<p><a href="#">Rekanan</a></p>
                <ul>
                    <li><a href="/home/featured_brand">Daftarkan Brand Anda</a></li>
                    <li><a href="/home/brand-list">Daftar Brand</a></li>
                </ul>
            </div>
            <div class="footer-item">
            	<p><a href="#">Bantuan</a></p>
                <ul>
                    <li><a href="/home/cod">Bayar di Tempat</a></li>
                    <li><a href="/home/faq">Pertanyaan Umun</a></li>
                    <li><a href="/home/how_to_order">Cara Pemesanan</a></li>
                    <li><a href="/home/help_return">Ketentuan Pengembalian</a></li>
                    <li><a href="/home/shipping_handling">Ketentuan Pengiriman</a></li>
                </ul>
            </div>
            <div class="footer-item">
            	<p><a href="#">Toko Online Lainnya</a></p>
                <ul>
                	<li><a href="http://www.berrybenka.com">Berrybenka</a>
                </li></ul>
            </div>
            <div class="subscriber right">
            	<p>BERLANGGANAN NEWSLETTER<br> DAPATKAN VOUCHER SENILAI IDR 50.000</p>
                <div class="subscribe">
                    <form name="subscribe" method="POST" action="/newsubcriber">
                    {!! csrf_field() !!}
                    <input type="hidden" value="Newsletter" name="referrer">
                    <input type="hidden" value="berrybenka.com" name="host_name">
                    <input type="hidden" value="" name="utm_source">
                    <input type="hidden" value="" name="utm_campaign">
                    <input type="hidden" value="" name="utm_medium">
                    <input type="hidden" value="newsletter" name="form_location">
                    <input type="hidden" value="" id="subscriber_gender" name="subscriber_gender">
                    <input type="email" name="subscriber_email" placeholder="Masukan Alamat Email" style="padding: 6px 10px; font-style: italic; background: none; border: none; box-sizing: border-box; width: 75%;" required>
                    <input type="submit" value="Daftar">
                    </form>
                </div>
                <p>Belanja Lewat Mobile App</p>
                <div class="appstore">
                	<ul>
                    	<li><a href="https://itunes.apple.com/us/app/berrybenka/id961924940" target="_blank"><img src="{{ asset('hijabenka/desktop/img/apple.gif') }}"></a></li>
                        <li><a href="https://play.google.com/store/apps/details?id=com.berrybenka.android" target="_blank"><img src="{{ asset('hijabenka/desktop/img/google.gif') }}"></a></li>
                    </ul>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
   
    <div class="bottom-footer">
    	<div class="wrapper">
        	<div class="follow">
                <ul>
                    <li><a href="https://www.facebook.com/hijabenka" target="_blank"><i class="fa fa-facebook-official"></i></a></li>
                    <li><a href="https://twitter.com/Hijabenkacom" target="_blank"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="https://www.instagram.com/hijabenka/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="https://www.youtube.com/channel/UCmLiO2tGyXIZW4geZ1yGDlg" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
                </ul>
            </div>
        	<div class="copyright"> ALL RIGHTS RESERVED &copy; 2018 hijabenka.COM </div>
            <div class="payment-certificate">
            	<span><a href="#"><i class="fa fa-cc-mastercard"></i></a></span>
            	<span><a href="http://visa.co.id/ap/id/personal/security/onlineshopping.shtml"><i class="fa fa-cc-visa" target="_blank"></i></a></span>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</footer>