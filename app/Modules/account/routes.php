<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'Account', 'namespace' => 'App\Modules\Account\Controllers'), function() {
	    //Route::resource('user/account_dashboard', 'AccountController@index');
	    Route::get('/user/set_primary_address/{address_type}/{address_id}', 'AccountController@setPrimaryAddress')
				->where(array('address_type' => '[0-9]+', 'address_id' => '[0-9]+'));
            Route::get('/user/account_dashboard', 'AccountController@index');
            Route::get('/user/benka_poin', 'AccountController@benkaPoin');
	    //Route::get('/user/referral_program', 'AccountController@referral');
	    Route::get('/user/text_me_app', 'AccountController@textMeApp');
	    Route::get('/user/wishlist', 'AccountController@wishlist');
	    Route::get('/user/order_history', 'AccountController@orderHistory');
	    Route::get('/user/setting', 'AccountController@setting');
	   
	    //Route::get('/user/return_form', 'AccountController@returnForm');
	    Route::post('/user/order_status', 'AccountController@ajax_order_status');
	    Route::get('/user/order_history_detail/{purchase_code}', 'AccountController@orderHistoryDetail')
					->where(array('purchase_code' => '[a-zA-Z0-9]+'));
	    Route::post('/user/check_account', 'AccountController@validateAccount');
	    Route::get('/user/return_purchase/{order_item_id}', 'AccountController@returnPurchase');
	    Route::post('/user/insert_customer_return', 'AccountController@insertCustomerReturn');
	    Route::post('/user/call_wms_return_process', 'AccountController@callWmsReturnProcess');
	    Route::get('/user/cancel_return/{order_item_id}', 'AccountController@cancelReturn');
		Route::get('/user/confirm_email', 'AccountController@confirmEmail');
		Route::get('/user/reset_password', 'AccountPasswordController@resetPassword');
	    Route::get('/user/change_password', 'AccountPasswordController@changePassword');
	    Route::post('/user/update_password', 'AccountPasswordController@updatePassword');
	    Route::get('/forgot_password', 'AccountPasswordController@forgotPassword');
	    Route::post('/forgot_password/post', 'AccountPasswordController@forgotPasswordPost');
	    Route::post('/user/add_address/{shipping_type}', 'AccountController@addAddress');
	    Route::post('/user/edit_address/{address_id}', 'AccountController@editAddress');
	    Route::get('/user/delete_address/{address_id}', 'AccountController@deleteAddress');
	    Route::post('/user/get_shipping_city_new', 'AccountController@getShippingCityNew');
	    Route::get('/user/edit_personal_detail', 'AccountController@editPersonalDetail');
	    Route::post('/user/save_personal_detail', 'AccountController@savePersonalDetail');
	    Route::post('/newsubcriber', 'AccountController@newsubcriber');

	    Route::get('/order_status/order_status_tracking/{purchase_code}/{customer_email}', 'AccountController@order_status_tracking');	    
	    Route::get('/stamp_cron', 'AccountController@benkaStampActivation');
	    Route::get('/stamp_email', 'AccountController@benkaStampEmailNotif');

	    Route::get('/order_status/order_status_tracking/{purchase_code}/{customer_email}', 'AccountController@order_status_tracking');	                
            
            /*
             * benka stamp 
             */
            Route::get('/user/stamp/deals', 'AccountController@stampDeals');
            Route::get('/user/stamp/deals/{deals_id}', 'AccountController@stampDealsDetail')->where(array('deals_id' => '[a-zA-Z0-9]+'));
            Route::post('/user/stamp/deals/redeem', 'AccountController@stampDealsRedeem');
            Route::get('/user/stamp/deals/redeem/{deals_id}', 'AccountController@stampDealsRedeemMobile')->where(array('deals_id' => '[a-zA-Z0-9]+'));
            Route::get('/user/stamp/terms', 'AccountController@stampTerms');
            Route::get('/user/stamp/faq', 'AccountController@stampFaq');
            Route::get('/user/stamp/history', 'AccountController@stampHistory');
            Route::get('/json/benka-stamp/terms', 'AccountController@jsonBenkaStampTerms');
            Route::get('/json/benka-stamp/faq', 'AccountController@jsonBenkaStampFaq');

	});	
});