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
			                                <li>Harap tidak mengisolasi kotak secara berlebihan tetapi cukup membungkusnya untuk mencegah kerusakan. Barang tersebut tetap menjadi tanggung jawab Anda sampai Hijabneka menerimanya.</li>
			                                <li>Mohon bantuan Anda untuk mengembalikan paket dengan hati-hati.</li>
			                                <li>Pengembalian poduk tidak berlaku untuk kategori lingerie dan beauty.</li>
			                                <li>Pihak Hijabenka akan melakukan pengecekan kembali akan kualitas produk yang dikembalikan. Apabila&nbsp;ada persyaratan yang tidak dipenuhi, Hijabenka berhak menolak pengembalian produk tersebut.</li>
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
			                                <li>Pengembalian barang dari alamat Anda akan memakan waktu 1-3 hari kerja (untuk Jakarta) atau 2-6 hari kerja (untuk luar Jakarta) sampai di gudang Hijabenka. Lamanya waktu juga dipengaruhi oleh jasa pengiriman yang Anda pilih.</li>
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
			                            <id class="city-choose" id="create-shipping">
                                                        <form method="post" id="account" action="#">
			                            	<p style="margin:5px 0 !important;">Pilih Kota/ Propinsi : </p>
			                                <span>
                                                            <select onChange="requestCity();" name="shipping_area" required id="shipping_area">
			                                        <option value>Provinsi</option>
                                                                @foreach ($list_province as $area)
                                                                   <option value="{{$area->shipping_area}}">{{$area->shipping_area}}</option>
                                                                @endforeach
			                                    </select>
			                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
			                                </span>                                                        
			                                <span>
			                                	<select required id="shipping_name" name="shipping_name" onChange="requestCityReturnFee();">
			                                        <option value="">Kota</option>
                                                                
			                                    </select>
			                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
			                                </span>
                                                        <span id="load_ship_city" style="display: none;border:none;width:60px;"><i class="fa fa-refresh fa-spin"></i> Loading</span>
                                                        <span id="shipping_fee" style="border:none;width:150px;"></span>
                                                        </form>
			                            </id>
			                        </div>
			                    </li>
                          */?>
<!--                                            <li>
			                    	<a>CARA MENGEMBALIKAN BARANG</a>
			                        <div>
                                                    Siapkan barang yang akan dikembalikan dengan membungkus kembali barang tersebut ke kotak kemasannya<br /><br />	
                                                    Sertakan formulir pengembalian pada saat melakukan pengiriman barang. Apabila tidak, Berrybenka berhak untuk menolak proses pengembalian lebih lanjut (Form pengembalian dapat ditemukan di dalam kotak kemasan paket pengiriman Anda).
			                        </div>
			                    </li>-->
<!--                                            <li>
			                    	<a>PENGISIAN FORMULIR PENGEMBALIAN BARANG ONLINE (ONLINE RETURN FORM)</a>
			                        <div>
                                                  Berikut adalah panduan pengisian online return form:
                                                  <ol style="list-style-type: decimal;">
                                                  <li>Masuk ke menu akun Anda di <a href="http://www.berrybenka.com">www.berrybenka.com</a></li>
                                                  <li>Klik Menu ‘Form Retur’</li>
                                                  <li>Pilih nama barang yang akan dikembalikan,  klik ‘Retur’</li>
                                                  <li>Isi tujuan Anda melakukan  pengembalian : 
                                                          <ul style="list-style-type: disc;">
                                                          <li>Penukaran barang yang sama (warna/ukuran)</li>
                                                          <li>Pengembalian dana (Refund rekening / kredit)</li>
                                                          </ul>
                                                  </li>
                                                  <li>Isi alasan Anda melakukan  pengembalian : 
                                                  <ul style="list-style-type: disc;">
                                                          <li>Berbeda dengan di website</li>
                                                          <li>Ukuran tidak sesuai</li>
                                                          <li>Tidak sesuai pesanan</li>
                                                          <li>Kualitas tidak baik</li>
                                                          <li>Tidak cocok</li>
                                                          <li>Product cacat</li>
                                                  </ul>
                                                  </li>
                                                  <li>Lalu klik ‘Proses’</li>
                                                  <li>Tunggu notifikasi melalui email atau informasi dari tim Customer Service Berrybenka</li>
                                                  </ol>
                                                </div>
			                    </li>-->
<!--                                            <li>
			                    	<a>MENGKREDITKAN UANG DARI BARANG YANG DIKEMBALIKAN DENGAN MENGGUNAKAN BENKA POIN</a>
			                        <div>
                                                    Benka Poin hanya akan diberikan apabila barang yang dikembalikan masih dalam kondisi belum terpakai dan tidak rusak dengan tags masih terpasang.. Benka Poin yang diberikan dapat digunakan untuk pembelian berikutnya di Berrybenka. Benka Poin tidak dapat diuangkan.
			                        </div>
			                    </li>-->
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

<!-- start requestcity -->
<script type="text/javascript">
  shipping_price = new Array();
  
function requestCity() {
  $.ajax({
  url : '{{ url('/') }}/checkout/json_get_shipping_list',
  type : 'post',
  data : $("#account").serialize(),
  beforeSend : function () {
    $("#load_ship_city").show();
    $('#shipping_fee').empty();
  },
  success : function (data) {
      //var tgh = document.getElementById("shipping_name");
      //tgh.innerHTML = data;
      var area = $('#shipping_area').val();
      
      if(area != ''){
        if(data){
          var obj = jQuery.parseJSON(data);
          $("#load_ship_city").hide();      
          $('#shipping_name').empty();
          
          $('#shipping_name').append('\
            <option>Kota</option>\
          ');
          shipping_price = new Array();
          $.each(obj.list_shipping, function (key, value) {
            $('#shipping_name').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
            shipping_price[value.shipping_name] = value.shipping_price;            
          }); 
        }else{
          $("#load_ship_city").hide(); 
        }
      }else{
        $('#shipping_name').empty();
        $('#shipping_name').append('\
          <option value=>Kota</option>\
        ');         
        $("#load_ship_city").hide(); 
      }  
        
    }
  });

  
}

function requestCityReturnFee() {
  $('#shipping_fee').empty();
  var city = $('#shipping_name').val();
  if(city == ''){    
    $('#shipping_fee').empty();
  }else{
    $('#shipping_fee').html(shipping_price[city]);  
  }
  
}
</script>
<!--  end requestcity  -->

@endsection



