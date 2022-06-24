<?php

Route::group(['middleware' => ['web','secure.url']], function () {
    Route::group(array('module' => 'Checkout', 'namespace' => 'App\Modules\Checkout\Controllers'), function() {
    
    //Cart
    Route::get('checkout/', 'CartController@index');
    Route::get('checkout/cart', 'CartController@index');

    Route::post('checkout/update_cart', 'CartController@UpdateCart');
    Route::post('checkout/json_update_cart', 'CartController@jsonUpdateCart');
    Route::post('checkout/json_load_cart', 'CartController@jsonLoadCart');
    //End Cart

    //Submit Order 
    Route::get('checkout/submit_order', 'SubmitOrderController@index');
    Route::post('checkout/insert_order_process', 'SubmitOrderController@insertOrderProcess');
    
    Route::post('checkout/apply_voucher', 'SubmitOrderController@ApplyVoucher');
    Route::post('checkout/apply_benka_point', 'SubmitOrderController@ApplyBenkaPoint');
    Route::post('checkout/apply_bank_promo', 'SubmitOrderController@ApplyBankPromo');
    Route::post('checkout/json_new_customer_address', 'SubmitOrderController@jsonNewCustomerAddress');
    Route::post('checkout/json_get_customer_address', 'SubmitOrderController@jsonGetCustomerAddress');
    Route::post('checkout/json_set_primary_address', 'SubmitOrderController@jsonSetPrimaryAddress');
    Route::post('checkout/json_add_customer_address', 'SubmitOrderController@jsonAddCustomerAddress');
    Route::post('checkout/json_edit_customer_address', 'SubmitOrderController@jsonEditCustomerAddress');
    Route::post('checkout/json_get_shipping_list', 'SubmitOrderController@jsonGetShippingList');
    Route::post('checkout/json_set_shipping_method', 'SubmitOrderController@jsonSetShippingMethod');
    Route::post('checkout/json_set_payment_method', 'SubmitOrderController@jsonSetPaymentMethod');
    Route::post('checkout/json_get_bank_promo', 'SubmitOrderController@jsonGetBankPromo');
    Route::post('checkout/json_apply_voucher', 'SubmitOrderController@jsonApplyVoucher');
    Route::post('checkout/json_apply_freegift_auto', 'SubmitOrderController@jsonApplyFreegiftAuto');
    Route::post('checkout/json_apply_benka_point', 'SubmitOrderController@jsonApplyBenkaPoint');
    Route::post('checkout/json_insert_order_process', 'SubmitOrderController@jsonInsertOrderProcess');
    Route::post('checkout/json_check_order_process', 'SubmitOrderController@jsonCheckOrderProcess');
    Route::post('checkout/json_clear_order_process', 'SubmitOrderController@jsonClearOrderProcess');
    Route::post('checkout/json_payment_kredivo', 'SubmitOrderController@jsonListPaymentKredivo');
    Route::post('checkout/json_setsession_kredivo_mobile', 'SubmitOrderController@jsonSessionKredivoMobile');
    
    Route::get('checkout/insert_order_process', function(){
      return redirect('checkout/submit_order');
    });
    
    Route::get('checkout/update_master_payment', 'SubmitOrderController@updateMasterPayment');
    //End Submit Order

    //Final Order
    Route::match(['get', 'post'], 'checkout/final_order', 'FinalOrderController@index');
    //End Final Order
    
    // T-Cash
    Route::get('checkout/tcash_redirect', 'TcashController@redirect');
    Route::get('checkout/tcash_success', 'TcashController@success');
    Route::get('checkout/debug', 'TcashController@debug');

    // Go-Pay
    Route::get('checkout/gopay/qrcode', 'GopayController@qrcode');

    // Kredivo push notification
    Route::post('checkout/kredivo_push_notif', 'KredivoController@pushNotification');

    //Sprintasia 3rd party for KlikBCA and KlikPay
    Route::get('checkout/paygate_sprint', 'SprintAsiaController@paymentInquiry');
    Route::get('checkout/paygate_flag_sprint', 'SprintAsiaController@paymentConfirmation');
    Route::get('checkout/inquiry_klikbca', 'SprintAsiaController@generateUrlPaymentInquiry');
    Route::get('checkout/confirmation_klikbca', 'SprintAsiaController@generateUrlPaymentConfirmation');

    Route::get('checkout/process_voucher', 'SubmitOrderController@process_voucher');
    Route::get('checkout/test_db', 'SubmitOrderController@testDb');
    
    //Fix Promotions Condition
    Route::get('checkout/fix_promotions_condition', function(){
      echo "Fix Promotions Condition";
      \DB::enableQueryLog();
      $datas = \DB::table('promotions_condition as pc')
        ->join('voucher_condition as vc', 'pc.voucher_condition_id', '=', 'vc.voucher_condition_id')
        ->select(\DB::raw('
          pc.promotions_condition_id,
          pc.promotions_template_id,
          pc.promotions_type_condition,
          pc.promotions_type_equal_type,
          pc.promotions_type_equal_value,
          vc.voucher_type_condition,
          vc.voucher_type_equal_type,
          vc.voucher_type_equal_value'))
        ->whereNull('pc.promotions_type_equal_type')
        ->where('pc.promotions_condition_parent_id', '!=', 0)
        ->get();

      foreach ($datas as $data) {
        $promotions_condition_id  = $data->promotions_condition_id;
        $voucher_type_condition   = $data->voucher_type_condition;

        $voucher_type_equal_type = NULL;
        if($voucher_type_condition == 15 || $voucher_type_condition == 27 || $voucher_type_condition == 14 || $voucher_type_condition == 13){
          $voucher_type_equal_type = 9;
        }else if($voucher_type_condition == 22 || $voucher_type_condition == 20 || $voucher_type_condition == 7 || $voucher_type_condition == 22){
          $voucher_type_equal_type = 1;
        }else if($voucher_type_condition == 4){
          $voucher_type_equal_type = 3;
        }else if($voucher_type_condition == 5 || $voucher_type_condition == 21){
          $voucher_type_equal_type = 4;
        }
        
        \DB::enableQueryLog();
        $update_item = array();
        $update_item['promotions_type_equal_type'] = $voucher_type_equal_type;
        $update_process = \DB::table('promotions_condition')
          ->where('promotions_condition_id', $promotions_condition_id)
          ->update($update_item);
        bb_debug(\DB::getQueryLog());
      }
      bb_debug(\DB::getQueryLog());
    });
	});
});
