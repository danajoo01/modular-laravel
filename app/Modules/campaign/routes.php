<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'Campaign', 'namespace' => 'App\Modules\Campaign\Controllers'), function() {

	    Route::get('campaign/page/{campaign_name}', 'CampaignController@index');
	    Route::post('campaign/subscribe', 'CampaignController@subscribe');
	    
	});	
});