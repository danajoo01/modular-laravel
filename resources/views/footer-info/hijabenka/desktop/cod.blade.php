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
				        	<h4 style="padding:1px !important;">BAYAR DI TEMPAT</h4>
				        </div>
				      <div class="full-width mb20">
				        <div class="static-content">
				          <p> <strong>Apa itu COD?</strong> </p>
				          <p> COD (<i>cash-on-delivery</i>) adalah jenis transaksi dimana pelanggan melakukan pembayaran langsung saat produk sudah diterima. Syarat dan ketentuannya adalah sebagai berikut: </p>
				          <ol>
				            <li>Pembayaran menggunakan uang tunai</li>
				            <li>Pelanggan dikenakan biaya pengiriman sesuai dengan alamat yang dituju</li>				            
				            <li>Maksimal pembelian Rp 1.000.000,- (satu juta rupiah)</li>
				            <li>Apabila ingin melakukan pengembalian barang, mohon melihat syarat dan ketentuan (<i>cek <a href="http://www.berrybenka.com/home/help_return" style="text-decoration: underline;">Ketentuan Pengembalian</a></i>) dan pelanggan dipastikan telah melakukan pembayaran terlebih dahulu ditempat</li>
				            <li>Berlaku untuk daerah - daerah seperti berikut ini :</li>
				          </ol>
				          <div id="cod" class="cod-area accordion">
				            <li class="panel">
				              <h3 href="#cod-bali" data-parent="#cod" data-toggle="collapse"> Bali <i class="fa fa-angle-down"></i></h3>
				              <ul class="collapse" id="cod-bali">
				                <li>Badung</li>
				                <li>Bangli</li>
				                <li>Buleleng</li>
				                <li>Denpasar</li>
				                <li>Gianyar</li>
				                <li>Jembrana</li>
				                <li>Karangasem</li>
				                <li>Klungkung</li>
				                <li>Tabanan</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 class="panel" href="#cod-banten" data-parent="#cod" data-toggle="collapse"> Banten <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-banten" class="collapse">
				                <li>Cilegon</li>
				                <li>Lebak</li>
				                <li>Pandeglang</li>
				                <li>Serang</li>
				                <li>Tangerang</li>
				                <li>Tangerang Selatan</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-yogya" data-parent="#cod" data-toggle="collapse">D.I Yogyakarta <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-yogya" class="collapse">
				                <li>Bantul</li>
				                <li>Gunung Kidul</li>
				                <li>Kulon Progo</li>
				                <li>Sleman</li> 
				                <li>Yogyakarta</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-dki" data-parent="#cod" data-toggle="collapse">DKI Jakarta <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-dki" class="collapse">
				                <li>Jakarta Barat</li>
				                <li>Jakarta Pusat</li>
				                <li>Jakarta Selatan</li>
				                <li>Jakarta Timur</li>
				                <li>Jakarta Utara</li>
				                <!--<li>Kepulauan Seribu</li>-->
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-jabar" data-parent="#cod" data-toggle="collapse">Jawa Barat <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-jabar" class="collapse">
				                <li>Bandung</li>
				                <li>Bandung Barat</li>
				                <li>Banjar</li>
				                <li>Bekasi</li>
				                <li>Bogor</li>
				                <li>Ciamis</li>
				                <li>Cianjur</li>
				                <li>Cimahi</li>
				                <li>Cirebon</li>
				                <li>Cirebon Kota</li>
				                <li>Depok</li>
				                <li>Garut</li>
				                <li>Indramayu</li>
				                <li>Karawang</li>
				                <li>Kuningan</li>
				                <li>Majalengka</li>
				                <li>Pangandaran</li>
				                <li>Purwakarta</li>
				                <li>Subang</li>
				                <li>Sukabumi</li>
				                <li>Sumedang</li>
				                <li>Tasikmalaya</li>
				                <li>Kota Bandung</li>
				                <li>Kota Bekasi</li>
				                <li>Kota Bogor</li>
				                <li>Kota Cirebon</li>
				                <li>Kota Sukabumi</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-jateng" data-parent="#cod" data-toggle="collapse">Jawa Tengah <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-jateng" class="collapse">
				                <li>Banjarnegara</li>
				                <li>Banyumas</li>
				                <li>Batang</li>
				                <li>Blora</li>
				                <li>Boyolali</li>
				                <li>Brebes</li>
				                <li>Cilacap</li>
				                <li>Demak</li>
				                <li>Grobogan</li>
				                <li>Jepara</li>
				                <li>Karanganyar</li>
				                <li>Kebumen</li>
				                <li>Kendal</li>
				                <li>Klaten</li>
				                <li>Kudus</li>
				                <li>Magelang</li>
				                <li>Pati</li>
				                <li>Pekalongan</li>
				                <li>Pemalang</li>
				                <li>Purbalingga</li>
				                <li>Purworejo</li>
				                <li>Rembang</li>
				                <li>Salatiga</li>
				                <li>Semarang</li>
				                <li>Sragen</li>
				                <li>Sukoharjo</li>
				                <li>Surakarta/Solo</li>
				                <li>Tegal</li>
				                <li>Temanggung</li>
				                <li>Wonogiri</li>
				                <li>Wonosobo</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-jatim" data-parent="#cod" data-toggle="collapse">Jawa Timur <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-jatim" class="collapse">
				                <li>Bangkalan</li>
				                <li>Banyuwangi</li>
				                <li>Batu</li>
				                <li>Blitar</li>
				                <li>Bojonegoro</li>
				                <li>Bondowoso</li>
				                <li>Gresik</li>
				                <li>Jember</li>
				                <li>Jombang</li>
				                <li>Kediri</li>
				                <li>Lamongan</li>
				                <li>Lumajang</li>
				                <li>Madiun</li>
				                <li>Magetan</li>
				                <li>Malang</li>
				                <li>Mojokerto</li>
				                <li>Nganjuk</li>
				                <li>Ngawi</li>
				                <li>Pacitan</li>
				                <li>Pamekasan</li>
				                <li>Pasuruan</li>
				                <li>Ponorogo</li>
				                <li>Probolinggo</li>
				                <li>Sampang</li>
				                <li>Sidoarjo</li>
				                <li>Situbondo</li>
				                <li>Sumenep</li>
				                <li>Surabaya</li>
				                <li>Trenggalek</li>
				                <li>Tuban</li>
				                <li>Tulungagung</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-kaltim" data-parent="#cod" data-toggle="collapse">Kalimantan Timur <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-kaltim" class="collapse">
				                <li>Balikpapan</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-kalsel" data-parent="#cod" data-toggle="collapse">Kalimantan Selatan <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-kalsel" class="collapse">
				                <li>Banjarmasin</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-kalbar" data-parent="#cod" data-toggle="collapse">Kalimantan Barat <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-kalbar" class="collapse">
				                <li>Pontianak</li>
				              </ul>
				            </li>
				                        <li class="panel">
				              <h3 href="#cod-sulsel" data-parent="#cod" data-toggle="collapse">Sulawesi Selatan <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-sulsel" class="collapse">
				                <li>Makassar</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-sulut" data-parent="#cod" data-toggle="collapse">Sulawesi Utara <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-sulut" class="collapse">
				                <li>Manado</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-sumut" data-parent="#cod" data-toggle="collapse">Sumatra Utara <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-sumut" class="collapse">
				                <li>Medan</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-sumsel" data-parent="#cod" data-toggle="collapse">Sumatra Selatan <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-sumsel" class="collapse">
				                <li>Palembang</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-riau" data-parent="#cod" data-toggle="collapse">Riau <i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-riau" class="collapse">
				                <li>Pekanbaru</li>
				              </ul>
				            </li>
				            <li class="panel">
				              <h3 href="#cod-papua" data-parent="#cod" data-toggle="collapse">Papua<i class="fa fa-angle-down"></i></h3>
				              <ul id="cod-papua" class="collapse">
				                <li>Jayapura</li>
				              </ul>
				            </li>
				          </div>
				        </div>
				      </div>
				      <div class="full-width mb20">
				        <div class="static-content">
				          <p> INFORMASI </p>
				          <p> 
                                            Hijabneka menggunakan jasa Ninjavan untuk wilayah JABODETABEK, dan untuk wilayah di luar JABODETABEK menggunakan JX dan J&amp;T .                                        
                                          </p>
				        </div>
				      </div>
				    </div>
				  </div>
            </div>
        </div>
    </div>
</div>

@endsection



