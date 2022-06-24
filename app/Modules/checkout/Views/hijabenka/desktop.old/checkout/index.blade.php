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

@section('marketing-tag')
<script type="text/javascript">
<?php $user = \Auth::user(); ?>
var finalorder336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    purchase_code : "{{ $purchase_code }}",
    item : {!! json_encode($tag_products) !!}
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.cart-page')
@endif

@endsection