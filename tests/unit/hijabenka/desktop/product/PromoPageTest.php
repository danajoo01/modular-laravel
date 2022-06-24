<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductController;
use \App\Modules\Product\Controllers;

class PromoPageTestHB extends TestCase
{
	function value_sort_price()
	{
		$sort_price = array('0'=>'sprice=0-500','1'=>'sprice=500-1000','2'=>'sprice=1000-2000','3'=>'sprice=2000-3000',
							'4'=>'sprice=4000-5000','5'=>'sprice=5000-6000');

		return $sort_price;
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

	
	function promo_show_page_ok()
	{
		$params = ['id'=>'2244', 'special_page_name' => 'koleksi-atasan-terbaru-wanita'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@promo',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see("Koleksi Atasan Terbaru Wanita");
	}

	
	function promo_page_failed_special_page_name_empty()
	{
		$params = ['id'=>'2244', 'special_page_name' => ''];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@promo',$params);
		$this->assertResponseStatus(404);
	}

	
	function promo_page_failed_id_empty()
	{
		$params = ['id'=>'', 'special_page_name' => 'koleksi-atasan-terbaru-wanita'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@promo',$params);
		$this->assertResponseStatus(404);
	}

	
	function promo_page_failed_id_and_special_page_name_empty()
	{
		$params = ['id'=>'', 'special_page_name' => ''];

		$response = $this->action('GET','\App\Modules\Product\Controllers\ProductController@promo',$params);
		$this->assertResponseStatus(404);
	}

	function get_promo_page()
	{
		$promos = DB::table('special_page')
					->select('special_page_url')
					->where('enabled','=',1)
					->where('domain_id','=',2)
					->where('special_page_url','!=','')
					->orderBy('special_page_id','desc')
					->take(100)
					->skip(100)
					->get();

		return $promos;
	}

	/** @test */
	function check_all_array_not_null_from_promo_page_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
		
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
		}
	}

	/** @test */
	function check_promo_page_param_gender_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['category'] as $c){
				$cats = $c->type_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cats);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['color'] as $c){
				$url_color = $c->color_name;
				
				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);
			} 
		}
	}

	/** @test */
	function check_promo_page_param_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($sort_price as $s){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?'.$s);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);
			} 
		}
	}

	/** @test */
	function check_promo_page_param_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['size'] as $size_all){
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				} 
				
				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?size='.$url_size);
				
					$get_data2 = $this->get_original_data($response2);				
							
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['brand'] as $b){
				$url_brand = $b->brand_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?brand='.$url_brand);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				
				foreach ($sort_price as $val) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$val);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['size'] as $size_all) {
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					} 

					if($url_size != ""){
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&size='.$url_size);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['brand'] as $val) {
					$url_brand = $val->brand_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&brand='.$url_brand);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['category'] as $g){
				$cat = $g->type_url;
				
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
		
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
			
			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&'.$sort);
				
					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['category'] as $g){
				$cat = $g->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['size'] as $size_all) {
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&size='.$url_size);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach($get_data['category'] as $g){
				$cat = $g->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);
			
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['brand'] as $val) {
					$url_brand = $val->brand_url;

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&brand='.$url_brand);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
		
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
			
			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort);
				
					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
		
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
			
			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&size='.$url_size);
								
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
		
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
			
			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach($get_data2['brand'] as $b){
					$url_brand = $b->brand_url;
									
					$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&brand='.$url_brand);

					$get_data5 = $this->get_original_data($response5);				
						
					$this->assert_data($get_data5);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach ($sort_price as $sort) {
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort);

				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach($get_data['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort.'&size='.$url_size);
								
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach ($sort_price as $sort) {
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort);

				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach($get_data['brand'] as $brand_all){
					$url_brand = $brand_all->brand_url;

					$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort.'&brand='.$url_brand);
								
					$get_data4 = $this->get_original_data($response4);				
						
					$this->assert_data($get_data4);
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
		
			$response = $this->call('GET','http://'.$domain.'/'.$url);

			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);
			
			foreach ($get_data['size'] as $size_all) {
				if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
					$url_size = $size_all->product_size_url;
				}else{
					$url_size = str_replace(' ','_',strtolower($size_all->product_size));
				}

				if($url_size != ''){
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?size='.$url_size);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['brand'] as $b){
						$url_brand = $b->brand_url;
										
						$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?size='.$url_size.'&brand='.$url_brand);

						$get_data5 = $this->get_original_data($response5);				
						
						$this->assert_data($get_data5);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_color_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					foreach ($sort_price as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['size'] as $size_all) {
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&size='.$url_size);

							$get_data3 = $this->get_original_data($response3);				
							
							$this->assert_data($get_data3);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['brand'] as $brand_all) {
						$url_brand = $brand_all->brand_url;
						
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&brand='.$url_brand);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color);


					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){

							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&size='.$url_size);
								
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['brand'] as $brand_all){
						$url_brand = $brand_all->brand_url;

						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&brand='.$url_brand);
								
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort.'&size='.$url_size);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				
				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['brand'] as $brand_all){
						$url_brand = $brand_all->brand_url;

						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort.'&brand='.$url_brand);
							
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach($get_data['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&size='.$url_size);
							
						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);

						foreach($get_data2['brand'] as $brand_all){
							$url_brand = $brand_all->brand_url;

							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&size='.$url_size.'&brand='.$url_brand);
								
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);

					foreach ($get_data3['size'] as $size_all) {
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&size='.$url_size);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);

					foreach ($get_data3['brand'] as $brand_all) {
						$url_brand = $brand_all->brand_url;
					
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&brand='.$url_brand);
							
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				foreach ($sort_price as $sort) {
					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&'.$sort);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);

					foreach($get_data3['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&'.$sort.'&size='.$url_size);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				foreach ($sort_price as $sort) {
					$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&'.$sort);

					$get_data3 = $this->get_original_data($response3);				
						
					$this->assert_data($get_data3);

					foreach($get_data['brand'] as $b){
						$url_brand = $b->brand_url;
						
						$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&'.$sort.'&brand='.$url_brand);

						$get_data5 = $this->get_original_data($response5);				
						
						$this->assert_data($get_data5);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_category_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&size='.$url_size);
									
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);

						foreach($get_data4['brand'] as $b){
							$url_brand = $b->brand_url;
							
							$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&size='.$url_size.'&brand='.$url_brand);

							$get_data5 = $this->get_original_data($response5);				
						
							$this->assert_data($get_data5);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort.'&size='.$url_size);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['brand'] as $brand_all){
						$url_brand = $brand_all->brand_url;

						$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort.'&brand='.$url_brand);
							
						$get_data4 = $this->get_original_data($response4);				
						
						$this->assert_data($get_data4);
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach ($sort_price as $sort) {
				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort);
				
				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach($get_data2['size'] as $size_all){
					if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
						$url_size = $size_all->product_size_url;
					}else{
						$url_size = str_replace(' ','_',strtolower($size_all->product_size));
					}

					if($url_size != ''){
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort.'&size='.$url_size);
						
						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['brand'] as $brand_all){
							$url_brand = $brand_all->brand_url;

							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?'.$sort.'&size='.$url_size.'&brand='.$url_brand);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_color_price_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_category_color_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&size='.$url_size);
								
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
	function check_promo_page_param_gender_category_color_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['brand'] as $b){
							$url_brand = $b->brand_url;
							
							$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&brand='.$url_brand);

							$get_data5 = $this->get_original_data($response5);				
						
							$this->assert_data($get_data5);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);

						foreach($get_data2['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
								
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
	function check_promo_page_param_gender_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);

						foreach($get_data2['brand'] as $brand_all){
							$url_brand = $brand_all->brand_url;

							$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort.'&brand='.$url_brand);
							
							$get_data4 = $this->get_original_data($response4);				
						
							$this->assert_data($get_data4);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_gender_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				
				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort.'&size='.$url_size);
							
							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);

							foreach($get_data3['brand'] as $b){
								$url_brand = $b->brand_url;
								
								$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&'.$sort.'&size='.$url_size.'&brand='.$url_brand);

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
	function check_promo_page_param_category_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
								
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
	function check_promo_page_param_category_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['brand'] as $b){
							$url_brand = $b->brand_url;
							
							$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort.'&brand='.$url_brand);

							$get_data5 = $this->get_original_data($response5);				
						
							$this->assert_data($get_data5);
						}
					}
				}
			} 
		}
	}

	/** @test */
	function check_promo_page_param_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['color'] as $val) {
				$url_color = $val->color_name;

				if($url_color == "Multi Color"){
					$url_color = "Multi%20Color";
				}

				foreach ($sort_price as $sort) {
					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach($get_data2['size'] as $size_all){
						if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
							$url_size = $size_all->product_size_url;
						}else{
							$url_size = str_replace(' ','_',strtolower($size_all->product_size));
						}

						if($url_size != ''){
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort.'&size='.$url_size);
								
							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);

							foreach($get_data3['brand'] as $brand_all){
								$url_brand = $brand_all->brand_url;

								$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?color='.$url_color.'&'.$sort.'&size='.$url_size.'&brand='.$url_brand);
									
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
	function check_promo_page_param_gender_category_color_price_size_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);

							foreach($get_data3['size'] as $size_all){
								if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
									$url_size = $size_all->product_size_url;
								}else{
									$url_size = str_replace(' ','_',strtolower($size_all->product_size));
								}

								if($url_size != ''){
									$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
									
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
	function check_promo_page_param_gender_category_color_price_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);

							foreach($get_data3['brand'] as $b){
								$url_brand = $b->brand_url;
								
								$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort.'&brand='.$url_brand);

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
	function check_promo_page_param_gender_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['color'] as $val) {
					$url_color = $val->color_name;

					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort);

						$get_data2 = $this->get_original_data($response2);				
						
						$this->assert_data($get_data2);

						foreach($get_data2['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
								
								$get_data3 = $this->get_original_data($response3);				
						
								$this->assert_data($get_data3);

								foreach($get_data3['brand'] as $b){
									$url_brand = $b->brand_url;
									
									$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&color='.$url_color.'&'.$sort.'&size='.$url_size.'&brand='.$url_brand);

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
	function check_promo_page_param_category_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			$response = $this->call('GET','http://'.$domain.'/'.$url);
			
			$get_data = $this->get_original_data($response);				
						
			$this->assert_data($get_data);

			foreach ($get_data['category'] as $val) {
				$cat = $val->type_url;

				$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat);

				$get_data2 = $this->get_original_data($response2);				
						
				$this->assert_data($get_data2);

				foreach ($get_data2['color'] as $color_all) {
					$url_color = $color_all->color_name;
					
					if($url_color == "Multi Color"){
						$url_color = "Multi%20Color";
					}

					foreach ($sort_price as $sort) {
						$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort);

						$get_data3 = $this->get_original_data($response3);				
						
						$this->assert_data($get_data3);

						foreach($get_data3['size'] as $size_all){
							if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
								$url_size = $size_all->product_size_url;
							}else{
								$url_size = str_replace(' ','_',strtolower($size_all->product_size));
							}

							if($url_size != ''){
								$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
								
								$get_data4 = $this->get_original_data($response4);				
						
								$this->assert_data($get_data4);

								foreach($get_data4['brand'] as $b){
									$url_brand = $b->brand_url;
									
									$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size.'&brand='.$url_brand);

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
	function check_promo_page_param_gender_category_color_price_size_brand_hb()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$gender = array('0'=>'women','1'=>'men');

		$sort_price = $this->value_sort_price();

		$promos = $this->get_promo_page();

		foreach ($promos as $value) {
			$url = $value->special_page_url;
			
			foreach($gender as $g){
				$response = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g);
			
				$get_data = $this->get_original_data($response);				
						
				$this->assert_data($get_data);

				foreach ($get_data['category'] as $val) {
					$cat = $val->type_url;

					$response2 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat);

					$get_data2 = $this->get_original_data($response2);				
						
					$this->assert_data($get_data2);

					foreach ($get_data2['color'] as $color_all) {
						$url_color = $color_all->color_name;
						
						if($url_color == "Multi Color"){
							$url_color = "Multi%20Color";
						}

						foreach ($sort_price as $sort) {
							$response3 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort);

							$get_data3 = $this->get_original_data($response3);				
						
							$this->assert_data($get_data3);

							foreach($get_data3['size'] as $size_all){
								if(isset($size_all->product_size_url) && $size_all->product_size_url != ''){
									$url_size = $size_all->product_size_url;
								}else{
									$url_size = str_replace(' ','_',strtolower($size_all->product_size));
								}

								if($url_size != ''){
									$response4 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size);
									
									$get_data4 = $this->get_original_data($response4);				
						
									$this->assert_data($get_data4);

									foreach($get_data4['brand'] as $b){
										$url_brand = $b->brand_url;
										
										$response5 = $this->call('GET','http://'.$domain.'/'.$url.'?gender='.$g.'&cat='.$cat.'&color='.$url_color.'&'.$sort.'&size='.$url_size.'&brand='.$url_brand);

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