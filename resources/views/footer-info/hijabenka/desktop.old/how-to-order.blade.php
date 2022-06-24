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
			        	<h4 style="padding:1px !important;">CARA PEMESANAN</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			          <div class="how-order">
			          	<ul>
			            	<li>Pilih Produk</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Tas Belanja</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Masuk / Daftar</li>
			                <li><i class="fa fa-long-arrow-right" aria-hidden="true"></i></li>
			                <li>Pembayaran</li>
			            </ul>
			          </div>
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>INFORMASI PENTING</a>
			                        <div>Anda akan melakukan pembayaran melalui transfer ke rekening Hijabenka. Produk yang dijual menggunakan ‘first-pay-first-serve’ dimana barang yang Anda pesan dapat dibeli oleh pelanggan lain sebelum Anda melakukan pembayaran. Pesanan yang Anda lakukan akan secara otomatis dibatalkan apabila pembayaran tidak dilakukan dalam waktu 48 jam.</div>
			                    </li>
			                    <li>
			                    	<a>KONFIRMASI PEMBAYARAN</a>
			                        <div>
                                Untuk pembayaran dengan metode bank transfer, silahkan konfirmasikan pembayaran Anda langsung di notifikasi yang Anda dapatkan setelah selesai berbelanja dan setelah Anda melakukan pembayaran. Atau konfirmasikan pembayaran Anda di email konfirmasi yang telah kami kirimkan ke email Anda dengan langkah-langkah sebagai berikut : <br>
                                <ul>
                                  <li>Tekan tombol "Konfirmasi Pembayaran"</li>
                                  <li>Masuk ke menu "Order Anda"</li>
                                  <li>
                                    Dibagian bawah sebelah kiri ada keterangan "METODE PEMBAYARAN", harap masukkan<br>
                                    <ul>
                                      <li>Nama Bank beserta Nama Pemilik Rekening</li>
                                      <li>Nominal yang Anda transfer (harus sesuai dengan grand total yang Anda bayarkan, mohon perhatikan 3 digit angka dibelakang)</li>
                                    </ul>
                                  </li>
                                  <li>Tekan tombol "KONFIRMASI"</li>
                                  <li>Anda akan mendapatkan keterangan status pembayaran yang telah Anda konfirmasikan</li>
                                </ul>
                              </div>
			                    </li>
			                    <li>
			                    	<a>TUNGGU PESANAN</a>
			                        <div>Kami akan memverifikasi pembayaran dan mengirimkan pesanan Anda setelah pembayaran telah disetujui. Pesanan Anda akan tiba dalam waktu 1-3 hari kerja untuk wilayah Jakarta dan 2-9 hari kerja untuk luar Jakarta. Untuk barang yang berupa aerosol/minyak/air diluar jakarta akan memakan waktu 14HK untuk sampai ke tujuan.</div>
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



