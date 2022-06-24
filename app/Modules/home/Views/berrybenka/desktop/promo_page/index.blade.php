@extends('layouts.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<style type='text/css'>
  .bg-sticky{background: #e9e9e9;}
  .sticky-banner{font-weight:300; font-size:18px; display: inline-block; line-height: 40px; padding: 0 15px; letter-spacing: 1px;}
  .sticky-space{height:30px; border-right:1px solid #222; margin:0px 5px;}
  .sticky-banner.blue{color: #1E8BC3;}
  .sticky-banner.gray{color: #999;}
</style>
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/speical-promo-landing.css') }}">
@endsection

@section('content')
<!-- CONTENT -->
    <!-- TOP MINI BANNER -->
    @if (!empty($top_banner_mini) and count($top_banner_mini) > 0){
        <div class="full-width bg-sticky text-center">
			<div class="container">
    			{!! $landing_page !!}
    		</div>
    	</div>
    @endif
	<!-- END TOP MINI BANNER -->
    
	<div class="full-width">
	    <div class='container mb10'>
	        <div class='bystyle-container'>
	            {!! $landing_page !!}
	        </div>
	    </div>
	</div>
<!-- CONTENT -->
@endsection

@section('js')
<!-- JS here -->
@endsection