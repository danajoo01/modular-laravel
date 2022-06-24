<?php namespace App\Modules\Product\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Product\Models\Product;
use \App\Modules\Product\Models\Brand;
use \App\Modules\Product\Models\Tag;

use Input;
use Validatoor;

use Illuminate\Http\Request;

//use Illuminate\Http\Request;
// use Redis;
use Illuminate\Support\Facades\Redis;

class CronCategoryController extends Controller {

	/**
     * Run Filter Type
     *
     * @return Response
     */
    public function run_filter_type()
    {
      set_time_limit(0);
      $get_domain = get_domain(); //dd($get_domain);
      $data       = null;
      
      switch ($get_domain['domain_id']) {
        case '1':
          $type = [1, 2, 3, 4, 5, 6];
          break;
        case '2':
          $type = [1, 2, 3, 4, 5, 152, 153, 154];
          break;
        case '3':
          $type = [1, 2, 3, 4, 5, 6, 223, 242];
          break;
      }

      foreach ($type as $list_type) {
        $gender = [1, 2, 3];
        foreach ($gender as $list_gender) {
          //$path = url('/filter_type/'.$list_type.'/'.$list_gender.'');
          $path     = 'http://' . $get_domain['domain'] . '/filter_type/' . $list_type . '/' . $list_gender . '';
          $opts     = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
          $context  = stream_context_create($opts);
          $run      = file_get_contents($path, false, $context);
          //$run = file_get_contents($path);
          $data     = $run;
        }
      }
      return $data;
    }

    // --------------------------------------------------------------------

    /**
     * Run Filter Type
     *
     * @return Response
     */
    public function run_parent_type()
    {
      set_time_limit(0);
      $get_domain = get_domain();
      $data = null;
      $gender = [1, 2, 3];
      foreach ($gender as $list_gender) {
        //$path = url('/parent_type/'.$list_gender.'');
        $path = 'http://' . $get_domain['domain'] . '/parent_type/' . $list_gender . '';
        $opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $run = file_get_contents($path, false, $context);
        //$run = file_get_contents($path);
        $data = $run;
      }
      return $data;
    }

    // --------------------------------------------------------------------

    /**
     * Run Filter Type
     *
     * @return Response
     */
    public function run_menu_type()
    {
         $get_domain  = get_domain();
         $data = null;
         $gender = [1,2,3];
         foreach ($gender as $list_gender) {
            //$path = url('/menu_type/'.$list_gender.'');
            $path       = 'http://'.$get_domain['domain'].'/menu_type/'.$list_gender.'';
            $opts       = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
            $context    = stream_context_create($opts);
            $run        = file_get_contents($path, false, $context); 
            //$run = file_get_contents($path);
            $data = $run;
         }
         return $data;
    }

    // --------------------------------------------------------------------
	
	/**
     * Generate Menu Type Category by Brand
     *
     * @return Response
     */
	public function run_brand_parent_type($brand_url) {
		//Define Domain and Channel
    $get_domain = get_domain();
    
    $genders = [1, 2, 3];
    
		if ($brand_url == "all") {
			set_time_limit(0);
      
			$fetch_brand = Brand::where('enabled','=',1)
        ->orderBy('brand_id')->get();
      
			foreach ($fetch_brand as $brand) {
        foreach($genders as $gender){
          $params["folder_file"]	= "brand";
          $params["field_name"]   = "brand_url";
          $params["field_value"]	= trim($brand->brand_url);
          $params["gender"]       = $gender;

          $menu_category  = Product::generate_category_menu($params);

          $update_brand   = Brand::where('brand_url','=',$brand->brand_url)->first();

          $gender_string = "";
          if($gender == 1){
            $gender_string = "women_";
          }else if($gender == 2){
            $gender_string = "men_";
          }

          $update_brand->update(['brand_type_menu_'.$gender_string.$get_domain['domain_alias'].'' => $menu_category]);

          bb_debug($menu_category);
        }
			}
			
		} else  {
      foreach($genders as $gender){
        $params["folder_file"]	= "brand";
        $params["field_name"]   = "brand_url";
        $params["field_value"]	= trim($brand_url);
        $params["gender"]       = $gender;

        $menu_category  = Product::generate_category_menu($params);
        
        $update_brand   = Brand::where('brand_url','=',$brand_url)->first();

        $gender_string = "";
        if($gender == 1){
          $gender_string = "women_";
        }else if($gender == 2){
          $gender_string = "men_";
        }

        $update_brand->update(['brand_type_menu_'.$gender_string.$get_domain['domain_alias'].'' => $menu_category]);

        bb_debug($menu_category);
      }
		}
		
		echo 'This page took'.(microtime(true) - LARAVEL_START).' seconds to render';
	}

    // --------------------------------------------------------------------
	
	/**
     * Generate Menu Type Category by Tag
     *
     * @return Response
     */
	public function run_tag_parent_type($tag_url) {
		//Define Domain and Channel
    $get_domain = get_domain();
    
    $genders = [1, 2, 3];
		
		if ($tag_url == "all") {
			set_time_limit(0);
			/*$fetch_tag = Tag::where('tag_status','=',1)
        ->orderBy('tag_id')->get();*/
      $fetch_tag = Tag::orderBy('tag_id')->get();
							
			foreach ($fetch_tag as $tag) {
        foreach($genders as $gender){
          $params["folder_file"]	= "tag";
          $params["field_name"]   = "tag_url";
          $params["field_value"]  = str_replace(' ', '-', trim($tag->tag_url)); 
          $params["gender"]       = $gender;

          $menu_category = Product::generate_category_menu($params);
          
          $gender_string = "";
          if($gender == 1){
            $gender_string = "women_";
          }else if($gender == 2){
            $gender_string = "men_";
          }

          $update_tag = Tag::where('tag_url','=',$tag->tag_url)->first();
          $update_tag->update(['tag_type_menu_'.$gender_string.$get_domain['domain_alias'].'' => $menu_category]);
          
          bb_debug($menu_category);
        }
			}			
		} else {
      foreach($genders as $gender){
        $params["folder_file"]	= "tag";
        $params["field_name"]   = "tag_url";
        $params["field_value"]	= trim($tag_url);
        $params["gender"]       = $gender;

        $menu_category  = Product::generate_category_menu($params);

        $gender_string = "";
        if($gender == 1){
          $gender_string = "women_";
        }else if($gender == 2){
          $gender_string = "men_";
        }

        $update_tag = Tag::where('tag_url','=',$tag_url)->first();
        $update_tag->update(['tag_type_menu_'.$get_domain['domain_alias'].'' => $menu_category]);

        bb_debug($menu_category);
      }
		}
    
		echo 'This page took'.(microtime(true) - LARAVEL_START).' seconds to render';
	}

    // --------------------------------------------------------------------
	
	 /**
     * Call Filter Type
     *
     * @return Response
     */
    public function call_filter_type($id = null, $gender = null)
    {
        $value = Product::filter_type($id, $gender);
        echo $value;
    }

    // --------------------------------------------------------------------

    /**
     * Call Parent Type
     *
     * @return Response
     */
    public function call_parent_type($gender = null)
    {
        $value = Product::parent_type($gender);
        echo $value;
    }

    // --------------------------------------------------------------------

    /**
     * Call Menu Type
     *
     * @return Response
     */
    public function call_menu_type($gender = null)
    {
        $value = Product::menu_type($gender);
        echo $value;
    }

    // --------------------------------------------------------------------

    /** 
    **** Generate Type Category
    **** @return Json file. 
    */
    public function generateTypeCategory()
    {
        $alltype = Product::generateTypeCategory();

        echo $alltype;
    }

    public function testCreate()
    {
        //\Storage::disk('local')->put('berrybenka/catalog/test_create.json', 'test create');
        $homepage = file_get_contents('http://berrybenka.biz/');
        echo $homepage;
    }

}
