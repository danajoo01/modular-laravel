@extends('layouts.berrybenka.mobile.main')

@section('css')
	<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/error.css') }}">
@endsection

@section('content')
	<div class="error-wrapper">
		<h1>{!! $message !!}</h1>
        <p>Lihat koleksi terbaru kami <a href="{{ URL::to('/new-arrival') }}">disini</a> atau klik ikon dibawah ini untuk berbelanja barang-barang anda.</p>
        <a href="{{ URL::to('/new-arrival') }}"><img src="{{ asset('berrybenka/desktop/img/mane.png') }}" alt=""></a>
	</div>
    <div class="error-report">Tolong bantu Kami untuk meningkatkan situs kami dan melaporkan kesalahan kepada Tim CS kami.<br>Terima Kasih</div>
@endsection