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
			        	<h4 style="padding:1px !important;">RETURN POLICY</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
			            	<ul>
			                	<li>
			                    	<p>If the products you have purchased from us are faulty, wrongly described or different from the picture shown, then we will refund you or provide a replacement product provided that items are returned within a 7 (seven) days of receipt of the order with proof of purchase.</p>
			                    	<p>If you find that the products are not the right size for you, please email us at shopdeca@berrybenka.com within 7 (seven) days of order receipt and we will arrange a different size.</p>
			                    </li>
			                    <li>
			                    	<a>The product return & exchange are subject to these following conditions:</a>
			                        <div>
			                        	<ul>
			                                <li>The item must be returned in its original condition, complete with the price tag, invoice, barcode, and the packaging.</li>
			                                <li>It should not be damaged, unclean, and/or being washed.</li>
			                                <li>We do not receive return of any of these products: jewelleries, fragrance, cosmetics, and lingerie.</li>
			                                <li>Discounted products are non-refundable.</li>
			                                <li>If you've requested an exchange of a product, we’ll do our best to fulfil your request, however please note that this is subject to stock availability.</li>
			                            </ul>	
			                        </div>
			                    </li>
			                    <li>
			                    	<a>Steps to return a product:</a>
			                        <div>
			                        	<ol> 
			                                <li>Contact our Customer Service at shopdeca@berrybenka.com to confirm your return.</li>
			                                <li>Send us the product you want to return along with the exchange and return form attached.</li>
			                                <li>The product must be returned in the period of 7 (seven) days after being received.</li>
			                                <li>Your money will be refunded within 5 working days counting from the day we receive the item.</li>
			                            </ol>
			                        </div>
			                    </li>
<!--                                            <li>
			                    	<a>CARA MENGEMBALIKAN BARANG</a>
			                        <div>
                                                    Siapkan barang yang akan dikembalikan dengan membungkus kembali barang tersebut ke kotak kemasannya<br /><br />	
                                                    Sertakan formulir pengembalian pada saat melakukan pengiriman barang. Apabila tidak, Shopdeca berhak untuk menolak proses pengembalian lebih lanjut (Form pengembalian dapat ditemukan di dalam kotak kemasan paket pengiriman Anda).
			                        </div>
			                    </li>-->
<!--                                            <li>
			                    	<a>PENGISIAN FORMULIR PENGEMBALIAN BARANG ONLINE (ONLINE RETURN FORM)</a>
			                        <div>
                                                  Berikut adalah panduan pengisian online return form:
                                                  <ol style="list-style-type: decimal;">
                                                  <li>Masuk ke menu akun Anda di <a href="http://www.shopdeca.com">www.shopdeca.com</a></li>
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
                                                  <li>Tunggu notifikasi melalui email atau informasi dari tim Customer Service Shopdeca</li>
                                                  </ol>
                                                </div>
			                    </li>-->
<!--                                            <li>
			                    	<a>MENGKREDITKAN UANG DARI BARANG YANG DIKEMBALIKAN DENGAN MENGGUNAKAN BENKA POIN</a>
			                        <div>
                                                    Benka Poin hanya akan diberikan apabila barang yang dikembalikan masih dalam kondisi belum terpakai dan tidak rusak dengan tags masih terpasang.. Benka Poin yang diberikan dapat digunakan untuk pembelian berikutnya di Shopdeca. Benka Poin tidak dapat diuangkan.
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



