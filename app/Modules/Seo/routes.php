<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'Seo', 'namespace' => 'App\Modules\Seo\Controllers'), function() {

	    Route::resource('Seo/manage', 'SeoController');
	    
	});	
});