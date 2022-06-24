<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Checkout\Controllers\SubmitOrderController;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\PromotionCondition;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Controllers;
use \App\Customer;

class CheckFreegiftAutoTrueTestBBD extends TestCase
{

	use WithoutMiddleware;

	function add_to_cart($cek)
	{
		if($cek){
			Cart::destroy();
		}
		
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

	function add_to_cart_false($cek)
	{
		if($cek){
			Cart::destroy();
		}
		
		\Cart::add(array(
						'id' 	  => 'SAMESHBL39-T6',
						'name' 	  => 'Men Spectra Blackmagma Maroon',
						'qty' 	  => 1,
						'price'	  => 135000,
						'options' => array(
									'brand_id' 		=> 2083,
									'brand_name' 	=> 'Sabertooth',
									'front_end_type'=> ',2,16,',
									'type_url'  	=> 'shoes,sandals',
									'product_id' 	=> 121452,
									'color_id' 		=> 127,
									'color_name' 	=> 'Black Maroon',
									'size' 			=> '39',
									'image' 		=> '121452_men-spectra-blackmagma-maroon_black_EBFT4.jpg',
									'weight'		=> '1',
									'price'     	=> 135000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'shoes men', 			/** For tracking sale **/
									'child_track_sale' 	=> 'shoes men',            /** For tracking sale **/
                  					'gender'  => 2
							)
					));
			
			$add_draft_order = OrderItem::addDraftOrder();
	}

	function add_to_cart_2($cek)
	{
		if($cek){
			Cart::destroy();
		}
		
		\Cart::add(array(
						'id' 	  => 'BEGRCLBLM0-57',
						'name' 	  => 'Gryta Dress Black',
						'qty' 	  => 2,
						'price'	  => 299000,
						'options' => array(
									'brand_id' 		=> 225,
									'brand_name' 	=> 'Berrybenka Label',
									'front_end_type'=> ',1,7,46,',
									'type_url'  	=> 'clothing,dresses,casual',
									'product_id' 	=> '130884',
									'color_id' 		=> 127,
									'color_name' 	=> 'Black',
									'size' 			=> 'M',
									'image' 		=> '130884_gryta-dress-black_black_RIIQ2.jpg',
									'weight'		=> '0.2',
									'price'     	=> 299000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'clothing dresses', 			/** For tracking sale **/
									'child_track_sale' 	=> 'clothing dresses',            /** For tracking sale **/
                  					'gender'  => 1
							)
					));
			
			$add_draft_order = OrderItem::addDraftOrder();
	}

	function add_to_cart_2_false($cek)
	{
		if($cek){
			Cart::destroy();
		}
		
		\Cart::add(array(
						'id' 	  => 'SAMESHBL39-T6',
						'name' 	  => 'Men Spectra Blackmagma Maroon',
						'qty' 	  => 1,
						'price'	  => 135000,
						'options' => array(
									'brand_id' 		=> 2083,
									'brand_name' 	=> 'Sabertooth',
									'front_end_type'=> ',2,16,',
									'type_url'  	=> 'shoes,sandals',
									'product_id' 	=> 121452,
									'color_id' 		=> 127,
									'color_name' 	=> 'Black Maroon',
									'size' 			=> '39',
									'image' 		=> '121452_men-spectra-blackmagma-maroon_black_EBFT4.jpg',
									'weight'		=> '1',
									'price'     	=> 135000,
									'sale_price' 	=> 0,
									'special_price' => 0,
									'promo_id'    	=> '',
									'promo_name'    => '',
									'utm_source' 	=> '',
									'utm_medium' 	=> '',
									'utm_campaign' 	=> '',
									'parent_track_sale' => 'shoes men', 			/** For tracking sale **/
									'child_track_sale' 	=> 'shoes men',            /** For tracking sale **/
                  					'gender'  => 2
							)
					));
			
			$add_draft_order = OrderItem::addDraftOrder();
	}

	function set_customer($key,$domain,$keycon)
	{
		\Auth::logout();

		if($key == 22 && !$keycon){
			$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "anochtavio@gmail.com","password" => "inop22"]);
	    }elseif($key == 27 && !$keycon){
	    	$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "effendy@berrybenka.com","password" => "123123Ef"]);	
	    }else{
	    	$response = $this->call('POST','http://'.$domain.'/login',["customer_email" => "herman@berrybenka.com","password" => "123456"]);
	    }

	    return $response;
	}

	function set_cart($key,$keycon,$cek = FALSE,$gender = TRUE)
	{
		if(($key == 5 && $keycon) || $gender == FALSE){
	    	$add_cart = $this->add_to_cart_2($cek);
	    	$qty = 2;

	    	return array($add_cart,$qty);
	    }elseif($key == 5 && !$keycon){
	    	$add_cart = $this->add_to_cart_2_false($cek);
	    	$qty = 2;

	    	return array($add_cart,$qty);
	    }elseif(!$keycon){
	    	$add_cart = $this->add_to_cart_false($cek);
	    	$qty = 1;

	    	return array($add_cart,$qty);
	    }else{
	    	$add_cart = $this->add_to_cart($cek);
	    	$qty = 1;

	    	return array($add_cart,$qty);
	    }

	    
	}

	/*
		Check domain for berrybenka or hijabenka and desktop or mobile
	*/
	function set_domain($key,$keycon)
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		//If freegift condition by platform device
		if($key == 28){
			if($keycon == TRUE){
				session()->put('platform_domain', 1);
				session()->put('platform_device', 1 );
			}else{
				$domain = env('BERRYBENKA_MOBILE', 'm-herman.berrybenka.biz');
				session()->put('platform_domain', 1);
				session()->put('platform_device', 2 );
			}
		}

		//If freegift condition by platform domain
		if($key == 30){
			if($keycon == TRUE){
				session()->put('platform_domain', 1);
			}else{
				$domain = env('HIJABENKA', 'herman.hijabenka.biz');
				session()->put('platform_domain', 2);
			}
		}

		return $domain;
	}

	function enabled_freegift_auto_status($key)
	{
		$update['enabled'] = 1;
	    $update_template = DB::table('promotions_template')
	      						->where('promotions_template_id', $key)
	      						->update($update);

	    return TRUE;
	}

	function disabled_freegift_auto_status($key)
	{
		$update['enabled'] = 0;
	    $update_template = DB::table('promotions_template')
	      						->where('promotions_template_id', $key)
	      						->update($update);

	    return TRUE;
	}

	function set_day($key,$keycon)
	{
		if($key == 17 && $keycon){
	    	$selected_day = array();
		   	array_push($selected_day,3);
		   
		    session()->put('selected_day', $selected_day);
	    }

	    return TRUE;
	}

	function set_city($key)
	{
		if($key == 27){
	    	$list_shipping_method = Shipping::getShippingMethod();
			$shipping_id = $list_shipping_method[0]['id'];
			session()->put('shipping_id', $shipping_id);
	    }

	    return TRUE;
	}

	function delete_order_diskon($id,$keycon)
	{
		if($id == 21 && !$eycon){
	    	$delete = DB::table('order_discount')
						->where('purchase_code', '000000')
						->where('order_item_id', 1001)
						->delete();
	    }

	    return TRUE;
	}

	function set_template_subtotal($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 3;
			$equal_value = '200000';
		}else{
			$equal_type = 1;
			$equal_value = '1000';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_total_qty($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 1;
			$equal_value = '2';
		}else{
			$equal_type = 1;
			$equal_value = 4;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_product($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 9;
			$equal_value = '^135328^';
		}else{
			$equal_type = 9;
			$equal_value = '^1^';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_brand($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 9;
			$equal_value = '^225^';
		}else{
			$equal_type = 9;
			$equal_value = '^1^';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_category($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 9;
			$equal_value = '^1^,^7^,^46^,^47^,^48^,^49^';
		}else{
			$equal_type = 9;
			$equal_value = '^4^,^24^';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_day($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 9;
			$equal_value = '^3^';
		}else{
			$equal_type = 9;
			$equal_value = '^7^';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_gender($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 1;
			$equal_value = 1;
		}else{
			$equal_type = 1;
			$equal_value = 1;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_max_usage($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 4;
			$equal_value = 1;
		}else{
			$equal_type = 4;
			$equal_value = 1;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_customer($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 1;
			$equal_value = 1;
		}else{
			$equal_type = 1;
			$equal_value = 1;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_city($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 9;
			$equal_value = '^717^';
		}else{
			$equal_type = 9;
			$equal_value = '^717^';
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_device($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 1;
			$equal_value = 1;
		}else{
			$equal_type = 1;
			$equal_value = 1;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	function set_template_domain($template_id,$cond_id,$key,$keycon)
	{
		if($keycon){
			$equal_type = 1;
			$equal_value = 1;
		}else{
			$equal_type = 1;
			$equal_value = 1;
		}

		$delete_template = DB::table('promotions_condition')
				->where('promotions_condition_parent_id','!=', 0)
				->where('promotions_template_id','=',$template_id)
				->delete();

		$create_cond = array();
     	$create_cond['promotions_condition_parent_id']	= $cond_id;
      	$create_cond['promotions_template_id']          = $template_id;
      	$create_cond['promotions_type_condition']       = $key;
      	$create_cond['promotions_type_all_required']    = 0;
      	$create_cond['promotions_type_rules_type']      = 1;
      	$create_cond['promotions_type_equal_type']      = $equal_type;
      	$create_cond['promotions_type_equal_value']     = $equal_value;
      	
      	$insert_temp_con = DB::table('promotions_condition')->insert($create_cond); 
	}

	/** @test */
	function check_freegift_auto_no_comb_true_bbd()
	{
		$condition = [];
		$condition[4][0] = TRUE;
		$condition[4][1] = FALSE;
		$condition[5][0] = TRUE;
		$condition[5][1] = FALSE;
		$condition[13][0] = TRUE;
		$condition[13][1] = FALSE;
		$condition[14][0] = TRUE;
		$condition[14][1] = FALSE;
		$condition[15][0] = TRUE;
		$condition[15][1] = FALSE;
		$condition[17][0] = TRUE;
		$condition[17][1] = FALSE;
		$condition[20][0] = TRUE;
		$condition[20][1] = FALSE;
		//$condition[21][0] = TRUE;
		//$condition[21][1] = FALSE;
		$condition[22][0] = TRUE;
		$condition[22][1] = FALSE;
		$condition[27][0] = TRUE;
		$condition[27][1] = FALSE;
		$condition[28][0] = TRUE;
		$condition[28][1] = FALSE;
		$condition[30][0] = TRUE;
		$condition[30][1] = FALSE;

		$template_id = 7974;

		$cond_id = 26117;

		foreach($condition as $key => $value){
			foreach ($condition[$key] as $keycon => $val) {
				
				$domain = $this->set_domain($key,$val);
			
				if($key == 4){
					$set_template = $this->set_template_subtotal($template_id,$cond_id,$key,$val);
				}elseif($key == 5){
					$set_template = $this->set_template_total_qty($template_id,$cond_id,$key,$val);
				}elseif($key == 13){
					$set_template = $this->set_template_product($template_id,$cond_id,$key,$val);
				}elseif($key == 14){
					$set_template = $this->set_template_brand($template_id,$cond_id,$key,$val);
				}elseif($key == 15){
					$set_template = $this->set_template_category($template_id,$cond_id,$key,$val);
				}elseif($key == 17){
					$set_template = $this->set_template_day($template_id,$cond_id,$key,$val);
				}elseif($key == 20){
					$set_template = $this->set_template_gender($template_id,$cond_id,$key,$val);
				}elseif($key == 21){
					$set_template = $this->set_template_max_usage($template_id,$cond_id,$key,$val);
				}elseif($key == 22){
					$set_template = $this->set_template_customer($template_id,$cond_id,$key,$val);
				}elseif($key == 27){
					$set_template = $this->set_template_city($template_id,$cond_id,$key,$val);
				}elseif($key == 28){
					$set_template = $this->set_template_device($template_id,$cond_id,$key,$val);
				}elseif($key == 30){
					$set_template = $this->set_template_domain($template_id,$cond_id,$key,$val);
				}
				
				$login = $this->set_customer($key,$domain,$val);

			    $cart  = $this->set_cart($key,$val,TRUE);

			    $day = $this->set_day($key,$val);

			    $city = $this->set_city($key);

			    $response = $this-> call('POST','http://'.$domain.'/checkout/json_apply_freegift_auto');

		    	$result_json = $response->getContent();

				$json_decode = json_decode($result_json,TRUE);		    
	 			
	 			$freegift_id = array();

	 			foreach ($json_decode['freegift_auto'] as $jsonkey => $jsonvalue) {
	 				array_push($freegift_id, $json_decode['freegift_auto'][$jsonkey]['promotions_id']);
	 			}

			    if($key == 21 && $val){var_dump($json_decode);
			    	//Insert Database
	              	$create_order_discount = array();
	             	$create_order_discount['order_item_id']             = 1001;
	              	$create_order_discount['SKU']                       = 'BEHACLGRS0-MT';
	              	$create_order_discount['quantity']                  = $cart[1];
	              	$create_order_discount['purchase_code']             = '000000';
	              	$create_order_discount['discount_id']               = $json_decode['freegift_auto'][0]['promotions_id'];
	              	$create_order_discount['discount_name']             = $json_decode['freegift_auto'][0]['promotions_name'];
	              	$create_order_discount['discount_nfc_or_discount']  = $json_decode['freegift_auto'][0]['promotions_name_for_customer'];
	              	$create_order_discount['discount_value']            = $json_decode['freegift_auto'][0]['promotions_value'];
	              	$create_order_discount['discount_type']             = 1;
	              	$create_order_discount['customer_email']            = 'Unit Testing';
	              	$create_order_discount['customer_id']               = 34251;
	              	$create_order_discount['domain_id']                 = 1;
	              	$create_order_discount['is_laravel']                = 1;
	              
	              	$order_discount = DB::table('order_discount')->insert($create_order_discount);
		        }

		        if(!$val){
		        	$this->assertEmpty($json_decode['freegift_auto']);
		        }else{
		        	$this->assertEquals(TRUE,in_array($template_id, $freegift_id));
		        }
			    
				$this->assertEquals(200, $response->getStatusCode());
				$this->assertResponseOk();

				$delete_order_diskon = $this->delete_order_diskon($key,$val);
			}
		}
	}

	
	function check_freegift_auto_2_comb_true_bbd()
	{	
		$condition = [];
		$condition[4][0] = TRUE;
		$condition[4][1] = FALSE;
		$condition[5][0] = TRUE;
		$condition[5][1] = FALSE;
		$condition[13][0] = TRUE;
		$condition[13][1] = FALSE;
		$condition[14][0] = TRUE;
		$condition[14][1] = FALSE;
		$condition[15][0] = TRUE;
		$condition[15][1] = FALSE;
		$condition[17][0] = TRUE;
		$condition[17][1] = FALSE;
		$condition[20][0] = TRUE;
		$condition[20][1] = FALSE;
		//$condition[21][0] = TRUE;
		//$condition[21][1] = FALSE;
		$condition[22][0] = TRUE;
		$condition[22][1] = FALSE;
		$condition[27][0] = TRUE;
		$condition[27][1] = FALSE;
		$condition[28][0] = TRUE;
		$condition[28][1] = FALSE;
		$condition[30][0] = TRUE;
		$condition[30][1] = FALSE;

		/*$condition = array('0' => 'Subtotal',
						   '1' => 'Subtotal F',
						   '2' => 'Total Item Qty',
						   '3' => 'Total Item Qty F',
						   '4' => 'Product',
						   '5' => 'Product F',
						   '6' => 'Brand',
						   '7' => 'Brand F',
						   '8' => 'Category',
						   '9' => 'Category F',
						   '10'=> 'Day',
						   '11'=> 'Day F',
						   '12' => 'Gender',
						   '13' => 'Gender F',
						   '14' => 'Max Usage',
						   '15' => 'Max Usage F',
						   '16' => 'Customer',
						   '17' => 'Customer F',
						   '18' => 'City',
						   '19' => 'City F',
						   '20' => 'Device',
						   '21' => 'Device F',
						   '22' => 'Domain',
						   '23' => 'Domain F');

		$template = array('02' => 7915,'03' => 7916,'04' => 7917,'05' => 7918,'06' => 7919,'07' => 7920,'08' => 7921,'09' => 7922,
						  '010' => 7923,'011' => 7924,'012' => 7925,'013' => 7941,'014' => 7926,'015' => 7926,'016' => 7927,'017' => 7927,
						  '018' => 7928,'019' => 7928,'020' => 7929,'021' => 7929,'022' => 7930, '023' => 7930, '12' => 7942, '13' => 7943,
						  '14' => 7944,'15' => 7945,'16' => 7946,'17' => 7947,'18' => 7948,'19' => 7949,'110' => 7950, '111' => 7951,
						  '112' => 7952,'113' => 7953,'114' => 7954,'115' => 7954,'116' => 7955,'117' => 7955,'118' => 7956, '119' => 7956,
						  '120' => 7957, '121' => 7957,'122' => 7958,'123' => 7958,'24' => 7931,'25' => 7961,'26' => 7932,'27' => 7962,
						  '28' => 7933,'29' => 7963,'210' => 7934,'211' => 7964,'212' => 7935,'213' => 7965,'214' => 7936, '215' => 7936,
						  '216' => 7937,'217' => 7937,'218' => 7938,'219' => 7938,'220' => 7939,'221' => 7939,'222' => 7940, '223' => 7940,
						  '34' => 7959,'35' => 7960,'36' => 7967,'37' => 7966,'38' => 7968,'39' => 7969,'310' => 7970,'311' => 7971,
						  '312' => 7972,'313' => 7973,);*/

		foreach($condition as $key => $value){
			$keys = array_keys($condition);
			//var_dump($keys);
			//var_dump(current($keys));
			

			while($current = current($keys)){
				$next = next($keys);
var_dump($next);
				if (false !== $next && $next == $current)
			    {
			        
			    }
			}
			foreach ($condition[$key] as $keycon => $val) {
				
				
				/*$domain = $this->set_domain($key,$val);

				$login = $this->set_customer($key,$domain,$val);

				$day = $this->set_day($key,$val);

			    $city = $this->set_city($key);


				for($i=$next_key;$i<count($condition);$i++){
					$domain = $this->set_domain($i);

					$login = $this->set_customer($i,$domain);
	var_dump($condition[$key].' - '.$condition[$i]);
					$cart  = $this->set_cart($key,$condition[$key],TRUE);

					$gender = TRUE;
					if($i == 13){
						$gender = FALSE;
					}

					$cart2  = $this->set_cart($i,$condition[$i],FALSE,$gender);
					//var_dump(Cart::content());
					$update_status = $this->enabled_freegift_auto_status($template[$key.$i]);

					$day = $this->set_day($i);

		    		$city = $this->set_city($i);

		    		$response = $this-> call('POST','http://'.$domain.'/checkout/json_apply_freegift_auto');

			    	$result_json = $response->getContent();

					$json_decode = json_decode($result_json,TRUE);

					$freegift_id = array();

		 			foreach ($json_decode['freegift_auto'] as $jsonkey => $jsonvalue) {
		 				array_push($freegift_id, $json_decode['freegift_auto'][$jsonkey]['promotions_id']);
		 			}		    

		 			if($key == 14 OR $i == 14){
				    	//Insert Database
		              	$create_order_discount = array();
		             	$create_order_discount['order_item_id']             = 1001;
		              	$create_order_discount['SKU']                       = 'BEHACLGRS0-MT';
		              	$create_order_discount['quantity']                  = $cart[1];
		              	$create_order_discount['purchase_code']             = '000000';
		              	$create_order_discount['discount_id']               = $json_decode['freegift_auto'][0]['promotions_id'];
		              	$create_order_discount['discount_name']             = $json_decode['freegift_auto'][0]['promotions_name'];
		              	$create_order_discount['discount_nfc_or_discount']  = $json_decode['freegift_auto'][0]['promotions_name_for_customer'];
		              	$create_order_discount['discount_value']            = $json_decode['freegift_auto'][0]['promotions_value'];
		              	$create_order_discount['discount_type']             = 1;
		              	$create_order_discount['customer_email']            = 'Unit Testing';
		              	$create_order_discount['customer_id']               = 34251;
		              	$create_order_discount['domain_id']                 = 1;
		              	$create_order_discount['is_laravel']                = 1;
		              
		              	$order_discount = DB::table('order_discount')->insert($create_order_discount);
			        }

			        if(substr($condition[$key],-1) == 'F' OR substr($condition[$i],-1) == 'F'){
			        	$this->assertEmpty($json_decode['freegift_auto']);
			        }else{
			        	$this->assertEquals(TRUE,in_array($template[$key.$i], $freegift_id));
			        }

			        $this->assertEquals(200, $response->getStatusCode());
					$this->assertResponseOk();

					$update_disabled = $this->disabled_freegift_auto_status($template[$key.$i]);
					
					if($i == 15 OR ($key == 15 AND $i == 23)){
						$id = 15;
						$delete_order_diskon = $this->delete_order_diskon($id);
					}			
				}*/
			}
		}
	}
}