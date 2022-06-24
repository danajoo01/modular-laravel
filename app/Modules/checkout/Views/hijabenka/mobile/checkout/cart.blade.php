@extends('layouts.hijabenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/cart.css') }}">
<style>
    .sub-btn{
        padding: 15px 0 !important;
    }
</style>
@endsection

@section('filter')
<div class="content-detail">
    <div class="cart-wraper">
    	<div class="cart-header">
        	<h1>Tas Belanja Saya</h1>
            <a href="/new-arrival/">Kembali Berbelanja</a>
        </div>
        <div class="cart-list">
        	<ul>
                {{--*/ $invalid_item_count = 0  /*--}}
                {{--*/ $i = 0  /*--}}
                {{--*/ $criteo_val = []  /*--}}
                @if(!empty($fetch_cart))
                    <?php $product_ids = []; ?>
                    @foreach($fetch_cart as $cart)
                        <?php $product_ids[] = $cart['product_id']; ?>
                        {!! Form::open(['id' => 'form-cart-'.$cart['SKU'], 'url' => 'checkout/update_cart']) !!}
                            {{--*/ $is_in_stock = ($cart['qty'] > $cart['inv_qty']) ? FALSE : TRUE  /*--}}
                            {{--*/ $is_empty = ($cart['inv_qty'] <= 0) ? TRUE : FALSE  /*--}}
                            {{--*/ $invalid_item_count = (!$is_in_stock || $is_empty) ? $invalid_item_count+1 : $invalid_item_count /*--}}
                            <li>
                                <div class="error-msg-login" {!!($is_in_stock) ? 'style="display:none;"' : ''!!}><i class="fa fa-bell" aria-hidden="true"></i>
                                    @if($is_empty)
                                        Stok untuk product ini habis, mohon dihapus sebelum melanjutkan belanja.
                                    @else
                                        Maksimal quantity untuk product ini adalah {{ $cart['inv_qty'] }}
                                    @endif
                                </div>
                                <div id="del-{{$cart['SKU']}}" class="del-confirm">
                                    <div class="del-wrapper">
                                        <p>Hapus Produk Ini</p>
                                        <div class="del-button clear">
                                            <a sku="{{$cart['SKU']}}" class="del-btn-yes" href="#">Ya</a>
                                            <a sku="{{$cart['SKU']}}" class="del-btn-no" href="#">Tidak</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="cart-img left"><a href="{{ $cart['url'] }}"><img src="{{IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH.$cart['image']}}" alt=""></a></div>
                                <div class="cart-detail left">
                                    <h1>{{ $cart['name'] }}</h1>
                                    {{-- <h2>{{ strtoupper($cart['brand_name']) }}</h2> --}}
                                    <p>SKU <span>: {{ $cart['SKU'] }}</span></p>
                                    <p>Warna <span>: {{ $cart['color_name'] }}</span></p>
                                    <p>Ukuran <span>: {{ $cart['size'] }}</span></p>
                                    <p>IDR {{ number_format($cart['price']) }}</p>
                                    <p class="cart-qty clear">JUMLAH
                                        <span>:
                                            <select name="quantity" class="sel-qty" sku="{{ $cart['SKU'] }}">
                                                {{--*/ $temp_max_qty = ($is_in_stock) ? $cart['inv_qty'] : $cart['qty']  /*--}}
                                                @for ($temp = 1; $temp <= $temp_max_qty && $temp <= 10; $temp++)
                                                    <option <?php echo ($cart['qty'] == $temp) ? 'selected' : '' ; ?> value="{{ $temp }}">{{ $temp }}</option>
                                                @endfor
                                            </select>
                                        </span>
                                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </p>
                                    <a href="#" class="del-cart" sku="{{ $cart['SKU'] }}"><i class="fa fa-times" aria-hidden="true"></i> Hapus</a>
                                </div>
                            </li>
                            <input type="hidden" name="SKU" value="{{ $cart['SKU'] }}" />
                            <input type="hidden" id="is-delete-{{ $cart['SKU'] }}" name="is_delete" value="0" />
                        {!! Form::close() !!}

                        <!-- variable for criteo marketing tag -->
                        {{--*/ $criteo_val[$i]['id'] = $cart['product_id'] /*--}}
                        {{--*/ $criteo_val[$i]['price'] = $cart['price'] /*--}}
                        {{--*/ $criteo_val[$i]['quantity'] = $cart['qty'] /*--}}

                        {{--*/ $i++ /*--}}

                    @endforeach
                @else
                    Cart is empty
                @endif
            </ul>
        </div>
        <div class="cart-total">
        	Total<br><p>*Diluar Biaya Pengiriman</p>
            <span>IDR {{number_format($grand_total)}}</span>
            <input type="hidden" id="raw-grandtotal-value" value="{{$grand_total}}" />
        </div>
        <a href="/checkout/submit_order/" value="Proses Pembayaran" class="sub-btn" {!!($invalid_item_count > 0) ? 'disabled="disabled"' : ''!!}>Proses Pembayaran</a>
    </div>
</div>
@endsection

@section('js')
    <script src="{{ asset('js/mobile/cart.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php $user = \Auth::user();

//$product_ids_json = !empty($product_ids) ? json_encode($product_ids) : json_encode([]);
$product_json = $marketing_data;
?>
var cart336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
  }

var tag_val336CC993E54E = {!! json_encode($criteo_val) !!};

var marketing336CC993E54E = {!! $marketing_data !!};
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.mobile.cart-page', ['cart_data' => $product_json])
@endif

@endsection
