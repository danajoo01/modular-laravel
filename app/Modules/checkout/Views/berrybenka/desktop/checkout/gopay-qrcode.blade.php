@extends('layouts.berrybenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/error.css') }}">

@if($status == 1)
<link rel="stylesheet" media="all" href="//d2f3dnusg0rbp7.cloudfront.net/snap/assets/v3/gray-be8fa47564d0cdf0b5525e16139e8b8e4b1b052071a1b0273c6345dad8ab06a5.css" crossorigin="anonymous" integrity="sha256-vo+kdWTQzfC1Ul4WE56LjksbBSBxobAnPGNF2tirBqU=">
<style>
   header{
      display:flex;
   }
   .text-initial{
      text-align: initial;
   }
</style>
@endif

@endsection

@section('content')
<div class="error-content thx-wrapper">
  <div class="error-overlay">
    <div class="error-inside" style="top:-50px;">
      @if($status == 0)
        <h1>KAMI MOHON MAAF, ORDER ANDA TIDAK DITEMUKAN.</h1>
        <h2>Nomer Transaksi Anda Tidak Ditemukan.</h2>
      @elseif($status == 2)
        <h1>TERIMA KASIH ORDER ANDA TELAH KAMI PROSES</h1>
        <h2>Nomor Transaksi Anda adalah <strong>#{{$po_number}}</strong>
      @elseif($status == 1)
      <div class="card-container" style="float:initial;">
         <div class="notice primary">
            <div class="content"><span>Buka <strong>Aplikasi GO-JEK</strong> di ponsel Anda lalu scan kode QR di bawah</span></div>
         </div>
         <div class="main">
            <div class="main-content">
               <div class="text-center"><img class="qr" src="{{$qr_url}}"></div>
            </div>
         </div>
         <div class="notice warning text-center">
            <div class="content">
               <span>Harap selesaikan pembayaran sebelum 2x24 jam</span>
            </div>
         </div>
      </div>
      <div class="card-container" style="float:initial;">
         <div class="twelve columns">
            <div class="card-title">
               <div class="content text-initial"><span>Cara Pembayaran</span><span class="pull-right bank-sprite gopay"></span></div>
            </div>
            <table class="table">
               <tbody>
                  <tr>
                     <td class="table-row table-numeric text-body">1.</td>
                     <td class="table-row text-body text-initial"><span>Buka aplikasi <strong>GO-JEK</strong> di HP Anda.</span></td>
                  </tr>
                  <tr>
                     <td class="table-row table-numeric text-body">2.</td>
                     <td class="table-row text-body text-initial">
                        <span>Klik <strong>Bayar</strong>.</span>
                     </td>
                  </tr>
                  <tr>
                     <td class="table-row table-numeric text-body">3.</td>
                     <td class="table-row text-body text-initial">
                        <span>Arahkan kamera Anda ke <strong>Kode QR</strong>.</span>
                        <div class="table-images text-center"><img class="qr-instruction" alt="GO-PAY QR Instruction 3" src="//d2f3dnusg0rbp7.cloudfront.net/snap/assets/qr-instruction-2-76e4903a94594acce1954b8b037bb321ce1770d703406ba8f2d4290d368ee574.png" width="440" height="320"></div>
                     </td>
                  </tr>
                  <tr>
                     <td class="table-row table-numeric text-body">4.</td>
                     <td class="table-row text-body text-initial"><span>Periksa kembali detail pembayaran Anda di aplikasi <strong>GO-JEK</strong> dan tekan <strong>Pay</strong>.</span></td>
                  </tr>
                  <tr>
                     <td class="table-row table-numeric text-body">5.</td>
                     <td class="table-row text-body text-initial"><span>Setelah pembayaran selesai, untuk dapat mengetahui informasi mengenai pesanan anda, silahkan menggunakan tautan dibawah ini. <br><br><a style="text-align: center;width: 100%;" href="<?php echo url('/') . "/user/order_history_detail/" . $po_number; ?>">Informasi Pesanan</a></span></td>
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
      @endif
    </div>                   
  </div>
</div>
@endsection