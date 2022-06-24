<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers;
use \App\Modules\Product\Controllers\ProductController;


use Mockery as m;

class AuthTest extends TestCase
{
	use DatabaseTransactions;

	public function setUp()
    {
        parent::setup();
    }

    /** @test */
	function login_ok()
	{
		$this->visit('/login')
			 ->type('herman@berrybenka.com','customer_email')
			 ->type('123456','password')
			 ->press('login')
			 ->seePageIs('/clothing');	
	}

	/** @test */
	function login_failed()
	{
		$this->visit('/login')
			 ->type('herman@berrybenka1.com','customer_email')
			 ->type('123456','password')
			 ->press('login')
			 ->seePageIs('/login')
			 ->see('Wrong email or password');	
	}

	/** @test */
	function register_ok()
	{
		$this->visit('/login')
			 ->type('herman','customer_fname')
			 ->type('dasril','customer_lname')
			 ->type('herman3@berrybenka.com','customer_email')
			 ->type('123456','password')
			 ->type('123456','password_confirmation')
			 ->press('register')
			 ->seeInDatabase('customer', ['customer_email' => 'herman3@berrybenka.com'])
			 ->seePageIs('/clothing');	
	}

	/** @test */
	function register_failed_confirmation_password_not_match()
	{
		$this->visit('/login')
			 ->type('herman','customer_fname')
			 ->type('dasril','customer_lname')
			 ->type('herman2@berrybenka.com','customer_email')
			 ->type('123456','password')
			 ->type('1234567','password_confirmation')
			 ->press('register')
			 ->seePageIs('/login')
			 ->see('The password confirmation does not match');	
	}

	/** @test */
	function register_failed_customer_email_already_registered()
	{
		$this->visit('/login')
			 ->type('herman','customer_fname')
			 ->type('dasril','customer_lname')
			 ->type('herman@berrybenka.com','customer_email')
			 ->type('123456','password')
			 ->type('123456','password_confirmation')
			 ->press('register')
			 ->seePageIs('/login')
			 ->see('The customer email has already been taken');	
	}

	/** @test */
	function register_failed_password_less_character()
	{
		$this->visit('/login')
			 ->type('herman','customer_fname')
			 ->type('dasril','customer_lname')
			 ->type('herman4@berrybenka.com','customer_email')
			 ->type('12345','password')
			 ->type('12345','password_confirmation')
			 ->press('register')
			 ->seePageIs('/login')
			 ->see('The password must be at least 6 characters');	
	}
}