<?php
Route::group(['middleware' => ['web','secure.url']], function () {
	
	Route::group(array('module' => 'Product', 'namespace' => 'App\Modules\Product\Controllers'), function() {

		/* Route to update user address priority */
		Route::get('/user/set_primary_address/{address_type}/{address_id}', function() {
		 	$params['address_id']   = \Request::segment(4);
		    $params['address_type'] = \Request::segment(3);

		    $set_primary_address = App\Customer::setPrimaryAddress($params);

		    if($set_primary_address == true){
		        return redirect('/user/setting?address_type='.$params['address_type']);
		    }
		 })->where(array('address_type' => '[0-9]+', 'address_id' => '[0-9]+'));

		//Route::get('/product', 'ProductController@filter_type');
		Route::get('/generate_alltype', 'CronCategoryController@generateTypeCategory');
		
		Route::get('/special/{id}/{special_page_name}', 'ProductController@promo')
				->where(array('id' => '[0-9]+', 'special_page_name' => '[0-9a-zA-Z._-]+'));

		Route::get('/brand/{brand_name}', 'ProductController@brand')
				->where(array('brand_name' => '[0-9a-zA-Z._-]+'));
				
		Route::get('/brand/{brand_name}/{limit}', 'ProductController@brand')
				->where(array('brand_name' => '[0-9a-zA-Z._-]+','limit' => '[0-9]+'));

                Route::get('/tag', 'ProductController@TagsList');
		Route::get('/tag/{tag_name}', 'ProductController@tag')
				->where(array('tag_name' => '[0-9a-zA-Z._-]+'));

		Route::get('/filter_type/{id}/{gender}', 'CronCategoryController@call_filter_type');

		Route::get('/run_filter_type', 'CronCategoryController@run_filter_type');

		Route::get('/run_offline_store', 'CronCheckoutController@run_offline_store');

		Route::get('/run_homepage_cache/{channel}', 'CronCheckoutController@run_homepage_cache');

		Route::get('/run_parent_type', 'CronCategoryController@run_parent_type');

		Route::get('/run_test_create', 'CronCategoryController@testCreate');

		Route::get('/run_menu_type', 'CronCategoryController@run_menu_type');

		Route::get('/parent_type/{gender}', 'CronCategoryController@call_parent_type');

		Route::get('/menu_type/{gender}', 'CronCategoryController@call_menu_type');
		
		Route::get('{parent}/size/{id}/{all}', 'ProductController@index')
				->where(array('parent' => '[a-zA-Z\-]+', 'id' => '[0-9]+', 'all' => '[a-zA-Z._-]+'));
		
		Route::get('{parent}/{category}/{id}/{productname}', 'ProductDetailController@index')
				->where(array('parent' => '[a-zA-Z\-]+', 'child' => '[a-zA-Z\-]+', 'id' => '[0-9]+', 'productname' => '[0-9a-zA-Z._-]+'));
		
		Route::get('product/set_wishlist', 'ProductDetailController@set_wishlist');

		// $path = public_path() . "/upload/genfile/dd.ss.json"; // ie: /var/www/laravel/app/public/upload/genfile/json/filename.json
	 	//    if (!File::exists($path)) {
	 	//        throw new Exception("Invalid File");
	 	//    }

		$path = path_type();
		if (!File::exists($path)) {
	 	    //throw new Exception("Invalid File");
			$path = path_type_public();
	 	}

	    $get_frontend_type = json_decode(@file_get_contents($path), TRUE);
      if(is_array($get_frontend_type)){
        array_push($get_frontend_type,"new-arrival","sale");
        foreach ($get_frontend_type as $key => $value) { //clothing, dresses
          Route::get('{gender}/'.$value ,'ProductController@index')
            ->where(array('gender' => '(men|women)'));

          Route::get('{gender}/'.$value.'/{all}' ,'ProductController@index')
            ->where(array('gender' => '(men|women)', 'all' => '.*'));

          Route::get($value.'/{gender}' ,'ProductController@index')
            ->where(array('gender' => '(men|women)'));

          Route::get($value.'/{all}' ,'ProductController@index')
            ->where(array('all' => '.*'));

          Route::get($value, 'ProductController@index');
        }
      }

		Route::get('/product/get_image_color', 'ProductDetailController@get_image_color');
		Route::get('/product/get_image_color_mobile', 'ProductDetailController@get_image_color_mobile');

		Route::get('get_catalog', 'ProductController@getCatalog');
		Route::get('product/addtocart', 'ProductDetailController@add_to_cart');
		
		Route::get('run_brand_parent_type/{brand_url}', 'CronCategoryController@run_brand_parent_type');
		Route::get('run_tag_parent_type/{tag_url}', 'CronCategoryController@run_tag_parent_type');

		Route::get('/migrate-file-visit', 'ProductDetailController@migrateFileProductVisit');

		/* Route for search */
		Route::get('/home/search', 'SearchController@ajaxSearch');
    
		Route::match(['get', 'post'], '/search', 'SearchController@search');
		Route::match(['get', 'post'], '/search/{limit}', 'SearchController@search')
					->where(array('limit' => '[0-9-]+'));

		
        Route::get('/products/{any}', function () {
            return redirect('/new-arrival');
        })->where('any', '.*');
    });	
});