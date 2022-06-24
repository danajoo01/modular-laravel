@extends('layouts.shopdeca.desktop.main')

@section('css')
	<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/error.css') }}">
@endsection

@section('content')
	<div class="error-content">
		<div class="error-overlay">
			<div class="error-inside">
				<h1>{!! $message !!}</h1>
                <p>Lihat koleksi terbaru kami <a href="{{ URL::to('/new-arrival') }}">disini</a> atau klik ikon dibawah ini untuk berbelanja barang-barang anda.</p>
                <a href="{{ URL::to('/new-arrival') }}"><img src="{{ asset('shopdeca/desktop/img/mane.png') }}" alt=""></a>
            </div>
        </div>
    </div>
@endsection