<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Checkout\Controllers\CartController;
use \App\Modules\Checkout\Controllers;

class CartTestBBD extends TestCase
{
	function add_to_cart()
	{
		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'BEHACLGRS0-MT',
						'name' 	  => 'Hattie Green Tone Dress',
						'qty' 	  => 1,
						'price'	  => 279000,
						'options' => array(
									'brand_id' 		=> 225,
									'brand_name' 	=> 'Berrybenka Label',
									'front_end_type'=> ',1,7,46,',
									'type_url'  	=> 'clothing,dresses,casual',
									'product_id' 	=> '135328',
									'color_id' 		=> 54,
									'color_name' 	=> 'Green',
									'size' 			=> 'S',
									'image' 		=> '135328_hattie-green-tone-dress_green_Z2IRV.jpg',
									'weight'		=> '0.2',
									'price'     	=> 279000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'new-arrival women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'new-arrival women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));
	}

	/** @test */
	function cart_access_redirect_when_not_login_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('login/?continue='.urlencode('/checkout/cart')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function cart_access_ok_when_login_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');
		
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);
		
		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('TOTAL');
	}

	/** @test */
	function check_cart_with_product_have_session_and_qty_cart_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["inv_status" => 1]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_have_session_but_out_of_qty_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'BEHACLGRS0-MT',
						'name' 	  => 'Hattie Green Tone Dress',
						'qty' 	  => 50,
						'price'	  => 279000,
						'options' => array(
									'brand_id' 		=> 225,
									'brand_name' 	=> 'Berrybenka Label',
									'front_end_type'=> ',1,7,46,',
									'type_url'  	=> 'clothing,dresses,casual',
									'product_id' 	=> '135328',
									'color_id' 		=> 54,
									'color_name' 	=> 'Green',
									'size' 			=> 'S',
									'image' 		=> '135328_hattie-green-tone-dress_green_Z2IRV.jpg',
									'weight'		=> '0.2',
									'price'     	=> 279000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'new-arrival women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'new-arrival women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["inv_status" => 2]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_with_product_not_in_db_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'BEHACLGRS0-MT',
						'name' 	  => 'Hattie Green Tone Dress',
						'qty' 	  => 1,
						'price'	  => 279000,
						'options' => array(
									'brand_id' 		=> 225,
									'brand_name' 	=> 'Berrybenka Label',
									'front_end_type'=> ',1,7,46,',
									'type_url'  	=> 'clothing,dresses,casual',
									'product_id' 	=> '100000000000',
									'color_id' 		=> 54,
									'color_name' 	=> 'Green',
									'size' 			=> 'S',
									'image' 		=> '135328_hattie-green-tone-dress_green_Z2IRV.jpg',
									'weight'		=> '0.2',
									'price'     	=> 279000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'new-arrival women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'new-arrival women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["total_cart" => 0]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_with_product_not_have_session_cart_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');
		
		$this->seeJsonContains(["total_cart" => 0]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_update_qty_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'		=> 'BEHACLGRS0-MT',
								 'quantity'	=> 1]);
		
		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');

		$this->seeJsonContains(["inv_status" => 1]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_update_qty_out_of_stock_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'		=>	'BEHACLGRS0-MT',
								 'quantity'	=>	100]);
		
		$response = $this-> call('POST','http://'.$domain.'/checkout/json_load_cart');

		$this->seeJsonContains(["inv_status" => 2]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_delete_product_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_update_cart',
								['SKU'			=> 'BEHACLGRS0-MT',
								 'quantity'		=> 1,
								 'is_delete'	=> 1]);
		
		$this->seeJsonContains(["result" => TRUE]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}
}