$(document).ready(function () {
  var ajax_url = $('#ajax_url').val();
  var url = $('#img_path').val();
  var global_inv_status = true;

  //Function
  var load_cart = function () {
    $.ajax({
      url: ajax_url + '/checkout/json_load_cart',
      type: 'post',
      data: {
      },
      beforeSend: function () {
        $('#loading_cart').show();
        $('#error-msg-container').empty();
        $('#error-msg-container').hide();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        var grand_total = 0;
        global_inv_status = true;
        var inv_err_msg = [];
        var inv_status = "";
        var add_stock_btn = "";
        var min_stock_btn = "";
        var tag_value = [];
        var index = 0;

        $('#cart-container').empty();
        $('#cart-container').append('\
          <tr>\
            <td colspan="2">PRODUK</td>\
            <td width="20%">HARGA</td>\
            <td width="10%">JUMLAH</td>\
            <td width="22%">TOTAL</td>\
            <td width="6%"></td>\
          </tr>\
        ');

        if (obj.total_cart > 0) {
          $.each(obj.cart, function (key, value) {

            //Check Inventory
            if(value.inv_qty <= 0){
              inv_status = "zero-stock";
              add_stock_btn = "disabled";
              min_stock_btn = "disabled";
              global_inv_status = false;
              inv_err_msg.push("Maaf maksimum stock barang dengan SKU <strong>" + value.SKU + "</strong> sudah habis. Mohon hapus produk tersebut dari cart anda.");
            }else{
              if(value.inv_status == 1){
                inv_status = "";
                add_stock_btn = "";
              }else if(value.inv_status == 2){
                inv_status = "min-stock";
                add_stock_btn = "disabled";
                global_inv_status = false;
                inv_err_msg.push("Maaf maksimum stock barang dengan SKU <strong>" + value.SKU + "</strong> adalah " + value.inv_qty +". Mohon kurangi jumlah pembelian produk tersebut.");
              }
            }
            //End Check Inventory
            // if(domain == 1){
            //   var body_param = '<h1>' + value.name.toUpperCase() + '</h1>\
            //         <p>SKU : ' + value.SKU + '</p>\
            //         <p>color : ' + value.color_name + '</p>\
            //         <p>Size : ' + value.size + '</p>';
            // }else{
            //   var body_param = '<h1>' + value.name.toUpperCase() + '</h1>\
            //         <h2>' + value.brand_name.toUpperCase() + '</h2>\
            //         <p>SKU : ' + value.SKU + '</p>\
            //         <p>color : ' + value.color_name + '</p>\
            //         <p>Size : ' + value.size + '</p>';
            // }

            var body_param = '<h1>' + value.name.toUpperCase() + '</h1>\
            <p>SKU : ' + value.SKU + '</p>\
            <p>color : ' + value.color_name + '</p>\
            <p>Size : ' + value.size + '</p>';

            $('#cart-container').append('\
              <tr class="'+inv_status+'">\
                <td width="10%">\
                  <div class="cart-list-img">\
                    <a href="'+value.url+'">\
                      <img src="' + url + value.image + '">\
                    </a>\
                  </div>\
                </td>\
                <td width="30%">\
                  <div class="product-name">' + body_param + '\
                  </div>\
                </td>\
                <td width="20%"><div class="cart-price">IDR ' + number_format(value.price) + '</div></td>\
                <td width="10%">\
                  <div class="cart-qty clearfix">\
                    <input type="text" disabled="disabled" readonly value="' + value.qty + '" name="qty-val" id="qty-value-' + value.SKU + '">\
                    <input type="button" class="add-qty" value="+" sku="' + value.SKU + '" '+add_stock_btn+'>\
                    <input type="button" class="min-qty" value="-" sku="' + value.SKU + '" '+min_stock_btn+'>\
                  </div>\
                </td>\
                <td width="20%"><div id="subtotal-value-' + value.SKU + '" class="total-qty">IDR ' + number_format(value.subtotal) + '</div></td>\
                <td width="10%">\
                  <a class="modal-remove" sku="' + value.SKU + '" href="#"><i class="fa fa-trash-o"></i></a>\
                  <div id="remove-box-' + value.SKU + '" class="ask-box clearfix remove-box" style="display:none;">\
                    <span>Hapus Produk ini ?</span>\
                      <ul>\
                        <li><a class="remove-cart" href="#" sku="' + value.SKU + '">Ya</a></li>\
                        <li><a class="close-box" href="#">Tidak</a></li>\
                      </ul>\
                  </div>\
                </td>\
              </tr>\
            ');
            tag_value[index] = {'id' : value.SKU, 'price' : value.price, 'quantity' : value.qty};
            grand_total = parseInt(grand_total) + parseInt(value.subtotal);
            index ++;
          });
          
          tag_val336CC993E54E = JSON.stringify(tag_value);
          // console.log(tag_value);
          // console.log(tag_json);

          $('#grandtotal-value').html('IDR ' + number_format(grand_total) + ',-');
          $('#raw-grandtotal-value').val(grand_total);
          $('#tot_itm').html("("+obj.total_cart+")");
          $('#itm_tot').val(obj.total_cart);

          //Show Inventory Error
          if(!global_inv_status){
            $.each(inv_err_msg, function (key, value) {
              $('#error-msg-container').append('<span class="error-msg-login stock-alert span-alert"><i aria-hidden="true" class="fa fa-bell"></i>'+value+'</span>');
            });
            $('#error-msg-container').show('slow');
            $("#cart-wrapper").scrollTop();
          }
          //End Show Inventory Error

          set_button();
        } else {
          $('#cart-container').append('\
            <tr>\
              <td colspan="6" style="text-align:left">\
                <span style="margin-left:7px;">Keranjang anda kosong.</span>\
              </td>\
            </tr>\
          ');
          $('#grandtotal-value').html('IDR 0,-');
          $('#tot_itm').html("");
          $('#itm_tot').val(obj.total_cart);
          $(".addtocart").hide();
        }

        $('#loading_cart').hide();
      }
    });
  };

  var update_cart = function (SKU, quantity, is_delete) {
    console.log('halo');
    $.ajax({
      url: ajax_url + '/checkout/json_update_cart',
      type: 'post',
      data: {
        "SKU": SKU,
        "quantity": quantity,
        "is_delete": is_delete
      },
      beforeSend: function () {
        $('#loading_cart').show();
        $('#error-msg-container').empty();
        $('#error-msg-container').hide();
        $('.remove-box').hide('fast');
      },
      success: function (result) {
        $('#loading_cart').hide();
        load_cart();

        var obj = jQuery.parseJSON(result);
        if(!obj.result){
          $('#error-msg-container').append('<span class="error-msg-login stock-alert span-alert"> <i aria-hidden="true" class="fa fa-bell"></i> <i aria-hidden="true" class="fa fa-times close-alert"></i> Maaf stock barang dengan SKU '+SKU+' tidak mencukupi</span>');
          $('#error-msg-container').show('slow');

          $('html, body').animate({
            scrollTop: $("#cart-wrapper").offset().top
          }, 500);
        }
      }
    });
  };

  var set_button = function () {
    $(document).off('click', '.add-qty');
    $(document).on('click', '.add-qty', function () {
      var sku = $(this).attr("sku");
      var qty = parseInt($('#qty-value-'+sku).val())+parseInt(1);
      update_cart(sku, qty, 0);
    });

    $(document).off('click', '.min-qty');
    $(document).on('click', '.min-qty', function () {
      var sku = $(this).attr("sku");
      var qty = parseInt($('#qty-value-'+sku).val())-parseInt(1);
      update_cart(sku, qty, 0);
    });

    $(document).off('click', '.modal-remove');
    $(document).on('click', '.modal-remove', function (event) {
      event.preventDefault();
      $('.remove-box').hide('fast');
      var sku = $(this).attr("sku");
      $('#remove-box-' + sku).show('fast');
    });

    $(document).off('click', '.remove-cart');
    $(document).on('click', '.remove-cart', function (event) {
      event.preventDefault();
      var sku = $(this).attr("sku");
      update_cart(sku, 0, 1);
    });

    $(document).off('click', '.close-box');
    $(document).on('click', '.close-box', function (event) {
      event.preventDefault();
      $('.remove-box').hide('fast');
    });

    $(document).off('click', '.close-alert');
    $(document).on('click', '.close-alert', function (event) {
      event.preventDefault();
      $('.span-alert').hide('fast');
    });
  };
  //End Function

  //User Action
  $('#btn-checkout').click(function(event){
    event.preventDefault();
    if(!global_inv_status){
      $('html, body').animate({
        scrollTop: $("#cart-wrapper").offset().top
      }, 500);
    }else{
      window.location.href = ajax_url + '/checkout/submit_order';
    }
  });
  //End User Action

  //Initial State
  load_cart();
  //End Initial State
});
