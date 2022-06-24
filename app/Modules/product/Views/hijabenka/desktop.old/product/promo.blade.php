@extends('layouts.hijabenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/catalog-list.css?t=').date('YmdHis') }}">
@endsection

@section('content')
<div class="content">
    <!-- Loading div -->
    <div id="loading">
        <div class="load-icon">
            <img src="{{ asset('hijabenka/desktop/img/bb-loading.gif') }}">
        </div>
    </div>
    <!-- ******** -->
    <input type="hidden" class='input-url' value="">
    <input type="hidden" class='input-size-url' value="">
    <input type="hidden" class='input-brand-url' value="">
    <input type="hidden" class='sort-url' value="">
    <input type="hidden" class='price-url' value="">
    <input type="hidden" class='page-url' value="">

    <!-- TOP MINI BANNER -->
    @if (!empty($top_banner_mini) && count($top_banner_mini) > 0):
        <div class="full-width bg-sticky text-center">
            <div class="container">
                {!! $top_banner_mini->content !!}
            </div>
        </div>
    @endif
    <!-- END TOP MINI BANNER -->

    @if (is_null($special) && is_null($special_image) && empty($special_image) && count($special_image) < 1 && $special->special_page_type != 4):
        <?php $_utm_campaign_promo = app('request')->input('utm_campaign'); ?>
        
        @if (!isset($special->utm_campaign) && $_utm_campaign_promo != $special->utm_campaign):
        <?php
            $banner     = isset($special->special_page_image_banner) ? $special->special_page_image_banner : '';
            $html_code  = isset($special->html_code) ? $special->html_code : '';
        ?>
            <div class="full-width">
                <div class="banner-image" id="banner-img" style="text-align: center;">
                    <a href="#"><img src="{{ IMAGE_SPECIAL_PAGE_UPLOAD_PATH }}/banner/{{ $banner }}"></a>
                </div>
            </div>
        @endif
        <div class="full-width">
            {!! $html_code !!}
        </div>
    @endif

    @if (! is_null($special) && !empty($special_image) && count($special_image) > 1 && $special->special_page_type == 4):
        <!-- PRODUCT CATALOG LIST -->
        <div class="sixteen columns alpha omega mb20">
            <div class="lookbook-slider-cont">
                <div class="slide-nav">
                    <span class="lookbook-prev"><i class="fa fa-angle-left"></i></span>
                    <span class="lookbook-next"><i class="fa fa-angle-right"></i></span>
                </div>
                <div id="lookbook-slider" class="owl-carousel">
                    @foreach($special_image as $spsi):
                    <div class="item">
                        <a href="#"><img class="lazyOwl" data-src="{{ IMAGE_SPECIAL_PAGE_UPLOAD_PATH }}/slider/{{ $spsi->special_page_slider_images }}"></a>
                    </div>
                    @endforeach 
                </div>
            </div>
        </div>
    @else 
        <?php
            $utm_trigger  = app('request')->input('utm_trigger');
            $get_sukses   = app('request')->input('sukses');
            $get_error    = app('request')->input('error');
            $email_trigger = isset($special->email_trigger) ? $special->email_trigger : '';
        ?>

        @if (($email_trigger <> ''  || $email_trigger <> NULL) && $utm_trigger == $email_trigger):
            @if($get_error == 1):
                <div class="alert alert-danger" id="error-size-back" style="">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                    <i class="fa fa-info-circle"></i> Maaf data anda gagal kami simpan. Silakan ulangi kembali.
                </div>
            @endif
            
            @if ($get_sukses == 1):
                <div id="success-back" class="alert alert-success" >
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="fa fa-check-circle"></i> Terima Kasih Atas Email Anda.
                </div>
            @endif
        
            {!! $special->email_code !!}
        @else
            <div class="span12">
              <div class="banner-image" id="banner-img" {!! (\Request::get('page') != null && \Request::get('page') > 0) ? "style='display:none;'" : "" !!} >
                   <a href="#"><img src="{{ IMAGE_SPECIAL_PAGE_UPLOAD_PATH }}/banner/{{ $special['special_page_image_banner'] }}"></a>
              </div>
            </div>

            <div id="banner-html" {!! (\Request::get('page') != null && \Request::get('page') > 0) ? "style='display:none;'" : "" !!} >
                {!! $special->html_code !!}
            </div>
        @endif
    @endif
    
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

                <h1 class="catalog-title" style="text-transform:none!important">{{ $title }}</h1>
                <div class="sortby">
                    <select id="sort-by" data-sort="true" onchange="ChangeUrl(this)">
                        <option value="" disabled="">Urutkan</option>
                        <?php 
                        $default_filter = '';
                        if(!isset($_GET["price"]) && !isset($_GET["pn"]) && !isset($_GET["discount"]) && !isset($_GET["popular"]) && !isset($_GET["recommended"]) && !isset($_GET["recommended"])){
                            $default_filter = 'selected';
                        }
                        ?>
                        <option value="recommended=asc" <?php echo (isset($_GET["recommended"]) && $_GET["recommended"] == "asc") ? "selected" : $default_filter; ?>>Rekomendasi</option>
                        <option value="popular=desc" <?php echo (isset($_GET["popular"]) && $_GET["popular"] == "desc") ? "selected" : ""; ?>>Populer</option>  
                        <option value="pn=desc" <?php echo (isset($_GET["pn"]) && $_GET["pn"] == "desc") ? "selected" : ""; ?>>Produk Terbaru</option>
                        <option value="discount=desc" <?php echo (isset($_GET["discount"]) && $_GET["discount"] == "desc") ? "selected" : ""; ?>>Diskon Terbesar</option>                        
                        <option value="price=asc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "asc") ? "selected" : ""; ?>>Harga Terendah</option>
                        <option value="price=desc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "desc") ? "selected" : ""; ?>>Harga Tertinggi</option>                                 
                    </select>
                    <i class="fa fa-angle-down"></i>
                </div>
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
                                <input type="radio" id="radio-gender-pria" data-gender="gender=men" onclick="ChangeUrl(this)" @if(isset($gender) && $gender == 'men') checked="checked" @endif name="gender">
                                <label for="radio-gender-pria" class="clearfix">Pria</label>
                            </li>
                            <li>
                                <input type="radio" id="radio-gender-wanita" data-gender="gender=women" onclick="ChangeUrl(this)" @if(isset($gender) && $gender == 'women') checked="checked" @endif name="gender">
                                <label for="radio-gender-wanita" class="clearfix">Wanita</label>
                            </li>
                        </ul>
                    </div>
                    <div class="filter-category filter-content">
                        <p>Kategori</p>
                        <div class="filter-scroll">
                        <ul id="ul-category">
                        	@if(isset($category))
                            @foreach ($category as $key => $values)
                                <!--<ol>{{ $values->type_name_bahasa }}</ol>-->
                                    <li>
                                        <input 
                                            type="radio" 
                                            id="checkbox-category-{{ $values->type_url }}" 
                                            class="category-filter parentCheckBox" 
                                            data-level="parent" 
                                            name="category_level1" 
                                            data-url="cat={{ $values->type_url }}" 
                                            onclick="ChangeUrl(this)"
                                            @if(!empty(findUriKey('cat')))
                                                {{--*/ $get_key = array_search($values->type_url, findUriKey('cat')) /*--}}
                                                @if($values->type_url == findUriKey('cat')[$get_key])
                                                checked="checked"
                                                @endif
                                                @if(!empty($values->child))
                                                  @foreach ( $values->child as $key2 => $value2 )
                                                    @if(!empty(findUriKey('cat')))
                                                        {{--*/ $get_key = array_search($value2->type_url, findUriKey('cat')) /*--}}
                                                        @if($value2->type_url == findUriKey('cat')[$get_key])
                                                          checked="checked"
                                                        @endif
                                                    @endif
                                                  @endforeach
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
                                                    class="category-filter parentCheckBox" 
                                                    data-level="parent" 
                                                    name="category_level2" 
                                                    data-url="cat={{ $value2->type_url }}" 
                                                    onclick="ChangeUrl(this)"
                                                    @if(!empty(findUriKey('cat')))
                                                        {{--*/ $get_key = array_search($values->type_url, findUriKey('cat')) /*--}}
                                                        @if($value2->type_url == findUriKey('cat')[$get_key])
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
                            @endif
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
                                    @if(!empty(findUriKey('color')))
                                        {{--*/ $get_key = array_search($row->color_name, findUriKey('color')) /*--}}

                                        @if($row->color_name == findUriKey('color')[$get_key])
                                        checked="checked"
                                        @endif
                                    @endif
                                >
                                <label for="checkbox-{{ $row->color_name }}" style="background-color:#{{ $row->color_hex }} !important">
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
                        </div>
                        <div class="filter-size filter-content">
                            <input type="hidden" class='input-color-url' value="">
                            <p>Size</p>
                            <ul id="ul-size">
                                @foreach($size as $row)
                                <li>
                                    <div>
                                        <input 
                                            type="checkbox" 
                                            id="size-{{ $row->product_size }}" 
                                            class="size-filter {{ $row->product_size }}" 
                                            onclick="ChangeUrl(this)" 
                                            data-size="true" 
                                            value="{{ $row->product_size_url }}"
                                            @if(!empty(findUriKey('size')))
                                                {{--*/ $get_key = array_search($row->product_size_url, findUriKey('size', '-')) /*--}}

                                                @if($row->product_size_url == findUriKey('size', '-')[$get_key])
                                                checked="checked"
                                                @endif
                                            @endif
                                        >
                                        <label for="size-{{ $row->product_size }}">{{ $row->product_size }}</label>
                                    </div>
                                </li>
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
                                        <?php 
                                            $brand_url = '';
                                            if(!empty($row->brand_url)) {
                                                $brand_url = $row->brand_url;
                                            }
                                        ?>
                                        <li>
                                            <input type="checkbox" id="checkbox-category-{{ $brand_url }}" class="category-filter" onclick="ChangeUrl(this)" data-brand="true" value="{{ $brand_url }}">
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
                @if (empty($catalog))
                <div class="pnf-wrapper">
                    <div class="pnf-content">
                        <h1>Kami Mohon Maaf,<br>Produk Yang Anda Cari Tidak Ditemukan.</h1>
                        <p>Lihat koleksi terbaru kami <a href="/new-arrival">disini</a></p>
                    </div>
                </div>
                @endif
                <ul id="ul-catalog">
                @foreach ($catalog as $row)
                    <?php 
                        if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
                            ?>
                            <li id="li-catalog">
                                <a href="{{ $row->FullURLBanner }}">
                                      <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/catalog-banner/{{ $row->path_image }}" original="{{ $row->path_image }}">
                                </a>
                                <div class="catalog-detail">
                                    <a href="{{ $row->FullURLBanner }}">
                                        <h2 class="catalog-brand">{{ $row->text_1 }}</h2>
                                        <h1 class="catalog-name">{{ $row->text_2 }}</h1>
                                        <p class="catalog-price">{{ $row->text_3 }}</p>
                                    </a>
                                </div>
                            </li>
                            @continue   
                            <?php    
                        }
                        $url_arr = explode(',', $row->url_set);
                        $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                        $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                        $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'?spc_case='.Request::segment(1).'&spc_num='.Request::segment(2).'');
						
                    ?>
					 <li id="li-catalog">
                        <a href="{{ $url }}" class="catalog-img">        
                            
                            <?php
                            $imageoverlay = '';
                            if(isset($row->product_overlay_image)){
                                $imageoverlay = '<img src="http://img.berrybenka.biz/assets/cache/300x456/product-overlay/'. $row->product_overlay_image .'" alt="" class="overlay-product" />';
                            }
                            ?>
                            {!! $imageoverlay !!}
                            <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">
                            @if (isset($row->set_display_limited_item_set_hb) && $row->set_display_limited_item_set_hb == 1 && (int) $row->inventory > 0 && $row->product_status <> 2)
                                <?php
                                    $set_display_limited_item_category_hb = isset($row->set_display_limited_item_category_hb) ? $row->set_display_limited_item_category_hb : '';
                                    $set_display_limited_item_minimal_hb = isset($row->set_display_limited_item_minimal_hb) ? $row->set_display_limited_item_minimal_hb : 0;
                                ?>
                                @if(! stristr($set_display_limited_item_category_hb, $row->type_url) === FALSE && (int) $row->inventory < $set_display_limited_item_minimal_hb)
                                    <div class="sold-wrapper2">
                                        <div class="sold-tags3">Persediaan Terbatas</div>
                                    </div>
                                @endif
                            @endif
                                                        
                            <?php                            
                            $special_page_oos             = isset($special->special_page_oos) ? $special->special_page_oos : 0;
                            //$special_page_oos_category    = isset($special->special_page_oos_category) ? array_filter(explode(',', $special->special_page_oos_category)) : [];
                            ?>                            
                            @if($special_page_oos == 1 && ($row->product_status == 2 || (int) $row->inventory == 0))
                                <div class="sold-wrapper2">
                                    <div class="sold-tags3">Habis Terjual</div>
                                </div>
                            @endif                            
                        </a>
                        <div class="catalog-detail text-left atw-wrapper">
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
                        </div>
                    </li>
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
<script src="{{ asset('js/desktop/promo.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.hijabenka.desktop.promo-body')        
    @endif
@endsection

@section('marketing-tag')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.hijabenka.desktop.promo-page')        
    @endif
@endsection
