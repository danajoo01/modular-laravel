<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductController;
use \App\Modules\Product\Controllers;

class TagPageHBTest extends TestCase
{
	function value_sort_price()
	{
		$sort_price = array('0'=>'sprice=0-500','1'=>'sprice=500-1000','2'=>'sprice=1000-2000','3'=>'sprice=2000-3000',
							'4'=>'sprice=4000-5000','5'=>'sprice=5000-6000');

		return $sort_price;
	}

	function get_data_content($response)
	{
		$data['size'] = $response->getOriginalContent()->getData()['size'];
		$data['color'] = $response->getOriginalContent()->getData()['color'];
		$data['category'] = $response->getOriginalContent()->getData()['category'];
		$data['catalog'] = $response->getOriginalContent()->getData()['catalog'];
		$data['brand'] = $response->getOriginalContent()->getData()['brand'];

		if(empty($data['catalog'])){
			$this->see('Produk Yang Ada Cari Tidak Ditemukan.');
		}else{
			$this->assertNotEmpty($data['size']);
			$this->assertNotEmpty($data['color']);
			$this->assertNotEmpty($data['category']);
			$this->assertNotEmpty($data['catalog']); 
			$this->assertNotEmpty($data['brand']);
		}

		return TRUE;
	}

	function get_original_data($response)
	{
		$data['catalog'] = $response->getOriginalContent()->getData()['catalog'];
		$data['size'] = $response->getOriginalContent()->getData()['size'];
		$data['color'] = $response->getOriginalContent()->getData()['color'];
		$data['category'] = $response->getOriginalContent()->getData()['category'];
		$data['brand'] = $response->getOriginalContent()->getData()['brand'];

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
			$this->assertNotEmpty($data['catalog']);
			$this->assertNotEmpty($data['brand']);
		}

		return TRUE;

	}

	function get_tags()
	{
		$tags = DB::table('tags')
					->select('tag_id','tag_url')
					->where('tag_status','=',1)
					->where('domain_id','=',2)
					->get();

		return $tags;
	}

	function get_products($tag_id)
	{
		$products = DB::table('products')
							->select('products.product_id', 'products.product_status', 'product_variant.status', 'inventory.quantity', 'inventory.inventory_status')
							->join('product_variant', 'product_variant.product_id', '=', 'products.product_id')
            				->join('inventory', 'inventory.SKU', '=', 'product_variant.SKU')
							->where('products.product_status','=',1)
							->where('products.product_tag_hb','LIKE', '%,'.$tag_id.',%')
							->where('products.own_hb','=',1)
							->where('product_variant.status', '=', 1)
							->get();
		return $products;
	}

	
	function tag_page_show_ok()
	{
		$params = ['tag_name'=>'gelang'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@tag',$params);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	 
	function tag_page_show_failed_empty_name()
	{
		$params = ['tag_name'=>''];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@tag',$params);
		$this->assertEquals(404, $response->getStatusCode());
	}

	
	function tag_page_show_failed_wrong_name()
	{
		$params = ['tag_name'=>'testaja'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@tag',$params);
		$this->assertEquals(404, $response->getStatusCode());
	}
	
	/** @test */
	function check_all_array_not_null_from_tag_page_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$name = $value->tag_url;
				$response = $this->call('GET','http://'.$domain.'/tag/'.$name);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);
			}
		}
	}

	/** @test */
	function check_tag_page_param_category_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach($get_data['category'] as $c){
					$cats = $c->type_url;

					foreach ($sorts as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats.'&'.$sort);
					
						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_tag_page_param_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach($get_data['color'] as $c){
					$url_color = $c->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sorts as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$sort);
						
						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	 /** @test */
	function check_tag_page_param_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				foreach($sort_price as $price){
					foreach ($sorts as $sort) {
						$response = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price.'&'.$sort);
					
						$get_data = $this->get_original_data($response);				
							
						$this->assert_data($get_data);
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach($get_data['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?size='.$url_size.'&'.$sort);
						
							$get_data2 = $this->get_original_data($response2);				
							
							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_tag_page_param_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach($get_data['brand'] as $b){
					$url_brand = $b->brand_url;
					
					foreach ($sorts as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?brand='.$url_brand.'&'.$sort);
					
						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					} 
				}
			}
		}
	}

	 /** @test */
	function check_tag_page_param_category_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach($get_data['category'] as $g){
					$cats = $g->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats);
				
					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $val) {
						$url_color = $val->color_name;

						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sorts as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);
						}
					} 
				}
			}
		}
	}

	/** @test */
	function check_tag_page_param_category_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
			
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);
				
				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sort_price as $price) {
						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&'.$price.'&'.$sort);
					
							$get_data2 = $this->get_original_data($response2);				
						
							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_category_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach($get_data['category'] as $g){
					$cats = $g->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats);

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
								$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats.'&size='.$url_size.'&'.$sort);

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
	function check_tag_page_param_category_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach($get_data['category'] as $g){
					$cats = $g->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats);
				
					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['brand'] as $val) {
						$url_brand = $val->brand_url;

						foreach ($sorts as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cats.'&brand='.$url_brand.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
							
							$this->assert_data($get_data3);
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
			
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);
				
				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {
						foreach ($sorts as $sort) {
							$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price.'&'.$sort);
					
							$get_data2 = $this->get_original_data($response2);				
							
							$this->assert_data($get_data2);
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
			
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);
				
				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							foreach ($sorts as $sort) {
								$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&size='.$url_size.'&'.$sort);
									
								$get_data4 = $this->get_original_data($response4);				
							
								$this->assert_data($get_data4);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */  
	function check_tag_page_param_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
			
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);

				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);
				
				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach($get_data2['brand'] as $b){
						$url_brand = $b->brand_url;
						
						foreach ($sorts as $sort) {
							$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&brand='.$url_brand.'&'.$sort);

							$get_data5 = $this->get_original_data($response5);				
							
							$this->assert_data($get_data5);
						}
					}
				} 
			}
		}
	}

	/** @test */  
	function check_tag_page_param_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				foreach ($sort_price as $price) {
					$response = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price);

					$get_data = $this->get_original_data($response);				
							
					$this->assert_data($get_data);

					foreach($get_data['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							foreach ($sorts as $sort) {
								$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price.'&size='.$url_size.'&'.$sort);
									
								$get_data4 = $this->get_original_data($response4);				
							
								$this->assert_data($get_data4);
							}
						}
					}
				} 
			}
		}
	}

	/** @test */  
	function check_tag_page_param_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				foreach ($sort_price as $price) {
					$response = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price);

					$get_data = $this->get_original_data($response);				
							
					$this->assert_data($get_data);

					foreach($get_data['brand'] as $brand_all){
						$url_brand = $brand_all->brand_url;

						foreach ($sorts as $sort) {	
							$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price.'&brand='.$url_brand.'&'.$sort);
									
							$get_data4 = $this->get_original_data($response4);				
							
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */  
	function check_tag_page_param_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['size'] as $size_all) {
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?size='.$url_size);

						$get_data2 = $this->get_original_data($response2);				
								
						$this->assert_data($get_data2);

						foreach($get_data2['brand'] as $b){
							$url_brand = $b->brand_url;
							
							foreach ($sorts as $sort) {
								$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?size='.$url_size.'&brand='.$url_brand.'&'.$sort);

								$get_data5 = $this->get_original_data($response5);				
								
								$this->assert_data($get_data5);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_category_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {
							foreach ($sorts as $sort) {	
								$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&'.$sort);

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
	function check_tag_page_param_category_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);				
							
						$this->assert_data($get_data3);

						foreach($get_data3['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								foreach ($sorts as $sort) {	
									$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&size='.$url_size.'&'.$sort);
									
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
	function check_tag_page_param_category_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);				
							
						$this->assert_data($get_data3);

						foreach ($get_data3['brand'] as $brand_all) {
							$url_brand = $brand_all->brand_url;

							foreach ($sorts as $sort) {	
								$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&brand='.$url_brand.'&'.$sort);
								$get_data4 = $this->get_original_data($response4);				
							
								$this->assert_data($get_data4);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_category_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sort_price as $price) {
						$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&'.$price);

						$get_data3 = $this->get_original_data($response3);				
							
						$this->assert_data($get_data3);

						foreach($get_data3['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								foreach ($sorts as $sort) {	
									$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&'.$price.'&size='.$url_size.'&'.$sort);
								
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
	function check_tag_page_param_category_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sort_price as $price) {
						$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&'.$price);

						$get_data3 = $this->get_original_data($response3);				
							
						$this->assert_data($get_data3);

						foreach($get_data3['brand'] as $b){
							$url_brand = $b->brand_url;
							
							foreach ($sorts as $sort) {
								$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&'.$price.'&brand='.$url_brand.'&'.$sort);

								$get_data5 = $this->get_original_data($response5);				
							
								$this->assert_data($get_data5);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_category_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&size='.$url_size);
										
							$get_data4 = $this->get_original_data($response4);				
							
							$this->assert_data($get_data4);

							foreach($get_data4['brand'] as $b){
								$url_brand = $b->brand_url;
								
								foreach ($sorts as $sort) {
									$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&size='.$url_size.'&brand='.$url_brand.'&'.$sort);

									$get_data5 = $this->get_original_data($response5);				
							
									$this->assert_data($get_data);
								}
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price);

						$get_data2 = $this->get_original_data($response2);				
							
						$this->assert_data($get_data2);

						foreach($get_data2['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								foreach ($sorts as $sort) {
									$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price.'&size='.$url_size.'&'.$sort);
								
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
	function check_tag_page_param_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price);

						$get_data2 = $this->get_original_data($response2);				
							
						$this->assert_data($get_data2);

						foreach($get_data2['brand'] as $brand_all){
							$url_brand = $brand_all->brand_url;

							foreach ($sorts as $sort) {
								$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price.'&brand='.$url_brand.'&'.$sort);
								
								$get_data4 = $this->get_original_data($response4);				
							
								$this->assert_data($get_data4);
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				foreach ($sort_price as $price) {
				
					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){

							$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price.'&size='.$url_size);

							$get_data4 = $this->get_original_data($response4);				
							
							$this->assert_data($get_data4);

							foreach ($get_data4['brand'] as $b) {
								$url_brand = $b->brand_url;

								foreach ($sorts as $sort){
									$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?'.$price.'&size='.$url_size.'&brand='.$url_brand.'&'.$sort);	
									$get_data5 = $this->get_original_data($response5);				
							
									$this->assert_data($get_data5);
								}
							}
						}
					}
				} 
			}
		}
	}

	/** @test */ 
	function check_tag_page_param_category_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {
							$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price);

							$get_data3 = $this->get_original_data($response3);				
							
							$this->assert_data($get_data3);

							foreach($get_data3['size'] as $size_all){
								if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
									$url_size = $size_all->product_size_url;
								}else{
									$url_size = str_replace(' ','_',strtolower($size_all->product_size));
								}

								if($url_size != ''){
									foreach ($sorts as $sort) {
										$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&size='.$url_size.'&'.$sort);
									
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

	/** @test */ 
	function check_tag_page_param_category_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {
							$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price);

							$get_data3 = $this->get_original_data($response3);				
							
							$this->assert_data($get_data3);

							foreach($get_data3['brand'] as $b){
								$url_brand = $b->brand_url;

								foreach ($sorts as $sort) {
									$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&brand='.$url_brand.'&'.$sort);

									$get_data5 = $this->get_original_data($response5);				
							
									$this->assert_data($get_data5);
								}
							}
						}
					}
				}
			} 
		}
	}

	/** @test */ 
	function check_tag_page_param_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $price) {
						$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price);

						$get_data2 = $this->get_original_data($response2);				
							
						$this->assert_data($get_data2);

						foreach($get_data2['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price.'&size='.$url_size);
									
								$get_data3 = $this->get_original_data($response3);				
							
								$this->assert_data($get_data3);

								foreach($get_data3['brand'] as $brand_all){
									$url_brand = $brand_all->brand_url;

									foreach ($sorts as $sort) {
										$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?color='.$url_color.'&'.$price.'&size='.$url_size.'&brand='.$url_brand.'&'.$sort);
										
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

	/** @test */ 
	function check_tag_page_param_category_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$tags = $this->get_tags();

		foreach ($tags as $value) {
			$products = $this->get_products($value->tag_id);

			if(count($products) > 0){
				$url = $value->tag_url;
				
				$response = $this->call('GET','http://'.$domain.'/tag/'.$url);
				
				$get_data = $this->get_original_data($response);				
							
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $price) {
							$response3 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price);

							$get_data3 = $this->get_original_data($response3);				
							
							$this->assert_data($get_data3);

							foreach($get_data3['size'] as $size_all){
								if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
									$url_size = $size_all->product_size_url;
								}else{
									$url_size = str_replace(' ','_',strtolower($size_all->product_size));
								}

								if($url_size != ''){
									$response4 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&size='.$url_size);
									
									$get_data4 = $this->get_original_data($response4);				
							
									$this->assert_data($get_data4);

									foreach($get_data4['brand'] as $b){
										$url_brand = $b->brand_url;
										
										foreach ($sorts as $sort) {
											$response5 = $this->call('GET','http://'.$domain.'/tag/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$price.'&size='.$url_size.'&brand='.$url_brand.'&'.$sort);

											$get_data5 = $this->get_original_data($response5);				
							
											$this->assert_data($get_data5);
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
}
