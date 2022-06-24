<?php 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
<style type="text/css">
  .partner-image{
    width: 60px;
  }
</style>
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
			            <ul class="sidebar-list">
                                        <li><a href="{{ url('/') }}/home/cod"><i class="fa fa-question-circle"></i>Bayar di tempat</a></li>
                                        <li><a href="{{ url('/') }}/home/faq"><i class="fa fa-question-circle"></i>Pertanyaan Umum</a></li>
                                        <li><a href="{{ url('/') }}/home/how_to_order"><i class="fa fa-shopping-cart"></i>Cara Pemesanan</a></li>
                                        <li><a href="{{ url('/') }}/home/help_return"><i class="fa fa-puzzle-piece"></i>Ketentuan Pengembalian</a></li>
                                        <li><a href="{{ url('/') }}/home/help_return_watch"><i class="fa fa-compass"></i>Ketentuan Pengembalian Produk Jam Tangan</a></li>
                                        <li class="help-active"><a href="{{ url('/') }}/home/shipping_handling"><i class="fa fa-truck"></i>Ketentuan Pengiriman</a></li>
                                        <?php /*
                                        <li><a href="{{ url('/') }}/home/same-day"><i class="fa fa-question-circle"></i>Same Day &amp; Next Day Delivery</a></li>*/?>
                                    </ul>      
			        </div>
			    </div>
			    <div class="eleven columns">
			        <div class="category-head">
			        	<h4 style="padding:1px !important;">KETENTUAN PENGIRIMAN</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
                        <!--li>
                          <a>PENGIRIMAN LEBARAN</a>
                          <div>
                            Terkait libur Idul Fitri tahun ini yang akan jatuh di tanggal 25-26 Juni 2017, sebagian besar partner logistik pengiriman yang bekerjasama dengan Berrybenka akan menjalani libur operasional pada tanggal 24 Juni-2 Juli 2017. Dalam rangka mengantisipasi kendala pengiriman baik reguler ataupun COD yang mungkin terjadi selama peak season tersebut, untuk memastikan bahwa pesanan sampai di tangan kamu sebelum Idul Fitri maka mohon agar memperhatikan timeline sebagai berikut:<br><br>
Area tujuan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanggal terakhir memesan agar pesanan sampai sebelum libur Idul Fitri<br><br>
Jabodetabek &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;21 Juni 2017<br>
Jawa & Sumatera &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;19 Juni 2017<br>
Luar Jawa & Sumatera &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;15 Juni 2017<br><br>
Pengiriman akan kembali normal per tanggal 3 Juli 2017. Sementara itu, semua offline stores kami akan beroperasional seperti biasa selama libur Idul Fitri. Kami mohon maaf atas ketidaknyamanan tersebut, apabila ada pertanyaan lebih lanjut dapat dikirimkan via email ke cs@berrybenka.com.
                          </div>
                        </li-->
			                	<li>
			                    	<a>WAKTU PENGIRIMAN</a>
			                        <div>
								<div id="wrapshl">
                  <div id="left_col_shl">
                      <em> Daerah </em>
                  </div>
                  <div id="right_col_shl">
                      <em> Estimasi Waktu </em>
                  </div>
                  <div id="left_col_shl">
                      Jakarta
                  </div>
                  <div id="right_col_shl">
                      1 - 3 Hari Kerja
                  </div>
                  <div id="left_col_shl">
                      Jawa- Bali - Bengkulu - Sumbar
                  </div>
                  <div id="right_col_shl">
                      3 - 6 Hari Kerja
                  </div>
                  <div id="left_col_shl">
                    Bangka Belitung - Gorontalo - Riau - Sumut - Sumsel
                  </div>
                  <div id="right_col_shl">
                      3 - 7 Hari Kerja
                  </div>
                  <div id="left_col_shl">
                      Jambi - Kepulauan Riau
                  </div>
                  <div id="right_col_shl">
                      3 - 8 Hari Kerja
                  </div>
                  <div id="left_col_shl">
                      Aceh - Kalimantan - Lampung - NTB - Sulawesi
                  </div>
                  <div id="right_col_shl">
                      3 - 10 Hari Kerja
                  </div>
                  <div id="left_col_shl">
                      Maluku - NTT - Papua
                  </div>
                  <div id="right_col_shl">
                      3 - 12 Hari Kerja
                  </div>
              </div>

 <br> Waktu pengiriman ini berlaku setelah pembayaran kami terima, kecuali untuk pemesanan yang menggunakan COD. Untuk barang yang berupa aerosol/minyak/air diluar jakarta akan memakan waktu 14 hari kerja untuk sampai ke tujuan. <br><br> Kami tidak melakukan pengiriman pada hari libur nasional.					</div>
			                    </li>
			                    <li>
			                    	<a>BIAYA PENGIRIMAN</a>
                                                <div class="city-choose">
                                                            
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
                                                    <br />
			                            <P> Setiap transaksi di Berrybenka bebas ongkos kirim dengan minimum pembelian Rp. 300.000 </P>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>PARTNER LOGISTIK PENGIRIMAN</a>
			                        <div>
			                            <ul class="vendorlogo">                        
                                                        <li><a href="http://sap-express.id/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoSAP.png" width="60"></a></li>			                                
                                                        <li><a href="http://www.lionparcel.com/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoLionParcel.png" width="60"></a></li>			                                
                                                        <li><a href="https://www.ninjaxpress.co/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoNinjavan.png" width="60"></a></li>
                                                        <li><a href="https://web.etobee.com/login" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoEtobee.png" width="75"></a></li>
                                                        <li><a href="https://iruna.id/trackorder" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoIruna.png" width="60"></a></li>
                                                        <li><a href="http://islandlogistic.com/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoIslandLog.png" width="60"></a></li>
                                                        <li><a href="https://porter.id/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoPorter.png" width="60"></a></li>
                                                        <li><a href="#" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoMPS EXPRESS.png" width="60"></a></li>
                                                        <li><a href="http://www.jne.co.id/" target="_blank"><img class="partner-image" src="/berrybenka/desktop/img/shipping-vendor/logoJNE.png" width="60"></a></li>
			                            </ul>
			                            <p class="vendorwording"> Partner-partner logistik ini telah kami pilih secara ketat dan masing-masing partner logistik akan melayani pengiriman untuk wilayah yang berbeda-beda. Berrybenka memiliki kuasa penuh untuk memilih partner logistik yang dianggap paling handal untuk mengirimakan setiap barang yang dipesan dari customer. Berrybenka selalu memegang teguh kepuasan pelanggan, termasuk memastikan keamanan dan ketepatan waktu pengiriman barang. </p>
			                            <p class="vendorwording">Selain partner-partner logistik tersebut, kami juga memiliki armada pengiriman logistik bernama Berrybenka Express yang akan mengirimkan paket-paket di wilayah Jakarta. </p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>CEK STATUS PENGIRIMAN</a>
			                        <div>
			                        	<ol>
															<li>Berrybenka akan mengirimkan informasi pengiriman melalui email beserta tautan untuk cek status pesanan dan nomor resi kepada pelangan. </li>
															<li>Pelanggan dapat cek ke website masing - masing jasa pengiriman :
																<table width="400" border="0" class="list-vendor">
																	<tbody><tr><td width="35%">JNE</td><td width="5%">:</td><td><a href="http://www.jne.co.id/home.php" target="_blank">http://www.jne.co.id/</a></td></tr>																	
																	<tr><td>First Logistic</td><td>:</td><td><a href="http://www.firstlogistics.co.id/" target="_blank">http://www.firstlogistics.co.id/</a></td></tr>																																		
																	<tr><td>J-Express</td><td>:</td><td><a href="http://www.j-express.id" target="_blank">http://www.j-express.id/</a></td></tr>
                                                                                                                                        <tr><td>Ninja Van</td><td>:</td><td><a href="https://www.ninjavan.id/" target="_blank">https://www.ninjavan.id/</a></td></tr>
																	<tr><td>Berrybenka Express</td><td>:</td><td><a href="https://berrybenka.com/user/order_history" target="_blank">https://berrybenka.com/</a></td></tr>
																</tbody></table>
															</li>
															<li>Untuk Berrybenka Express, customer dapat mengecek status pengiriman melalui menu "Daftar Konfirmasi" di dalam akun dashboard customer atau bertanya langsung ke CS Berrybenka.</li>
															</ol>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>GANTI ALAMAT</a>
			                        <div>Pelanggan dapat menghubungi cs@berrybenka untuk mengganti alamat pengiriman sebelum 12 jam dari pembayaran atau konfirmasi pembayaran kami terima</div>
			                    </li>
			                    <li>
			                    	<a>APAKAH PELANGGAN DAPAT MENGIRIM KE ALAMAT YANG BERBEDA DARI ALAMAT RUMAH?</a>
			                        <div>Bisa, pada halaman check out sebelum submit, dengan mengubah Alamat Pengiriman pada table Data Diri</div>
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



