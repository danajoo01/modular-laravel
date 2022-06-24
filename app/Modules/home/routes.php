<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'Home', 'namespace' => 'App\Modules\Home\Controllers'), function() {
		Route::get('/', 'HomeController@index');
		Route::get('/special-promo/{page_url}', 'PromoPageController@index');
		Route::get('/promo/special_deals', 'PromoPageController@specialDeals');
	});	
});
