<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers;
use \App\Modules\Account\Controllers\AccountController;
use \App\Http\Controllers\Auth\AuthController;
use \App\Customer;

class AccountTestBBD extends TestCase
{
	use DatabaseTransactions;

	/** @test */
	function account_page_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/account_dashboard');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Halaman Akun');
	}

	/** @test */
	function account_page_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/account_dashboard');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/account_dashboard')),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function account_page_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/account_dashboard');
		
		$this->assertArrayHasKey('user',$account->index());
		$this->assertArrayHasKey('limit',$account->index());
		$this->assertArrayHasKey('credits_history',$account->index());
	}

	/** @test */
	function referral_page_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/referral_program');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Referral Code');
	}

	/** @test */
	function referral_page_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/referral_program');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/referral_program')),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function referral_page_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/referral_program');
		
		$this->assertArrayHasKey('user',$account->referral());
	}

	/** @test */
	function wishlist_page_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/wishlist');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see("Wishlist");
	}

	/** @test */
	function wishlist_page_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/wishlist');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/wishlist')),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function wishlist_page_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/wishlist');
		
		$this->assertArrayHasKey('user',$account->wishlist());
		$this->assertArrayHasKey('wishlist',$account->wishlist());
	}

	/** @test */
	function order_history_page_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/order_history');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Order Anda');
	}

	/** @test */
	function order_history_page_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/order_history');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/order_history')),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function order_history_page_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/order_history');
		
		$this->assertArrayHasKey('user',$account->orderHistory());
		$this->assertArrayHasKey('start_catalog',$account->orderHistory());
		$this->assertArrayHasKey('total_catalog',$account->orderHistory());
	}

	/** @test */
	function setting_page_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/setting');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengaturan');
	}

	/** @test */
	function setting_page_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/setting');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/setting')),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function setting_page_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/setting');
		
		$this->assertArrayHasKey('shipping_area',$account->setting());
		$this->assertArrayHasKey('customer_address',$account->setting());
		$this->assertArrayHasKey('shipping_name',$account->setting());
		$this->assertArrayHasKey('user',$account->setting());
	}

	/** @test */
	function add_address_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','http://'.$domain.'/user/add_address/shipping',
								['address'=>'BERRYBENKA TEST AJA',
								 'shipping_name'=>'Jakarta Barat',
								 'shipping_area'=>'Jakarta',
								 'postcode'=>'1111111112',
								 'phone'=>'081283169163']);
		
		$this->seeInDatabase('customer_address', ['address_street' => 'BERRYBENKA TEST AJA']);
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/setting'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengaturan');
	}

	/** @test */
	function edit_address_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','http://'.$domain.'/user/edit_address/1075917',
								['address'=>'BERRYBENKA TEST AJA 2',
								 'shipping_name'=>'Jakarta Barat',
								 'shipping_area'=>'Jakarta',
								 'postcode'=>'1111111110',
								 'phone'=>'081283169163']);
		
		$this->seeInDatabase('customer_address', ['address_street' => 'BERRYBENKA TEST AJA 2']);
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/setting'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengaturan');
	}

	/** @test */
	function set_primary_address_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/set_primary_address/1/1075917');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/setting'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengaturan');
	}

	/** @test */
	function delete_address_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/delete_address/1075920');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/setting'),$response->getTargetUrl());

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengaturan');
	}

	/** @test */
	function get_shipping_city_new_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/user/get_shipping_city_new');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	
	function return_form_page_show_has_login_ok()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/get_shipping_city_new');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Form Retur');
	}

	
	function return_form_page_show_not_login_redirect_to_login_page()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountController@returnForm');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/return_form')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	
	function return_form_page_show_has_array()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountController@returnForm');
		
		$this->assertArrayHasKey('user',$account->returnForm());
		$this->assertArrayHasKey('total_delivered',$account->returnForm());
		$this->assertArrayHasKey('total_returned',$account->returnForm());
		$this->assertArrayHasKey('delivered_list',$account->returnForm());
		$this->assertArrayHasKey('returned_list',$account->returnForm());
	}

	/** @test */
	function return_purchase_proccess_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/return_purchase/3474061');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function return_purchase_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/return_purchase/3474061');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/return_purchase')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function return_purchase_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"frisella@berrybenka.com","password"=>"zlazlazla"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$params = ['order_item_id' => '9368905'];

		$response = $this->call('GET','http://'.$domain.'/user/return_purchase/9368905');
		
		$this->assertArrayHasKey('user',$account->returnPurchase());
		$this->assertArrayHasKey('return_detail',$account->returnPurchase());
		$this->assertArrayHasKey('available_size',$account->returnPurchase());
		$this->assertArrayHasKey('available_color',$account->returnPurchase());
	}

	/** @test */
	function insert_customer_return_proccess_ok_bbd()
	{
		$response = $this->call('POST','/user/insert_customer_return',
								['return_order_item_id'=>'5712347',
								 'return_sku'=>'MOSASHBL36-YG',
								 'return_purchase_code'=>'1734554515883',
								 'return_customer_id'=>'34251',
								 'return_reason'=>'1',
								 'return_obj'=>'1']);
		
		$this->seeInDatabase('customer_return', ['order_item_id' => '5712347']);
	}

	/** @test */
	function call_wms_return_proccess_ok()
	{
		$response = $this->call('POST','/user/call_wms_return_process',
								['return_order_item_id'=>'5139724',
								 'return_sku'=>'LUFOBAPION-O4',
								 'return_customer_id'=>'34251',
								 'return_reason'=>'5',
								 'return_obj'=>'5',
								 'url'=> WMS_API . 'api/eksternal/ajax_process_return_frontend',
								 'return_note'=>'5 - Pengembalian Pembayaran bca 08877 A/N bon']);
		
	}

	/** @test */
	function call_wms_return_proccess_with_data_not_valid()
	{
		$response = $this->call('POST','/user/call_wms_return_process',
								['return_order_item_id'=>'5712347',
								 'return_sku'=>'MOSASHBL36-YG',
								 'return_customer_id'=>'34251',
								 'return_reason'=>'1',
								 'return_obj'=>'1',
								 'url'=> WMS_API . 'api/eksternal/ajax_process_return_frontend',
								 'return_note'=>'1-Tukar dengan barang yang sama']);
		
	}

	/** @test */
	function cancel_return_proccess_ok()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$params = ['order_item_id' => '55507'];

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountController@cancelReturn',$params);
		
		$session = \Session::get('sukses');
		$this->assertNotEmpty($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/return_form'),$response->getTargetUrl());
		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Form Retur');
	}

	/** @test */
	function ajax_return_proccess_ok()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/user/order_status',['purchase_code'=>'D307ELB26031334251']);
		
		$array_response =$response->getData();
		
		$array = array_shift($array_response);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();

		$this->assertJson($response->getContent());
		$this->assertEquals('D307ELB26031334251', $array->purchase_code);
		$this->assertEquals('VOSTCLWHOF-C1', $array->SKU);
	}

	/** @test */
	function order_history_detail_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/order_history_detail/1103562138333');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Rincian Pemesanan');
	}

	/** @test */
	function order_history_detail_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/order_history_detail/1224353213122');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/order_history_detail/1224353213122')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function order_history_detail_show_has_array_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/order_history_detail/1103562138333');
		
		$this->assertArrayHasKey('user',$account->orderHistoryDetail());
		$this->assertArrayHasKey('data',$account->orderHistoryDetail());
	}

	/** @test */
	function validate_account_proccess_ok()
	{
		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/user/check_account',
								['account-name'=>'HD TEST AJA',
								 'amount'=>'166543',
								 'purchase_code'=>'D307ELB26031334251']);
		
		
		$session = \Session::get('errors');
		$this->assertNull($session);
		

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/order_history_detail/D307ELB26031334251'),$response->getTargetUrl());

		$params = ['purchase_code'=>'D307ELB26031334251'];

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountController@orderHistoryDetail',$params);
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Rincian Pemesanan');
	}

	/** @test */
	function validate_account_proccess_with_no_valid_data_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','http://'.$domain.'/user/check_account',
								['account-name'=>'HD TEST AJA',
								 'purchase_code'=>'D307ELB26031334251']);
		
		$session = \Session::get('errors');
		
		$this->assertNotEmpty($session);
		

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/order_history_detail/D307ELB26031334251'),$response->getTargetUrl());

		$response = $this->call('GET','http://'.$domain.'/user/order_history_detail/D307ELB26031334251');
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Rincian Pemesanan');
	}

	/** @test */
	function edit_personal_detail_show_has_login_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/edit_personal_detail');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Ubah Alamat');
	}

	/** @test */
	function edit_personal_detail_show_not_login_redirect_to_login_page_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/user/edit_personal_detail');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/login'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function edit_personal_detail_show_has_array()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$account = new AccountController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('GET','http://'.$domain.'/user/edit_personal_detail');
		
		$this->assertArrayHasKey('user',$account->orderHistoryDetail());
	}

	/** @test */
	function save_personal_detail_proccess_ok()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/user/save_personal_detail',
								['customer_fname'=>'herman',
								 'customer_lname'=>'dasril',
								 'yy' => '1990',
								 'mm' => '01',
								 'dd' => '12',
								 'customer_phone' => '081283169163',
								 'customer_gender' => '1',
								 'how_did_you_know_us' => 'Friends']);
		
		$this->seeInDatabase('customer', ['customer_id' => '34251','customer_date_of_birth' => '1990-01-12']);
		
		$session = \Session::get('messages');
		
		$this->assertNull($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/edit_personal_detail/'),$response->getTargetUrl());

		$account = new AccountController();

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountController@editPersonalDetail');
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Ubah Alamat');
	}
	
}