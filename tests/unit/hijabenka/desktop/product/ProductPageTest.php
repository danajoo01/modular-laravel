<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductController;
use \App\Modules\Product\Controllers;

class ProductPageTestHB extends TestCase
{
	function value_sort_price()
	{
		$sort_price = array('0'=>'sprice=0-500','1'=>'sprice=500-1000','2'=>'sprice=1000-2000','3'=>'sprice=2000-3000',
							'4'=>'sprice=4000-5000','5'=>'sprice=5000-6000');

		return $sort_price;
	}

	function get_front_end_type_women()
	{
		$types = DB::table('front_end_type')
					->select('type_url')
					->where('enabled','=',1)
					->whereIn('gender',[1,3])
					->where('type_owner','!=',1)
					->orderBy('type_id','desc')
					->get();

		return $types;

	}

	function get_front_end_type_men()
	{
		$types = DB::table('front_end_type')
					->select('type_url')
					->where('enabled','=',1)
					->whereIn('gender',[2,3])
					->where('type_owner','!=',1)
					->orderBy('type_id','desc')
					->get();

		return $types;

	}

	function get_data_content($response)
	{
		$size = $response->getOriginalContent()->getData()['size'];
		$color = $response->getOriginalContent()->getData()['color'];
		$category = $response->getOriginalContent()->getData()['category'];
		$catalog = $response->getOriginalContent()->getData()['catalog'];
		$brand = $response->getOriginalContent()->getData()['brand'];

		if(empty($catalog) && empty($size) && empty($color) && empty($category) && empty($brand)){
			$this->see('Produk Yang Ada Cari Tidak Ditemukan.');
		}else{
			$this->assertNotEmpty($size);
			$this->assertNotEmpty($color);
			$this->assertNotEmpty($category);
			$this->assertNotEmpty($catalog); 
			$this->assertNotEmpty($brand);
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
			$this->assertNotEmpty($data['brand']);
		}

		return TRUE;

	}

	/** @test */
	function check_catalog_page_category_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = DB::table('front_end_type')
					->select('type_url')
					->where('enabled','=',1)
					->where('type_owner','!=',1)
					->orderBy('type_id','desc')
					->get();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach ($sorts as $sort) {
			 	$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort);

			 	$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);
			} 
		}
	}

	/** @test */
	function check_catalog_page_category_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();
		
		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
		
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);
			
			foreach ($sorts as $sort) {
			 	$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/women?'.$sort);

			 	$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);
			} 
		}
	}

	/** @test */
	function check_catalog_page_category_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');

			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach ($sorts as $sort) {
			 	$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/men?'.$sort);

			 	$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);
			}  
		}
	}

	/** @test */
	function check_combination_catalog_page_category_color_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['color'] as $color_all){
				$url_color = $color_all->color_name;
				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach ($sorts as $sort) {
				 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/women?'.$sort);

				 	$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_color_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['color'] as $color_all){
				$url_color = $color_all->color_name;
				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach ($sorts as $sort) {
				 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/men?'.$sort);

				 	$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_prize_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			foreach($sort_price as $val){
				$prize = $val;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/women?'.$prize);

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach ($sorts as $sort) {
				 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/women?'.$prize.'&'.$sort);

				 	$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_prize_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			foreach($sort_price as $val){
				$prize = $val;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/men?'.$prize);

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach ($sorts as $sort) {
				 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/men?'.$prize.'&'.$sort);

				 	$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/women');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/women?'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}  
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/men');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/men?'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}  
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;
				
				if($url_brand != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women?'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}  
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;
				
				if($url_brand != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men?'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					} 
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_color_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/women');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach($get_data2['color'] as $color){
						$url_color = $color->color_name;
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/color/'.$url_color.'/women');

						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/color/'.$url_color.'/women?'.$sort);

						 	$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);
						} 
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_color_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/men');

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach($get_data2['color'] as $color){
						$url_color = $color->color_name;
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/color/'.$url_color.'/men');
		
						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/color/'.$url_color.'/men?'.$sort);

						 	$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);
						} 
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_prize_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){

					foreach($sort_price as $val){
						$prize = $val;
						
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/women?'.$prize);

						$get_data2 = $this->get_original_data($response2);				
					
						$this->assert_data($get_data2);

						foreach ($sorts as $sort) {
						 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/women?'.$prize.'&'.$sort);

						 	$get_data3 = $this->get_original_data($response3);				
					
							$this->assert_data($get_data3);
						}
					}
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_size_prize_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){

					foreach($sort_price as $val){
						$prize = $val;
						
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/men?'.$prize);

						$get_data2 = $this->get_original_data($response2);				
					
						$this->assert_data($get_data2);

						foreach ($sorts as $sort) {
						 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/size/'.$url_size.'/men?'.$prize.'&'.$sort);

						 	$get_data3 = $this->get_original_data($response3);				
					
							$this->assert_data($get_data3);
						}
					}
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_color_prize_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['color'] as $color){
				$url_color = $color->color_name;
				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}
				
				foreach($sort_price as $val){
					$prize = $val;
						
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/women?'.$prize);

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/women?'.$prize.'&'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}
				} 
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_color_prize_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['color'] as $color){
				$url_color = $color->color_name;
				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				foreach($sort_price as $val){
					$prize = $val;
					
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/men?'.$prize);

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/color/'.$url_color.'/men?'.$prize.'&'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_prize_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;
				
				foreach($sort_price as $val){
					$prize = $val;
						
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women?'.$prize);

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women?'.$prize.'&'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}
				} 
			}
		}
	}

	
	function check_combination_catalog_page_category_brand_prize_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;

				foreach($sort_price as $val){
					$prize = $val;
					
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men?'.$prize);

					$get_data2 = $this->get_original_data($response2);				
					
					$this->assert_data($get_data2);

					foreach ($sorts as $sort) {
					 	$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men?'.$prize.'&'.$sort);

					 	$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);
					}
				}  
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_size_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/women');

						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/women?'.$sort);

						 	$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);
						} 
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_size_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand_all){
				$url_brand = $brand_all->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2= $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/men');

						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/men?'.$sort);

						 	$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);
						} 
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_color_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $color){
					$url_color = $color->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/women');

					$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);

					foreach ($sorts as $sort) {
					 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/women?'.$sort);

					 	$get_data4 = $this->get_original_data($response4);				
					
						$this->assert_data($get_data4);
					} 
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_color_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $color){
					$url_color = $color->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/men');

					$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);

					foreach ($sorts as $sort) {
					 	$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/men?'.$sort);

					 	$get_data4 = $this->get_original_data($response4);				
					
						$this->assert_data($get_data4);
					} 
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_size_color_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $colors_all){
					$url_color = $colors_all->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/women');
					
					$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);

					foreach($get_data3['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						} 

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/size/'.$url_size.'/women');
	
							$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);

							foreach ($sorts as $sort) {
							 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/size/'.$url_size.'/women?'.$sort);

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
	function check_combination_catalog_page_category_brand_size_color_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $colors_all){
					$url_color = $colors_all->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/men');
					
					$get_data3 = $this->get_original_data($response3);				
					
					$this->assert_data($get_data3);

					foreach($get_data3['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						} 

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/size/'.$url_size.'/men');
	
							$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);

							foreach ($sorts as $sort) {
							 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/size/'.$url_size.'/men?'.$sort);

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
	function check_combination_catalog_page_category_brand_size_price_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){

						foreach ($sort_price as $val) {
							
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/women?'.$val);
	
							$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);

							foreach ($sorts as $sort) {
							 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/women?'.$val.'&'.$sort);

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
	function check_combination_catalog_page_category_brand_size_price_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){

						foreach ($sort_price as $val) {
							
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/men?'.$val);
	
							$get_data4 = $this->get_original_data($response4);				
					
							$this->assert_data($get_data4);

							foreach ($sorts as $sort) {
							 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/men?'.$val.'&'.$sort);

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
	function check_combination_catalog_page_category_brand_color_price_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $colors_all){
					$url_color = $colors_all->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $val){
					
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/women?'.$val);
					
						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/women?'.$val.'&'.$sort);

						 	$get_data5 = $this->get_original_data($response5);				
					
							$this->assert_data($get_data5);
						}
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_color_price_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['color'] as $colors_all){
					$url_color = $colors_all->color_name;
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $val){
					
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/men?'.$val);
					
						$get_data3 = $this->get_original_data($response3);				
					
						$this->assert_data($get_data3);

						foreach ($sorts as $sort) {
						 	$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/color/'.$url_color.'/men?'.$val.'&'.$sort);

						 	$get_data5 = $this->get_original_data($response5);				
					
							$this->assert_data($get_data5);
						}
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_size_color_price_women_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_women();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/women');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/women');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/women?');

						$get_data4 = $this->get_original_data($response4);				
					
						$this->assert_data($get_data4);

						foreach($get_data4['color'] as $colors_all){
							$url_color = $colors_all->color_name;
							if($url_color == "Multi Color"){
								$url_color = "Multi%20Color";
							}

							foreach ($sort_price as $val) {
								
								$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/color/'.$url_color.'/women?'.$val);
		
								$get_data5 = $this->get_original_data($response5);				
					
								$this->assert_data($get_data5);

								foreach ($sorts as $sort) {
								 	$response6 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/color/'.$url_color.'/women?'.$val.'&'.$sort);

								 	$get_data6 = $this->get_original_data($response6);				
					
									$this->assert_data($get_data6);
								} 
							}
						}
					}
				}
			}
		}
	}

	/** @test */
	function check_combination_catalog_page_category_brand_size_color_price_men_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$sorts = array('0'=>'price=asc','1'=>'price=desc','2'=>'pn=desc','3'=>'discount=desc');

		$sort_price = $this->value_sort_price();

		$types = $this->get_front_end_type_men();

		foreach ($types as $value) {
			$url = $value->type_url;
			$response = $this->call('GET','http://'.$domain.'/'.$url.'/men');
			
			$get_data = $this->get_original_data($response);				
					
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $brand){
				$url_brand = $brand->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/men');

				$get_data2 = $this->get_original_data($response2);				
					
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ''){
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/men?');

						$get_data4 = $this->get_original_data($response4);				
					
						$this->assert_data($get_data4);

						foreach($get_data4['color'] as $colors_all){
							$url_color = $colors_all->color_name;
							if($url_color == "Multi Color"){
								$url_color = "Multi%20Color";
							}

							foreach ($sort_price as $val) {
								
								$response5 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/color/'.$url_color.'/men?'.$val);
		
								$get_data5 = $this->get_original_data($response5);				
					
								$this->assert_data($get_data5);

								foreach ($sorts as $sort) {
								 	$response6 = $this->call('GET','http://'.$domain.'/'.$url.'/brand/'.$url_brand.'/size/'.$url_size.'/color/'.$url_color.'/men?'.$val.'&'.$sort);

								 	$get_data6 = $this->get_original_data($response6);				
					
									$this->assert_data($get_data6);
								} 
							}
						}
					}
				}
			}
		}
	}
}
