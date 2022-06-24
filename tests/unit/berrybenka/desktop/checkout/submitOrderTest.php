<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Checkout\Controllers\SubmitOrderController;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\PromotionCondition;

use \App\Modules\Checkout\Controllers;
use \App\Customer;

class SubmitOrderTestBBD extends TestCase
{
	use WithoutMiddleware;

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
			
			$add_draft_order = OrderItem::addDraftOrder();
	}

	/** @test */
	function submit_order_access_redirect_when_not_login_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('GET','http://'.$domain.'/checkout/submit_order');
		
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('login/?continue='.urlencode('/checkout/cart')),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Pengunjung terdaftar? Silahkan masuk dengan Store2Go Account');
	}

	/** @test */
	function submit_order_access_redirect_when_login_but_dont_have_cart_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		
		
		$user = Auth::user();
		
		$products = DB::table('order_item')
							->where('purchase_status','=',0)
							->where('domain_id','=',1)
							->where('customer_id', '=', $user->customer_id)
							->delete();

		$products1 = DB::table('order_item')
							->select('order_item_id')
							->where('purchase_status','=',0)
							->where('domain_id','=',1)
							->where('customer_id', '=', $user->customer_id)
							->get();

		Cart::destroy();

		$response = $this->call('GET','http://'.$domain.'/checkout/submit_order');
	
		$this->assertResponseStatus(302);
		$this->assertEquals(URL::to('checkout/cart'),$response->getTargetUrl());

		$response = $this->call('GET', $response->getTargetUrl());

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('TOTAL');
	}

	/** @test */
	function submit_order_access_ok_when_login_and_have_cart_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');
		
		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		
		
		$add_cart = $this->add_to_cart();

		$response = $this->call('GET','http://'.$domain.'/checkout/submit_order');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('GRAND TOTAL');
	}

	/** @test */
	function check_submit_order_if_customer_dont_have_address_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "effendy@berrybenka.com123","password" => "123123Ef"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_get_customer_address');
		
		$this->seeJsonContains(["have_address" => FALSE]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	
	/** @test */
	function check_submit_order_customer_have_address_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_get_customer_address',["get_primary" => TRUE]);

		$this->seeJsonContains(["have_address" => TRUE]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_shipping_province_and_area_match_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response1 = $this-> call('POST','http://'.$domain.'/checkout/json_set_primary_address',
									['address_type'		=> 1,
									 'address_id'	=> 165259]);

		$response = $this-> call('GET','http://'.$domain.'/checkout/submit_order',["get_primary" => TRUE]);
		
		$this->see('Provinsi dan Kota alamat pengiriman anda salah');
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_add_address_empty_field_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_add_customer_address',
									['address_type'		=> 1,
									 'address_street'	=> 'TEST',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '32323',
									 'address_phone'	=> '']);
		
		$this->seeJsonContains(["id" 				=> NULL,
							 	"result" 			=> FALSE,
								"result_message"	=> "Telpon harus diisi. <br /> <br/>"]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_add_address_wrong_condition_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_add_customer_address',
									['address_type'		=> 1,
									 'address_street'	=> 'S. Parman',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '32323',
									 'address_phone'	=> 'qwertyuiop']);
		
		$this->seeJsonContains(["id"				=> NULL,
								"result" 			=> FALSE,
								"result_message"	=> "Telpon harus berupa angka. <br /> <br/>"]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_add_address_ok_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_add_customer_address',
									['address_type'		=> 1,
									 'address_street'	=> 'Jl. S. Parman',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '12345',
									 'address_phone'	=> '0987654321']);
		
		$this->seeJsonContains(["result" 			=> TRUE,
								"result_message"	=> ""]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_edit_address_empty_field_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_edit_customer_address',
									['address_id'		=>423610,
									 'address_type'		=> 1,
									 'address_street'	=> 'TEST',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '32323',
									 'address_phone'	=> '']);
		
		$this->seeJsonContains(["result" 			=> FALSE,
								"result_message"	=> "Telpon harus diisi. <br /> <br/>"]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_edit_address_with_data_another_user_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_edit_customer_address',
									['address_id'		=>966928,
									 'address_type'		=> 1,
									 'address_street'	=> 'TEST',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '32323',
									 'address_phone'	=> '123123123']);
		
		$this->seeJsonContains(["result" 			=> FALSE,
								"result_message"	=> "Address tidak ditemukan untuk akun ini."]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_edit_address_wrong_condition_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_edit_customer_address',
									['address_id'		=>423610,
									 'address_type'		=> 1,
									 'address_street'	=> 'S. Parman1',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '32323',
									 'address_phone'	=> 'qwertyuiop']);
		
		$this->seeJsonContains(["result" 			=> FALSE,
								"result_message"	=> "Telpon harus berupa angka. <br /> <br/>"]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_edit_address_ok_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_edit_customer_address',
									['address_id'		=>423610,
									 'address_type'		=> 1,
									 'address_street'	=> 'Jl. S. Parman1',
									 'address_province'	=> 'Jakarta',
									 'address_city'		=> 'Jakarta Barat',
									 'address_postcode'	=> '12345',
									 'address_phone'	=> '0987654321']);
		
		$this->seeJsonContains(["result" 			=> TRUE,
								"result_message"	=> ""]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_set_primary_with_data_another_user_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_primary_address',
									['address_type'		=> 1,
									 'address_id'	=> 966928]);
		
		$this->seeJsonContains(["result" => FALSE ]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_set_primary_ok_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_primary_address',
									['address_type'		=> 1,
									 'address_id'	=> 423610]);
		
		$this->seeJsonContains(["result" => TRUE ]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	
	/** @test */
	function check_value_shipping_ok_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('GET','http://'.$domain.'/checkout/submit_order');
		
		$this->assertContains('9,500',$response->getOriginalContent()->getData()['list_shipping_method'][0]['text']);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_method_payment_enabled_submit_order_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('GET','http://'.$domain.'/checkout/submit_order');

		$payment = DB::table('master_payment')
							->select('*')
							->where('enabled_bb','=',1)
							->where('master_payment_enabled','=',1)
							->get();

		$this->assertEquals(array(),$response->getOriginalContent()->getData()['is_popup_store_available']);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals(count($payment), count($response->getOriginalContent()->getData()['list_payment_method']));
		$this->assertEquals(TRUE, $response->getOriginalContent()->getData()['is_cod_available']);
		$this->assertResponseOk();
		$this->see('Transfer BCA');
	}

	/** @test */
	function check_freeshipping_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('GET','http://'.$domain.'/checkout/submit_order');

		var_dump($response->getOriginalContent()->getData()['total']['grand_total_before_benka_point']);
		$this->assertEquals(TRUE,$response->getOriginalContent()->getData()['total']['is_freeshipping']);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_not_freeshipping_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		Cart::destroy();

		\Cart::add(array(
						'id' 	  => 'BEESCLGRON-LJ',
						'name' 	  => 'Esrato Grey Tribal Cardigan',
						'qty' 	  => 1,
						'price'	  => 197000,
						'options' => array(
									'brand_id' 		=> 225,
									'brand_name' 	=> 'Berrybenka Label',
									'front_end_type'=> ',1,10,59,',
									'type_url'  	=> 'clothing,outerwear,cardigans',
									'product_id' 	=> 126869,
									'color_id' 		=> 122,
									'color_name' 	=> 'Grey',
									'size' 			=> 'S',
									'image' 		=> '126869_esrato-grey-tribal-cardigan_grey_IIW9K.jpg',
									'weight'		=> '0.2',
									'price'     	=> 279000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'clothing women', 			/** For tracking sale **/
									'child_track_sale' 	=> 'clothing women',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));
			
		$add_draft_order = OrderItem::addDraftOrder();

		$response = $this-> call('GET','http://'.$domain.'/checkout/submit_order');

		var_dump($response->getOriginalContent()->getData()['total']['grand_total_before_benka_point']);
		$this->assertEquals(FALSE,$response->getOriginalContent()->getData()['total']['is_freeshipping']);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_payment_code_for_transfer_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 1]);
		
		$this->dontSeeJson(["paycode" => 0 ]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_payment_code_exclude_transfer_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 5]);
		
		$this->seeJsonContains(["paycode" => 0 ]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_next_delivery_by_payment_method_exclude_transfer_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 5]);
		
		$result_json = $response->getContent();

		$json_decode = json_decode($result_json,TRUE);;

		$this->assertContains('Next Day Delivery',$json_decode['list_shipping_method'][3]['text']);
		$this->seeJsonStructure(["total" => ["shipping_type", "base_subtotal"]]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_same_next_delivery_by_payment_method_transfer_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 1]);
		
		$result_json = $response->getContent();

		$json_decode = json_decode($result_json,TRUE);;

		foreach($json_decode['list_shipping_method'] as $key => $val){
				$this->assertNotContains('Next Day Delivery',$json_decode['list_shipping_method'][$key]['text']);
		}

		$this->seeJsonStructure(["total" => ["shipping_type", "base_subtotal"]]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_not_freeshipping_when_shipping_method_same_next_delivery_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_shipping_method',
									['shipping_type' => 4,
									 'shipping_id'	 => 1245]);
		
		$result_json = $response->getContent();

		$json_decode = json_decode($result_json,TRUE);;

		$this->assertNotEquals(0,$json_decode['total']['shipping_cost']);
		$this->seeJsonStructure(["total" => ["shipping_type", "base_subtotal"]]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function check_same_next_delivery_by_set_primary_address_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 5]);

		$response1 = $this-> call('POST','http://'.$domain.'/checkout/json_set_primary_address',
									['address_type'		=> 1,
									 'address_id'	=> 966999]);
	
		$response2 = $this-> call('POST','http://'.$domain.'/checkout/json_get_customer_address',["get_primary" => TRUE]);

		$result_json = $response2->getContent();

		$json_decode = json_decode($result_json,TRUE);;

		foreach($json_decode['list_shipping_method'] as $key => $val){
				$this->assertNotContains('Next Day Delivery',$json_decode['list_shipping_method'][$key]['text']);
		}

		$this->seeJsonStructure(["total" => ["shipping_type", "base_subtotal"]]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	
	function check_next_delivery_by_payment_method_exclude_transfer_before_11_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$date_now   = date('Y-m-d H:i:s');
      	$start_date = date('Y-m-d 00:00:00');
      	$end_date   = date('Y-m-d 11:00:00');
      
      	if(date("N") == 5): //Batas same day untuk hari Jumat adalah 10:30
        	$end_date = date('Y-m-d 10:30:00');
      	endif;

      	$start_ts = strtotime($start_date);
      	$end_ts   = strtotime($end_date);
      	$user_ts  = strtotime($date_now);

	    $response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);		

		$add_cart = $this->add_to_cart();

		$response1 = $this-> call('POST','http://'.$domain.'/checkout/json_set_primary_address',
									['address_type'		=> 1,
									 'address_id'	=> 967002]);

		$response = $this-> call('POST','http://'.$domain.'/checkout/json_set_payment_method',
									['payment_method' => 5]);
		
		$result_json = $response->getContent();

		$json_decode = json_decode($result_json,TRUE);

		if(($user_ts >= $start_ts) && ($user_ts <= $end_ts)){
    		$this->assertContains('Same Day Delivery',$json_decode['list_shipping_method'][2]['text']);
		}
		
		$this->assertContains('Next Day Delivery',$json_decode['list_shipping_method'][3]['text']);
		$this->seeJsonStructure(["total" => ["shipping_type", "base_subtotal"]]);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}	
}
