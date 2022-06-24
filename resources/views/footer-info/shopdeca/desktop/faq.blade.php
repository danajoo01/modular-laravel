<?php 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
@endsection

@section('content')

<div class="error-content thx-wrapper">
	<div class="error-overlay">
        <div class="error-inside ref-wrap">
            <div class="about-wrapper help">
            	<div class="row">
				    <div class="five columns">
				        <div class="sidebarCont">
				            <div class="sidebar-head">
				            	<h4>BANTUAN</h4>
				            </div>
				            @include("footer-info.$domain.desktop.left-menu-help")        
				        </div>
				    </div>
				    <div class="eleven columns">
				        <div class="category-head">
				        	<h4 style="padding:1px !important;">FAQ</h4>
				        </div>
				      	<div class="full-width mb20">
				        <div class="static-content">
				          	<div class="list-q">
				            	<ul>
				                	<li>
				                    	<a>Should I make an account for shopping at Shopdeca.com?</a>
				                        <div>Yes, if you have an account, it is easier for you to shop because you don’t need to fill in your Personal data and address every time you made an order at Shopdeca.com. You can also subscribe to our newsletter, which gives you information about discount, special offer, and other benefits.</div>
				                    </li>
				                    <li>
				                    	<a>How can I get Shopdeca.com Account?</a>
				                        <div>You can register your account by clicking “Log in / Sign up” at the top right corner of the website page. Click “NOT REGISTER YET” and fill in your complete identity. Please make sure that the identity you entered is correct, then click “Create An Account”. After that, you have an account at Shopdeca! You can enter or change your complete address at menu “Account-Edit Address Detail”</div>
				                    </li>
				                    <li>
				                    	<a>How can I change my personal data at Shopdeca.com?</a>
				                        <div>You must login to your account and click “My Account” to change personal profile or others.</div>
				                    </li>
				                    <li>
				                    	<a>What happens after I click “Check Out” Button?</a>
				                        <div>Goods will only be shipped after we receive your payment. When you have completed your payment process, confirm your payment by clicking “Confirm” at “My Account” page. When confirmation process is done, Shopdeca will process your order and ship to your address. You have 4 days to complete your payment starting on the day the order was placed. If payment has not been made after the stated period, your order will be canceled.</div>
				                    </li>
				                    <li>
				                    	<a>How can I use Discount Code?</a>
				                        <div>You can enter the code at the “Checkout” page. You only can use one code for each transaction. Please read the terms and conditions before you use it.</div>
				                    </li>
				                    <li>
				                    	<a>How can I use Promo/ Gift Voucher Shopdeca?</a>
				                        <div>When you SUBMIT ORDER, enter the voucher code at COUPON CODE column (note: the coupon codes are case sensitive - please fill the code as written at the voucher, use capital letter accordingly). Make sure you enter the correct code, and then click REDEEM COUPON. Your payment nominal will be reduced automatically. Please read the terms and conditions.</div>
				                    </li>
				                    <li>
				                    	<a>Is the Payment Process at Shopdeca.com secure?</a>
				                        <div>Our payment process is secure and we are committed to keep your privacy. Your personal data is secure and will not be used for other issues.</div>
				                    </li>
				                    <li>
				                    	<a>Can I change or cancel my order?</a>
				                        <div>If you haven’t made any payment to our account (transfer bank via Shopdeca account). You can still cancel your order by informing our customer service. But, after you have made the payment, your order can NOT be change or cancel.</div>
				                    </li>
				                    <li>
				                    	<a>What if I receive a different item from what I ordered?</a>
				                        <div>If this is happened, you can contact our customer service to manage the return of the goods or the return of your money within 7 days after the goods received.</div>
				                    </li>
				                    <li>
				                    	<a>Can the purchased products be returned?</a>
				                        <div>The product that has been purchased can be returned within a maximum period of 7 days after you received the product. Returned product must be intact and unused. </div>
				                    </li>
				                    <li>
				                    	<a>Where I can get the information / the newest promo from Shopdeca.com?</a>
				                        <div>You can get the information about the newest promo from your email if you fill the “Subscribe Newsletter” column. You can find the column at the bottom right corner of the website page. Furthermore, you can get the newest information if you follow Shopdeca’s social media accounts (Twitter, Facebook, Instagram, and Youtube).</div>
				                    </li>
				                    <li>
				                    	<a>How can I contact Shopdeca.com?</a>
				                        <div>For more information, suggestions, and critics, you can contact our customer service by :
				                            <ol>
				                                <li>Phone				: 0878 7585 4772</li>
				                                <li>Text and Whatsapp 	: 0878 7585 4772</li>
				                                <li>Email 				: shopdeca@berrybenka.com</li>
				                                <li>Office hours		: Monday – Friday (9.00 am– 6.00 pm)</li>
				                            </ol>
				                        </div>
				                    </li>
				                </ul>
				            </div>
				          
				        </div>
				      </div>
				     
				    </div>
				  </div>
            </div>
        </div>
    </div>
</div>

@endsection



