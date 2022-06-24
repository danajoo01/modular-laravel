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
        <div class="error-inside error-inside-fix ref-wrap" style="position:static;">
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
			        	<h4 style="padding:1px !important;">KETENTUAN PENGEMBALIAN PRODUK JAM TANGAN</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>SYARAT DAN KETENTUAN PENGEMBALIAN PRODUK JAM TANGAN</a>
			                        <div>Batas waktu pengembalian tidak lebih dari 14 hari sejak produk jam tangan diterima pembeli. Hijabenka akan membantu menangani klaim garansi dalam rentang waktu 14 hari sejak diterimanya produk. Apabila melebihi dari 14 hari maka klaim garansi akan diarahkan langsung ke distributor resmi.
			                        </div>
			                    </li>
			                    <li>
			                    	<a>PERSYARATAN KONDISI PRODUK JAM TANGAN</a>
			                        <div>
			                        	<ol style="list-style-type: disc;">
			                                <li>Produk masih disegel</li><li>Stiker pelindung kaca belum dilepas</li>
			                                <li>Kemasan utuh dan tidak rusak, penyok, pecah, sobek, terlipat, atau tergores</li>
			                                <li>Label merek dan barcode masih tertera pada produk</li>
			                                <li>Produk tidak dalam keadaan rusak, kotor, telah dipakai, dan&nbsp;tercelup/terkena air</li>
			                                <li>Hijabenka berhak menolak pengembalian produk jika ada persyaratan yang tidak dipenuhi</li>
			                            </ol>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>PENUKARAN BARANG DAN PENGEMBALIAN UANG</a>
			                        <div>
			                        	<table class="table table-hover table-bordered">
											<tbody><tr style="font-weight: bold;" class="active text-center">
												<th rowspan="2">Perihal</th>
												<th colspan="8">Syarat dan Ketentuan Penukaran Produk Jam Tangan</th>																														
											</tr>
											<tr style="font-weight: bold;" class="active text-center">
												<th>Kondisi Baru</th>																														
												<th>Belum Digunakan</th>																														
												<th>Utuh dan Lengkap</th>																														
												<th>Tidak Rusak / Cacat</th>																														
												<th>Harga Sama</th>																														
												<th>Harga Lebih Murah</th>																														
												<th>Harga Lebih Mahal</th>																														
												<th>Merek yang sama</th>																														
											</tr>
											<tr class="text-center">
												<td>Rusak</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>&nbsp;</td>
												<td>v</td>
											</tr>
											<tr class="text-center">
												<td>Tidak sesuai website, tukar produk jam tangan dengan merek yang sama</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
												<td>v</td>
											</tr>
											</tbody>
										</table>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>PROSES PENGECEKAN PRODUK JAM TANGAN</a>
			                        <div>
			                        	<ol style="list-style-type: disc !important;">
			                            <li>Setelah produk diterima, Hijabneka akan melakukan pengecekan kualitas kembali dalam waktu 2 x 24 jam</li>
			                            <li>Proses perbaikan atau pergantian produk baru membutuhkan waktu minimal 6 hari kerja setelah produk diterima oleh distributor</li>
			                            <li>Proses perbaikan atau pergantian produk yang lebih lama akan diinformasikan oleh Customer Service</li>
			                            </ol>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>KONDISI YANG TERMASUK DALAM GARANSI PRODUK JAM TANGAN</a>
			                        <div>Kerusakan mekanis antara lain mesin tidak berfungsi, jarum jam lepas, atau baterai mati ketika produk diterima</div>
			                    </li>
			                    <li>
			                    	<a>KONDISI YANG TIDAK TERMASUK DALAM GARANSI PRODUK JAM TANGAN</a>
			                        <div>
			                        	<ol style="list-style-type: disc;">
			                            <li>Kerusakan karena kelalaian pemakaian ( jatuh / tercelup air )</li>
			                            <li>Baret dan karat akibat pemakaian</li>
			                            <li>Perawatan yang tidak tepat, kasar, atau ceroboh</li>
			                            <li>Perbaikan yang tidak dilakukan oleh pusat pelayanan resmi</li>
			                            <li>Kondisi kemasan utuh dan tidak rusak</li></ol>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>AKHIR MASA GARANSI BERLAKU</a>
			                        <div>Apabila masa berlaku garansi telah habis silakan membawa produk yang ingin diperbaiki ke alamat tujuan pusat pelayanan distributor yang tertera pada kartu garansi.</div>
			                    </li>
			                    <li>
			                    	<a>WAKTU PERBAIKAN SETELAH MASA GARANSI BERAKHIR</a>
			                        <div>Waktu yang dibutuhkan untuk memperbaiki produk jam tangan bergantung kepada tingkat kerusakan dan ketersediaan suku cadang. Hijabenka akan menginformasikan durasi perbaikan produk jam tangan.</div>
			                    </li>
			                    <li>
			                    	<a>PERIODE GARANSI PRODUK JAM TANGAN</a>
			                        <div>
			                        	Lama waktu garansi produk jam tangan berbeda-beda tergantung merek produk tersebut. Informasi garansi produk dapat dilihat pada kartu garansi.
			                        </div>
			                    </li>
			                    <li>
			                    	<a>WAKTU PENUKARAN PRODUK JAM TANGAN</a>
			                        <div>
			Pada umumnya, pengembalian produk tersebut memakan waktu 7 hari untuk Jakarta dan 15 hari untuk luar Jakarta terhitung dari saat pembeli menerima barangnya dan syarat dan ketentuan pengembaliannya telah disetujui oleh Hijabenka.
			                        </div>
			                    </li>
			                    <li>
			                    	<a>KETENTUAN PRODUK YANG MENDAPATKAN GARANSI</a>
			                        <div>Produk yang mengalami kerusakan dapat diganti dengan produk baru atau diperbaiki selama masa garansi masih berlaku
			                        </div>
			                    </li>
			                    <li>
			                    	<a>ALAMAT PUSAT PELAYANAN DISTRIBUTOR</a>
			                        <div>Alamat tujuan pusat pelayanan masing-masing produk tertera pada kartu garansi. Mohon sertakan bukti pembayaran asli dan kartu garansi sebagai bukti</div>
			                    </li>
			                    <li>
			                    	<a>KETENTUAN PENGIRIMAN PRODUK JAM TANGAN KE DISTRIBUTOR VIA HIJABENKA</a>
			                        <div>Apabila pelanggan berdomisili di lokasi yang tidak ada kantor cabang distributor resmi, maka pelanggan dapat mengirimkan produk ke Hijabenka yang selanjutnya akan meneruskan kepada pusat pelayanan distributor terdekat</div>
			                    </li>
			                    <li>
			                    	<a>BIAYA PERBAIKAN</a>
			                        <div>Selama produk masih dalam masa garansi, maka perbaikan produk jam tangan tersebut tidak akan dipungut biaya</div>
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



