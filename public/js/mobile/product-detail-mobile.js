	var _gaq = _gaq || [];
	
	function selectcolor(val) {
		var variant_color_id = val;		
		$.get("/product/get_image_color_mobile", $("#frmAddCart").serialize(), 
		function(data){
			var obj = jQuery.parseJSON( data );
			$("#images-selected").show().html(obj.image);
			$('.flexslider').removeData("flexslider");
			$('.flex-control-nav').remove();
			$('.flexslider').flexslider({
				animation: "fade",
				start: function(slider){
				  $('body').removeClass('loading');
				}
			});
			$("#select-size").show().html(obj.size);
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
		// if(getCookie(sku) == "") {
			// createCookie(sku, parseInt(item.val()));
		// };		
		
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

			//alert(data);
			//console.log(data);
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
				$("#bags-item a").click();
				$("#bags-item").addClass('open');
				$(".nav-checkout").css({ display: "block" });
				// $("#tot_itm").html(new_qty);
				// $("#itm_tot").replaceWith('<input type="hidden" id="itm_tot" value="' +  new_qty +'">');
				$("#tot_itm").html(t_qty);
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
		
		/*
      	//* Set AddToCart pixel event 
		var product_id = $('#product_id').val();
		window._fbq = window._fbq || [];
		window._fbq.push(['track', 'AddToCart', {
			content_ids: [product_id],
			content_type: 'product'
		}]);
		*/
	}
