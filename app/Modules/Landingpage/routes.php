<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'Landingpage', 'namespace' => 'App\Modules\Landingpage\Controllers'), function() {
            /**
            * Landing page - Berrybenka Face 
            */
	    Route::get('/berrybenkaface', 'BerrybenkafaceController@index');
	    Route::post('/berrybenkaface/register', 'BerrybenkafaceController@postBerrybenkaFace');
            
            /**
            * Landing page - Bulletin Ads 
            */
	    Route::get('/bulletin-ads', 'BulletinController@index');  
	});	
});