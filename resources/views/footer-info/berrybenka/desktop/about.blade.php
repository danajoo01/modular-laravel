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
    <div class="error-inside error-inside-fix" style="position:static;">
        <div class="about-wrapper">
            <h1>TENTANG BERRYBENKA</h1>
            <p>Berrybenka.com adalah situs belanja online fesyen dan kecantikan ternama di Indonesia. Berrybenka menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label.Berrybenka menawarkan kombinasi produk fesyen dan kecantikan terkini untuk setiap gaya personal yang beragam.</p><p>Kami menyediakan produk berkualitas terbaik untuk wanita dan pria, bervariasi dari pakaian, aksesori, sepatu, tas, produk olahraga dan kecantikan. Komitmen kami adalah memberikan pengalaman belanja online yang menyenangkan, mudah, dan terpercaya untuk memuaskan pelanggan dengan koleksi baru dan penawaran spesial setiap harinya, serta beragam keuntungan seperti kemudahan pengembalian produk hingga 14 hari setelah barang diterima, layanan bayar di tempat dan pengiriman gratis. </p>
        </div>
    </div>
    </div>
</div>

@endsection



