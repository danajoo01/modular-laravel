@extends('layouts.hijabenka.main')

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
                <h1 class="catalog-title">sepatu &amp; tas stylish diskon hingga 80%</h1>
                <div class="sortby">
                    <select>
                        <option>Urutkan</option>
                        <option>Harga Terendah</option>
                        <option>Harga Tertinggi</option>
                        <option>Produk Terbaru</option>
                        <option>Diskon Terbesar</option>
                    </select>
                    <i class="fa fa-angle-down"></i>
                </div>
                <div class="clear"></div>
            </div>
            <div class="catalog-wrapper">
            	<div class="catalog-filter left" id="sidebar">
                <div class="filter-list">
                    <div class="reset-filter"><a href="">Reset Filter</a></div>
                        <div class="filter-category filter-content">
                            <p>Kategori</p>
                            <div class="filter-scroll">
                            <ul>
                                <li>
                                	<input type="checkbox" id="checkbox-category-jeans" class="category-filter parentCheckBox" data-level="parent" name="category_level1"><label for="checkbox-category-jeans" class="clearfix">Dress</label>
                                	<ul>
                                    	<li><input type="checkbox" id="checkbox-category-santai" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai">santai</label></li>
                                        <li><input type="checkbox" id="checkbox-category-santai2" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai2">santai2</label></li>
                                        <li><input type="checkbox" id="checkbox-category-santai3" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai3">santai3</label></li>
                                        <li><input type="checkbox" id="checkbox-category-santai4" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai4">santai4</label></li>
                                        <li><input type="checkbox" id="checkbox-category-santai5" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai5">santai5</label></li>
                                        <li><input type="checkbox" id="checkbox-category-santai6" class="category-filter childCheckBox" data-level="child" name="category_level2"><label for="checkbox-category-santai6">santai6</label></li>
                                    </ul>
                                </li>
                                <li><input type="checkbox" id="checkbox-category-legging" class="category-filter parentCheckBox" data-level="parent" name="category_level1"><label for="checkbox-category-legging">Atasan</label></li>
                                <li><input type="checkbox" id="checkbox-category-celpen" class="category-filter parentCheckBox" data-level="parent" name="category_level1"><label for="checkbox-category-celpen">bawahan</label></li>
                                <li><input type="checkbox" id="checkbox-category-celpan" class="category-filter parentCheckBox" data-level="parent" name="category_level1"><label for="checkbox-category-celpan">outerwear</label></li>
                                <li><input type="checkbox" id="checkbox-category-rok" class="category-filter parentCheckBox" data-level="parent" name="category_level1"><label for="checkbox-category-rok">pakaian dalam</label></li>
                                <!--<li><input type="checkbox" id="checkbox-category-kemeja" class="category-filter"><label for="checkbox-category-kemeja">kemeja</label></li>
                                <li><input type="checkbox" id="checkbox-category-dress" class="category-filter"><label for="checkbox-category-dress">dress</label></li>-->
                            </ul>
                            </div>
                        </div>
                        <div class="filter-color filter-content">
                            <p>Colors</p>
                            <ul>
                                <li><input type="checkbox" id="checkbox-light-grey" class="color-filter light-grey"><label for="checkbox-light-grey"></label></li>
                                <li><input type="checkbox" id="checkbox-light-blue" class="color-filter light-blue"><label for="checkbox-light-blue"></label></li>
                                <li><input type="checkbox" id="checkbox-pink" class="color-filter pink"><label for="checkbox-pink"></label></li>
                                <li><input type="checkbox" id="checkbox-light-orange" class="color-filter light-orange"><label for="checkbox-light-orange"></label></li>
                                <li><input type="checkbox" id="checkbox-light-green" class="color-filter light-green"><label for="checkbox-light-green"></label></li>
                                <li><input type="checkbox" id="checkbox-purple" class="color-filter purple"><label for="checkbox-purple"></label></li>
                                <li><input type="checkbox" id="checkbox-dark-blue" class="color-filter dark-blue"><label for="checkbox-dark-blue"></label></li>
                                <li><input type="checkbox" id="checkbox-dark-green" class="color-filter dark-green"><label for="checkbox-dark-green"></label></li>
                                <li><input type="checkbox" id="checkbox-orange" class="color-filter orange"><label for="checkbox-orange"></label></li>
                                <li><input type="checkbox" id="checkbox-light-brown" class="color-filter light-brown"><label for="checkbox-light-brown"></label></li>
                                <li><input type="checkbox" id="checkbox-black" class="color-filter black"><label for="checkbox-black"></label></li>
                                <li><input type="checkbox" id="checkbox-dark-grey" class="color-filter dark-grey"><label for="checkbox-dark-grey"></label></li>
                                <li><input type="checkbox" id="checkbox-dark-brown" class="color-filter dark-brown"><label for="checkbox-dark-brown"></label></li>
                                <li><input type="checkbox" id="checkbox-brown" class="color-filter brown"><label for="checkbox-brown"></label></li>
                            </ul>
                        </div>
                            <div class="filter-price filter-content">
                                <p>
                                    <label for="amount">Price range</label>
                                    <div class="price">
                                    	<select>
                                            <option>tampilkan semua</option>
                                            <option>IDR 0 - IDR 500K</option>
                                            <option>IDR 500K - IDR 1000K</option>
                                            <option>IDR 1000K - IDR 2000K</option>
                                            <option>IDR 2000K - IDR 3000K</option>
                                            <option>IDR 4000K - IDR 5000K</option>
                                            <option>IDR 5000K - IDR 6000K</option>
                                        </select>
                                        <i class="fa fa-angle-down"></i>
                                    </div>
                                    <!--<input type="text" id="low-amount" readonly value="Rp. 0k">
                                    <input type="text" id="high-amount" readonly value="Rp. 6000K">-->
                                </p>
                                <div id="slider-range"></div>
                            </div>
                            <div class="filter-size filter-content">
                                <p>Size</p>
                                <ul>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
                                    <li><div><input type="checkbox" id="size-s" class="size-filter size-s"><label for="size-s">S</label></div></li>
                                    <li><div><input type="checkbox" id="size-m" class="size-filter size-m"><label for="size-m">m</label></div></li>
                                    <li><div><input type="checkbox" id="size-l" class="size-filter size-l"><label for="size-l">l</label></div></li>
                                    <li><div><input type="checkbox" id="size-xl" class="size-filter size-xl"><label for="size-xl">xl</label></div></li>
                                    <li><div><input type="checkbox" id="onesize" class="size-filter onesize"><label for="onesize">one<br>size</label></div></li>
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
                                    	<ul>
                                            <li><input type="checkbox" id="checkbox-category-jeans" class="category-filter"><label for="checkbox-category-jeans">jeans</label></li>
                                            <li><input type="checkbox" id="checkbox-category-legging" class="category-filter"><label for="checkbox-category-legging">legging</label></li>
                                            <li><input type="checkbox" id="checkbox-category-celpen" class="category-filter"><label for="checkbox-category-celpen">celana pendek</label></li>
                                            <li><input type="checkbox" id="checkbox-category-celpan" class="category-filter"><label for="checkbox-category-celpan">celana panjang</label></li>
                                            <li><input type="checkbox" id="checkbox-category-rok" class="category-filter"><label for="checkbox-category-rok">rok</label></li>
                                            <li><input type="checkbox" id="checkbox-category-kemeja" class="category-filter"><label for="checkbox-category-kemeja">kemeja</label></li>
                                            <li><input type="checkbox" id="checkbox-category-dress" class="category-filter"><label for="checkbox-category-dress">dress</label></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="Terapkkan Filter">
                        </div>
                </div>
                <div class="catalog-list-wrapper right">
                <ul>
                    <li>
                        <a href="#" class="catalog-img"><img src="{{ asset('berrybenka/theme/img/promo-catalog.jpg') }}"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price"><span>IDR 900.000</span>IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price"><span>IDR 900.000</span>IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
                        <div class="catalog-detail">
                            <a href="#">
                                <h1 class="catalog-name">Lorine</h1>
                                <h2 class="catalog-brand">Waiscoat Denim Bella</h2>
                                <p class="catalog-price">IDR 209.999</p>
                            </a>
                        </div>
                    </li>
                    <li>
                        <a href="#" class="catalog-img"><img src="http://im.berrybenka.biz/assets/cache/238x358/product/zoom/74433_kyle-v-top_white_LGIT6.jpg"></a>
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
            <div class="clear"></div>
            <div class="pagination right">
            	<ul>
                	<li><a href="#"><i class="fa fa-angle-left"></i></a></li>
                	<li><a href="#">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection