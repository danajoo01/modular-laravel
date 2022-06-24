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
                        <img src="/shopdeca/desktop/img/affiliate/banner.jpg">
                    </a>
                    <p>SHOPDECA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner SHOPDECA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.</p>
                   
                </div>
               <div class="affiliate-faq">
               		<h2>FAQ</h2>
                    <ol>
                        <li>
                            <p>Apa itu program affiliate Shopdeca ?</p>
                            <span>SHOPDECA affiliate program adalah program kerjasama yang menguntungkan untuk para pemilik blog atau website melalui link afiliasi. Anda sebagai partner hanya  perlu menampilkan link afiliasi yang telah kami sediakan dan Anda akan mendapatkan keuntungan berupa komisi untuk setiap transaksi dari link yang Anda referensikan.</span>
                        </li>
                        <li>
                            <p>Mengapa harus bergabung dalam program affiliate Shopdeca ?</p>
                            <span>Pertama Shopdeca.com adalah situs belanja online fesyen dan kecantikan ternama di Indonesia. Shopdeca menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label yang update mengikuti trend terkini,  memiliki lebih dari 20 juta page views dan  1,5 juta unique visitors perbulannya, komisi  hinga 10 % yang tentunya dapat memberikan keuntungan lebih bagi Anda, dan berbagai affiliate program yang menarik yang mendukung kinerja Anda.</span>
                        </li>
                        <li>
                            <p>Bagaimana cara bergabung menjadi partner affiliate Shopdeca ?</p>
                            <span>Untuk bergabung menjadi partner affiliate Shopdeca, Anda hanya harus melakukan sign up sebagai partner affiliate dalam website ini. Untuk mendaftar silahkan klik <a href="http://shopdeca.hasoffers.com/signup" target="_blank">disini</a>.</span>
                        </li>
                        <li>
                            <p>Siapa saja yang dapat bergabung dalam program affiliate Shopdeca ?</p>
                            <span>Siapapun yang memiliki blog atau website dapat bergabung  menjadi partner affiliate.</span>
                        </li>
                        <li>
                            <p>Bagaimana cara masuk ke dashboard akun affiliate saya ?</p>
                            <span>Login dengan menggunakan email dan password yang Anda gunakan untuk mendaftar, maka Anda akan masuk ke dalam dashboard affiliate Anda. Untuk masuk ke halaman login, silahkan klik<a href="https://shopdeca.hasoffers.com/login" target="_blank"> disini</a>.</span>
                        </li>
                        <li>
                            <p>Berapa besar komisi yang diberikan Shopdeca ?</p> 
                            <span>Komisi merupakan hak Anda sebagai partner affiliate ketika terjadi transaksi dari link afiliasi yang direfensikan, adapun besar komisi yang Anda dapatkan dalam program affiliate Shopdeca adalah sebesar 10% dari setiap transaksi (sesuai ketentuan pajak yang berlaku).</span>
                        </li>
                        <li>
                            <p>Bagaimana dan kapan saya akan mendapatkan komisi ?</p>
                            <span>Pembayaran komisi akan dilakukan secara periodik setiap bulannya dan akan dibayarkan melalui transfer ke rekening aktif yang telah Anda daftarkan.<br>Jika ada perubahan nomor  rekening bank silahkan hubungi kami melalui <a href="mailto:affiliate@shopdeca.com">affiliate@shopdeca.com</a></span>
                        </li>
                        <li>
                            <p>Bagaimana cara melihat pendapatan dan performa saya ?</p>
                            <span>Login ke dalam dashboard akun affiliate Anda dan lihat pada menu Report. Untuk masuk ke halaman login, silahkan klik <a href="https://shopdeca.hasoffers.com/login">disini</a>.</span>
                        </li>
                    </ol>
               </div>
                               
            </div>
        </div>
    </div>
</div>

@endsection



