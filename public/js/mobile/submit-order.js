$(document).ready(function(){
  var ajax_url = $('#ajax_url').val();

  //Function
  var check_login = function(obj){
    if(obj.result == false && obj.need_refresh == true){
      location.reload();
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
  
  var set_shipping_method = function(shipping_type){
    $.ajax({
      url: ajax_url + '/checkout/json_set_shipping_method',
      type: 'post',
      data: {
        shipping_type: shipping_type
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
    $.ajax({
      url: ajax_url + '/checkout/json_set_payment_method',
      type: 'post',
      data: {
        payment_method: payment_method
      },
      beforeSend: function () {
        $('#loading_gif').show();
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
              <li>\
                  <label class="clear">\
                      <input type="radio" class="rad-shipping-method" name="rad-shipping-method" value="'+value.shipping_type+'" '+temp_checked+' '+temp_disabled+' id="RadioGroup1_0">\
                      <span><p>'+value.text+'</p></span>\
                  </label>\
              </li>\
            ');
          });
        }
        //End Generate Shipping Method if COD
        
        //Reset Freegift
        $('.freegift-content').hide();
        $('#freegift-notif').hide();
        //End Reset Freegift

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
                ajaxpost: 'init',
            },
            beforeSend: function () {
                $('#select-kredivo').html('Loading ...');
                $('input#btn-submit-order').attr('disabled', 'disabled');
            },
            success: function (result) {
                var obj = jQuery.parseJSON(result);     
                var selected_value = obj.selected_value;
                if(typeof selected_value != "undefined"){
                    $('input#kredivo-payment-type').val(selected_value);    
                }
                
                var select_opt = '';
                var selected = '';
                            
                if(obj.status === 'OK'){      
                    if(obj.payments.length > 0){
                        obj.payments.sort(function (a, b) {
                            return a.tenure - b.tenure;
                        });    
                    }                                        
                    $.each(obj.payments, function(i, item) {
                        if(item.id == selected_value){
                            selected = 'selected';
                        }else{
                            selected = '';
                        }
                        select_opt += '<option value="'+ item.id +'" '+ selected +'>'+ item.name +'</option>';
                    });                
                    $('input#btn-submit-order').removeAttr("disabled");
                    $('#select-kredivo').html('<select name="kredivo-payment" id="kredivo-payment" style="width:200px;padding-left:10px;">'+ select_opt+ '</select>');
                }
            }
        });    
    };
    
    var SetkredivoSession = function(type_kredivo){
        $.ajaxq('SessionKredivo', {
            url: ajax_url + '/checkout/json_setsession_kredivo_mobile',
            type: 'post',
            data: {                
                kredivo_type: type_kredivo,
            },
            beforeSend: function () {
                $('input#btn-submit-order').attr('disabled', 'disabled');
            },
            success: function (result) {
                if(result){
                    $('input#btn-submit-order').removeAttr("disabled");    
                }                
            }
        });  
    };

  var set_primary_address = function(address_id, address_type){
    $.ajax({
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
          location.reload();
        }
      }
    });
  };

  var new_address = function(){
    var multi_address = 0;
    if($("#set-as-address").is(':checked')){
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

    $.ajax({
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
          location.reload();
        }else{
          $('#new-shipping-alert').show();
          $('#new-shipping-alert').html('Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var add_address = function(){
    var address_type = $('#add-address-type').val();
    var address_street = $('#address-street').val();
    var address_province = $('#address-province').val();
    var address_city = $('#address-city').val();
    var address_postcode = $('#address-postcode').val();
    var address_phone = $('#address-phone').val();

    $.ajax({
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
          set_primary_address(obj.id, address_type);
        }else{
          $('#address-alert').show();
          $('#address-alert').html('Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var edit_address = function(){
    var address_id = $('#address-id').val();
    var address_type = $('#address-type').val();
    var address_street = $('#address-street').val();
    var address_province = $('#address-province').val();
    var address_city = $('#address-city').val();
    var address_postcode = $('#address-postcode').val();
    var address_phone = $('#address-phone').val();

    $.ajax({
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
          location.reload();
        }else{
          $('#address-alert').show();
          $('#address-alert').html('Terdapat kesalahan: <br/> <br/>'+obj.result_message);
          $('#loading_gif').hide();
        }
      }
    });
  };

  var load_list_address = function (address_type) {
    $.ajax({
      url: ajax_url + '/checkout/json_get_customer_address',
      type: 'post',
      data: {
        address_type: address_type
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-container').empty();
      },
      success: function (result) {
        var obj = jQuery.parseJSON(result);
        check_login(obj);
        
        if(obj.have_address){
          $.each(obj.list_customer_address, function (key, value) {
            $('#address-container').append('\
              <li>\
                <p>'+value.address_street+' - '+value.address_postcode+'</p>\
                <p>'+value.address_city+' - '+value.address_province+'</p>\
                <p>Nomor Handphone: '+value.address_phone+'</p>\
                <a href="#" class="edit-address set-primary-address show-edit" address_id="'+value.address_id+'" address_type="'+address_type+'" style="margin-right: 7px;">Atur Sebagai Alamat utama</a>\
                <a href="#" class="edit-address edit-current-address show-edit" address_id="'+value.address_id+'" address_type="'+address_type+'">Ubah Alamat</a>\
                <div class="clear"></div>\
              </li>\
            ');
          });
          $('#address-container').append('\
            <a id="add-address" href="#" class="edit-address show-edit add-new-add"><i class="fa fa-plus" aria-hidden="true"></i>Tambah Alamat Baru</a>\
          ');
        }
        $('#loading_gif').hide();
      }
    });
  };

  var load_city = function(shipping_area, type){
    //Type 1: Add Address | 2: New Shipping Address | 4: New Billing Address
    $.ajax({
      url: ajax_url + '/checkout/json_get_shipping_list',
      type: 'post',
      data: {
        type: 1,
        shipping_area: shipping_area
      },
      beforeSend: function () {
        if(type == 1){
          $('#address-city').empty();
          $('#address-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }else if(type == 2){
          $('#new-shipping-city').empty();
          $('#new-shipping-city').append('\
            <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
          ');
        }else if(type == 3){
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
          $('#address-city').empty();
          $('#address-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#address-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }else if(type == 2){
          $('#new-shipping-city').empty();
          $('#new-shipping-city').append('\
            <option value="" disabled="disabled" selected="selected">Kota</option>\
          ');
          $.each(obj.list_shipping, function (key, value) {
            $('#new-shipping-city').append('\
              <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
            ');
          });
        }else if(type == 3){
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
    $.ajax({
      url: ajax_url + '/checkout/json_get_customer_address',
      type: 'post',
      data: {
        address_id: address_id
      },
      beforeSend: function () {
        $('#loading_gif').show();
        $('#address-container').hide();
        $('#edit-address-alert').hide();
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

        $('#address-id').val(address_id);
        $('#address-type').val(address_type);
        $('#address-street').val(address_street);
        $('#address-province').val(address_province);
        $('#address-postcode').val(address_postcode);
        $('#address-phone').val(address_phone);

        //Load City
        $.ajax({
          url: ajax_url + '/checkout/json_get_shipping_list',
          type: 'post',
          data: {
            type: 1,
            shipping_area: address_province
          },
          beforeSend: function () {
            $('#address-city').empty();
            $('#address-city').append('\
              <option value="" disabled="disabled" selected="selected" readonly="readonly">Loading ...</option>\
            ');
          },
          success: function (result) {
            var obj = jQuery.parseJSON(result);
            $('#address-city').empty();
            $('#address-city').append('\
              <option value="" disabled="disabled" selected="selected">Kota</option>\
            ');
            $.each(obj.list_shipping, function (key, value) {
              $('#address-city').append('\
                <option value="'+value.shipping_name+'">'+value.shipping_name+'</option>\
              ');
            });
            $('#address-city').val(address_city);
            $('#loading_gif').hide();
            // $('#address-detail-container').show('fast');
          }
        });
        //End Load City
      }
    });
  };

  var set_button = function(){
    $(document).off('click', '.rad-shipping-method');
    $(document).on('click', '.rad-shipping-method', function () {
      set_shipping_method($(this).val());
    });

    $(document).off('click', '.rad-payment-method');
    $(document).on('click', '.rad-payment-method', function () {
      set_payment_method($(this).val());
    });

    $(document).off('click', '.set-primary-address');
    $(document).on('click', '.set-primary-address', function (event) {
      event.preventDefault();
      var address_id = $(this).attr("address_id");
      var address_type = $(this).attr("address_type");

      set_primary_address(address_id, address_type);
    });

    $(document).off('click', '.new-address');
    $(document).on('click', '.new-address', function () {
      $('.list-alamat').show('fast');
      $('.content-detail').hide('fast');
      $('.list-alamat ul').show('fast');
      $('#new-address-container').show('fast');
    });

    $(document).off('change', '#new-shipping-province');
    $(document).on('change', '#new-shipping-province', function () {
      load_city($(this).val(), 2);
    });

    $(document).off('change', '#new-billing-province');
    $(document).on('change', '#new-billing-province', function () {
      load_city($(this).val(), 3);
    });

    $(document).off('click', '.new-close-button');
    $(document).on('click', '.new-close-button', function () {
      $('.list-alamat').hide('fast');
      $('.content-detail').show('fast');
      $('.list-alamat ul').hide('fast');
      $('#new-address-container').hide('fast');
    });

    $(document).off('click', '#set-as-address');
    $(document).on('click', '#set-as-address', function () {
      if($(this).prop('checked')){
        $('.form-billing').hide('fast');
      }else{
        $('.form-billing').show('fast');
      }
    });

    $(document).off('click', '.edit-shipping-address');
    $(document).on('click', '.edit-shipping-address', function () {
      $('.list-alamat').show('fast');
    	$('.content-detail').hide('fast');
    	$('.list-alamat ul').show('fast');
    	$('.editing-address').hide('fast');

      $('#add-address-type').val(1);
      load_list_address(1);
    });

    $(document).off('click', '.edit-billing-address');
    $(document).on('click', '.edit-billing-address', function () {
      $('.list-alamat').show('fast');
    	$('.content-detail').hide('fast');
    	$('.list-alamat ul').show('fast');
    	$('.editing-address').hide('fast');

      $('#add-address-type').val(2);
      load_list_address(2);
    });

    $(document).off('change', '#address-province');
    $(document).on('change', '#address-province', function () {
      load_city($(this).val(), 1);
    });

    $(document).off('click', '#add-address');
    $(document).on('click', '#add-address', function () {
      $('#submit-address').attr('mode', 'add');
      $('#address-container').hide();
      // $('#new-address-container').hide('fast');
      // $('#address-detail-container').show('fast');
    });

    $(document).off('click', '.edit-current-address');
    $(document).on('click', '.edit-current-address', function () {
      $('#submit-address').attr('mode', 'edit');
      // $('#new-address-container').hide('fast');
      load_detail_address($(this).attr("address_id"));
    });

    $(document).off('click', '.back-btn');
    $(document).on('click', '.back-btn', function () {
      $('.form-address').val('');
      $('#address-detail-container').hide();
      $('#address-container').show('fast');
    });

    $(document).off('click', '#new-submit-address');
    $(document).on('click', '#new-submit-address', function () {
      new_address();
    });

    $(document).off('click', '#submit-address');
    $(document).on('click', '#submit-address', function () {
      var mode = $(this).attr("mode");
      if(mode == 'edit'){
        edit_address();
      }else{
        add_address();
      }
    });
    
    $(document).off('click', '#btn-submit-voucher');
    $(document).on('click', '#btn-submit-voucher', function (event) {
      event.preventDefault();
      $('#form-voucher').submit();
    });
    
    $(document).off('click', '#btn-submit-benka-point');
    $(document).on('click', '#btn-submit-benka-point', function (event) {
      event.preventDefault();
      $('#form-benka-point').submit();
    });
    
    $(document).off('click', '#btn-submit-bank-promo');
    $(document).on('click', '#btn-submit-bank-promo', function (event) {
      event.preventDefault();
      $('#form-bank-promo').submit();
    });
    
    $(document).off('click', '#btn-submit-mandiri-debit');
    $(document).on('click', '#btn-submit-mandiri-debit', function (event) {
      event.preventDefault();
      $('#form-mandiri-debit').submit();
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
    }
    else if(payment_method == 20){ //Use 3DS if Mandiri Debit
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
        $('#loading_gif').hide();
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
        $('#msg-container').addClass("error-msg-login");
        $('#msg-container').removeClass("success-login");
        $('#msg-content').html("Terjadi kesalahan informasi dari kartu kredit. Mohon cek kembali kartu kredit anda.");
        $('#msg-container').show();

        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#loading_gif').hide();
        closeDialog();
        console.log(JSON.stringify(response));
      }
    }
  }
  
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
  //End Function

  //User Action
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
    $('#select-kredivo').on('change', 'select#kredivo-payment', function () {
        var kredivo_value = $(this).val();
        $('input#kredivo-payment-type').val(kredivo_value);
        SetkredivoSession(kredivo_value);
    });       
    //if already set
    $( document ).ready(function(){
        var selected_payment_method = $('.rad-payment-method:checked').val();
        if(selected_payment_method == 99){
            kredivo_payment_list();
        }
    });
    
    
    //End User Action
    
  //Initialization    
  Veritrans.url         = $('#veritrans-api').val();
  Veritrans.client_key  = $('#client-key').val();
  transaction_queuing   = $('#transaction-queuing').val();
  queuing_periodic_time = $('#queuing-periodic-time').val();
  max_queuing_trying    = $('#max-queuing-trying').val();
  set_button();
  //End Initialization
});
