<?php 
$user       = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
$total_data = ['grand_total' => $grand_total, 'tax' => $tax, 'shipping' => $shipping_finance];

$marketing_data['purchase_code'] = isset($purchase_code) ? $purchase_code : '';
$marketing_data['grand_total']   = isset($grand_total) ? $grand_total : 0;
$marketing_data['shipping']      = isset($shipping_finance) ? $shipping_finance : 0;
$marketing_data['city']          = isset($city) ? $city : '';
$marketing_data['tax']           = isset($tax) ? $tax : 0;
$marketing_data['province']      = isset($province) ? $province : '';
?>

@section('marketing-tag-header')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.hijabenka.desktop.header-thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'total_data' => $total_data]);
    @endif
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.hijabenka.desktop.body-thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'total_data' => $total_data]);
    @endif
@endsection

@extends('layouts.hijabenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/error.css') }}">
<style>
  ol li {
    padding: 5px;
  }
  ol li a{
    padding: 0 !important;
    text-transform: none !important;
    background: transparent !important;
    color: inherit !important;
    letter-spacing: normal !important;
  }
</style>
@endsection

@section('content')
<div class="error-content thx-wrapper">
  <div class="error-overlay">
    <div class="error-inside">
        @if($payment_method == 4) <!--Veritrans BCA KlikPay-->
          @if($transaction_status == 'settlement')
            <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
            <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
              <br>
              dan Anda akan menerima email konfirmasi pembelian
            </h2>
          @else
            <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
              <br>
              silahkan lakukan pembelian kembali
            </h2>
          @endif
        @elseif($payment_method == 28) <!--BCA Virtual Account-->
          <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
          <h2>Nomor BCA Virtual Account kamu adalah <strong>{{ $va_number }}</strong>
              <br>
              dan kamu akan menerima email konfirmasi pembelian
          </h2>
        @elseif($payment_method == 98) <!--Permata Virtual Account-->
          <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
          <h2>Nomor Permata Virtual Account kamu adalah <strong>{{ $va_number }}</strong>
              <br>
              dan kamu akan menerima email konfirmasi pembelian
          </h2>          
        @elseif($payment_method == 99) <!-- Kredivo -->
            @if(isset($kredivo_redirect))
                <h1>PESANAN SUDAH DI TERIMA</h1>
                <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                    <br>
                    Silahkan klik tombol berikut untuk melanjutkan ke pembayaran kredivo
                    <br /><br />
                    <a style="background:#47BBE4;color:#fff;" href="{{ $kredivo_redirect }}">LANJUT KE KREDIVO.COM</a>
                </h2>
            @else
                @if($transaction_status == 'settlement')
                    <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
                    <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                      <br>
                      dan Anda akan menerima email konfirmasi pembelian
                    </h2>
                @else
                    <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                      <br>
                      silahkan lakukan pembelian kembali
                    </h2>
                @endif
            @endif
        @elseif($payment_method == 135) <!-- T-Cash -->
            @if($tcash_signature != '')
              <h1>PESANAN SUDAH DI TERIMA</h1>
              <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
                  <br>
                  Silahkan klik tombol berikut untuk melanjutkan ke pembayaran T-Cash
                  <br /><br />
                  <form method="POST" action="{{$tcash_webcheckout}}">
                    <input type="text" name="message" value="{{$tcash_signature}}" hidden>
                    <input type="submit" value="LANJUT KE T-CASH" style='background: #ED1B24;color: #fff;padding: 15px 61px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font-size: 18px;font: 18px "futura", sans-serif;'>
                  </form>
              </h2>
            @endif                     
        @else
          <h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
          <h2>Nomor Transaksi Anda adalah <strong>#{{$purchase_code}}</strong>
              <br>
              dan Anda akan menerima email konfirmasi pembelian
          </h2>
        @endif
      
      @if($payment_method == 1) <!--Transfer BCA-->
        <h2 class="payment-info">Mohon lakukan pembayaran Anda ke<br>Bank BCA<br>a/n PT. BERRYBENKA<br>No Rekening : 546 032 7077</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
        <p>Konfirmasi pembayaran anda di sini ketika pembayaran sudah dilakukan</p>
        <a href="/user/order_history_detail/{{$purchase_code}}">akun saya</a>
        
      @elseif($payment_method == 2) <!--Transfer Mandiri-->
        <h2 class="payment-info">Mohon lakukan pembayaran Anda ke<br>Bank Mandiri<br>a/n PT. BERRYBENKA<br>No Rekening : 165 000 042 7964</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
        <p>Konfirmasi pembayaran anda di sini ketika pembayaran sudah dilakukan</p>
        <a href="/user/order_history_detail/{{$purchase_code}}">akun saya</a>
        
      @elseif($payment_method == 3) <!--KlikBCA-->
        <h2 class="payment-info">Pembayaran melalui KlikBCA harus dilakukan paling lambat <b>2 jam</b> setelah Anda melakukan order.</h2>
        <p>Berikut ini mekanisme pembayaran melalui KlikBCA:</p>
        <ol>
          <li>Login ke <a href="{{$redirect_url}}" target="_blank">KlikBCA</a>.</li>
          <li>Pilih Pembayaran E-Commerce.</li>
          <li>Pilih Kategori: Baju/Aksesoris.</li>
          <li>Pilih Nama Perusahaan: BERRYBENKA, kemudian  klik Lanjut.</li>
          <li>Pilih transaksi yang ingin dibayarkan, klik Lanjutkan.</li>
          <li>Pembayaran akan langsung diproses saat itu juga dan item di order Anda akan dikirim secepatnya.</li>
        </ol>
        <p>KlikBCA Clause:</p>
        <ol>
          <li>The keyID in KlikBCA's User ID is an active registered KlikBCA's User ID.</li>
          <li>Please make a payment through KlikBCA (<a href="{{$redirect_url}}" target="_blank">KlikBCA</a>) by using the same KlikBCA's User ID.</li>
          <li>The payment must be made within <b>2 hours</b> after the order.</li>
          <li>The transaction will be cancelled (expired) if you do not make a payment within the determined period.</li>
        </ol>
        <p>Klausul KlikBCA:</p>
        <ol>
          <li>User ID KlikBCA yang Anda masukkan adalah User ID KlikBCA yang terdaftar dan aktif.</li>
          <li>Harap lakukan pembayaran melalui KlikBCA (<a href="{{$redirect_url}}" target="_blank">KlikBCA</a>) dengan menggunakan User ID KlikBCA yang sama.</li>
          <li>Lakukan pembayaran paling lambat <b>2 jam</b> setelah order Anda.</li>
          <li>Transaksi Anda akan dibatalkan (kadaluarsa) jika Anda tidak melakukan pembayaran dalam batas waktu yang ditentukan.</li>
        </ol>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method == 29) <!--Transfer BNI-->
        <h2 class="payment-info">Mohon lakukan pembayaran Anda ke<br>Bank BNI<br>a/n PT. BERRYBENKA<br>No Rekening : 290 222 0008</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
        <p>Konfirmasi pembayaran anda di sini ketika pembayaran sudah dilakukan</p>
        <a href="/user/order_history_detail/{{$purchase_code}}">akun saya</a>
        
      @elseif($payment_method == 30) <!--Transfer BRI-->
        <h2 class="payment-info">Mohon lakukan pembayaran Anda ke<br>Bank BRI<br>a/n PT. BERRYBENKA<br>No Rekening : 0505 01 000 151302</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
        <p>Konfirmasi pembayaran anda di sini ketika pembayaran sudah dilakukan</p>
        <a href="/user/order_history_detail/{{$purchase_code}}">akun saya</a>
        
      @elseif($payment_method == 5) <!--Veritrans-->
        <h2 class="payment-info">Pembayaran menggunakan Visa / Mastercard sukses dilakukan.</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method == 4) <!--Veritrans BCA KlikPay-->
        @if($transaction_status == 'settlement')
        <h2 class="payment-info">Pembayaran menggunakan BCA KlikPay sukses dilakukan.</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
        @else
          <h2 class="payment-info">Pembayaran menggunakan BCA KlikPay gagal dilakukan.</h2>
        @endif
      @elseif($payment_method == 99) <!--KREDIVO-->
        @if(!isset($kredivo_redirect))
            @if($transaction_status == 'settlement')
                <h2 class="payment-info">Pembayaran menggunakan KREDIVO sukses dilakukan.</h2>
                <!-- start info benka stamp -->
                {!! $get_stamp_info !!}
                <!-- end info benka stamp -->
            @else
              <h2 class="payment-info">Pembayaran menggunakan KREDIVO gagal dilakukan.</h2>
            @endif
        @endif                 
      @elseif($payment_method == 20) <!--Mandiri Debit-->
        <h2 class="payment-info">Pembayaran menggunakan Mandiri Debit sukses dilakukan.</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method == 24) <!--Indomaret-->
        <h2 class="payment-info">Harap melakukan pembayaran maksimal 2x24 jam di Indomaret terdekat, <br/> jika tidak maka transaksi akan dibatalkan. <br/> Kode pembayaran anda : {{$payment_code}}</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method == 28) <!--BCA Virtual Account-->
        <h2 class="payment-info">Pembayaran melalui BCA Virtual Account harus dilakukan paling lambat <b>2 x 24 jam</b> setelah kamu melakukan order.</h2>
        <p>Cara pembayaran menggunakan ATM BCA: </p>
        <ol>
            <li>Pilih <strong>Transaksi Lainnya</strong> > <strong>Transfer</strong> > <strong>Ke Rek BCA Virtual Account</strong></li>
            <li>Masukkan nomor BCA Virtual Account kamu <strong>{{$va_number}}</strong> dan pilih <strong>Benar</strong></li>
            <li>Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian pilih <strong>Ya</strong></li>
        </ol>
        <p>Cara pembayaran menggunakan KlikBCA:</p>
        <ol>
            <li>Pilih <strong>Transfer Dana</strong> > <strong>Transfer ke BCA Virtual Account</strong></li>
            <li>Centang No. Virtual Account lalu masukkan nomor BCA Virtual Account kamu <strong>{{$va_number}}</strong> dan klik <strong>Lanjutkan</strong></li>
            <li>Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>Lanjutkan</strong></li>    
            <li>Ambil BCA Token kamu dan masukkan respons KeyBCA Appli 1, kemudian klik <strong>Kirim</strong></li>
        </ol>
        <p>Cara pembayaran menggunakan m-BCA</p>
        <ol>            
            <li>Pilih <strong>m-Transfer</strong> > <strong>BCA Virtual Account</strong></li>         
            <li>Masukkan nomor BCA Virtual Account kamu <strong>{{$va_number}}</strong> dan klik <strong>OK</strong> > <strong>Send</strong></li>
            <li>Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>OK</strong></li>
            <li>Masukkan PIN m-BCA kamu dan klik <strong>OK</strong></li>
        </ol>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method == 98) <!--Permata Virtual Account-->
        <h2 class="payment-info">Pembayaran melalui Permata Virtual Account harus dilakukan paling lambat <b>2 x 24 jam</b> setelah kamu melakukan order.</h2> 
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @elseif($payment_method >= 6 && $payment_method <= 11)
        <h2 class="payment-info">Cicilan pembayaran menggunakan BCA KlikPay sukses dilakukan.</h2>
        <!-- start info benka stamp -->
        {!! $get_stamp_info !!}
        <!-- end info benka stamp -->
      @endif
      
      <!-- thank you banner -->  
      @if($thankyou_banner)
        <div class="sixteen columns text-center">
        @foreach ($thankyou_banner as $bannerty)
        <a href="{{ $bannerty->ty_page_url }}" target="_blank" style="background:none;padding:0;">
            <img style="padding:20px;margin-top:0;" src="{{ IMAGE_SPECIAL_PAGE_UPLOAD_PATH }}ty_page/{{ $bannerty->ty_page_img_web }}" />
          </a>
        @endforeach
        </div>
      @endif
      <!-- end thank you banner --> 
    </div>                   
  </div>
</div>

<!-- BEGIN GCR Opt-in Module Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderOptIn"
  async defer>
</script>

<script>
  window.renderOptIn = function() { 
    window.gapi.load('surveyoptin', function() {
      window.gapi.surveyoptin.render(
        {
          // REQUIRED
          "merchant_id":"{{ GetMerchantIdGcr() }}",
          "order_id": "{{ $purchase_code }}",
          "email": @if(!empty($user->customer_email)) "{{ $user->customer_email }}" @else "" @endif,
          "delivery_country": "ID",
          "estimated_delivery_date": "{{ $purchase_date }}",

          // OPTIONAL
          "opt_in_style": "BOTTOM_LEFT_DIALOG"
        }); 
     });
  }
</script>
<!-- END GCR Opt-in Module Code -->
<!-- BEGIN GCR Language Code -->
<script>
  window.___gcfg = {
    lang: 'id'
  };
</script>
<!-- END GCR Language Code -->

@endsection

@section('js')
  <script>
    $(document).ready(function(){
      var payment_method = '{{(isset($payment_method)) ? $payment_method : ''}}';
      var redirect_url = '{{(isset($redirect_url)) ? $redirect_url : ''}}';
      
      if(payment_method == '3' && redirect_url != ''){
        window.open(redirect_url, "KlikBCA", "width=800,height=600");
      }
    });
  </script>
@endsection

@section('marketing-tag')
<script type="text/javascript">

var finalorder336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    purchase_code : "{{ $purchase_code }}",
    grand_total : "{{ $marketing_data['grand_total'] }}",
    item : {!! json_encode($tag_products) !!}
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.thankyou-page', ['marketing_data' => $marketing_data, 'carts' => $fetch_cart, 'tag_products' => $tag_products,])
@endif

@endsection
