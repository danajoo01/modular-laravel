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
				<input type="submit" value="LANJUT KE T-CASH" style='background: #ED1B24;color: #fff;padding: 15px 61px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font-size: 18px;font: 18px "futura", sans-serif;'>
				</form>
			</h2>
		@elseif($status == 2)
			<h1>KAMI MOHON MAAF, ORDER ANDA TIDAK DITEMUKAN.</h1>
			<h2>Tidak Terdapat Order Yang Harus Diproses.</h2>
		@elseif($status == 3)
			<h1>TERIMA KASIH TELAH BERBELANJA DI {{$domain_name}}</h1>
            <h2>Nomor Transaksi Anda adalah <strong>#{{$po_number}}</strong>
              <br>
              dan Anda akan menerima email konfirmasi pembelian
            </h2>
		@elseif($status == 4)
			<h1>KAMI MOHON MAAF, TERJADI KESALAHAN PADA SISTEM.</h1>
			<h2>Mohon maaf, silakan menghubungi customer service untuk intruksi lebih lanjut.</h2>
		@endif
    </div>                   
  </div>
</div>
@endsection