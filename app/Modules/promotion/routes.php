<?php
Route::group(['middleware' => ['web','secure.url']], function () {
    Route::group(array('module' => 'Promotion', 'namespace' => 'App\Modules\Promotion\Controllers'), function() {

        Route::get('import/voucher_template/{id}', 'PromotionController@migrateVoucherTemplate');
        Route::get('import/voucher_code/{id}', 'PromotionController@migrateVoucherCode');
        Route::get('import/voucher_condition/{id}', 'PromotionController@migrateVoucherCondition');
        Route::get('import/promotions_condition', 'PromotionController@migratePromotionCondition');
        Route::get('import/promotions_condition_update/{id}', 'PromotionController@updatePromotionConditionParent');
        Route::get('import/gift/{id}', 'PromotionController@giftMigrate');
        Route::get('import/gift_condition/{id}', 'PromotionController@giftConditionMigrate');
        Route::get('import/gift_condition_update/{id}', 'PromotionController@giftConditionUpdate');
        Route::get('import/promotions_domain', 'PromotionController@PromotionConditionNewRow');
        Route::get('update/voucher/{id}', 'PromotionController@voucherUpdate');
        Route::get('update/gift/{id}', 'PromotionController@giftUpdate');
        Route::get('delete/voucher/{id}', 'PromotionController@voucherConditionDelete');
        Route::get('delete/gift/{id}', 'PromotionController@giftConditionDelete');
        Route::get('import/empty_voucher_code', 'PromotionController@emptyVoucherCode');
        Route::get('import/update_promo/{id}', 'PromotionController@updatePromo');
        Route::get('fix/voucher_template', 'PromotionController@voucherTemplateFix');
	});	
});
