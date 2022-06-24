action.hit = [];


/* Trigger back button on browser */
$(window).on('popstate', function() {
    var fullPathUrl = window.location.pathname + window.location.search;
    var max_hit     = action.hit.length - 1;
    var hitted      = action.hit[max_hit];

    execute_ajax(fullPathUrl, hitted, true);
    
    action.hit.splice(max_hit, 1);
});

/* get all checked value based on choosen element */
function getCheckedVal(elm) {
    var valUrl;
    var checked = $('#ul-'+ elm +' input:checkbox:checked').length;

    if(checked > 0) {
        var checkedUrl = $('#ul-'+ elm +' input:checkbox:checked').map(function() {
            if(elm == 'color' || elm == 'brand'){
                var checkedVal = '-'+ this.value +'-'
            } else {
                var checkedVal = this.value +'-'
            }
            
            return checkedVal;
        }).get();

        if(elm == 'color' || elm == 'brand'){
            valUrl = '/'+ elm +'/'+ checkedUrl.join('').slice(1, -1);
        } else {
            valUrl = '/'+ elm +'/'+ checkedUrl.join('').slice(0, -1);
        }

    } else {
        valUrl = '';
    }

    return valUrl;
}

function removeGender() {
    var segment = urlSegment();
    var gender;

    if(segment.indexOf('men') > -1 || segment.indexOf('women') > -1) {
        count   = segment.length - 1;
        gender  = segment[count];
    }
    
    segment_key = find_key(gender, segment);
    segment.splice(segment_key, 1);

    return segment.join('/');
}

function ChangeUrl(elm) {
    var full_uri;
    var colorUrl        = '';
    var sort            ='';
    var gender          = '';
    var pathUrl         = window.location.pathname;
    var lastUrl         = pathUrl.substring(pathUrl.lastIndexOf('/') + 1);
    var segment         = urlSegment();
    var segment_count   = segment.length - 1;
    var data_gender     = $(elm).data('gender');
    var color           = $(elm).data('color');
    var size            = $(elm).data('size');
    var brand           = $(elm).data('brand');
    var sort_elm        = $(elm);
    var urlFlag         = $(".input-url"); //flag to get catalog url
    var urlColorFlag    = $(".input-color-url"); //flag to get color url
    var urlSizeFlag     = $(".input-size-url"); //flag to get size url
    var urlBrandFlag    = $(".input-brand-url"); //flag to get size url
    var urlSortFlag     = $(".sort-url"); //flag to get sort url
    var urlPriceFlag    = $(".price-url"); //flag to get price url
    var urlPageFlag     = $(".page-url"); //flag to get page url
    var sortUri;
    var pageFlag        = "";
    var page_num        = 0;

    /* Define gender segment */
    if(segment[segment_count] == 'men' || segment[segment_count] == 'women') {
        gender = '/'+ segment[segment_count]; //get last url segment / 'gender' keyword uri    
    } else if(segment[segment_count - 1] == 'men' || segment[segment_count - 1] == 'women') {
        gender = '/'+ segment[segment_count - 1]; //get last url segment / 'gender' keyword uri
    }

    /* Define first uri */
    if(typeof $(elm).data('url') != 'undefined') {
        urlPageFlag.val('');

        var uri = '/'+ $(elm).data('url');
        urlFlag.val(uri);
        
        urlColorFlag.val('');
        urlSizeFlag.val('');
        urlBrandFlag.val('');
        urlPageFlag.val('');
    } else {
        if(urlFlag.val() == ""){
            urlFlag.val('/'+ segment[1]);
        }

        var uri = "";
        uri = pathUrl;
    }
    
    /* Define gender uri */
    if(typeof data_gender != 'undefined') {
        urlPageFlag.val('');

        if(gender != data_gender) {
            var uri_gender = '/'+ segment[1] +'/'+ data_gender;
        } else {
            var uri_gender = data_gender;
        }

        uri = "";

        urlSortFlag.val('');
        urlPriceFlag.val('');
        urlPageFlag.val('');
    } else {
        var uri_gender = gender;
        // var is_gender = false;
    }

    /* Define color uri */
    if(typeof color != 'undefined') {
        urlPageFlag.val('');
        urlPriceFlag.val('');
        uri_flag = removeGender();
        colorUrl = getCheckedVal('color');

        var uri  = urlFlag.val() + urlSizeFlag.val() + colorUrl + urlBrandFlag.val();

        urlColorFlag.val(colorUrl);
    } else {
        colorUrl = "";
    }

    /* Define size uri */
    if(typeof size != 'undefined') {
        urlPageFlag.val('');

        sizeUrl     = getCheckedVal('size');
        uri_flag    = removeGender();

        var uri     = urlFlag.val() + urlColorFlag.val() + sizeUrl + urlBrandFlag.val();

        urlSizeFlag.val(sizeUrl);
    } else {
        sizeUrl = "";
    }

    /* Define size uri */
    if(typeof brand != 'undefined') {
        urlPageFlag.val('');

        brandUrl = getCheckedVal('brand');
        uri_flag = removeGender();
        var uri = urlFlag.val() + urlColorFlag.val() + brandUrl;
        urlBrandFlag.val(brandUrl);
        /* append all brand */
        $("#ul-brand li").show();
        $("#search_brand").val('');
    } else {
        brandUrl = "";
    }

    /* paging section */
    var url_pagenum = '';
    if(typeof sort_elm.data('page') != 'undefined') {
        var getSegment = urlGetSegment();
        page_num = (sort_elm.data('page') - 1) * 48;
        var num_length = page_num.toString().length

        uri_gender = '';
        if(lastUrl != 'men' && lastUrl != 'women'){
            url_pagenum = '/'+ page_num;
            
            if(urlPageFlag.val() != '') {
                uri = uri.replace(lastUrl, ''); // replace last url
                
                /* Remove /0 page */
                if(page_num == 0) {
                    url_pagenum = '';
                    uri = uri.substring(0, uri.length -1);
                } else {
                    url_pagenum = page_num;
                }
            }

        } else {
            url_pagenum = '/' + page_num;

        }

        urlPageFlag.val(url_pagenum, elm);

        pageFlag = urlPageFlag.val();

        getSegment.splice(0,1);
        getSegment.unshift('');

        sortUri = create_uri(getSegment);
    }
    

    /* get all uri and add sort uri on the last */
    if(typeof sort_elm.data('sort') != 'undefined') {
        var value = sort_elm.val();

        if(value.indexOf('price') == 0) {
            sortUri = sort_action(value, 'price');
        }else if(sort_elm.val().indexOf('pn') == 0) {        
            sortUri = sort_action(value, 'pn');
        }else if(sort_elm.val().indexOf('discount') == 0) {        
            sortUri = sort_action(value, 'discount');
        }else if(sort_elm.val().indexOf('popular') == 0) {        
            sortUri = sort_action(value, 'popular');
        }else if(sort_elm.val().indexOf('recommended') == 0) {        
            sortUri = sort_action(value, 'recommended');
        }

        pageFlag    = "";
        uri_gender  = "";
    } else {
    	if(typeof sort_elm.data('page') == 'undefined')
    	{
            // $('#sort-by').val("pn=desc");
            $('#sort-by').val("popular=desc");
        }
    }

    /* get all uri and add sort uri on the last */
    if(typeof sort_elm.data('sortprice') != 'undefined') {
        uri_gender = "";
        if(sort_elm.val().indexOf('sprice') == 0) {
            sortUri = price_action(sort_elm.val(), 'sprice');
        }

        pageFlag = "";
        uri_gender = "";
    } else {
        if(typeof sort_elm.data('page') == 'undefined')
    	{
			$('#sort-by-price').val("");
		}
    }

    if(typeof sortUri != 'undefined') {
        sortUri = sortUri
    } else {
        //additional sort parameter       
        var currentUri = window.location.href;
        if(typeof currentUri.split('?')[1] != 'undefined'){    
            
            var varSort = '';
            var available_sort = ['price', 'pn', 'discount', 'popular'];
            var available_method = ['asc', 'desc'];
            
            varSort = currentUri.split('?')[1].split('=');
            if(typeof varSort[0] != 'undefined' && typeof varSort[1] != 'undefined' && available_sort.indexOf(varSort[0]) >= 0 && available_method.indexOf(varSort[1]) >= 0){
                varSort = varSort[0] + '=' + varSort[1];
                $('#sort-by').val(varSort);
                sortUri = '?' + varSort;
            }else{
                $('#sort-by').val('');
                // sortUri = '?pn=desc'; //default sort
                sortUri = '?popular=desc'; //default sort
            }            
            
        }else{
            // sortUri = '?pn=desc'; //default sort
            sortUri = '?popular=desc'; //default sort
        }        
    }

    if($("#wishlist-info").length) {
        $('#wishlist-info').empty();
    }

    // Combine all URL
    full_uri = uri + uri_gender + pageFlag + sortUri;    
    
    /* start to run url SEO with pushState */
    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: "", Url: full_uri };
        history.pushState(obj, obj.Title, obj.Url);       
        
        var uriEncode = encodeURIComponent(full_uri);        
        $('#form-login-route').attr("action", "/login?continue=" + uriEncode);
        $('a#register-route').attr("href", "/login?continue=" + uriEncode);
    } else {
        alert("Browser does not support HTML5.");
    }

    action.hit.push(elm);
    
    execute_ajax(full_uri, elm, false, page_num);
}

function sort_action(elm, keyword) {
    var getSegment = urlGetSegment();
    var sort_key;

    getSegment.splice(0,1);
    
    for(var i = 0; i < getSegment.length; i++) {
        /* Kehabisan akal */
        if(getSegment[i].indexOf('price') == 0 || getSegment[i].indexOf('pn') == 0 || getSegment[i].indexOf('discount') == 0 || getSegment[i].indexOf('popular') == 0) {
            sort_key = i;
        }
    }

    if(typeof sort_key != 'undefined') {
        getSegment.splice(sort_key, 1);
    }

    getSegment.push(elm);
    getSegment.unshift(""); //push blank string to the begining of array

    fullUri = create_uri(getSegment);

    return fullUri;
}

function price_action(elm, keyword) {
    var getSegment = urlGetSegment();
    
    getSegment.splice(0,1);
    
    segment_key = find_key(keyword, getSegment);

    if(typeof segment_key != 'undefined') {
        getSegment.splice(segment_key, 1);    
    }

    getSegment.push(elm);
    getSegment.unshift(""); //push blank string to the begining of array

    fullUri = create_uri(getSegment);

    return fullUri;
}


function execute_ajax(full_uri, elm, reload, page_num=0) {
    /* Inisiate current page */
    if(typeof $(elm).data('page') == 'undefined') {
        var currentPage = 1;
    } else {
        var currentPage = $(elm).data('page');
    }

    var timeout;

    /* Start to call ajax based on called url */
    $.ajax({
        timeout: 3000,
        dataType: "json",
        url: full_uri,
        type: "get",
        cache: false,
        beforeSend: function() {
            $('#loading').show();
            $('#loading').css({"top": "0%"});
            $('html, body').animate({scrollTop : 0},300);
        },
        success: function(data){
            /* Call all necessary function */
            $('#loading').hide();
            $('.catalog-title').html(data.title);

            var arr_wish = (data.wishlist_user != null) ? $.map(data.wishlist_user, function(el) { return el }) : [];

            append_catalog(data.catalog,arr_wish,data.domain_alias, data.ref, page_num);

            if(reload == false) {
                if(typeof $(elm).data('url') != 'undefined') {
                    append_color(data.color);    
                    append_size(data.size);
                    append_brand(data.brand);
                    paginate_page(currentPage, data.total_catalog);
                }          

                if(typeof $(elm).data('gender') != 'undefined') {
                    append_category(data.category);
                    append_color(data.color);    
                    append_size(data.size);
                    append_brand(data.brand);
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('brand') != 'undefined') { 
                    append_category(data.category);
                    append_color(data.color);    
                    append_size(data.size);
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('color') != 'undefined') { 
                    append_size(data.size);
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('size') != 'undefined') { 
                    append_color(data.color);
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('page') != 'undefined') {
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('sortprice') != 'undefined') {
                    
                    getSegment = urlGetSegment();

                    page_harga = getSegment[0].split('/');

                    page_harga = page_harga[page_harga.length - 1];

                    if(isNaN(page_harga)){
                        paginate_page(currentPage, data.total_catalog);
                    }else{
                        page_harga = (page_harga / 48) + 1;
                        paginate_page(page_harga, data.total_catalog);
                    }
                }

            } else {
                append_category(data.category);
                append_color(data.color);    
                append_size(data.size);
                append_brand(data.brand);
                paginate_page(currentPage, data.total_catalog);
            }
            
        },
        error: function(jqXHR, textStatus){
            if(textStatus === 'timeout'){
                window.location.href = window.location.origin + full_uri;
            }
        }
    });
}

/*
Append html for catalog
*/
function append_catalog(data,wishlist,domain_alias, ref, page_num=0) {
    //alert(data.wishlist);
    var root_url   = window.location.origin;
    var segment    = urlSegment();
    var index_data = 0;
    var counter;

    $('#ul-catalog').html('');

    if(data.length == 0) {
        html = '<div class="pnf-wrapper">';
        html += '<div class="pnf-content">';
        html += '<h1>Kami Mohon Maaf,<br>Produk Yang Ada Cari Tidak Ditemukan.</h1>';
        html += '<p>Lihat koleksi terbaru kami <a href="'+ root_url +'/new-arrival">disini</a></p>';
        html += '</div>';
        html += '</div>';

        $('#ul-catalog').append(html).fadeIn('slow');
    }    
    $.each(data, function(index, value) {
        counter = 1;
        /*     append banner catalog */                      
        if(value.isBannerCatalog !== 'undefined' && value.isBannerCatalog == true){
            html  = '<li id="li-catalog">';
            html += '<a href="'+ value.FullURLBanner +'">';
            html += '<img src="https://img.berrybenka.biz/assets/cache/238x358/catalog-banner/'+ value.path_image +'" original="'+ value.path_image +'">';                         
            html += '</a>';
            html += '<div class="catalog-detail text-left atw-wrapper">';
            html += '<a href="'+ value.FullURLBanner +'" class="title-anchor">';
            html += '<h1 class="catalog-brand">'+ value.text_1 +'</h1>';
            html += '<h2 class="catalog-name">'+ value.text_2 +'</h2>';
            html += '<p class="catalog-price">'+ value.text_3 +'</p>';                            
            html += '</a>';
            html += '</div>';
            html += '</li>';
            $('#ul-catalog').append(html).fadeIn('slow');
            return true;
        }
        
        if(typeof value.pid == 'undefined'){  
            return true;            
        }
        /* end append banner catalog*/
        
        url_set = value.url_set;
        url_arr = url_set.split(',');
        child   = value.type_url;
        parent  = value.type_url;

        if(typeof url_arr[0] != 'undefined') {
            parent = url_arr[0];
        }

        if(typeof url_arr[1] != 'undefined') {
            child = url_arr[1];
        }

        detail_url      = window.location.hostname + '/' + parent +'/'+ child +'/'+ value.pid +'/'+ toSlug(value.product_name) +'';
        next_segment    = (typeof segment[2] != 'undefined') ? '+'+ segment[2] : '';
        full_detail_url = detail_url +'?trc_sale='+ segment[1] + next_segment;

        var set_active_wishlist = '';
        var style = '';
        if (wishlist && (jQuery.inArray(value.pid,wishlist) !== -1)) {
            //style = 'style=background:#333;';
            set_active_wishlist = ' heart-red';
        }                
        
        html = '<li id="li-catalog" class="'+ value.url_set +'">';

        html += "<a href='http://"+ full_detail_url +"' class='catalog-img' ";
        html += "onClick=\"onProductClick("+ value.pid +", '"+ value.product_name +"', '"+ value.url_set +"', '"+ value.brand_name +"', '', "+ counter +", '"+ ref +"', '"+ full_detail_url +"')\">";
       
        if(typeof value.product_overlay_image != 'undefined'){
            html += '<img alt="" class="overlay-product" src="http://im.onedeca.com/assets/cache/300x456/product-overlay/'+ value.product_overlay_image +'">';
        }
        html += '<img src="http://im.onedeca.com/assets/cache/300x456/product/zoom/'+ value.image_name +'">';
        /* Condition for oos and limited stock */
        if (domain_alias == 'hb') {
            if (value.set_display_limited_item_set_hb == 1 && parseInt(value.inventory) > 0 && value.product_status != 2) {
                if(typeof value.set_display_limited_item_category_hb != 'undefined') {
                    var set_display_limited_item_category_hb = value.set_display_limited_item_category_hb;
                } else {
                    var set_display_limited_item_category_hb = '';
                }

                if(typeof value.set_display_limited_item_minimal_hb != 'undefined') {
                    var set_display_limited_item_minimal_hb = value.set_display_limited_item_minimal_hb;
                } else {
                    var set_display_limited_item_minimal_hb = 0;
                }
                
                if(set_display_limited_item_category_hb.search(value.type_url) != -1 && parseInt(value.inventory) < set_display_limited_item_minimal_hb) {
                    //html += '<div class="limited-qty">Persediaan Terbatas</div>';
                    html += '<div class="sold-wrapper2"><div class="sold-tags3">Persediaan Terbatas</div></div>';
                }
            }

            if(value.set_display_oos_set_hb == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
                if(typeof value.set_display_oos_category_hb != 'undefined') {
                    var set_display_oos_category_hb = value.set_display_oos_category_hb;
                } else {
                    var set_display_oos_category_hb = '';
                }

                if(set_display_oos_category_hb.search(value.type_url) != -1 ) {
                    //html += '<div class="limited-qty">Persediaan Habis</div>';
                    html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
                }
            }
        } else if(domain_alias == 'sd') {
                if (value.set_display_limited_item_set_sd == 1 && parseInt(value.inventory) > 0 && value.product_status != 2) {
                    if(typeof value.set_display_limited_item_category_sd != 'undefined') {
                        var set_display_limited_item_category_sd = value.set_display_limited_item_category_sd;
                    } else {
                        var set_display_limited_item_category_sd = '';
                    }

                    if(typeof value.set_display_limited_item_minimal_sd != 'undefined') {
                        var set_display_limited_item_minimal_sd = value.set_display_limited_item_minimal_sd;
                    } else {
                        var set_display_limited_item_minimal_sd = 0;
                    }
                    
                    if(set_display_limited_item_category_sd.search(value.type_url) != -1 && parseInt(value.inventory) < set_display_limited_item_minimal_sd) {
                        //html += '<div class="limited-qty">Persediaan Terbatas</div>';
                        html += '<div class="sold-wrapper2"><div class="sold-tags3">Persediaan Terbatas</div></div>';
                    }
                }

                if(value.set_display_oos_set_sd == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
                    if(typeof value.set_display_oos_category_sd != 'undefined') {
                        var set_display_oos_category_sd = value.set_display_oos_category_sd;
                    } else {
                        var set_display_oos_category_sd = '';
                }

                if(set_display_oos_category_sd.search(value.type_url) != -1 ) {
                    //html += '<div class="limited-qty">Persediaan Habis</div>';
                    html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
                }
            }
        } else {
            if (value.set_display_limited_item_set_bb == 1 && parseInt(value.inventory) > 0 && value.product_status != 2) {
                if(typeof value.set_display_limited_item_category_bb != 'undefined') {
                    var set_display_limited_item_category_bb = value.set_display_limited_item_category_bb;
                } else {
                    var set_display_limited_item_category_bb = '';
                }

                if(typeof value.set_display_limited_item_minimal_bb != 'undefined') {
                    var set_display_limited_item_minimal_bb = value.set_display_limited_item_minimal_bb;
                } else {
                    var set_display_limited_item_minimal_bb = 0;
                }
                
                if(set_display_limited_item_category_bb.search(value.type_url) != -1 && parseInt(value.inventory) < set_display_limited_item_minimal_bb) {
                    //html += '<div class="limited-qty">Persediaan Terbatas</div>';
                    html += '<div class="sold-wrapper2"><div class="sold-tags3">Persediaan Terbatas</div></div>';
                }
            }

            if(value.set_display_oos_set_bb == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
                if(typeof value.set_display_oos_category_bb != 'undefined') {
                    var set_display_oos_category_bb = value.set_display_oos_category_bb;
                } else {
                    var set_display_oos_category_bb = '';
                }

                if(set_display_oos_category_bb.search(value.type_url) != -1 ) {
                    html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
                }
            }

            /* Write to impression data that placed in app.js */
            writeImpression(value, ref, counter);

            counter ++;
            index_data++;
        }
        /* ------ */

        html += '</a>';
        html += '<div class="catalog-detail text-left atw-wrapper">';
        html += "<a class=\"title-anchor\" href='http://"+ full_detail_url +"'";
        html += "onClick=\"onProductClick("+ value.pid +", '"+ value.product_name +"', '"+ value.url_set +"', '"+ value.brand_name +"', '', "+ counter +", '"+ ref +"', '"+ full_detail_url +"') \">";
        html += '<h1 class="catalog-brand">'+ ucfirst(value.brand_name) +'</h1>';
        html += '<h2 class="catalog-name">'+ ucfirst(value.product_name) +'</h2>';

        if(typeof (value.product_sale_price) != 'undefined' && value.product_sale_price != 0 && !isNaN(value.product_sale_price) != '') {
            html += '<p class="catalog-price disc-price"><span>IDR '+ number_format(value.product_price, 0, ',', '.') +'</span>IDR '+ number_format(value.product_sale_price, 0, ',', '.') +'</p>';
        } else {
            html += '<p class="catalog-price">IDR '+ number_format(value.product_price, 0, ',', '.') +'</p>';
        }
        
        if(value.discount > 0) {
            html += '<div class="disc-tags-bot" style="margin-top:5px;">'+ value.discount +'%</div>';
        }
        html += '</a>';    
        

        html += '<a id="add2wish-'+ value.pid + '" rel="'+ value.pid + '" onclick="set_wishlist(this)" ' + style +'>';
        html += '<i class="fa fa-heart-o atw2 '+ set_active_wishlist +'" id="wish_'+ value.pid +'">';
        html += '</a>';
        
        html += '</div></li>';

        $('#ul-catalog').append(html).fadeIn('slow');
    });
}

/*
Append html for category
*/
function append_category(data) {
    var segment = urlSegment();
    var parent_val = findUriSegment('cat_parent');
    var children_val = findUriSegment('cat_children');

    $('#ul-category').html('');
    // var html = '';
    $.each(data, function(index, value) {
        html = '<li>';
        html += '<input type="radio" id="checkbox-category-'+ value.type_url +'" class="category-filter parentCheckBox" data-level="parent" name="category_level1" data-url="'+ segment[1] +'/'+ value.type_url +'" onclick="ChangeUrl(this)"'

        if(typeof parent_val != 'undefined') {
            var get_key = parent_val.indexOf(value.type_url);
            if(get_key >= 0) {
                html += 'checked="checked"';
            }
        }

        html += '>';

        html += '<label for="checkbox-category-'+ value.type_url +'">'+ value.type_name_bahasa +'</label>';
        html += '<ul>';

        if(value.child != null){
            $.each(value.child, function(val_index, child) {
                html += '<li><input type="radio" id="checkbox-category-'+ child.type_url +'" class="category-filter childCheckBox" data-level="child" name="category_level2" onclick="ChangeUrl(this)" data-url="'+ segment[1] +'/'+ value.type_url +'/'+ child.type_url +'"'

                if(typeof children_val != 'undefined') {
                    var get_key = children_val.indexOf(child.type_url);

                    if(get_key >= 0) {
                        html += 'checked="checked"';
                    }
                }

                html += '>';

                html += '<label for="checkbox-category-'+ child.type_url +'">'+ child.type_name_bahasa +'</label></li>';
            });
        }

        html += '</ul></li>';             

        $('#ul-category').append(html);
    });
}

/*
Append html for color
*/
function append_color(data) {
    $('#ul-color').html('');
    var color_val = findUriSegment('color');

    $.each(data, function(index, value) {
        html = '<li style="margin-left: 2px;">';
        html += '<input type="checkbox" id="checkbox-'+ value.color_name +'" class="color-filter '+ value.color_name +'" onclick="ChangeUrl(this)" data-color="true" value="'+ value.color_name +'"';

        if(typeof color_val != 'undefined') {
            var get_key = color_val.indexOf(value.color_name);

            if(get_key >= 0) {
                html += 'checked="checked"';
            }
        }

        html += '>';
        html += '<label for="checkbox-'+ value.color_name +'" style="background-color:#'+ value.color_hex +' !important"></label>';
        html += '</li>';
        
        $('#ul-color').append(html);
    });
}

/*
Append html for size
*/
function append_size(data) {
    $('#ul-size').html('');
    var size_val = findUriSegment('size');

    $.each(data, function(index, value) {
        if(value.product_size_url != '' && typeof value.product_size_url != 'undefined') {
            html = '<li><div>';
            html += '<input type="checkbox" id="size-'+ value.product_size +'" class="size-filter '+ value.product_size +'" onclick="ChangeUrl(this)" data-size="true" value="'+ value.product_size_url +'"';

            if(typeof size_val != 'undefined') {
                var get_key = size_val.indexOf(value.product_size_url);
                if(value.product_size_url == size_val[get_key]) {
                    html += 'checked="checked"';
                }
            }

            html += '>';
            html += '<label for="size-'+ value.product_size +'">'+ value.product_size +'</label>';
            html += '</div></li>';

            $('#ul-size').append(html);
        }
    });
}

function append_brand(data) {
    $('#ul-brand').html('');
    var brand_val = findUriSegment('brand');

    $.each(data, function(index, value) {
        var brand_url = (typeof value.brand_url != 'undefined') ? value.brand_url : '';

        html = '<li>';
        html += '<input type="checkbox" id="checkbox-category-'+ brand_url +'" class="category-filter brand-filter" onclick="ChangeUrl(this)" data-brand="true" value="'+ brand_url +'"';

        if(typeof brand_val != 'undefined') {
            var get_key = brand_val.indexOf(brand_url);
            if(brand_url == brand_val[get_key]) {
                html += 'checked="checked"';
            }
        }

        html += '>';
        html += '<label for="checkbox-category-'+ brand_url +'">'+ value.brand_name +'</label>';
        html += '</li>';

        $('#ul-brand').append(html);
    });
}

function paginate_page(current_page, total_page) {
    $('#ul-pagination').html('');
    var html          = '';
    
    var page          = (current_page - 1) * 48;
    var get_rest_page = (total_page - page) / 48;
    var get_rest_page = parseInt(get_rest_page) + 1;
    var rest_page     = (get_rest_page <= 4) ? get_rest_page : 4;
    
    if(total_page > 0 && total_page != 1 && current_page <= total_page) {
        var right_links    = current_page + rest_page; 
        var previous       = current_page - 1; //previous link 
        var next           = current_page + 1; //next link
        var first_link     = true; //boolean var to decide our first link

        if(current_page > 1) {
            var previous_link = previous;

            if(previous == 0) {
                previous_link = 1;
            }

            html += '<li><a data-page="'+ previous_link +'" href="javascript:void(0);" onclick="ChangeUrl(this)"><i class="fa fa-angle-left"></i></a></li>'; //previous
            
            for(i = (current_page - 2); i < current_page; i++) {
                if(i > 0) {
                    html += '<li><a data-page="'+ i +'" href="javascript:void(0);" onclick="ChangeUrl(this)">'+ i +'</a></li>';
                }
            }

            first_link = false; 
        }

        if(current_page == total_page) {
            html += '<li class="last active">'+ current_page +'</li>';
        } else {
            html += (current_page != null) ? '<li class="active">'+ current_page +'</li>' : '<li class="active">1</li>'; 
        }

        for(i = ((current_page != null) ? current_page + 1 : current_page + 2) ; i < ((current_page != null) ? right_links : right_links+1); i++) {
            if(i <= total_page) {
                html += '<li><a data-page="'+ i +'" href="javascript:void(0);" onclick="ChangeUrl(this)">'+ i +'</a></li>';
            }
        }

        if(current_page < total_page && rest_page > 1) {
            var next_link = current_page + 1;
            
            html += '<li><a data-page="'+ next_link +'" href="javascript:void(0);" onclick="ChangeUrl(this)"><i class="fa fa-angle-right"></i></a></li>'; //next link
        }

        $('#ul-pagination').append(html);
    }    
}

/* Recall lazyload after append image */
$(document).ajaxStop(function(){
    $(".catalog-img img").lazyload({ 
        effect: "fadeIn" 
    });
});

$(document).ready(function() {

    $(".filter-price .price").click(function(){
        $('#sort-by-price').attr('size',7);
        $('.filter-price .fa-angle-down').hide();
    });

    $(".filter-price #sort-by-price").mouseleave(function(){
        $('.filter-price .fa-angle-down').show();
        $('.filter-price #sort-by-price').removeAttr('size');
    });
    /* event for brand search */
    $("#search_brand").keyup(function(){
        // Retrieve the input field text and reset the count to zero
        var filter = $(this).val(), count = 0;
 
        // Loop through the comment list
        $("#ul-brand li").each(function(){
 
            // If the list item does not contain the text phrase fade it out
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).fadeOut();
 
            // Show the list item if the phrase matches and increase the count by 1
            } else {
                $(this).show();
                count++;
            }
        });
    });

    /* Group all checked checkbox to top */
    $('#ul-brand').on('click', ':checkbox', function() {
        var list = $('#ul-brand');
        var i, checked = document.createDocumentFragment(),
        unchecked = document.createDocumentFragment();
        var origOrder = list.children();

        for (i = 0; i < origOrder.length; i++) {
            if (origOrder[i].getElementsByTagName("input")[0].checked) {

                checked.appendChild(origOrder[i]);
            } else {
                unchecked.appendChild(origOrder[i]);
            }
        }

        list.append(checked).append(unchecked);
    });

    $('.clear-filter').click(function() {
        var root_url = $('.input-url').val();
        var max_hit = action.hit.length - 1;
        var hitted = action.hit[max_hit];

        $(".input-size-url").val('');
        $(".input-url").val('');
        $(".input-color-url").val('');
        $(".input-brand-url").val('');
        $(".sort-url").val('');
        $(".price-url").val('');
        $(".page-url").val('');
        $("input[name='gender']").prop('checked', false);

        arr_rul = root_url.split('/');
        url = '/' + arr_rul[1];

        if(root_url != '') {
            /* start to run url SEO with pushState */
            if (typeof (history.pushState) != "undefined") {
                var obj = { Title: "", Url: url };
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }

            execute_ajax(url, hitted, true);
        }
    })
});
