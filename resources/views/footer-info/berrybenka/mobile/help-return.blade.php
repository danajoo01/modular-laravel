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
			            <ul class="sidebar-list">
                                        <li><a href="{{ url('/') }}/home/cod"><i class="fa fa-question-circle"></i>Bayar di tempat</a></li>
                                        <li><a href="{{ url('/') }}/home/faq"><i class="fa fa-question-circle"></i>Pertanyaan Umum</a></li>
                                        <li><a href="{{ url('/') }}/home/how_to_order"><i class="fa fa-shopping-cart"></i>Cara Pemesanan</a></li>
                                        <li class="help-active"><a href="{{ url('/') }}/home/help_return"><i class="fa fa-puzzle-piece"></i>Ketentuan Pengembalian</a></li>
                                        <li><a href="{{ url('/') }}/home/help_return_watch"><i class="fa fa-compass"></i>Ketentuan Pengembalian Produk Jam Tangan</a></li>
                                        <li><a href="{{ url('/') }}/home/shipping_handling"><i class="fa fa-truck"></i>Ketentuan Pengiriman</a></li>
                                        <li><a href="{{ url('/') }}/home/same-day"><i class="fa fa-question-circle"></i>Same Day &amp; Next Day Delivery</a></li>
                                    </ul>      
			        </div>
			    </div>
			    <div class="eleven columns">
			        <div class="category-head">
			        	<h4 style="padding:1px !important;">KETENTUAN PENGEMBALIAN</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>SYARAT DAN KETENTUAN PENGEMBALIAN</a>
			                        <div>
			                        	<ul>
			                                <li>Anda dapat melakukan pengembalian barang dalam jangka waktu 14 hari (termasuk hari libur) terhitung sejak barang Anda terima. Saat Anda menerima barang sudah terhitung sebagai 1 hari.</li>
			                                <li>Produk harus dikirimkan dalam kondisi asli dan berada dalam kotak kemasan lengkap dengan aksesoris terkait dan "hang tags".</li>
			                                <li>Produk tidak dalam keadaan rusak, kotor, telah dipakai, dan&nbsp;tercelup/terkena air.</li>
			                                <li>Kemasan utuh dan tidak rusak,&nbsp;penyok, pecah, sobek, terlipat atau tergores.</li>
			                                <li>Harap tidak mengisolasi kotak secara berlebihan tetapi cukup membungkusnya untuk mencegah kerusakan. Barang tersebut tetap menjadi tanggung jawab Anda sampai Berrybenka menerimanya.</li>
			                                <li>Mohon bantuan Anda untuk mengembalikan paket dengan hati-hati.</li>
			                                <li>Pengembalian poduk tidak berlaku untuk kategori lingerie dan beauty.</li>
			                                <li>Pihak Berrybenka akan melakukan pengecekan kembali akan kualitas produk yang dikembalikan. Apabila&nbsp;ada persyaratan yang tidak dipenuhi, Berrybenka berhak menolak pengembalian produk tersebut.</li>
			                                <li>Untuk pengembalian barang hanya dapat dilakukan dengan memilih salah satu metode pengembalian :
			                                    <ul style="list-style: outside;">
			                                        <li>Penukaran barang yang sama (warna/ukuran)</li>
			                                        <li>Pengembalian dana (Refund rekening/kredit)</li>
			                                    </ul>
			                                </li>
			                                <li>Mengisi formulir pengembalian barang dengan lengkap dan benar sesuai dengan petunjuk pengisian.</li>
			                                <li>1 nomor pesanan hanya dapat memilih 1 jenis metode pengembalian (tukar barang/refund/kredit)</li>
			                            </ul>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>LAMA PENGEMBALIAN BARANG</a>
			                        <div>
			                        	<ul>
			                                <li>Pengembalian barang dari alamat Anda akan memakan waktu 1-3 hari kerja (untuk Jakarta) atau 2-6 hari kerja (untuk luar Jakarta) sampai di gudang Berrybenka. Lamanya waktu juga dipengaruhi oleh jasa pengiriman yang Anda pilih.</li>
			                                <li>Retur/ refund Anda akan selesai kami proses 3-4 hari kerja semenjak barang retur diterima di warehouse kami.</li>
			                            </ul>	
			                        </div>
			                    </li>
			                    <?php /*
			                    <li>
			                    	<a>GRATIS BIAYA PENGEMBALIAN BARANG</a>
			                        <div>
			                        	<ol> 
                                                        <li>Anda diwajibkan menyertakan bukti resi pengiriman barang saat mengembalikan barang ke Warehouse Berrybenka dan mengirimkan foto bukti resi ke <a href="mailto:cs@berrybenka.com" target="_top" style="display:inline;">cs@berrybenka.com</a> sebelum barang retur diterima di Warehouse Berrybenka.</li>
                                                        <li>Biaya pengembalian barang akan diproses dalam waktu 2 x 24 jam sejak barang retur diproses di Warehouse Berrybenka.</li>
                                                        <li>Biaya pengiriman di bawah Rp10.000 akan dikreditkan ke akun Berrybenka Anda sedangkan untuk biaya pengiriman di atas Rp 10.000 akan di refund ke rekening Anda (apabila Anda mencantumkan nomor rekening Anda).</li>
                                                        <li>Untuk penggantian biaya pengembalian barang akan disesuaikan dengan standar tarif reguler dari JNE. Berrybenka akan melakukan penggantian biaya maksimal senilai Rp.30.000,-.</li>
                                                        <li>Berrybenka tidak akan mengganti biaya pengembalian barang yang dikirim ke warehouse Berrybenka dengan metode COD (Cash on Delivery).</li>                                                                                                                                                                        
                                                    </ol>
			                            <span class="city-choose">
			                            	<p style="margin:5px 0 !important;">Pilih Kota/ Propinsi : </p>
			                                <span>
			                                	<select>
			                                        <option value="">Jakarta</option>
			                                    </select>
			                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
			                                </span>
			                                <span>
			                                	<select>
			                                        <option value="">Jakarta</option>
			                                    </select>
			                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
			                                </span>
			                            </span>
			                        </div>
			                    </li>
			                    */?>
			                </ul>
			            </div>
			          
			        </div>
			      </div>
			     <div class="thx-wrapper">
                                <a href="/home/download_pdf">Unduh form retur disini  <i class="fa fa-caret-right"></i></a>
                            </div>
			    </div>
                              
			  </div>
            </div>
        </div>
    </div>
</div>

@endsection



