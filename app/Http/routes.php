<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web','secure.url']], function () {
	Route::get('/', function () {

		$get_domain = get_domain();

		switch($get_domain['channel']) {
            case 1:
                return view('layouts.berrybenka.desktop.main');
            break;
            case 2: 
                return view('layouts.berrybenka.mobile.main');
            break;
            case 3: 
                return view('layouts.hijabenka.desktop.main');
            break;
            case 4: 
                return view('layouts.hijabenka.mobile.main');
            break;
            case 5: 
                return view('layouts.shopdeca.desktop.main');
            break;
            case 6: 
                return view('layouts.shopdeca.mobile.main');
            break;
        }
    
	});

    Route::get('/error_500', 'HomeController@Error');
    Route::get('/qtest', 'HomeController@queue');
    Route::get('/logtest', 'HomeController@log');
    Route::get('/status', 'HomeController@status');
    Route::get('/boanversion', 'HomeController@boanversion');
    Route::get('/getmysqlversion', 'HomeController@getMySQLVersion');
    Route::get('/mysql_conn', 'HomeController@mysql_conn');
    Route::get('/mysql_client', 'HomeController@mysql_client');
    Route::post('/jslog', 'HomeController@js_log');
    // Route::get('login', )
	
	
    Route::get('/home', 'HomeController@index');
    Route::get('/home/about', 'HomeController@about');
    Route::get('/affiliate', 'HomeController@affiliate');
    Route::get('/affiliate/{page}', 'HomeController@affiliate');
    Route::get('/home/brand-list', 'HomeController@brand');
    Route::get('/home/cod', 'HomeController@cod');
    Route::get('/home/faq', 'HomeController@faq');
    Route::get('/home/featured_brand', 'HomeController@featuredBrand');
    Route::get('/home/help_return', 'HomeController@helpReturn');
    Route::get('/home/help_return_watch', 'HomeController@helpReturnWatch');
    Route::get('/home/how_to_order', 'HomeController@howToOrder');
    //Route::get('/special-promo/referral-program', 'HomeController@referal');
    Route::get('/home/same-day', 'HomeController@sameDay');
    Route::get('/home/shipping_handling', 'HomeController@shipping');
    Route::get('/home/term_condition', 'HomeController@termCondition');
    Route::get('/home/privacy', 'HomeController@privacy');
    Route::get('/home/download_pdf', 'HomeController@DownloadPDF');
    Route::get('/home/contact', 'HomeController@contact');
    Route::get('/kredivo', 'HomeController@kredivo');

    Route::auth();

    Route::get('/user/auth_cs', 'Auth\AuthController@getAuthCs');
    Route::post('/user/auth_cs', 'Auth\AuthController@postAuthCs');

    Route::group(['middleware' => ['auth.cs']], function () {
        Route::get('/user/customer_auth', 'Auth\AuthController@getCustomerAuth');
        Route::post('/user/customer_auth', 'Auth\AuthController@postCustomerAuth');
    });

    Route::get('auth/facebook', 'Auth\AuthController@redirectToProvider');
    Route::get('auth/facebook/callback', 'Auth\AuthController@handleProviderCallback');

    Route::get('auth/line', 'Auth\AuthController@handleLineCallback');

    /*Route::get('test_line', function() {
        
        $url = 'https://access.line.me/dialog/oauth/weblogin?response_type=code&client_id=1478974875&redirect_uri='. urlencode('http://irfan.berrybenka.biz/auth/line'). '&state='. str_random(5);

        return $url;
    });*/
});
