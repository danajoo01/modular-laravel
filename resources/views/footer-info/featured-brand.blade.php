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
                <h1>DAFTARKAN BRAND ANDA</h1>
                <p> Jika kamu memiliki semangat dalam bidang fashion yang sama dengan kami dan ingin mengembangkan Brand/ produk/ koleksi pakaian/ aksesoris kamu bersama Berrybenka, kamu hanya perlu mengirimkan detail produk kamu ke brand@berrybenka.com</p>
                <h5>Semua Kebutuhan Fashionmu Ada di Sini!</h5>
                <p>Berrybenka adalah tempat dimana kamu bisa mendapatkan semua kebutuhan fashion kamu. Kami selalu meyediakan pilihan produk fashion terkini dan trendy. Dengan menampilkan koleksi brand kamu di Berrybenka.com, brand kamu akan mendapatkan perhatian dari pengguna internet yang besar di Indonesia!</p>
                <p>Kami akan memasarkan brand kamu secara online dan offline dengan menyediakan fotografer dan model yang akan menunjang penampilan produk kamu di sini. Dengan kerjasama ini, kami akan menjaga brand image kamu sekaligus memperluas jaringan pembeli yang bisa kamu dapat.</p>
            </div>
        </div>
    </div>
</div>

@endsection



