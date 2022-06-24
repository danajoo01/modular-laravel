@extends('layouts.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/catalog-list.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/wishlist.css') }}">
<style>
.ask-box{width: 200px;height: 100px;text-align: center;top: 5px;left: 5px;box-sizing:border-box;border:1px solid #dedede;background:#eee;position:absolute;border-radius:3px;z-index:10;-webkit-box-shadow: -1px 1px 5px 0px rgba(50, 50, 50, 0.3);
-moz-box-shadow:    -1px 1px 5px 0px rgba(50, 50, 50, 0.3);
box-shadow:         -1px 1px 5px 0px rgba(50, 50, 50, 0.3);}
.ask-box ul li{display:block;float:left;width:49.5%;}
.ask-box ul li a{display:block;padding:10px 0;box-sizing:border-box;color:#000;}
.ask-box span{border-bottom:1px solid #fff;display:block;padding:15px;color:#000;font-size: small;}
/*.ask-box ul li:first-child{border-right:1px solid #fff;}*/
.ask-box ul li a:hover{color:#fff !important;background:#222;}
</style>
@endsection

@section('content')

<div class="user-wrapper clearfix">
    <div class="wrapper">
    	<div class="user-wrap">
        	{!! get_view('account', 'account.leftmenu', array('page'=>'wishlist','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-heart"></i>Wishlist
                    </h1>
                    <div class="wish-list clearfix">
					@if ($total>0)
						<div id="wishlist-info"></div>
                        <ul>
                        	@foreach($wishlist as $wishlist_data)
								<?php 
									$url_arr = explode(',', $wishlist_data->url_set);
									$parent  = isset($url_arr[0]) ? $url_arr[0] : $wishlist_data->type_url;
									$child   = isset($url_arr[1]) ? $url_arr[1] : $wishlist_data->type_url;
									$url     = url(''.$parent.'/'.$child.'/'.$wishlist_data->pid.'/'.str_slug($wishlist_data->product_name, '-').'');
								?>
	                            <li id="wishlist-{{ $wishlist_data->pid }}">
	                                <a href="{{ $url }}">
	                                    @if(isset($wishlist_data->discount) && $wishlist_data->discount > 0)
					                    <div class="disc-flag clear" style="z-index:1;">
					                        <div class="disc-wrap">{{ $wishlist_data->discount }}%</div>
					                        <div class="triangle-topleft left"></div>
					                        <div class="triangle-topright right"></div>
					                    </div>
					                    @endif
					                	<span>
	                                        <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $wishlist_data->image_name }}" alt="">
	                                        @if (isset($wishlist_data->set_display_limited_item_set_bb) && $wishlist_data->set_display_limited_item_set_bb == 1 && (int) $wishlist_data->inventory > 0 && $wishlist_data->product_status <> 2)
												<?php
													$set_display_limited_item_category_bb = isset($wishlist_data->set_display_limited_item_category_bb) ? $wishlist_data->set_display_limited_item_category_bb : '';
													$set_display_limited_item_minimal_bb = isset($wishlist_data->set_display_limited_item_minimal_bb) ? $wishlist_data->set_display_limited_item_minimal_bb : 0;
												?>
												@if(! stristr($set_display_limited_item_category_bb, $wishlist_data->type_url) === FALSE && (int) $wishlist_data->inventory < $set_display_limited_item_minimal_bb)
													<div class="limited-qty">PERSEDIAAN TERBATAS</div>
												@endif
											@endif
											
											@if(isset($wishlist_data->set_display_oos_set_bb) && $wishlist_data->set_display_oos_set_bb == 1  && ($wishlist_data->product_status == 2 || (int) $wishlist_data->inventory == 0))
												<?php
													$set_display_oos_category_bb = isset($wishlist_data->set_display_oos_category_bb) ? $wishlist_data->set_display_oos_category_bb : '';
												?>
												@if(! stristr($set_display_oos_category_bb, $wishlist_data->type_url) === FALSE )
													<div class="limited-qty">PERSEDIAAN HABIS</div>
												@endif
											@endif                                        
	                                    </span>
	                                    <div class="prod-title-list">
		                                    <h1 class="catalog-name">{{ ucfirst($wishlist_data->product_name) }}</h1>
		                                    {{-- <h2 class="catalog-brand">{{ ucfirst($wishlist_data->brand_name) }}</h2> --}}
		                                    @if(isset($wishlist_data->product_sale_price) && $wishlist_data->product_sale_price <> 0 && $wishlist_data->product_sale_price <> '')
												<p class="catalog-price disc-price"><span>IDR {{ number_format(($wishlist_data->product_price), 0, '.', '.') }}</span>IDR {{ number_format(($wishlist_data->product_sale_price), 0, '.', '.') }}</p>
											@else
												<p class="catalog-price">IDR {{ number_format(($wishlist_data->product_price), 0, '.', '.') }}</p>
											@endif
										</div>
									</a>
									<a class="close-wish modal-remove" id="wishlist" pid="{{ $wishlist_data->pid }}" href="#">
										<i class="fa fa-times-circle" aria-hidden="true"></i>
									</a>
				                  	<div id="remove-box-{{ $wishlist_data->pid }}" class="ask-box clearfix remove-box" style="display:none;">
				                    	<span>Hapus Produk Dari Wishlist ?</span>
				                      	<ul>
				                        	<li><a class="remove-cart" href="#" pid="{{ $wishlist_data->pid }}">Ya</a></li>
				                        	<li><a class="close-box" href="#" pid="{{ $wishlist_data->pid }}">Tidak</a></li>
				                      	</ul>
				                  	</div>
	                            </li>
                            @endforeach
                        </ul>
					@else
						<ul>
							<li>Wishlist anda kosong</li>
						</ul>
					@endif
                    </div>
					@if ($total>0)
					<div class="clear"></div>
					<div class="pagination right">
						<ul>
							{!! paginate_page($page_num, $total, 'onclick="changePage(this)"', 8) !!}	
						</ul>
					</div>
					@endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- JS here -->
<!-- s: script wishlist -->
<script type="text/javascript">
    function remove_wish(el)
    {
		// Save it!
		var product_id = el;
		var dataString = 'product_id=' + product_id;
		var type = 2;
		dataString = dataString + '&type=' + type;
		$.ajax({
			type: "GET",
			url: "<?php echo URL::to('/product/set_wishlist');?>",
			data: dataString,
			dataType: "json",
			success: function(data) {
				if(data == 'success' && type == 2){
					$('#wishlist-info').empty().append('<span class="success-msg"><i aria-hidden="true" class="fa fa-times"></i> Sukses menghapus produk dari wishlist anda.</span>').slideDown();
					$('#wishlist-'+product_id).remove();
				}else{
				  $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Terjadi Kesalahan! Produduk ini tidak dapat dihapus dari wishlist anda. Silahkan mencoba beberapa saat lagi.</span>').slideDown();
        }
			}
		});
			// SCROLL TO TOP
		$("html, body").animate({ scrollTop: 0 }, 300);
		setTimeout(function(){ $('#wishlist-info').slideUp().empty(); }, 7000);
    }
</script>

<script src="{{ asset('js/desktop/wishlist_bb.js') }}"></script>
<!-- e: script wishlist -->
@endsection

@section('marketing-tag')

@if(getMarketingEnv() == true)
    @include('marketing-tag.berrybenka.desktop.wishlist', ['wishlist' => $wishlist])
@endif

@endsection