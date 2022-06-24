@extends('layouts.berrybenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/search.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/catalog.css?t=').date('YmdHis') }}">
@endsection

@section('content')

<div class="filter-tab-search clear">
    <input type="hidden" class='keyword-search' value="{!! ucwords(urldecode(str_replace('+', ' ', $title))) !!}">
	<ul>
    	<li>
            <select name="searchData" id="searchData" data-sort="true" onchange="getSearchData(this)">
                <option value="" disabled="disabled">URUTKAN</option>
                <?php 
                $default_filter = '';
                if(!isset($_GET["price"]) && !isset($_GET["pn"]) && !isset($_GET["discount"]) && !isset($_GET["popular"])){
                    $default_filter = 'selected';
                }
                ?>
                <option value="price=desc" <?php echo (session('sort') == 'real_price+desc') ? 'selected="selected"' : ''; ?>>Harga Mahal ke Murah</option>
                <option value="price=asc" <?php echo (session('sort') == 'real_price+asc') ? 'selected="selected"' : ''; ?>>Harga Murah ke Mahal</option>
                <option value="pn=desc" <?php echo (session('sort') == 'launch_date_bb+desc') ? 'selected="selected"' : ''; ?>>Terbaru</option>
                <option value="discount=desc" <?php echo (session('sort') == 'discount+desc') ? 'selected="selected"' : ''; ?>>Diskon Terbesar</option>
                <option value="popular=desc" <?php echo (session('sort') == 'total_series_score+desc%2Cproduct_scoring+desc') ? 'selected="selected"' : $default_filter; ?>>Populer</option>
          </select>
        <i class="fa fa-sort"></i>
		</li>
    </ul>
    
</div>
    <div class="process-steps">
        Search "{{ $title }}"
    </div>
<div class="content-catalog">
	<div class="catalog-list clear">
    @if($total_catalog > 0)
    	<ul>
    		@foreach ($catalog as $row)
                <?php 
                    $url_arr = explode(',', $row->url_set);
                    $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                    $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                    $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'');
                ?>
        	<li>
            	<a href="{{ $url }}">
                    <div class="catalog-img">
                        <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $row->image_name }}"> 
                        <?php
                        $set_display_limited_item_category_bb   = isset($row->set_display_limited_item_category_bb) ? $row->set_display_limited_item_category_bb : '';
                        $set_display_limited_item_minimal_bb    = isset($row->set_display_limited_item_minimal_bb) ? $row->set_display_limited_item_minimal_bb : 0;
                        $set_display_oos_category_bb            = isset($row->set_display_oos_category_bb) ? $row->set_display_oos_category_bb : '';
                        $inventory                              = isset($row->inventory) ? $row->inventory : '';
                        ?>
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
    @else
        <div class="pnf-wrapper">
            <div class="pnf-content" style="text-align:center;">
                <h1>Kami Mohon Maaf,<br>Produk Yang Anda Cari Tidak Ditemukan.</h1>
                <p>Lihat koleksi terbaru kami <a href="/new-arrival" style="color:blue;cursor:pointer;">disini</a></p>
            </div>
        </div>
        <br/>
    @endif
    </div>
    <div class="clear"></div>
    <div class="search-pagination clear">
        <ul>
            {!! paginate_page($start_catalog, $total_catalog, 'onclick="getSearchData(this)"') !!}
        </ul>
    </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/mobile/search-mobile.js') }}"></script>
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.berrybenka.mobile.search', ['catalog' => $catalog])
    @endif
@endsection