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
    <input type="hidden" class='keyword-search' value="{!! $title !!}">
    <input type="hidden" class='skeyword' value="{!! $skeyword !!}">
    <input type="hidden" class='page-url' value="@if($start_catalog != 0)/{{ $start_catalog }}@endif">
	<div class="catalog-list">
		<div class="wrapper">
        	<div class="content-header">
                <h1 class="catalog-title">{!! $title !!}</h1>
                <div class="search-sortby">
                    <select id="sort-by" data-sort="true" onchange="getSearchData(this)">
                        <option value="" disabled="">Urutkan</option>
                        <?php 
                        $default_filter = '';
                        if(!isset($_GET["price"]) && !isset($_GET["pn"]) && !isset($_GET["discount"]) && !isset($_GET["popular"])){
                            $default_filter = 'selected';
                        }
                        ?>
                        <option value="price=asc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "asc") ? "selected" : ""; ?>>Harga Terendah</option>
                        <option value="price=desc" <?php echo (isset($_GET["price"]) && $_GET["price"] == "desc") ? "selected" : ""; ?>>Harga Tertinggi</option>
                        <option value="pn=desc" <?php echo (isset($_GET["pn"]) && $_GET["pn"] == "desc") ? "selected" : ""; ?>>Produk Terbaru</option>
                        <option value="discount=desc" <?php echo (isset($_GET["discount"]) && $_GET["discount"] == "desc") ? "selected" : ""; ?>>Diskon Terbesar</option>
                        <option value="popular=desc" <?php echo (isset($_GET["popular"]) && $_GET["popular"] == "desc") ? "selected" : $default_filter; ?>>Populer</option>
                    </select>
                    <i class="fa fa-angle-down"></i>
                </div>
                <div class="clear"></div>
            </div>
            <div class="search-catalog-wrapper">
                <div class="catalog-list-wrapper right grid">
                @if($total_catalog > 0)
                    <ul id="ul-catalog">
                    @foreach ($catalog as $row)
                        <?php 
                            $url_arr = explode(',', $row->url_set);
                            $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                            $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                            $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'?trc_sale=search-solr');
                        ?>
    					 <li id="li-catalog">
                            <a href="{{ $url }}" class="catalog-img">
                                @if($row->discount > 0)
                                <div class="disc-flag">
                                    <div class="disc-wrap">{{ $row->discount }}%</div>
                                    <div class="triangle-topleft left"></div>
                                    <div class="triangle-topright right"></div>
                                    <div class="clear"></div>
                                </div>
                                @endif
                                <?php /*
                                <div class="add2wish">
                                    <i class="fa fa-heart" style="color:#000 !important">
                                        <div class="b-tips">
                                            Add to Wishlist
                                        </div>
                                    </i>
                                </div> */?>
                                <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">
                                
                                @if (isset($row->set_display_limited_item_set) && $row->set_display_limited_item_set == 1 && (int) $row->inventory > 0 && $row->product_status <> 2)
                                    <?php
                                        $set_display_limited_item_category = isset($row->set_display_limited_item_category) ? $row->set_display_limited_item_category : '';
                                        $set_display_limited_item_minimal = isset($row->set_display_limited_item_minimal) ? $row->set_display_limited_item_minimal : 0;
                                    ?>
                                    @if(! stristr($set_display_limited_item_category, $row->type_url) === FALSE && (int) $row->inventory < $set_display_limited_item_minimal)
                                        <div class="limited-qty">Persediaan Terbatas</div>
                                    @endif
                                @endif
                                
                                @if(isset($row->set_display_oos_set) && $row->set_display_oos_set == 1  && ($row->product_status == 2 || (int) $row->inventory == 0))
                                    <?php
                                        $set_display_oos_category = isset($row->set_display_oos_category) ? $row->set_display_oos_category : '';
                                    ?>
                                    @if(! stristr($row->set_display_oos_category, $row->type_url) === FALSE )
                                        <div class="limited-qty">Persediaan Habis</div>
                                    @endif
                                @endif
                            </a>
                            <div class="catalog-detail">
                                <a href="{{ $url }}">
                                    <h1 class="catalog-name">{{ ucfirst($row->product_name) }}</h1>
                                    <h2 class="catalog-brand">{{ ucfirst($row->brand_name) }}</h2>
                                    @if(isset($row->product_sale_price) && $row->product_sale_price <> 0 && $row->product_sale_price <> '')
                                        <p class="catalog-price disc-price"><span>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</span>IDR {{ number_format(($row->product_sale_price), 0, '.', '.') }}</p>
                                    @else
                                        <p class="catalog-price">IDR {{ number_format(($row->product_price), 0, '.', '.') }}</p>
                                    @endif
                                </a>
                            </div>
                        </li>
    				@endforeach
                    </ul>
                @else
                    <div class="pnf-wrapper">
                        <div class="pnf-content">
                            <h1>Kami Mohon Maaf,<br>Produk Yang Anda Cari Tidak Ditemukan.</h1>
                            <p>Lihat koleksi terbaru kami <a href="/new-arrival">disini</a></p>
                        </div>
                    </div>
                    <br/>
                @endif
                </div>
            </div>
            <div class="clear"></div>
            <div class="search-pagination right">
            	<ul id="ul-pagination">
                    {!! paginate_page($start_catalog, $total_catalog, 'onclick="getSearchData(this)"') !!}
                </ul>
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
<script src="{{ asset('js/desktop/search.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.shopdeca.desktop.search')
    @endif
@endsection