<?php 
$time = microtime(true); 
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
            <div class="about-wrapper affi-wrapper">
            	<div class="sixteen affiliate-header">
                    <a class="affi-head-img" target="_blank" href="https://shopdeca.hasoffers.com/login">
                        <img src="/shopdeca/desktop/img/affiliate/banner-referral.jpg">
                    </a>
                    <p>SHOPDECA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner SHOPDECA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.</p>
                   
                </div>
                
               <div class="towork">
                    <h1>HOW TO WORK </h1>
                    <ul>
                        <li>A Terdaftar sebagai Partner affiliate Shopdeca atau Hijabenka.</li>
                        <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                        <li>A membagikan link referral pendaftaran affiliate miliknya melalui media tertentu.</li>
                        <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                        <li>B tertarik bergabung dan mendaftar melalui link yang dibagikan A.</li>
                        <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                        <li>A akan mendapatkan 1% komisi berkelanjutan dari komisi affiliate uang didapat oleh B.</li>
                    </ul>
                </div>
                
                <div class="towork commision">
                    <h1>COMMISION</h1>
                    <ul>
                        <li><img src="/shopdeca/desktop/img/affiliate/commision.gif" alt=""></li>
                        <li>Besar komisi yang Anda dapatkan sebagai referring dalam program referral affiliate ini adalah 1% dari persentasi komisi affiliate yang didapat downline Anda, atau 10% dari total komisi (payout) yang didapat downline Anda dari transaksi yang datang melalui link affiliate yang downline dibagikan.</li>
                    </ul>
                </div>
                
                <div class="towork assist">
                    <h1>NEED ASSISTANT ?</h1>
                    <p>Tertarik dapat komisi lebih? Klik <a href="https://blogaffiliateshopdeca.wordpress.com/"><strong><u>disini</u></strong></a> untuk mengetahui informasi mengenai program referral affiliate Shopdeca lebih detail.</p>
                    <p>Jika ada hal lain yang ingin ditanyakan, silahkan langsung hubungi tim support kami melalui email ke <a href="mailto:affiliate@berrybenka.com"><strong><u>affiliate@berrybenka.com</u></strong></a></p>
                </div>
                
            </div>
        </div>
    </div>
</div>

@endsection



