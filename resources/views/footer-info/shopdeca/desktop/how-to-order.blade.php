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
			        	<h4 style="padding:1px !important;">HOW TO ORDER</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			          <div class="how-order">
			          	<!--<ul>
			            	<li>Pilih Produk</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Tas Belanja</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Masuk / Daftar</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Pembayaran</li>
			            </ul>-->
			          </div>
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>HOW TO ORDER</a>
			                        <div>Search through your desired product in our website and then click the “Add To Cart” button. You can continue shopping or proceed to checkout. Once you are done, review your shopping bag/cart content then continue to the payment page. Complete your order by filling in the details and following the instructions.</div>
			                    </li>
			                    <li>
			                    	<a>HOW TO PAY</a>
			                        <div>We accept payments via Bank Transfer and Credit Cards (Visa, Mastercard). If you are paying via Bank Transfer, please email your payment confirmation to shopdeca@berrybenka.com upon completion of payment.</div>
			                    </li>
			                    <li>
			                    	<a>PAYMENT CONFIRMATION</a>
			                        <div><p>Please be aware that inter-bank transfers require additional processing time and we recommend transferring as soon as possible to avoid accidental cancellations.</p>
			                        <p>To expedite the verification process of your payment, please confirm your payment by filling & submitting the form after you have made your bank transfer to accounts below:<br>
			                        •	BCA 546 032 7077 (a/n PT Berrybenka)<br>
									•	Bank Mandiri 165 000 042 7964 (a/n PT. Berrybenka)</p>
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



