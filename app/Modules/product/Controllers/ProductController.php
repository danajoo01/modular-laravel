<?php 

namespace App\Modules\Product\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Product\Models\Product;
use \App\Modules\Product\Models\Brand;
use \App\Modules\Product\Models\Tag;
use \App\Modules\Product\Models\Wishlist;
use \App\Modules\Product\Models\SpecialPage;

use Input;
use Request;
use Validatoor;
use Auth;

//use Illuminate\Http\Request;
// use Redis;
use Illuminate\Support\Facades\Redis;

class ProductController extends Controller {

	/**
	 * Display a listing of the resource product.
	 *
	 * @return Response
	 */
	public function index(){   

            if(isforbidRoute() === true){
                return redirect('/new-arrival');
            }
            
            // Start of code
            $time = microtime(true); // Gets microseconds
            //Define Domain and Channel
            $get_domain              = get_domain();                  
//Generate uri
            $generate_uri_segment    = generate_uri_segment();//bb_debug($generate_uri_segment);die;
            //Generate get uri
            $generate_uri_httpget    = generate_get_uri();
            //Get filter type name
            $filter_type_redis_name  = $this->get_filter_type_name();

            // Check redis connection
            $redis_connection   = Redis::connection();
            // Get filter type data
            $filter_type        = Redis::get($filter_type_redis_name);

            $status = 1;

            if (is_null($filter_type)) {
                $file = storage_path().'/app/'.$get_domain['domain_name'].'/catalog/'.$filter_type_redis_name.'.json';

                if (\File::exists($file)) {
                    $filter_type    = \Storage::get($get_domain['domain_name'].'/catalog/'.$filter_type_redis_name.'.json');
                } else {
                    $status         = 0;
                }
            }

            $filter_type    = (array) json_decode($filter_type);

            //Get Catalog            
            $solr_param         = Product::uri_to_solr($generate_uri_segment, $generate_uri_httpget, TRUE);

            //Banner Catalog
            $BannerCatalog      = getBannerCatalog($solr_param['limit'],$solr_param['offset']);      
            $bannerperPage      = isset($BannerCatalog['perPage']) ? $BannerCatalog['perPage'] : 0;
            $bannerLastCount    = isset($BannerCatalog['lastCount']) ? $BannerCatalog['lastCount'] : 0;
            if(is_array($BannerCatalog)){
                if($BannerCatalog['totalBanner'] > 0){ // jumlah banner catalog sesuai segment > 0
                    if($BannerCatalog['perPage'] > 0 ){  // jumlah banner di page aktif > 0
                        if($solr_param['offset'] > 0 && $BannerCatalog['perPage'] <= $solr_param['offset']){                         
                            $solr_param['offset']   = ($solr_param['offset'] - $bannerLastCount);
                            $solr_param['limit']    = ($solr_param['limit']  - $bannerperPage);
                        }else{
                            $solr_param['limit']    = $solr_param['limit'] - ($bannerperPage );                          
                        }                                                                                                                        
                    }else{
                        if($solr_param['offset'] > 0){
                            $solr_param['offset']   = ($solr_param['offset'] - $bannerLastCount);
                            //$solr_param['limit']    = ($solr_param['limit']  - $bannerLastCount);
                        }
                    }              
                }                
            }                   

            $size = null;
            $catalogs           = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);                          

            try {
                $catalog = $catalogs->docs;

                //insert banner to array catalog               
                if(isset($BannerCatalog['docs']) && !empty($BannerCatalog['docs'])){
                    $start              = isset($solr_param['offset']) ? $solr_param['offset'] : 0;
                    $limit              = isset($solr_param['limit'])  ? $solr_param['limit']  : 48;                                                

    //                \Log::notice('banner per page = '. $bannerperPage);
    //                \Log::notice('banner last count ='. $bannerLastCount); 
    //                \Log::notice('limit = '.$solr_param['limit']); 
    //                \Log::notice('start = '.$solr_param['offset']);
    //                \Log::notice('#########################################');


                    foreach($BannerCatalog['docs'] as $row){  
                        $display_number     = (isset($row->display_number) &&  $row->display_number > 0) ? $row->display_number : 1;                    
                        $BannerImg       = isset($row->image) ? $row->image : '';                                                                                    
                        $display_position  = ($display_number - $start) - $bannerLastCount -  1;                                      

                        #\Log::notice('banner pos '.$i.'=' . $display_position);


                        if($display_number >= $start && $display_number <= ($start + $limit) + $bannerperPage + $bannerLastCount){                                      
                            $BannerCatalogObj = new \stdClass();
                            $BannerCatalogObj->isBannerCatalog  = true;                        
                            $BannerCatalogObj->display_number   = $row->display_number;   


                            $BannerCatalogObj->landing_page_url         = isset($row->landing_page_url) ? $row->landing_page_url : 'Berrybenka';                         
                            $BannerCatalogObj->template_domain          = isset($row->template_domain) ? $row->template_domain : 1;                         
                            $BannerCatalogObj->template_title           = isset($row->template_title) ? $row->template_title : '';
                            $BannerCatalogObj->path_image               = isset($row->image) ? $BannerImg : '';                          
                            $BannerCatalogObj->image_banner_name        = isset($row->image_banner_name) ? $row->image_banner_name : '';
                            $BannerCatalogObj->landing_page_segment_1   = isset($row->landing_page_segment_1) ? $row->landing_page_segment_1 : '';
                            $BannerCatalogObj->landing_page_segment_2   = isset($row->landing_page_segment_2) ? $row->landing_page_segment_2 : '';
                            $BannerCatalogObj->landing_page_segment_3   = isset($row->landing_page_segment_3) ? $row->landing_page_segment_3 : '';
                            $BannerCatalogObj->landing_page_type        = isset($row->landing_page_type) ? $row->landing_page_type : '';
                            $BannerCatalogObj->landing_page_id          = isset($row->landing_page_id) ? $row->landing_page_id : '';
                            $BannerCatalogObj->landing_gender           = isset($row->landing_gender) ? $row->landing_gender : '';

                            $paramLanding                               = array();
                            $paramLanding['landing_page_type']          = isset($BannerCatalogObj->landing_page_type) ? $BannerCatalogObj->landing_page_type : '';
                            $paramLanding['landing_page_id']            = isset($BannerCatalogObj->landing_page_id) ? $BannerCatalogObj->landing_page_id : '';
                            $paramLanding['landing_page_url']           = isset($BannerCatalogObj->landing_page_url) ? $BannerCatalogObj->landing_page_url : '';
                            $paramLanding['landing_page_segment_1']     = isset($BannerCatalogObj->landing_page_segment_1) ? $BannerCatalogObj->landing_page_segment_1 : '';
                            $paramLanding['landing_page_segment_2']     = isset($BannerCatalogObj->landing_page_segment_2) ? $BannerCatalogObj->landing_page_segment_2 : '';
                            $paramLanding['landing_page_segment_3']     = isset($BannerCatalogObj->landing_page_segment_3) ? $BannerCatalogObj->landing_page_segment_3 : '';                                                
                            $paramLanding['landing_gender']             = isset($BannerCatalogObj->landing_gender) ? $BannerCatalogObj->landing_gender : '';                                                
                            $BannerCatalogObj->FullURLBanner            = generateLinkBanner($paramLanding);                                                                        

                            $BannerCatalogObj->text_1                   = isset($row->text_1) && $row->text_1 != '' ? $row->text_1 : '&nbsp;'; 
                            $BannerCatalogObj->text_2                   = isset($row->text_2) && $row->text_2 != '' ? $row->text_2 : '&nbsp;'; 
                            $BannerCatalogObj->text_3                   = isset($row->text_3) && $row->text_3 != '' ? $row->text_3 : '&nbsp;'; 

                            array_splice( $catalog, $display_position, 0, array ( $display_position => $BannerCatalogObj) );
                        }      

                    }                          
                }

              if(!stristr($solr_param['core_selector'], "product_detail") === FALSE){
                  $total_catalog = count(get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, 0, $solr_param['order'], $solr_param['group'], 'pid')->docs);
              } else {
                  $total_catalog = $catalogs->numFound;
              }

              $start_catalog  = $catalogs->start;

              $catalog_url    = $solr_param['order'];

              //Get Color
              $solr_param_color = Product::uri_color_to_solr($generate_uri_segment, $generate_uri_httpget);
              $color            = get_active_solr($solr_param_color['core_selector'], $solr_param_color['query'], $solr_param_color['where'], 100, null, $solr_param_color['order'], $solr_param_color['group'], $solr_param_color['field_list'])->docs;

              //Get Size
              $solr_param_size  = Product::uri_size_to_solr($generate_uri_segment, $generate_uri_httpget);
              $size             = get_active_solr($solr_param_size['core_selector'], $solr_param_size['query'], $solr_param_size['where'], 300, null, $solr_param_size['order'], $solr_param_size['group'], $solr_param_size['field_list'])->docs;

              //Get Brand
              $solr_param_brand = Product::uri_brand_to_solr($generate_uri_segment, $generate_uri_httpget);
              $brand            = get_active_solr($solr_param_brand['core_selector'], $solr_param_brand['query'], $solr_param_brand['where'], 10000, null, $solr_param_brand['order'], $solr_param_brand['group'], $solr_param_brand['field_list'])->docs;
            } catch (\Exception $e) {
              //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
            }

            // filter empty product_size_url
            if(is_array($size)){
                foreach ($size as $key => $value) {
                    if (!isset($value->product_size_url) || $value->product_size_url == "" ) {
                        unset($size[$key]);
                        // \Log::error('Empty product_size_url with PID : '.$value->pid);
                        // \Log::error('product_size_url error with with URI : ' . \Request::fullUrl());
                    }
                }
            }

            if(isset($generate_uri_segment['new'])) {
                $product_title 	= $generate_uri_segment['new'];
            } elseif(isset($generate_uri_segment['sale'])) {
                $product_title 	= $generate_uri_segment['sale'];
            } else {
                $product_title 	= $generate_uri_segment['child_type_url'];
            }

            /**** Check Wishlist ****/
            $user = Auth::user();
            if ($user) {
                    $where['customer_id'] = $user->customer_id;
                    $where['domain_id']   = $get_domain['domain_id'];
              $wishlist_user        = Wishlist::fetchWishlist($where);
            }
            //-----------------------------------------------------------------------------

            /*** Trancking sale, set get http track landing page to product detail ***/
            $page_ref = Product::trackingSale();
                $ref = explode('=', $page_ref['trc_sale']);

            //-----------------------------------------------------------------------------

            /*** UTM SOURCE ***/
            $utmCookie = Product::gaUtmz();
            //-----------------------------------------------------------------------------

            $product_title = ucwords(str_replace('-',' ',$product_title));
            //dd($wishlist_user);
            //bb_debug($get_domain);

            // Data call into view
            $data['category']       = isset($filter_type) ? $filter_type : null;
            $data['catalog']        = isset($catalog) ? $catalog : [];
            $data['catalog_url']    = isset($catalog_url) ? $catalog_url : '';
            $data['total_catalog']  = isset($total_catalog) ? $total_catalog : null;
            $data['start_catalog']  = isset($start_catalog) ? $start_catalog : null;
            $data['color']          = isset($color) ? $color : [];
            $data['size']           = isset($size) ? $size : [];
            $data['brand']          = isset($brand) ? $brand : [];
            $data['page_ref']       = isset($page_ref) ? $page_ref : [];
            $data['ref']            = isset($page_ref) ? $ref[1] : "Catalog";
            $data['status']         = isset($status) ? $status : null;
            $data['title']          = isset($product_title) ? $product_title : null;
            $data['wishlist_user']  = isset($wishlist_user["pid_data"]) ? $wishlist_user["pid_data"] : array();
            $data['gender']         = isset($generate_uri_segment['gender']) ? $generate_uri_segment['gender'] : '';
            $data['page_num']     	= isset($generate_uri_segment['pagination']) ? $generate_uri_segment['pagination'] : 0;
            $data['domain_alias']   = isset($get_domain["domain_alias"]) ? $get_domain["domain_alias"] : null;

            \Log::info('Time Elapsed Product Controller '.$product_title.': '.(microtime(true) - $time).'s');

            if(\Request::ajax()) {
                return response()->json($data);
            } else {
                return get_view('product', 'product.index', $data);
            }
	}

    // --------------------------------------------------------------------

    /**
     * Display a listing of the resource product promo.
     *
     * @return Response
     */
    public function promo()
    {
        //Define Domain and Channel
        $get_domain              = get_domain();
        //Generate uri
        $generate_uri_segment    = generate_uri_segment();
        //Generate get uri
        $generate_uri_httpget    = generate_get_uri();
        //Get Kategory
        $filter_type             = Product::get_left_menu_type_special();
        $filter_type             = (array) json_decode($filter_type);
        
        //***** FOR CHECK Special Page Active ****//
        $check_special = SpecialPage::checkSpecialPageActive($generate_uri_segment['special']);
        //***** END FOR CHECK Special Page Active ****//

        //Get Catalog
        $solr_param     = Product::uri_promo_to_solr($generate_uri_segment, $generate_uri_httpget);
        
        //Banner Catalog
        $BannerCatalog      = getBannerCatalog($solr_param['limit'],$solr_param['offset']);      
        $bannerperPage      = isset($BannerCatalog['perPage']) ? $BannerCatalog['perPage'] : 0;
        $bannerLastCount    = isset($BannerCatalog['lastCount']) ? $BannerCatalog['lastCount'] : 0;
        if(is_array($BannerCatalog)){
            if($BannerCatalog['totalBanner'] > 0){ // jumlah banner catalog sesuai segment > 0
                if($BannerCatalog['perPage'] > 0 ){  // jumlah banner di page aktif > 0
                    if($solr_param['offset'] > 0 && $BannerCatalog['perPage'] <= $solr_param['offset']){                         
                        $solr_param['offset']   = ($solr_param['offset'] - $bannerLastCount);
                        $solr_param['limit']    = ($solr_param['limit']  - $bannerperPage);
                    }else{
                        $solr_param['limit']    = $solr_param['limit'] - ($bannerperPage );                          
                    }                                                                                                                        
                }else{
                    if($solr_param['offset'] > 0){
                        $solr_param['offset']   = ($solr_param['offset'] - $bannerLastCount);
                        $solr_param['limit']    = ($solr_param['limit']  - $bannerLastCount);
                    }
                }              
            }                
        } 
        
//        \Log::notice('banner per page = '. $bannerperPage);
//        \Log::notice('banner last count ='. $bannerLastCount); 
//        \Log::notice('limit = '.$solr_param['offset']); 
//        \Log::notice('start = '.$solr_param['limit']);
//        \Log::notice('#########################################');        

        $size = null;
        $catalogs       = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);
        
        try {
            $catalog = $catalogs->docs;
          
            //insert banner to array catalog               
            if(isset($BannerCatalog['docs']) && !empty($BannerCatalog['docs'])){
                $start              = isset($solr_param['offset']) ? $solr_param['offset'] : 0;
                $limit              = isset($solr_param['limit'])  ? $solr_param['limit']  : 48;                                                

                            


                foreach($BannerCatalog['docs'] as $row){  
                    $display_number     = (isset($row->display_number) &&  $row->display_number > 0) ? $row->display_number : 1;                    
                    $BannerImg       = isset($row->image) ? $row->image : '';                                                                                    
                    $display_position  = ($display_number - $start) - $bannerLastCount -  1;                                      

                    #\Log::notice('banner pos '.$i.'=' . $display_position);


                    if($display_number >= $start && $display_number <= ($start + $limit) + $bannerperPage + $bannerLastCount){                                      
                        $BannerCatalogObj = new \stdClass();
                        $BannerCatalogObj->isBannerCatalog  = true;                        
                        $BannerCatalogObj->display_number   = $row->display_number;   


                        $BannerCatalogObj->landing_page_url = isset($row->landing_page_url) ? $row->landing_page_url : 'Berrybenka';                         
                        $BannerCatalogObj->template_domain  = isset($row->template_domain) ? $row->template_domain : 1;                         

                        switch($BannerCatalogObj->template_domain){
                            case 1 : 
                                $folderimg = 'berrybenka';
                                break;
                            case 2 :
                                $folderimg = 'hijabenka';
                                break;
                            case 3 :
                                $folderimg = 'shopdeca';
                                break;
                            default :
                                $folderimg = 'berrybenka';
                        }

                        $BannerCatalogObj->path_image               = isset($row->image) ? 'https://img.berrybenka.biz/assets/upload/catalog-banner/'.$folderimg.'/'.$BannerImg : '';                          
                        $BannerCatalogObj->landing_page_segment_1   = isset($row->landing_page_segment_1) ? $row->landing_page_segment_1 : '';
                        $BannerCatalogObj->landing_page_segment_2   = isset($row->landing_page_segment_2) ? $row->landing_page_segment_2 : '';
                        $BannerCatalogObj->landing_page_segment_3   = isset($row->landing_page_segment_3) ? $row->landing_page_segment_3 : '';
                        $BannerCatalogObj->landing_page_type        = isset($row->landing_page_type) ? $row->landing_page_type : '';
                        $BannerCatalogObj->landing_page_id          = isset($row->landing_page_id) ? $row->landing_page_id : '';

                        $paramLanding                               = array();
                        $paramLanding['landing_page_type']          = isset($BannerCatalogObj->landing_page_type) ? $BannerCatalogObj->landing_page_type : '';
                        $paramLanding['landing_page_id']            = isset($BannerCatalogObj->landing_page_id) ? $BannerCatalogObj->landing_page_id : '';
                        $paramLanding['landing_page_url']           = isset($BannerCatalogObj->landing_page_url) ? $BannerCatalogObj->landing_page_url : '';
                        $paramLanding['landing_page_segment_1']     = isset($BannerCatalogObj->landing_page_segment_1) ? $BannerCatalogObj->landing_page_segment_1 : '';
                        $paramLanding['landing_page_segment_2']     = isset($BannerCatalogObj->landing_page_segment_2) ? $BannerCatalogObj->landing_page_segment_2 : '';
                        $paramLanding['landing_page_segment_3']     = isset($BannerCatalogObj->landing_page_segment_3) ? $BannerCatalogObj->landing_page_segment_3 : '';                                                
                        $BannerCatalogObj->FullURLBanner            = generateLinkBanner($paramLanding);                                                                        

                        $BannerCatalogObj->text_1                   = isset($row->text_1) ? $row->text_1 : ''; 
                        $BannerCatalogObj->text_2                   = isset($row->text_2) ? $row->text_2 : ''; 
                        $BannerCatalogObj->text_3                   = isset($row->text_3) ? $row->text_3 : ''; 

                        array_splice( $catalog, $display_position, 0, array ( $display_position => $BannerCatalogObj) );
                    }      

                }                          
            }
          
          if(!stristr($solr_param['core_selector'], "product_detail") === FALSE){
              $total_catalog = count(get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, 0, $solr_param['order'], $solr_param['group'], 'pid')->docs);
          } else {
              $total_catalog = $catalogs->numFound;
          }
          
          $start_catalog  = $catalogs->start;
          
          //Get Gender
          $solr_param     = Product::uri_promo_gender_to_solr($generate_uri_segment, $generate_uri_httpget);
          $gender         = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, null, $solr_param['order'], $solr_param['group'], $solr_param['field_list'])->docs;

          //Get Brand
          $solr_param     = Product::uri_promo_brand_to_solr($generate_uri_segment, $generate_uri_httpget);
          $brand          = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, null, $solr_param['order'], $solr_param['group'], $solr_param['field_list'])->docs;

          //Get Color
          $solr_param     = Product::uri_promo_color_to_solr($generate_uri_segment, $generate_uri_httpget);
          $color          = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 100, null, $solr_param['order'], $solr_param['group'], $solr_param['field_list'])->docs;

          //Get Size
          $solr_param     = Product::uri_promo_size_to_solr($generate_uri_segment, $generate_uri_httpget);
          $size           = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 100, null, $solr_param['order'], $solr_param['group'], $solr_param['field_list'])->docs;

          $product_title = ucwords(str_replace('-',' ',$generate_uri_segment['special_name']));
        } catch (\Exception $e) {
          //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
        }
        // $total_catalog  = $catalogs->numFound;

        // filter empty product_size_url
        if(is_array($size)){
            foreach ($size as $key => $value) {
                if (!isset($value->product_size_url) || $value->product_size_url == "" ) {
                    unset($size[$key]);
                    // \Log::error('Empty product_size_url with PID : '.$value->pid);
                    // \Log::error('product_size_url error with with URI : ' . \Request::fullUrl());
                }
            }
        }

        /*** UTM SOURCE ***/

        $utmCookie = Product::gaUtmz();

        //-----------------------------------------------------------------------------

        /* s: PROSES TOP BANNER */
        $top_banner_mini = Product::get_top_banner_mini();
        /* e: PROSES TOP BANNER */

        /* s: PROSES TOP BANNER */
        $fetch_special_image = SpecialPage::fetch_special_page_image($generate_uri_segment['special']);
        /* e: PROSES TOP BANNER */
        
        // Data call into view
        $data['category']           = isset($filter_type) ? $filter_type : null;
        $data['catalog']            = isset($catalog) ? $catalog : [];
        $data['total_catalog']      = isset($total_catalog) ? $total_catalog : null;
        $data['start_catalog']      = isset($start_catalog) ? $start_catalog : null;
        $data['color']              = isset($color) ? $color : [];
        $data['size']               = isset($size) ? $size : [];
        $data['brand']              = isset($brand) ? $brand : [];
        $data['status']             = isset($status) ? $status : null;
        $data['title']              = isset($product_title) ? $product_title : null;
        $data['gender']             = isset($generate_uri_httpget['gender']) ? $generate_uri_httpget['gender'] : '';
        $data['page_num']           = isset($generate_uri_httpget['page']) ? $generate_uri_httpget['page'] : 0;
        $data['domain_alias']       = isset($get_domain["domain_alias"]) ? $get_domain["domain_alias"] : null;
        $data['top_banner_mini']    = isset($top_banner_mini) ? $top_banner_mini : null;
        $data['special']            = isset($check_special) ? $check_special : null;
        $data['special_image']      = isset($fetch_special_image) ? $fetch_special_image : null;

        if(\Request::ajax()) {
            return response()->json($data);
        } else {
            return get_view('product', 'product.promo', $data);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Display a listing of the resource product brand.
     *
     * @return Response
     */
    public function brand()
    {
      //Define Domain and Channel
      $get_domain = get_domain();
      
      //Generate uri
      $generate_uri_segment = generate_uri_segment(); //bb_debug($generate_uri_segment);die;
      
      //Generate get uri
      $generate_uri_httpget = generate_get_uri();

      //Get filter type name
      $get_filter_type_category = $this->get_filter_type_category();

      // Check redis connection
      $redis_connection = Redis::connection();
      
      // Get filter type datas
      $filter_type = Redis::get($get_filter_type_category);

      $status = 1;

      if (is_null($filter_type)) {
        $file = storage_path() . '/app/' . $get_domain['domain_name'] . '/brand/' . $get_filter_type_category . '.json';

        if (\File::exists($file)) {
          $filter_type = \Storage::get($get_domain['domain_name'] . '/brand/' . $get_filter_type_category . '.json');
        } else {
          $get_brand = Brand::where('brand_url', '=', $generate_uri_segment['brand_url'])->first();

          if (empty($get_brand)) {
            \Log::info("get_brand");
            abort(404);
          }

          $filter_type = $get_brand->brand_type_menu;
          $status = 0;
        }
      }

      $filter_type = (array) json_decode($filter_type);

      //Get Catalog
      $solr_param = Product::get_brand_to_solr($generate_uri_segment, $generate_uri_httpget);

      //Banner Catalog
      $BannerCatalog = getBannerCatalog($solr_param['limit'], $solr_param['offset']);
      $bannerperPage = isset($BannerCatalog['perPage']) ? $BannerCatalog['perPage'] : 0;
      $bannerLastCount = isset($BannerCatalog['lastCount']) ? $BannerCatalog['lastCount'] : 0;
      if (is_array($BannerCatalog)) {
        if ($BannerCatalog['totalBanner'] > 0) { // jumlah banner catalog sesuai segment > 0
          if ($BannerCatalog['perPage'] > 0) {  // jumlah banner di page aktif > 0
            if ($solr_param['offset'] > 0 && $BannerCatalog['perPage'] <= $solr_param['offset']) {
              $solr_param['offset'] = ($solr_param['offset'] - $bannerLastCount);
              $solr_param['limit'] = ($solr_param['limit'] - $bannerperPage);
            } else {
              $solr_param['limit'] = $solr_param['limit'] - ($bannerperPage );
            }
          } else {
            if ($solr_param['offset'] > 0) {
              $solr_param['offset'] = ($solr_param['offset'] - $bannerLastCount);
              $solr_param['limit'] = ($solr_param['limit'] - $bannerLastCount);
            }
          }
        }
      }

      $size = null;
      $catalogs = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);

      try {
        $catalog = $catalogs->docs;
        
        //insert banner to array catalog               
        if (isset($BannerCatalog['docs']) && !empty($BannerCatalog['docs'])) {
          $start = isset($solr_param['offset']) ? $solr_param['offset'] : 0;
          $limit = isset($solr_param['limit']) ? $solr_param['limit'] : 48;


          foreach ($BannerCatalog['docs'] as $row) {
            $display_number = (isset($row->display_number) && $row->display_number > 0) ? $row->display_number : 1;
            $BannerImg = isset($row->image) ? $row->image : '';
            $display_position = ($display_number - $start) - $bannerLastCount - 1;

            #\Log::notice('banner pos '.$i.'=' . $display_position);


            if ($display_number >= $start && $display_number <= ($start + $limit) + $bannerperPage + $bannerLastCount) {
              $BannerCatalogObj = new \stdClass();
              $BannerCatalogObj->isBannerCatalog = true;
              $BannerCatalogObj->display_number = $row->display_number;


              $BannerCatalogObj->landing_page_url = isset($row->landing_page_url) ? $row->landing_page_url : 'Berrybenka';
              $BannerCatalogObj->template_domain = isset($row->template_domain) ? $row->template_domain : 1;

              switch ($BannerCatalogObj->template_domain) {
                case 1 :
                  $folderimg = 'berrybenka';
                  break;
                case 2 :
                  $folderimg = 'hijabenka';
                  break;
                case 3 :
                  $folderimg = 'shopdeca';
                  break;
                default :
                  $folderimg = 'berrybenka';
              }

              $BannerCatalogObj->path_image = isset($row->image) ? 'https://img.berrybenka.biz/assets/upload/catalog-banner/' . $folderimg . '/' . $BannerImg : '';
              $BannerCatalogObj->landing_page_segment_1 = isset($row->landing_page_segment_1) ? $row->landing_page_segment_1 : '';
              $BannerCatalogObj->landing_page_segment_2 = isset($row->landing_page_segment_2) ? $row->landing_page_segment_2 : '';
              $BannerCatalogObj->landing_page_segment_3 = isset($row->landing_page_segment_3) ? $row->landing_page_segment_3 : '';
              $BannerCatalogObj->landing_page_type = isset($row->landing_page_type) ? $row->landing_page_type : '';
              $BannerCatalogObj->landing_page_id = isset($row->landing_page_id) ? $row->landing_page_id : '';

              $paramLanding = array();
              $paramLanding['landing_page_type'] = isset($BannerCatalogObj->landing_page_type) ? $BannerCatalogObj->landing_page_type : '';
              $paramLanding['landing_page_id'] = isset($BannerCatalogObj->landing_page_id) ? $BannerCatalogObj->landing_page_id : '';
              $paramLanding['landing_page_url'] = isset($BannerCatalogObj->landing_page_url) ? $BannerCatalogObj->landing_page_url : '';
              $paramLanding['landing_page_segment_1'] = isset($BannerCatalogObj->landing_page_segment_1) ? $BannerCatalogObj->landing_page_segment_1 : '';
              $paramLanding['landing_page_segment_2'] = isset($BannerCatalogObj->landing_page_segment_2) ? $BannerCatalogObj->landing_page_segment_2 : '';
              $paramLanding['landing_page_segment_3'] = isset($BannerCatalogObj->landing_page_segment_3) ? $BannerCatalogObj->landing_page_segment_3 : '';
              $BannerCatalogObj->FullURLBanner = generateLinkBanner($paramLanding);

              $BannerCatalogObj->text_1 = isset($row->text_1) ? $row->text_1 : '';
              $BannerCatalogObj->text_2 = isset($row->text_2) ? $row->text_2 : '';
              $BannerCatalogObj->text_3 = isset($row->text_3) ? $row->text_3 : '';

              array_splice($catalog, $display_position, 0, array($display_position => $BannerCatalogObj));
            }
          }
        }

        if (!stristr($solr_param['core_selector'], "product_detail") === FALSE) {
          $total_catalog = count(get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, 0, $solr_param['order'], $solr_param['group'], 'pid')->docs);
        } else {
          $total_catalog = $catalogs->numFound;
        }

        $start_catalog = $catalogs->start;

        //Get Gender
        $solr_param_gender = Product::get_brand_gender_to_solr($generate_uri_segment, $generate_uri_httpget);
        $gender = get_active_solr($solr_param_gender['core_selector'], $solr_param_gender['query'], $solr_param_gender['where'], 10000, null, $solr_param_gender['order'], $solr_param_gender['group'], $solr_param_gender['field_list'])->docs;

        //Get Color
        $solr_param_color = Product::get_brand_color_to_solr($generate_uri_segment, $generate_uri_httpget);
        $color = get_active_solr($solr_param_color['core_selector'], $solr_param_color['query'], $solr_param_color['where'], 100, null, $solr_param_color['order'], $solr_param_color['group'], $solr_param_color['field_list'])->docs;

        //Get Size
        $solr_param_size = Product::get_brand_size_to_solr($generate_uri_segment, $generate_uri_httpget);
        $size = get_active_solr($solr_param_size['core_selector'], $solr_param_size['query'], $solr_param_size['where'], 100, null, $solr_param_size['order'], $solr_param_size['group'], $solr_param_size['field_list'])->docs;
      } catch (\Exception $e) {
        //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
      }
      /*       * * UTM SOURCE ** */
      $utmCookie = Product::gaUtmz();
      //-----------------------------------------------------------------------------

      // filter empty product_size_url
      if(is_array($size)){
          foreach ($size as $key => $value) {
            if (!isset($value->product_size_url) || $value->product_size_url == "" ) {
                unset($size[$key]);
                // \Log::error('Empty product_size_url with PID : '.$value->pid);
                // \Log::error('product_size_url error with with URI : ' . \Request::fullUrl());
            }
          }
      }  

      $data['category'] = isset($filter_type) ? $filter_type : null;
      $data['catalog'] = isset($catalog) ? $catalog : [];
      $data['total_catalog'] = isset($total_catalog) ? $total_catalog : null;
      $data['start_catalog'] = isset($start_catalog) ? $start_catalog : null;
      $data['color'] = isset($color) ? $color : [];
      $data['size'] = isset($size) ? $size : [];
      $data['gender'] = isset($gender) ? $gender : [];
      //$data['status']         = isset($status) ? $status : null;
      $data['title'] = isset($generate_uri_segment['brand_url']) ? str_replace("-", " ", $generate_uri_segment['brand_url']) : null;
      //$data['gender']         = isset($generate_uri_segment['gender']) ? $generate_uri_segment['gender'] : '';
      $data['get'] = isset($generate_uri_httpget) ? $generate_uri_httpget : '';
      $data['domain_alias'] = isset($get_domain["domain_alias"]) ? $get_domain["domain_alias"] : null;

      if (\Request::ajax()) {
        return response()->json($data);
      } else {
        return get_view('product', 'product.brand', $data);
      }
    }

    // --------------------------------------------------------------------

    /**
    * Display tags list.
    *
    * @return Response
    */
    
    public function TagsList(){                                
        $taglist        = Tag::TagsList();      
        $data           = [];
        foreach ($taglist as $keydata => $valuedata) {
            $data['all_data'][$valuedata['category']][] = $valuedata;
        }           
        return get_view('product', 'tag.index', $data);        
    }                
    // -------------------------------------------------------------------- 
        
        
    /**
     * Display a listing of the resource product tag.
     *
     * @return Response
     */
    public function tag()
    {
      //Define Domain and Channel
      $get_domain = get_domain();
      //Generate uri
      $generate_uri_segment = generate_uri_segment(); //bb_debug($generate_uri_segment);//die;
      //Generate get uri
      $generate_uri_httpget = generate_get_uri(); //bb_debug($generate_uri_httpget);//die;
      //Get filter type name
      $get_filter_type_category = $this->get_filter_type_category();

      // Check redis connection
      $redis_connection = Redis::connection();
      // Get filter type data
      $filter_type = Redis::get($get_filter_type_category);

      $status = 1;

      if (is_null($filter_type)) {
        $file = storage_path() . '/app/' . $get_domain['domain_name'] . '/tag/' . $get_filter_type_category . '.json';

        if (\File::exists($file)) {
          $filter_type = \Storage::get($get_domain['domain_name'] . '/tag/' . $get_filter_type_category . '.json');
        } else {
          $get_tag = Tag::where('tag_url', '=', $generate_uri_segment['tag'])->first();

          if (empty($get_tag)) {
            \Log::info("get_tag empty");
            abort(404);
          }
          $filter_type = $get_tag->tag_type_menu;
          $status = 0;
        }
      }
      $filter_type = (array) json_decode($filter_type);
      // bb_debug($filter_type);
      //Get Catalog
      $solr_param = Product::get_tag_to_solr($generate_uri_segment, $generate_uri_httpget);

      //Banner Catalog
      $BannerCatalog = getBannerCatalog($solr_param['limit'], $solr_param['offset']);
      $bannerperPage = isset($BannerCatalog['perPage']) ? $BannerCatalog['perPage'] : 0;
      $bannerLastCount = isset($BannerCatalog['lastCount']) ? $BannerCatalog['lastCount'] : 0;
      if (is_array($BannerCatalog)) {
        if ($BannerCatalog['totalBanner'] > 0) { // jumlah banner catalog sesuai segment > 0
          if ($BannerCatalog['perPage'] > 0) {  // jumlah banner di page aktif > 0
            if ($solr_param['offset'] > 0 && $BannerCatalog['perPage'] <= $solr_param['offset']) {
              $solr_param['offset'] = ($solr_param['offset'] - $bannerLastCount);
              $solr_param['limit'] = ($solr_param['limit'] - $bannerperPage);
            } else {
              $solr_param['limit'] = $solr_param['limit'] - ($bannerperPage );
            }
          } else {
            if ($solr_param['offset'] > 0) {
              $solr_param['offset'] = ($solr_param['offset'] - $bannerLastCount);
              $solr_param['limit'] = ($solr_param['limit'] - $bannerLastCount);
            }
          }
        }
      }

      $size = null;
      $catalogs = get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);

      try {
        $catalog = $catalogs->docs;
        // $total_catalog  = $catalogs->numFound;
        //insert banner to array catalog               
        if (isset($BannerCatalog['docs']) && !empty($BannerCatalog['docs'])) {
          $start = isset($solr_param['offset']) ? $solr_param['offset'] : 0;
          $limit = isset($solr_param['limit']) ? $solr_param['limit'] : 48;

          foreach ($BannerCatalog['docs'] as $row) {
            $display_number = (isset($row->display_number) && $row->display_number > 0) ? $row->display_number : 1;
            $BannerImg = isset($row->image) ? $row->image : '';
            $display_position = ($display_number - $start) - $bannerLastCount - 1;

            #\Log::notice('banner pos '.$i.'=' . $display_position);


            if ($display_number >= $start && $display_number <= ($start + $limit) + $bannerperPage + $bannerLastCount) {
              $BannerCatalogObj = new \stdClass();
              $BannerCatalogObj->isBannerCatalog = true;
              $BannerCatalogObj->display_number = $row->display_number;


              $BannerCatalogObj->landing_page_url = isset($row->landing_page_url) ? $row->landing_page_url : 'Berrybenka';
              $BannerCatalogObj->template_domain = isset($row->template_domain) ? $row->template_domain : 1;

              switch ($BannerCatalogObj->template_domain) {
                case 1 :
                  $folderimg = 'berrybenka';
                  break;
                case 2 :
                  $folderimg = 'hijabenka';
                  break;
                case 3 :
                  $folderimg = 'shopdeca';
                  break;
                default :
                  $folderimg = 'berrybenka';
              }

              $BannerCatalogObj->path_image = isset($row->image) ? 'https://img.berrybenka.biz/assets/upload/catalog-banner/' . $folderimg . '/' . $BannerImg : '';
              $BannerCatalogObj->landing_page_segment_1 = isset($row->landing_page_segment_1) ? $row->landing_page_segment_1 : '';
              $BannerCatalogObj->landing_page_segment_2 = isset($row->landing_page_segment_2) ? $row->landing_page_segment_2 : '';
              $BannerCatalogObj->landing_page_segment_3 = isset($row->landing_page_segment_3) ? $row->landing_page_segment_3 : '';
              $BannerCatalogObj->landing_page_type = isset($row->landing_page_type) ? $row->landing_page_type : '';
              $BannerCatalogObj->landing_page_id = isset($row->landing_page_id) ? $row->landing_page_id : '';

              $paramLanding = array();
              $paramLanding['landing_page_type'] = isset($BannerCatalogObj->landing_page_type) ? $BannerCatalogObj->landing_page_type : '';
              $paramLanding['landing_page_id'] = isset($BannerCatalogObj->landing_page_id) ? $BannerCatalogObj->landing_page_id : '';
              $paramLanding['landing_page_url'] = isset($BannerCatalogObj->landing_page_url) ? $BannerCatalogObj->landing_page_url : '';
              $paramLanding['landing_page_segment_1'] = isset($BannerCatalogObj->landing_page_segment_1) ? $BannerCatalogObj->landing_page_segment_1 : '';
              $paramLanding['landing_page_segment_2'] = isset($BannerCatalogObj->landing_page_segment_2) ? $BannerCatalogObj->landing_page_segment_2 : '';
              $paramLanding['landing_page_segment_3'] = isset($BannerCatalogObj->landing_page_segment_3) ? $BannerCatalogObj->landing_page_segment_3 : '';
              $BannerCatalogObj->FullURLBanner = generateLinkBanner($paramLanding);

              $BannerCatalogObj->text_1 = isset($row->text_1) ? $row->text_1 : '';
              $BannerCatalogObj->text_2 = isset($row->text_2) ? $row->text_2 : '';
              $BannerCatalogObj->text_3 = isset($row->text_3) ? $row->text_3 : '';

              array_splice($catalog, $display_position, 0, array($display_position => $BannerCatalogObj));
            }
          }
        }

        if (!stristr($solr_param['core_selector'], "product_detail") === FALSE) {
          $total_catalog = count(get_active_solr($solr_param['core_selector'], $solr_param['query'], $solr_param['where'], 10000, 0, $solr_param['order'], $solr_param['group'], 'pid')->docs);
        } else {
          $total_catalog = $catalogs->numFound;
        }

        $start_catalog = $catalogs->start;

        //Get Gender
        $solr_param_gender = Product::get_tag_gender_to_solr($generate_uri_segment, $generate_uri_httpget);
        $gender = get_active_solr($solr_param_gender['core_selector'], $solr_param_gender['query'], $solr_param_gender['where'], 10000, null, $solr_param_gender['order'], $solr_param_gender['group'], $solr_param_gender['field_list'])->docs;

        //Get Brand
        $solr_param_brand = Product::get_tag_brand_to_solr($generate_uri_segment, $generate_uri_httpget);
        $brand = get_active_solr($solr_param_brand['core_selector'], $solr_param_brand['query'], $solr_param_brand['where'], 10000, null, $solr_param_brand['order'], $solr_param_brand['group'], $solr_param_brand['field_list'])->docs;

        //Get Color
        $solr_param_color = Product::get_tag_color_to_solr($generate_uri_segment, $generate_uri_httpget);
        $color = get_active_solr($solr_param_color['core_selector'], $solr_param_color['query'], $solr_param_color['where'], 100, null, $solr_param_color['order'], $solr_param_color['group'], $solr_param_color['field_list'])->docs;

        //Get Size
        $solr_param_size = Product::get_tag_size_to_solr($generate_uri_segment, $generate_uri_httpget);
        $size = get_active_solr($solr_param_size['core_selector'], $solr_param_size['query'], $solr_param_size['where'], 100, null, $solr_param_size['order'], $solr_param_size['group'], $solr_param_size['field_list'])->docs;
      } catch (\Exception $e) {
        //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
      }

      // filter empty product_size_url
      if(is_array($size)){
          foreach ($size as $key => $value) {
            if (!isset($value->product_size_url) || $value->product_size_url == "" ) {
                unset($size[$key]);
                // \Log::error('Empty product_size_url with PID : '.$value->pid);
                // \Log::error('product_size_url error with with URI : ' . \Request::fullUrl());
            }
          }
      }

      /*       * * UTM SOURCE ** */
      $utmCookie = Product::gaUtmz();
      //-----------------------------------------------------------------------------

      $data['category'] = isset($filter_type) ? $filter_type : null;
      $data['catalog'] = isset($catalog) ? $catalog : [];
      $data['total_catalog'] = isset($total_catalog) ? $total_catalog : null;
      $data['start_catalog'] = isset($start_catalog) ? $start_catalog : null;
      $data['color'] = isset($color) ? $color : [];
      $data['brand'] = isset($brand) ? $brand : [];
      $data['size'] = isset($size) ? $size : [];
      $data['gender'] = isset($gender) ? $gender : [];
      //$data['status']         = isset($status) ? $status : null;
      $data['title'] = isset($generate_uri_segment['tag']) ? $generate_uri_segment['tag'] : '';
      $data['get'] = isset($generate_uri_httpget) ? $generate_uri_httpget : '';
      $data['domain_alias'] = isset($get_domain["domain_alias"]) ? $get_domain["domain_alias"] : null;

      if (\Request::ajax()) {
        return response()->json($data);
      } else {
        return get_view('product', 'product.tag', $data);
      }
    }

    // --------------------------------------------------------------------

    /**
     * Get file name based on URI
     *
     * @return Response redis key/file name
     */
    private function get_filter_type_name()
    {
        //Define Domain and Channel
        $get_domain           = get_domain();
        //Generate uri
        $generate_uri_segment = generate_uri_segment();
        //Generate get uri
        $generate_uri_httpget = generate_get_uri();

        if ($generate_uri_segment['gender'] == null) {
            $gender = null;
        } else {
            $gender = '-'.$generate_uri_segment['gender'];
        }

        $filter_type_redis  = $generate_uri_segment['parent_type_url'].$gender.'-'.$get_domain['domain_alias'];
        if ($generate_uri_segment['new'] == 'new-arrival' || $generate_uri_segment['sale'] == 'sale') {
            $filter_type_redis  = 'menu'.$gender.'-'.$get_domain['domain_alias'];
            if($generate_uri_segment['sale'] == 'sale'){
                $filter_type_redis  = 'menu-sale'.$gender.'-'.$get_domain['domain_alias'];    
            }
        }

        return $filter_type_redis;
    }

    // --------------------------------------------------------------------

	/**
     * Get file name based on URI
     *
     * @return Response redis key/file name
     */
    private function get_filter_type_category()
    {
      //Define Domain and Channel
      $get_domain           = get_domain();
      //Generate uri
      $generate_uri_segment = generate_uri_segment();
      //Generate get uri
      $generate_uri_httpget = generate_get_uri();

      $gender = isset($generate_uri_httpget['gender']) ? '-'.$generate_uri_httpget['gender'] : '';

      //$filter_type_redis  = $generate_uri_segment['parent_type_url'].$gender.'-'.$get_domain['domain_alias'];

      try{
        if (! empty($generate_uri_segment['brand_url'])) {
          $brand_url = $generate_uri_segment['brand_url'];
          $filter_type_redis  = 'menu-brand-'.$brand_url.$gender.'-'.$get_domain['domain_alias'];
        }
        if (! empty($generate_uri_segment['tag'])) {
          $tag = $generate_uri_segment['tag'];
          $filter_type_redis  = 'menu-tag-'.$tag.$gender.'-'.$get_domain['domain_alias'];
        }
      }catch (\Exception $ex){
        \Log::error('get_filter_type_category ProductController : '.json_encode($generate_uri_segment).' with URI : ' . \Request::fullUrl());       
      }

      return $filter_type_redis;
    }

    // --------------------------------------------------------------------

}
