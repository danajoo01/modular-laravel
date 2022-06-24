<?php

include_once __DIR__.'/../TestLoader.php';
use App\Http\Controllers\Auth;

class Login2Test extends TestLoader
{
	/*public function setUp()
	{
		$this->setBrowser('firefox');
		$this->setBrowserUrl('http://berrybenka-laravel.dev/');
	}*/

	/** @test */
	function login2_ok()
	{
		$auth = new AuthController();

		//$this->login();
$response = $this->action('GET','\App\Http\Controllers\Auth\AuthController@login');
			// ->type('herman@berrybenka.com','customer_email');
		//$this->type('123456','password');
		//$this->press('login');
		//$this->seePageIs('/clothing');
			//$response = $this->call('GET', '/login');
	}

	
}