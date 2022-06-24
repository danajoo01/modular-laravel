var _gaq = _gaq || [];

function selectcolor(val) {
	var variant_color_id = val;		
	$.get("/product/get_image_color", $("#frmAddCart").serialize(), 
	function(data){

		var get_image = fetch_image_html(data);
		var get_size = fetch_size_html(data);

		$("#images-selected").show().html(get_image);
		$("#select-size").show().html(get_size);
		$("#selectsku").hide();
		$("#variant_color_id").val(variant_color_id);
	});
}

function getSKU(sku) {
	$("#selectsku").hide();
	var sku = sku;
	$('#selectsku').show().empty().append('<strong>SKU</strong>: '+ sku + ' <input type="hidden" name="SKU" id="SKU" value="'+ sku + '">');
		
}

function setCart(elem) {		
	var weight = document.getElementById("weight");
	var qty = parseInt($("#quantity").val());
	var quantity = $("#itm_tot").val();
	var new_qty = parseInt(qty) + parseInt(quantity);
	var brand_id = $("#brand_id").val();
	var sku = $('#SKU').val();
	var item = $('#SKU').next().next().next('#product_inv');
	
	if (typeof sku == "undefined"){
		var stat = false;
		//alert('Please Select Your Size');
		$('#success-back,#error-stock-back,#error-color-back,#error-colorsize-back,#error-manetail-back').hide();
		$( "#error-size-back" ).show( "slow" );
		$("html, body").animate({ scrollTop: 0 }, "slow");
			return false;
	} else {
		var stat = true;
	}
			
	$.get("/product/addtocart", $("#frmAddCart").serialize(), 
	function(data) {
		var obj = jQuery.parseJSON(data);
		var statusAddCart = obj.bags;
		var t_qty = obj.t_qty;
		var errormsg = obj.errormsg;
		
		$("#bags-list").html(obj.bags);
		$("#bags-list-float").html(obj.bags);

		if (errormsg == "size_null"){
			var stat = false;
			//alert('Please Select Your Size');
			$('#success-back,#error-stock-back,#error-color-back,#error-colorsize-back,#error-manetail-back').hide();
			$( "#error-size-back" ).show( "slow" );
			$("html, body").animate({ scrollTop: 0 }, "slow");
				return false;
		} 
		
		if(statusAddCart!=null  && stat!= false) {
			$('#error-stock-back,#error-size-back,#error-color-back,#error-colorsize-back').hide();
			$("#success-back").show( "slow" );
			$("html, body").animate({ scrollTop: 0 }, "slow");
			$(".add2cart").click();
			$(".cart-list-wrapper").addClass('show-cart');
			// $(".nav-checkout").css({ display: "block" });
			if(t_qty > 0){
				$("#tot_itm").html("(" + t_qty + ")");
				$(".addtocart").show();
			}else{
				$("#tot_itm").html("");
				$(".addtocart").hide();
			}

			$("#itm_tot").replaceWith('<input type="hidden" id="itm_tot" value="' +  t_qty +'">');
			
			return false;										  
			//location.reload();
		} else if(statusAddCart == false && stat!= false) {
			//alert('Product Out of Stock!');	
			$('#success-back,#error-size-back,#error-color-back,#error-colorsize-back').hide();
			$( "#error-stock-back" ).show( "slow" );
			$("html, body").animate({ scrollTop: 0 }, "slow");
			return false;
		}
	});
	
	
  	//* Set AddToCart pixel event 
	/*
	var product_id = $('#product_id').val();
	window._fbq = window._fbq || [];
	window._fbq.push(['track', 'AddToCart', {
		content_ids: [product_id],
		content_type: 'product'
	}]);
	*/
}

function set_wishlist(el) {
	var fullPathUrl = window.location.pathname + window.location.search;
	var link_login = "/login?continue=" + encodeURIComponent(fullPathUrl);
	var product_id = $(el).attr('rel');
	var dataString = 'product_id=' + product_id + '&type=1';
	
	$.ajax({
            type: "GET",
            url: "/product/set_wishlist",
            data: dataString,
            dataType: "json",
            success: function (data) {
                if (data == 'success') {
                    link_login = '/user/wishlist';
                    $('#wishlist-info').empty().append('<span class="success-msg"><i aria-hidden="true" class="fa fa-times"></i> Anda Berhasil Menambahkan Produk Ke Dalam Wishlist Anda. <a class="text-underline" href="' + link_login + '">Klik Disini</a> Untuk Melihat Wishlist Anda.</span>');
                    $(el).removeClass("fa-heart-o");
                    $(el).addClass("fa-heart");
                } else if (data == 'exist') {
                    $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>  Error!! Produk Anda Sudah Disimpan Sebelumnya.</span>');
                } else if (data == 'not-login') {
                    $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Silakan <a class="text-underline" href="'+ link_login +'"> Klik Disini</a> Untuk Login Terlebih Dahulu Sebelum Menggunakan Wishlist.</span>');
                } else {
                    $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>  Error!! Produk Tidak Bisa Disimpan. Coba Ulangi Kembali.</span>');
                }
            }
	});
	$("html, body").animate({ scrollTop: 0 }, 300);
}

function fetch_image_html(data) {
	var html = "";

	// html += '<div class="big-photo left"><a class="fancybox-effects-c" href="'+ data.asset_path +'upload/product/zoom/'+ data.get_image_def.image_name +'"><img src="'+ data.asset_path +'upload/product/zoom/'+ data.get_image_def.image_name +'"></a></div>';
	// html += '<div class="small-photo left"><ul>';

	// $.each(data.get_imagesSolr, function(index, rows) {
	// 	if (rows.id != data.get_image_def.id) {
	// 		html += '<li><a class="fancybox-effects-c" href="'+ data.asset_path +'upload/product/zoom/'+ rows.image_name +'"><img src="'+ data.asset_path +'upload/product/zoom/'+ rows.image_name +'"></a></li>';
	// 	}
	// });
	
	html += '<li><a href="'+ data.asset_path +'upload/product/zoom/'+ data.get_image_def.image_name +'" data-featherlight="image"><img src="'+ data.asset_path +'upload/product/zoom/'+ data.get_image_def.image_name +'"></a></li>';

	
	$.each(data.get_imagesSolr, function(index, rows) {
		if (rows.id != data.get_image_def.id) {
			html += '<li><a href="'+ data.asset_path +'upload/product/zoom/'+ rows.image_name +'" data-featherlight="image"><img src="'+ data.asset_path +'upload/product/zoom/'+ rows.image_name +'"></a></li>';
		}
	});

	// html += '</ul></div>';

	return html;
}

function fetch_size_html(data) {
	var html = "";
	var total_inventory = 0;
	var disabled = "";
	var style = "";
	var tooltip_style = "";
	var get_sku = '';
        var titleoos = '';

	html += '<ul>';
	$.each(data.get_sizeSolr, function(index, rows) {
		if(rows.inventory <= 0) {
                    titleoos = 'title="Habis Terjual"';
                    get_sku = "";
                    disabled = "disabled";
                    style = "style='background-color: #dedede;'"
                    tooltip_style = 'style="color:red"';
		}else{
                    titleoos = '';
                    get_sku = 'onclick="getSKU(this.id); _gaq.push([\'_trackEvent\',\'Product\',\'Button\',\'sizeSelect\']);"';
                    disabled = "";
                    style = "";
                    tooltip_style = "";
                }
		
		html += '<li '+ titleoos +'>\n\
                            <div '+ style +'><input type="radio" name="size_category" value="'+ rows.product_size +'" id="size-'+ rows.product_size_url +'" class="size-filter size-'+ rows.product_size_url +'" '+ disabled +'>\n\
                                <label for="size-'+ rows.product_size_url +'" id="'+ rows.product_sku +'" '+ get_sku +'>'+ rows.product_size +'</label>\n\
                            </div>\n\
                        </li>';
		total_inventory = total_inventory + rows.inventory;
    });

    /*if (total_inventory <= 0) {
    	html += "<span style='font:bold; color:red'>Maaf, Stok Barang Habis</span><br>";
    }*/

	html += '</ul>';
	html += '<input type="hidden" name="variant_color_name" id="variant_color_name" value="'+ data.color_name +'">';
	html += '<input type="hidden" name="image_name" id="image_name" value="'+ data.get_image_def.image_name +'">';

	return html;
}