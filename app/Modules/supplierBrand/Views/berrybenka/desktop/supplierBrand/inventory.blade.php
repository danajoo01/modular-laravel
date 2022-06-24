@extends('supplier.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/order-stat-track.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/about.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/supplier.css') }}">
@endsection

@section('content')

<div class="order-track">
	<div class="wrapper">
	    <div class="category-head">
	        <h4>
	        	{!! ucwords(str_replace('-',' ',$title)) !!}
				<a href="/brand_report/index/id/{{ $customer_id }}" class="pull-right back-button">Back</a>
	        </h4>
	    </div>
	    <div class="pull-left" style="padding-bottom: 5px;">
	    	<a href="/brand_report/get_csv_inventory/{{ $customer_id }}/{{ $brand_id }}/{{ $status }}" class="pull-left back-button">Click Here To Make Report in CSV</a>
	    </div>
	    <div class="pull-right">
            <select class="dropdown-select" name="sort-price" id="sort-price" onchange="javascript:location.href='/brand_report/inventory/customer-id/{{ $customer_id }}/brand-id/{{ $brand_id }}?status='+$(this).val();">
                <option value="ALL"> -- Pilih --</option>
                <option value="INCOMING" <?php echo ($status == "INCOMING") ? 'selected="selected"' : ''; ?>> Incoming</option>
                <option value="NEW_PRODUCT" <?php echo ($status == "NEW_PRODUCT") ? 'selected="selected"' : ''; ?>> New Product</option>
                <option value="ENABLED" <?php echo ($status == "ENABLED") ? 'selected="selected"' : ''; ?>> Enabled</option>
                <option value="OUT_OF_STOCK" <?php echo ($status == "OUT_OF_STOCK") ? 'selected="selected"' : ''; ?>> Out of Stock</option>
                <option value="DISABLED" <?php echo ($status == "DISABLED") ? 'selected="selected"' : ''; ?>> Disabled</option>
            </select>
        </div>
	    <div class="pull-right" style="margin: 8px;">
	    	FILTER PRODUCT STATUS :
	    </div>
	    <div class="pull-right">
	    	<div class="search-brand">
		    	<form name="search_product" id="search_product" action="/brand_report/inventory/customer-id/{{ $customer_id }}/brand-id/{{ $brand_id }}" method="GET">
	                <input type="text" name="product_name" id="product_name" placeholder="Product Name" class="text-input" value="">
	                <input type="hidden" name="status" value="{{ $status }}"/>
	                <button type="submit" class="btn-brand"><i class="fa fa-search"></i></button>
	            </form>
	        </div>
	    </div>
	    <table class="table table-bordered" id="tracking_table">
	    	<thead>
	        	<tr>
	                <th>#</th>
	                <th>SKU</th>
	                <th>Brand</th>
	                <th>Product Name</th>
	                <th>Size</th>
	                <th>Color</th>
	                <th>Inventory</th>
	                <th>Launch Date</th>
	                <th>Status</th>
	            </tr>
	        </thead>
	        <tbody>
	            @if ($data)
	                <?php $i = $data->firstItem(); ?>
	                @foreach ($data as $row)
	                    <tr>
	                        <td>{{ $i }}</td>
	                        <td>{{ $row->SKU }}</td>
	                        {{-- <td>{{ $fetch_brand_name['brand_name'] }}</td> --}}
	                        <td>{{ $row->product_name }}</td>
	                        <td>{{ $row->product_size }}</td>
                			<td><?php echo ($row->variant_color_name_custom <> '' && $row->variant_color_name_custom <> NULL) ? $row->variant_color_name_custom : $row->variant_color_name;?></td>
	                        <td>{{ $row->quantity_warehouse }}</td>
	                        <td>{{ $row->product_launch_date }}</td>
	                        <td>{{ $row->product_status }}</td>
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
            	<?php 
            		$param = ['status' => $status];

            		if(isset($product_name) && !empty($product_name))
            		{
            			$param['product_name'] = $product_name;
            		}
            	?>
                {!! $data->appends($param)->render() !!}
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

$(document).ready(function()
{
    $("#search_product").submit(
    	function()
    	{
	        if($("#product_name").val()=="")
	        {
	                $("#product_name").remove();
	        }
    	}
    );
 });
</script>
@endsection