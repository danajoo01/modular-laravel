@extends('layouts.shopdeca.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/mobile/css/home.css') }}">
@endsection

@section('content')

    {!! $home !!}

@endsection

@section('js')
  
@endsection

@section('marketing-tag')
	@include('marketing-tag.shopdeca.mobile.homepage')
@endsection