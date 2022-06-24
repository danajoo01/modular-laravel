<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

use \App\Modules\Product\Controllers\ProductController;
use \App\Modules\Product\Controllers;

class ProductTest extends TestCase
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
					->where('type_owner','!=',2)
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
					->where('type_owner','!=',2)
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

	
}
