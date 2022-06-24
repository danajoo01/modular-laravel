<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Checkout\Controllers\CartController;
use \App\Modules\Checkout\Controllers;

class CartTestHBD extends TestCase
{
	function add_to_cart()
	{
		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'RAKECLWHS0-11',
						'name' 	  => 'Kerry Abaya',
						'qty' 	  => 1,
						'price'	  => 540000,
						'options' => array(
									'brand_id' 		=> 1524,
									'brand_name' 	=> 'RA by Restu Anggraini',
									'front_end_type'=> ',1,163,',
									'type_url'  	=> 'clothing,dresses-and-jumpsuit',
									'product_id' 	=> '57946',
									'color_id' 		=> 101,
									'color_name' 	=> 'White',
									'size' 			=> 'One Size',
									'image' 		=> '57946_kerry-abaya_white_FS05A.jpg',
									'weight'		=> '0.2',
									'price'     	=> 540000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'dresses-and-jumpsuit women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'dresses-and-jumpsuit women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));
	}

	/** @test */
	function cart_access_redirect_when_not_login_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('login/?continue='.urlencode('/checkout/cart')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Hijabenka Account');
	}

	/** @test */
	function cart_access_ok_when_login_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);
		
		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('TOTAL');
	}

	/** @test */
	function check_cart_with_product_have_session_and_qty_cart_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["inv_status" => 1]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_have_session_but_out_of_qty_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'RAKECLWHS0-11',
						'name' 	  => 'Kerry Abaya',
						'qty' 	  => 200,
						'price'	  => 540000,
						'options' => array(
									'brand_id' 		=> 1524,
									'brand_name' 	=> 'RA by Restu Anggraini',
									'front_end_type'=> ',1,163,',
									'type_url'  	=> 'clothing,dresses-and-jumpsuit',
									'product_id' 	=> '57946',
									'color_id' 		=> 101,
									'color_name' 	=> 'White',
									'size' 			=> 'One Size',
									'image' 		=> '57946_kerry-abaya_white_FS05A.jpg',
									'weight'		=> '0.2',
									'price'     	=> 540000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'dresses-and-jumpsuit women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'dresses-and-jumpsuit women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["inv_status" => 2]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_with_product_not_in_db_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		Cart::destroy();
		
		\Cart::add(array(
						'id' 	  => 'RAKECLWHS0-11',
						'name' 	  => 'Kerry Abaya',
						'qty' 	  => 1,
						'price'	  => 540000,
						'options' => array(
									'brand_id' 		=> 1524,
									'brand_name' 	=> 'RA by Restu Anggraini',
									'front_end_type'=> ',1,163,',
									'type_url'  	=> 'clothing,dresses-and-jumpsuit',
									'product_id' 	=> '100000000000',
									'color_id' 		=> 101,
									'color_name' 	=> 'White',
									'size' 			=> 'One Size',
									'image' 		=> '57946_kerry-abaya_white_FS05A.jpg',
									'weight'		=> '0.2',
									'price'     	=> 540000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'dresses-and-jumpsuit women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'dresses-and-jumpsuit women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["total_cart" => 0]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_with_product_not_have_session_cart_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["total_cart" => 0]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_update_qty_ok_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'		=> 'RAKECLWHS0-11',
								 'quantity'	=> 1]);
		
		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');

		$this->seeJsonContains(["inv_status" => 1]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_update_qty_out_of_stock_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'		=>	'RAKECLWHS0-11',
								 'quantity'	=>	200]);
		
		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');

		$this->seeJsonContains(["inv_status" => 2]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_delete_product_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'			=> 'RAKECLWHS0-11',
								 'quantity'		=> 1,
								 'is_delete'	=> 1]);
		
		$this->seeJsonContains(["result" => TRUE]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}
}