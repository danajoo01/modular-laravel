action.hit = [];


/* Trigger back button on browser */
$(window).on('popstate', function() {
    var fullPathUrl = window.location.pathname + window.location.search;
    var max_hit = action.hit.length - 1;
    var hitted = action.hit[max_hit];
    
    action.hit.splice(max_hit, 1);
});

function getSearchData(elm) {
    var datas       = "";
    var url_pagenum = "";
    var eventClick  = "getSearchData(this)";
    var word        = $('.keyword-search').val();
    var skeyword    = $('.skeyword').val();
    var token       = $('meta[name=_token]').attr('content');
    
    if(typeof $(elm).data('sort') != 'undefined') {
       var datas    = $(elm).val();
    }
    
    if(typeof $(elm).data('page') != 'undefined') {
        var datas       = $(elm).data('page');
        var page_num    = ($(elm).data('page') - 1) * 48;
        var url_pagenum = (page_num == 0) ? '' : '/'+ page_num;
        var currentPage = datas;
    }else{
        var currentPage = 1;
    }

    var uri = '/search'+url_pagenum+'?s='+skeyword+'&' + datas ;;
    
    /* Start to call ajax based on called url */
    $.ajax({
        dataType: "json",
        url: uri,
        type: "post",
        cache: false,
        data:{searchData:datas, keyword:word, _token:token},
        beforeSend: function() {
            $('#loading').show();
            $('#loading').css({"top": "0%"});
            $('html, body').animate({scrollTop : 0},300);
        },
        success: function(data){
            /* Call all necessary function */
            $('#loading').hide();
            $('.catalog-title').html(data.title);

            append_catalog(data.catalog);

            paginate_page(currentPage, data.total_catalog, eventClick);

            /* start to run url SEO with pushState */
            if (typeof (history.pushState) != "undefined") {
                var obj = { Title: "", Url: uri };
                history.pushState(obj, obj.Title, obj.Url);
            } else {
                alert("Browser does not support HTML5.");
            }

            action.hit.push(elm);
        }
    });
}

function append_catalog(data) {
    var segment = urlSegment();
    $('#ul-catalog').html('');

    $.each(data, function(index, value) {
        url_set = value.url_set;
        url_arr = url_set.split(',');

        if(typeof url_arr[0] != 'undefined') {
            parent = url_arr[0];
        } else {
            parent = value.type_url;
        }

        if(typeof url_arr[1] != 'undefined') {
            child = url_arr[1];
        } else {
            child = value.type_url;
        }

        detail_url = window.location.hostname + '/' + parent +'/'+ child +'/'+ value.pid +'/'+ toSlug(value.product_name) +'';
        full_detail_url = detail_url +'?trc_sale=search-solr';

        html = '<li id="li-catalog" class="'+ value.url_set +'">';
        html += '<a href="http://'+ full_detail_url +'" class="catalog-img">';

        if(value.discount > 0) {
            html += '<div class="disc-flag">';
            html += '<div class="disc-wrap">'+ value.discount +'%</div>';
            html += '<div class="triangle-topleft left"></div>';
            html += '<div class="triangle-topright right"></div>';
            html += '<div class="clear"></div></div>';
        }

        // html += '<div class="add2wish">';
        // html += '<i class="fa fa-heart" style="color:#000 !important">';
        // html += '<div class="b-tips">';
        // html += 'Add to Wishlist</div></i></div>';
        html += '<img src="http://im.onedeca.com/assets/cache/238x358/product/zoom/'+ value.image_name +'">';
        
        /* Condition for oos and limited stock */
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
            
            if(value.set_display_limited_item_category_bb.search(value.type_url) != -1 && parseInt(value.inventory) < set_display_limited_item_minimal_bb) {
                html += '<div class="limited-qty">Persediaan Terbatas</div>';
            }
        }

        if(value.set_display_oos_set_bb == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
            if(typeof value.set_display_oos_category_bb != 'undefined') {
                var set_display_oos_category_bb = value.set_display_oos_category_bb;
            } else {
                var set_display_oos_category_bb = '';
            }

            if(value.set_display_oos_category_bb.search(value.type_url) != -1 ) {
                html += '<div class="limited-qty">Persediaan Habis</div>';
            }
        }
        /* ------ */

        html += '</a>';
        html += '<div class="catalog-detail">';
        html += '<a href="http://'+ full_detail_url +'">';
        html += '<h1 class="catalog-name">'+ ucfirst(value.product_name) +'</h1>';
        html += '<h2 class="catalog-brand">'+ ucfirst(value.brand_name) +'</h2>';

        if(typeof (value.product_sale_price) != 'undefined' && value.product_sale_price != 0 && !isNaN(value.product_sale_price) != '') {
            html += '<p class="catalog-price disc-price"><span>IDR '+ number_format(value.product_price, 0, ',', '.') +'</span>IDR '+ number_format(value.product_sale_price, 0, ',', '.') +'</p>';
        } else {
            html += '<p class="catalog-price">IDR '+ number_format(value.product_price, 0, ',', '.') +'</p>';
        }

        html += '</a></div></li>';

        $('#ul-catalog').append(html).fadeIn('slow');
    });
}

function paginate_page(current_page, total_data, events) {
    $('#ul-pagination').html('');
    var html = '';

    if(typeof events != 'undefined'){
        var eventClick = events
    }else{
        var eventClick = 'ChangeUrl(this)'
    }
    
    var total_page = total_data / 48;
    var total_page = Math.ceil(total_page);
    var get_rest_page = (total_data - current_page) / 48;
    var get_rest_page = parseInt(get_rest_page);
    var rest_page = (get_rest_page <= 4) ? get_rest_page : 4;

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

            html += '<li><a data-page="'+ previous_link +'" href="javascript:void(0);" onclick="'+eventClick+'"><i class="fa fa-angle-left"></i></a></li>'; //previous
            
            for(i = (current_page - 2); i < current_page; i++) {
                if(i > 0) {
                    html += '<li><a data-page="'+ i +'" href="javascript:void(0);" onclick="'+eventClick+'">'+ i +'</a></li>';
                }
            }

            first_link = false; 
        }

        if(current_page == total_page) {
            html += '<li class="last active">'+ current_page +'</li>';
        } else {
            html += '<li class="active">'+ current_page +'</li>';
        }

        for(i = (current_page + 1); i < right_links; i++) {
            if(i <= total_page) {
                html += '<li><a data-page="'+ i +'" href="javascript:void(0);" onclick="'+eventClick+'">'+ i +'</a></li>';
            }
        }

        if(current_page < total_page && rest_page > 1) {
            var next_link = current_page+1;

            html += '<li><a data-page="'+ next_link +'" href="javascript:void(0);" onclick="'+eventClick+'"><i class="fa fa-angle-right"></i></a></li>'; //next link
        }

        $('#ul-pagination').append(html);
    }    
}

function urlSegment() {
    var newURL = window.location.protocol + "://" + window.location.host + "/" + window.location.pathname;
    var pathArray = window.location.pathname.split( '/' );
    var segment = pathArray

    return segment;
}