@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/product-detail.css') }}">
<style type="text/css">
    #prism-widget { display: none; }
    .cta{position:fixed;bottom:0px;width:100%;z-index:999;display:flex;justify-content:center;align-items:center;background:#333;left:0px;}
    .cta input[type="submit"]{    display: block;
        text-transform: uppercase;
        font-family: 'gotham',arial;
        letter-spacing: 1px;
        text-align: center;
        width: 50%;
        padding: 15px 0;
        background: #333;
        color: #fff;
        border: none;height:100%;line-height:1;height:100%;}
    .cta .size-choose{width:50%;background:#999;height:100%;}
    .cta .size-choose select{width:100%;padding:15px;display:block;border:none;border-radius:0;background:none;font-family:'gotham',arial;color:#fff;text-transform:uppercase;line-height:1;}
</style>
@endsection

@section('content')
<div class="content-detail">
	<!-----_----- ALERT ---------------->
	<div style="display: none" id="success-back" class="success-msg-login" >
		<i aria-hidden="true" class="fa fa-times"></i> 
		Sukses Memasukkan Produk ke Dalam Tas Belanja
	</div>
	<div style="display: none" id="error-size-back" class="error-msg-login" >
		<i aria-hidden="true" class="fa fa-bell"></i>
		<i aria-hidden="true" class="fa fa-times"></i>
		Pilih Ukuran Terlebih Dahulu
	</div>				
	<div style="display: none" id="error-color-back" class="error-msg-login" >
		<i aria-hidden="true" class="fa fa-bell"></i>
		<i aria-hidden="true" class="fa fa-times"></i>
		Pilih Warna Terlebih Dahulu
	</div>
	<div style="display: none" id="error-stock-back" class="error-msg-login" >
		<i aria-hidden="true" class="fa fa-bell"></i>
		<i aria-hidden="true" class="fa fa-times"></i>
		Produk ini sedang tidak tersedia
	</div>
	<div style="display: none" id="error-colorsize-back" class="error-msg-login" >
		<i aria-hidden="true" class="fa fa-bell"></i>
		<i aria-hidden="true" class="fa fa-times"></i>
		Pilih Ukuran dan Warna Terlebih Dahulu
	</div>
	<div style="display: none" id="error-manetail-back" class="error-msg-login" >
		<i aria-hidden="true" class="fa fa-bell"></i>
		<i aria-hidden="true" class="fa fa-times"></i>
		Produk ini hanya bisa dibeli maksimal 2 barang
	</div>
	<div id="wishlist-info"></div>
	<!------------------------------------>
	<div class="flexslider">
        <ul class="slides" id="images-selected">
		<li><img src="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}"></li>
		<?php $i=0 ?>
                @if (isset($fetch_product_image) && !empty($fetch_product_image))
                    @foreach ($fetch_product_image as $rows)
                            @if ($rows->id != $fetch_product_image_def->id)
                                    <li><img src="{{ ASSETS_PATH }}upload/product/zoom/{{ $rows->image_name }}"></li>
                            @endif
                            <?php if (++$i == 5) break; ?>
                    @endforeach
                @endif
        </ul>
    </div>
    <div class="product-detail">
    	<h1>{{ $product_name }}</h1>
        {{-- <!-- <h2>{{ $product_brand_name }}</h2> --> --}}
        <div class="detail-price">
		 @if (isset($fetch_product->product_sale_price) && $fetch_product->product_sale_price != 0)
			<span>IDR{{ number_format(($fetch_product->product_price), 0, '.', '.') }}</span>IDR{{ number_format(($fetch_product->product_sale_price), 0, '.', '.') }}
		@else
			IDR{{ number_format(($fetch_product->product_price), 0, '.', '.') }}
		@endif
		</div>
        <p>{{ $product_description }}</p>
		<form onsubmit="setCart(this.value);" method="post" action="javascript:void(0);" name="frmAddCart" id="frmAddCart">
		<input type="hidden" name="_token" value="{{ csrf_token() }}"> 
		<input type="hidden" name="brand_id" id="brand_id" value="{{ isset($fetch_product->brand_id) ? $fetch_product->brand_id : '' }}">  
		<input type="hidden" name="type_id" id="type_id" value="{{ isset($fetch_product->type_id) ? $fetch_product->type_id : '' }}">
		<input type="hidden" name="type_id_real" id="type_id_real" value="{{ isset($fetch_product->type_id_real) ? $fetch_product->type_id_real : '' }}"> 
		<input type="hidden" name="parent_type_id" id="parent_type_id" value="{{ isset($fetch_product->parent_type_id) ? $fetch_product->parent_type_id : '' }}"> 
		<input type="hidden" name="parent_type_id_real" id="parent_type_id_real" value="{{ isset($fetch_product->parent_type_id_real) ? $fetch_product->parent_type_id_real : '' }}"> 
		<input type="hidden" name="product_price" id="product_price" value="{{ isset($fetch_product->product_price) ? $fetch_product->product_price : '' }}">   
		<input type="hidden" name="product_sale_price" id="product_sale_price" value="{{ isset($fetch_product->product_sale_price)?$fetch_product->product_sale_price:0 }}">   
		<input type="hidden" name="product_special_price" id="product_special_price" value="{{ isset($fetch_product->special_price) ? $fetch_product->special_price : '' }}">   
		<input type="hidden" name="product_name" id="product_name" value="<?php echo str_replace(array("&","/","\'"), array('and','or','_singlequote_'), $product_name);?>">
		<input type="hidden" name="product_weight" id="product_weight" value="<?php echo isset($fetch_product->product_weight)?$fetch_product->product_weight:NULL;?>">
		<input type="hidden" name="brand_name" id="brand_name" value="{{ isset($fetch_product->brand_name) ? $fetch_product->brand_name : '' }}">                                    
		<input type="hidden" name="quantity" id="quantity" value="1">  					
		<input type="hidden" name="variant_color_id" id="variant_color_id" value="{{ isset($fetch_product_image_def->variant_color_id) ? $fetch_product_image_def->variant_color_id : '' }}"/>
		<input type="hidden" name="product_id" id="product_id" value="{{ isset($fetch_product->pid) ? $fetch_product->pid : '' }}"> 
		<input type="hidden" name="product_inv" id="product_inv" value="{{ isset($fetch_product->brand_name) ? $fetch_product->brand_name : '' }}">
		<input type="hidden" name="product_front_end_type" id="product_front_end_type" value="{{ isset($fetch_product->front_end_type) ? $fetch_product->front_end_type : '' }}">
		<input type="hidden" name="product_type_url" id="product_type_url" value="{{ isset($fetch_product->url_set) ? $fetch_product->url_set : '' }}">
    <input type="hidden" name="product_gender" id="product_gender" value="{{ isset($fetch_product->product_gender) ? $fetch_product->product_gender : '' }}">
					
        <div class="detail-color">
        <p>Pilih Warna</p>
            <ul id="filter-color">
                @if(isset($fetch_product_color) && !empty($fetch_product_color))
                    @foreach ($fetch_product_color as $row)
                        @if (isset($row->variant_color_id) && !empty($row->variant_color_id))
                            <?php 
                            $original_color_name = isset($row->variant_color_name) ? $row->variant_color_name : 'color name not set';

                            $desc_color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;
                            $hold_desc_color = NULL;
                            $color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;

                            $original_hexa = isset($row->variant_color_hexa) ? $row->variant_color_hexa : 'FFF';
                            $color_hex = isset($row->variant_color_hexa_custom) ? $row->variant_color_hexa_custom : $original_hexa;
                            $hold_desc_color[0] = $desc_color;
                            $color_id = $row->variant_color_id; 
                            ?>
                            <li>
                                <input name="variant_color_id" type="radio" class="color-filter" id="checkbox-{{ $color_id }}" value="{{ $color_id }}" onclick="selectcolor(this.value); ga('send', 'event', 'Product', 'Button', 'color', 1);">
                                <label style="background-color: <?php echo "#".$color_hex?>;" for="checkbox-{{ $color_id }}" data-original-title="{{ $desc_color }}"></label>
                            </li>
                        @endif
                    @endforeach
                @endif
                            
                @if(isset($fetch_product_color_zero) && !empty($fetch_product_color_zero))
                    @foreach ($fetch_product_color_zero as $row)
                        @if (isset($row->variant_color_id) && !empty($row->variant_color_id))
                            <?php 
                            $original_color_name = isset($row->variant_color_name) ? $row->variant_color_name : 'color name not set';

                            $desc_color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;
                            $hold_desc_color = NULL;
                            $color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;

                            $original_hexa = isset($row->variant_color_hexa) ? $row->variant_color_hexa : 'FFF';
                            $color_hex = isset($row->variant_color_hexa_custom) ? $row->variant_color_hexa_custom : $original_hexa;
                            $hold_desc_color[0] = $desc_color;
                            $color_id = $row->variant_color_id; 
                            ?>
                            <li>
                                <input name="variant_color_id" type="radio" class="color-filter" id="checkbox-{{ $color_id }}" value="{{ $color_id }}" onclick="selectcolor(this.value); ga('send', 'event', 'Product', 'Button', 'color', 1);">
                                <label style="background-color: <?php echo "#".$color_hex?>;" for="checkbox-{{ $color_id }}" data-original-title="{{ $desc_color }}"></label>
                            </li>
                        @endif
                    @endforeach
                @endif
            </ul>
        </div>
        <div class="detail-size-select">
        	
        </div>
        
        @if ( !empty(\Request::get('spc_case')) || !empty(\Request::get('spc_num')) || !empty(\Request::get('trc_sale')) )
			@if ( !is_array(\Request::get('spc_case')) || !is_array(\Request::get('spc_num')) || !is_array(\Request::get('trc_sale')) )
                <?php 
                    $spc_case = !empty(\Request::get('spc_case')) ? \Request::get('spc_case') : '';
                    $spc_num = !empty(\Request::get('spc_num')) ? \Request::get('spc_num') : '';
                    $trc_sale = !empty(\Request::get('trc_sale')) ? \Request::get('trc_sale') : '';
                    
                    $spc_case = is_array(\Request::get('spc_case')) ? json_encode(\Request::get('spc_case')) : $spc_case; 
                    $spc_num = is_array(\Request::get('spc_num')) ? json_encode(\Request::get('spc_num')) : $spc_num; 
                    $trc_sale = is_array(\Request::get('trc_sale')) ? json_encode(\Request::get('trc_sale')) : $trc_sale; 
                ?>
            @endif
            <input type="hidden" name="Promo" value="{{ isset($spc_case) ? $spc_case : ucwords(str_replace('-',' ',\Request::get('spc_case'))) }}">
            <input type="hidden" name="Promo_ID" value="{{ isset($spc_num) ? $spc_num : \Request::get('spc_num') }}">
            <input type="hidden" name="sale_tracking" value="{{ isset($trc_sale) ?  $trc_sale : \Request::get('trc_sale') }}">
		@endif
		
        @if($product_is_oos !== TRUE)
        <div class="cta">
            <div class="size-choose">
                <?php $total_inventory = 0; ?>
                @if(isset($fetch_product_size) && !empty($fetch_product_size))
                <select name="size_category" id="size_category" onchange="getNewSKU(); _gaq.push(['_trackEvent','Product','Button','sizeSelect']);">
                    <option value="">pilih ukuran</option>
                    @foreach($fetch_product_size as $rows)
                    <option id="{{ $rows->product_sku }}" value="{{ $rows->product_size }}" @if($rows->inventory <= 0) disabled @endif>{{ $rows->product_size }}</option>                    
                    <?php $total_inventory = $total_inventory + $rows->inventory; ?>
                    @endforeach
                </select>
                @endif
                <input type="hidden" name="variant_color_name" id="variant_color_name" value="{{ $variant_color_name }}">
                <input type="hidden" name="image_name" id="image_name" value="{{ $image_def_name }}">
            </div>
            <input type="submit" value="beli sekarang">
        </div>
        @else
        <input type="submit" class="detail-order" value="habis terjual" disabled>
        @endif
        
        <div class="sku-tags">
            <span id="selectsku"></span>
            <span><strong>Categories</strong>: <a href="#">{{ $fetch_product->type_name }}</a></span>
            <span><strong>Tags</strong>: {!! $tag_name !!}</span>
        </div>
		</form>
        <div class="additional-detail">
            <ul>
                <li class="detail-col">
                    <h1>Rincian Ukuran &amp; Fit :</h1>
                    <div>
                        {!! isset($fetch_product->product_size_guideline)?$fetch_product->product_size_guideline:'' !!}
                    </div>
                </li>
                <li class="detail-col">
                    <h1>Perawatan :</h1>
                    <div>
                        {!! isset($fetch_product->product_info)?$fetch_product->product_info:'' !!}
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
<script src="{{ asset('js/mobile/product-detail-mobile.js') }}"></script>
<script type="text/javascript">   
    function getNewSKU() {
        var sku = $("#size_category").children(":selected").attr("id");
        if (sku != undefined) {
            getSKU(sku);
        }        
    }
</script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php 
$user = \Auth::user(); 

//additional for gtm                
$arrCat = array_filter(explode(',' , $fetch_product->front_end_type));
if(isset($arrCat)){
    $ChildId_gtm = array_values(array_slice($arrCat, -1))[0];    
}

$arrCatName = array_filter(explode(',' , $fetch_product->url_set));
if(isset($arrCatName)){
    $ChildName_gtm = array_values(array_slice($arrCatName, -1))[0];
}
?>
var detail_product336CC993E54E = {
	customer_id                 : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
	customer_fname              : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
	customer_lname              : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
	customer_email              : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
	product_id                  : '{{ isset($fetch_product->pid) ? $fetch_product->pid : '' }}',
        product_price               : '{{ isset($fetch_product->product_price) ? $fetch_product->product_price : '' }}',        
        product_name                : '{{ $product_name }}',
        brand_name                  : '{{ $product_brand_name }}',
        brand_id                    : '{{ isset($fetch_product->brand_id) ? $fetch_product->brand_id : '' }}',
        product_frontendtypeID      : '{{ isset($ChildId_gtm) ? $ChildId_gtm : '' }}',
        product_frontendtypeName    : '{{ isset($ChildName_gtm) ? $ChildName_gtm : '' }}'
}
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.berrybenka.mobile.detail-page',['mkt_product_id' =>json_encode(isset($fetch_product->pid) ? $fetch_product->pid : '')])    
@endif

@endsection