<footer>
	 <div class="upper-footer">
    	<div class="wrapper">
                {!! $footer_seo !!}
        </div>
    </div>
	<div class="top-footer">
    	<div class="wrapper">
        	<div class="footer-item">
            	<p><a href="#">INFORMATION</a></p>
                <ul>
                    <li><a href="/home/about">About Us</a></li>
                    <li><a href="/home/term_condition">Term and Condition</a></li>
                    <li><a href="/home/privacy">Privacy Policy</a></li>
                    <!--<li><a href="/special-promo/referral-program">Program Referral</a></li>-->
                    <!--<li><a href="/affiliate">Program Loyalitas</a></li>-->
                    <li><a href="/home/contact">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-item">
            	<p><a href="#">PARTNERS</a></p>
                <ul>
                	<li><a href="/home/featured_brand">Partners</a></li>
                    <li><a href="/home/brand-list">Brand List</a></li>
                </ul>
            </div>
            <div class="footer-item">
            	<p><a href="#">HELP</a></p>
                <ul>
                	<!--<li><a href="/home/cod">Bayar di Tempat</a></li>-->
                    <li><a href="/home/faq">FAQ</a></li>
                    <li><a href="/home/how_to_order">How to Order</a></li>
                    <li><a href="/home/help_return">Return Policy</a></li>
                    <!--<li><a href="/home/help_return_watch">Ketentuan Retur Produk Jam</a></li>-->
                    <li><a href="/home/shipping_handling">Shipping and Handling</a></li>
                    <!--<li><a href="/home/same-day">Ketentuan Pengiriman Same-day</a></li>-->
<!--                    <li><a href="/home/kredivo">Payment with Kredivo</a></li>-->
                </ul>
            </div>
<!--            <div class="footer-item">
            	<p><a href="#">Toko Online Lainnya</a></p>
                <ul>
                	<li><a href="http://www.hijabenka.com">Hijabenka</a>
                </li></ul>
            </div>-->
            <div class="subscriber right">
                <div class="sd-social">
                    <p>discover more of our world on :</p>
                    <ul>
                        <li><a href="https://www.facebook.com/Shopdeca/" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://twitter.com/shopdeca" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.instagram.com/shopdeca/" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                    </ul>
                </div>
                <div class="sub-wrapper">
                    <p>Subscribe Our Newsletter</p>
                    <div class="subscribe">
                        <form name="subscribe" method="POST" action="/newsubcriber">
                        {!! csrf_field() !!}
                        <input type="hidden" value="Newsletter" name="referrer">
                        <input type="hidden" value="shopdeca.com" name="host_name">
                        <input type="hidden" value="" name="utm_source">
                        <input type="hidden" value="" name="utm_campaign">
                        <input type="hidden" value="" name="utm_medium">
                        <input type="hidden" value="newsletter" name="form_location">
                        <input type="hidden" value="" id="subscriber_gender" name="subscriber_gender">
                        <input type="email" name="subscriber_email" placeholder="Input Email" style="padding: 6px 10px; font-style: italic; background: none; border: none; box-sizing: border-box; width: 75%;" required>
    <!--                    <input type="submit" id="subscribe_men" value="Men">
                        <input type="submit" id="subscribe_women" value="Women">-->
                        <input type="submit" id="subscribe_shopdeca" value="Subscribe">
                        </form>
                    </div>
                </div>            	
<!--                <p>Belanja Lewat Mobile App</p>
                <div class="appstore">
                	<ul>
                    	<li><a href="https://itunes.apple.com/us/app/shopdeca/id961924940" target="_blank"><img src="{{ asset('shopdeca/desktop/img/apple.gif') }}"></a></li>
                        <li><a href="https://play.google.com/store/apps/details?id=com.shopdeca.android" target="_blank"><img src="{{ asset('shopdeca/desktop/img/google.gif') }}"></a></li>
                    </ul>
                </div>-->
            </div>
            <div class="clear"></div>
        </div>
    </div>
   
    <div class="bottom-footer">
    	<div class="wrapper">
            <div class="copyright"> ALL RIGHTS RESERVED &copy; 2018 SHOPDECA.COM </div>
            <div class="payment-certificate">
                <span><a href="#"><img src="/shopdeca/desktop/img/footer/card-mastercard.png"></a></span>
                <span><a href="#"><img src="/shopdeca/desktop/img/footer/card-visa.png"></a></span>
            </div>            
        </div>
    </div>
</footer>