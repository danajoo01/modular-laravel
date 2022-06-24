<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers;
use \App\Modules\Account\Controllers\AccountPasswordController;
use \App\Http\Controllers\Auth\AuthController;

class AccountPasswordTest extends TestCase
{
	/** @test */
	function reset_password_proccess_ok()
	{
		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@resetPassword');
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('/forgot_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Lupa Password Anda');
	}

	/** @test */
	function reset_password_proccess_has_login_ok()
	{
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@resetPassword');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/forgot_password'),$response->getTargetUrl());

		$response2 = $this->call('GET', $response->getTargetUrl());

		$this->assertResponseStatus(302);
		
		$user = Auth::attempt($request,$remember);

		$this->assertEquals(URL::to('/login/?continue='.urlencode('/user/change_password')),$response2->getTargetUrl());

		$response3 = $this->call('GET', $response2->getTargetUrl());

		$this->assertResponseStatus(302);

		$response4 = $this->call('GET', $response3->getTargetUrl());
		$this->assertEquals(200, $response4->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function change_password_show_has_login_ok()
	{
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@changePassword');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Password Baru*');
	}

	/** @test */
	function change_password_show_not_login_redirect_to_login_page()
	{
		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@changePassword');
		
		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('login/?continue='.urlencode('/user/change_password')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function change_password_show_has_array()
	{
		$account = new AccountPasswordController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@changePassword');
		
		$this->assertArrayHasKey('user',$account->changePassword());
	}

	/** @test */
	function update_password_error_cause_password_less_than_6()
	{
		$account = new AccountPasswordController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/user/update_password',['password'=>'12345']);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/change_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Password Baru*');
	}

	/** @test */
	function update_password_success()
	{
		$account = new AccountPasswordController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/user/update_password',['password'=>'123456']);

		$session = \Session::get('message');
		
		//$this->assertNotEmpty($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/change_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Password Baru*');
	}

	/** @test */
	function forgot_password_proccess_without_login_ok()
	{
		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@forgotPassword');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Lupa Password Anda');
	}

	/** @test */
	function forgot_password_proccess_has_login_ok()
	{
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@forgotPassword');
		
		$this->assertResponseStatus(302);
		
		$user = Auth::attempt($request,$remember);

		$this->assertEquals(URL::to('/login/?continue='.urlencode('/user/change_password')),$response->getTargetUrl());

		$response3 = $this->call('GET', $response->getTargetUrl());

		$this->assertResponseStatus(302);

		$response4 = $this->call('GET', $response3->getTargetUrl());
		$this->assertEquals('200', $response4->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function forgot_password_show_has_array()
	{
		$account = new AccountPasswordController();

		$response = $this->action('GET','\App\Modules\Account\Controllers\AccountPasswordController@forgotPassword');
		
		$this->assertArrayHasKey('user',$account->forgotPassword());
		$this->assertArrayHasKey('domain_name',$account->forgotPassword());
	}

	/** @test */
	function forgot_password_post_proccess_ok()
	{
		$account = new AccountPasswordController();

		$response = $this->call('POST','/forgot_password/post',['customer_email'=>'herman@berrybenka.com']);

		$session = \Session::get('message');
		
		$this->assertNotEmpty($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/forgot_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Lupa Password Anda');
	}

	/** @test */
	function forgot_password_post_email_not_registered()
	{
		$account = new AccountPasswordController();

		$response = $this->call('POST','/forgot_password/post',['customer_email'=>'hermantestinggkada@berrybenka.com']);

		$session = \Session::get('error_message');
		
		$this->assertNotEmpty($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/forgot_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Lupa Password Anda');
	}

	/** @test */
	function forgot_password_post_invalid_email()
	{
		$account = new AccountPasswordController();

		$response = $this->call('POST','/forgot_password/post',['customer_email'=>'hermantestinberrybenka']);

		$session = \Session::get('error_message');
		
		$this->assertNotEmpty($session);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/forgot_password'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Lupa Password Anda');
	}

	/** @test */
	function forgot_password_post_with_login_will_redirect()
	{
		$account = new AccountPasswordController();

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this->call('POST','/forgot_password/post',['customer_email'=>'herman@berrybenka.com']);

		$this->assertResponseStatus(302);
		
		$this->assertEquals(URL::to('/user/account_dashboard'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());
		$this->assertEquals('200', $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Halaman Akun');
	}		
}