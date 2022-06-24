<?php
$user       = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
$total_data = ['purchase_code' => $purchase_code,'grand_total' => $grand_total, 'tax' => $tax, 'shipping' => $shipping_finance];

$marketing_data['purchase_code']    = isset($purchase_code) ? $purchase_code : '';
$marketing_data['grand_total']      = isset($grand_total) ? $grand_total : 0;
$marketing_data['shipping']         = isset($shipping_finance) ? $shipping_finance : 0;
$marketing_data['city']             = isset($city) ? $city : '';
$marketing_data['tax']              = isset($tax) ? $tax : 0;
$marketing_data['province']         = isset($province) ? $province : '';
?>

@extends('layouts.shopdeca.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/account.css') }}">
<style>
  .sub-btn{
    padding: 15px 0 !important;
  }
  
  .thx-wrapper a {
    background: transparent !important;
    color: #000 !important;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  .thx-wrapper a.btn-kredivo {
    display: block;
    background: #47BBE4 !important;
    color: #fff !important;
    text-align: center;
    padding: 15px 0 !important;
    letter-spacing: 1px;
    font-family: 'gotham',arial;
    text-transform: uppercase;
    margin: 30px 0 !important;
  }
</style>
@endsection

@section('marketing-tag-header')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.shopdeca.mobile.header-thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'total_data' => $total_data]);
    @endif
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.shopdeca.mobile.body-thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'total_data' => $total_data]);
    @endif
@endsection

@section('filter')
<div class="content-detail">
  <div class="account-wrapper">
    <div class="account-header">
        @if(Auth::user() != null)  
            <h1>{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</h1>
        @endif
<!--      <a href="#">Point Anda : 0 </a>-->
    </div>
    <div class="thx-wrapper">
      @if($payment_method == 4) <!--Veritrans BCA KlikPay-->
        @if($transaction_status == 'settlement')
          <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
          <h2>Nomor transaksi Anda adalah <strong>#{{$purchase_code}}</strong> dan Anda akan menerima email konfirmasi pembelian.</h2>
        @else
          <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
            <br>
            silahkan lakukan pembelian kembali
          </h2>
        @endif
      @elseif($payment_method == 98)
            <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
          <h2>Nomor Permata Virtual Account kamu adalah <strong>{{ $va_number }}</strong>
              <br>
              dan kamu akan menerima email konfirmasi pembelian
          </h2> 
      @elseif($payment_method == 99) <!--Kredivo-->
            @if(isset($kredivo_redirect))
                <h1>PESANAN SUDAH DI TERIMA</h1>
                <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                    <br>
                    Silahkan klik tombol berikut untuk melanjutkan ke pembayaran kredivo
                    <br /><br />
                    <a class="btn-kredivo" href="{{ $kredivo_redirect }}">LANJUT KE KREDIVO.COM</a>
                </h2>
            @else
                @if($transaction_status == 'settlement')
                    <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
                    <h2>Nomor transaksi Anda adalah <strong>#{{$purchase_code}}</strong> dan Anda akan menerima email konfirmasi pembelian.</h2>
                @else
                    <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                      <br>
                      silahkan lakukan pembelian kembali
                    </h2>
                @endif 
            @endif                  
      @else
        <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
        <h2>Nomor transaksi Anda adalah <strong>#{{$purchase_code}}</strong> dan Anda akan menerima email konfirmasi pembelian.</h2>
      @endif
      
      @if($payment_method == 1) <!--Transfer BCA-->
        <h3>Mohon lakukan pembayaran anda ke :<br>Bank BCA<br>a/n PT. BERRYBENKA<br>No Rekening : <strong>546 032 7077</strong></h3>
        <h4>* Pertanyaan atau  Masalah? <strong>Hubungi ( 021 ) 290 22 136 137</strong></h4>
        <a href="/user/order_history_detail/{{$purchase_code}}">konfirmasi pembayaran</a>
        
      @elseif($payment_method == 2) <!--Transfer Mandiri-->
        <h3>Mohon lakukan pembayaran anda ke :<br>Bank Mandiri<br>a/n PT. BERRYBENKA<br>No Rekening : <strong>165 000 042 7964</strong></h3>
        <h4>* Pertanyaan atau  Masalah? <strong>Hubungi ( 021 ) 290 22 136 137</strong></h4>
        <a href="/user/order_history_detail/{{$purchase_code}}">konfirmasi pembayaran</a>
        
      @elseif($payment_method == 3) <!--KlikBCA-->
        <h2>Pembayaran melalui KlikBCA harus dilakukan paling lambat <b>2 jam</b> setelah Anda melakukan order.</h2>
        <h3>
          Berikut ini mekanisme pembayaran melalui KlikBCA: <br/>
          Login ke <a href="{{$redirect_url}}" target="_blank">KlikBCA</a><br/>
          Pilih Pembayaran E-Commerce.<br/>
          Pilih Kategori: Baju/Aksesoris.<br/>
          Pilih Nama Perusahaan: BERRYBENKA, kemudian  klik Lanjut.<br/>
          Pilih transaksi yang ingin dibayarkan, klik Lanjutkan.<br/>
          Pembayaran akan langsung diproses saat itu juga dan item di order Anda akan dikirim secepatnya.<br/>
        </h3>
        <h3>
          KlikBCA Clause: <br/>
          The keyID in KlikBCA's User ID is an active registered KlikBCA's User ID.<br/>
          Please make a payment through KlikBCA (<a href="{{$redirect_url}}" target="_blank">KlikBCA</a>) by using the same KlikBCA's User ID.<br/>
          The payment must be made within <b>2 hours</b> after the order.<br/>
          The transaction will be cancelled (expired) if you do not make a payment within the determined period.<br/>
        </h3>
        <h3>
          Klausul KlikBCA: <br/>
          User ID KlikBCA yang Anda masukkan adalah User ID KlikBCA yang terdaftar dan aktif.<br/>
          Harap lakukan pembayaran melalui KlikBCA (<a href="{{$redirect_url}}" target="_blank">KlikBCA</a>) dengan menggunakan User ID KlikBCA yang sama.<br/>
          Lakukan pembayaran paling lambat <b>2 jam</b> setelah order Anda.<br/>
          Transaksi Anda akan dibatalkan (kadaluarsa) jika Anda tidak melakukan pembayaran dalam batas waktu yang ditentukan.<br/>
        </h3>
        <h4>* Pertanyaan atau  Masalah? <strong>Hubungi ( 021 ) 290 22 136 137</strong></h4>
        
      @elseif($payment_method == 29) <!--Transfer BNI-->
        <h3>Mohon lakukan pembayaran anda ke :<br>Bank BNI<br>a/n PT. BERRYBENKA<br>No Rekening : <strong>290 222 0008</strong></h3>
        <h4>* Pertanyaan atau  Masalah? <strong>Hubungi ( 021 ) 290 22 136 137</strong></h4>
        <a href="/user/order_history_detail/{{$purchase_code}}">konfirmasi pembayaran</a>
        
      @elseif($payment_method == 30) <!--Transfer BRI-->
        <h3>Mohon lakukan pembayaran anda ke :<br>Bank BRI<br>a/n PT. BERRYBENKA<br>No Rekening : <strong>0505 01 000 151302</strong></h3>
        <h4>* Pertanyaan atau  Masalah? <strong>Hubungi ( 021 ) 290 22 136 137</strong></h4>
        <a href="/user/order_history_detail/{{$purchase_code}}">konfirmasi pembayaran</a>
        
      @elseif($payment_method == 5) <!--Veritrans-->
        <h3>Pembayaran menggunakan Visa / Mastercard sukses dilakukan.</h3>
      
      @elseif($payment_method == 4) <!--Veritrans BCA KlikPay-->
        @if($transaction_status == 'settlement')
          <h3>Pembayaran menggunakan BCA KlikPay sukses dilakukan.</h3>
        @else
          <h3>Pembayaran menggunakan BCA KlikPay gagal dilakukan.</h3>
        @endif
        
      @elseif($payment_method == 20) <!--Mandiri Debit-->
        <h3>Pembayaran menggunakan Mandiri Debit sukses dilakukan.</h3>
                
      @elseif($payment_method == 98) <!--Permata Virtual Account-->
        <h2 class="payment-info">Pembayaran melalui Permata Virtual Account harus dilakukan paling lambat <b>2 x 24 jam</b> setelah kamu melakukan order.</h2>     
        
      @elseif($payment_method == 24) <!--Indomaret-->
        <h3>Harap melakukan pembayaran maksimal 2x24 jam di Indomaret terdekat, <br/> jika tidak maka transaksi akan dibatalkan. <br/> Kode pembayaran anda : {{$payment_code}}</h3>
      @elseif($payment_method == 99) <!--Kredivo-->
        @if(!isset($kredivo_redirect))
            @if($transaction_status == 'settlement')
              <h3>Pembayaran menggunakan KREDIVO sukses dilakukan.</h3>
            @else
              <h3>Pembayaran menggunakan KREDIVO gagal dilakukan.</h3>
            @endif       
        @endif        
      @elseif($payment_method >= 6 && $payment_method <= 11)
        <h3>Cicilan pembayaran menggunakan Visa / Mastercard sukses dilakukan.</h3>
      @endif
      <!-- thank you banner -->  
      @if($thankyou_banner)
        <div class="sixteen columns text-center" style="text-align:center;">
        @foreach ($thankyou_banner as $bannerty)
        <a href="{{ $bannerty->ty_page_url }}" target="_blank" style="background:none;padding:0;display:inline-block;width:auto;">
            <img style="padding:20px;margin-top:0;width:auto;" src="{{ IMAGE_SPECIAL_PAGE_UPLOAD_PATH }}ty_page/{{ $bannerty->ty_page_img_mob }}" />
          </a>
        @endforeach
        </div>
      @endif
      <!-- end thank you banner -->      
    </div>
  </div>
</div>
@endsection

@section('js')    
@endsection

@section('marketing-tag')
<script type="text/javascript">  
  var finalorder336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    purchase_code : "{{ $purchase_code }}",
    item : {!! json_encode($tag_products) !!}
  }
</script>

@if(getMarketingEnv() == true)
  @include('marketing-tag.shopdeca.mobile.thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'total_data' => $total_data]);
@endif

@endsection
