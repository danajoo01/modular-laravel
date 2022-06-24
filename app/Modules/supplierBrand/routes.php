<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	Route::group(array('module' => 'SupplierBrand', 'namespace' => 'App\Modules\SupplierBrand\Controllers'), function() {

	    /*Route::get('/brand_report', 'SupplierBrandController@index');
	    Route::get('/brand_report/login', 'SupplierBrandController@index');
	    Route::get('/brand_report/index/id/{customer_id}', 'SupplierBrandController@index');
	    Route::get('/brand_report/index/id/{customer_id}/brand-id/{brand_id}', 'SupplierBrandController@index');
	    Route::get('/brand_report/inventory/customer-id/{customer_id}/brand-id/{brand_id}', 'SupplierBrandController@inventory');
	    Route::get('/brand_report/get_csv_inventory/{customer_id}/{brand_id}/{status}', 'SupplierBrandController@getCsvInventory');*/
	    
	});	
});