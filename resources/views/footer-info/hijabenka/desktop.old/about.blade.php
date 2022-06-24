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
    <div class="error-inside">
        <div class="about-wrapper">
            <h1>TENTANG HIJABENKA</h1>
            <p>Hijabenka.com adalah e-commerce busana muslim yang didedikasikan untuk menyediakan pakaian muslim dengan gaya yang fresh dan fashionable. Kami menawarkan ragam pilihan busana muslim; mulai dari pakaian bergaya basic seperti dress, atasan, rok, tunik, hingga scarf dan aksesori. Produk-produk yang kami sediakan dipilih secara selektif untuk memenuhi kebutuhan fashion muslimah bergaya chic dan up to date, semuanya ditawarkan dengan harga yang terjangkau. Selain bertujuan menjadi e-commerce busana muslim terbaik di Indonesia, Hijabenka.com juga ingin menjadikan para Hijabi Indonesia untuk menjadi trendsetter bagi fashion muslim dunia.</p><p>Daftarkan juga email Anda ke newsletter dan follow social media Hijabenka.com untuk menjadi pertama yang tahu mengenai promo spesial dan potongan harga. Nikmati juga layangan pengiriman dan retur gratis untuk kepuasan belanja online Anda di Hijabenka.com. </p>
        </div>
    </div>
    </div>
</div>

@endsection



