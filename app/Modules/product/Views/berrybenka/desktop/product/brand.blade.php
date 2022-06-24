@extends('layouts.berrybenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/catalog.css?t=').date('YmdHis') }}">
@endsection

@section('content')
<div class="catalog-content">

    <input type="hidden" class='input-url'
    @if(!empty(findUriSegment('cat_parent')))
        value="{{ categoryUrl($gender) }}"
    @else
        value=""
    @endif
    >

    <input type="hidden" class='input-url' value="">
    <input type="hidden" class='input-size-url' value="">
    <input type="hidden" class='input-brand-url' value="">
    <input type="hidden" class='sort-url' value="">
    <input type="hidden" class='price-url' value="">
    <input type="hidden" class='page-url' value="">

    <div class="wrapper">
        <div class="small-bc">
            <a href="#" class="filter-trigger">filter</a>
            <h1 class="catalog-title">{!! $title !!}</h1>
        </div>
        <div class="catalog-wrapper">
            <div class="filter show-filter">
                <div class="filter-outer">
                    <div class="filter-wrapper">
                        <div class="sort-wrapper filter-item">
                            <h2>sort by :</h2>
                            <div class="dropdown">
                                <i class="fa fa-angle-down" aria-hidden="true"></i>
                                <select id="sort-by" data-sort="true" onchange="ChangeUrl(this)">
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
                            </div>
                        </div>
                        <?php /*
                        @if (count($gender) > 1)
                        <div class="filter-item gender">
                            <h2>Gender</h2>
                            <ul>
                                @foreach($gender as $row)
                                  <?php 
                                    $gender_name = ($row->product_gender==1)?"women":"men";
                                    $gender_bahasa = ($row->product_gender==1)?"Wanita":"Pria"; 
                                  ?>
                                     <li>
                                        <input type="radio" 
                                        id="checkbox-gender-{{ $gender_name }}"
                                        data-gender="gender={{ $gender_name }}" 
                                        onclick="ChangeUrl(this)" 
                                        name="gender"
                                        @if(isset($get['gender']) && $get['gender'] == $gender_name) checked="checked" @endif
                                        >

                                        <label for="checkbox-gender-{{ $gender_name }}" class="clearfix">{{ $gender_bahasa }}</label>
                                    </li>
                                @endforeach
                                
                            </ul>
                        </div>
                        @endif
                        */ ?>
                        <div class="kategori filter-item">
                            <h2>kategori</h2>
                            <ul id="ul-category">
                                @foreach ($category as $key => $values)
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
                                        <label for="checkbox-category-{{ $values->type_url }}" class="clearfix">{{ $values->type_name_bahasa }}</label>
                                        
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
                                                            data-url="cat={{ $value2->type_url }}"
                                                            @if(!empty(findUriKey('cat')))
                                                              {{--*/ $get_key = array_search($value2->type_url, findUriKey('cat')) /*--}}
                                                              @if($value2->type_url == findUriKey('cat')[$get_key])
                                                                checked="checked"
                                                              @endif
                                                            @endif
                                                        >

                                                        <label for="checkbox-category-{{ $value2->type_url }}" class="clearfix">{{ $value2->type_name_bahasa }}</label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="color filter-item">
                            <h2>Warna</h2>
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
                                        <label for="checkbox-{{ $row->color_name }}" style="background-color:#{{ $row->color_hex }} !important"></label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="filter-item price">
                            <h2>Harga</h2>
                            <ul id="ul-harga">
                                <li>
                                    <input type="checkbox" 
                                    id="checkbox-price1" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=all"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "all" ? 'checked=checked' : ""); ?>
                                    >

                                    <label for="checkbox-price1" class="clearfix">Tampilkan semua</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price2" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=0-100"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "0-100" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price2" class="clearfix">Under 100K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price3" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=100-149"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "100-149" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price3" class="clearfix">100 - 149K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price4" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=150-199"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "150-199" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price4" class="clearfix">150 - 199K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price5" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=200-249"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "200-249" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price5" class="clearfix">200 - 249K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price6" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=250-349"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "250-349" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price6" class="clearfix">250 - 349K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price7" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=350-449"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "350-449" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price7" class="clearfix">350 - 449K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price8" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=450-999"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "450-999" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price8" class="clearfix">450 - 999K</label>
                                </li>
                                <li>
                                    <input 
                                    type="checkbox" 
                                    id="checkbox-price9" 
                                    data-harga="true" 
                                    onclick="ChangeUrl(this)" 
                                    value="sprice=1000-above"
                                    <?php echo (isset($_GET['sprice']) && $_GET['sprice'] == "1000-above" ? 'checked=checked' : ""); ?>
                                    >
                                    <label for="checkbox-price9" class="clearfix">1000K above</label>
                                </li>                                
                            </ul>
                        </div>
                        <div class="filter-item size">
                            <h2>ukuran</h2>
                            <ul id="ul-size">
                                @foreach($size as $row)
                                    <?php $product_size_url_var = isset($row->product_size_url) ? $row->product_size_url : ''; ?>
                                    @if($product_size_url_var != '')
                                        <li>
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
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="customer_id" id="userauth" value="{{ (Auth::guest()) ? "undefined" : Auth::user()->customer_id }}">

            <div class="catalog-list">
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
                                    <a href="{{ $row->FullURLBanner }}">
                                        <div class="catalog-image"><img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/catalog-banner/{{ $row->path_image }}" original="{{ $row->path_image }}"></div>
                                        <div class="catalog-detail">
                                            <div class="detail-left">
                                                <h1>{{ $row->text_2 }}</h1>
                                            </div>
                                            <div class="detail-right">
                                                <p class="discount">{{ $row->text_3 }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>

                                 @continue   
                                <?php    
                            }
                        
                            $url_arr = explode(',', $row->url_set);
                            $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                            $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                            $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'?spc_case='.Request::segment(1).'&spc_num='.$row->brand_id.'');
                        
                        ?>

                    <li id="li-catalog">
                        <?php 
                        //cek wishlist
                        $set_active_wishlist = '';
                        $style = '';
                        // if ($wishlist_user <> NULL && in_array((int) $row->pid, $wishlist_user)) {
                        //     //$style = 'style=background:#333;';
                        //     $set_active_wishlist = ' heart-red';                            
                        // }
                        $imageoverlay = '';
                        if(isset($row->product_overlay_image)){
                            $imageoverlay = '<img src="http://img.berrybenka.biz/assets/cache/300x456/product-overlay/'. $row->product_overlay_image .'" alt="" class="overlay-product" style="position: absolute;" />';
                        }
                        ?>

                        <a href="{{ $url }}">

                            
                            <div class="catalog-image">{!! $imageoverlay !!}<img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">

                            <!-- <div class="sold-wrapper2">
                                <div class="sold-tags3">Persediaan Terbatas</div>
                            </div> -->

                            <?php
                                $set_display_limited_item_category_bb = isset($row->set_display_limited_item_category_bb) ? $row->set_display_limited_item_category_bb : '';
                                $set_display_limited_item_minimal_bb = isset($row->set_display_limited_item_minimal_bb) ? $row->set_display_limited_item_minimal_bb : 0;
                                $inventory = isset($row->inventory) ? $row->inventory : '';                                                                
                            ?>
                            @if (isset($row->set_display_limited_item_set_bb) && $row->set_display_limited_item_set_bb == 1 && (int) $inventory > 0 && $row->product_status <> 2)
                                @if(! stristr($set_display_limited_item_category_bb, $row->type_url) === FALSE && (int) $inventory < $set_display_limited_item_minimal_bb)                                                                                                    
                                    <div class="sold-wrapper2">
                                        <div class="sold-tags3">Persediaan Terbatas</div>
                                    </div>
                                @endif
                            @endif
                            
                            @if(isset($row->set_display_oos_set_bb) && $row->set_display_oos_set_bb == 1  && ($row->product_status == 2 || (int) $inventory == 0))
                                <?php
                                    $set_display_oos_category_bb = isset($row->set_display_oos_category_bb) ? $row->set_display_oos_category_bb : '';
                                ?>
                                @if(! stristr($set_display_oos_category_bb, $row->type_url) === FALSE )                                    
                                    <div class="sold-wrapper2">
                                        <div class="sold-tags3">Habis Terjual</div>
                                    </div>
                                @endif
                            @endif

                            </div>

                            <div class="catalog-detail">
                                <div class="detail-left">
                                    <h1>{{ ucfirst($row->product_name) }}</h1>

                                    @if($row->discount > 0)
                                        <p>{{ $row->discount }}% off</p>
                                    @endif 


                                </div>
                                <div class="detail-right">
                                    @if(isset($row->product_sale_price) && $row->product_sale_price <> 0 && $row->product_sale_price <> '')
                                        <p>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</p>
                                        <p class="discount">IDR {{ number_format(($row->product_sale_price), 0, '.', '.') }}</p>
                                    @else
                                        <p class="discount">IDR {{ number_format(($row->product_price), 0, '.', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </li>
                    <?php $index ++ ?>
                @endforeach
                </ul>
            </div>
        </div>
        <div class="pagination">
            <ul id="ul-pagination">
                {!! paginate_page($start_catalog, $total_catalog) !!}
            </ul>
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
<script src="{{ asset('js/desktop/promo_bb.js?t=').date('YmdHis') }}"></script>
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
    @include('marketing-tag.berrybenka.desktop.category-page')
@endif

@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.berrybenka.desktop.brand')
    @endif
@endsection