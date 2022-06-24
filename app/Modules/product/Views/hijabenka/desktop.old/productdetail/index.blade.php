@extends('layouts.hijabenka.desktop.main')

@section('meta')
	<link rel="canonical" href="{{ \Request::url() }}" />
	
	<meta property="og:site_name" content="hijabenka.com" />	
	<meta property="og:type" content="article" />
	<meta property="og:url" content="{{ \Request::url() }}" />
        
        <meta property="og:title" content="{{ $product_name }}" />
        <meta property="og:description" content="{{ $product_description }}" />
        
	<meta property="og:image" content="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:width" content="520" />
	<meta property="og:image:height" content="320" />
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/product-detail.css') }}">
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/catalog-list.css') }}">
@endsection

@section('content')
<div class="content">
	<div class="catalog-list">
		<div class="wrapper">
        	<div class="content-header">
                <div class="breadcrump">
					<?php $breadcrump = explode(",", $fetch_product->bahasa); ?>
                    <ul>
                        @if ($breadcrump)
                            @for ($i = 0; $i < count($breadcrump); $i++)
                                    <li><a href="#">{{ $breadcrump[$i] }}</a></li>
                                    @if ($i < (count($breadcrump)-1))
                                    <li>/</li>	
                                    @endif
                            @endfor	
                        @endif
                    </ul>
                </div>
                <div class="clear"></div>
				<!-----_----- ALERT ---------------->
				<div style="display: none" id="success-back" class="success-msg" >
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
            </div>
            <div class="detail-wrapper">
            	<div class="detail-photo left" id="images-selected">
					<div class="big-photo left">
                                            @if (isset($image_def_name))                                            
                                                <a class="fancybox-effects-c" href="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}">
                                                    <img id="default_image" src="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}">
                                                </a>
                                            @endif                                              
                                        </div>
						<div class="small-photo left">
							<ul>
								<?php $i=0 ?>
                                                                @if(isset($fetch_product_image) && !empty($fetch_product_image))
                                                                    @foreach ($fetch_product_image as $rows)
                                                                        @if ($rows->id != $fetch_product_image_def->id)
                                                                        <li><a class="fancybox-effects-c" href="{{ ASSETS_PATH }}upload/product/zoom/{{ $rows->image_name }}"><img src="{{ ASSETS_PATH }}upload/product/zoom/{{ $rows->image_name }}"></a></li>
                                                                        @endif
                                                                        <?php if (++$i == 5) break; ?>
                                                                    @endforeach              
                                                                @endif
							</ul>
						</div>
				</div>
				
                <div class="detail-spec left">
					<div class="addtowish"><a href="#"><i title="Add to Wishlist" class="fa {{ ($wishlist_status==true)?'fa-heart':'fa-heart-o' }}" rel="<?php echo $fetch_product->pid;?>" onclick="set_wishlist(this);"></i></a></div>
                    <div class="prod-spec-title">
                    	<h1>{{ $product_name }}</h1>
                        <h2><a href="{{ URL::to('/brand/'.$fetch_product->brand_url) }}">{{ $product_brand_name }}</a></h2>
                        @if (isset($fetch_product->product_sale_price) && $fetch_product->product_sale_price != 0)
                            <p><span>IDR{{ number_format(($fetch_product->product_price), 0, '.', '.') }}</span>IDR{{ number_format(($fetch_product->product_sale_price), 0, '.', '.') }}</p>
                        @else
                            <p>IDR{{ number_format(($fetch_product->product_price), 0, '.', '.') }}</p>
                        @endif
                    </div>
                    <p class="prod-desc" id="product_description" rel="{{ $product_description }}">{{ $product_description }}</p>
                    <a href="#" class="lengkap"></a>
                    <form onsubmit="setCart(this.value);" method="post" action="javascript:void(0);" name="frmAddCart" id="frmAddCart">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                        <input type="hidden" name="customer_id" id="userauth" value="{{ (Auth::guest()) ? "undefined" : Auth::user()->customer_id }}">
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
					
                    <div class="detail-spec-detail">
                    	<p>Warna</p>
                        <div class="filter-color filter-content">
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
								<!--
                                <li><input type="checkbox" class="color-filter light-grey" id="checkbox-light-grey"><label for="checkbox-light-grey"></label></li>
                                <li><input type="checkbox" class="color-filter light-blue" id="checkbox-light-blue"><label for="checkbox-light-blue"></label></li>
                                -->
                            </ul>							
                        </div>
                    </div>
                    @if($product_is_oos !== TRUE)
                    <div class="detail-spec-detail">
                    	<p>Ukuran</p>
                        <div class="filter-size filter-content" id="select-size">
                            <ul>
                            	<?php $total_inventory = 0; ?>
                                @if(isset($fetch_product_size) && !empty($fetch_product_size))
                                    @foreach ($fetch_product_size as $rows)
                                    <li @if($rows->inventory <= 0) title="Habis Terjual" @endif>
                                        <div @if($rows->inventory <= 0) style="background-color:#dedede;" @endif>
                                              <input type="checkbox" name="size_category" value="{{ $rows->product_size }}" id="size-{{ $rows->product_size_url }}" class="size-filter size-{{ $rows->product_size_url }}" @if($rows->inventory <= 0) disabled @endif>
                                               <label for="size-{{ $rows->product_size_url }}" id="{{ $rows->product_sku }}" @if(!$rows->inventory <= 0) onclick="getSKU(this.id); _gaq.push(['_trackEvent','Product','Button','sizeSelect']);" @endif>{{ $rows->product_size }}
                                        <!--                           <span class="tooltip" @if($rows->inventory == 0) style="color:red" @endif >Stok Sisa {{ $rows->inventory }}-->
                                          </span>
                                        </label>
                                        </div>
                                    </li>
                                    <?php $total_inventory = $total_inventory + $rows->inventory; ?>
                                    @endforeach
                                @endif				
                                <!-- @if ($total_inventory <= 0)
                                	<span style='font:bold; color:red'>Maaf, Stok Barang Habis</span><br>
                                @endif -->
                            </ul>
							<input type="hidden" name="variant_color_name" id="variant_color_name" value="{{ $variant_color_name }}">
							<input type="hidden" name="image_name" id="image_name" value="{{ $image_def_name }}">
			</div>
                    </div>
                    @else
                    <div class="detail-spec-detail">
                    	<p>Ukuran</p>
                        <div class="filter-size filter-content">
                            <ul>
                            	<?php $total_inventory = 0; ?>
                                @if(isset($fetch_product_size) && !empty($fetch_product_size))
                                    @foreach ($fetch_product_size as $rows)
                                    <li @if($rows->inventory <= 0) title="Habis Terjual" @endif>
                                        <div>
                                            <input type="checkbox" name="size_category" value="{{ $rows->product_size }}" disabled>
                                          <label style="cursor: default;">{{ $rows->product_size }}                    
                                          </label>
                                        </div>
                                    </li>
                                    <?php $total_inventory = $total_inventory + $rows->inventory; ?>
                                    @endforeach
                                @endif   
                            </ul>
                        </div>
                    </div> 
                    
                    @endif
                                        
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
                        <input type="hidden" name="Promo_ID" value="{!! isset($spc_num) ? $spc_num : \Request::get('spc_num') !!}">
                        <input type="hidden" name="sale_tracking" value="{{ isset($trc_sale) ?  $trc_sale : \Request::get('trc_sale') }}">
                    @endif
				
                    
                    @if($product_is_oos !== TRUE)
                        <input class="checkout-btn" type="submit" value="beli sekarang">
                    @else
                        <input class="checkout-btn" type="submit" value="habis terjual" disabled style="cursor: default;">
                    @endif
                    <div class="sku-tags">
                    	<span id="selectsku"></span>
                        <span><strong>Categories</strong>: <a href="#">{{ $product_type_name }}</a></span>
                        <span><strong>Tags</strong>: {!! $tag_name !!}</span>
                    </div>
					</form>
					<?php // {!! Form::close() !!} ?>
                    <div class="collapse-detail">
                        <ul>
                            <li class="detail-col">
                                <h1>Rincian Ukuran & Fit <span><i class="fa fa-angle-down"></i></span></h1>
                                <div>
								{!! isset($fetch_product->product_size_guideline)?$fetch_product->product_size_guideline:'' !!}
                                </div>
                            </li>
                            <li class="detail-col">
                                <h1>Perawatan <span><i class="fa fa-angle-down"></i></span></h1>
                                <div>
								{!! isset($fetch_product->product_info)?$fetch_product->product_info:'' !!}
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                </div>
                <div class="share right">
                	<ul>
                        <?php /*
                        <!--<li><a href="#"><i class="fa fa-facebook-official" id="fbShareBtn"></i></a></li>-->
                         
                        <div class="fb-share-button" data-href="{{ \Request::url() }}" data-layout="icon" data-mobile-iframe="true"></div>
                        */?>
                        <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Facebook" onclick="fbShare();" id="fb_share"><i class="fa fa-facebook-official"></i></a></li>
                        <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Twitter" onclick="twitterShare()" class="twitter popup" id="twitter_share"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Google+" id="gplus_share" ><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Pinterest" id="pinterest_share"><i class="fa fa-pinterest"></i></a></li>
                    </ul>
                </div>
                <div class="clear"></div>
                <div class="ymal">
                @if (isset($product_recommended) && !empty($product_recommended))
                <h1>Anda Juga Akan Menyukai</h1>
                <ul>				
					<?php
						/* Insert Page Referer */
						$page_ref = urlencode('product-detail+push-product');

						/* Define URL */
						$url_ref = '?trc_sale='.$page_ref;
						$catalog_ids = [];
					?>
					@foreach ($product_recommended as $row)
						<?php
						$catalog_ids[] = $row->pid;
						$url_type 	= explode(',', $row->url_set);
						$parent  	= isset($url_type[0]) ? $url_type[0] : $row->type_url;
                        $child   	= isset($url_type[1]) ? $url_type[1] : $row->type_url;
						$url 		= URL::to('/'.$parent.'/'.$child.'/'.$row->pid.'/'.url_title(strtolower(stripslashes($row->product_name))).$url_ref); 
						?>
						<li>
							<a href="{{ $url }}"><img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $row->image_name }}"></a>
							<div class="catalog-detail">
								<a href="{{ $url }}">
									<h1 class="catalog-name">{{ $row->product_name }}</h1>
									<h2 class="catalog-brand">{{ $row->brand_name }}</h2>
									@if (isset($row->product_sale_price) && $row->product_sale_price != 0)
										<p class="catalog-price"><span>IDR{{ number_format(($row->product_price), 0, '.', '.') }}</span>IDR{{ number_format(($row->product_sale_price), 0, '.', '.') }}</p>
									@else
										<p class="catalog-price">IDR{{ number_format(($row->product_price), 0, '.', '.') }}</p>
									@endif
								</a>
							</div>
						</li>
					@endforeach 				
                </ul>
                @endif
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script async defer src="//assets.pinterest.com/js/pinit.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script src="{{ asset('js/desktop/sosmed.js') }}"></script>
<script src="{{ asset('js/desktop/product-detail.js') }}"></script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php
$user = \Auth::user();

$catalog_json = !empty($catalog_ids) ? $catalog_ids : [];

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
    product_id                  : '{{ !empty($fetch_product->pid) ? $fetch_product->pid : '' }}',
    product_price               : '{{ !empty($fetch_product->product_sale_price) ? $fetch_product->product_sale_price : $fetch_product->product_price }}',
    product_name                : '{{ $product_name }}',
    brand_name                  : '{{ $product_brand_name }}',
    brand_id                    : '{{ isset($fetch_product->brand_id) ? $fetch_product->brand_id : '' }}',
    product_frontendtypeID      : '{{ isset($ChildId_gtm) ? $ChildId_gtm : '' }}',
    product_frontendtypeName    : '{{ isset($ChildName_gtm) ? $ChildName_gtm : '' }}'    
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.product-page', ['catalog_ids' => json_encode($catalog_json)])
@endif

@endsection