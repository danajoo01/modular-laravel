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
				                <li class="help-active"><a href="{{ url('/') }}/home/faq"><i class="fa fa-question-circle"></i>Pertanyaan Umum</a></li>
				                <li><a href="{{ url('/') }}/home/how_to_order"><i class="fa fa-shopping-cart"></i>Cara Pemesanan</a></li>
				                <li><a href="{{ url('/') }}/home/help_return"><i class="fa fa-puzzle-piece"></i>Ketentuan Pengembalian</a></li>
				                <li><a href="{{ url('/') }}/home/help_return_watch"><i class="fa fa-compass"></i>Ketentuan Pengembalian Produk Jam Tangan</a></li>
				                <li><a href="{{ url('/') }}/home/shipping_handling"><i class="fa fa-truck"></i>Ketentuan Pengiriman</a></li>
				                <?php /*
				                <li><a href="{{ url('/') }}/home/same-day"><i class="fa fa-question-circle"></i>Same Day &amp; Next Day Delivery</a></li>*/?>
				            </ul>      
				        </div>
				    </div>
				    <div class="eleven columns">
				        <div class="category-head">
				        	<h4 style="padding:1px !important;">PERTANYAAN UMUM</h4>
				        </div>
				      <div class="full-width mb20">
				        <div class="static-content">
				          <p>Apakah Berrybenka.com memiliki toko retail?</p>
				          <p>Tidak, Berrybenka.com adalah sebuah departemen store online di mana Anda dapat berbelanja 24 jam setiap hari. Produk-produk Berrybenka.com dapat dipesan melalui website, mobile application dan agen pelayanan pelanggan. Pesanan akan dikirim langsung ke alamat Anda.</p>
				          	
				            <div class="list-q">
				            	<ul>
				                	<li>
				                    	<a>Apakah saya harus membuat akun untuk berbelanja di Berrybenka.com?</a>
				                        <div>Ya. Memiliki akun akan mempermudah proses belanja karena tidak perlu untuk menulis data pribadi dan alamat setiap kali Anda berbelanja. Anda juga bisa mendapatkan newsletter yang didalamnya terdapat info diskon, penawaran khusus dan manfaat lainnya.</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana Saya bisa mendapatkan akun Berrybenka.com?</a>
				                        <div>
				Anda dapat melakukan registrasi dengan mengklik tautan "Log in / Sign up" pada bagian pojok kanan atas halaman website. Lalu isi identitas lengkap Anda pada bagian "NOT REGISTER YET". Mohon pastikan bahwa identitas yang Anda isi adalah benar, kemudian tekan tombol "Create an Account". Setelahnya, Anda memiliki akun Berrybenka! Mohon untuk mengisi alamat lengkap Anda pada menu ???Account - Edit Address Detail???.</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana cara mengubah data pribadi saya di Berrybenka.com?</a>
				                        <div>Anda dapat login dan akses halaman "My Account" untuk mengubah profil pribadi atau data lainnya.</div>
				                    </li>
				                    <li>
				                    	<a>Apa yang terjadi setelah saya menekan tombol "Check Out"?</a>
				                        <div>Barang hanya akan dikirim setelah kami menerima pembayaran Anda. Saat Anda telah menyelesaikan proses pembayaran, Berrybenka menyarankan agar Anda melakukan konfirmasi dengan meng-klik pilihan "Confirm" pada halaman. "My Account". Saat proses konfirmasi selesai, Berrybenka akan segera memproses pesanan Anda dan mengirimkannya ke alamat yang telah dicantumkan.</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana cara saya menggunakan kode diskon?</a>
				                        <div>Saat akan melakukan Checkout, Anda dapat mengisikan kode tersebut. Setiap transaksi hanya dapat menggunakan satu kode. Mohon perhatikan syarat dan ketentuan yang tercantum pada kode diskon tersebut.</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana saya bisa menggunakan Promo / Gift Voucher Berrybenka?</a>
				                        <div>Saat Anda akan melakukan SUBMIT ORDER, masukan kode voucher pada kolom COUPON CODE (tulis sesuai dengan penggunaan huruf besar dan huruf kecil). Pastikan Anda telah menuliskannya dengan benar. Setelahnya, klik REDEEM COUPON. Nominal pembayaran Anda akan dikurangi sesuai dengan jumlah voucher secara otomatis. Harap pastikan syarat dan ketentuan yang berlaku.</div>
				                    </li>
				                    <li>
				                    	<a>Apakah yang dimaksud dengan Benka Poin?</a>
				                        <div>Benka Poin adalah sejumlah nominal yang tersimpan dalam akun Berrybenka Anda. Anda bisa menggunakan Benka Poin untuk melakukan pembelian pada Berrybenka.com</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana saya dapat menggunakan Benka Poin?</a>
				                        <div>Benka Poin dapat digunakan secara langsung saat Anda ingin berbelanja di Berrybenka.com. Jumlah Benka Poin akan mengurangi total pembayaran Anda. Benka Poin tidak dapat digabungkan dengan Promo atau Voucher tertentu</div>
				                    </li>
				                    <li>
				                    	<a>Apakah yang di maksud dengan handling charges?</a>
				                        <div>Ini adalah biaya tambahan yang dibutuhkan untuk memproses sebuah barang termasuk inspeksi, pengemasan, pengangkutan, transportasi darat dan dokumentasi. Biaya ini akan dimasukkan ke dalam biaya kirim.</div>
				                    </li>
				                    <li>
				                    	<a>Apakah proses pembayaran di Berrybenka.com aman?</a>
				                        <div>Proses pembayaran kami aman dan kami berkomitmen untuk selalu menjaga data pribadi Anda. Data Anda aman dan tidak akan digunakan untuk tujuan penggunaan lainnya selain Berrybenka.com.</div>
				                    </li>
				                    <li>
				                    	<a>Apakah saya bisa mengganti atau membatalkan pesanan saya?</a>
				                        <div>Jika Anda belum melakukan pembayaran ke rekening kami (via transfer bank ke rekening Berrybenka atau Cash-On-Delivery), Anda masih bisa membatalkan pesanan dengan menginformasikan ke pelayanan pelanggan. Namun, setelah Anda melakukan pembayaran, pesanan Anda tidak dapat dibatalkan atau diganti.</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana jika saya menerima barang yang berbeda dengan pesanan?</a>
				                        <div>Jika hal ini terjadi, Anda dapat menghubungi pelayanan pelanggan kami untuk mengatur pergantian barang atau pengembalian uang.</div>
				                    </li>
				                    <li>
				                    	<a>Apakah produk yang telah dibeli bisa dikembalikan?</a>
				                        <div>Produk yang telah dibeli dapat dikembalikan dalam jangka waktu maksimal 30 hari setelah Anda menerima produk tersebut. Barang yang dikembalikan harus dalam kondisi yang utuh dan belum terpakai. Untuk penjelasan lebih lengkap, lihat halaman return policy.</div>
				                    </li>
				                    <li>
				                    	<a>Darimana saya bisa memperoleh informasi / promo terbaru dari Berrybenka.com?</a>
				                        <div>Anda bisa mengetahui promo terbaru dengan melakukan dengan menginput alamat email Anda pada kolom "Subscribe Newsletter". Kolom tersebut dalam Anda temukan pada bagian sudut kanan bawah dari halaman website. Selain itu, Anda juga bisa mendapatkan informasi terbaru dengan mengikuti akun Berrybenka di media social (Twitter, Facebook, Instagram dan Youtube).</div>
				                    </li>
				                    <li>
				                    	<a>Bagaimana saya dapat menghubungi pelayanan pelanggan?</a>
				                        <div> Untuk informasi, saran dan kritik, Anda dapat menghubungi petugas pelayanan pelanggan melalui jalur yang paling nyaman menurut Anda.
				                            <ol>
				                                <?php /*<li>Telepon		: 021 2520555</li>*/?>
				                                <li>SMS			: 0812 8880 9992</li>
				                                <li>Email		: cs@berrybenka.com</li>
				                                <li>Waktu kerja	: 
				                                    <ol>
				                                        <li>Senin &ndash; Jumat (9 pagi &ndash; 8 malam)</li>
				                                        <li>Sabtu &ndash; Minggu (8 pagi &ndash; 5 sore)</li>
				                                    </ol>
				                                </li>
				                            </ol>
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



