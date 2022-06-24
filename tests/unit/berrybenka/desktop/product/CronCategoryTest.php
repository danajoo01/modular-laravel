<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Request;

//use \App\Modules\Product\Controllers\CronCategoryController;
use \App\Modules\Product\Controllers;


class CronCategoryTest extends TestCase
{	
	/** @test */
	function run_filter_type_ok()
	{
		//$cron = new CronCategoryController();

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_filter_type');
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function run_parent_type_ok()
	{
		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_parent_type');
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function run_menu_type_ok()
	{
		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_menu_type');
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/** @test */
	function run_brand_parent_type_ok()
	{
		$params = ['brand_url'=>'accent'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_brand_parent_type',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/* 
		RESULT -> If brand url empty or null will return HTTP status code 404 
	*/
	/** @test */
	function run_brand_parent_type_failed_cause_data_empty()
	{
		$params = ['brand_url'=>''];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_brand_parent_type',$params);
		$this->assertEquals(404, $response->getStatusCode());
	}

	/* 
		RESULT -> If brand url not exist in database will return empty data 
	*/
	/** @test */
	function run_brand_parent_type_failed_cause_wrong_data()
	{
		$params = ['brand_url'=>'example'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_brand_parent_type',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	
	function run_tag_parent_type_ok()
	{
		$params = ['tag_url'=>'winter-boots-wanita'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_tag_parent_type',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}

	/* 
		RESULT -> If tag url empty or null will return HTTP status code 404 
	*/
	/** @test */
	function run_tag_parent_type_failed_cause_empty_data()
	{
		$params = ['tag_url'=>''];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_tag_parent_type',$params);
		$this->assertEquals(404, $response->getStatusCode());
		//$this->assertResponseOk();
	}

	/* 
		RESULT -> If tag url not exist in database will return empty data 
	*/
	/** @test */
	function run_tag_parent_type_failed_cause_wrong_data()
	{
		$params = ['tag_url'=>'example'];

		$response = $this->action('GET','\App\Modules\Product\Controllers\CronCategoryController@run_tag_parent_type',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}
}
