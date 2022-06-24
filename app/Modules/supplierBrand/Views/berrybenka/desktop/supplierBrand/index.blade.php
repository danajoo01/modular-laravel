@extends('supplier.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/order-stat-track.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/about.css') }}">
@endsection

@section('content')

<div class="order-track">
	<div class="wrapper">
	    <div class="category-head">
	        <h4>{!! ucwords(str_replace('-',' ',$title)) !!}</h4>
	    </div>
	    <?php
	        $int_before = mktime(0,0,0,date("m")-1);
	        $date_before = date('F',$int_before);
	    ?>
		<table class="table table-bordered" id="tracking_table">
	    	<thead>
	        	<tr>
	                <td>#</td>
	                <td>Brand</td>
	                <td>Review Inventory</td>
	                <td>Report Download <?php echo $date_before.' '.date('Y');?></td>
	            </tr>
	        </thead>
	        <tbody>
	            @if ($data)
	                <?php $i = $data->firstItem(); ?>
	                @foreach ($data as $row)
	                    <tr>
	                        <td>{{ $i }}</td>
	                        {{-- <td>{{ $row['brand_name'] }}</td> --}}
	                        <td>
	                        	<a href="/brand_report/inventory/customer-id/{{ $customer_id }}/brand-id/{{ $row['brand_id'] }}?status=ENABLED">Click Here To Review</a>
	                        </td>
	                        <td>
	                            <a href="/brand_report/show_report/{{ $row['brand_id'] }}/{{ $date_before }}/<?php echo date('Y');?>">Click Here To Download CSV</a>
	                        </td>
	                    </tr>
	                <?php $i++ ?>
	                @endforeach
	            @else 
	                <tr>
	                    <td colspan="5" style="text-align:center;">Data Anda Kosong</td>
	                </tr>
	            @endif
	        </tbody>
	    </table>
	    <div class="pagination right">
            @if ($data)
                {!! $data->render() !!}
            @endif
        </div>
	</div>
</div>

@endsection

@section('js')
<!-- JS here -->
<script>
$(window).scroll(function()
{
	if ($(this).scrollTop()>0)
	{
		$('.top-header').fadeOut(0);
		$('.bottom-header').addClass('nav-header');
	}
	else
	{
		$('.top-header').fadeIn(0);
		$('.bottom-header').removeClass('nav-header');
	}
});

$('html').click(function()
{
	$('.user-wrappers').hide();
});

$('.user-dd a').click(function()
{
	$('.user-wrappers').toggle('fast');
});
</script>
@endsection