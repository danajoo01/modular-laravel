<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductController;
use \App\Modules\Product\Controllers;

class BrandPageHBMTest extends TestCase
{
	function value_sort_price()
	{
		$sort_price = array('0'=>'sprice=0-500','1'=>'sprice=500-1000','2'=>'sprice=1000-2000','3'=>'sprice=2000-3000',
							'4'=>'sprice=4000-5000','5'=>'sprice=5000-6000');

		return $sort_price;
	}

	
	function brand_page_show_ok()
	{
		$params = ['brand_name'=>'adidas', 'limit' => 1];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@brand',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	function get_original_data($response)
	{

		$data['catalog'] = $response->getOriginalContent()->getData()['catalog'];
		$data['size'] = $response->getOriginalContent()->getData()['size'];
		$data['color'] = $response->getOriginalContent()->getData()['color'];
		$data['category'] = $response->getOriginalContent()->getData()['category'];

		return $data;
	}

	function assert_data($data)
	{
		if(empty($data['catalog'])){
			$this->see('Produk Yang Ada Cari Tidak Ditemukan.');
		}else{
			$this->assertNotEmpty($data['size']);
			$this->assertNotEmpty($data['color']);
			$this->assertNotEmpty($data['category']);
		}

		return TRUE;

	}

	function get_brand()
	{
		$brands = DB::table('brand')
					->select('brand_id','brand_url','brand_type_menu')
					->where('enabled','=',1)
					->orderBy('brand_id','desc')
					->get();

		return $brands;
	}

	function get_products($brand_id)
	{
		$products = DB::table('products')
							->select('products.product_id', 'products.product_status', 'product_variant.status', 'inventory.quantity', 'inventory.inventory_status')
							->join('product_variant', 'product_variant.product_id', '=', 'products.product_id')
            				->join('inventory', 'inventory.SKU', '=', 'product_variant.SKU')
							->where('products.product_status','=',1)
							->where('products.product_brand','=',$brand_id)
							->where('products.own_hb','=',1)
							->where('product_variant.status', '=', 1)
							->get();
		return $products;
	}

	/** @test */
	function check_all_array_not_null_from_brand_hbm_page()
	{	
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;

				foreach ($sorts as $sort) {
					$response = $this->call('GET','http://'.$domain.'/brand/'.$name.'?'.$sort);
				
					$get_data = $this->get_original_data($response);				
						
					$this->assert_data($get_data);
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sorts as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);

						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_color_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $colors_all) {
					$url_color = $colors_all->color_name;
						
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sorts as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);

						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_price_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;

				foreach ($sort_price as $price) {

					foreach ($sorts as $sort) {
						$response = $this->call('GET','http://'.$domain.'/brand/'.$name.'?'.$price.'&'.$sort);
				
						$get_data = $this->get_original_data($response);				
					
						$this->assert_data($get_data);
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['size'] as $size_all) {
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}
					
					if($url_size != ''){
						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?size='.$url_size.'&'.$sort);

							$get_data2 = $this->get_original_data($response2);

							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_color_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $colors_all) {
						$url_color = $colors_all->color_name;
							
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sorts as $sort) {
							$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);

							$this->assert_data($get_data3);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_price_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sort_price as $price) {

						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&'.$price.'&'.$sort);

							$get_data2 = $this->get_original_data($response2);

							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['size'] as $size_all) {
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}
						
						if($url_size != ''){	
							foreach ($sorts as $sort) {
								$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&size='.$url_size.'&'.$sort);

								$get_data3 = $this->get_original_data($response3);

								$this->assert_data($get_data3);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_color_price_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $colors_all) {
					$url_color = $colors_all->color_name;
							
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {

						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color.'&'.$price.'&'.$sort);

							$get_data2 = $this->get_original_data($response2);

							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_color_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $colors_all) {
					$url_color = $colors_all->color_name;
							
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['size'] as $size_all) {
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}
						
						if($url_size != ''){	
							foreach ($sorts as $sort) {
								$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color.'&size='.$url_size.'&'.$sort);

								$get_data3 = $this->get_original_data($response3);

								$this->assert_data($get_data3);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_price_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;

				foreach ($sort_price as $price) {

					$response = $this->call('GET','http://'.$domain.'/brand/'.$name.'?'.$price);
			
					$get_data = $this->get_original_data($response);

					$this->assert_data($get_data);

					foreach ($get_data['size'] as $size_all) {
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}
						
						if($url_size != ''){	
							foreach ($sorts as $sort) {
								$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?'.$price.'&size='.$url_size.'&'.$sort);

								$get_data3 = $this->get_original_data($response3);

								$this->assert_data($get_data3);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_color_price_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $colors_all) {
						$url_color = $colors_all->color_name;
							
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {

							foreach ($sorts as $sort) {
								$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&'.$sort);

								$get_data3 = $this->get_original_data($response3);

								$this->assert_data($get_data3);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_color_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $colors_all) {
						$url_color = $colors_all->color_name;
							
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);

						$this->assert_data($get_data3);

						foreach ($get_data3['size'] as $size_all) {
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}
							
							if($url_size != ''){	
								foreach ($sorts as $sort) {
									$response4= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color.'&size='.$url_size.'&'.$sort);

									$get_data4 = $this->get_original_data($response4);

									$this->assert_data($get_data4);
								}
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_color_price_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $colors_all) {
					$url_color = $colors_all->color_name;
						
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {
						$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color.'&'.$price);

						$get_data3 = $this->get_original_data($response3);

						$this->assert_data($get_data3);

						foreach ($get_data3['size'] as $size_all) {
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}
							
							if($url_size != ''){	
								foreach ($sorts as $sort) {
									$response4= $this->call('GET','http://'.$domain.'/brand/'.$name.'?color='.$url_color.'&'.$price.'&size='.$url_size.'&'.$sort);

									$get_data4 = $this->get_original_data($response4);

									$this->assert_data($get_data4);
								}
							}
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_brand_page_filter_category_color_price_size_hbm()
	{
		$domain = env('HIJABENKA_MOBILE', 'm-herman.hijabenka.biz');
		
		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$brands = $this->get_brand();

		foreach ($brands as $value) {
			
			$products = $this->get_products($value->brand_id);

			if(count($products) > 0){
				$name = $value->brand_url;
				$response = $this->call('GET','http://'.$domain.'/brand/'.$name);
			
				$get_data = $this->get_original_data($response);				
				
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);

					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $colors_all) {
						$url_color = $colors_all->color_name;
							
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {
							$response3= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color.'&'.$price);

							$get_data3 = $this->get_original_data($response3);

							$this->assert_data($get_data3);

							foreach ($get_data3['size'] as $size_all) {
								if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
									$url_size = $size_all->product_size_url;
								}else{
									$url_size = str_replace(' ','_',strtolower($size_all->product_size));
								}
								
								if($url_size != ''){	
									foreach ($sorts as $sort) {
										$response4= $this->call('GET','http://'.$domain.'/brand/'.$name.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&size='.$url_size.'&'.$sort);

										$get_data4 = $this->get_original_data($response4);

										$this->assert_data($get_data4);
									}
								}
							}
						}
					}
				}
			} 
		}
	}
}