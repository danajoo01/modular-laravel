@extends('layouts.shopdeca.main')

@section('content')
<div class="content">
	<div class="catalog-list">
		<div class="wrapper">
        	<div class="content-header">
                <div class="breadcrump">
                    <ul>
                        <li><a href="#">Pakaian</a></li>
                        <li>/</li>
                        <li><a href="#">Atasan</a></li>
                        <li>/</li>
                        <li><a href="#">Kemeja</a></li>
                    </ul>
                </div>
                <div class="clear"></div>
            </div>
            <div class="detail-wrapper">
            	<div class="detail-photo left">
                	<div class="big-photo left"><a class="fancybox-effects-c" href="{{ asset('shopdeca/theme/img/detail-big.jpg') }}"><img src="{{ asset('shopdeca/theme/img/detail-big.jpg') }}"></a></div>
                    <div class="small-photo left">
                    	<ul>
                        	<li><a class="fancybox-effects-c" href="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"><img src="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"></a></li>
                            <li><a class="fancybox-effects-c" href="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"><img src="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"></a></li>
                            <li><a class="fancybox-effects-c" href="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"><img src="{{ asset('shopdeca/theme/img/detail-small.jpg') }}"></a></li>
                            <li><a class="fancybox-effects-c" href="{{ asset('shopdeca/theme/img/bb-detail-small.jpg') }}"><img src="{{ asset('shopdeca/theme/img/bb-detail-small.jpg') }}"></a></li>
                        </ul>
                    </div>
                </div>
                <div class="detail-spec left">
                	<div class="addtowish"><a href="#"><i class="fa fa-heart-o"></i></a></div>
                    <div class="prod-spec-title">
                    	<h1>Outer Batik Bamboo Collar</h1>
                        <h2>Berrybenka Label</h2>
                        <p><span>IDR229.000</span>IDR149.000</p>
                    </div>
                    <p class="prod-desc">Lengkapi tampilan casual kamu dengan atasan kaos lengan pendek ini! Memiliki aksen asimetris dengan detail kantung pada bagian depan. Cocok untuk dipadu padankan dengan ripped jeans putih dan sepatu slip on!</p>
                    <a href="#" class="lengkap"></a>
                    <div class="detail-spec-detail">
                    	<p>Warna</p>
                        <div class="filter-color filter-content">
                            <ul>
                                <li><input type="checkbox" class="color-filter light-grey" id="checkbox-light-grey"><label for="checkbox-light-grey"></label></li>
                                <li><input type="checkbox" class="color-filter light-blue" id="checkbox-light-blue"><label for="checkbox-light-blue"></label></li>
                                <li><input type="checkbox" class="color-filter pink" id="checkbox-pink"><label for="checkbox-pink"></label></li>
                                <li><input type="checkbox" class="color-filter light-orange" id="checkbox-light-orange"><label for="checkbox-light-orange"></label></li>
                                <li><input type="checkbox" class="color-filter light-grey" id="checkbox-light-grey"><label for="checkbox-light-grey"></label></li>
                                <li><input type="checkbox" class="color-filter light-blue" id="checkbox-light-blue"><label for="checkbox-light-blue"></label></li>
                                <li><input type="checkbox" class="color-filter pink" id="checkbox-pink"><label for="checkbox-pink"></label></li>
                                <li><input type="checkbox" class="color-filter light-orange" id="checkbox-light-orange"><label for="checkbox-light-orange"></label></li>
                                <li><input type="checkbox" class="color-filter light-grey" id="checkbox-light-grey"><label for="checkbox-light-grey"></label></li>
                                <li><input type="checkbox" class="color-filter light-blue" id="checkbox-light-blue"><label for="checkbox-light-blue"></label></li>
                                
                            </ul>
                        </div>
                    </div>
                    <div class="detail-spec-detail">
                    	<p>Ukuran</p>
                        <div class="filter-size filter-content">
                            <ul>
                                <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size <span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl<span class="tooltip">Stok Sisa 39</span></label></div></li>
                                <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size <span class="tooltip">Stok Sisa 39</span></label></div></li>
                            </ul>
                        </div>
                    </div>
                    <input class="checkout-btn" type="submit" value="beli sekarang">
                    <div class="sku-tags">
                    	<span><strong>SKU</strong>: 9012930583237</span>
                        <span><strong>Categories</strong>: <a href="#">Bags</a></span>
                        <span><strong>Tags</strong>: <a href="#">Baju, Dreses</a></span>
                    </div>
                    <div class="collapse-detail">
                        <ul>
                            <li class="detail-col">
                                <h1>Rincian Ukuran & Fit <span><i class="fa fa-angle-down"></i></span></h1>
                                <div>
                                    <p>Lingkar Dada <span>: 70 cm</span></p>
                                    <p>Lingkar Pinggang <span>: 34-68 cm</span></p>
                                    <p>Panjang Baju <span>: 71 cm</span></p>
                                </div>
                            </li>
                            <li class="detail-col">
                                <h1>Perawatan <span><i class="fa fa-angle-down"></i></span></h1>
                                <div>
                                    <p>Gunakan detergen yang lembut</p>
                                    <p>Jangan gunakan pemutih</p>
                                    <p>Setrika suhu rendah</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                    
                </div>
                <div class="share right">
                	<ul>
                        <li><a href="#"><i class="fa fa-facebook-official"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                        <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                    </ul>
                </div>
                <div class="clear"></div>
                <div class="ymal">
                <h1>Anda Juga Akan Menyukai</h1>
                <ul>
                    <li>
                        <a href="#"><img src="{{ asset('berrybenka/theme/img/promo-catalog.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price"><span>IDR 900.000</span>IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#"><img src="http://im.berrybenka.biz/{{ asset('shopdeca/theme/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price"><span>IDR 900.000</span>IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#"><img src="http://im.berrybenka.biz/{{ asset('shopdeca/theme/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#"><img src="http://im.berrybenka.biz/{{ asset('shopdeca/theme/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#"><img src="http://im.berrybenka.biz/{{ asset('shopdeca/theme/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection