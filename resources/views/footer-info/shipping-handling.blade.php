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
			        	<h4 style="padding:1px !important;">KETENTUAN PENGIRIMAN</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<a>WAKTU PENGIRIMAN</a>
			                        <div>
								Jakarta &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;1-3 hari kerja<br>Luar Jakarta &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;2-6 hari kerja. <br> Waktu pengiriman ini berlaku setelah pembayaran kami terima, kecuali untuk pemesanan yang menggunakan COD. Untuk barang yang berupa aerosol/minyak/air diluar jakarta akan memakan waktu 14 hari kerja untuk sampai ke tujuan. <br><br> Kami tidak melakukan pengiriman pada hari libur nasional.					</div>
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
			                            <P> Setiap transaksi di Hijabenka bebas ongkos kirim dengan minimum pembelian Rp. 200.000 </P>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>PARTNER LOGISTIK PENGIRIMAN</a>
			                        <div>
			                            <ul class="vendorlogo">                        
			                                <li><a href="http://www.jne.co.id/home.php"><img src="https://berrybenka.com/assets/images/LogoJNE.png"></a></li>
			                                <li><a href="http://rpx.co.id/"><img src="https://berrybenka.com/assets/images/LogoRPX.png"></a></li>
			                                <li><a href="http://www.firstlogistics.co.id/"><img src="https://berrybenka.com/assets/images/LogoFirstLogistics.png"></a></li>
			                                <li><a href="https://tracking.acommerce.asia/"><img src="https://berrybenka.com/assets/images/LogoAcommerce.png"></a></li>
			                                <li><a href="https://www.ninjavan.id/#/"><img src="https://berrybenka.com/assets/images/LogoNinjaXpress.png"></a></li>
			                                <li><a href="http://www.etobee.com/"><img src="https://berrybenka.com/assets/images/LogoEtobee.png"></a></li>
			                                <li><a href="http://www.j-express.id/"><img src="https://berrybenka.com/assets/images/JX.jpg"></a></li>
			                            </ul>
			                            <p class="vendorwording"> Partner-partner logistik ini telah kami pilih secara ketat dan masing-masing partner logistik akan melayani pengiriman untuk wilayah yang berbeda-beda. Hijabenka memiliki kuasa penuh untuk memilih partner logistik yang dianggap paling handal untuk mengirimakan setiap barang yang dipesan dari customer. Hijabenka selalu memegang teguh kepuasan pelanggan, termasuk memastikan keamanan dan ketepatan waktu pengiriman barang. </p>
			                            <p class="vendorwording">Selain partner-partner logistik tersebut, kami juga memiliki armada pengiriman logistik bernama Hijabenka Express yang akan mengirimkan paket-paket di wilayah Jakarta. </p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>CEK STATUS PENGIRIMAN</a>
			                        <div>
			                        	<ol>
															<li>Hijabenka akan mengirimkan informasi pengiriman melalui email beserta tautan untuk cek status pesanan dan nomor resi kepada pelangan. </li>
															<li>Pelanggan dapat cek ke website masing - masing jasa pengiriman :
																<table width="400" border="0" class="list-vendor">
																	<tbody><tr><td width="35%">JNE</td><td width="5%">:</td><td><a href="http://www.jne.co.id/home.php" target="_blank">http://www.jne.co.id/</a></td></tr>
																	<tr><td>RPX</td><td>:</td><td><a href="http://rpx.co.id/" target="_blank">http://rpx.co.id/</a></td></tr>
																	<tr><td>First Logistic</td><td>:</td><td><a href="http://www.firstlogistics.co.id/" target="_blank">http://www.firstlogistics.co.id/</a></td></tr>
																	<tr><td>acommerce</td><td>:</td><td><a href="https://tracking.acommerce.asia/" target="_blank">https://tracking.acommerce.asia/</a></td></tr>
																	<tr><td>Ninja Van</td><td>:</td><td><a href="https://www.ninjavan.id/" target="_blank">https://www.ninjavan.id/</a></td></tr>
																	<tr><td>Etobee</td><td>:</td><td><a href="http://www.etobee.com/" target="_blank">http://www.etobee.com/</a></td></tr>
																	<tr><td>J-Express</td><td>:</td><td><a href="http://www.j-express.id" target="_blank">http://www.j-express.id/</a></td></tr>
																	<tr><td>Hijabenka Express</td><td>:</td><td><a href="https://hijabenka.com/user/order_history" target="_blank">https://hijabenka.com/</a></td></tr>
																</tbody></table>
															</li>
															<li>Untuk Hijabenka Express, customer dapat mengecek status pengiriman melalui menu "Daftar Konfirmasi" di dalam akun dashboard customer atau bertanya langsung ke CS Hijabenka.</li>
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



