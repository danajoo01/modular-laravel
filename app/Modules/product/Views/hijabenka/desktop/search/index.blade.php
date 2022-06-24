@extends('layouts.hijabenka.desktop.main')

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/catalog.css?t=').date('YmdHis') }}">
@endsection

@section('content')
<div class="catalog-content">
    <input type="hidden" class='keyword-search' value="{!! $title !!}">
    <input type="hidden" class='skeyword' value="{!! $skeyword !!}">
    <input type="hidden" class='page-url' value="@if($start_catalog != 0)/{{ $start_catalog }}@endif">

    <div class="wrapper">
        <div class="small-bc">
            <h1 class="catalog-title">{!! $title !!}</h1>
        </div>
        <div class="catalog-wrapper">
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
                        $url_arr = explode(',', $row->url_set);
                        $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->type_url;
                        $child   = isset($url_arr[1]) ? $url_arr[1] : $row->type_url;
                        $url     = url(''.$parent.'/'.$child.'/'.$row->pid.'/'.str_slug($row->product_name, '-').'?trc_sale=search-solr');
                        ?>

                    <li id="li-catalog">

                        <a href="{{ $url }}">

                            
                            <div class="catalog-image" style="position: relative;"><img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}300x456/product/zoom/{{ $row->image_name }}">

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

                            <!-- <div class="sold-wrapper2">
                                <div class="sold-tags3">Persediaan Terbatas</div>
                            </div> -->


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
                {!! paginate_page($start_catalog, $total_catalog, 'onclick="getSearchData(this)"') !!}
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
<script src="{{ asset('js/desktop/search_bb.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag-body')
    @if(getMarketingEnv() == true)
        @include('marketing-tag.hijabenka.desktop.search')
    @endif
@endsection