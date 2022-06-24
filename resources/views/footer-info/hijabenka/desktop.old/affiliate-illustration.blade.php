<?php $time = microtime(true); 
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
                    <a class="affi-head-img" target="_blank" href="https://berrybenka.hasoffers.com/login">
                        <img src="/hijabenka/desktop/img/affiliate/banner.jpg">
                    </a>
                    <p>HIJABENKA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner HIJABENKA.com pada website atau blog Anda dan bersiaplah mendapatkan komisidari setiap transaksi yang Anda referensikan.</p>
                </div>
                
                <div class="affiliate-faq">
                	<h2>ILUSTRASI</h2>
                    <div class="ilustrasi clearit">
                        <ul>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/1.jpg">
                                <p>A adalah pemilik website yang ingin mendapatkan income dari websitenya</p>
                            </li>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/2.jpg">
                                <p>Tertarik dengan cara yang mudah dan besar komisi 10% yang diberikan, A mendaftar sebagai partner program affiliate </p>
                            </li>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/3.jpg">
                                <p>A menampilkan link  dan banner affiliate HIJABENKA pada websitenya </p>
                            </li>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/4.jpg">
                                <p>B adalah pengunjung website A. B tertarik dan melakukan klik pada link affiliate yang ditampilkan dalam website A</p>
                            </li>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/5.jpg">
                                <p>B diarahkan menuju website HIJABENKA dan melakukan transaksi pembelian</p>
                            </li>
                            <li>
                                <img src="/hijabenka/desktop/img/affiliate/6.jpg">
                                <p>Maka, A sebagai partner affiliate HIJABENKA, berhak mendapatkan komisi 10% atas transaksi yang dilakukan B</p>
                            </li>
                        </ul>
                    </div>
                    <div class="ilustrasi-detail">
            <p>Ilustrasi komisi program affiliate HIJABENKA,</p>
            <strong>Hijabenka.com memberikan komisi sebesar 10%  untuk setiap tranksaksi yang berasal dari link affiliate yang direfensikan partner.</strong>
            <div class="simulasi-komisi">
            	<p>Simulasi komisi</p>
                <ol>
                	<li>
                    	<span>Pageview website atau blog Anda</span>
                        <span> : </span>
                        <span>10.000 viewer /bulan</span>
                    </li>
                    <li>
                    	<span>Viewer yang membaca &amp; meng-klik post Anda</span>
                    	<span> : </span>
                        <span>5.000 viewer ( asumsi 50% )</span>    
                    </li>
                    <li>
                    	<span>Viewer yang meng-klik dan melakukan<br>transaksi di website Hijabenka.com</span>
                        <span> : </span>
                        <span>500 viewer (asumsi 10% )</span>
                    </li>
                    <li>
                    	<span>Average order volume Hijabenka.com</span>
                        <span> : </span>
                        <span>Rp 200.000</span>
                    </li>
                </ol>
                <p>Maka asumsi komisi yang akan Anda dapatkan dalam satu bulan adalah,</p>
                <p><strong>500</strong> (Viewer yang meng-klik dan melakukan transakasi di web Hijabenka.com dalam 30 hari setelah meng-klik) <strong>x Rp 200.000</strong> (Average order Hijabenka) <strong>x 10%</strong> (Besar komisi yang diberikan Hijabenka) = <strong>Rp 10.000.000,-/bulan</strong></p>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



