<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Modules\Product\Controllers\SearchController;
use \App\Modules\Product\Controllers;

class SearchTest extends TestCase
{
	/** @test */
	function search()
	{
		ini_set('memory_limit','256M');
		
		$params = ["limit"=>"1"];

		$response = $this->action('POST','\App\Modules\Product\Controllers\SearchController@search',$params);
		$this->assertEquals(200, $response->getStatusCode());
		$this->assertResponseOk();
	}
}