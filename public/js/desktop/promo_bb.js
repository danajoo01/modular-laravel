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

    if(elm == 'sprice'){
        var checked = $('#ul-harga input:radio:checked').length;
    }else{
        var checked = $('#ul-'+ elm +' input:checkbox:checked').length;
    }


    if(checked > 0) {
        if(elm == 'sprice'){
            var checkedUrl = $('#ul-harga input:radio:checked').map(function() {
                var checkedVal = this.value;
                return checkedVal;
            }).get();
        }else{
            var checkedUrl = $('#ul-'+ elm +' input:checkbox:checked').map(function() {              
                if(elm == 'color' || elm == 'brand'){
                    var checkedVal = '-'+ this.value +'-'
                } else {
                    var checkedVal = this.value +'-'
                }
                
                return checkedVal;
            }).get();
        }
        
        
        if(elm == 'color' || elm == 'brand'){
            valUrl = elm +'='+ checkedUrl.join('').slice(1, -1);
        }else if(elm == 'sprice'){
            if(checkedUrl.length > 1){
                // clean URL
                var priceuri = '';
                $.each(checkedUrl, function(index, value) {
                    priceuri += value.replace("sprice=", "") + '|'; 
                });

                valUrl = 'sprice=' + priceuri.slice(0, -1);
            }else{
                valUrl = checkedUrl[0];
            }
            // valUrl = checkedUrl.join('').slice(0, -1);
        } else {
            valUrl = elm +'='+ checkedUrl.join('').slice(0, -1);
        }

    } else {
        valUrl = '';
    }

    return valUrl;
}

function button_action(elm, keyword, sort_default = false) {
    segment     = urlGetSegment();
    segment_key = find_key(keyword, segment);
    
    if(typeof segment_key != 'undefined') {
        segment.splice(segment_key, 1);
    }
    
    segment.push(elm);
    if(sort_default === true){
        var defaultSortVal = $('#sort-by').val();        
        if(typeof defaultSortVal !== 'undefined'){
            var urlSortApply = defaultSortVal;
        }           
        segment.push(urlSortApply);
    }

    fullUri = create_uri(segment);

    return fullUri;
}

function sort_action(elm) {
    segment = urlGetSegment();
    var sort_key;

    for(var i = 0; i < segment.length; i++) {
        /* Kehabisan akal */
        if(segment[i].indexOf('price') == 0 || segment[i].indexOf('pn') == 0 || segment[i].indexOf('discount') == 0 || segment[i].indexOf('popular') == 0 || segment[i].indexOf('recommended') == 0) {
            sort_key = i;
        }
    }    
    if(typeof sort_key != 'undefined') {
        segment.splice(sort_key, 1);
    }    
    segment.push(elm);
    $('#sort-by').val(elm);

    fullUri = create_uri(segment);
    return fullUri;
}

function checkbox_action(elm, keyword, sort_default = false) {
    segment = urlGetSegment();        

    url = getCheckedVal(keyword);
    segment_key = find_key(keyword, segment);

    if(typeof segment_key != 'undefined') {
        segment.splice(segment_key, 1);    
    }

    segment.push(url);
    if(sort_default === true){
        var defaultSortVal = $('#sort-by').val();        
        if(typeof defaultSortVal !== 'undefined'){
            var urlSortApply = defaultSortVal;
        }           
        segment.push(urlSortApply);
    }

    full_uri = create_uri(segment);

    return full_uri;
}

// function ChangeUrl(elm) {
//     var full_uri;
//     var sort            ='';
//     var gender          = '';
//     var segment         = urlGetSegment();
//     var data_gender     = $(elm).data('gender');
//     var color           = $(elm).data('color');
//     var size            = $(elm).data('size');
//     var brand           = $(elm).data('brand');
//     var sort_elm        = $(elm);
    
//     if(typeof data_gender != 'undefined') {
//         var arr_gender = [];
//         arr_gender.push(segment[0]);
//         arr_gender.push(data_gender);
        
//         full_uri = arr_gender.join('?');
//     }
    
// //    if(typeof sort_elm.data('url') != 'undefined') {
// //        full_uri = button_action(sort_elm.data('url'), 'cat');
// //    }

//     /* *
//      * custom default sorting
//      * */
//     var currentUri = window.location.href;
//     var currentPath = null;
//     var available_sort_method = ['popular=desc', 'pn=desc', 'discount=desc', 'price=asc', 'price=desc', 'recommended=asc'];
//     var sort_default = true;
//     var defaultSortVal =  $('#sort-by').val();
//     var urlSortApply = '';
//     if(typeof currentUri.split('?')[1] != 'undefined'){
//         currentPath = currentUri.split('?')[1];            
//         if(currentPath){
//             for(var i = 0; i < available_sort_method.length; i++){
//                 if(currentPath.includes(available_sort_method[i])){
//                     sort_default = false;
//                     break;
//                 }
//             }
//         }            
//     }                            
    
        
    
//     if(typeof sort_elm.data('url') != 'undefined') {
//         //Reset Paging
//         var full_uri = "";
//         for (var key in segment){
//           if (segment.hasOwnProperty(key)) {
//             if(segment[key].includes("page") == false && segment[key].includes("cat") == false){
//               if(key == 0){
//                 full_uri += segment[key];
//               }else {
//                 if(full_uri.includes("?") == false){
//                   full_uri += '?' + segment[key];
//                 }else{
//                   full_uri += '&' + segment[key];
//                 }
//               }
//             }
//           }
//         }
//         //End Reset Paging
        
        
//         if(sort_default === true){
//             if(typeof defaultSortVal !== 'undefined'){
//                 urlSortApply = defaultSortVal + '&';
//             }else{
//                 urlSortApply = 'popular=desc&';
//             }    
//         }                
        
//         if(full_uri.includes("?") == false) {
//           full_uri += '?' + urlSortApply + sort_elm.data('url');          
//         }else{
//           full_uri += '&' + urlSortApply + sort_elm.data('url');              
//         }
//     }
    
//     if(typeof color != 'undefined') {
//         full_uri = checkbox_action(color, 'color', sort_default);        
//     }

//     if(typeof size != 'undefined') {
//         full_uri = checkbox_action(size, 'size', sort_default);   
//     }

//     if(typeof brand != 'undefined') {
//         full_uri = checkbox_action(brand, 'brand', sort_default);
        
//         $("#ul-brand li").show();
//         $("#search_brand").val('');
//     }

//     if(typeof sort_elm.data('sortprice') != 'undefined') {        
//         if(sort_elm.val().indexOf('sprice') == 0) {
//             full_uri = button_action(sort_elm.val(), 'sprice', sort_default);
//         }                
//     }

//     /* action for sorting */
//     //Still a problem temporarily not used
//     if(typeof sort_elm.data('sort') != 'undefined') {
//         if(sort_elm.val().indexOf('price') == 0) {
//             full_uri = sort_action(sort_elm.val());
//         }else if(sort_elm.val().indexOf('pn') == 0) {        
//             full_uri = sort_action(sort_elm.val());
//         }else if(sort_elm.val().indexOf('discount') == 0) {
//             full_uri = sort_action(sort_elm.val());
//         }else if(sort_elm.val().indexOf('popular') == 0) {
//             full_uri = sort_action(sort_elm.val());
//         }else if(sort_elm.val().indexOf('recommended') == 0) {
//             full_uri = sort_action(sort_elm.val());
//         }        
//     }
//     /* --- */

//     /* Inisiate current page */
//     if(typeof sort_elm.data('page') == 'undefined') {
//         var currentPage = 1;
//     } else {
//         var currentPage = sort_elm.data('page');
//         if(currentPage == 1){
//             $('#banner-img').show();    
//         }else{
//             $('#banner-img').hide();
//         }
        
//     }

//     if(typeof sort_elm.data('page') != 'undefined') {
//         var page_num = (sort_elm.data('page') - 1) * 48;
//         if(page_num != 0) {
//             page_num = 'page='+ page_num;    
//         } else {
//             page_num = '';
//         }
        
//         full_uri = button_action(page_num, 'page');
//     }
      
//     if(full_uri.slice(-1) == '?' || full_uri.slice(-1) == '&') {
//         full_uri = full_uri.slice(0, -1);
//     }


//     /* start to run url SEO with pushState */
//     if (typeof (history.pushState) != "undefined") {
//         var obj = { Title: "", Url: full_uri };
//         history.pushState(obj, obj.Title, obj.Url);
//     } else {
//         alert("Browser does not support HTML5.");
//     }
    
//     /* Start to call ajax based on called url */
    
//     action.hit.push(elm);

//     execute_ajax(full_uri, elm, false);
// }

function ChangeUrl(elm) {
    var full_uri;
    var sort            ='';
    var gender          = '';
    var segment         = urlGetSegment();
    var data_gender     = $(elm).data('gender');
    var color           = $(elm).data('color');
    var size            = $(elm).data('size');
    var brand           = $(elm).data('brand');
    var harga           = $(elm).data('harga');
    var sort_elm        = $(elm);
    
    if(typeof data_gender != 'undefined') {
        var arr_gender = [];
        arr_gender.push(segment[0]);
        arr_gender.push(data_gender);
        
        full_uri = arr_gender.join('?');
    }
    
//    if(typeof sort_elm.data('url') != 'undefined') {
//        full_uri = button_action(sort_elm.data('url'), 'cat');
//    }

    /* *
     * custom default sorting
     * */
    var currentUri = window.location.href;
    var currentPath = null;
    var available_sort_method = ['popular=desc', 'pn=desc', 'discount=desc', 'price=asc', 'price=desc', 'recommended=asc'];
    var sort_default = true;
    var defaultSortVal =  $('#sort-by').val();
    var urlSortApply = '';
    if(typeof currentUri.split('?')[1] != 'undefined'){
        currentPath = currentUri.split('?')[1];            
        if(currentPath){
            for(var i = 0; i < available_sort_method.length; i++){
                if(currentPath.includes(available_sort_method[i])){
                    sort_default = false;
                    break;
                }
            }
        }            
    }                            
    
        
    
    if(typeof sort_elm.data('url') != 'undefined') {
        //Reset Paging
        var full_uri = "";
        for (var key in segment){
          if (segment.hasOwnProperty(key)) {
            if(segment[key].includes("page") == false && segment[key].includes("cat") == false){
              if(key == 0){
                full_uri += segment[key];
              }else {
                if(full_uri.includes("?") == false){
                  full_uri += '?' + segment[key];
                }else{
                  full_uri += '&' + segment[key];
                }
              }
            }
          }
        }
        //End Reset Paging
        
        
        if(sort_default === true){
            if(typeof defaultSortVal !== 'undefined'){
                urlSortApply = defaultSortVal + '&';
            }else{
                // urlSortApply = 'pn=desc&';
                urlSortApply = 'popular=desc&';
            }    
        }                
        
        if(full_uri.includes("?") == false) {
          full_uri += '?' + urlSortApply + sort_elm.data('url');          
        }else{
          full_uri += '&' + urlSortApply + sort_elm.data('url');              
        }
    }
    
    if(typeof color != 'undefined') {
        full_uri = checkbox_action(color, 'color', sort_default);        
    }

    if(typeof size != 'undefined') {
        full_uri = checkbox_action(size, 'size', sort_default);   
    }

    if(typeof harga != 'undefined') {
        me = elm;

        // $('#ul-harga > li > input[type=checkbox]').each(function(){
        //     if(me.id != this.id){
        //         $(this).prop('checked', false);
        //     }
        // }); 

        // Remove other selected if current selected equal sprice=all
        $('#ul-harga > li > input[type=radio]').each(function(){
            if(me.value == "sprice=all"){
                if(me.id != this.id){
                    $(this).prop('checked', false);
                    this.setAttribute("harga-param", "false");
                }
            }

            if(me.id == this.id){
                if(me.getAttribute("harga-param") == "false"){
                    me.setAttribute("harga-param", "true"); 
                }else{
                    this.setAttribute("harga-param", "false");
                    $(this).prop('checked', false);
                }
            }
        });

        // Remove selected sprice=all if current selected not equal sprice=all
        $('#ul-harga > li > input:radio:checked').map(function() {
            if(me.value != "sprice=all" && this.value == "sprice=all"){
                $(this).prop('checked', false);
                this.setAttribute("harga-param", "false");
            }
        }).get();

        // $(me).prop('checked', true);
        
        full_uri = checkbox_action(harga, 'sprice', sort_default);

        // full_uri = button_action(sort_elm.val(), 'sprice', sort_default);   
    }

    if(typeof brand != 'undefined') {
        full_uri = checkbox_action(brand, 'brand', sort_default);
        
        $("#ul-brand li").show();
        $("#search_brand").val('');
    }

    if(typeof sort_elm.data('sortprice') != 'undefined') {        
        if(sort_elm.val().indexOf('sprice') == 0) {
            full_uri = button_action(sort_elm.val(), 'sprice', sort_default);
        }                
    }

    /* action for sorting */
    //Still a problem temporarily not used
    if(typeof sort_elm.data('sort') != 'undefined') {
        if(sort_elm.val().indexOf('price') == 0) {
            full_uri = sort_action(sort_elm.val());
        }else if(sort_elm.val().indexOf('pn') == 0) {        
            full_uri = sort_action(sort_elm.val());
        }else if(sort_elm.val().indexOf('discount') == 0) {
            full_uri = sort_action(sort_elm.val());
        }else if(sort_elm.val().indexOf('popular') == 0) {
            full_uri = sort_action(sort_elm.val());
        }else if(sort_elm.val().indexOf('recommended') == 0) {
            full_uri = sort_action(sort_elm.val());
        }        
    }
    /* --- */

    /* Inisiate current page */
    if(typeof sort_elm.data('page') == 'undefined') {
        var currentPage = 1;
    } else {
        var currentPage = sort_elm.data('page');
        if(currentPage == 1){
            $('#banner-img').show();
            $('#banner-html').show();    
        }else{
            $('#banner-img').hide();
            $('#banner-html').hide(); 
        }
        
    }

    if(typeof sort_elm.data('page') != 'undefined') {
        var page_num = (sort_elm.data('page') - 1) * 48;
        if(page_num != 0) {
            page_num = 'page='+ page_num;    
        } else {
            page_num = '';
        }
        
        full_uri = button_action(page_num, 'page');
    }
      
    if(full_uri.slice(-1) == '?' || full_uri.slice(-1) == '&') {
        full_uri = full_uri.slice(0, -1);
    }


    /* start to run url SEO with pushState */
    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: "", Url: full_uri };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        alert("Browser does not support HTML5.");
    }
    
    /* Start to call ajax based on called url */
    
    action.hit.push(elm);

    execute_ajax(full_uri, elm, false);
}

function execute_ajax(full_uri, elm, reload) {
    /* Inisiate current page */
    if(typeof $(elm).data('page') == 'undefined') {
        var currentPage = 1;
    } else {
        var currentPage = $(elm).data('page');
    }

    /* Start to call ajax based on called url */
    $.ajax({
        timeout: 3000,
        dataType: "json",
        url: full_uri,
        type: "get",
        cache: false,
        beforeSend: function() {
            $('#loading').show();
            $('html, body').animate({scrollTop : 0},300);
        },
        success: function(data){
            /* Call all necessary function */
            $('#loading').hide();
            $('.catalog-title').html(data.title);

            append_catalog(data.catalog,data.domain_alias,data.special); 
            
            if(reload == false) {
                if(typeof $(elm).data('url') != 'undefined') {
                    // append_category(data.category);
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
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('page') != 'undefined') {
                    paginate_page(currentPage, data.total_catalog);
                }

                if(typeof $(elm).data('harga') != 'undefined') {
                    page_harga = $_GET('page');

                    if(page_harga !== null){
                        page_harga = (page_harga / 48) + 1;
                        paginate_page(page_harga, data.total_catalog);
                    }else{
                        paginate_page(currentPage, data.total_catalog);
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

function $_GET(param) {
    var vars = {};
    window.location.href.replace( location.hash, '' ).replace( 
        /[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
        function( m, key, value ) { // callback
            vars[key] = value !== undefined ? value : '';
        }
    );

    if ( param ) {
        return vars[param] ? vars[param] : null;    
    }
    return vars;
}

/*
Append html for catalog
*/
function append_catalog(data,domain_alias,special) {
    var root_url   = window.location.origin;
    var segment = urlGetSegment();
    var counter = 1;
    $('#ul-catalog').html('');
    
    var special_page_oos = 0;
    if(typeof(special) !== 'undefined'){
        var special_page_oos = special.special_page_oos;    
    }
    
    //var special_page_oos_category = (special.special_page_oos_category !== null) ? special.special_page_oos_category : null;    
    if(data.length === 0) {
        html = '<div class="pnf-wrapper">';
        html += '<div class="pnf-content">';
        html += '<h1>Kami Mohon Maaf,<br>Produk Yang Ada Cari Tidak Ditemukan.</h1>';
        html += '<p>Lihat koleksi terbaru kami <a href="'+ root_url +'/new-arrival">disini</a></p>';
        html += '</div>';
        html += '</div>';

        $('#ul-catalog').append(html).fadeIn('slow');
    }
    

    $.each(data, function(index, value) {       
        
        /*     append banner catalog */                      
        if(value.isBannerCatalog !== 'undefined' && value.isBannerCatalog == true){
            // html  = '<li id="li-catalog">';
            // html += '<a href="'+ value.FullURLBanner +'">';
            // html += '<img src="'+ value.path_image +'" original="'+ value.path_image +'">';                         
            // html += '</a>';
            // html += '<div class="catalog-detail">';
            // html += '<a href="'+ value.FullURLBanner +'">';
            // html += '<h2 class="catalog-brand">'+ value.text_1 +'</h2>';
            // html += '<h1 class="catalog-name">'+ value.text_2 +'</h1>';
            // html += '<p class="catalog-price">'+ value.text_3 +'</p>';                            
            // html += '</a>';
            // html += '</div>';
            // html += '</li>';

            html += '<li id="li-catalog">';
            html += '<a href="'+ value.FullURLBanner +'">';
            html += '<div class="catalog-image"><img src="https://img.berrybenka.biz/assets/cache/238x358/catalog-banner/'+ value.path_image +'" original="'+ value.path_image +'"></div>;';
            html += 'div class="catalog-detail">';
            html += '<div class="detail-left">';
            html += '<h1>'+ value.text_2 +'</h1></div>';
            html += '<div class="detail-right">';
            html += '<p class="discount">'+ value.text_3 +'</p></div></div></a></li>';
            $('#ul-catalog').append(html).fadeIn('slow');
            return true;
        }
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

        if(segment[1] == 'brand') {
            full_detail_url = detail_url +'?spc_case='+ segment[1] +'&spc_num='+ value.brand_id;
        } else {
            full_detail_url = detail_url +'?spc_case='+ segment[1] +'&spc_num='+ segment[2];
        }

//         html = '<li id="li-catalog" class="'+ value.url_set +'">';
//         // html += '<a href="http://'+ detail_url +'" class="catalog-img">';
//         html += "<a href='http://"+ detail_url +"' class='catalog-img' ";
//         html += "onClick=\"onProductClick("+ value.pid +", '"+ value.product_name +"', '"+ value.url_set +"', '"+ value.brand_name +"', '', "+ counter +", '', '"+ detail_url +"') ;\">";

// //        if(value.discount > 0) {
// //            html += '<div class="disc-flag">';
// //            html += '<div class="disc-wrap">'+ value.discount +'%</div>';
// //            html += '<div class="triangle-topleft left"></div>';
// //            html += '<div class="triangle-topright right"></div>';
// //            html += '<div class="clear"></div></div>';
// //        }
//         if(typeof value.product_overlay_image != 'undefined'){
//             html += '<img alt="" class="overlay-product" src="http://im.onedeca.com/assets/cache/300x456/product-overlay/'+ value.product_overlay_image +'">';
//         }
//         html += '<img src="http://im.onedeca.com/assets/cache/238x358/product/zoom/'+ value.image_name +'">';
       

        html = '<li id="li-catalog">';
        html += "<a href='http://"+ detail_url +"'";
        html += "onClick=\"onProductClick("+ value.pid +", '"+ value.product_name +"', '"+ value.url_set +"', '"+ value.brand_name +"', '', "+ counter +", '"+ detail_url +"')\">";

        html += '<div class="catalog-image" style="position: relative;">';

        if(typeof value.product_overlay_image != 'undefined'){
            html += '<img alt="" style="position: absolute;" src="http://im.onedeca.com/assets/cache/300x456/product-overlay/'+ value.product_overlay_image +'">';
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

            if(special_page_oos == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
               if(typeof special_page_oos_category != 'undefined') {
                   var set_display_oos_special_category_hb = special_page_oos_category;
               } else {
                   var set_display_oos_special_category_hb = '';
               }

               if(set_display_oos_special_category_hb.search(value.type_url) != -1 ) {
                   //html += '<div class="limited-qty">Persediaan Habis</div>';                    
                   html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
               }
                html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
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
            if(special_page_oos == 1  && (value.product_status == 2 || parseInt(value.inventory) == 0)) {
               if(typeof special_page_oos_category != 'undefined') {
                   var set_display_oos_category_bb = special_page_oos_category;
               } else {
                   var set_display_oos_category_bb = '';
               }

               if(set_display_oos_category_bb.search(value.type_url) != -1 ) {                    
                   //html += '<div class="limited-qty">Persediaan Habis</div>';
                   html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
               }
                html += '<div class="sold-wrapper2"><div class="sold-tags3">Habis Terjual</div></div>';
            }

            counter ++;
        }

        html += '</div>';
        /* ------ */

        // html += '</a>';
        // html += '<div class="catalog-detail text-left atw-wrapper">';
        // html += "<a class=\"title-anchor\" href='http://"+ detail_url +"' ";
        // html += "onClick=\"onProductClick("+ value.pid +", '"+ value.product_name +"', '"+ value.url_set +"', '"+ value.brand_name +"', '', "+ counter +", '', '"+ detail_url +"') \">";
        // html += '<h1 class="catalog-brand">'+ ucfirst(value.brand_name) +'</h1>';
        // html += '<h2 class="catalog-name">'+ ucfirst(value.product_name) +'</h2>';

        // if(typeof (value.product_sale_price) != 'undefined' && value.product_sale_price != 0 && !isNaN(value.product_sale_price) != '') {
        //     html += '<p class="catalog-price disc-price"><span>IDR '+ number_format(value.product_price, 0, ',', '.') +'</span>IDR '+ number_format(value.product_sale_price, 0, ',', '.') +'</p>';
        // } else {
        //     html += '<p class="catalog-price">IDR '+ number_format(value.product_price, 0, ',', '.') +'</p>';
        // }
        
        // if(value.discount > 0) {
        //     html += '<div class="disc-tags-bot" style="margin-top:5px;">'+ value.discount +'%</div>';
        // }
        
        // html += '</a>';  
        // html += '</div></li>';


        // 
        html += '<div class="catalog-detail">';
        html += '<div class="detail-left">';
        html += '<h1>'+ ucfirst(value.product_name) +'</h1>';
        if(value.discount > 0) {
            html += '<p>'+ value.discount +'%</p>';
        }

        html += '</div><div class="detail-right">';
        if(typeof (value.product_sale_price) != 'undefined' && value.product_sale_price != 0 && !isNaN(value.product_sale_price) != '') {
            html += '<p>IDR '+ number_format(value.product_price, 0, ',', '.') +'</p><p class="discount">IDR '+ number_format(value.product_sale_price, 0, ',', '.') +'</p>';
        } else {
            html += '<p class="discount">IDR '+ number_format(value.product_price, 0, ',', '.') +'</p>';
        }

        html += '</div></div></a></li>';

        $('#ul-catalog').append(html).fadeIn('slow');
    });
}

/*
Append html for category
*/
function append_category(data) {
    var segment = urlGetSegment();
    var category_val = findUriKey('cat', '--');

    $('#ul-category').html('');
    // var html = '';
    $.each(data, function(index, value) {
        html = '<li>';
        html += '<input type="radio" id="checkbox-category-'+ value.type_url +'" class="category-filter parentCheckBox" data-level="parent" name="category_level1" data-url="cat='+ value.type_url +'" onclick="ChangeUrl(this)"'

        if(typeof category_val != 'undefined') {
            var get_key = category_val.indexOf(value.type_url);
            if(value.type_url == category_val[get_key]) {
                html += 'checked="checked"';
            }
        }

        html += '>';
        html += '<label for="checkbox-category-'+ value.type_url +'">'+ value.type_name_bahasa +'</label>';
        html += '<ul>';

        if(value.child != null){
            $.each(value.child, function(val_index, child) {
                html += '<li><input type="radio" id="checkbox-category-'+ child.type_url +'" class="category-filter childCheckBox" data-level="child" name="category_level2" onclick="ChangeUrl(this)" data-url="cat='+ child.type_url +'">';
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
    var color_val = findUriKey('color', '--');

    $.each(data, function(index, value) {
        html = '<li>';
        html += '<input type="checkbox" id="checkbox-'+ value.color_name +'" class="color-filter '+ value.color_name +'" onclick="ChangeUrl(this)" data-color="true" value="'+ value.color_name +'"';
        
        if(typeof color_val != 'undefined') {
            var get_key = color_val.indexOf(value.color_name);
            if(value.color_name == color_val[get_key]) {
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
    var size_val = findUriKey('size', '-');

    $.each(data, function(index, value) {
        if(value.product_size_url != '' && typeof value.product_size_url != 'undefined') {
            html = '<li><div>';
            html += '<input type="checkbox" id="size-'+ value.product_size +'" class="size-filter '+ value.product_size +'" onclick="ChangeUrl(this)" data-size="true" value="'+ value.product_size_url +'"';
            
            if(typeof size_val != 'undefined') {
                var get_key = size_val.indexOf(value.product_size);
                if(value.product_size == size_val) {
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
    var brand_val = findUriKey('brand', '--');

    if(typeof data != 'undefined') {
        $.each(data, function(index, value) {
            var brand_url = (typeof value.brand_url != 'undefined') ? value.brand_url : '';

            html = '<li>'
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
}

function paginate_page(current_page, total_page) {
    $('#ul-pagination').html('');
    var html = '';

    var page = (current_page - 1) * 48;
    var get_rest_page   = (total_page - page) / 48;
    var get_rest_page   = parseInt(get_rest_page) + 1;
    var rest_page       = (get_rest_page <= 4) ? get_rest_page : 4;

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
            html += '<li class="active">'+ current_page +'</li>';
        }

        for(i = (current_page + 1); i < right_links; i++) {
            if(i <= total_page) {
                html += '<li><a data-page="'+ i +'" href="javascript:void(0);" onclick="ChangeUrl(this)">'+ i +'</a></li>';
            }
        }

        if(current_page < total_page && rest_page > 1) {
            var next_link = current_page+1;

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
    // $(".filter-price .price").click(function(){
    //     $('#sort-by-price').attr('size',7);
    //     $('.filter-price .fa-angle-down').hide();
    // });

    // $(".filter-price #sort-by-price").mouseleave(function(){
    //     $('.filter-price .fa-angle-down').show();
    //     $('.filter-price #sort-by-price').removeAttr('size');
    // });
    // /* event for brand search */
    // $("#search_brand").keyup(function(){

    //     // Retrieve the input field text and reset the count to zero
    //     var filter = $(this).val(), count = 0;
 
    //     // Loop through the comment list
    //     $("#ul-brand li").each(function(){
 
    //         // If the list item does not contain the text phrase fade it out
    //         if ($(this).text().search(new RegExp(filter, "i")) < 0) {
    //             $(this).fadeOut();
 
    //         // Show the list item if the phrase matches and increase the count by 1
    //         } else {
    //             $(this).show();
    //             count++;
    //         }
    //     });
    // });

    // /* Group all checked checkbox to top */
    // $('#ul-brand').on('click', ':checkbox', function() {
    //     var list = $('#ul-brand');
    //     var i, checked = document.createDocumentFragment(),
    //     unchecked = document.createDocumentFragment();
    //     var origOrder = list.children();

    //     for (i = 0; i < origOrder.length; i++) {
    //         if (origOrder[i].getElementsByTagName("input")[0].checked) {

    //             checked.appendChild(origOrder[i]);
    //         } else {
    //             unchecked.appendChild(origOrder[i]);
    //         }
    //     }

    //     list.append(checked).append(unchecked);
    // });

    $('.clear-filter').click(function() {
        var url = window.location.pathname;
        var max_hit = action.hit.length - 1;
        var hitted = action.hit[max_hit];

        $("input[name='gender']").prop('checked', false);

        /* start to run url SEO with pushState */
        if (typeof (history.pushState) != "undefined") {
            var obj = { Title: "", Url: url };
            history.pushState(obj, obj.Title, obj.Url);
        } else {
            alert("Browser does not support HTML5.");
        }

        execute_ajax(url, hitted, true);
    })
});