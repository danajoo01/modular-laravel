@extends('layouts.berrybenka.desktop.main')

@section('css')
  <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/cart.css') }}">
@endsection

@section('content')

<div class="cart">
  <div class="wrapper">

    <input id="img_path" type="hidden" value="{{IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH}}" />
    <input id="ajax_url" type="hidden" value="{{ url('/') }}" />

    <div class="cart-wrapper">
      <div class="help-bar">
        <ul>
          <li>
            <i class="fa fa-info" aria-hidden="true"></i>
            <span>
              <h1>Butuh Bantuan? Hubungi Customer Service Kami</h1>
              <p>Jam Kerja, Senin - Jumat (9.00 - 18.00)</p>
              <p>Sabtu - Minggu (8.00 - 17.00)</p>
            </span>
          </li>
          <li>
            <i class="fa fa-envelope"></i>
            <span>
              <h1>Email Kami</h1>
              <p><a href="mailto:cs@berrybenka.com">cs@berrybenka.com</a></p>
              <p>Sabtu - Minggu (8.00 - 17.00)</p>
            </span>
          </li>
          <li>
            <i class="fa fa-comments"></i>
            <span>
              <h1>SMS Kami</h1>
              <p>0812 8880 9992</p>
            </span>
          </li>
        </ul>
      </div>
    
      <div class="cart-table">
        <div class="cart-table-header">
          <ul>
            <li>produk</li>
            <li>harga</li>
            <li>jumlah</li>
            <li>total</li>
            <li>&nbsp;</li>
          </ul>
        </div>
        <div class="cart-table-content">
          <ul id="cart-container">
          </ul>

          <div class="total-cart">
            <ul>
              <li>
                <h1>TOTAL</h1>
                <h2>*Diluar Biaya Pengiriman</h2>
              </li>
              <li id="grandtotal-value">IDR 0,-</li>
              <input type="hidden" id="raw-grandtotal-value" value="0" />
            </ul>
          </div>
          <div class="cart-proceed">
            <a href="/new-arrival/">Kembali Berbelanja</a>
            <a  id="btn-checkout" href="#">proses pembayaran</a>
          </div>
        </div>
        <div class="bank-promo">
          <h1>promo bank</h1>
          <ul>
            @foreach($bank_promo as $promo)
              @if($promo['highlight'])
                <li>
              @else
                <li>
              @endif
                <span>
                  <h2>IDR 100.000</h2>
                  <h1>uob</h1>
                  <h2>min purchase 500k</h2>
                </span>
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
  <script src="{{ asset('js/desktop/cart.js?t=').date('YmdHis') }}"></script>

  <!-- Define variable for criteo -->
  <script type="text/javascript">
  var tag_val336CC993E54E;
  </script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php $user = \Auth::user(); ?>
var cart336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    cart           : {!! $marketing_data !!}
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.berrybenka.desktop.cart-page')
@endif

@endsection