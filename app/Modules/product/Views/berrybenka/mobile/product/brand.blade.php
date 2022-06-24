@extends('layouts.berrybenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/catalog.css?t=').date('YmdHis') }}">
@endsection

@section('filter')

<div class="filter-list-outer" id="filter-list" style="display: none; z-index: 100001;">
<h1><i class="fa fa-search" aria-hidden="true"></i>Filter<i class="fa fa-times" aria-hidden="true"></i></h1>
	<div class="filter-list" id="sticky-list">
		{!! Form::open(['class' => 'form-horizontal', 'files' => true, 'id' => 'filter-form', 'methode' => 'GET']) !!}
    	<ul>
        	<li class="has-sub filter-kategori">
        		<a href="">Kategori<div class="display"></div></a>
            <ul>
            	@foreach($category as $row)
            	<li>
                	<input type="radio" name="categories" value="{{ $row->type_name_bahasa }}|{{ $row->type_url }}" id="RadioGroup-{{ $row->type_url }}">
                    <label for="RadioGroup-{{ $row->type_url }}">{{ $row->type_name_bahasa }}</label>
                </li>
                @endforeach
            </ul>
            </li>
            <li class="has-sub filter-color"><a href="">Warna<div class="display"></div></a>
            <ul id="ul-color">
            	@foreach($color as $row)
            	<li>
                	<input type="checkbox" name="colors" value="{{ $row->color_name }}" id="RadioGroup-{{ $row->color_name }}" data-url="{{ $row->color_name }}">
                    <label class="color-merah" for="RadioGroup-{{ $row->color_name }}">{{ $row->color_name }}<span style="background-color:#{{ $row->color_hex }}"></span></label>
                </li>
                @endforeach
            </ul>
            </li>
            <li class="filter-size">
            <select id="sizes">
            	<option disabled="disabled" selected="selected" name="size">Ukuran</option>
            	@foreach($size as $row)
        		<option value="{{ $row->product_size_url }}">{{ $row->product_size }}</option>
            	@endforeach
            </select></li>
        </ul>
        <input type="submit" class="apply-filter" value="Terapkan Filter" id="submit-filter">
        {!! Form::close() !!}
    </div>
</div>

@endsection

@section('content')

<div class="filter-tab clear">
	<ul>
    	<li><a href="#" class="filter-link">FILTER <i class="fa fa-filter" aria-hidden="true"></i></a></li>
        <li>
            <select id="sort">
                <option value="" disabled="disabled">URUTKAN</option>
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
		</li>
    </ul>
</div>

<div class="content-catalog">
	<div class="catalog-list clear">
        
    	<ul>
            @if (empty($catalog))
                <div class="pnf-wrapper">
                    <div class="pnf-content">
                        <h1>Kami Mohon Maaf,<br>Produk Yang Anda Cari Tidak Ditemukan.</h1>
                        <p>Lihat koleksi terbaru kami <a href="/new-arrival">disini</a></p>
                    </div>
                </div>
            @endif
            
            @foreach ($catalog as $row)
            <?php
            if (isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE) {
                ?>
                <li>
                    <a href="{{ $row->FullURLBanner }}">
                        <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/catalog-banner/{{ $row->path_image }}" original="{{ $row->path_image }}">

                        <div class="prod-title-list">
                            <h1>{{ $row->text_1 }}</h1>
                            <h2>{{ $row->text_2 }}</h2>
                            <h3>{{ $row->text_3 }}</h3>
                        </div>
                    </a>      
                </li>
                @continue    
                <?php
            }

            $url_arr = explode(',', $row->url_set);
            $parent = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
            $child = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
            $url = url('' . $parent . '/' . $child . '/' . $row->pid . '/' . str_slug($row->product_name, '-') . '?spc_case=' . Request::segment(1) . '&spc_num=' . $row->brand_id . '');
            ?>
            <li>
                <a href="{{ $url }}">
                    <div class="catalog-img">
                        <?php
                        $set_display_limited_item_category_bb   = isset($row->set_display_limited_item_category_bb) ? $row->set_display_limited_item_category_bb : '';
                        $set_display_limited_item_minimal_bb    = isset($row->set_display_limited_item_minimal_bb) ? $row->set_display_limited_item_minimal_bb : 0;
                        $set_display_oos_category_bb            = isset($row->set_display_oos_category_bb) ? $row->set_display_oos_category_bb : '';
                        $inventory                              = isset($row->inventory) ? $row->inventory : '';
                        
                        $imageoverlay = '';
                        if(isset($row->product_overlay_image)){
                            $imageoverlay = '<img src="http://img.berrybenka.biz/assets/cache/238x358/product-overlay/'. $row->product_overlay_image .'" alt="" class="overlay-product" />';
                        }
                        ?>
                        {!! $imageoverlay !!}
                        <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $row->image_name }}">
                        @if (isset($row->set_display_limited_item_set_bb) && $row->set_display_limited_item_set_bb == 1 && (int) $inventory > 0 && $row->product_status <> 2)
                            @if(! stristr($set_display_limited_item_category_bb, $row->type_url) === FALSE && (int) $inventory < $set_display_limited_item_minimal_bb)                                                                                                    
                            <div class="sold-wrapper2">
                                <div class="sold-tags3">Persediaan Terbatas</div>
                            </div>
                            @endif
                        @endif

                        @if(isset($row->set_display_oos_set_bb) && $row->set_display_oos_set_bb == 1  && ($row->product_status == 2 || (int) $inventory == 0))
                            @if(! stristr($set_display_oos_category_bb, $row->type_url) === FALSE )                                    
                                <div class="sold-wrapper2">
                                    <div class="sold-tags3">Habis Terjual</div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="prod-title-list">
                        {{-- <h1> {{ ucfirst($row->brand_name) }} </h1> --}}
                        <h2> {{ ucfirst($row->product_name) }} </h2>
                        
                        @if(isset($row->product_sale_price) && $row->product_sale_price <> 0 && $row->product_sale_price <> '')
                            <h3 class="disc-price"><span>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</span> IDR {{ number_format(($row->product_sale_price), 0, '.', '.') }}</h3>
                        @else
                            <h3>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</h3>
                        @endif
                        
                        @if(isset($row->discount) && $row->discount > 0)
                            <h4 class="discount-tag">{{ $row->discount }}%</h4>
                        @endif  
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="pagination clear">
        <ul>
            {!! paginate_page($start_catalog, $total_catalog, 'onclick="paginate(this)"') !!}
        </ul>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/mobile/app.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('js/mobile/promo-mobile.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php $user = \Auth::user(); ?>

var mydata336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    product_data : [
        @foreach($catalog as $row)
        <?php
        if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
            ?>                    
            @continue    
            <?php    
            }
        ?>
        '{{ $row->pid }}',
        @endforeach
    ]
  }

</script>
@if(getMarketingEnv() == true)
    @include('marketing-tag.berrybenka.mobile.category-page', ['catalog' => $catalog])
@endif

@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.berrybenka.mobile.brand')
    @endif
@endsection