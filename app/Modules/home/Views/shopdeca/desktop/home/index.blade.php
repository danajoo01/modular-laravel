@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/home.css') }}">
@endsection

@section('content')

    {!! $home !!}

@endsection

@section('js')
  @if(isset($show_snowflakes) && $show_snowflakes)
    <script src="{{ asset('shopdeca/desktop/script/snowfall.min.js') }}"></script>
  @endif
@endsection

@section('marketing-tag')
	@include('marketing-tag.shopdeca.desktop.homepage')
@endsection