<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Checkout\Controllers\CartController;
use \App\Modules\Checkout\Controllers;

class CartTestBBM extends TestCase
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
	function cart_access_redirect_when_not_login_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('login/?continue='.urlencode('/checkout/cart')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Belum Punya Akun Berrybenka?');
	}

	/** @test */
	function cart_access_ok_when_login_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');
		
		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);
		
		$response = $this->call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Tas Belanja Saya');
	}

	/** @test */
	function check_cart_with_product_have_session_and_qty_cart_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$add_cart = $this->add_to_cart();

		$response = $this-> call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_have_session_but_out_of_qty_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'BEHACLGRS0-MT',
						'name' 	  => 'Hattie Green Tone Dress',
						'qty' 	  => 200,
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

		$response = $this-> call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Maksimal quantity untuk product ini');
	}

	/** @test */
	function check_cart_with_product_not_in_db_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);
		
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

		$response = $this-> call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Cart is empty');
	}

	/** @test */
	function check_cart_with_product_not_have_session_cart_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$response = $this-> call('GET','http://'.$domain.'/checkout/cart');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Cart is empty');
	}

	/** @test */
	function check_cart_update_qty_ok_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/update_cart',
								['SKU'		=> 'BEHACLGRS0-MT',
								 'quantity'	=> 1]);
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('checkout/cart'),$response->getTargetUrl());

		$response2 = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response2->getStatusCode());
		$this->assertResponseOk();
		$this->see('Tas Belanja Saya');
	}

	/** @test */
	function check_cart_update_qty_out_of_stock_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/update_cart',
								['SKU'		=>	'BEHACLGRS0-MT',
								 'quantity'	=>	200]);
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('checkout/cart'),$response->getTargetUrl());

		$response2 = $this->call('GET', $response->getTargetUrl());

		$this->see('Maksimal quantity untuk product ini');
		$this->assertEquals(200, $response2->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_cart_delete_product_bbm()
	{
		$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');

		$request = ["customer_email"=>"herman@berrybenka.com","password"=>"123456"];
		$remember = null;
		
		$user = Auth::attempt($request,$remember);
		
		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/update_cart',
								['SKU'			=> 'BEHACLGRS0-MT',
								 'quantity'		=> 1,
								 'is_delete'	=> 1]);
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('checkout/cart'),$response->getTargetUrl());

		$response2 = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response2->getStatusCode());
		$this->assertResponseOk();
		$this->see('Tas Belanja Saya');
	}
}