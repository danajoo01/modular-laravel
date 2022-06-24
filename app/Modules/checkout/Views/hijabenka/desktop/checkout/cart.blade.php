@extends('layouts.hijabenka.desktop.main')

@section('css')
  <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/cart.css') }}">
  <style>
    #berrybenka{
      padding-top:90px; 
    }
  </style>
@endsection

@section('content')

<div class="content">
  
  <input id="img_path" type="hidden" value="{{IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH}}" />
  <input id="ajax_url" type="hidden" value="{{ url('/') }}" />
  
  <div id="berrybenka" class="berrybenka">
    <div class="wrapper">
      <div class="cart-help">
        <ul class="need-help">
          <li>
            <span class="icon"> <i class="fa fa-info-circle"></i></span> 
            Butuh Bantuan? Hubungi Customer Service Kami<br><strong>Jam Kerja, Senin - Jumat (9.00 - 18.00)<br>Sabtu - Minggu (8.00 - 17.00)</strong>					
          </li>
          <?php /*
          <li>
            <span class="icon"> <i class="fa fa-phone"></i>
            </span> Kontak Kami <br> <strong>021 2520555</strong>
          </li>
          */?>
          <li>
            <span class="icon"> <i class="fa fa-envelope"></i>
            </span> Email Kami <br> <strong><a href="mailto:cs@berrybenka.com">cs@berrybenka.com</a></strong>
          </li>
          <li>
            <span class="icon"> <i class="fa fa-comments"></i>
            </span> SMS Kami <br> <strong>0812 8880 9992</strong>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <div class="cart-list-wrapper123">
    <div class="wrapper">
      <div class="cart-list-wrapper-left">
        <!--span class="error-msg-login stock-alert" style="background:#d9edf7;border-color:#bce8f1;"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>Informasi pengiriman pesanan selama Lebaran 2017 dapat mengacu ke ketentuan berikut: <a href="{{ url('/') }}/home/shipping_handling" style="color:#710f0c;text-decoration:underline;">shipping & handling</a></span-->
        <div id="error-msg-container" style="display: none;"><!--LIST INVENTORY ERROR--></div>
        <div class="cart-list">
          <table width="100%" border="0">
            <tbody id="cart-container">
              <!--LIST CART GENERATED HERE-->
            </tbody>
          </table>
        </div>
        <div class="grand-total-wrapper">
          <div class="grand-total-wording"><h1>TOTAL</h1><p> *Diluar Biaya Pengiriman </p></div>
          <div id="grandtotal-value" class="grand-total">IDR 0,-</div>
          <input type="hidden" id="raw-grandtotal-value" value="0" />
        </div>
        <div class="goto-checkout clearfix">
          <a href="/new-arrival/"><i class="fa fa-chevron-left"></i> Kembali Berbelanja</a>
          <a id="btn-checkout" href="#">Proses Pembayaran <i class="fa fa-chevron-right"></i></a>
        </div>
        <div class="bank-promo">
          <h1>PROMOSI</h1>
          <ul>
            @foreach($bank_promo as $promo)
              @if($promo['highlight'])
                <li class="highlight-bank-promo">
              @else
                <li>
              @endif
                  <h3>{{ $promo['promo_value'] }}</h3>
                  <h2>{{ $promo['bank_name'] }}</h2>
                  <h5>{{ $promo['min_purchase'] }}</h5>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
  <script type="text/javascript">
    var domain = <?php echo get_domain()['domain_id']; ?>
  </script>
  <script src="{{ asset('js/desktop/cart_bb.js?t=').date('YmdHis') }}"></script>

  <!-- Define variable for criteo -->
  <script type="text/javascript">
  var tag_val336CC993E54E;
  </script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php 
$user = \Auth::user(); 
$product_ids = [];

$product_json = $marketing_data;
?>
var cart336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    cart           : {!! $marketing_data !!}
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.cart-page', ['cart_data' => $product_json])
@endif

@endsection