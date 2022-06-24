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
        <div class="error-inside">
            <div class="about-wrapper tnc-wrapper feature-brand">
                <h1>PELUANG MENJADI SUPPLIER</h1>
                <p>Ingin menjadi bagian dari Hijabenka.com? Daftarkan brand/ produk Anda ke brand@berrybenka.com</p>
                <h5>Semua Kebutuhan Hijabmu Ada di Sini!</h5>
                <p>Hijabenka.com adalah e-commerce busana muslim yang didedikasikan untuk menyediakan pakaian muslim dengan gaya yang fresh dan fashionable. Produk-produk yang hadir di Hijabenka.com telah melalui proses kurasi selektif untuk memastikan kualitas terbaik bagi konsumen kami.</p>
                <p>Dengan menjadi bagian dari Hijabenka.com, produk/ brand Anda akan hadir dengan tampilan yang profesional juga dipromosikan kepada konsumen di seluruh Indonesia melalui media promosi online dan offline kami.</p>
            </div>
        </div>
    </div>
</div>

@endsection



