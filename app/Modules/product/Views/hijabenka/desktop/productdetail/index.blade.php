@extends('layouts.hijabenka.desktop.main')

@section('meta')
    <link rel="canonical" href="{{ \Request::url() }}" />
    
    <meta property="og:site_name" content="hijabenka.com" />   
    <meta property="og:type" content="article" />
    <meta property="og:url" content="{{ \Request::url() }}" />
        <meta property="og:title" content="{{ $product_name }}" />
        <meta property="og:description" content="{{ $product_description }}" /> 
        <meta property="og:image" content="{{ ASSETS_PATH }}upload/product/zoom/{{ isset($fetch_product_image_def->image_name) ? $fetch_product_image_def->image_name : '' }}" />
        
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="520" />
    <meta property="og:image:height" content="320" />
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/detail.css') }}">
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/pro-rec.css') }}">

<style>
    .msg-width{
      width: 1200px;
    }
  </style>
@endsection

@section('content')

<div class="product-detail">
    <div class="wrapper">
        <!-----_----- ALERT ---------------->
        <div style="display: none; margin-bottom: 20px;" id="success-back" class="success-msg msg-width" >
            <i aria-hidden="true" class="fa fa-times"></i> 
            Sukses Memasukkan Produk ke Dalam Tas Belanja
        </div>
        <div style="display: none; margin-bottom: 20px;" id="error-size-back" class="error-msg-login msg-width >
            <i aria-hidden="true" class="fa fa-bell"></i>
            <i aria-hidden="true" class="fa fa-times"></i>
            Pilih Ukuran Terlebih Dahulu
        </div>              
        <div style="display: none; margin-bottom: 20px;" id="error-color-back" class="error-msg-login msg-width" >
            <i aria-hidden="true" class="fa fa-bell"></i>
            <i aria-hidden="true" class="fa fa-times"></i>
            Pilih Warna Terlebih Dahulu
        </div>
        <div style="display: none; margin-bottom: 20px;" id="error-stock-back" class="error-msg-login msg-width" >
            <i aria-hidden="true" class="fa fa-bell"></i>
            <i aria-hidden="true" class="fa fa-times"></i>
            Produk ini sedang tidak tersedia
        </div>
        <div style="display: none; margin-bottom: 20px;" id="error-colorsize-back" class="error-msg-login msg-width" >
            <i aria-hidden="true" class="fa fa-bell"></i>
            <i aria-hidden="true" class="fa fa-times"></i>
            Pilih Ukuran dan Warna Terlebih Dahulu
        </div>
        <div style="display: none; margin-bottom: 20px;" id="error-manetail-back" class="error-msg-login msg-width" >
            <i aria-hidden="true" class="fa fa-bell"></i>
            <i aria-hidden="true" class="fa fa-times"></i>
            Produk ini hanya bisa dibeli maksimal 2 barang
        </div>
        <div class="detail-wrapper">
            <div class="prod-image">
                <div class="bc">
                    <?php $breadcrump = explode(",", $fetch_product->bahasa); ?>
                    <ul>
                        @if (isset($breadcrump))
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
                
                <div id="wishlist-info"></div>
                <!------------------------------------>

                <ul id="images-selected">
                    <!-- Set First Image -->
                    @if (isset($image_def_name))
                        <li>
                            <a href="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-featherlight="image">
                                <img src="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" id="default_image">
                            </a>
                        </li>
                    @endif   

                    <?php $i=0 ?>
                    @if (isset($fetch_product_image) && !empty($fetch_product_image))
                        @foreach ($fetch_product_image as $rows)
                            @if ($rows->id != $fetch_product_image_def->id)
                                <li>
                                    <a href="{{ ASSETS_PATH }}upload/product/zoom/{{ $rows->image_name }}" data-featherlight="image">
                                        <img src="{{ ASSETS_PATH }}upload/product/zoom/{{ $rows->image_name }}">
                                    </a>
                                </li>
                            @endif
                            <?php if (++$i == 5) break; ?>
                        @endforeach  
                    @endif 
                </ul>
            </div>
            <div class="prod-desc">
                <div class="prod-desc-wrapper">
                    <div class="prod-title">
                        <!-- <a href="#" class="add2wish"><i class="fa fa-heart-o" aria-hidden="true"></i></a> -->
                        {{-- <a href="{{ URL::to('/brand/'.$fetch_product->brand_url) }}">{{ $product_brand_name }}</a> --}}
                        <h1>{{ $product_name }}</h1>
                        
                        <!-- <p><span>IDR 259.000</span>IDR 149.000</p> -->
                    </div>
                    <div class="prod-wording">
                        <p id="product_description" rel="{{ $product_description }}">{{ $product_description }}</p>
                    </div>
                    <div class="price">
                        @if ($fetch_product->product_sale_price != 0)
                            IDR{{ number_format(($fetch_product->product_sale_price), 0, '.', '.') }}
                        @else
                            IDR{{ number_format(($fetch_product->product_price), 0, '.', '.') }}
                        @endif
                    </div>
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
                        <input type="hidden" name="product_name" id="product_name" value="<?php echo str_replace(array("&","/","\'"), array('and','or','_singlequote_'), $fetch_product->product_name);?>">
                        <input type="hidden" name="product_weight" id="product_weight" value="<?php echo isset($fetch_product->product_weight)?$fetch_product->product_weight:NULL;?>">
                        <input type="hidden" name="brand_name" id="brand_name" value="{{ isset($fetch_product->brand_name) ? $fetch_product->brand_name : '' }}">                                    
                        <input type="hidden" name="quantity" id="quantity" value="1">                   
                        <input type="hidden" name="variant_color_id" id="variant_color_id" value="{{ isset($fetch_product_image_def->variant_color_id) ? $fetch_product_image_def->variant_color_id : '' }}"/>
                        <input type="hidden" name="product_id" id="product_id" value="{{ isset($fetch_product->pid) ? $fetch_product->pid : '' }}"> 
                        <input type="hidden" name="product_inv" id="product_inv" value="{{ isset($fetch_product->brand_name) ? $fetch_product->brand_name : '' }}">
                        <input type="hidden" name="product_front_end_type" id="product_front_end_type" value="{{ isset($fetch_product->front_end_type) ? $fetch_product->front_end_type : '' }}">
                        <input type="hidden" name="product_type_url" id="product_type_url" value="{{ isset($fetch_product->url_set) ? $fetch_product->url_set : '' }}">
                        <input type="hidden" name="product_gender" id="product_gender" value="{{ isset($fetch_product->product_gender) ? $fetch_product->product_gender : '' }}">

                        <div class="sizing choose-color">

                            <h1>warna</h1>
                            <span>:</span>

                            <ul id="filter-color">
                                @if (isset($fetch_product_color) && !empty($fetch_product_color))
                                    @foreach ($fetch_product_color as $row)
                                        @if (isset($row->variant_color_id) && !empty($row->variant_color_id))
                                            <?php 
                                            $original_color_name = isset($row->variant_color_name) ? $row->variant_color_name : 'color name not set';

                                            $desc_color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;
                                            $hold_desc_color = NULL;
                                            $color = isset($row->variant_color_name_custom) ? $row->variant_color_name_custom : $original_color_name;
                                            
                                            $original_hexa = isset($row->variant_color_hexa) ? $row->variant_color_hexa : 'FFF';

                                            if(isset($row->variant_color_hexa_custom) && $row->variant_color_hexa_custom != ""){
                                                $color_hex = $row->variant_color_hexa_custom;
                                            }else{
                                                $color_hex = $original_hexa;
                                            }

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
                                            // $color_hex = isset($row->variant_color_hexa_custom) ? $row->variant_color_hexa_custom : $original_hexa;

                                            if(isset($row->variant_color_hexa_custom) && $row->variant_color_hexa_custom != ""){
                                                $color_hex = $row->variant_color_hexa_custom;
                                            }else{
                                                $color_hex = $original_hexa;
                                            }
                                            
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

                        @if($product_is_oos !== TRUE)
                            <div class="sizing choose-size">
                                <h1>ukuran</h1>
                                <span>:</span>
                                <ul id="select-size">
                                    <?php $total_inventory = 0; ?>
                                    @if(isset($fetch_product_size) && !empty($fetch_product_size))
                                        @foreach ($fetch_product_size as $rows)
                                            <li @if($rows->inventory <= 0) title="Habis Terjual" @endif>
                                                <div @if($rows->inventory <= 0) style="background-color:#dedede;" @endif><input type="radio" name="size_category" value="{{ $rows->product_size }}" id="size-{{ $rows->product_size_url }}" class="size-filter size-{{ $rows->product_size_url }}" @if($rows->inventory <= 0) disabled @endif>
                                                  <label for="size-{{ $rows->product_size_url }}" id="{{ $rows->product_sku }}" @if(!$rows->inventory <= 0) onclick="getSKU(this.id); _gaq.push(['_trackEvent','Product','Button','sizeSelect']);" @endif>{{ $rows->product_size }}
                            <!--                        <span class="tooltip" @if($rows->inventory == 0) style="color:red" @endif >Stok Sisa {{ $rows->inventory }}</span>-->
                                                  </label>
                                                </div>
                                            </li>
                                        <?php $total_inventory = $total_inventory + $rows->inventory; ?>
                                        @endforeach
                                    @endif  
                                    
                                    <input type="hidden" name="variant_color_name" id="variant_color_name" value="{{ $variant_color_name }}">
                                    <input type="hidden" name="image_name" id="image_name" value="{{ $image_def_name }}">
                                </ul>
                            </div>
                        @else
                            <div class="sizing choose-size" id="select-size">
                                <h1>ukuran :</h1>
                                <ul>
                                    <?php $total_inventory = 0; ?>
                                    @if(isset($fetch_product_size) && !empty($fetch_product_size))
                                        @foreach ($fetch_product_size as $rows)
                                        <li @if($rows->inventory <= 0) title="Habis Terjual" @endif>
                                            <div>
                                                <input type="radio" name="size_category" value="{{ $rows->product_size }}" disabled>
                                              <label style="cursor: default;">{{ $rows->product_size }}                    
                                              </label>
                                            </div>
                                        </li>
                                        <?php $total_inventory = $total_inventory + $rows->inventory; ?>
                                        @endforeach
                                    @endif 
                                </ul>
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
                            <div class="button">
                                <input type="submit" name="" value="Beli Sekarang"> 
                            </div>
                        @else
                            <div class="button">
                                <input type="submit" name="" value="Habis Terjual" disabled> 
                            </div>
                        @endif
                        

                    <a href="#" class="add2wish" rel="<?php echo $fetch_product->pid;?>" onclick="set_wishlist(this);"">add to wishlist</a>
                    <div class="tag">
                        <ul>
                            <span id="selectsku"></span>
                            <li><span>Categories : </span>{{ $fetch_product->type_name }}</li>
                            <li><span>Tags : </span>{!! $tag_name !!}</li>
                        </ul>
                    </div>

                    </form>
                    <div class="share-soc">
                        <ul>
                            <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Facebook" onclick="fbShare();" id="fb_share"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                            <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Twitter" onclick="twitterShare()" class="twitter popup" id="twitter_share"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Google+" id="gplus_share"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
                            <li><a href="#" data-url="{{ \Request::url() }}" data-title="{{ $product_name }}" data-image="{{ ASSETS_PATH }}upload/product/zoom/{{ $image_def_name }}" data-desc="{{ $product_description }}" alt="Share on Pinterest" id="pinterest_share"><i class="fa fa-pinterest"></i></a></li>
                        </ul>
                    </div>
                    <div class="sizing-care">
                        <div class="sizing">
                            <h1>Rincian Ukuran &amp; Fit<i class="fa fa-angle-down" aria-hidden="true"></i></h1>
                            <ul>
                                <li>
                                    {!! isset($fetch_product->product_size_guideline)?$fetch_product->product_size_guideline:'' !!}
                                </li>
                            </ul>
                        </div>
                        <div class="care">
                            <h1>Perawatan<i class="fa fa-angle-down" aria-hidden="true"></i></h1>
                            <ul>
                                <li>
                                    {!! isset($fetch_product->product_info)?$fetch_product->product_info:'' !!}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(count($product_related) > 0): ?>
<div class="pro-rec">
    <div class="wrapper">
        <div class="pro-rec-wrapper">
            <h1>shop the look</h1>
            <div class="pro-rec-outer">
                <div class="pro-rec-item">
                    @foreach ($product_related as $row)
                        <?php 
                            $url_arr = explode(',', $row->url_set);
                            $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                            $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                            $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-'));
                        ?>
                        <div>
                            <a href="{{$url}}">
                                <div class="pro-rec-img">
                                    <img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">
                                </div>
                                <div class="pro-rec-detail">
                                    <div class="pr-detail-left">
                                        <h1>{{ ucfirst($row->product_name) }}</h1>
                                    </div>
                                    <div class="pr-detail-right">
                                        @if(isset($row->product_sale_price) && $row->product_sale_price <> 0 && $row->product_sale_price <> '')
                                            <h2>IDR {{ number_format(($row->product_price), 0, '.', '.') }}</h2>
                                            <h2 class="discount">IDR {{ number_format(($row->product_sale_price), 0, '.', '.') }}</h2>
                                        @else
                                            <h2 class="discount">IDR {{ number_format(($row->product_price), 0, '.', '.') }}</h2>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

@endsection

@section('js')
<script async defer src="//assets.pinterest.com/js/pinit.js"></script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script src="{{ asset('js/desktop/sosmed_bb.js') }}"></script>
<script src="{{ asset('js/desktop/product-detail_bb.js') }}"></script>
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/script/featherlight.css') }}">
<script type="text/javascript" src="{{ asset('hijabenka/desktop/script/featherlight.js') }}"></script>

<?php if(count($product_related) > 0): ?>
<script type="text/javascript">
    var next = "{{ asset('hijabenka/desktop/script/slick/right.png') }}";
    var prev = "{{ asset('hijabenka/desktop/script/slick/left.png') }}";
</script>
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/script/slick/slick.css') }}">
<script src="{{ asset('hijabenka/desktop/script/slick/slick.js') }}"></script>

<script type="text/javascript">
$('.pro-rec-item').slick({
    infinite: true,
    slidesToShow: 5,
    slidesToScroll: 1,
    margin: 10,
    responsive: [
    {
      breakpoint: 900,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1
      }
    }
  ]
});
</script>
<?php endif; ?>
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
    product_id                  : '{{ isset($fetch_product->pid) ? $fetch_product->pid : '' }}',
    product_price               : '{{ isset($mkt_sale_price) ? $mkt_sale_price : $fetch_product->product_price }}',
    product_name                : '{{ $product_name }}',
    brand_name                  : '{{ $product_brand_name }}',
    brand_id                    : '{{ isset($fetch_product->brand_id) ? $fetch_product->brand_id : '' }}',
    product_frontendtypeID      : '{{ isset($ChildId_gtm) ? $ChildId_gtm : '' }}',
    product_frontendtypeName    : '{{ isset($ChildName_gtm) ? $ChildName_gtm : '' }}'
  }
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.product-page', ['catalog_ids' => json_encode($catalog_json), 'mkt_product_id' =>json_encode(isset($fetch_product->pid) ? $fetch_product->pid : '')])
@endif

@endsection