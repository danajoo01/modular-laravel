
$(document).ready(function(){
  $('input[name="rad-payment-method"]').attr('checked', false);
  
  var ajax_url = $('#ajax_url').val();

  //Function
  var check_login = function(obj){
    if(obj.result == false && obj.need_refresh == true){
      location.reload();
    }
  };
  
  var load_primary_address = function () {
    var payment_method = $('input[rad-payment-method]:checked').val();
    $.ajaxq ('Order', {
      url: ajax_url + '/checkout/json_get_customer_address',
      type: 'post',
      data: {
        get_primary: true
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#txt-primary-shipping-street').html('Loading ...');
        $('#txt-primary-shipping-city').html('Loading ...');
        $('#txt-primary-shipping-phone').html('Loading ...');
        $('#txt-primary-billing-street').html('Loading ...');
        $('#txt-primary-billing-city').html('Loading ...');
        $('#txt-primary-billing-phone').html('Loading ...');
        $('#shipping-method-container').empty();
        $('input[rad-payment-method]').prop('checked', false);
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        //Check whether customer have address or not
        if(obj.have_address){
          $('#address-container').show();
          $('#div-shipping-method-container').show();
          $('#new-address-container').hide();

          //Generate Primary Address
          $.each(obj.list_customer_address, function (key, value) {
            if(value.address_type == 1){
              $('#txt-primary-shipping-street').html(value.address_street+' - '+value.address_postcode);
              $('#txt-primary-shipping-city').html(value.address_city+' - '+value.address_province);
              $('#txt-primary-shipping-phone').html('Nomor Handphone: '+value.address_phone);
            }else{
              $('#txt-primary-billing-street').html(value.address_street+' - '+value.address_postcode);
              $('#txt-primary-billing-city').html(value.address_city+' - '+value.address_province);
              $('#txt-primary-billing-phone').html('Nomor Handphone: '+value.address_phone);
            }
          });
          //End Generate Primary Address

          //Generate Shipping Method
          if(obj.list_shipping_method != null){
            var temp_checked = '';
            var temp_disabled = '';
            $.each(obj.list_shipping_method, function (key, value) {
              temp_checked = value.is_primary ? 'checked' : '' ;
              temp_disabled = value.is_available ? '' : 'disabled' ;
              $('#shipping-method-container').append('\
                <p>\
                  <label>\
                    <input type="radio" name="rad-shipping-method" shipping_id="'+value.id+'" type="'+value.shipping_type+'" '+temp_checked+' '+temp_disabled+'>\
                    <span class="span-shipping-method" shipping_id="'+value.id+'" type="'+value.shipping_type+'" '+temp_disabled+'>'+value.text+'</span>\
                  </label>\
                </p>\
              ');
            });
          }
          //End Generate Shipping Method

          //Check COD
          if(obj.is_cod_available == true){
            $('#cod-container').show();
          }else{
            $('#cod-container').hide();
          }
          //End Check COD

          //Check Popup Store
          $('.popup-store-container').hide();
          if (jQuery.isEmptyObject(obj.is_popup_store_available) == false) {
            $.each(obj.is_popup_store_available, function (key, value) {
              var master_payment_id = obj.is_popup_store_available[key]['master_payment_id'];
              $('#popup-store-'+master_payment_id).show();
            });
          }
          //End Check Popup Store
        }else{
          $('#address-container').hide();
          $('#div-shipping-method-container').hide();
          $('#new-address-container').show();
        }
        //End Check whether customer have address or not

        generate_grand_total(obj);
        set_button();
        $('#loading_gif').hide();
      }
    });
  };

  var load_list_address = function (address_type) {
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_get_customer_address',
      type: 'post',
      data: {
        address_type: address_type
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#list-address-container').empty();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.have_address){
          $.each(obj.list_customer_address, function (key, value) {
            var display_primary = "";
            var margin_edit = "";
            if(value.is_primary == 1){
              display_primary = 'style="display:none;"';
              margin_edit = 'style="margin-left:0 !important;"';
            }
            
            $('#list-address-container').append('\
              <li>\
                <div class="radio-address">\
                  <input type="radio" name="RadioGroup3" value="radio" id="alamat3">\
                  <div class="address">\
                    <label for="alamat3">\
                      <span></span>\
                      <p>'+value.address_street+' - '+value.address_postcode+'</p>\
                      <p>'+value.address_city+' - '+value.address_province+'</p>\
                      <p>Nomor Handphone: '+value.address_phone+'</p>\
                      <p>\
                        <input '+display_primary+' class="set-primary-address" address_id="'+value.address_id+'" address_type="'+address_type+'" type="submit" value="Atur Sebagai Alamat Utama">\
                        <a '+margin_edit+' class="edit-current-address" href="#" address_id="'+value.address_id+'" address_type="'+address_type+'">Ubah Alamat</a>\
                      </p>\
                    </label>\
                  </div>\
                </div>\
              </li>\
            ');
          });

          set_button();
        }
        $('#loading_gif').hide();
        $.fancybox("#list-alamat");
        $.fancybox.update();
      }
    });
  };

  var load_city = function(shipping_area, type){
    //Type 1: Add Address | 2: Edit Address | 3: New Shipping Address | 4: New Billing Address
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_get_shipping_list',
      type: 'post',
      data: {
        type: 1,
        shipping_area: shipping_area
      },
      beforeSend: function () {
        if(type == 1){
          $('#add-address-city').empty();
          $('#add-address-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }else if(type == 2){
          $('#edit-address-city').empty();
          $('#edit-address-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }else if(type == 3){
          $('#new-shipping-city').empty();
          $('#new-shipping-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }else if(type == 4){
          $('#new-billing-city').empty();
          $('#new-billing-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(type == 1){
          $('#add-address-city').empty();
          $('#add-address-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#add-address-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }else if(type == 2){
          $('#edit-address-city').empty();
          $('#edit-address-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#edit-address-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }else if(type == 3){
          $('#new-shipping-city').empty();
          $('#new-shipping-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#new-shipping-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }else if(type == 4){
          $('#new-billing-city').empty();
          $('#new-billing-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#new-billing-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }
      }
    });
  };

  var load_detail_address = function(address_id){
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_get_customer_address',
      type: 'post',
      data: {
        address_id: address_id
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-success').hide();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        var address_id = obj.list_customer_address[0].address_id;
        var address_type = obj.list_customer_address[0].address_type;
        var address_street = obj.list_customer_address[0].address_street;
        var address_province = obj.list_customer_address[0].address_province;
        var address_city = obj.list_customer_address[0].address_city;
        var address_postcode = obj.list_customer_address[0].address_postcode;
        var address_phone = obj.list_customer_address[0].address_phone;

        $('#edit-address-id').val(address_id);
        $('#edit-address-type').val(address_type);
        $('#edit-address-street').val(address_street);
        $('#edit-address-province').val(address_province);
        $('#edit-address-postcode').val(address_postcode);
        $('#edit-address-phone').val(address_phone);

        //Load City
        $.ajaxq('Order', {
          url: ajax_url + '/checkout/json_get_shipping_list',
          type: 'post',
          data: {
            type: 1,
            shipping_area: address_province
          },
          beforeSend: function () {
            $('#edit-address-city').empty();
            $('#edit-address-city').append('\
              <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
            ');
          },
          success: function (result) {
            var obj = jQuery.parseJSON(result);
            check_login(obj);
            
            $('#edit-address-city').empty();
            $('#edit-address-city').append('\
              <option value="" disabled="disabled" selected="selected">Kota</option>\
            ');
            $.each(obj.list_shipping, function (key, value) {
              $('#edit-address-city').append('\
                <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
              ');
            });
            $('#edit-address-city').val(address_city);
            $('#loading_gif').hide();
          }
        });
        //End Load City
      }
    });
  };

  var set_primary_address = function(address_id, address_type){
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_set_primary_address',
      type: 'post',
      data: {
        address_id: address_id,
        address_type: address_type
      },
      beforeSend: function () {
        $('#loading_gif').show();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.result){
          $('.close-list-alamat').trigger('click');
          load_primary_address();
          
          $('#msg-container').addClass("success-msg");
          $('#msg-container').removeClass("error-msg-login");
          $('#msg-content').html("Ubah Alamat Utama Berhasil.");
          
          $('#msg-container').show();
          $("html, body").animate({ scrollTop: 0 }, "slow");
        }
        
        //Reapply Freegift Auto
        generate_freegift_auto(obj.freegift_auto);
        
        //Reapply Voucher
        generate_voucher(obj.voucher);
        
        //Reapply Freegift
        generate_freegift(obj.freegift);
      }
    });
  };

  var new_address = function(){
    var multi_address = 0;
    if($("#tambah-alamat-checkbox").is(':checked')){
      multi_address = 1;
    }

    var shipping_street = $('#new-shipping-street').val();
    var shipping_province = $('#new-shipping-province').val();
    var shipping_city = $('#new-shipping-city').val();
    var shipping_postcode = $('#new-shipping-postcode').val();
    var shipping_phone = $('#new-shipping-phone').val();

    var billing_street = $('#new-billing-street').val();
    var billing_province = $('#new-billing-province').val();
    var billing_city = $('#new-billing-city').val();
    var billing_postcode = $('#new-billing-postcode').val();
    var billing_phone = $('#new-billing-phone').val();

    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_new_customer_address',
      type: 'post',
      data: {
        multi_address: multi_address,
        shipping_street: shipping_street,
        shipping_province: shipping_province,
        shipping_city: shipping_city,
        shipping_postcode: shipping_postcode,
        shipping_phone: shipping_phone,
        billing_street: billing_street,
        billing_province: billing_province,
        billing_city: billing_city,
        billing_postcode: billing_postcode,
        billing_phone: billing_phone
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-success').hide();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.result){
          load_primary_address();
          $('#new-address-container').hide();
          $('#address-container').show();
        }else{
          $('#new-address-alert').show();
          $('#new-address-alert').html('<i class="fa fa-bell" aria-hidden="true"></i> Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var add_address = function(){
    var address_type = $('#add-address-type').val();
    var address_street = $('#add-address-street').val();
    var address_province = $('#add-address-province').val();
    var address_city = $('#add-address-city').val();
    var address_postcode = $('#add-address-postcode').val();
    var address_phone = $('#add-address-phone').val();

    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_add_customer_address',
      type: 'post',
      data: {
        address_type: address_type,
        address_street: address_street,
        address_province: address_province,
        address_city: address_city,
        address_postcode: address_postcode,
        address_phone: address_phone
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-success').hide();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.result){
          $('.cancel-add-address').trigger("click");
          $.ajaxq('Order', {
            url: ajax_url + '/checkout/json_get_customer_address',
            type: 'post',
            data: {
              address_type: address_type
            },
            beforeSend: function () {
              $('#list-address-container').empty();
            },
            success: function (result) {
              var obj = jQuery.parseJSON(result);
              $.each(obj.list_customer_address, function (key, value) {
                $('#list-address-container').append('\
                  <li>\
                    <div class="radio-address">\
                      <input type="radio" name="RadioGroup3" value="radio" id="alamat3">\
                      <div class="address">\
                        <label for="alamat3">\
                          <span></span>\
                          <p>'+value.address_street+' - '+value.address_postcode+'</p>\
                          <p>'+value.address_city+' - '+value.address_province+'</p>\
                          <p>Nomor Handphone: '+value.address_phone+'</p>\
                          <p><input class="set-primary-address" address_id="'+value.address_id+'" address_type="'+address_type+'" type="submit" value="Atur Sebagai Alamat Utama">\
                          <a class="edit-current-address" href="#" address_id="'+value.address_id+'">Ubah Alamat</a></p>\
                        </label>\
                      </div>\
                    </div>\
                  </li>\
                ');
              });

              set_button();
              $('#loading_gif').hide();
              $('#alert-description').html('Sukses Menambah Alamat');
              $('#address-success').show();
              $.fancybox.update();

              $('#add-address-type').val('');
              $('#add-address-street').val('');
              $('#add-address-province').val('');
              $('#add-address-city').val('');
              $('#add-address-postcode').val('');
              $('#add-address-phone').val('');
            }
          });
        }else{
          $('#add-address-alert').show();
          $('#add-address-alert').html('<i class="fa fa-bell" aria-hidden="true"></i> Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var edit_address = function(){
    var address_id = $('#edit-address-id').val();
    var address_type = $('#edit-address-type').val();
    var address_street = $('#edit-address-street').val();
    var address_province = $('#edit-address-province').val();
    var address_city = $('#edit-address-city').val();
    var address_postcode = $('#edit-address-postcode').val();
    var address_phone = $('#edit-address-phone').val();

    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_edit_customer_address',
      type: 'post',
      data: {
        address_id: address_id,
        address_street: address_street,
        address_province: address_province,
        address_city: address_city,
        address_postcode: address_postcode,
        address_phone: address_phone
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-success').hide();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.result){
          $('.back-list-alamat').trigger("click");
          $.ajaxq('Order', {
            url: ajax_url + '/checkout/json_get_customer_address',
            type: 'post',
            data: {
              address_type: address_type
            },
            beforeSend: function () {
              $('#list-address-container').empty();
            },
            success: function (result) {
              var obj = jQuery.parseJSON(result);
              $.each(obj.list_customer_address, function (key, value) {
                $('#list-address-container').append('\
                  <li>\
                    <div class="radio-address">\
                      <input type="radio" name="RadioGroup3" value="radio" id="alamat3">\
                      <div class="address">\
                        <label for="alamat3">\
                          <span></span>\
                          <p>'+value.address_street+' - '+value.address_postcode+'</p>\
                          <p>'+value.address_city+' - '+value.address_province+'</p>\
                          <p>Nomor Handphone: '+value.address_phone+'</p>\
                          <p>\
                            <input class="set-primary-address" address_id="'+value.address_id+'" address_type="'+address_type+'" type="submit" value="Atur Sebagai Alamat Utama">\
                            <a class="edit-current-address" href="#" address_id="'+value.address_id+'" address_type="'+address_type+'">Ubah Alamat</a>\
                          </p>\
                        </label>\
                      </div>\
                    </div>\
                  </li>\
                ');
              });

              set_button();
              $('#loading_gif').hide();
              $('#alert-description').html('Sukses Mengubah Alamat');
              $('#address-success').show();
              $.fancybox.update();
              load_primary_address();
            }
          });
        }else{
          $('#edit-address-alert').show();
          $('#edit-address-alert').html('<i class="fa fa-bell" aria-hidden="true"></i> Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var set_shipping_method = function(shipping_type, shipping_id){
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_set_shipping_method',
      type: 'post',
      data: {
        shipping_type: shipping_type,
        shipping_id: shipping_id
      },
      beforeSend: function () {
        $('#loading_gif').show();
      },
      success: function (result) {
        $('#loading_gif').hide();
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        generate_grand_total(obj);
      }
    });
  };

  var set_payment_method = function(payment_method){
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_set_payment_method',
      type: 'post',
      data: {
        payment_method: payment_method
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('.input-voucher').show();
      },
      success: function (result) {
        $('#loading_gif').hide();
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        //Generate Shipping Method if COD
        if(obj.list_shipping_method != null){
          $('#shipping-method-container').empty();
          var temp_checked = '';
          var temp_disabled = '';
          $.each(obj.list_shipping_method, function (key, value) {
            temp_checked = value.is_primary ? 'checked' : '' ;
            temp_disabled = value.is_available ? '' : 'disabled' ;
            $('#shipping-method-container').append('\
              <p>\
                <label>\
                  <input type="radio" name="rad-shipping-method" shipping_id="'+value.id+'" type="'+value.shipping_type+'" '+temp_checked+' '+temp_disabled+'>\
                  <span class="span-shipping-method" shipping_id="'+value.id+'" type="'+value.shipping_type+'" '+temp_disabled+'>'+value.text+'</span>\
                </label>\
              </p>\
            ');
          });
        }
        //End Generate Shipping Method if COD
        
        //Reset Freegift
        $('#freegift-container').empty();
        $('.input-cc').val('');
        $('#freegift-notif').hide();
        $('.line-freegift-notif').hide();
        //End Reset Freegift
        
        //Reapply Freegift Auto
        generate_freegift_auto(obj.freegift_auto);
        
        //Reapply Voucher
        generate_voucher(obj.voucher);

        generate_grand_total(obj);
        
        //KREDIVO
        if(payment_method === '99'){             
            kredivo_payment_list();
        }
      }
    });
  };
  
    var kredivo_payment_list = function(){
        $.ajaxq('PaymentListKredivo', {
            url: ajax_url + '/checkout/json_payment_kredivo',
            type: 'post',
            data: {
                ajaxpost: 'init'
            },
            beforeSend: function () {
                $('#select-kredivo').html('Loading ...');
                $('input#btn-submit-order').attr('disabled', 'disabled');
            },
            success: function (result) {
                var obj = jQuery.parseJSON(result);                                                                                         
                var select_opt = '';
                            
                if(obj.status === 'OK'){      
                    if(obj.payments.length > 0){
                        obj.payments.sort(function (a, b) {
                            return a.tenure - b.tenure;
                        });    
                    }                                        
                    $.each(obj.payments, function(i, item) {
                        select_opt += '<option value="'+ item.id +'">'+ item.name +'</option>';
                    });                
                    $('input#btn-submit-order').removeAttr("disabled");
                    $('#select-kredivo').html('<select name="kredivo-payment" id="kredivo-payment" style="width:200px;padding-left:10px;">'+ select_opt+ '</select>');
                }
            }
        });    
    };
  
  var generate_freegift_auto = function(freegift_auto){
    $('#freegift-auto-container').empty();
    
    if($.isEmptyObject(freegift_auto) == false){
      freegift_auto.forEach(function(entry) {
        if(entry.promotions_value > 0){
          $('#freegift-auto-container').append('\
            <div class="table-purchase">\
              <span class="purchase-label">'+entry.promotions_name_for_customer+'</span>\
              <span id="txt-promo" class="purchase-value">(-) IDR '+number_format(entry.promotions_value)+'</span>\
            </div>\
          ');
        }else if(entry.promotions_mode == 4 || entry.promotions_mode == 5){
          $('#freegift-auto-container').append('\
            <div class="table-purchase">\
              <span class="purchase-label">'+entry.promotions_name_for_customer+'</span>\
            </div>\
          ');
        }
        
        if(entry.promotions_eksklusif == 1){
          $('.input-voucher').hide();
        }
      });
    }
    
    $("#freegift-auto-container:not(:empty)").prepend('\
      <span class="purchase-line"></span>\
    ');
  };
  
  var generate_voucher = function(voucher){
    $('#voucher-container').empty();
    if($.isEmptyObject(voucher) == false){
      if(voucher.promotions_value > 0){
        $('#voucher-container').append('\
          <span class="purchase-line"></span>\
          <div class="table-purchase">\
            <span class="purchase-label">'+voucher.promotions_name+'</span>\
            <span id="txt-promo" class="purchase-value">(-) IDR '+number_format(voucher.promotions_value)+'</span>\
          </div>\
        ');
      }else if(voucher.promotions_mode == 4 || voucher.promotions_mode == 5){
        $('#voucher-container').append('\
          <div class="table-purchase">\
            <span class="purchase-label">'+voucher.promotions_name_for_customer+'</span>\
          </div>\
        ');
      }
    }
  };
  
  var generate_freegift = function(freegift){
    $('#freegift-container').empty();
    $('#freegift-notif').hide();
    $('.line-freegift-notif').hide();
    
    if($.isEmptyObject(freegift) == false){
      freegift.forEach(function(entry) {
        if(entry.promotions_value > 0){
          $('#freegift-container').append('\
            <div class="table-purchase">\
              <span class="purchase-label">'+entry.promotions_name_for_customer+'</span>\
              <span id="txt-promo" class="purchase-value">(-) IDR '+number_format(entry.promotions_value)+'</span>\
            </div>\
          ');
          
          if(entry.promotions_notice != ""){
            $('.line-freegift-notif').show();
            $('#freegift-notif').show();
            $('#freegift-notif').html(entry.promotions_notice);
          }
        }else if(entry.promotions_mode == 4 || entry.promotions_mode == 5){
          $('#freegift-container').append('\
            <div class="table-purchase">\
              <span class="purchase-label">'+entry.promotions_name_for_customer+'</span>\
            </div>\
          ');
        }
        
        if(entry.promotions_eksklusif == 1){
          $('.input-voucher').hide();
        }
      });
    }
    
    $("#freegift-container:not(:empty)").prepend('\
      <span class="purchase-line"></span>\
    ');
  };
  
  var generate_benka_point = function(obj){
    if(obj.total.benka_point > 0){
      $('#benka-point-container').empty();

      $('#benka-point-container').append('\
        <span class="purchase-line"></span>\
        <div class="table-purchase">\
          <span class="purchase-label">Benka Point</span>\
          <span id="txt-promo" class="purchase-value">(-) IDR '+number_format(obj.total.benka_point)+'</span>\
        </div>\
      ');

      $('#benka_point').val(obj.total.benka_point);
    }
  };
  
  var generate_grand_total = function(obj){
    var shipping_cost = '(+) IDR '+number_format(obj.total.shipping_cost);
    if(obj.total.shipping_cost == 0){
      shipping_cost = 'FREE';
    }

    $('#txt-shipping-cost').html(shipping_cost);
    $('#txt-subtotal').html('(+) IDR '+number_format(obj.total.base_subtotal));
    $('#txt-paycode').html('(+) IDR '+number_format(obj.total.paycode));
    $('#txt-grandtotal').html('IDR '+number_format(obj.total.grand_total));
    $('#final-grand-total').val(obj.total.grand_total);
  };

  var get_bank_promo = function(){
    var payment_method  = $('input[name=rad-payment-method]:checked').val();
    var bin_number      = $('#bin-number').val();
    
    if(payment_method == 20){
        bin_number = $('#bin-number-mandiri').val();
    }
    
    //change value bin_number
    bin_number = bin_number.substr(0, 10);
    
    $.ajaxq.abort('Order'); 
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_get_bank_promo',
      type: 'post',
      data: {
        bin_number: bin_number
      },
      beforeSend: function () {
        $('#freegift-container').empty();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        if(obj.bank_name != null){
          $('#final-acquiring-bank').val(obj.bank_name);
        }else{
          $('#final-acquiring-bank').val('');
        }
        
        $('.input-voucher').show();
        
        //Apply Freegift
        generate_freegift(obj.freegift);

        //Reapply Freegift Auto
        generate_freegift_auto(obj.freegift_auto);
        
        //Reapply Voucher
        generate_voucher(obj.voucher);
        
        //Check Benka Point
        if(obj.allow_benka_point == 1){
          $('#benka-point-form').show();
          $('#benka-point-container').show();
        }else{
          $('#benka-point-form').hide();
          $('#benka-point-container').hide();
        }
        //End Check Benka Point
        
        //Reapply Benka Point
        generate_benka_point(obj);

        generate_grand_total(obj);
      }
    });
  };
  
  var apply_freegift_auto = function(){
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_apply_freegift_auto',
      type: 'post',
      data: {

      },
      beforeSend: function () {
        $('#loading_gif').show();
      },
      success: function (result) {
        $('#loading_gif').hide();
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        if(obj.result){
          //Apply Freegift Auto
          generate_freegift_auto(obj.freegift_auto);
        }
        
        //Check Benka Point
        if(obj.allow_benka_point == 1){
          $('#benka-point-form').show();
          $('#benka-point-container').show();
        }else{
          $('#benka-point-form').hide();
          $('#benka-point-container').hide();
        }
        //End Check Benka Point

        generate_grand_total(obj);
        
        $('#sticky-cart').hcSticky();
      }
    });
  };

  var apply_voucher = function(){
    var voucher_code = $('#voucher_code').val();

    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_apply_voucher',
      type: 'post',
      data: {
        voucher_code: voucher_code
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#voucher-container').empty();
        $('#freegift-container').empty();
      },
      success: function (result) {
        $('#loading_gif').hide();
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        if(obj.result){
          //Apply Voucher
          generate_voucher(obj.voucher);

          $('#msg-container').addClass("success-msg");
          $('#msg-container').removeClass("error-msg-login");
          
          $('#msg-content').html(obj.voucher.promotions_name_for_customer + ". " + obj.voucher.promotions_notice);
        }else{
          $('#msg-container').addClass("error-msg-login");
          $('#msg-container').removeClass("success-login");
          $('#msg-content').html(obj.error_msg);
        }
        
        $('#msg-container').show();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        
        //Reapply Freegift Auto
        generate_freegift_auto(obj.freegift_auto);
        
        //Reapply Freegift
        generate_freegift(obj.freegift);
        
        //Check Benka Point
        if(obj.allow_benka_point == 1){
          $('#benka-point-form').show();
          $('#benka-point-container').show();
        }else{
          $('#benka-point-form').hide();
          $('#benka-point-container').hide();
        }
        //End Check Benka Point
        
        //Reapply Benka Point
        generate_benka_point(obj);

        generate_grand_total(obj);
      }
    });
  };
  
  var apply_benka_point = function(){
    var benka_point = $('#benka_point').val();
    
    $.ajaxq('Order', {
      url: ajax_url + '/checkout/json_apply_benka_point',
      type: 'post',
      data: {
        benka_point: benka_point
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#benka-point-container').empty();
      },
      success: function (result) {
        $('#loading_gif').hide();
        var obj = jQuery.parseJSON(result);
        check_login(obj);

        if(obj.result){
          generate_benka_point(obj);

          $('#msg-container').addClass("success-msg");
          $('#msg-container').removeClass("error-msg-login");
          $('#msg-content').html('Penggunaan Benka Point sukses');
        }else{
          $('#msg-container').addClass("error-msg-login");
          $('#msg-container').removeClass("success-login");
          $('#msg-content').html(obj.error_msg);
        }

        $('#msg-container').show();
        $("html, body").animate({ scrollTop: 0 }, "slow");

        generate_grand_total(obj);
      }
    });
  };
  
  var process_time = 0;
  var check_process_time = function(){
    process_time++;
    if(process_time <= max_queuing_trying){
      return true;
    }else{
      process_time = 0;
      clearInterval(interval);
      interval = 0;
      $.ajaxq.clear('CheckOrder');
      
      $.ajaxq('ClearOrder', {
        url: ajax_url + '/checkout/json_clear_order_process',
        type: 'post',
        data: {},
        beforeSend: function () {
        },
        success: function (result) {
        }
      });
      
      return false;
    }
  };
  
  var check_order_process = function(){
    $.ajaxq('CheckOrder', {
      url: ajax_url + '/checkout/json_check_order_process',
      type: 'post',
      data: {},
      beforeSend: function () {
      },
      success: function (result) {
        if(check_process_time()){
          var obj = jQuery.parseJSON(result);
          check_login(obj);
          
          if(obj.result){
            if(obj.result.status == 1 || obj.result.status == 2 || obj.result.status == 3){
              clearInterval(interval);
              interval = 0;
              $.ajaxq.clear('CheckOrder');
            }

            if(obj.result.status == 1){
              //Transaction Success
              window.location.href = ajax_url + '/checkout/final_order/?po=' + obj.result.purchase_order;
            }else if(obj.result.status == 2){
              //Transaction Failed
              $('#msg-container').addClass("error-msg-login");
              $('#msg-container').removeClass("success-login");
              $('#msg-content').html(obj.result.status_message);
              $('#msg-container').show();

              $("html, body").animate({ scrollTop: 0 }, "slow");
              $('#loading_gif').hide();
            }else if(obj.result.status == 3){
              //Item out of stock
              window.location.href = ajax_url + '/checkout/cart/';
            }
          }else{
            $('#msg-container').addClass("error-msg-login");
            $('#msg-container').removeClass("success-login");
            $('#msg-content').html("Terjadi kesalahan sistem. Mohon coba kembali.");
            $('#msg-container').show();

            $("html, body").animate({ scrollTop: 0 }, "slow");
            $('#loading_gif').hide();
          }
        }else{
          //Queuing Failed, Transaction is now processed without queuing system
          alert("Transaction QUEUING failed");
          $("#form-submit-order").submit();
        }
      }
    });
  };
  
  var interval;
  var interval_check = function(){
    interval = setInterval(function(){ check_order_process(); }, queuing_periodic_time);
  };
  
  var insert_order_process = function(){
    if(transaction_queuing == 1){
      $.ajaxq('Order', {
        url: ajax_url + '/checkout/json_insert_order_process',
        type: 'post',
        data: {
          'klikbca-user-id' : $('#txt-klikbca-user-id').val(),
          'token-id'        : $("#token-id").val(),
          'cc-holder'       : $('#bin-name').val()
        },
        beforeSend: function () {
        },
        success: function (result) {
          var obj = jQuery.parseJSON(result);
          check_login(obj);
          
          if(obj.result){
            interval_check();
          }else{
            $('#msg-container').addClass("error-msg-login");
            $('#msg-container').removeClass("success-login");
            $('#msg-content').html("Terjadi kesalahan sistem. Mohon coba kembali.");
            $('#msg-container').show();

            $("html, body").animate({ scrollTop: 0 }, "slow");
            $('#loading_gif').hide();
          }
        }
      });
    }else{
      $("#form-submit-order").submit();
    }
  };

  var set_button = function(){
    $(document).off('click', '.set-primary-address');
    $(document).on('click', '.set-primary-address', function (event) {
      event.preventDefault();
      var address_id = $(this).attr("address_id");
      var address_type = $(this).attr("address_type");

      set_primary_address(address_id, address_type);
    });

    $(document).off('click', '.edit-current-address');
    $(document).on('click', '.edit-current-address', function () {
      var address_id = $(this).attr("address_id");

      $('.ubah-list-alamat').hide('.2s');
      $('.close-list-alamat').hide();
      $('.back-list-alamat').show();
      $('.edit-alamat-detail').show('.2s');

      $('#edit-address-alert').hide();
      load_detail_address(address_id);
    });

    $(document).off('change', '#edit-address-province');
    $(document).on('change', '#edit-address-province', function () {
      load_city($(this).val(), 2);
    });

    $(document).off('click', '.span-shipping-method');
    $(document).on('click', '.span-shipping-method', function () {
      set_shipping_method($(this).attr("type"), $(this).attr("shipping_id"));
    });

    $(document).off('click', '#btn-apply-voucher');
    $(document).on('click', '#btn-apply-voucher', function (event) {
      event.preventDefault();
      apply_voucher();
    });
    
    $(document).off('click', '#btn-benka-point');
    $(document).on('click', '#btn-benka-point', function (event) {
      event.preventDefault();
      apply_benka_point();
    });

    $('#voucher_code').keypress(function (e) {
     var key = e.which;
     if(key == 13){
        apply_voucher();
        return false;
      }
    });
    
    $('#benka_point').keypress(function (e) {
     var key = e.which;
     if(key == 13){
        apply_benka_point();
        return false;
      }
    });
  };
  
  function openDialog(url) {
    $.fancybox.open({
      href: url,
      type: 'iframe',
      autoSize: false,
      width: 400,
      height: 420,
      closeBtn: false,
      modal: true
    });
    $('#loading_gif').show();
  }
  
  function closeDialog() {
    $.fancybox.close();
  }
  

  
  var card = function () {
    var payment_method = $('input[name=rad-payment-method]:checked').val();
        var domainName = window.location.hostname;
        var secureDefault = false;        
        
        if(domainName == 'shopdeca.com' || domainName == 'www.shopdeca.com' || domainName == 'm.shopdeca.com' || domainName == 'eff-deca.benka.co' || domainName == 'dev-deca.benka.co' || domainName == 'staging-deca.benka.co'){
            secureDefault = true;            
        }
        if(payment_method == 5){ //Credit Card          
          return {
            "card_number": $("#bin-number").val(),
            "card_exp_month": $("#bin-month").val(),
            "card_exp_year": $("#bin-year").val(),
            "card_cvv": $("#bin-cvv").val(),
            "secure": secureDefault,
            "bank": $('#final-acquiring-bank').val(),
            "gross_amount": $('#final-grand-total').val()
          };
        }else if(payment_method == 20){ //Use 3DS if Mandiri Debit
          return {
            "card_number": $("#bin-number-mandiri").val(),
            "card_exp_month": $("#bin-month-mandiri").val(),
            "card_exp_year": $("#bin-year-mandiri").val(),
            "card_cvv": $("#bin-cvv-mandiri").val(),
            "secure": true,
            "bank": $('#final-acquiring-bank').val(),
            "gross_amount": $('#final-grand-total').val()
          };
        }
  };
  
  function callback(response) {
    if(response.status_code) {
      if (response.redirect_url) {
        // 3Dsecure transaction. Open 3Dsecure dialog
        console.log('Open Dialog 3Dsecure');
        openDialog(response.redirect_url);
      } else if (response.status_code == '200') {
        // success 3d secure or success normal
        closeDialog();
        // store token data in input #token_id and then submit form to merchant server
        $("#token-id").val(response.token_id);
        insert_order_process();
      } else {
        // failed request token
        $('#loading_gif').hide();
        $('#msg-container').addClass("error-msg-login");
        $('#msg-container').removeClass("success-login");
        $('#msg-content').html("Terjadi kesalahan informasi dari kartu kredit. Mohon cek kembali kartu kredit anda.");
        $('#msg-container').show();
        $("html, body").animate({ scrollTop: 0 }, "slow");
        closeDialog();
        console.log(JSON.stringify(response));
      }
    }
  }
  //End Function

  //User Action
  $('#btn-list-shipping-address').click(function(){
    $('#add-address-type').val(1);
    $('#add-address-alert').hide();
    $('#address-success').hide();
    load_list_address(1);
  });

  $('#btn-list-billing-address').click(function(){
    $('#add-address-type').val(2);
    $('#add-address-alert').hide();
    $('#address-success').hide();
    load_list_address(2);
  });

  $('.close-list-alamat').click(function(){ //Avoid scroll bug from fancybox
    $('.fancybox-lock').removeClass('fancybox-lock');
    $('.fancybox-margin').removeClass('fancybox-margin');
  });

  $('.tambah-alamat-baru').click(function(){
    $('#address-success').hide();
    $('#add-address-alert').hide();
  });

  $('#add-address-province').change(function(){
    load_city($('#add-address-province').val(), 1);
  });

  $('#new-shipping-province').change(function(){
    load_city($('#new-shipping-province').val(), 3);
  });

  $('#new-billing-province').change(function(){
    load_city($('#new-billing-province').val(), 4);
  });

  $('#btn-edit-address').click(function(event){
    event.preventDefault();
    edit_address();
  });

  $('#btn-add-address').click(function(event){
    event.preventDefault();
    add_address();
  });

  $('#btn-new-address').click(function(event){
    event.preventDefault();
    new_address();
  });

  $('.rad-payment-method').change(function(event){
    event.preventDefault();            
    set_payment_method($(this).val());  
  });
  
  $("#bin-number").keyup(function(){
    get_bank_promo();
  });
  
  $('#bin-number-mandiri').keyup(function(){
    get_bank_promo();
  });
  
  $('#btn-submit-order').click(function(event){
    event.preventDefault();
    $('#loading_gif').show();
    
    var payment_method = $('input[name=rad-payment-method]:checked').val();
    if(payment_method == 5 || payment_method == 20){
      $('#cc-holder').val($('#bin-name').val());
      Veritrans.token(card, callback);
    }else{
      $('#klikbca-user-id').val($('#txt-klikbca-user-id').val());
      insert_order_process();
    }
  });
  
  //kredivo
  $('#select-kredivo').on('change', 'select#kredivo-payment', function(){
      var kredivo_value = $(this).val();
      $('input#kredivo-payment-type').val(kredivo_value);
  });    
  //End User Action

  //Initialization    
  Veritrans.url         = $('#veritrans-api').val();
  Veritrans.client_key  = $('#client-key').val();
  transaction_queuing   = $('#transaction-queuing').val();
  queuing_periodic_time = $('#queuing-periodic-time').val();
  max_queuing_trying    = $('#max-queuing-trying').val();
  load_primary_address();
  apply_freegift_auto();
  //End Initialization
});
