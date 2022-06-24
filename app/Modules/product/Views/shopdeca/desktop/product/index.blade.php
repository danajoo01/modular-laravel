<?php $time = microtime(true); ?>
@extends('layouts.shopdeca.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/catalog-list.css?t=').date('YmdHis') }}">

@endsection

@section('content')
<div class="content">
    <!-- Loading div -->
<!--    <div id="loading">
        <div class="load-icon">
            <img src="{{ asset('shopdeca/desktop/img/bb-loading.gif') }}">
        </div>
    </div>-->
    <!-- ******** -->
    <input type="hidden" class='input-url'
    @if(!empty(findUriSegment('cat_parent')))
        value="{{ categoryUrl($gender) }}"
    @else
        value=""
    @endif
    >

    <input type="hidden" class='input-color-url' value="{{ implodeUri('color', findUriSegment('color')) }}">
    <input type="hidden" class='input-size-url' value="{{ implodeUri('size', findUriSegment('size'), '-') }}">
    <input type="hidden" class='input-brand-url' value="{{ implodeUri('brand', findUriSegment('brand')) }}">
    <input type="hidden" class='sort-url' value="">
    <input type="hidden" class='price-url' value="">
    <input type="hidden" class='page-url' value="@if($page_num != 0)/{{ $page_num }}@endif">
	<div class="catalog-list">
		<div class="wrapper">
        	<div class="content-header">
                <div class="breadcrump">
<!--                     <ul>
                        <li><a href="#">Pakaian</a></li>
                        <li>/</li>
                        <li><a href="#">Atasan</a></li>
                        <li>/</li>
                        <li><a href="#">Kemeja</a></li>
                    </ul> -->
                </div>
                <h1 class="catalog-title">{!! $title !!}</h1>
                <!--<h1 class="catalog-url">{!! $catalog_url !!}</h1>-->
                <div class="sortby">
                    <select id="sort-by" data-sort="true" onchange="ChangeUrl(this)">
                        <option value="" disabled="">Urutkan</option>
                        <?php 
                        $default_filter = '';
                        if(!isset($_GET["price"]) && !isset($_GET["pn"]) && !isset($_GET["discount"]) && !isset($_GET["popular"])){
                            $default_filter = 'selected';
                        }
                        ?>
                        <option value="popular=desc" <?php echo (isset($_GET["popular"]) && $_GET["popular"] == "desc") ? "selected" : $default_filter ; ?>>Populer</option>
                        <option value="pn=desc" <?php echo (isset($_GET["pn"]) && $_GET["pn"] == "desc") ? "selected" : ""; ?>>Produk Terbaru</option>
                        <option value="discount=desc" <?php echo (isset($_GET["discount"]) && $_GET["discount"] == "desc") ? "selected" : ""; ?>>Diskon Terbesar</option> 
                        <option value="price=asc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "asc") ? "selected" : ""; ?>>Harga Terendah</option>
                        <option value="price=desc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "desc") ? "selected" : ""; ?>>Harga Tertinggi</option>
                    </select>
                    <i class="fa fa-angle-down"></i>
                </div>
                <div id="wishlist-info"></div>
                <div class="clear"></div>
            </div>
            <div class="catalog-wrapper">
            	<div class="catalog-filter left" id="sidebar">
                <div class="filter-list">
                    <a href="#" class="clear-filter">Clear Filter</a>
                    <div class="filter-gender filter-content">
                        <p>Gender</p>
                        <ul>
                            <li>
                                <input type="radio" id="checkbox-gender-pria" data-gender="men" onclick="ChangeUrl(this)" @if(isset($gender) && $gender == 'men') checked="checked" @endif name="gender">
                                <label for="checkbox-gender-pria" class="clearfix">Pria</label>
                            </li>
                            <li>
                                <input type="radio" id="checkbox-gender-wanita" data-gender="women" onclick="ChangeUrl(this)" @if(isset($gender) && $gender == 'women') checked="checked" @endif name="gender">
                                <label for="checkbox-gender-wanita" class="clearfix">Wanita</label>
                            </li>
                        </ul>
                    </div>
                    <div class="filter-category filter-content">
                        <p>Kategori</p>
                        <div class="filter-scroll">
                        <ul id="ul-category">
                            @foreach ($category as $key => $values)
                                <!--<ol>{{ $values->type_name_bahasa }}</ol>-->
                                    <li>
                                        <input 
                                            type="radio" 
                                            id="checkbox-category-{{ $values->type_url }}" 
                                            class="category-filter parentCheckBox" 
                                            data-level="parent" 
                                            name="category_level1" 
                                            data-url="{{ Request::segment(1) }}/{{ $values->type_url }}" 
                                            onclick="ChangeUrl(this)"
                                            @if(!empty(findUriSegment('cat_parent')))
                                                {{--*/ $get_key = array_search($values->type_url, findUriSegment('cat_parent')) /*--}}
                                                @if($values->type_url == findUriSegment('cat_parent')[$get_key])
                                                checked="checked"
                                                @endif
                                            @endif
                                        >

                                        <label for="checkbox-category-{{ $values->type_url }}">{{ $values->type_name_bahasa }}</label>

                                        @if ( !empty($values->child) )
                                        <ul>
                                            @foreach ( $values->child as $key2 => $value2 )
                                                <li>
                                                <input 
                                                    type="radio" 
                                                    id="checkbox-category-{{ $value2->type_url }}" 
                                                    class="category-filter childCheckBox" 
                                                    data-level="child" 
                                                    name="category_level2" 
                                                    onclick="ChangeUrl(this)" 
                                                    data-url="{{ Request::segment(1) }}/{{ $values->type_url }}/{{ $value2->type_url }}"
                                                    @if(!empty(findUriSegment('cat_children')))
                                                        {{--*/ $get_key = array_search($value2->type_url, findUriSegment('cat_children')) /*--}}
                                                        @if($value2->type_url == findUriSegment('cat_children')[$get_key])
                                                        checked="checked"
                                                        @endif
                                                    @endif
                                                >
                                                        <label for="checkbox-category-{{ $value2->type_url }}">{{ $value2->type_name_bahasa }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                            @endforeach
                        </ul>
                        </div>
                    </div>
                    <div class="filter-color filter-content">
                        <p>Colors</p>
                        <ul id="ul-color">
                            @foreach($color as $row)
                            <li>
                                <input 
                                    type="checkbox" 
                                    id="checkbox-{{ $row->color_name }}" 
                                    class="color-filter {{ $row->color_name }}" 
                                    onclick="ChangeUrl(this)" 
                                    data-color="true" 
                                    value="{{ $row->color_name }}"
                                    @if(!empty(findUriSegment('color')))
                                        {{--*/ $get_key = array_search($row->color_name, findUriSegment('color')) /*--}}
                                        @if($row->color_name == findUriSegment('color')[$get_key])
                                        checked="checked"
                                        @endif
                                    @endif
                                >
                                    <label for="checkbox-{{ $row->color_name }}" style="background-color:#{{ $row->color_hex }} !important"></label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                        <div class="filter-price filter-content">
                            <p>
                                <label for="amount">Price range</label>
                                <div class="price">
                                    <select id="sort-by-price" data-sortPrice="true" onchange="ChangeUrl(this)">
                                        <option value="sprice=all">tampilkan semua</option>
                                        <option value="sprice=0-100" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "0-100") ? "selected" : ""; ?>>Under 100K</option>
                                        <option value="sprice=100-149" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "100-149") ? "selected" : ""; ?>>100 - 149K</option>
                                        <option value="sprice=150-199" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "150-199") ? "selected" : ""; ?>>150 - 199K</option>
                                        <option value="sprice=200-249" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "200-249") ? "selected" : ""; ?>>200 - 249K</option>
                                        <option value="sprice=250-349" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "250-349") ? "selected" : ""; ?>>250 - 349K</option>
                                        <option value="sprice=350-449" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "350-449") ? "selected" : ""; ?>>350 - 449K</option>
                                        <option value="sprice=450-999" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "450-999") ? "selected" : ""; ?>>450 - 999K</option>
                                        <option value="sprice=1000-above" <?php echo (isset($_GET["sprice"]) && $_GET["sprice"] == "1000-above") ? "selected" : ""; ?>>1000K above</option>
                                    </select>
                                    <i class="fa fa-angle-down"></i>
                                </div>
                                <!--<input type="text" id="low-amount" readonly value="Rp. 0k">
                                <input type="text" id="high-amount" readonly value="Rp. 6000K">-->
                            </p>
                            <!-- <div id="slider-range"></div> -->
                        </div>
                        <div class="filter-size filter-content">
                            <p>Size</p>
                            <ul id="ul-size">
                                @foreach($size as $row)
                                    <?php $product_size_url_var = isset($row->product_size_url) ? $row->product_size_url : ''; ?>
                                    @if($product_size_url_var != '')
                                    <li>
                                        <div>
                                            <input 
                                                type="checkbox" 
                                                id="size-{{ $row->product_size }}" 
                                                class="size-filter {{ $row->product_size }}" 
                                                onclick="ChangeUrl(this)" 
                                                data-size="true"
                                                value="{{ $row->product_size_url }}"
                                                @if(!empty(findUriSegment('size')))
                                                    {{--*/ $get_key = array_search($row->product_size_url, findUriSegment('size')) /*--}}
                                                    @if($row->product_size_url == findUriSegment('size')[$get_key])
                                                    checked="checked"
                                                    @endif
                                                @endif
                                            >
                                            <label for="size-{{ $row->product_size }}">{{ $row->product_size }}</label>
                                        </div>
                                    </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        <div class="filter-category filter-content">
                            <p>Brands</p>
                            <div class="filter-scroll scroll">
                                <div class="search-brand">
                                    <input type="text" placeholder="Search Brand" id="search_brand" class="text-input">
                                    <button type="submit" class="btn-brand"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="brand-scroll">
                                    <ul id="ul-brand">
                                        @foreach($brand as $row)
                                        {{--*/ 
                                            $brand_url = '';
                                            if(!empty($row->brand_url)) {
                                                $brand_url = $row->brand_url;
                                            } else {
                                                $brand_url = '';
                                            }
                                        /*--}}
                                        <li>
                                            <input 
                                                type="checkbox" 
                                                id="checkbox-category-{{ $brand_url }}" 
                                                class="category-filter brand-filter" 
                                                onclick="ChangeUrl(this)" 
                                                data-brand="true" 
                                                value="{{ $brand_url }}"
                                                @if(!empty(findUriSegment('brand')))
                                                    {{--*/ $get_key = array_search($brand_url, findUriSegment('brand')) /*--}}
                                                    @if($brand_url == findUriSegment('brand')[$get_key])
                                                    checked="checked"
                                                    @endif
                                                @endif                                        
                                            >
                                            <label for="checkbox-category-{{ $brand_url }}">{{ $row->brand_name }}</label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="catalog-list-wrapper right grid">
                <input type="hidden" name="customer_id" id="userauth" value="{{ (Auth::guest()) ? "undefined" : Auth::user()->customer_id }}">
                
                <ul id="ul-catalog">
                @if (empty($catalog))
                <div class="pnf-wrapper">
                    <div class="pnf-content">
                        <h1>Kami Mohon Maaf,<br>Produk Yang Anda Cari Tidak Ditemukan.</h1>
                        <p>Lihat koleksi terbaru kami <a href="/new-arrival">disini</a></p>
                    </div>
                </div>
                @endif
                <?php $index = 1 ?>
                @foreach ($catalog as $row)
                    <?php                         
                        if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
                            ?>
                            <li id="li-catalog">
                                <a href="{{ $row->FullURLBanner }}" onclick="onBannerCatalogClick('{{ $row->template_title}}', '{{ $row->image_banner_name }}');">
                                      <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/catalog-banner/{{ $row->path_image }}" original="{{ $row->path_image }}">
                                </a>
                                <div class="catalog-detail">
                                    <a href="{{ $row->FullURLBanner }}" onclick="onBannerCatalogClick('{{ $row->template_title}}', '{{ $row->image_banner_name }}');">
                                        <h2 class="catalog-brand">{{ $row->text_1 }}</h2>
                                        <h1 class="catalog-name">{{ $row->text_2 }}</h1>
                                        <h1 class="catalog-name">{{ $row->text_3 }}</h1>
                                    </a>
                                </div>
                            </li>
                            @continue   
                            <?php    
                        }
                    
                        $url_arr = explode(',', $row->url_set);
                        $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                        $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                        $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'?'.$page_ref['trc_sale'].'');
						
                        ?>
                        <li id="li-catalog">
                        <?php 
                        //cek wishlist
                        $set_active_wishlist = '';
                        $style = '';
                        if ($wishlist_user <> NULL && in_array((int) $row->pid, $wishlist_user)) {
                            //$style = 'style=background:#333;';
                            $set_active_wishlist = ' heart-red';                            
                        }
                        ?>
			<a href="{{ $url }}" class="catalog-img" onClick="onProductClick({{ $row->pid }}, '{{ $row->product_name }}', '{{ $row->url_set }}', '{{ $row->brand_name }}', '', {{ $index }}, '{{ $ref }}', '{{ $url }}')">
                            
                            <?php
                            $set_display_limited_item_category_sd = isset($row->set_display_limited_item_category_sd) ? $row->set_display_limited_item_category_sd : '';
                            $set_display_limited_item_minimal_sd = isset($row->set_display_limited_item_minimal_sd) ? $row->set_display_limited_item_minimal_sd : 0;
                            $inventory = isset($row->inventory) ? $row->inventory : '';

                            $imageoverlay = '';
                            if(isset($row->product_overlay_image)){
                                $imageoverlay = '<img src="http://img.berrybenka.biz/assets/cache/300x456/product-overlay/'. $row->product_overlay_image .'" alt="" class="overlay-product" />';
                            }
                            
                            ?>                                                        
                            
<!--                            @if($row->discount > 0)
                            <div class="disc-flag">
                                <div class="disc-wrap">{{ $row->discount }}%</div>
                                <div class="triangle-topleft left"></div>
                                <div class="triangle-topright right"></div>
                                <div class="clear"></div>
                            </div>
                            @endif-->
                            {!! $imageoverlay !!}
                            <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">
                            
                            @if (isset($row->set_display_limited_item_set_sd) && $row->set_display_limited_item_set_sd == 1 && (int) $inventory > 0 && $row->product_status <> 2)
                                @if(! stristr($set_display_limited_item_category_sd, $row->type_url) === FALSE && (int) $inventory < $set_display_limited_item_minimal_sd)
                                    <div class="sold-wrapper2">
                                        <div class="sold-tags3">Persediaan Terbatas</div>
                                    </div>
                                @endif
                            @endif
                            
                            @if(isset($row->set_display_oos_set_sd) && $row->set_display_oos_set_sd == 1  && ($row->product_status == 2 || (int) $inventory == 0))
                                <?php
                                    $set_display_oos_category_sd = isset($row->set_display_oos_category_sd) ? $row->set_display_oos_category_sd : '';
                                ?>
                                @if(! stristr($set_display_oos_category_sd, $row->type_url) === FALSE )
                                    <div class="sold-wrapper2">
                                        <div class="sold-tags3">Habis Terjual</div>
                                    </div>
                                @endif
                            @endif
                        </a>
                        <div class="catalog-detail  text-left atw-wrapper">
                            <a href="{{ $url }}" class="title-anchor">
                                <h1 class="catalog-brand">{{ ucfirst($row->brand_name) }}</h1>
                                <h2 class="catalog-name">{{ ucfirst($row->product_name) }}</h2>
                                @if(isset($row->product_sale_price) && $row->product_sale_price <> 0 && $row->product_sale_price <> '')
                                    <p class="catalog-price disc-price"><span>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</span>IDR {{ number_format(($row->product_sale_price), 0, '.', '.') }}</p>
                                @else
                                    <p class="catalog-price">IDR {{ number_format(($row->product_price), 0, '.', '.') }}</p>
                                @endif
                                @if($row->discount > 0)
                                    <div class="disc-tags-bot" style="margin-top:5px;">{{ $row->discount }}%</div>
                                @endif 
                            </a>
                            <a id="add2wish-{{ $row->pid }}" rel="{{ $row->pid }}" onclick="set_wishlist(this)" href="#">                                
                                <i class="fa fa-heart-o atw2 {{ $set_active_wishlist }}" id="wish_{{ $row->pid }}"></i>
                            </a>
                        </div>
                    </li>
                    <?php $index ++ ?>
				@endforeach
                </ul>

            </div>
            </div>
            <div class="clear"></div>
            <div class="pagination right">
            	<ul id="ul-pagination">
                    {!! paginate_page($page_num, $total_catalog) !!}
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

</script>

<!-- Js page -->
<script src="{{ asset('js/desktop/product.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('js/desktop/wishlist.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag')

@if(getMarketingEnv() == true)
    @include('marketing-tag.shopdeca.desktop.category-page')
@endif

@endsection
<?php \Log::info('Time Elapsed Product View '.$title.': '.(microtime(true) - $time).'s'); ?>
