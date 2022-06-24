@extends('layouts.berrybenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/account.css') }}">
<style>
  .sub-btn{
    padding: 15px 0 !important;
  }
  
  /*.thx-wrapper a {
    background: transparent !important;
    color: #000 !important;
    padding: 0 !important;
    margin: 0 !important;
  }*/
</style>
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
    	@if($status == 0)
			<h1>KAMI MOHON MAAF, ORDER ANDA TIDAK DITEMUKAN.</h1>
			<h2>Nomer Transaksi Anda Tidak Ditemukan.</h2>
    	@elseif($status == 1)
			<h1>LANJUTKAN PEMBAYARAN</h1>
			<h2>Nomor Transaksi Anda adalah <strong>#{{$po_number}}</strong>
				<br>
				Silahkan klik tombol berikut untuk melanjutkan ke pembayaran T-Cash
				<br /><br />
				<form method="POST" action="{{$tcash_webcheckout}}">
                  <input type="text" name="message" value="{{$tcash_signature}}" hidden>
                  <input type="submit" value="LANJUT KE T-CASH" style='background: #ED1B24;color: #fff;padding: 10px 20px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font: 16px "futura", sans-serif;'>
                </form>
			</h2>
    	@elseif($status == 2)
			<h1>KAMI MOHON MAAF, ORDER ANDA TIDAK DITEMUKAN.</h1>
			<h2>Tidak Terdapat Order Yang Harus Diproses.</h2>
    	@elseif($status == 3)
			<h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
            <h2>Nomor transaksi Anda adalah <strong>#{{$po_number}}</strong> dan Anda akan menerima email konfirmasi pembelian.</h2>
    	@elseif($status == 4)
			<h1>KAMI MOHON MAAF, TERJADI KESALAHAN PADA SISTEM.</h1>
			<h2>Mohon maaf, silakan menghubungi customer service untuk intruksi lebih lanjut.</h2>
    	@endif
    </div>
  </div>
</div>
@endsection