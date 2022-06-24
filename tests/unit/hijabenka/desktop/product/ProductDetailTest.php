<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductDetailController;
use \App\Modules\Product\Controllers;


class ProductDetailTestHBD extends TestCase
{
	/** @test */
	function product_detail_show_ok_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		$response = $this->call('GET','http://'.$domain.'/jilbab/jilbab/39203/aisyah-shawl');

		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_vari']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_vari_off']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_size']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_image']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_image_def']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color_others']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color_zero']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['tag_name']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['product_recommended']);
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Anda Juga Akan Menyukai');
	}

	/* EXPECTED RESULT -> if category empty or null it must redirect to catalog like if clothing empty parent,id,or productname */
	/* RESULT -> if category empty or null not redirect to catalog,but show error "internal server error (500)"  */
	/** @test */
	function product_detail_show_failed_cause_category_empty_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$response = $this->call('GET','http://'.$domain.'/clothing//144778/fanya-tulle-dress-mocca-dusty');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->see('Produk Yang Ada Cari Tidak Ditemukan');
	}

	/* RESULT -> if parent or id or productname empty or null will redirect to catalog */
	/** @test */
	function product_detail_show_failed_cause_parent_id_productname_empty_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');
		
		$response = $this->call('GET','http://'.$domain.'/clothing/dresses-and-jumpsuit/144778/');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->see('Produk Yang Ada Cari Tidak Ditemukan');
	}
	
	/* RESULT -> if parent or id or productname empty or null will redirect to catalog */
	/** @test */
	function set_wishlist_check_proccess_ok_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$params = ["customer_id"=>"34521","product_id"=>"144778","type"=>"1"];

		$response = $this->call('GET','http://'.$domain.'/product/set_wishlist',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function set_wishlist_check_proccess_not_login_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$params = ["customer_id"=>"","product_id"=>"144778","type"=>"1"];

		$response = $this->call('GET','http://'.$domain.'/product/set_wishlist',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function add_to_cart_hbd()
	{
		$domain = env('HIJABENKA', 'herman.hijabenka.biz');

		$params = ["product_id"=>"144778",
				   "product_ori_price"=> 250900,
				   "product_sale_price"=>0,
				   "product_special_price"=>0,
				   "product_price"=>250900,
				   "product_weight"=>"0,2",
				   "product_name"=>"Fanya Tulle Dress Mocca Dusty",
				   "type_id"=>"dresses-and-jumpsuit",
				   "parent_type_id"=>"clothing",
				   "type_id_real"=>"163",
				   "parent_type_id_real"=>"1",
				   "brand_id"=>"1130",
				   "SKU"=>"FOFRCLCRON-OA",
				   "product_inv"=>"forTWO",
				   "product_type_url"=>"clothing,dresses-and-jumpsuit",
				   "quantity"=>1,
				   "color_category"=>"132",
				   "size_category"=>"ONE SIZE",
				   "image_name"=>"144778_fanya-tulle-dress-mocca-dusty_moccasin_8887M.jpg",
				   "brand_name"=>"forTWO",
				   "variant_color_name"=>"Mocca Dusty",
				   "variant_color_id"=>"132",
				   "product_front_end_type"=>",1,163,"];

		$response = $this->call('GET','http://'.$domain.'/product/addtocart',$params);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}	
}