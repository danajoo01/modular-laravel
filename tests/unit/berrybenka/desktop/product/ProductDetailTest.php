<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductDetailController;
use \App\Modules\Product\Controllers;


class ProductDetailTestBBD extends TestCase
{
	/** @test */
	function product_detail_show_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');
		$response = $this->call('GET','http://'.$domain.'/clothing/outerwear/132769/edora-jacket');

		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_vari']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_vari_off']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_size']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_image']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_image_def']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color']);
		//$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color_others']);
		//$this->assertNotEmpty($response->getOriginalContent()->getData()['fetch_product_color_zero']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['tag_name']);
		$this->assertNotEmpty($response->getOriginalContent()->getData()['product_recommended']);
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
		$this->see('Anda Juga Akan Menyukai');
	}

	/* EXPECTED RESULT -> if category empty or null it must redirect to catalog like if clothing empty parent,id,or productname */
	/* RESULT -> if category empty or null not redirect to catalog,but show error "internal server error (500)"  */
	/** @test */
	function product_detail_show_failed_cause_category_empty_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');
		
		$response = $this->call('GET','http://'.$domain.'/clothing//132769/edora-jacket');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->see('Produk Yang Ada Cari Tidak Ditemukan');
	}

	/* RESULT -> if parent or id or productname empty or null will redirect to catalog */
	/** @test */
	function product_detail_show_failed_cause_parent_id_productname_empty_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');
		
		$response = $this->call('GET','http://'.$domain.'/clothing/outerwear/132769/');
		
		$this->assertEquals(200, $response->getStatusCode());
		$this->see('Produk Yang Ada Cari Tidak Ditemukan');
	}
	
	/* RESULT -> if parent or id or productname empty or null will redirect to catalog */
	/** @test */
	function set_wishlist_check_proccess_ok_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$params = ["customer_id"=>"34521","product_id"=>"132769","type"=>"1"];

		$response = $this->call('GET','http://'.$domain.'/product/set_wishlist',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function set_wishlist_check_proccess_not_login_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$params = ["customer_id"=>"","product_id"=>"132769","type"=>"1"];

		$response = $this->call('GET','http://'.$domain.'/product/set_wishlist',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function add_to_cart_bbd()
	{
		$domain = env('BERRYBENKA', 'herman.berrybenka.biz');

		$params = ["product_id"=>"132769",
				   "product_ori_price"=> 239000,
				   "product_sale_price"=>0,
				   "product_special_price"=>0,
				   "product_price"=>239000,
				   "product_weight"=>"0,2",
				   "product_name"=>"Edora Jacket",
				   "type_id"=>"jackets",
				   "parent_type_id"=>"outerwear",
				   "type_id_real"=>"61",
				   "parent_type_id_real"=>"10",
				   "brand_id"=>"118",
				   "SKU"=>"STEDCLBLS0-UO",
				   "product_inv"=>"Stratto",
				   "product_type_url"=>"clothing,outerwear,jackets",
				   "quantity"=>1,
				   "color_category"=>"140",
				   "size_category"=>"S",
				   "image_name"=>"132769_edora-jacket_multi-color_4XAAH.jpg",
				   "brand_name"=>"Stratto",
				   "variant_color_name"=>"Multi Color",
				   "variant_color_id"=>"140",
				   "product_front_end_type"=>",1,10,61,"];

		$response = $this->call('GET','http://'.$domain.'/product/addtocart',$params);

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}	
}