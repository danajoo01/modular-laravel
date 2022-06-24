<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Session;
use Request;
use Redirect;
use \App\Modules\Product\Models\Term;

class Search extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'special_page'; //Define your table name

	protected $primaryKey = 'special_page_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['special_page_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Product\Models\Model_name');
	// }

	public static function getAjaxSearch($request) {

		$keyword        = ltrim(trim($request["q"]),"-");
    $unallowedchar  = array("[","]","+"," -","\\","/","{","}",":","(",")","^","\"");
    $word           = urlencode(str_replace($unallowedchar,"",$keyword));
    $gender         = $request["gender"];
       
        // if($request["domain"]["domain_id"] == 1){
            if($word!=FALSE)
            {
                $pname          = "";
                $core_selector  = getCoreSelector("front_end_type");
    
                //category
                $query          = "type_name:*$word*+OR+type_name_bahasa:*$word*";
                $rows           = 5;
    
                $showDatacategory   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
                // die(print_r($showDatacategory));
                try{
                  $totalCategory      = $showDatacategory->numFound;
                  $tag_val            = array();
                  $category_val       = array();
                  $hasil_pencarian    = array();
                  $totalTags          = 0;
    
                  if($totalCategory > 0){    
                    foreach($showDatacategory->docs as $category){
                      $category_label   = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$category->type_url.'\',\'category\',\'\',\''.$category->type_name_bahasa.'\',\'\',\'women\')">'.ucwords($keyword).' di kategori '.str_replace($keyword,'<span style="font-weight:bold;">'.ucwords($keyword).'</span>',strtolower($category->type_name_bahasa));
    
                      if($category->gender == 3){
                        $category_val[] = $category_label."</div></div>|".$category->type_name_bahasa."|".$category->type_url."|category|NULL|".$category->type_name."|NULL|women\n";
                      }else{
                        $cat_gender     = ($category->gender == 1) ? "women" : "men";
                        $category_label = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$category->type_url.'\',\'category\',\'\',\''.$category->type_name_bahasa.'\',\'\',\''.$cat_gender.'\')">'.ucwords($keyword).' di kategori '.str_replace($keyword,'<span style="font-weight:bold;">'.ucwords($keyword).'</span>',strtolower($category->type_name_bahasa));
                        $category_val[] = $category_label."</div></div>|".$category->type_name_bahasa."|".$category->type_url."|category|NULL|".$category->type_name."\n";
                      }
                    }
                  }

                  //tags
                  $core_selector  = getCoreSelector("tags");
                  $query          = "tag_name:*$word*";
                  $rows           = 5;
                  $showDataTags   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
                  $totalTags      = $showDataTags->numFound;

                  if($totalCategory > 0 || $totalTags > 0){
                    $pname .= "<h1>Rekomendasi Pencarian:</h1>\n";
    
                    //sorting 5 rekomendasi pencarian

                    // if ($totalCategory + $totalTags <= 5) {
                      if ($totalCategory > 0) {
                        foreach ($category_val as $cat) {
                          $pname .= $cat;
                        } 
                      }
  
                      if($totalTags > 0){
                        foreach($showDataTags->docs as $key => $tags){
                          $tag_label = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$tags->tag_url.'\',\'tags\',\'\',\''.$tags->tag_name.'\')">'.ucwords($keyword).' di tag: #'.preg_replace('/\s+/', '', str_replace($keyword,'<span style="font-weight:bold;">'.$keyword.'</span>',strtolower($tags->tag_name))).'</div></div>';
                          $pname .= $tag_label."|".$tags->tag_name."|".$tags->tag_url."|tags|NULL|".$tags->tag_name."\n";
                          }
                      }  
                    // } else {
                    //   if ($totalCategory >= 3 && $totalTags >= 2) {
                    //     $category_limit = 2;
                    //     $tag_limit = 1;
                    //   } elseif ($totalTags >= 3 && $totalCategory >= 2) {
                    //     $category_limit = 1;
                    //     $tag_limit = 2;
                    //   } elseif ($totalCategory >= 3 && $totalTags > 0) {
                    //     $category_limit = 4 - $totalTags;
                    //     $tag_limit = $totalTags - 1;
                    //   } elseif ($totalTags >= 3 && $totalCateogry > 0) {
                    //     $category_limit = $totalCategory - 1;
                    //     $tag_limit = 4 - $totalCategory;
                    //   } elseif (($totalCategory > 0 && $totalCategory < 3) && $totalTags > 0) {
                    //     $category_limit = 4 - $totalCategory;
                    //     $tag_limit = $category_limit - 1;
                    //   } elseif (($totalTags > 0 && $totalTags < 3) && $totalTags > 0) {
                    //     $category_limit = 4 - $totalTags;
                    //     $tag_limit = $tag_limit - 1;
                    //   }


                      // if ($totalCategory > 0) {
                      //   foreach ($category_val as $key => $cat) {
                      //     $pname .= $cat;
                      //     if ($key == $category_limit) {
                      //       break;
                      //     }
                      //   } 
                      // }
  
                      // if($totalTags > 0){
                      //   foreach($showDataTags->docs as $tags){
                      //     $tag_label = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$tags->tag_url.'\',\'tags\',\'\',\''.$tags->tag_name.'\')">'.ucwords($keyword).' di tag: #'.preg_replace('/\s+/', '', str_replace($keyword,'<span style="font-weight:bold;">'.$keyword.'</span>',strtolower($tags->tag_name))).'</div></div>';
                      //     $pname .= $tag_label."|".$tags->tag_name."|".$tags->tag_url."|tags|NULL|".$tags->tag_name."\n";
                      //     if ($key == $tag_limit) {
                      //       break;
                      //     }
                      //   }
                      // }
                      // if ($totalCategory > $totalTags) {
                      //   #if category > tags
                      //   if ($totalCategory > 5 || $totalTags > 5) {
                      //     # code...
                      //   }
                      // } else {
                      //   #if tags > category
                      // }
                    // }
                  }

                  //products
                  $core_selector  = getCoreSelector("product_detail");
                  $query          = 'product_name:'.$word.'*+OR+product_name:"'.$word.'"+OR+product_sku:"'.$word.'"';
                  $rows           = 100;
                  $group          = 'pid';
                  $sort           = "total_series_score+desc%2Cproduct_scoring+desc";
    
                  $fqPd["product_status"]       = 1;
                  $fqPd["eksklusif_in_promo"]   = 0;
    
                  $showDataproducts   = get_active_solr($core_selector, $query, $fqPd, $rows, null, $sort, $group);
                  $totalproducts      = $showDataproducts->numFound;
                  $getPromoByPid      = array();

                  if($totalproducts > 0){
                    $productsData   = array();
                    $countProducts  = 0;
    
                    foreach($showDataproducts->docs as $product){
                      $parents        = explode(",",$product->url_set);
                      $explodeBahasa  = explode(",",$product->bahasa);
                      $rightCategory  = "";
    
                      /**
                      * 
                      * check if url_set is available and has parent & child category
                      * if so then $parent variable value should be parent and child 
                      * also right category 
                      * 
                      */
                      if(isset($product->url_set)){
                        if($request["domain"]["channel"] == 1 || $request["domain"]["channel"] == 3 || $request["domain"]["channel"] == 5){
                          $style = 'style="float:right;"';
                        }else{
                          $style = '';
                        }
    
                        if(count($parents) > 1 && count($explodeBahasa) > 1){
                          $parent             = $parents[0].'/'.$parents[1];
                          $rightCategory      = "<span class=\"search-cat\" ".$style.">".$explodeBahasa[0].' - '.$explodeBahasa[1]."</span>";
                          $groupByCategory    = str_replace(" ","_",$explodeBahasa[0]).'_'.str_replace(" ","_",$explodeBahasa[1]);
                        }else{
                          if (!isset($gender) || $gender == null || $gender == '') {
                            $gender = $parents[0];
                          }
                          $parent             = $gender.'/'.$parents[0];
                          $rightCategory      = "<span class=\"search-cat\" ".$style.">".$explodeBahasa[0]."</span>";
                          $groupByCategory    = str_replace(" ","_",$explodeBahasa[0]);
                        }
                      }else{
                        $parent = $gender;
                      }
    
                      $url_product           = $product->pid."/".url_title(strtolower($product->product_name));
                      $products_name         = str_replace("'","\\",$product->product_name);
                      $product_name          = str_replace("\\","",$product->product_name);
                      $product_name_label    = '<div class="hvr" onclick="sendVal(\''.$url_product.'\',\'\',\'\',\''.$products_name.'\',\''.$parent.'\')">'."<strong>".$product_name."</strong>".$rightCategory.'</div>';
                      $productArray          = $product_name_label."|".$product_name."|".$url_product."|product|NULL|".$product_name."|".$parent."\n";
    
                      if(!isset($productsData[$groupByCategory])){
                        $productsData[$groupByCategory] = array();
    
                        if(!in_array($productArray,$productsData[$groupByCategory])){
                          array_push($productsData[$groupByCategory],$productArray);
                          $countProducts++;
                        }
                      }else{
                        if(in_array($productArray,$productsData[$groupByCategory]) || count($productsData[$groupByCategory])<=1){
                          array_push($productsData[$groupByCategory],$productArray);
                          $countProducts++;
                        }
                      }
    
                      $pid[] = $product->pid;
    
                      if($countProducts == 10)
                      {
                        break;
                      }
                    }
    
                    $pname .= "<span style=\"opacity:0.6;border-top: 1px solid rgba(0,0,0,.1);cursor:default;\" onclick=\"return false;\"><h4></h4></span>\n";
                    $pname .= "<h1>Rekomendasi Produk:</h1>\n";
    
                    foreach($productsData as $productKey => $productVal){
                      foreach($productVal as $vals){
                        $pname .= $vals;
                      }
                    }
                  }
                }catch (\Exception $ex) {
                  abort(404);
                  \Log::error($ex);
                  \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                }
                return $pname;
            }
        // } elseif ($request["domain"]["domain_id"] == 2) {
          // if($word!=FALSE)
          // {
          //     $pname          = "";
          //     $core_selector  = getCoreSelector("front_end_type");
  
          //     //category
          //     $query          = 'type_name_bahasa:'.$word.'*+OR+type_name_bahasa:'.$word;
          //     $rows           = 5;
  
          //     $showDatacategory   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
              
          //     try{
          //       $totalcategory      = $showDatacategory->numFound;
          //       $tag_val            = array();
          //       $category_val       = array();
          //       $hasil_pencarian    = array();
          //       $totalTags          = 0;
  
          //       if($totalcategory > 0){    
          //         foreach($showDatacategory->docs as $category){
          //           $category_label_women   = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$category->type_url.'\',\'category\',\'\',\''.$category->type_name_bahasa.'\',\'\',\'women\')">'.strtolower($category->type_name_bahasa);
          //           $category_label_men     = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$category->type_url.'\',\'category\',\'\',\''.$category->type_name_bahasa.'\',\'\',\'men\')">'.strtolower($category->type_name_bahasa);
  
          //           if($category->gender == 3){
          //             $category_val[] = $category_label_women." Wanita</div></div>|".$category->type_name_bahasa." Wanita|".$category->type_url."|category|NULL|".$category->type_name."|NULL|women\n";
          //             $category_val[] = $category_label_men." Pria</div></div>|".$category->type_name_bahasa." Pria|".$category->type_url."|category|NULL|".$category->type_name."|NULL|men\n";
          //           }else{
          //             $cat_gender     = ($category->gender == 1) ? "women" : "men";
          //             $category_label = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$category->type_url.'\',\'category\',\'\',\''.$category->type_name_bahasa.'\',\'\',\''.$cat_gender.'\')">'.str_replace($keyword,'<span style="font-weight:bold;">'.ucwords($keyword).'</span>',strtolower($category->type_name_bahasa));
          //             $category_val[] = $category_label."</div></div>|".$category->type_name_bahasa."|".$category->type_url."|category|NULL|".$category->type_name."\n";
          //           }
          //         }
          //       }
  
          //       if($totalcategory < 5){
          //         //tags
          //         $core_selector  = getCoreSelector("tags");
          //         $query          = 'tag_name:'.$word.'*+OR+tag_name:'.$word;
          //         $rows           = 5 - (int) $totalcategory;
          //         $showDataTags   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
          //         $totalTags      = $showDataTags->numFound;
          //       }
  
          //       if(count($category_val) > 0 || $totalTags > 0){
          //         $pname .= "<h1>Hasil Pencarian</h1>\n";
  
          //         foreach ($category_val as $key => $cats) {
          //           $pname .= $cats;
  
          //           if($key == 4){
          //             break;
          //           }
          //         }
  
          //         if($totalTags > 0){
          //           foreach($showDataTags->docs as $tags){
          //             $tag_label = '<div class="phvr"><div class="hvr" onclick="sendVal(\''.$tags->tag_url.'\',\'tags\',\'\',\''.$tags->tag_name.'\')">'.str_replace($keyword,'<span style="font-weight:bold;">'.ucwords($keyword).'</span>',strtolower($tags->tag_name)).'</div></div>';
  
          //             $pname .= $tag_label."|".$tags->tag_name."|".$tags->tag_url."|tags|NULL|".$tags->tag_name."\n";
          //           }
          //         }
          //       }

          //       $core_selector  = getCoreSelector("brand");
          //       $query          = 'brand_name:'.$word.'*+OR+brand_name:'.$word.'+OR+brand_name:"'.$word.'"';
          //       $rows           = 5;

          //       $showDatabrand   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
          //       $totalbrand      = $showDatabrand->numFound;

          //       if($totalbrand > 0){
          //           $pname .= "<h1>Brand</h1>\n";

          //           foreach($showDatabrand->docs as $brand){
          //           $brand_name = str_replace("'","\\",$brand->brand_name);
          //           $brandname_label = '<div class="hvr" onclick="sendVal(\''.$brand->brand_url.'\',\'brand\',\'\',\''.$brand_name.'\')">'.$brand->brand_name.'</div>';
          //           $pname .= $brandname_label."|".$brand->brand_name."|".$brand->brand_url."|brand|NULL|".$brand->brand_name."\n";
          //           }
          //       }
              
          //       //products
          //       $core_selector  = getCoreSelector("product_detail");
          //       $query          = 'product_name:'.$word.'*+OR+product_name:"'.$word.'"+OR+product_sku:"'.$word.'"';
          //       $rows           = 100;
          //       $group          = 'pid';
  
          //       $fqPd["product_status"]       = 1;
          //       $fqPd["eksklusif_in_promo"]   = 0;
  
          //       $showDataproducts   = get_active_solr($core_selector, $query, $fqPd, $rows, null, null, $group);
          //       $totalproducts      = $showDataproducts->numFound;
          //       $getPromoByPid      = array();
  
          //       if($totalproducts > 0){
          //         $productsData   = array();
          //         $countProducts  = 0;
  
          //         foreach($showDataproducts->docs as $product){
          //           $parents        = explode(",",$product->url_set);
          //           $explodeBahasa  = explode(",",$product->bahasa);
          //           $rightCategory  = "";
  
          //           /**
          //           * 
          //           * check if url_set is available and has parent & child category
          //           * if so then $parent variable value should be parent and child 
          //           * also right category 
          //           * 
          //           */
          //           if(isset($product->url_set)){
          //             if($request["domain"]["channel"] == 1 || $request["domain"]["channel"] == 3 || $request["domain"]["channel"] == 5){
          //               $style = 'style="float:right;"';
          //             }else{
          //               $style = '';
          //             }
  
          //             if(count($parents) > 1 && count($explodeBahasa) > 1){
          //               $parent             = $parents[0].'/'.$parents[1];
          //               $rightCategory      = "<span class=\"search-cat\" ".$style.">".$explodeBahasa[0].' - '.$explodeBahasa[1]."</span>";
          //               $groupByCategory    = str_replace(" ","_",$explodeBahasa[0]).'_'.str_replace(" ","_",$explodeBahasa[1]);
          //             }else{
          //               $parent             = $gender.'/'.$parents[0];
          //               $rightCategory      = "<span class=\"search-cat\" ".$style.">".$explodeBahasa[0]."</span>";
          //               $groupByCategory    = str_replace(" ","_",$explodeBahasa[0]);
          //             }
          //           }else{
          //             $parent = $gender;
          //           }
  
          //           $url_product           = $product->pid."/".url_title(strtolower($product->product_name));
          //           $products_name         = str_replace("'","\\",$product->product_name);
          //           $product_name          = str_replace("\\","",$product->product_name);
          //           $product_name_label    = '<div class="hvr" onclick="sendVal(\''.$url_product.'\',\'\',\'\',\''.$products_name.'\',\''.$parent.'\')">'."<strong>".$product_name."</strong>".$rightCategory.'</div>';
          //           $productArray          = $product_name_label."|".$product_name."|".$url_product."|product|NULL|".$product_name."|".$parent."\n";
  
          //           if(!isset($productsData[$groupByCategory])){
          //             $productsData[$groupByCategory] = array();
  
          //             if(!in_array($productArray,$productsData[$groupByCategory])){
          //               array_push($productsData[$groupByCategory],$productArray);
          //               $countProducts++;
          //             }
          //           }else{
          //             if(in_array($productArray,$productsData[$groupByCategory]) || count($productsData[$groupByCategory])<=1){
          //               array_push($productsData[$groupByCategory],$productArray);
          //               $countProducts++;
          //             }
          //           }
  
          //           $pid[] = $product->pid;
  
          //           if($countProducts == 10)
          //           {
          //             break;
          //           }
          //         }
  
          //         $pname .= "<span style=\"opacity:0.6;border-top: 1px solid rgba(0,0,0,.1);cursor:default;\" onclick=\"return false;\"><h4></h4></span>\n";
  
          //         foreach($productsData as $productKey => $productVal){
          //           foreach($productVal as $vals){
          //             $pname .= $vals;
          //           }
          //         }
          //       }
          //     }catch (\Exception $ex) {
          //       abort(404);
          //       \Log::error($ex);
          //       \Log::error('error Exception with URI : ' . \Request::fullUrl());       
          //     }
              
          //     return $pname;
          // }
      // }
    }

    public static function find_catalog($keyword)
    {
      $core_selector  = getCoreSelector("front_end_type");
      $query          = 'type_name_search:"'.urlencode($keyword).'"+OR+type_name_bahasa_search:"'.urlencode($keyword).'"';
      $rows           = 1;
      $showDatacategory   = get_active_solr($core_selector, $query, null, $rows, null, "id+asc", null);
      // $showDatacategory   = get_active_solr($core_selector, $query, null, $rows, null, null, null);

      if (isset($showDatacategory->docs)) {
        return $showDatacategory->docs;
      } else {
        return false;
      }
    }

    public static function find_tags($keyword)
    {
      $core_selector  = getCoreSelector("tags");
      $query          = 'tag_name_search:"'.urlencode($keyword).'"';
      $rows           = 1;
      $showDataTags   = get_active_solr($core_selector, $query, null, $rows, null, null, null);

      if (isset($showDataTags->docs)) {
        return true;
      } else {
        return false;
      }
    }

    public static function search($request)
    {
        $keyword  = ltrim(trim($request["q"]),"-");
      
        $gender   = $request['gender'] ? $request['gender'] : '';
        $type     = $request['type'] ? $request['type'] : 'filter';
        $filter   = ($request['filter'] != FALSE || $request['filter'] !=NULL) ? $request['filter'] : '';
        $url      = $request['url'] ? $request['url'] : '';
        $parent   = $request['parent'] ? strtolower($request['parent']) : $gender;

        $keyword  = $request['keywords'] ? ltrim(trim($request['keywords']),"-") : ltrim(trim($request['keyword']),"-");

        $isTag = false; 
        if (Request::input('isEnter')) {          
          $keyword = ucwords(strtolower($keyword));
          $check_category = Search::find_catalog($keyword);
          if($check_category != FALSE){
            $type = "category";
            $gender = "women";
            $keyword = strtolower($check_category[0]->type_url);
          }

          if(!$check_category){
            if (Search::find_tags($keyword)) {
              $type = "tags";
              $isTag = true;  
            }
          }
        }        

        // $unallowedchar  = array("[","]","+"," -","\\","/","{","}",":","(",")","^","\"");
        // $keyword        = urlencode(str_replace($unallowedchar,"",$keyword));

        $searchData = $request['searchData'] ? $request['searchData'] : '';
        $domain_id  = $request["domain"]['domain_id'];

        $search['word']         = str_replace(array("+","-and-"),array(" ","&"),strtolower($keyword));
        $search['category']     = $type;
        $search['url']          = ($url!='')?$url:NULL;
        $search['ip_address']   = Request::getClientIp();
        $search['domain_id']    = $domain_id;
        
        //customer login check
        
        if (!empty(\Auth::user()))
        {
            $search['user_id']      = \Auth::user()->customer_id;
            $search['user_gender']  = \Auth::user()->customer_gender;
        }
        else
        {
            $search['user_id']       = NULL;
            $search['user_gender']   = NULL;    
        }

        $create_terms_search = Term::create($search);
        
        if ($keyword)
        {
            Session::put('keyword', $keyword);
        }
        
        $keyword        = session('keyword');

        $fetch_search_products          = array();
        $count_fetch_search_products    = 0;
        $start_catalog                  = null;
        $rows                           = null;

        if(!empty($keyword))
        {
            /*---------------- Page Referer --------------*/
            $trc_sale       = Request::segment(2) . '-' . Request::segment(1);
            $skeyword       = str_replace(array(" ","&"),array("+","-and-"),strtolower($keyword));
            $ref_keyword    = '?s='.$skeyword;
            $page_ref       = array(
                                    'trc_sale' => isset($trc_sale) ?  $trc_sale : NULL,
                                    's' => isset($skeyword) ?  $skeyword : NULL,
                                 );
            $page_ref_qry   = '&trc_sale=search-solr';
            /*------------- End Of Page Referer ------------*/

            if($type != FALSE)
            {
                if($type == "product")
                {
                    // Update URL term search
                    $search['url_terms'] = $parent.'/'.$url.$ref_keyword.$page_ref_qry;
                    
                    $update_terms_search = Term::find($create_terms_search->id);
                    $update_terms_search->url = $search['url_terms'];
                    $update_terms_search->save();
                
                    return $search;
                }
                else if($type == "category")
                {
                    $solrCoreProducts = getCoreSelector('products');
                    $solrCoreFet      = getCoreSelector('front_end_type');

                    $rows           = 1;
                    
                    if($gender == "women")
                    {
                        $query          = 'url_set:'.$keyword;

                        $fq['-product_gender']      = "2";
                        $fq['product_status']       = 1;
                        $fq["eksklusif_in_promo"]   = 0;

                        $cTojsonCheck   = get_active_solr($solrCoreProducts, $query, $fq, $rows, null, null, null);
                        try{
                          $showDataCheck  = $cTojsonCheck->docs;
                          $countDataCheck = $cTojsonCheck->numFound;
                          
                          $query          = 'type_url:"'.urlencode($keyword).'"';

                          $cTojsoncategoryCheck   = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                          $showDatacategoryCheck  = $cTojsoncategoryCheck->docs;
                          $totalcategoryCheck     = $cTojsoncategoryCheck->numFound;


                          if($countDataCheck > 0 && $totalcategoryCheck > 0 && $showDatacategoryCheck[0]->parent != 0)
                          {
                            $query = 'id:"'.$showDatacategoryCheck[0]->parent.'"';

                            $decodeParentCheck     = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                            $showDataParentCheck   = $decodeParentCheck->docs;
                            $totalParentCheck      = $decodeParentCheck->numFound;

                            if($totalParentCheck>0)
                            {
                              // Update URL term search
                              if (Request::input('isEnter')) {
                                $search['url_terms'] = $showDataParentCheck[0]->type_url.'/'.$skeyword.$ref_keyword;
                              } else {
                                $search['url_terms'] = $showDataParentCheck[0]->type_url.'/'.$showDatacategoryCheck[0]->type_url.'/'.$gender.$ref_keyword;
                              }

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                            }
                            else
                            {
                              // Update URL term search
                              if (Request::input('isEnter')) {
                                $search['url_terms'] = $showDataCheck[0]->type_url.'/'.$skeyword.$ref_keyword;
                              } else {
                                $search['url_terms'] = $showDataCheck[0]->type_url.'/'.$showDatacategoryCheck[0]->type_url.'/'.$gender.$ref_keyword;
                              }

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                            }
                          }else{
                            // Update URL term search
                            if (Request::input('isEnter')) {
                              $search['url_terms'] = $keyword.$ref_keyword;
                            } else {
                              $search['url_terms'] = $keyword.'/'.$gender.$ref_keyword;
                            }

                            $update_terms_search = Term::find($create_terms_search->id);
                            $update_terms_search->url = $search['url_terms'];
                            $update_terms_search->save();

                            return $search;
                          }
                        }catch (\Exception $ex) {
                          \Log::error($ex);
                          \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                        }
                    }
                    elseif($gender == "men")
                    {
                        $query          = 'url_set:'.$keyword;

                        $fq['-product_gender']      = "1";
                        $fq['product_status']       = 1;
                        $fq["eksklusif_in_promo"]   = 0;

                        $cTojsonCheck   = get_active_solr($solrCoreProducts, $query, $fq, $rows, null, null, null);
                        
                        try{
                          $showDataCheck  = $cTojsonCheck->docs;
                          $countDataCheck = $cTojsonCheck->numFound;

                          $query          = 'type_url:"'.urlencode($keyword).'"';

                          $cTojsoncategoryCheck   = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                          $showDatacategoryCheck  = $cTojsoncategoryCheck->docs;
                          $totalcategoryCheck     = $cTojsoncategoryCheck->numFound;

                          if($countDataCheck>0 && $totalcategoryCheck>0 && $showDatacategoryCheck[0]->parent !=0)
                          {
                              $query = 'id:"'.$showDatacategoryCheck[0]->parent.'"';

                              $decodeParentCheck     = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                              $showDataParentCheck   = $decodeParentCheck->docs;
                              $totalParentCheck      = $decodeParentCheck->numFound;

                              if($totalParentCheck>0)
                              {
                                  // Update URL term search
                                  $search['url_terms'] = $showDataParentCheck[0]->type_url.'/'.$showDatacategoryCheck[0]->type_url.'/'.$gender.$ref_keyword;

                                  $update_terms_search = Term::find($create_terms_search->id);
                                  $update_terms_search->url = $search['url_terms'];
                                  $update_terms_search->save();

                                  return $search;
                              }
                              else
                              {
                                  // Update URL term search
                                  $search['url_terms'] = $showDataCheck[0]->type_url.'/'.$showDatacategoryCheck[0]->type_url.'/'.$gender.$ref_keyword;

                                  $update_terms_search = Term::find($create_terms_search->id);
                                  $update_terms_search->url = $search['url_terms'];
                                  $update_terms_search->save();

                                  return $search;
                              }
                          }
                          else
                          {
                              // Update URL term search
                              $search['url_terms'] = $keyword.'/'.$gender.$ref_keyword;

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                          }
                        }catch (\Exception $ex) {
                          \Log::error($ex);
                          \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                        }
                    }
                }
                // else if($type == "brand")
                // {
                //     $solrCoreProducts = getCoreSelector('products');

                //     $query            = 'brand_url:"'.$keyword.'"';

                //     if($gender == "women")
                //     {
                //         $fq['-product_gender']      = "2";
                //         $fq['product_status']       = 1;
                //         $fq["eksklusif_in_promo"]   = 0;

                //         $women          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                        
                //         try{
                //           $getDataWomen   = $women->docs;
                //           $countDataWomen = $women->numFound;

                //           if($countDataWomen>0)
                //           {
                //               $gender = '';
                //               if($domain_id != 3){
                //                 $gender     = "&gender=women";    
                //               }
                              
                //               $brand_url  = $getDataWomen[0]->brand_url;

                //               // Update URL term search
                //               $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //               $update_terms_search = Term::find($create_terms_search->id);
                //               $update_terms_search->url = $search['url_terms'];
                //               $update_terms_search->save();

                //               return $search;
                //           }
                //           else
                //           {
                //               $fq['-product_gender']      = "1";
                //               $fq['product_status']       = 1;
                //               $fq["eksklusif_in_promo"]   = 0;

                //               $men          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                //               $getDataMen   = $men->docs;
                //               $countDataMen = $men->numFound;

                //               if($countDataMen>0)
                //               {
                //                   $gender     = "&gender=men";
                //                   $brand_url  = $getDataMen[0]->brand_url;

                //                   // Update URL term search
                //                   $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //                   $update_terms_search = Term::find($create_terms_search->id);
                //                   $update_terms_search->url = $search['url_terms'];
                //                   $update_terms_search->save();

                //                   return $search;
                //               }
                //           }
                //         }catch (\Exception $ex) {
                //           \Log::error($ex);
                //           \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                //         }
                //     }
                //     elseif($gender == "men")
                //     {
                //         $fq['-product_gender']      = "1";
                //         $fq['product_status']       = 1;
                //         $fq["eksklusif_in_promo"]   = 0;

                //         $men          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                        
                //         try{
                //           $getDataMen   = $men->docs;
                //           $countDataMen = $men->numFound;

                //           if($countDataMen>0)
                //           {
                //               $gender     = "&gender=men";
                //               $brand_url  = $getDataMen[0]->brand_url;

                //               // Update URL term search
                //               $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //               $update_terms_search = Term::find($create_terms_search->id);
                //               $update_terms_search->url = $search['url_terms'];
                //               $update_terms_search->save();

                //               return $search;
                //           }
                //           else
                //           {
                //               $fq['-product_gender']      = "2";
                //               $fq['product_status']       = 1;
                //               $fq["eksklusif_in_promo"]   = 0;

                //               $women          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                //               $getDataWomen   = $women->docs;
                //               $countDataWomen = $women->numFound;

                //               if($countDataWomen>0)
                //               {
                //                   $gender     = "&gender=women";
                //                   $brand_url  = $getDataWomen[0]->brand_url;

                //                   // Update URL term search
                //                   $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //                   $update_terms_search = Term::find($create_terms_search->id);
                //                   $update_terms_search->url = $search['url_terms'];
                //                   $update_terms_search->save();

                //                   return $search;
                //               }
                //           }
                //         }catch (\Exception $ex) {
                //           \Log::error($ex);
                //           \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                //         }
                //     }
                //     else
                //     {
                //         $fq['-product_gender']      = "2";
                //         $fq['product_status']       = 1;
                //         $fq["eksklusif_in_promo"]   = 0;

                //         $women          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                        
                //         try{
                //           $getDataWomen   = $women->docs;
                //           $countDataWomen = $women->numFound;

                //           if($countDataWomen>0)
                //           {
                //               $gender     = "&gender=women";
                //               $brand_url  = $getDataWomen[0]->brand_url;

                //               // Update URL term search
                //               $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //               $update_terms_search = Term::find($create_terms_search->id);
                //               $update_terms_search->url = $search['url_terms'];
                //               $update_terms_search->save();

                //               return $search;
                //           }
                //           else
                //           {
                //               $fq['-product_gender']      = "1";
                //               $fq['product_status']       = 1;
                //               $fq["eksklusif_in_promo"]   = 0;

                //               $men          = get_active_solr($solrCoreProducts, $query, $fq, 1000, null, null, null,"brand_url");
                //               $getDataMen   = $men->docs;
                //               $countDataMen = $men->numFound;

                //               if($countDataMen>0)
                //               {
                //                   $gender     = "&gender=men";
                //                   $brand_url  = $getDataMen[0]->brand_url;

                //                   // Update URL term search
                //                   $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //                   $update_terms_search = Term::find($create_terms_search->id);
                //                   $update_terms_search->url = $search['url_terms'];
                //                   $update_terms_search->save();

                //                   return $search;
                //               }
                //           }
                //         }catch (\Exception $ex) {
                //           \Log::error($ex);
                //           \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                //         }
                //     }
                // }
                else if($type == "tags")
                {
                  if (Request::input('isEnter') && $isTag) {
                    $core_selector  = getCoreSelector("tags");
                    $query          = 'tag_name:"'.$keyword.'"';
                    $rows           = 1;
                    $showDataTags   = get_active_solr($core_selector, $query, null, $rows, null, null, null);
                    $totalTags      = $showDataTags->numFound;
                    
                    if($totalTags > 0){
                      $search['url_terms'] = "tag/".$showDataTags->docs[0]->tag_url.$url.$ref_keyword;

                      $update_terms_search = Term::find($create_terms_search->id);
                      $update_terms_search->url = $search['url_terms'];
                      $update_terms_search->save();
    
                      return $search;
                    } 

                  } else {
                    $search['url_terms'] = "tag/".$url.$ref_keyword;

                    $update_terms_search = Term::find($create_terms_search->id);
                    $update_terms_search->url = $search['url_terms'];
                    $update_terms_search->save();

                    return $search;
                  }
                }
            }

            $sort = !empty(session('sort')) ? session('sort') : "total_series_score+desc%2Cproduct_scoring+desc";

            if( isset($searchData))
            {
                switch ($searchData) {
                    case 'price=asc':
                        $sort = "real_price+asc";
                        Session::put('sort', $sort);
                        break;
                    
                    case 'price=desc':
                        $sort = "real_price+desc";
                        Session::put('sort', $sort);
                        break;
                    
                    case 'pn=desc':
                        $sort = "launch_date_bb+desc";
                        if($domain_id == 2){
                          $sort = "launch_date_hb+desc";
                        }elseif($domain_id == 3){
                          $sort = "launch_date_sd+desc";
                        }
                        
                        Session::put('sort', $sort);
                        break;
                    
                    case 'discount=desc':
                        $sort = "discount+desc";
                        Session::put('sort', $sort);
                        break;
                    
                    case 'popular=desc':
                        $sort = "total_series_score+desc%2Cproduct_scoring+desc";
                        Session::put('sort', $sort);
                        break;
                }
            }
            
            $offset = (Request::segment(2))?check_uri_segment('pagination'):0;
            $limit  = 48;
            
            $solrCoreProducts   = getCoreSelector("products");
            $solrCoreFet        = getCoreSelector("front_end_type");
            // $solrCoreBrand      = getCoreSelector("brand");
            $solrCorePd         = getCoreSelector("product_detail");

            if($gender == "women")
            {
                //check if keywords is category
                $query  = 'type_name_bahasa:"'.urlencode($keyword).'"';
                $rows   = 1;

                $fq_fet['-gender']      = "2";

                $cTojsoncategory     = get_active_solr($solrCoreFet, $query, $fq_fet, $rows, null, null, null);
                
                try{
                  $showDatacategory    = $cTojsoncategory->docs;
                  $totalcategory       = $cTojsoncategory->numFound;

                  if($totalcategory>0)
                  {
                      $query  = 'id:"'.$showDatacategory[0]->parent.'"';

                      $decodeParentCheck      = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                      $showDataParentCheck    = $decodeParentCheck->docs;
                      $totalParentCheck       = $decodeParentCheck->numFound;

                      if($totalParentCheck>0 && $showDatacategory[0]->parent !=0)
                      {
                          $search['url_terms'] = $showDataParentCheck[0]->type_url.'/'.$showDatacategory[0]->type_url.'/'.$gender.$ref_keyword;

                          $update_terms_search = Term::find($create_terms_search->id);
                          $update_terms_search->url = $search['url_terms'];
                          $update_terms_search->save();

                          return $search;
                      }
                      else
                      {
                          $search['url_terms'] = $showDatacategory[0]->type_url.'/'.$gender.$ref_keyword;

                          $update_terms_search = Term::find($create_terms_search->id);
                          $update_terms_search->url = $search['url_terms'];
                          $update_terms_search->save();

                          return $search;
                      }
                  }
                  else
                  {
                      $gender = "men";
                      //check if keywords is category
                      $fqs['-gender']      = "1";

                      $dataCategoryMen    = get_active_solr($solrCoreFet, $query, $fqs, $rows, null, null, null);
                      $showCategoryMen    = $dataCategoryMen->docs;
                      $totalCategoryMen   = $dataCategoryMen->numFound;

                      if($totalCategoryMen>0)
                      {
                          $query  = 'id:"'.$showDatacategory[0]->parent.'"';

                          $decodeParentMen    = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                          $showDataParentMen  = $decodeParentMen->docs;
                          $totalParentMen     = $decodeParentMen->numFound;

                          if($totalParentMen>0 && $showCategoryMen[0]->parent !=0)
                          {
                              $search['url_terms'] = $showDataParentMen[0]->type_url.'/'.$gender.$ref_keyword;

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                          }
                          else
                          {
                              $search['url_terms'] = $showCategoryMen[0]->type_url.'/'.$gender.$ref_keyword;

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                          }
                      }
                  }
                }catch (\Exception $ex) {
                  \Log::error($ex);
                  \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                }

                //check if keywords is brand
                // $query          = 'brand_url:"'.clean_teks_for_brand_url($keyword).'"';

                // $fq_brand['-product_gender']    = "2";
                // $fq_brand['product_status']     = 1;
                // $fq_brand["eksklusif_in_promo"] = 0;

                // $women          = get_active_solr($solrCoreProducts, $query, $fq_brand, 1000, null, null, null);
                
                // try{
                //   $getDataWomen   = $women->docs;
                //   $countDataWomen = $women->numFound;

                //   if($countDataWomen>0)
                //   {
                //       $gender     = "&gender=women";
                //       $brand_url  = $getDataWomen[0]->brand_url;

                //       $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //       $update_terms_search = Term::find($create_terms_search->id);
                //       $update_terms_search->url = $search['url_terms'];
                //       $update_terms_search->save();

                //       return $search;
                //   }
                //   else
                //   {
                //       $fq_brand_m['-product_gender']            = "1";
                //       $fq_brand_m['product_status']     = 1;
                //       $fq_brand_m["eksklusif_in_promo"] = 0;

                //       $men          = get_active_solr($solrCoreProducts, $query, $fq_brand_m, 1000, null, null, null);
                //       $getDataMen   = $men->docs;
                //       $countDataMen = $men->numFound;

                //       if($countDataMen>0)
                //       {
                //           $gender     = "&gender=men";
                //           $brand_url  = $getDataMen[0]->brand_url;

                //           $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //           $update_terms_search = Term::find($create_terms_search->id);
                //           $update_terms_search->url = $search['url_terms'];
                //           $update_terms_search->save();

                //           return $search;
                //       }
                //   }
                // }catch (\Exception $ex) {
                //   \Log::error($ex);
                //   \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                // }
            }
            elseif($gender == "men")
            {
                //check if keywords is category
                $query  = 'type_name_bahasa:"'.urlencode($keyword).'"';
                $rows   = 1;

                $fq_fet['-gender']      = "1";

                $cTojsoncategory     = get_active_solr($solrCoreFet, $query, $fq_fet, $rows, null, null, null);
                
                try{
                  $showDatacategory    = $cTojsoncategory->docs;
                  $totalcategory       = $cTojsoncategory->numFound;

                  if($totalcategory>0)
                  {
                      $query  = 'id:"'.$showDatacategory[0]->parent.'"';

                      $decodeParentCheck      = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                      $showDataParentCheck    = $decodeParentCheck->docs;
                      $totalParentCheck       = $decodeParentCheck->numFound;

                      if($totalParentCheck>0 && $showDatacategory[0]->parent !=0)
                      {
                          $search['url_terms'] = $showDataParentCheck[0]->type_url.'/'.$showDatacategory[0]->type_url.'/'.$gender.$ref_keyword;

                          $update_terms_search = Term::find($create_terms_search->id);
                          $update_terms_search->url = $search['url_terms'];
                          $update_terms_search->save();

                          return $search;
                      }
                      else
                      {
                          $search['url_terms'] = $showDatacategory[0]->type_url.'/'.$gender.$ref_keyword;

                          $update_terms_search = Term::find($create_terms_search->id);
                          $update_terms_search->url = $search['url_terms'];
                          $update_terms_search->save();

                          return $search;
                      }
                  }
                  else
                  {
                      $gender = "women";
                      //check if keywords is category
                      $fqs['-gender']      = "2";

                      $dataCategoryWomen    = get_active_solr($solrCoreFet, $query, $fqs, $rows, null, null, null);
                      $showCategoryWomen    = $dataCategoryWomen->docs;
                      $totalCategoryWomen   = $dataCategoryWomen->numFound;

                      if($totalCategoryWomen>0)
                      {
                          $query  = 'id:"'.$showCategoryWomen[0]->parent.'"';

                          $decodeParentWomen    = get_active_solr($solrCoreFet, $query, null, $rows, null, null, null);
                          $showDataParentWomen  = $decodeParentWomen->docs;
                          $totalParentWomen     = $decodeParentWomen->numFound;

                          if($totalParentWomen>0 && $showCategoryWomen[0]->parent !=0)
                          {
                              $search['url_terms'] = $showDataParentMen[0]->type_url.'/'.$gender.$ref_keyword;

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                          }
                          else
                          {
                              $search['url_terms'] = $showCategoryWomen[0]->type_url.'/'.$gender.$ref_keyword;

                              $update_terms_search = Term::find($create_terms_search->id);
                              $update_terms_search->url = $search['url_terms'];
                              $update_terms_search->save();

                              return $search;
                          }
                      }
                  }
                }catch (\Exception $ex) {
                  \Log::error($ex);
                  \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                }

                //check if keywords is brand
                // $query          = 'brand_url:"'.clean_teks_for_brand_url($keyword).'"';

                // $fq_brand['-product_gender']    = "1";
                // $fq_brand['product_status']     = 1;
                // $fq_brand["eksklusif_in_promo"] = 0;

                // $men          = get_active_solr($solrCoreProducts, $query, $fq_brand, 1000, null, null, null);
                
                // try{
                //   $getDataMen   = $men->docs;
                //   $countDataMen = $men->numFound;

                //   if($countDataMen>0)
                //   {
                //       $gender     = "&gender=women";
                //       $brand_url  = $getDataMen[0]->brand_url;

                //       $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //       $update_terms_search = Term::find($create_terms_search->id);
                //       $update_terms_search->url = $search['url_terms'];
                //       $update_terms_search->save();

                //       return $search;
                //   }
                //   else
                //   {
                //       $fq_brand_m['-product_gender']    = "2";
                //       $fq_brand_m['product_status']     = 1;
                //       $fq_brand_m["eksklusif_in_promo"] = 0;

                //       $women          = get_active_solr($solrCoreProducts, $query, $fq_brand_m, 1000, null, null, null);
                //       $getDataWomen   = $women->docs;
                //       $countDataWomen = $women->numFound;

                //       if($countDataWomen>0)
                //       {
                //           $gender     = "&gender=women";
                //           $brand_url  = $getDataWomen[0]->brand_url;

                //           $search['url_terms'] = 'brand/'.$brand_url.$ref_keyword.$gender;

                //           $update_terms_search = Term::find($create_terms_search->id);
                //           $update_terms_search->url = $search['url_terms'];
                //           $update_terms_search->save();

                //           return $search;
                //       }
                //   }
                // }catch (\Exception $ex) {
                //   \Log::error($ex);
                //   \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                // }
            }
            
            $keyword        = urlencode(str_replace(':','\:',$keyword));
            $query          = 'product_name:'.$keyword.'*+OR+product_name:"'.$keyword.'"+OR+product_sku:"'.$keyword.'"';
            // $query          = 'product_name:'.$keyword.'*+OR+product_name:"'.$keyword.'"+OR+product_sku:"'.$keyword.'"+OR+brand_name:"'.$keyword.'"';

            $start          = $offset;
            $rows           = $limit;
            $group          = "pid";
            
            $core_selector  = getCoreSelector("product_detail");

            $fqPd['product_status']       = 1;
            $fqPd["eksklusif_in_promo"]   = 0;

            $showDataProducts               = get_active_solr($core_selector, $query, $fqPd, $rows, $start, $sort, $group);
            
            try {
              $fetch_search_products          = $showDataProducts->docs;
              $count_fetch_search_products    = count(get_active_solr($core_selector, $query, $fqPd, 100000, 0, $sort, $group, "pid")->docs);
              $start_catalog                  = $showDataProducts->start;
            } catch (\Exception $e) {
              //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
            }
            
            if (is_null($count_fetch_search_products) )
            {
                $search['url_terms'] = '404';
                return $search;
            }

            $number = $count_fetch_search_products;

            /*GA Product Tracking - bb desktop & mobile*/
            if($domain_id == 1) 
            {
                $ga_list = array();
                
                if(isset($fetch_search_products) && !empty($fetch_search_products))
                {
                    $i=0;
                
                    foreach($fetch_search_products as $row)
                    {
                        $ga_list[$i]['id'] = (string) $row->pid;
                        $ga_list[$i]['name'] = (string) $row->product_name;
                        $ga_list[$i]['list'] = (string) $row->type_url;
                        $i++;
                    }
                
                    $data['ga_list'] = json_encode($ga_list); 
                }
            }
            /*-----------------------------------------*/
        }

        $data["products"]       = isset($fetch_search_products) ? $fetch_search_products : [];
        $data["total_products"] = isset($count_fetch_search_products) ? $count_fetch_search_products : 0 ;
        $data["start_catalog"]  = isset($start_catalog) ? $start_catalog : null;
        $data["limit"]          = isset($rows) ? $rows : null;
        $data["skeyword"]       = isset($skeyword) ? $skeyword : "";
        $data["title"]          = isset($keyword) ? ucfirst(urldecode($keyword)) : "";

        return $data;
    }
}
