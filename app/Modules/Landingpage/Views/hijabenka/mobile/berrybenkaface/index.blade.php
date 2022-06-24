@extends('layouts.berrybenka.main')

@section('css')
<!-- CSS here -->
@endsection

@section('content')

<div class="content">
	<h1 class="catalog-title">{!! ucwords(str_replace('-',' ',$title)) !!}</h1>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection