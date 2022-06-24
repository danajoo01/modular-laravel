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
			          <div>
			            <h4>KETENTUAN PENGIRIMAN SAME-DAY DAN NEXT DAY DELIVERY</h4>
			          </div>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>WAKTU PENGIRIMAN</a>
			                        <div class="sameday">
			                        <p class="sameday-first"><b>Same-day Delivery</b></p>
			                        <p>
			                        Sekarang kamu dapat menikmati layanan <em>Same-day Delivery</em> di Hijabenka. Artinya, barang kamu akan kami antarkan di hari yang sama dengan hari pemesananmu, apabila pemesanan dilakukan <strong>sebelum pukul 11.00 WIB di hari kerja (hari Senin &ndash; Kamis) dan 10.30 (Jumat)</strong>. Beli hari ini, kenakan hari ini!
			                        </p><br>
			                        <p class="sameday-first"><b>Next Day Delivery</b></p>
			                        <p>
			                        Kelewatan pesan sebelum jam 11 padahal mau pakai baju baru untuk kencan besok malam? Jangan khawatir! Kamu dapat menikmati layanan <em>Next Day Delivery</em> dari Hijabenka! Dengan layanan <em>Next Day Delivery</em>, kamu akan menerima barang 1 (satu) hari kerja setelah kamu melakukan pemesanan di Hijabenka. Ga perlu tunggu lama lagi!<br>
			                        Jika alamat kamu berada di daerah perkantoran, maka pesanan kamu akan kami kirimkan paling lambat pukul 18.00 WIB. Sedangkan, jika alamat ditujukan ke rumah/apartment maka akan kami kirimkan paling lambat hingga pukul 20.00 WIB.<br><br>
			    
			                        *<em>Saat ini kami belum bisa melakukan pengiriman di hari Sabtu, Minggu dan Hari libur nasional lainnya</em>
			                        </p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>BIAYA PENGIRIMAN</a>
			                        <div class="sameday">
			                        <p class="sameday-first"><b>Same-day Delivery</b></p>
			                        <p>
			                         Untuk mendapatkan layanan ini, kamu cukup membayar biaya tambahan sebesar Rp 22.000 saja. Tambahan biaya kirim hanya akan muncul di layar ponsel atau desktop-mu jika kamu memilih metode pengiriman Same-day Delivery.                         </p><br>
			                        <p class="sameday-first"><b>Next Day Delivery</b></p>
			                        <p>
			                         Untuk mendapatkan layanan ini, biaya tambahannya hanya sebesar Rp 17.000 saja. Seperti halnya Same-day Delivery, tambahan biaya kirim hanya akan muncul di layar ponsel atau desktop-mu jika kamu memilih metode pengiriman Next Day Delivery.</p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>CAKUPAN WILAYAH</a>
			                        <div>
			                        	<p>
								Untuk saat ini, layanan <em>Same-day</em> dan <em>Next Day Delivery</em> hanya mencakup wilayah Jakarta (Jakarta Utara, Jakarta Barat, Jakarta Pusat, Jakarta Selatan dan Jakarta Timur) dan juga Tangerang.
								</p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>AKSES LAYANAN</a>
			                        <div>
			                            <p>
								Layanan ini sudah dapat kamu akses melalui website kami, baik melalui desktop ataupun melalui ponselmu. </p>
			                    <p class="apps-download">
								Saat ini tim Hijabenka juga sedang membangun layanan yang sama di apps kami untuk memudahkan kamu yang sudah <a style="text-decoration:underline;" href="https://itunes.apple.com/us/app/berrybenka/id961924940" target="_blank">download apps IOS</a> dan <a style="text-decoration:underline;" href="https://play.google.com/store/apps/details?id=com.berrybenka.android" target="_blank">download apps Android</a> Berrybenka. Tunggu kehadirannya segera!
								</p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>CARA PEMBAYARAN</a>
			                        <div>
                                                    Layanan ini tersedia bagi kamu yang berbelanja dan melakukan pembayaran melalui kartu kredit Visa/Master. Untuk kamu yang kartu kreditnya tidak lolos verifikasi mohon dapat mengirimkan fotocopy KTP dan fotocopy kartu kredit kamu sebagai data validasi kami ke cs@berrybenka.com atau dapat menghubungi customer service kami di 021-2520555 sebelum pukul 11.00 WIB.
			                        </div>
			                    </li>
			                    <li>
			                    	<a>KETERLAMBATAN PENGIRIMAN</a>
			                        <div>

			Tentunya kami akan melakukan yang terbaik agar pesanan kamu tiba tepat waktu. Namun, apabila terjadi keterlambatan karena alasan mendesak, customer service dan kurir kami akan langsung mengabari kamu. Biaya pengiriman yang telah kamu bayarkan juga akan secara otomatis kami kembalikan dalam bentuk Benka Poin yang dapat kamu gunakan di pembelanjaan berikutnya di Hijabenka.
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



