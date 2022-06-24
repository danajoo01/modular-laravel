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
			        	<h4 style="padding:1px !important;">SHIPPING AND HANDLING</h4>
			        </div>
			      <div class="full-width mb20">
			        <div class="static-content">
			            <div class="list-q">
                    <p>We ship to you by means of professional courier company with nationwide coverage.</p>
                    <p>We strive to ship your order within 1 – 2 working days after receiving your order. In the event that we are unable to do this, we will be in touch with you via email.</p>
			            	<ul>
                        <!--li>
                          <a>Eid Al-Fitr Shipment </a>
                          <div>
                            In regards to the Ramadan holiday season this year, we will experience slowdown of delivery process closer to Eid al-Fitr as most of logistics partners will pause their operations between 24 June-2 July 2017. As such, to ensure that your order (either regular or COD) gets delivered before the Eid al-Fitr, please follow the order timeline as follow:<br><br> 
- Jabodetabek &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;latest by 21 June 2017 <br>
- Java & Sumatera &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;latest by 19 June 2017<br>
- Outside of Java & Sumatera &nbsp;&nbsp;&nbsp;latest by 15 June 2017<br><br>
For orders done after the aforementioned dates will be delivered after the Eid al-Fitr. All operations will resume normal by 3 July 2017. Meanwhile, all of our offline stores will continue operate normally during the Ramadan holiday. Should you have any question, please reach out to us via shopdeca@berrybenka.com.
                          </div>
                        </li-->
			                	<li>
			                    	<a>Shipping time</a>
			                        <div>
								<div id="wrapshl">
                  <div id="left_col_shl">
                      <em> Area </em>
                  </div>
                  <div id="right_col_shl">
                      <em> Estimated Time </em>
                  </div>
                  <div id="left_col_shl">
                      Jakarta
                  </div>
                  <div id="right_col_shl">
                      1 - 3 Working Days
                  </div>
                  <div id="left_col_shl">
                      Jawa- Bali - Bengkulu - Sumbar
                  </div>
                  <div id="right_col_shl">
                      3 - 6 Working Days
                  </div>
                  <div id="left_col_shl">
                    Bangka Belitung - Gorontalo - Riau - Sumut - Sumsel
                  </div>
                  <div id="right_col_shl">
                      3 - 7 Working Days
                  </div>
                  <div id="left_col_shl">
                      Jambi - Kepulauan Riau
                  </div>
                  <div id="right_col_shl">
                      3 - 8 Working Days
                  </div>
                  <div id="left_col_shl">
                      Aceh - Kalimantan - Lampung - NTB - Sulawesi
                  </div>
                  <div id="right_col_shl">
                      3 - 10 Working Days
                  </div>
                  <div id="left_col_shl">
                      Maluku - NTT - Papua
                  </div>
                  <div id="right_col_shl">
                      3 - 12 Working Days
                  </div>
              </div> <br> Shipping will be placed after payment has been received, except for orders with COD (Cash On Delivery).  Items such as aerosol / oil / water to Non Jabodetabek will take 14 working days to arrive.<br><br> We are not being able to ship on public holidays.					</div>
			                    </li>
			                    <li>
			                    	<a>Shipping Price</a>
                                                <div class="city-choose">
                                                            
                                                  <form method="post" id="account" action="#">
                                                  <p style="margin:5px 0 !important;">Choose City / Province: </p>
                                                  <span>
                                                      <select onChange="requestCity();" name="shipping_area" required id="shipping_area">
                                                          <option value>Province</option>
                                                          @foreach ($list_province as $area)
                                                             <option value="{{$area->shipping_area}}">{{$area->shipping_area}}</option>
                                                          @endforeach
                                                      </select>
                                                      <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                  </span>                                                        
                                                  <span>
                                                          <select required id="shipping_name" name="shipping_name" onChange="requestCityReturnFee();">
                                                          <option value="">City</option>

                                                      </select>
                                                      <i class="fa fa-angle-down" aria-hidden="true"></i>
                                                  </span>
                                                  <span id="load_ship_city" style="display: none;border:none;width:60px;"><i class="fa fa-refresh fa-spin"></i> Loading</span>
                                                  <span id="shipping_fee" style="border:none;width:150px;"></span>
                                                  </form>
                                                    <br />
			                            <P>Free shipping fee with minimum purchased of IDR 500.000</P>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>Shipping Partner</a>
			                        <div>
			                            <ul class="vendorlogo">                        
                                                        <li><a href="http://sap-express.id/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoSAP.png" width="60"></a></li>			                                
                                                        <li><a href="http://www.lionparcel.com/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoLionParcel.png" width="60"></a></li>			                                
                                                        <li><a href="https://www.ninjaxpress.co/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoNinjavan.png" width="60"></a></li>
                                                        <li><a href="https://web.etobee.com/login" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoEtobee.png" width="75"></a></li>
                                                        <li><a href="https://iruna.id/trackorder" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoIruna.png" width="60"></a></li>
                                                        <li><a href="http://islandlogistic.com/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoIslandLog.png" width="60"></a></li>
                                                        <li><a href="https://porter.id/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoPorter.png" width="60"></a></li>
                                                        <li><a href="#" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoMPS EXPRESS.png" width="60"></a></li>
                                                        <li><a href="http://www.jne.co.id/" target="_blank"><img src="/berrybenka/desktop/img/shipping-vendor/logoJNE.png" width="60"></a></li>
			                            </ul>
			                            <p class="vendorwording">We have strictly selected our partner logistics; each logistic will serve different regions. Shopdeca has full authorization on choosing the reliable partnership for shipping the goods to customer. Customer satisfaction is Shopdeca’s primary priority; we ensure the security and accuracy of our shipments.</p>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>CHECK THE STATUS OF YOUR ORDER</a>
			                        <div>
			                        	<ol>
															<li>You can check the status of your order by signing in to Shopdeca.com and click on “View order history” to see your order history and order status. Once your order is shipped, you will find an Airway Bill number (AWB) and a link to our Courier’s website to track the shipment.</li>
															<li>
																<table width="400" border="0" class="list-vendor">
																	<tbody><tr><td width="35%">JNE</td><td width="5%">:</td><td><a href="http://www.jne.co.id/home.php" target="_blank">http://www.jne.co.id/</a></td></tr>																	
																	<tr><td>First Logistic</td><td>:</td><td><a href="http://www.firstlogistics.co.id/" target="_blank">http://www.firstlogistics.co.id/</a></td></tr>																																		
																	<tr><td>J-Express</td><td>:</td><td><a href="http://www.j-express.id" target="_blank">http://www.j-express.id/</a></td></tr>
                                  <tr><td>Ninja Van</td><td>:</td><td><a href="https://www.ninjavan.id/" target="_blank">https://www.ninjavan.id/</a></td></tr>
																</tbody></table>
															</li>
															</ol>
			                        </div>
			                    </li>
			                    <li>
			                    	<a>Change of Address</a>
			                        <div>Customers can contact shopdeca@berrybenka.com to change the shipping address before the 12 hours of payment or confirmation of payment received.</div>
			                    </li>
			                    <li>
			                    	<a>Can a customer send to a different address than the home address?</a>
			                        <div>Yes, you can change the address on Personal Data table at the checkout page.</div>
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



