<?php 

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\ProductVariant;
use \App\Modules\Product\Models\SolrSync;
use \App\Http\Controllers\Requests;
use \App\Customer;
use DB;
use Cart;
use Request;

// use Redis;
use Illuminate\Support\Facades\Redis;

class Product extends Model {

  /**
   * The database table used by the model.
   *
   * @var string
   */
  protected $table = 'products'; //Define your table name

  protected $primaryKey = 'product_id'; //Define your primarykey

  public $timestamps = false; //Define yout timestamps

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = ['product_id']; //Define your guarded columns

  /*
  * Define your relationship with other model
  */
  // public function relation_name()
  // {
  //  return $this->belongsTo('App\Modules\Product\Models\Model_name');
  // }

  /**
>>>>>>> clear_comment_mark
  *** Fetch Data Product Detail
  *** get data detail product, product variant, product images form SOLR
  *** @return array with object values  
  **/
  public static function fetch_product_detail($id, $fields = array()) {

            $get_domain = get_domain();
            $core_selector = getCoreSelector("product_detail");

            switch ($get_domain['domain_id']) {
                case '1':
                    $default = "default_bb";
                    $default_image = "default_image";
                    break;
                case '2':
                    $default = "default_hb";
                    $default_image = "default_image";
                    break;
                case '3':
                    $default = "default_sd";
                    $default_image = "default_image";
                    break;
            }

            if (!isset($id) || $id == '' || empty($id)) {
                \Log::alert('Product ID is not found on Product Detail on URL : ' . \Request::fullUrl());
            }

            $where['pid'] = $id;
            $where_image['pid'] = $id;
            
            //****** GET DATA SOLR PRODUCT DETAIL BY VARIANT ID
            try {
                $productStatus = get_active_solr($core_selector, $query = null, $where, $limit = 1, $offset = null, $order = null, $group = null)->docs;
                if (isset($productStatus[0]->product_status) && $productStatus[0]->product_status === 1) {
                    $where['product_status'] = 1;                
                }

                $productSolr = get_active_solr($core_selector, $query = null, $where, $limit = 50, $offset = null, $order = null, $group = null)->docs;
                $AllProductVariant = $productSolr;                                
                
                //**************************************************
                if (empty($productSolr)) {
                    //return view('errors.404');
                    //\Log::critical('Product Detail record not found for pid '.$id.' with URI '.\Request::url().'');
                    abort(404);
                }
                
                //*** Product Detail Info
                $fetch_product = $productSolr[0];                
                $fetch_product->product_special_price = $fetch_product->special_price;

                /*$attributes = $fetch_product->product_attribute;
                $attribute = explode(",", $attributes);*/
                $fetch_product->product_recommended = (isset($fetch_product->product_gender) && $fetch_product->product_gender == "2" && isset($fetch_product->product_recommended_men)) ? @$fetch_product->product_recommended_men : @$fetch_product->product_recommended;

                $url_set = explode(',', trim($fetch_product->url_set));
                $front_type = explode(',', trim($fetch_product->front_end_type));

                $fetch_product->type_id = end($url_set);
                $fetch_product->parent_type_id = prev($url_set);

                $type_name = end($front_type);
                $fetch_product->type_id_real = ($type_name == "")?prev($front_type):$type_name;
                $fetch_product->parent_type_id_real = prev($front_type);

                //***************************************************

                    //*** Default Product Variant
                $product_vari = search_in_array($AllProductVariant, $default, 1);
                $fetch_product_vari = array_shift($product_vari);
                //***************************************************

                if (empty($fetch_product_vari)) {
                    $fetch_product_vari_off = $AllProductVariant;
                } else {
                    //*** Others Product Variant
                    $fetch_product_vari_off = search_not_in_array($AllProductVariant, "product_sku", $fetch_product_vari->product_sku);
                    //***************************************************
                }

                //*** If product variant default empty
                if (empty($fetch_product_vari) && $fetch_product_vari_off) {
                  $fetch_product_vari = array_shift($fetch_product_vari_off);

                  //*** Automatic Update Product Variant
                  $product_variant = ProductVariant::where('SKU','=',$fetch_product_vari->product_sku)->first();
                  if ($product_variant) {
                    $product_variant->$default = 1;
                    if ($product_variant->save()) {
                      $data_email['url'] = Request::url();
                      $data_email['email'] = 'developer@berrybenka.com';
                      $data_email['domain_name'] = $get_domain["domain_name"];
                      $data_email['referer'] = isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] <> '' ? $_SERVER['HTTP_REFERER'] : 'direct';
                      $do_send_mail = Customer::MN_send_forgot_product_default($data_email);
                      // redirect($current_url);

                      $solr_item[$default]    = true;
                      $solr_item['sku']       = $fetch_product_vari->product_sku;
                      $solr_item['pid']       = $fetch_product_vari->pid;

                      $updateSolr = SolrSync::updateSolr($solr_item);
                    }
                  }
                }
                //******************************************

                $variant_color_id_exc = $fetch_product_vari->variant_color_id;

                //*** Get Size Default
                $fetch_product_size = search_in_array($AllProductVariant,"variant_color_id",$variant_color_id_exc);
                //********************************************

                //****** GET DATA SOLR PRODUCT DETAIL IMAGE
                $core_selector      = getCoreSelector("product_images");
                $productimagesSolr  = get_active_solr($core_selector, $query = null, $where_image, $limit = 100, $offset = null, $order = null, $group = null)->docs;
                $AllProductImage    = $productimagesSolr;                                
                //***************************************************

                //*** Product Images
                $fetch_product_image = search_in_array($AllProductImage,"variant_color_id",$variant_color_id_exc);
                //$fetch_product_image_all_off = array_group($AllProductImage, "variant_color_id");

                $fetch_product_img_def = search_in_array($fetch_product_image,$default_image,1);
                $fetch_product_image_def = array_shift($fetch_product_img_def);

                if (empty($fetch_product_image_def)) {
                  $fetch_product_image_def = array_shift($fetch_product_image);
                }
                //***************************************************

                //*** Product Color
                $fetch_product_color = array($fetch_product_image_def);
                $fetch_product_color_others = search_not_in_array($AllProductImage,"variant_color_id",$variant_color_id_exc);
                $fetch_product_color_zero = search_in_array($fetch_product_color_others,$default_image,1);
                //***************************************************
            } catch (\Exception $e) {
              //\Log::error($e);
              //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
              abort(404);
            }
    
            //bb_debug($fetch_product);
            $data['fetch_product']                  = isset($fetch_product)?$fetch_product:NULL;
            $data['fetch_product_vari']             = isset($fetch_product_vari)?$fetch_product_vari:NULL;
            $data['fetch_product_vari_off']         = isset($fetch_product_vari_off)?$fetch_product_vari_off:NULL;
            $data['fetch_product_size']             = isset($fetch_product_size)?$fetch_product_size:NULL;
            $data['fetch_product_image']            = isset($fetch_product_image)?$fetch_product_image:NULL;
            $data['fetch_product_image_def']        = isset($fetch_product_image_def)?$fetch_product_image_def:NULL;
            $data['fetch_product_image_all_off']    = isset($fetch_product_image_all_off)?$fetch_product_image_all_off:NULL;
            $data['fetch_product_color']            = isset($fetch_product_color)?$fetch_product_color:NULL;
            $data['fetch_product_color_others']     = isset($fetch_product_color_others)?$fetch_product_color_others:NULL;
            $data['fetch_product_color_zero']       = isset($fetch_product_color_zero)?$fetch_product_color_zero:NULL;

            return $data;
  }
  // --------------------------------------------------------------------

  public static function fetchProduct($data = NULL)
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];

    $fetch_product = DB::table('products')
      ->select(DB::connection('read_mysql')->raw('products.*, brand.brand_name'))
      ->join('brand', 'brand.brand_id', '=', 'products.product_brand');

    if($domain_id == 1){
      $fetch_product->where('own_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_product->where('own_hb', '=', 1);
    }else{
      $fetch_product->where('own_sd', '=', 1);
    }

    if(isset($data['where']['id'])){
      $fetch_product->where('product_id', '=', $data['where']['id']);
    }

    return $fetch_product->get();
  }

  public static function fetchProductType($data = NULL)
  {
    $fetch_product_type = DB::table('front_end_type')
      ->select(DB::connection('read_mysql')->raw('*'));

    $fetch_product_type->where('enabled', '=', 1);
    if(isset($data['where']['type_id'])){
      $fetch_product_type->where('type_id', '=', $data['where']['type_id']);
    }

    return $fetch_product_type->get();
  }

  public static function fetchProductOwnership($data = NULL)
  {
    $fetch_product_ownership = DB::table('product_ownership')
      ->select(DB::connection('read_mysql')->raw('*'));

    if(isset($data['where']['id'])){
      $fetch_product_ownership->where('product_ownership_id', '=', $data['where']['id']);
    }

    return $fetch_product_ownership->get();
  }

  public static function fetchProductImage($data = NULL)
  {
    $fetch_product_image = DB::table('product_image')
      ->select(DB::connection('read_mysql')->raw('*'));

    if(isset($data['where']['domain_id'])){
      if($data['where']['domain_id'] == 1){
        $fetch_product_image->where('default_bb', '=', 1);
      }elseif($data['where']['domain_id'] == 2){
        $fetch_product_image->where('default_hb', '=', 1);
      }else{
        $fetch_product_image->where('default_sd', '=', 1);
      }
    }

    if(isset($data['where']['product_id'])){
      $fetch_product_image->where('product_id', '=', $data['where']['product_id']);
    }

    if(isset($data['where']['variant_color_id'])){
      $fetch_product_image->where('variant_color_id', '=', $data['where']['variant_color_id']);
    }

    return $fetch_product_image->get();
  }

  public static function fetchProductVariant($data = NULL)
  {
    $fetch_product_variant = DB::table('product_variant')
      ->select(DB::connection('read_mysql')->raw('*'))
      ->join('inventory', 'inventory.SKU', '=', 'product_variant.SKU')
      ->where('product_variant.status', '=', 1);

    if(isset($data['where']['SKU'])){
      $fetch_product_variant->where('product_variant.SKU', '=', $data['where']['SKU']);
    }

    return $fetch_product_variant->get();
  }
  
  public static function fetchProductOldType($data = NULL)
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    
    if($domain_id == 1){
      $parent = 'product_parent_type_bb';
    }elseif($domain_id == 2){
      $parent = 'product_parent_type_hb';
    }else{
      $parent = 'product_parent_type_sd';
    }
    
    $fetch_product_type = DB::table('products')
      ->select(DB::connection('read_mysql')->raw('type.type_id, type.type_name'))
      ->join('type', 'type.type_id', '=', 'products.'.$parent)
      ->where('products.product_id', '=', $data['product_id']);

    return $fetch_product_type->first();
  }

  /**
  *** Fetch Data Product Recommended
  *** get data product recommended form SOLR with product core
  *** @return array with object values
  **/
  public static function fetch_product_recommended($query=NULL, $where=NULL, $rows = 6) {
    $get_domain     = get_domain();
    $core_selector  = getCoreSelector("products");
    
    $product = array();
    
    try {
      $productSolr        = get_active_solr($core_selector, $query, $where, $limit = 50, $offset = null, $order = null, $group = null);
      $productrecommended = $productSolr->docs;

      if (empty($productrecommended)) {
        return false;
      }

      if ($productSolr->numFound > $rows) {
        $random_keys = array_rand($productrecommended,$rows);
        foreach ($random_keys as $key) {
          $product[] = $productrecommended[$key];
        }
      } else {
        $product = $productrecommended;
      }
    } catch (\Exception $e) {
      //\Log::error($e);
      //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
    }

    return $product;
  }

  public static function fetch_product_related($related){
    $related = trim($related, ',');
    $related = '(' . str_replace(',', ' OR ', $related) . ')';
    $related = urlencode($related);

    $core_selector  = getCoreSelector("products");

    $fq_arr['pid'] = $related;

    $data = get_active_solr(
      (isset($core_selector) ? $core_selector : null), 
      "*:*", 
      (isset($fq_arr) ? $fq_arr : null), 
      null, 
      null, 
      null, 
      null, 
      null
    );

    return $data->docs;
  }

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_to_solr($data, $data_http_get, $param=FALSE)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $show           = 48;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;
        $field_list     = null;

        $get_domain     = get_domain();
        
        $core_selector  = getCoreSelector("products");
        
        switch ($get_domain['domain_id']) {
            case '1':
                $launch_date        = 'launch_date_bb+desc';
                $launch_date_txt    = 'launch_date_bb';
                break;
            case '2':
                $launch_date        = 'launch_date_hb+desc';
                $launch_date_txt    = 'launch_date_hb';
                break;
            case '3':
                $launch_date        = 'launch_date_sd+desc';
                $launch_date_txt    = 'launch_date_sd';
                break;
        }        

        $query = "*:*";

        if ( isset($data['gender']) && ! is_null($data['gender'])) {
            $product_gender             = $data['gender'] == "women" ? 2 : 1;
            $filter_query               .= '&fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data['new']) && ! is_null($data['new'])) {
            $product_new            = $data['new'] == "new-arrival" ? 1 : 0;
            $filter_query           .= '&fq=new_arrival:"'.$product_new.'"';
            $fq_arr['new_arrival']  = $product_new;
            // $sort                   = $launch_date;
            // jika new arrival ganti jadi default popular
            $sort = 'total_series_score+desc%2Cproduct_scoring+desc';
        }

        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            $query = urlencode("product_sale_price:[3000 TO *]");
            //$sort   = 'product_scoring+desc';
            $sort   = 'sale_date+desc';
        }

        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            if($get_domain['domain_id'] == 1){
              $filter_query           .= "eksklusif_bb:(true+OR+false)";
            }else if ($get_domain['domain_id'] == 2){
              $filter_query           .= "eksklusif_hb:(true+OR+false)";
            }
        }else{
            if($get_domain['domain_id'] == 1){
              $filter_query           .= "eksklusif_bb:(false)";
            }else if ($get_domain['domain_id'] == 2){
              $filter_query           .= "eksklusif_hb:(false)";
            }
        }

        if ( isset($data['child_type_url']) && ! is_null($data['child_type_url'])) {
            //get front end type id
            $core_selector_search               = getCoreSelector("front_end_type");      
            $fq_arr_search['type_url_search']   = $data['child_type_url'];
            
            if(!$param){
              try{
                  $front_end_type_id = get_active_solr($core_selector_search, '*:*', $fq_arr_search, 1, 0, NULL, NULL, NULL);                                 
                  if (isset($front_end_type_id->docs[0]->id)) {
                      $fq_arr['front_end_type']   = urlencode('('.$front_end_type_id->docs[0]->id.',* OR *,'.$front_end_type_id->docs[0]->id.',* OR *,'.$front_end_type_id->docs[0]->id.')');
                  }
              } catch (Exception $ex) {

              } 
            }
                                               
            $filter_query .= '&fq=url_set:"'.$data['child_type_url'].'"';
            $fq_arr['url_set']   = $data['child_type_url'];
        }

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            if(empty($all_brand_url)){
              abort(404);
            }
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data['color_name']) && ! is_null($data['color_name'])) {
            $all_color_name = explode('--',$data['color_name']);
            if(empty($all_color_name)){
              abort(404);
            }
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            
            $core_selector  = getCoreSelector("product_detail");

            $group                   = 'pid';
        }

        if ( isset($data['product_size_url']) && ! is_null($data['product_size_url'])) {
            $all_size_name = explode('-',$data['product_size_url']);
            if(empty($all_size_name)){
              abort(404);
            }
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
            
            $core_selector  = getCoreSelector("product_detail");

            $group                       = 'pid';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            $start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = 'total_series_score+'.$data_http_get['popular'].'%2Cproduct_scoring+'.$data_http_get['popular'].'';
            }
                        
            if (array_key_exists('pn', $data_http_get)) {
                $sort = $launch_date_txt.'+'.$data_http_get['pn'].'';
            }

            if (array_key_exists('price', $data_http_get)) {
                $sort = 'real_price+'.$data_http_get['price'].'';
                if (isset($data['sale']) && ! is_null($data['sale'])) {
                    $sort = 'product_sale_price+'.$data_http_get['price'].'';
                }
            }
            if (array_key_exists('discount', $data_http_get)) {
                $sort = 'discount+'.$data_http_get['discount'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                $sprice = $data_http_get['sprice'];
                if(isset($sprice) && !is_array($sprice) && strtolower($sprice) != 'all' && strpos($sprice, '-') !== false){
                    $priceuri = urldecode($sprice);
                    $arrayprice = explode("|", $priceuri);
                    $query_price = '';

                    if(count($arrayprice) > 1){
                      foreach ($arrayprice as $sp) {
                        $filter_price = explode('-',$sp);

                        if(empty($filter_price) || count($filter_price) < 2){
                          abort(404);
                        }
                        $min_price = $filter_price[0]*1000;

                        if($filter_price[1] === 'above'){
                            $max_price = '*';
                        }else{
                            $max_price = $filter_price[1]*1000;
                        }

                        if (isset($data['sale']) && !is_null($data['sale'])) {
                            $query_price .= urlencode("product_sale_price:[".$min_price." TO ".$max_price."] OR ");
                        } else {
                            $query_price .= urlencode("real_price:[".$min_price." TO ".$max_price."] OR ");
                        } 

                      }

                      $query_price = urldecode($query_price);

                      $query_price = substr($query_price, 0, -4);

                      $query_price =  urlencode($query_price);

                      $fq_arr['sprice'] = '&fq=' . $query_price;
                    }else{
                      $filter_price = explode('-',$sprice);
                      if(empty($filter_price) || count($filter_price) < 2){
                        abort(404);
                      }
                      $min_price = $filter_price[0]*1000;
                      if($filter_price[1] === 'above'){
                          $max_price = '*';
                      }else{
                          $max_price = $filter_price[1]*1000;
                      }


                      if (isset($data['sale']) && !is_null($data['sale'])) {
                          $fq_arr['sprice'] = '&fq='.urlencode("product_sale_price:[".$min_price." TO ".$max_price."]");
                      } else {
                          $fq_arr['sprice'] = '&fq='.urlencode("real_price:[".$min_price." TO ".$max_price."]");
                      } 
                    }
                } 
            }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'total_series_score+desc%2Cproduct_scoring+desc';
                // $sort = $launch_date_txt.'+desc';
            }

        }


        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            if($get_domain['domain_id'] == 1){
              $fq_arr['eksklusif_bb'] = urlencode('(true OR false)');
            }else if ($get_domain['domain_id'] == 2){
              $fq_arr['eksklusif_hb'] = urlencode('(true OR false)');
            }
        }else{
            if($get_domain['domain_id'] == 1){
              $fq_arr['eksklusif_bb'] = urlencode('(false)');
            }else if ($get_domain['domain_id'] == 2){
              $fq_arr['eksklusif_hb'] = urlencode('(false)');
            }
        }


        if($sort == "") {
          // $sort = $launch_date_txt.'+desc';
        	$sort = "total_series_score+desc%2Cproduct_scoring+desc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }

        $fq_arr['eksklusif_in_promo'] = 0;
        
        $display_oos = CheckOOS();
        if($display_oos === FALSE){
          $fq_arr['product_status']     = 1;  
        }
        //$fq_arr['product_status']     = 1; comment this biar muncul status = 2

        // print_r($sort);die;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = isset($group) ? $group : null;
        $data['field_list']     = isset($field_list) ? $field_list : null;

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_color_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 20;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");
        $query = "*:*";

        if ( isset($data['gender']) && ! is_null($data['gender'])) {
            $product_gender             = $data['gender'] == "women" ? 2 : 1;
            $filter_query               .= '&fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data['new']) && ! is_null($data['new'])) {
            $product_new            = $data['new'] == "new-arrival" ? 1 : 0;
            $filter_query           .= '&fq=new_arrival:"'.$product_new.'"';
            $fq_arr['new_arrival']  = $product_new;
        }

        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            $query = urlencode("product_sale_price:[3000 TO *]");
        }

        if ( isset($data['child_type_url']) && ! is_null($data['child_type_url'])) {
            $filter_query .= '&fq=url_set:"'.$data['child_type_url'].'"';
            $fq_arr['url_set']   = $data['child_type_url'];
        }

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data['product_size_url']) && ! is_null($data['product_size_url'])) {

            $all_size_name = explode('-',$data['product_size_url']);
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            $start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        /*
        * Sorting and pagination from GET Method 
        * $data_http_get['_'] is for ajax response existant 
        */
        if ( isset($data_http_get) && is_array($data_http_get) && !array_key_exists("_", $data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }

        } else {

            if ($sort != null) {
                $sort = $sort;
            } else {
                $sort = 'color_name+asc';
            }

        }
        if($sort == "") {
          $sort = "color_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }

        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'color_name';
        $data['field_list']     = 'color_name,color_hex';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_size_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 100;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $core_selector  = getCoreSelector('product_detail');
        switch ($get_domain['domain_id']) {
          case '1':
            $launch_date    = 'launch_date_bb+desc';
            break;
          case '2':
            $launch_date    = 'launch_date_hb+desc';
            break;
          case '3':
            $launch_date    = 'launch_date_sd+desc';
            break;
        }

        $query = "*:*";

        if ( isset($data['gender']) && ! is_null($data['gender'])) {
            $product_gender             = $data['gender'] == "women" ? 2 : 1;
            $filter_query               .= '&fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data['new']) && ! is_null($data['new'])) {
            $product_new            = $data['new'] == "new-arrival" ? 1 : 0;
            $filter_query           .= '&fq=new_arrival:"'.$product_new.'"';
            $fq_arr['new_arrival']  = $product_new;
        }

        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            $query = urlencode("product_sale_price:[3000 TO *]");
        }

        if ( isset($data['child_type_url']) && ! is_null($data['child_type_url'])) {
            $filter_query .= '&fq=url_set:"'.$data['child_type_url'].'"';
            $fq_arr['url_set']   = $data['child_type_url'];
        }

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data['color_name']) && ! is_null($data['color_name'])) {
            $all_color_name = explode('--',$data['color_name']);
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            $group                   = 'pid';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            $start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'product_size_url+asc';
            }

        }
        if($sort == "") {
          $sort = "product_size_url+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'product_size_url';
        $data['field_list']     = 'product_size,product_size_url,pid';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_brand_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 5000;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");
        switch ($get_domain['domain_id']) {
          case '1':
            $launch_date    = 'launch_date_bb+desc';
            break;
          case '2':
            $launch_date    = 'launch_date_hb+desc';
            break;
          case '3':
            $launch_date    = 'launch_date_sd+desc';
            break;
        }

        $query = "*:*";

        if ( isset($data['gender']) && ! is_null($data['gender'])) {
            $product_gender             = $data['gender'] == "women" ? 2 : 1;
            $filter_query               .= '&fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data['new']) && ! is_null($data['new'])) {
            $product_new            = $data['new'] == "new-arrival" ? 1 : 0;
            $filter_query           .= '&fq=new_arrival:"'.$product_new.'"';
            $fq_arr['new_arrival']  = $product_new;
        }

        if ( isset($data['sale']) && ! is_null($data['sale'])) {
            $query = urlencode("product_sale_price:[3000 TO *]");
        }

        if ( isset($data['child_type_url']) && ! is_null($data['child_type_url'])) {
            $filter_query .= '&fq=url_set:"'.$data['child_type_url'].'"';
            $fq_arr['url_set']   = $data['child_type_url'];
        }

        if ( isset($data['color_name']) && ! is_null($data['color_name'])) {
            $all_color_name = explode('--',$data['color_name']);
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            $group                   = 'pid';
        }

        if ( isset($data['product_size_url']) && ! is_null($data['product_size_url'])) {
            $all_size_name = explode('-',$data['product_size_url']);
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
            $group                       = 'pid';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            $start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'brand_name+asc';
            }

        }
        if($sort == "") {
          $sort = "brand_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'brand_url_full';
        $data['field_list']     = 'brand_name,brand_url,product_gender';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_promo_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 48;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;
        $field_list     = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("special_page");

        $query = "*:*";

        if ( isset($data['special']) && ! is_null($data['special'])) {
            //$product_gender             = $data['special'] == "women" ? 2 : 1;
            //$filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['special_page']     = $data['special'];
        }

        if ( isset($data_http_get['gender']) && ! is_null($data_http_get['gender'])) {
            $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data_http_get['cat']) && ! is_null($data_http_get['cat'])) {
            $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
            $fq_arr['url_set']   = $data_http_get['cat'];
            
            $core_selector  = getCoreSelector("products_special");
        }

        if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
            $all_brand_url = explode('--',$data_http_get['brand']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
            
            $core_selector  = getCoreSelector("products_special");
        }

        if ( isset($data_http_get['color']) && ! is_null($data_http_get['color'])) {
            $all_color_name = explode('--',$data_http_get['color']);
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            
            $core_selector  = getCoreSelector("product_special_variant");

            $group                   = 'pid';
        }

        if ( isset($data_http_get['size']) && ! is_null($data_http_get['size'])) {
            $all_size_name = explode('-',$data_http_get['size']);
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
            
            $core_selector  = getCoreSelector("product_special_variant");

            $group                       = 'pid';
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price', 'recommended'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = 'total_series_score+'.$data_http_get['popular'].'%2Cproduct_scoring+'.$data_http_get['popular'].'';
            }
            
            if (array_key_exists('pn', $data_http_get)) {
                $sort = 'sale_date+'.$data_http_get['pn'].'';
            }

            if (array_key_exists('price', $data_http_get)) {
                $sort = 'real_price+'.$data_http_get['price'].'';
                if (isset($data['sale']) && ! is_null($data['sale'])) {
                    $sort = 'product_sale_price+'.$data_http_get['price'].'';
                }
            }
            if (array_key_exists('discount', $data_http_get)) {
                $sort = 'discount+'.$data_http_get['discount'].'';
            }
            
            if (array_key_exists('recommended', $data_http_get)) {
                $sort = 'special_order+'.$data_http_get['recommended'].'';
            }

            if (array_key_exists('sprice', $data_http_get)) {
                $sprice = $data_http_get['sprice'];
                if(isset($sprice) && !is_array($sprice) && strtolower($sprice) != 'all' && strpos($sprice, '-') !== false){
                    $priceuri = urldecode($sprice);
                    $arrayprice = explode("|", $priceuri);
                    $query_price = '';

                    if(count($arrayprice) > 1){
                      foreach ($arrayprice as $sp) {
                        $filter_price = explode('-',$sp);

                        if(empty($filter_price) || count($filter_price) < 2){
                          abort(404);
                        }
                        $min_price = $filter_price[0]*1000;

                        if($filter_price[1] === 'above'){
                            $max_price = '*';
                        }else{
                            $max_price = $filter_price[1]*1000;
                        }

                        if (isset($data['sale']) && !is_null($data['sale'])) {
                            $query_price .= urlencode("product_sale_price:[".$min_price." TO ".$max_price."] OR ");
                        } else {
                            $query_price .= urlencode("real_price:[".$min_price." TO ".$max_price."] OR ");
                        } 

                      }

                      $query_price = urldecode($query_price);

                      $query_price = substr($query_price, 0, -4);

                      $query_price =  urlencode($query_price);

                      $fq_arr['sprice'] = '&fq=' . $query_price;
                    }else{
                      $filter_price = explode('-',$sprice);
                      if(empty($filter_price) || count($filter_price) < 2){
                        abort(404);
                      }
                      $min_price = $filter_price[0]*1000;
                      if($filter_price[1] === 'above'){
                          $max_price = '*';
                      }else{
                          $max_price = $filter_price[1]*1000;
                      }


                      if (isset($data['sale']) && !is_null($data['sale'])) {
                          $fq_arr['sprice'] = '&fq='.urlencode("product_sale_price:[".$min_price." TO ".$max_price."]");
                      } else {
                          $fq_arr['sprice'] = '&fq='.urlencode("real_price:[".$min_price." TO ".$max_price."]");
                      } 
                    }

                    $core_selector  = getCoreSelector("products_special");
                } 
            }
            
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if ( isset($data_http_get['page']) && ! is_null($data_http_get['page'])) {
                $start = $data_http_get['page'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'special_order+asc';
                 // $sort = 'sale_date+desc';
            }

        }
        if($sort == "") {
          $sort = "special_order+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }

        // $sort = 'special_order+asc';              
        //$fq_arr['product_status']     = 1;
        if(isset($data['special'])){
            $displayOOSPromo = CheckOOSPromo($data['special']);
            if($displayOOSPromo === FALSE){
                $fq_arr['product_status']     = 1;
            }
        }    
        

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = isset($group) ? $group : null;
        $data['field_list']     = isset($field_list) ? $field_list : null;

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_promo_brand_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 5000;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_special_variant");
        
        $query = "*:*";

        if ( isset($data['special']) && ! is_null($data['special'])) {
            //$product_gender             = $data['special'] == "women" ? 2 : 1;
            //$filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['special_page']     = $data['special'];
        }

        if ( isset($data_http_get['gender']) && ! is_null($data_http_get['gender'])) {
            $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data_http_get['cat']) && ! is_null($data_http_get['cat'])) {
            $filter_query       .= '&fq=url_set:"'.$data_http_get['cat'].'"';
            $fq_arr['url_set']   = $data_http_get['cat'];
        }

        if ( isset($data_http_get['color']) && ! is_null($data_http_get['color'])) {
            $all_color_name = explode('--',$data_http_get['color']);
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            //$group                   = 'pid';
        }

        if ( isset($data_http_get['size']) && ! is_null($data_http_get['size'])) {
            $all_size_name = explode('-',$data_http_get['size']);
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
            //$group                       = 'pid';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'brand_name+asc';
            }

        }
        if($sort == "") {
          $sort = "brand_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        //$fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'brand_url_full';
        $data['field_list']     = 'brand_name,brand_url,product_gender';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_promo_color_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 20;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_special_variant");
        
        $query = "*:*";

        if ( isset($data['special']) && ! is_null($data['special'])) {
            //$product_gender             = $data['special'] == "women" ? 2 : 1;
            //$filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['special_page']     = $data['special'];
        }

        if ( isset($data_http_get['gender']) && ! is_null($data_http_get['gender'])) {
            $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data_http_get['cat']) && ! is_null($data_http_get['cat'])) {
            $filter_query       .= '&fq=url_set:"'.$data_http_get['cat'].'"';
            $fq_arr['url_set']   = $data_http_get['cat'];
        }

        if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
            $all_brand_url = explode('--',$data_http_get['brand']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data_http_get['size']) && ! is_null($data_http_get['size'])) {

            $all_size_name = explode('-',$data_http_get['size']);
            foreach ($all_size_name as $key => $value) {
                    $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'color_name+asc';
            }

        }
        if($sort == "") {
          $sort = "color_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        //$fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'color_name';
        $data['field_list']     = 'color_name,color_hex';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_promo_size_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 100;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_special_variant");
        
        $query = "*:*";

        if ( isset($data['special']) && ! is_null($data['special'])) {
            //$product_gender             = $data['special'] == "women" ? 2 : 1;
            //$filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['special_page']     = $data['special'];
        }

        if ( isset($data_http_get['gender']) && ! is_null($data_http_get['gender'])) {
            $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        if ( isset($data_http_get['cat']) && ! is_null($data_http_get['cat'])) {
            $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
            $fq_arr['url_set']   = $data_http_get['cat'];
        }

        if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
            $all_brand_url = explode('--',$data_http_get['brand']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data_http_get['color']) && ! is_null($data_http_get['color'])) {
            $all_color_name = explode('--',$data_http_get['color']);
            foreach ($all_color_name as $key => $value) {
                    $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            //$group                   = 'pid';
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'product_size_url+asc';
            }

        }
        if($sort == "") {
          $sort = "product_size_url+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        //$fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'product_size_url';
        $data['field_list']     = 'product_size,product_size_url,pid';

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function uri_promo_gender_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 1000;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("products_special");
        
        $query = "*:*";

        if ( isset($data['special']) && ! is_null($data['special'])) {
            $product_gender             = $data['special'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['special_page']     = $data['special'];
        }

        if ( isset($data_http_get['gender']) && ! is_null($data_http_get['gender'])) {
            $product_gender             = $data['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
        }

        // if ( isset($data_http_get['cat']) && ! is_null($data_http_get['cat'])) {
        //     $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        //     $fq_arr['url_set']   = $data_http_get['cat'];
        // }

        // if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
        //     $all_brand_url = explode('--',$data_http_get['brand']);
        //     foreach ($all_brand_url as $key => $value) {
        //             $list_brand_url[] = '"'.$value.'"';
        //     }
        //     $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
        //     $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        // }

        // if ( isset($data_http_get['color']) && ! is_null($data_http_get['color'])) {
        //     $all_color_name = explode('--',$data_http_get['color']);
        //     foreach ($all_color_name as $key => $value) {
        //             $list_color_name[] = '"'.$value.'"';
        //     }
        //     $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
        //     $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
        //     //$core_selector           = 'product_detail';
        //     //$group                   = 'pid';
        // }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = "pid+desc";
            }

        }
        if($sort == "") {
          $sort = "pid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        //$fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = $group;
        $data['field_list']     = 'product_gender';

        return $data;
    }

    // --------------------------------------------------------------------

   /**
   * Set and Display a listing of the parent type that has a child with Redis Key.
   *
   * Redis key - [parent-men,parent-women]
     * @berrybenka.com/clothing -> untuk generate file json kategory based on type clothing[parent]
   *
   * @return Array with json value
   */
    public static function parent_type($gen = null)
    {
        //Define Domain and Channel
        $get_domain     = get_domain();
        $core_selector  = getCoreSelector('front_end_type');

        //$genders = [1];
        if ($gen != null) {
             $genders = [$gen];
        } else {
             $genders = [1];
        }

        foreach ($genders as $gender) {

            if ($gender == 1) {
                $file_gender = 'men';
            } elseif ($gender == 2) {
                $file_gender = 'women';
            } elseif ($gender == 3) {
                $file_gender = 'all';
            }

            $hold_n2 = null;
            $hold_parent = null;

            $where['parent']    = 0;
            if ($file_gender != 'all') {
                $where['-gender']   = $gender;
            }
            
            try {
              $solr_front_end_type = get_active_solr($core_selector, $query = null, $where, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
              if (! empty($solr_front_end_type)) {
                  foreach ($solr_front_end_type as $key => $value) {
                      $key      = $value->type_url;
                      $type_url = $value->type_url;
                      $id       = $value->id;

                      $where_n1['parent'] = $id;
                      if ($file_gender != 'all') {
                          $where_n1['-gender']   = $gender;
                      }
                      $solr_front_end_type_n1 = get_active_solr($core_selector, $query = null, $where_n1, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                      if (! empty($solr_front_end_type_n1)) {
                          foreach ($solr_front_end_type_n1 as $key_n1 => $value_n1) {
                              $type_url_n2  = $value_n1->type_url;
                              $id_n2        = $value_n1->id;
                              $hold_n1[]    = $type_url_n2;

                              $where_n2['parent']     = $id_n2;
                              if ($file_gender != 'all') {
                                  $where_n2['-gender']    = $gender;
                              }
                              $solr_front_end_type_n2 = get_active_solr($core_selector, $query = null, $where_n2, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                              $hold_n3 = null;
                              if (! empty($solr_front_end_type_n2)) {
                                  foreach ($solr_front_end_type_n2 as $key_n2 => $value_n2) {
                                      $type_url_n3  = $value_n2->type_url;
                                      $id_n3        = $value_n2->id;
                                      $hold_n3[]    = $type_url_n3;
                                      $save_hold_n3 = $hold_n3;
                                  }

                                  $hold_n2[$type_url_n2] = $hold_n3;
                                  $hold_parent[] = $type_url_n2;
                                  unset($hold_n3);
                              }
                              else {
                                  if (! empty($save_hold_n3)) {
                                      $hold_n2[$type_url_n2] = null;
                                  } else {
                                      $hold_n2[] = $type_url_n2;
                                  }

                              }
                          }
                      }
                      $hold_parent[] = $type_url;
                  }
                  bb_debug($hold_parent);
                  if ($file_gender != 'all') {
                      Redis::set('parent-'.$file_gender.'-'.$get_domain['domain_alias'], json_encode($hold_parent));
                      \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/parent-'.$file_gender.'-'.$get_domain['domain_alias'].'.json', json_encode($hold_parent));
                  } else {
                      Redis::set('parent-'.$get_domain['domain_alias'], json_encode($hold_parent));
                      \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/parent-'.$get_domain['domain_alias'].'.json', json_encode($hold_parent));
                  }
              }
            } catch (\Exception $e) {
              //\Log::error($e);
              //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
            }
        }

        $key_check = 'parent-women-'.$get_domain['domain_alias'];
        $parent_women = Redis::get($key_check);                      //bb_debug(json_decode($parent_women));
        $parent_men   = Redis::get('parent-men-'.$get_domain['domain_alias']);   //bb_debug(json_decode($parent_men));

        if (! is_null($parent_women)) {
            $status = true;
        }

        if (is_null($parent_women)) {
            $file = storage_path().'/app/'.$get_domain['domain_name'].'/catalog/'.$key_check.'.json';

            //if (\Storage::get($key_check.'.json')) {
            if (\File::exists($file)) {
                $status         = true;
            } else {
                $status         = false;
            }
        }

        return $status;
    }

    // --------------------------------------------------------------------

    /**
     * Set and Display a listing of the parent type that has a child with Redis Key.
     *
     * Redis key - [parent-men,parent-women]
     * @berrybenka.com/clothing/dresses -> untuk generate file json kategory based on type dresses[child]
     *
     * @return Array with json value
     */
    public static function filter_type($id = null, $gen = null)
    {
    //Define Domain and Channel
      $get_domain = get_domain();
      
      $core_selector                = getCoreSelector('front_end_type');
      $core_selector_checkproducts  = getCoreSelector('products');

      $my_hold_n3 = null;
      $my_hold_n2 = null;
        //$parents = [1,2,3,4,5,6];
        if ($id != null) {
             $parents = [$id];
        } else {
             $parents = [1];
        }

        foreach ($parents as $parent) {

            //$genders = [1,2];
            //$genders = [2];
            if ($gen != null) {
                 $genders = [$gen];
            } else {
                 $genders = [2];
            }
            foreach ($genders as $gender) {

                //$gender = 2;
                if ($gender == 1) {
                    $file_gender = 'men';
                } elseif ($gender == 2) {
                    $file_gender = 'women';
                } elseif ($gender == 3) {
                    $file_gender = 'all';
                }

                $hold_n2 = null;

                $where['parent']    = 0;
                $where['id']        = $parent;
                if ($file_gender != 'all') {
                    $where['-gender']   = $gender;
                }                
                try {
                    $solr_front_end_type = get_active_solr($core_selector, $query = null, $where, $limit = 10000, $offset = null, $order = null, $group = null)->docs;                  
                    if (! empty($solr_front_end_type)) {
                        foreach ($solr_front_end_type as $key => $value) {                                                    

                            $key              = $value->type_url;
                            $type_url         = $value->type_url;
                            $type_name        = $value->type_name;
                            $type_name_bahasa = $value->type_name_bahasa;
                            $id               = $value->id;                                                                              

                            $where_n1['parent'] = $id;
                            if ($file_gender != 'all') {
                                $where_n1['-gender']   = $gender;
                            }           

                            //check child type product is exist / not, if not exist then skip n3
                            $where_products                        = array();
                            $where_products['url_set']          = urlencode("".$type_url."");
                            if ($file_gender != 'all') {
                              $where_products['-product_gender']  = $gender;
                            }  
                            $where_products['product_status']   = 1;
                            
                            $count_products  = get_active_solr($core_selector_checkproducts, $query = null, $where_products, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                                                                           
                            if(isset($count_products) && $count_products <= 0){                                                                                                                                        
                                continue;
                            }
                          
                            $solr_front_end_type_n1 = get_active_solr($core_selector, $query = null, $where_n1, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                            if (! empty($solr_front_end_type_n1)) {
                                foreach ($solr_front_end_type_n1 as $key_n1 => $value_n1) {
                                    $type_url_n2          = $value_n1->type_url;
                                    $type_name_n2         = $value_n1->type_name;
                                    $type_name_bahasa_n2  = $value_n1->type_name_bahasa;
                                    $id_n2                = $value_n1->id;
                                    $hold_n1[]            = $type_url_n2;

                                    $where_n2['parent'] = $id_n2;
                                    if ($file_gender != 'all') {
                                        $where_n2['-gender']   = $gender;
                                    }                                                                               
                                  
                                    $solr_front_end_type_n2 = get_active_solr($core_selector, $query = null, $where_n2, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                                    $hold_n3 = null;
                                    if (! empty($solr_front_end_type_n2)) {
                                        foreach ($solr_front_end_type_n2 as $key_n2 => $value_n2) {
                                            $type_url_n3            = $value_n2->type_url;
                                            $type_name_n3           = $value_n2->type_name;
                                            $type_name_bahasa_n3    = $value_n2->type_name_bahasa;
                                            $id_n3                  = $value_n2->id;
                                            $hold_n3[$type_url_n3]  = null;
                                                                                        
                                            $hn3['type_url']          = $type_url_n3;
                                            $hn3['type_name']         = $type_name_n3;
                                            $hn3['type_name_bahasa']  = $type_name_bahasa_n3;
                                            $hn3['child']             = null;                                                                                        
                                            
                                            //check child type product is exist / not, if not exist then skip n3
                                            $where_products_n3                        = array();
                                            $where_products_n3['url_set']          = urlencode("".$type_url_n3."");;
                                            if ($file_gender != 'all') {
                                              $where_products_n3['-product_gender']  = $gender;
                                            }
                                            $where_products_n3['product_status']   = 1;
                                            
                                            $save_hold_n3   = $hold_n3;
                                            $count_products_n3  = get_active_solr($core_selector_checkproducts, $query = null, $where_products_n3, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                                                                           
                                            if(isset($count_products_n3) && $count_products_n3 <= 0){                                                                                                                                        
                                                $my_hold_n3[]   = array();  
                                            }else{
                                                $my_hold_n3[]   = $hn3;   
                                            }                                                                                        
                                        }                                        
                                        $my_hold_n3 = array_filter($my_hold_n3);
                                        
                                        $hn2['type_url'] = $type_url_n2;
                                        $hn2['type_name'] = $type_name_n2;
                                        $hn2['type_name_bahasa'] = $type_name_bahasa_n2;
                                        $hn2['child'] = $my_hold_n3;
                                        //$my_hold_n2[] = $hn2;    
                                        
                                        //check child type product is exist / not, if not exist then skip n2
                                        $where_products_n2                     = array();
                                        $where_products_n2['url_set']          = urlencode("".$type_url_n2."");;
                                        if ($file_gender != 'all') {
                                          $where_products_n2['-product_gender']  = $gender;
                                        }
                                        $where_products_n2['product_status']   = 1;

                                        $save_hold_n2   = $hold_n2;
                                        $count_products_n2  = get_active_solr($core_selector_checkproducts, $query = null, $where_products_n2, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                                                                           
                                        if(isset($count_products_n2) && $count_products_n2 <= 0){                                                                                                                                        
                                            $my_hold_n2[]   = array();  
                                        }else{
                                            $my_hold_n2[]   = $hn2;   
                                        }                                         

                                        $hold_n2[$type_url_n2] = $hold_n3;
                                        if ($file_gender != 'all') {
                                            Redis::set($type_url_n2.'-'.$file_gender.'-'.$get_domain['domain_alias'], json_encode($my_hold_n3));
                                            \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/'.$type_url_n2.'-'.$file_gender.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n3));
                                        } else {
                                            Redis::set($type_url_n2.'-'.$get_domain['domain_alias'], json_encode($my_hold_n3));
                                            \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/'.$type_url_n2.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n3));
                                        }
                                        // Redis::set($type_url_n2.'-'.$file_gender.'-bb', json_encode($my_hold_n3));
                                        // \Storage::disk('local')->put($type_url_n2.'-'.$file_gender.'-bb.json', json_encode($my_hold_n3));
                                        unset($hold_n3);
                                        unset($my_hold_n3);
                                    }else {
                                        //if (! empty($save_hold_n3)) {
                                        $hold_n2[$type_url_n2] = null;
                                        // } else {
                                        //     $hold_n2[] = $type_url_n2;
                                        // }
                                        $hn2['type_url'] = $type_url_n2;
                                        $hn2['type_name'] = $type_name_n2;
                                        $hn2['type_name_bahasa'] = $type_name_bahasa_n2;
                                        $hn2['child'] = null;
                                        
                                        //check child type product is exist / not, if not exist then skip n3
                                        $where_products_n2                     = array();
                                        $where_products_n2['url_set']          = urlencode("".$type_url_n2."");;
                                        if ($file_gender != 'all') {
                                          $where_products_n2['-product_gender']  = $gender;
                                        }
                                        $where_products_n2['product_status']   = 1;

                                        $count_products_n2  = get_active_solr($core_selector_checkproducts, $query = null, $where_products_n2, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                                                                                                                   
                                        if(isset($count_products_n2) && $count_products_n2 <= 0){                                                                                                                                        
                                            $my_hold_n2[] = array();
                                        }else{
                                            $my_hold_n2[] = $hn2;                                            
                                        }                                                                             
                                    }
                              }
                              $my_hold_n2 = array_filter($my_hold_n2);   
                          }
                      }
                      if ($file_gender != 'all') {
                          Redis::set($type_url.'-'.$file_gender.'-'.$get_domain['domain_alias'], json_encode($my_hold_n2));
                          \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/'.$type_url.'-'.$file_gender.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2));
                      } else {
                          Redis::set($type_url.'-'.$get_domain['domain_alias'], json_encode($my_hold_n2));
                          \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/'.$type_url.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2));
                      }
                  }
                } catch (\Exception $e) {
                  \Log::error($e);
                  \Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
                }
            }
        }

        $key_check = 'accessories-men-sd';
        $value = Redis::get($key_check);

        if (! is_null($value)) {
            $status = true;
        }

        if (is_null($value)) {
            $file = storage_path().'/app/'.$get_domain['domain_name'].'/catalog/'.$key_check.'.json';

            //if (\Storage::get($key_check.'.json')) {
            if (\File::exists($file)) {
                $status         = true;
            } else {
                $status         = false;
            }
        }

        return $status;
    }

    // --------------------------------------------------------------------

    /**
     * Set and Display a listing of the parent type that has a child with Redis Key.
     *
     * Redis key - [parent-men,parent-women]
     * @berrybenka.com/clothing/dresses -> untuk generate file json kategory based on type new arrival and sale[special page]
     *
     * @return Array with json value
     */
    public static function menu_type($gen = null)
    {
        //Define Domain and Channel
        $get_domain                   = get_domain();
        $domain_id                    = $get_domain['domain_id'];
        $core_selector                = getCoreSelector('front_end_type');
        $core_selector_checkproducts  = getCoreSelector('products');

        $gender = $gen;
        
        if ($gender == 1) {
            $file_gender = 'men';
        } elseif ($gender == 2) {
            $file_gender = 'women';
        } elseif ($gender == 3) {
            $file_gender = 'all';
        }

        $where['parent']    = 0;
        if ($file_gender != 'all') {
            $where['-gender']   = $gender;
        }

        $my_hold_n = null;
        
        try {
            $solr_front_end_type = get_active_solr($core_selector, $query = null, $where, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
            if (! empty($solr_front_end_type)) {
                foreach ($solr_front_end_type as $key => $value) {//clothing,shoes
                    $key              = $value->type_url;
                    $type_url         = $value->type_url;
                    $type_name        = $value->type_name;
                    $type_name_bahasa = $value->type_name_bahasa;
                    $id               = $value->id;

                    $where_n1['parent'] = $id;
                    if ($file_gender != 'all') {
                      $where_n1['-gender']   = $gender;
                    }                              

                    //check child type product is exist / not, if not exist then skip 
                    $where_products                     = array();
                    $where_products['url_set']          = "".ucfirst(str_replace("\r\n",'', $type_url))."";
                    if ($file_gender != 'all') {
                      $where_products['-product_gender']  = $gender;
                    }
                    $where_products['product_status']   = 1;
                    $where_products['new_arrival']      = 1;  


                    $count_products  = get_active_solr($core_selector_checkproducts, $queryProducts = NULL, $where_products, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                            
                    if(isset($count_products) && $count_products <= 0){
                      continue;                                
                    } 

                    $solr_front_end_type_n1 = get_active_solr($core_selector, $query = null, $where_n1, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                    if (! empty($solr_front_end_type_n1)) {
                        foreach ($solr_front_end_type_n1 as $key_n1 => $value_n1) {
                            $type_url_n2          = $value_n1->type_url;
                            $type_name_n2         = $value_n1->type_name;
                            $type_name_bahasa_n2  = $value_n1->type_name_bahasa;
                            $id_n2                = $value_n1->id;
                            $hold_n1[]            = $type_url_n2;     

                            //check child type product is exist / not, if not exist then skip 
                            $where_products                     = array();
                            $where_products['url_set']          = "".ucfirst(str_replace("\r\n",'', $type_url_n2))."";
                            if ($file_gender != 'all') {
                              $where_products['-product_gender']  = $gender;
                            }
                            $where_products['product_status']   = 1;
                            $where_products['new_arrival']      = 1;                            

                            $queryProducts = NULL;                    

                            $count_products  = get_active_solr($core_selector_checkproducts, $queryProducts, $where_products, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                            
                            if(isset($count_products) && $count_products <= 0){
                                continue;
                            } 

                            $hn['type_url'] = $type_url_n2;
                            $hn['type_name'] = $type_name_n2;
                            $hn['type_name_bahasa'] = ($domain_id == 3) ? $type_name_n2 : $type_name_bahasa_n2;
                            $hn['child'] = null;
                            $my_hold_n[] = $hn;

                        }
                    }
                    
                    //bb_debug($value);
                    //bb_debug($solr_front_end_type_n1);
                    $hn2['type_url'] = $type_url;
                    $hn2['type_name'] = $type_name;
                    $hn2['type_name_bahasa'] = ($domain_id == 3) ? $type_name : $type_name_bahasa;
                    $hn2['child'] = isset($my_hold_n)?$my_hold_n:null;
                    $my_hold_n2[] = $hn2;
                    unset($my_hold_n);
                }            
                //var_dump($my_hold_n2);die;
                
                
                //sale menu
                foreach ($solr_front_end_type as $key => $valuesale) {//clothing,shoes
                    $keysale              = $valuesale->type_url;
                    $type_urlsale         = $valuesale->type_url;
                    $type_namesale        = $valuesale->type_name;
                    $type_name_bahasasale = $valuesale->type_name_bahasa;
                    $idsale               = $valuesale->id;

                    $where_n1['parent'] = $idsale;
                    if ($file_gender != 'all') {
                      $where_n1['-gender']   = $gender;
                    }                              

                    //check child type product is exist / not, if not exist then skip 
                    $where_products                     = array();
                    $where_products['url_set']          = "".ucfirst(str_replace("\r\n",'', $type_urlsale))."";
                    if ($file_gender != 'all') {
                      $where_products['-product_gender']  = $gender;
                    }
                    $where_products['product_status']   = 1;
                    $where_products['eksklusif_in_promo']   = 0;


                    $count_products  = get_active_solr($core_selector_checkproducts, $queryProducts = NULL, $where_products, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                            
                    if(isset($count_products) && $count_products <= 0){
                      continue;                                
                    } 

                    $solr_front_end_type_n1 = get_active_solr($core_selector, $query = null, $where_n1, $limit = 10000, $offset = null, $order = null, $group = null)->docs;
                    if (! empty($solr_front_end_type_n1)) {
                        foreach ($solr_front_end_type_n1 as $key_n1 => $value_n1_sale) {
                            $type_url_n2sale          = $value_n1_sale->type_url;
                            $type_name_n2sale         = $value_n1_sale->type_name;
                            $type_name_bahasa_n2sale  = $value_n1_sale->type_name_bahasa;
                            $id_n2sale                = $value_n1_sale->id;
                            $hold_n1sale[]           = $type_url_n2sale;     

                            //check child type product is exist / not, if not exist then skip 
                            $where_products                     = array();
                            $where_products['url_set']          = "".ucfirst(str_replace("\r\n",'', $type_url_n2sale))."";
                            if ($file_gender != 'all') {
                              $where_products['-product_gender']  = $gender;
                            }
                            $where_products['product_status']   = 1;
                            $where_products['eksklusif_in_promo']   = 0;

                            $queryProductsSale = urlencode('product_sale_price:[3000 TO *]');                    

                            $count_products  = get_active_solr($core_selector_checkproducts, $queryProductsSale, $where_products, $limit = 10, $offset = null, $order = null, $group = null)->numFound;                            
                            if(isset($count_products) && $count_products <= 0){
                                continue;
                            } 

                            $hnsale['type_url'] = $type_url_n2sale;
                            $hnsale['type_name'] = $type_name_n2sale;
                            $hnsale['type_name_bahasa'] = ($domain_id == 3) ? $type_name_n2sale : $type_name_bahasa_n2sale;
                            $hnsale['child'] = null;
                            $my_hold_nsale[] = $hnsale;

                        }
                    }
                    
                    //bb_debug($value);
                    //bb_debug($solr_front_end_type_n1);
                    $hn2sale['type_url'] = $type_urlsale;
                    $hn2sale['type_name'] = $type_namesale;
                    $hn2sale['type_name_bahasa'] = ($domain_id == 3) ? $type_namesale : $type_name_bahasasale;
                    $hn2sale['child'] = isset($my_hold_nsale)?$my_hold_nsale:null;
                    $my_hold_n2sale[] = $hn2sale;
                    unset($my_hold_nsale);
                }

                if ($file_gender != 'all') {
                    Redis::set('menu-'.$file_gender.'-'.$get_domain['domain_alias'], json_encode($my_hold_n2));
                    \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/menu-'.$file_gender.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2));
                    
                    Redis::set('menu-sale-'.$file_gender.'-'.$get_domain['domain_alias'], json_encode($my_hold_n2sale));
                    \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/menu-sale-'.$file_gender.'-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2sale));
                } else {
                    Redis::set('menu-'.$get_domain['domain_alias'], json_encode($my_hold_n2));              
                    \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/menu-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2));
                    
                    Redis::set('menu-sale-'.$get_domain['domain_alias'], json_encode($my_hold_n2sale));              
                    \Storage::disk('local')->put($get_domain['domain_name'].'/catalog/menu-sale-'.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2sale));
                }
            }
        } catch (\Exception $e) {
            //\Log::error($e);
            //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
        }
        
        if($domain_id == 3){
          $value      = Redis::get('menu-'.$get_domain['domain_alias']);
          $valuesale  = Redis::get('menu-sale-'.$get_domain['domain_alias']);
        }else{
          $value      = Redis::get('menu-men-'.$get_domain['domain_alias']);
          $valuesale  = Redis::get('menu-sale-men-'.$get_domain['domain_alias']);
        }
        
        $status = false;
        if (! is_null($value) && ! is_null($valuesale)) {
            $status = true;
        }

        if (is_null($value)) {
            $file = storage_path().'/app/'.$get_domain['domain_name'].'/catalog/'.$value.'.json';

            //if (\Storage::get($value.'.json')) {
            if (\File::exists($file)) {
                $status         = true;
            } else {
                $status         = false;
            }
        }
        

        return $status;
    }

  /**
  *** Add to cart function
  *** add cart to cart session.
  *** @return true or false (Boolean)
  **/
  public static function addtocart($params) {

    $status = FALSE;

    $SKU = isset($params['SKU']) ? str_replace('/', 'or', $params['SKU']) : NULL;
    $quantity = isset($params['quantity']) ? $params['quantity'] : NULL;
    $brand_id = isset($params['brand_id']) ? $params['brand_id'] : NULL;//
    $type_id = isset($params['type_id']) ? $params['type_id'] : NULL;//
    $parent_type_id = isset($params['parent_type_id']) ? $params['parent_type_id'] : NULL;//
    $type_id_real = isset($params['type_id_real']) ? $params['type_id_real'] : NULL;//
    $parent_type_id_real = isset($params['parent_type_id_real']) ? $params['parent_type_id_real'] : NULL;//
    $product_id = isset($params['product_id']) ? $params['product_id'] : NULL;
    $product_price = isset($params['product_price']) ? $params['product_price'] : NULL;
    $product_ori_price = isset($params['product_ori_price']) ? $params['product_ori_price'] : NULL;
    $product_sale_price = isset($params['product_sale_price']) ? $params['product_sale_price'] : NULL;
    $product_special_price = isset($params['product_special_price']) ? $params['product_special_price'] : NULL;
    $product_name = isset($params['product_name']) ? $params['product_name']: NULL;//
    $product_weight = isset($params['product_weight']) ? $params['product_weight'] : NULL;//

    $color_category = isset($params['color_category']) ? $params['color_category'] : NULL;//
    $size_category = isset($params['size_category']) ? str_replace('/', 'or', $params['size_category']) : NULL;//
    $image_name = isset($params['image_name']) ? $params['image_name'] : NULL;//
    $product_inv = isset($params['product_inv']) ? $params['product_inv'] : NULL;//
    $brand_name = isset($params['brand_name']) ? $params['brand_name'] : NULL;//
    $variant_color_name = isset($params['variant_color_name']) ? $params['variant_color_name'] : NULL;
    $promo_id = isset($params['promo_id']) ? $params['promo_id'] : NULL;
    $promo_name = isset($params['promo_name']) ? $params['promo_name'] : NULL;

    $parent_track_sale = isset($params['parent_track_sale']) ? $params['parent_track_sale'] : NULL;
    $child_track_sale = isset($params['child_track_sale']) ? $params['child_track_sale'] : NULL;
    $variant_items = isset($params['variant_items']) ? $params['variant_items'] : NULL;
    $product_front_end_type = isset($params['product_front_end_type']) ? $params['product_front_end_type'] : NULL;
    $product_type_url = isset($params['product_type_url']) ? $params['product_type_url'] : NULL;
    $image_name = isset($params['image_name']) ? $params['image_name'] : NULL;
    $product_gender = isset($params['product_gender']) ? $params['product_gender'] : NULL;

    if(\Cookie::get('_ga_utmz')) {
      $cookData = json_decode(\Cookie::get('_ga_utmz'));
      $utm_source = isset($cookData[0]->utm_source) ? $cookData[0]->utm_source : '';
      $utm_medium = isset($cookData[0]->utm_medium) ? $cookData[0]->utm_medium : '';
      $utm_campaign = isset($cookData[0]->utm_campaign) ? $cookData[0]->utm_campaign : '';
    } elseif(\Session::get('utm_source')!=FALSE || \Session::get('utm_medium')!=FALSE || \Session::get('utm_campaign')!=FALSE) {
      $utm_source =\Session::get('utm_source');
      $utm_medium = \Session::get('utm_medium');
      $utm_campaign = \Session::get('utm_campaign');
      /*
    }elseif(isset($_GET['utm_source']) || isset($_GET['utm_medium']) || isset($_GET['utm_campaign'])){
      $expire_time = 60 * 24 * 365 * 2;
          $cookie = array(
          'name'   => '_ga_utmz',
          'value'  => '[{"utm_source":"'.$_GET['utm_source'].'","utm_medium":"'.$_GET['utm_medium'].'","utm_campaign":"'.$_GET['utm_campaign'].'"}]',
          'expire' => $expire_time,
          'domain' => $_SERVER['HTTP_HOST'],
          'path'   => '/'
      );
      $cookies    = makeCookie($cookie);
      if ($cookies) {
        $cookData = json_decode(\Cookie::get('_ga_utmz'));
        $utm_source = isset($cookData[0]->utm_source) ? $cookData[0]->utm_source : '';
        $utm_medium = isset($cookData[0]->utm_medium) ? $cookData[0]->utm_medium : '';
        $utm_campaign = isset($cookData[0]->utm_campaign) ? $cookData[0]->utm_campaign : '';
      }
      */
    } else {
      $utm_source = "";
      $utm_medium = "";
      $utm_campaign = "";
    }

    $rowid = Cart::search(array('id' => $SKU));
    $cart = Cart::get($rowid[0]);

    if ($cart) {
      $rowId = $cart->rowid;
      $qty = $quantity + $cart->qty;

      Cart::update($rowId, array('qty' => $qty));
      $status = TRUE;
    } else {
      Cart::add(array(
            'id'    => $SKU,
            'name'    => $product_name,
            'qty'     => $quantity,
            'price'   => $product_price,
            'options' => array(
                  'brand_id'    => $brand_id,
                  'brand_name'  => $brand_name,
                  'front_end_type'=> $product_front_end_type,
                                                                        'type_id'       => $type_id,
                                                                        'type_id_real'  => $type_id_real,
                  'type_url'    => $product_type_url,
                  'product_id'  => $product_id,
                  'color_id'    => $color_category,
                  'color_name'  => $variant_color_name,
                  'size'      => $size_category,
                  'image'     => $image_name,
                  'weight'    => $product_weight,
                  'price'       => $product_ori_price,
                  'sale_price'  => $product_sale_price,
                  'special_price' => $product_special_price,
                  'promo_id'      => $promo_id,
                  'promo_name'    => $promo_name,
                  'utm_source'  => $utm_source,
                  'utm_medium'  => $utm_medium,
                  'utm_campaign'  => $utm_campaign,
                  'parent_track_sale' => $parent_track_sale,      /** For tracking sale **/
                  'child_track_sale'  => $child_track_sale,            /** For tracking sale **/
                  'gender'  => $product_gender
              )
          ));
      $status = TRUE;
    }

    return $status;
  }
  // --------------------------------------------------------------------------------------------------

  /**
     * Brand URI segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_brand_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 48;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;
        $field_list     = null;

        $get_domain     = get_domain();
        $domain_id      = $get_domain['domain_id'];
        $core_selector  = getCoreSelector("products");

        $query = "*:*";

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        // if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            // $start = $data['pagination'];
        // }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
      if (array_key_exists('gender', $data_http_get)) {
        $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
        $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
        $fq_arr['-product_gender']  = $product_gender;
      }

      if (array_key_exists('cat', $data_http_get)) {
        $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        $fq_arr['url_set']   = $data_http_get['cat'];
      }

      if (array_key_exists('color', $data_http_get)) {
        $all_color_name = explode('--',$data_http_get['color']);
        foreach ($all_color_name as $key => $value) {
            $list_color_name[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
        $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
        
        $core_selector  = getCoreSelector("product_detail");

        $group                   = 'pid';
      }

      if (array_key_exists('size', $data_http_get)) {
        $all_size_name = explode('-',$data_http_get['size']);
        foreach ($all_size_name as $key => $value) {
            $list_size_name[] = $value;
        }
        $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
        $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
        
        $core_selector  = getCoreSelector("product_detail");

        $group                       = 'pid';
      }

      if ( isset($data_http_get['page']) && ! is_null($data_http_get['page'])) {
        $start = $data_http_get['page'];
      }

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = 'total_series_score+'.$data_http_get['popular'].'%2Cproduct_scoring+'.$data_http_get['popular'].'';
            }

            if (array_key_exists('pn', $data_http_get)) {
                if ($domain_id == 1) {
                  $sort = 'launch_date_bb+'.$data_http_get['pn'].'';
                } elseif ($domain_id == 2) {
                  $sort = 'launch_date_hb+'.$data_http_get['pn'].'';
                } elseif ($domain_id == 3) {
                  $sort = 'launch_date_sd+'.$data_http_get['pn'].'';                  
                }                
                
            }

            if (array_key_exists('price', $data_http_get)) {
                $sort = 'real_price+'.$data_http_get['price'].'';
                if (isset($data['sale']) && ! is_null($data['sale'])) {
                    $sort = 'product_sale_price+'.$data_http_get['price'].'';
                }
            }
            if (array_key_exists('discount', $data_http_get)) {
                $sort = 'discount+'.$data_http_get['discount'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                $sprice = $data_http_get['sprice'];                
                
                
                if(isset($sprice) && !is_array($sprice) && strtolower($sprice) != 'all' && strpos($sprice, '-') !== false){             
                    
                    $low_filter_price   = 0;
                    $high_filter_price  = 0;

                    if(strpos($sprice, '-') !== false) {                
                        $filter_price       = explode('-' , $sprice);

                        $low_filter_price   = isset($filter_price[0]) && is_numeric($filter_price[0]) ? $filter_price[0] : 0;
                        $high_filter_price  = isset($filter_price[1]) && is_numeric($filter_price[1])? $filter_price[1] : 0;
                    }elseif(is_numeric($sprice)){
                       $high_filter_price  = $sprice; 
                    }  
                 
                    $min_price = $low_filter_price * 1000;
                    $max_price = $high_filter_price * 1000;

                    if (isset($data['sale']) && ! is_null($data['sale'])) {
                        $fq_arr['sprice'] = '&fq='.urlencode("product_sale_price:[".$min_price." TO ".$max_price."]");
                    } else {
                        $fq_arr['sprice'] = '&fq='.urlencode("real_price:[".$min_price." TO ".$max_price."]");
                    }    
                }                
            }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'total_series_score+desc%2Cproduct_scoring+desc';
            }

        }
        if($sort == "") {
          $sort = "total_series_score+desc%2Cproduct_scoring+desc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        
        $display_oos = CheckOOS();
        if($display_oos === FALSE){
          $fq_arr['product_status']     = 1;  
        }
        //$fq_arr['product_status']     = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = isset($group) ? $group : null;
        $data['field_list']     = isset($field_list) ? $field_list : null;

        return $data;
    }

    // --------------------------------------------------------------------

  /**
     * Brand Color URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_brand_color_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 20;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        // if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            // $start = $data['pagination'];
        // }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
      if (array_key_exists('gender', $data_http_get)) {
        $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
        $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
        $fq_arr['-product_gender']  = $product_gender;
      }

      if (array_key_exists('cat', $data_http_get)) {
        $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        $fq_arr['url_set']   = $data_http_get['cat'];
      }

      if (array_key_exists('size', $data_http_get)) {
        $all_size_name = explode('-',$data_http_get['size']);
        foreach ($all_size_name as $key => $value) {
            $list_size_name[] = $value;
        }
        $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
        $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
      }

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'color_name+asc';
            }

        }
        if($sort == "") {
          $sort = "color_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'color_name';
        $data['field_list']     = 'color_name,color_hex';

        return $data;
    }

  // --------------------------------------------------------------------

    /**
     * Brand Size URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_brand_size_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 100;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
      if (array_key_exists('gender', $data_http_get)) {
        $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
        $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
        $fq_arr['-product_gender']  = $product_gender;
      }

      if (array_key_exists('cat', $data_http_get)) {
        $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        $fq_arr['url_set']   = $data_http_get['cat'];
      }

      if (array_key_exists('color', $data_http_get)) {
        $all_color_name = explode('--',$data_http_get['color']);
        foreach ($all_color_name as $key => $value) {
            $list_color_name[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
        $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
        $group                   = 'pid';
      }

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'product_size_url+asc';
            }

        }
        if($sort == "") {
          $sort = "product_size_url+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'product_size_url';
        $data['field_list']     = 'product_size,product_size_url,pid';

        return $data;
    }

  /**
     * GET Brand Gender URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_brand_gender_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 1000;
        $fq_arr         = array();
        $sort           = null;
        $group          = "product_gender";

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("products");

        $query = "*:*";

        if ( isset($data['brand_url']) && ! is_null($data['brand_url'])) {
            $all_brand_url = explode('--',$data['brand_url']);
            foreach ($all_brand_url as $key => $value) {
                    $list_brand_url[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
            $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
        }

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = "pid+desc";
            }

        }
        if($sort == "") {
          $sort = "pid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = $group;
        $data['field_list']     = 'product_gender';

        return $data;
    }

   /**
     * Set and Display a listing of the parent type that has a child with Redis Key.
     *
     * Redis key - [parent-men,parent-women]
     *
     * @return Array with json value
     */
  public static function generate_category_menu($params) {
    //Define Domain and Channel
    $get_domain = get_domain();
    bb_debug($params);
    
    $core_selector      = getCoreSelector('products');
    $core_selector_fet  = getCoreSelector('front_end_type');

    $query = "*:*";
    
    $my_hold_n2     = NULL;
    $folder_file    = $params["folder_file"];
    $field_name     = $params["field_name"];
    $field_value    = $params["field_value"];
    $gender         = $params["gender"];
    $gender_string  = "";

    //Get Catalog
    $where[$field_name] = '"'.$field_value.'"';
    $where['eksklusif_in_promo']  = 0;
    $where['product_status']      = 1;
    
    //filter gender
    if($gender == 1){ //women
      $where['-product_gender'] = 2;
    }else if($gender == 2){ //men
      $where['-product_gender'] = 1;
    }
    //end filter gender
    
    $group  = "type_url";
    $fields = "type_url,type_name,type_name_bahasa,front_end_type,bahasa,url_set";
    
    try {
      $parent_type = get_active_solr($core_selector, $query=null, $where, $limit=null, $offset=null, $order=null, $group, $fields)->docs;

      if (! empty($parent_type)) {
        $my_hold_n2 = [];
        
        foreach ($parent_type as $row) {
          $parent = explode(',',$row->front_end_type);
          if(empty($parent)){
            \Log::info('generate_category_menu: parent is NULL');
            return false;
          }

          foreach ($parent as $key => $value) {
            if ($value) {
              $check_parent_type = Self::checkParentType($value);
              if ($check_parent_type == true) { 
                $parent_id = $value;
                break;
              }
            }
          }

          \Log::info('generate_category_menu: parent: ' . json_encode($parent));
          if(!isset($parent_id)){
            \Log::info('generate_category_menu: parent ID not found');
            return false;
          }

          bb_debug($parent);
          $parent_type_url          = $row->type_url;
          $parent_type_name         = $row->type_name;
          $parent_type_name_bahasa  = $row->type_name_bahasa;

          $where_type["parent"] = $parent_id;
          $solr_front_end_type = get_active_solr($core_selector_fet, $query = null, $where_type, $limit = 10000, $offset = null, $order = null, $group = null);

          try {
            $solr_front_end_types = $solr_front_end_type->docs;

            if (!empty($solr_front_end_types)) {
              foreach ($solr_front_end_types as $key => $value) {
                // $key      = $value->type_url;
                $type_url         = $value->type_url;
                $type_name        = $value->type_name;
                $type_name_bahasa = $value->type_name_bahasa;
                $id               = $value->id;
                $where['url_set'] = $type_url;

                $product = get_active_solr($core_selector, $query=null, $where, $limit=null, $offset=null, $order=null, $group=null, $fields);

                try {
                  $products = $product->docs;

                  if (!empty($products)) {
                    bb_debug($products);
                    $hn['type_url']         = $type_url;
                    $hn['type_name']        = $type_name;
                    $hn['type_name_bahasa'] = $type_name_bahasa;
                    $hn['child']            = null;
                    
                    $my_hold_n[] = $hn;

                    //$hold_n2[] = $type_url;
                  }
                } catch (\Exception $e) {
                  //\Log::error($e);
                  //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
                }
              }

              $hn2['type_url']          = $parent_type_url;
              $hn2['type_name']         = $parent_type_name;
              $hn2['type_name_bahasa']  = $parent_type_name_bahasa;
              $hn2['child']             = isset($my_hold_n) ? $my_hold_n : null;
              
              $my_hold_n2[] = $hn2;
              unset($my_hold_n);
            }
            } catch (\Exception $e) {
              //\Log::error($e);
              //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
            }

          //bb_debug($solr_front_end_type);
        }
      }
      
      //set gender
      if($gender == 1){ //women
        $gender_string = "women-";
      }else if($gender == 2){ //men
        $gender_string = "men-";
      }
      //end set gender
      
      //write to redis and json file
      Redis::set('menu-'.$folder_file.'-'.$field_value.'-'.$gender_string.$get_domain['domain_alias'], json_encode($my_hold_n2));
      \Storage::disk('local')->put($get_domain['domain_name'].'/'.$folder_file.'/menu-'.$folder_file.'-'.$field_value.'-'.$gender_string.$get_domain['domain_alias'].'.json', json_encode($my_hold_n2));
      //end write to redis and json file
    } catch (\Exception $e) {
      //\Log::error($e);
      //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
    }
    
    if($my_hold_n2 == NULL){
      $value = NULL;
    }else{
      $value = Redis::get('menu-'.$folder_file.'-'.$field_value.'-'.$gender_string.$get_domain['domain_alias']);
    }

    //bb_debug($parent_type);
    return $value;
  }

    public static function get_left_menu_type_special()
    {
      //Define Domain and Channel
      $get_domain           = get_domain();

      //Generate uri
      $generate_uri_segment = generate_uri_segment();
      
      //Generate get uri
      $generate_uri_httpget = generate_get_uri();

      $get_special_filter_type_name = Product::get_special_filter_type_name();

      $gender = isset($generate_uri_httpget['gender']) ? $generate_uri_httpget['gender'] : '';

      // Get filter type data
      $filter_type    = Redis::get($get_special_filter_type_name);

      $filter_type_db = \DB::table('left_menu_special')
        ->where('left_menu_special_id', $generate_uri_segment['special']);

      if($gender != ''){
        if($gender == 'women'){
          $filter_type_db->where('gender', 1);
        }else{
          $filter_type_db->where('gender', 2);
        }
      }

      $filter_type_db_value = $filter_type_db->value('left_menu_data_new');

      if (is_null($filter_type) || ($filter_type == "null")) {
        $file = storage_path().'/app/'.$get_domain['domain_name'].'/special/'.$get_special_filter_type_name.'.json';

        if (\File::exists($file)) {
            $filter_type = \Storage::get($get_domain['domain_name'].'/special/'.$get_special_filter_type_name.'.json');
        } else {
            $filter_type = "null";
        }

        if (($filter_type == "null") && (!empty($filter_type_db_value))) {
          Redis::set($get_special_filter_type_name, $filter_type_db_value);
          \Storage::disk('local')->put($get_domain['domain_name'].'/special/'.$get_special_filter_type_name.'.json', $filter_type_db_value);

          $filter_type   = Redis::get($get_special_filter_type_name);
        }
      }

      return $filter_type;
    }

    /**
     * Get file name based on URI
     *
     * @return Response redis key/file name
     */
    public static function get_special_filter_type_name()
    {
        //Define Domain and Channel
        $get_domain           = get_domain();
        //Generate uri
        $generate_uri_segment = generate_uri_segment();
        //Generate get uri
        $generate_uri_httpget = generate_get_uri();
        
        $gender = isset($generate_uri_httpget['gender']) ? '-'.$generate_uri_httpget['gender'] : '';

        $filter_type_redis  = 'special-'.$generate_uri_segment['special'].$gender.'-'.$get_domain['domain_alias'];

        return $filter_type_redis;
    }

  // --------------------------------------------------------------------------------------------------

  /**
     * Tag URI segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_tag_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 48;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;
        $field_list     = null;

        $get_domain           = get_domain();
        $domain_id            = $get_domain['domain_id'];
        $core_selector        = getCoreSelector("products");
        $core_selector_detail = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['tag']) && ! is_null($data['tag'])) {
            $fq_arr['tag_url']     = '"'.$data['tag'].'"';
        }

        // if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            // $start = $data['pagination'];
        // }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
      if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
        $all_brand_url = explode('--',$data_http_get['brand']);
        foreach ($all_brand_url as $key => $value) {
            $list_brand_url[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
        $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
      }

      if (array_key_exists('gender', $data_http_get)) {
        $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
        $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
        $fq_arr['-product_gender']  = $product_gender;
      }

      if (array_key_exists('cat', $data_http_get)) {
        $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        $fq_arr['url_set']   = $data_http_get['cat'];
      }

      if (array_key_exists('color', $data_http_get)) {
        $all_color_name = explode('--',$data_http_get['color']);
        foreach ($all_color_name as $key => $value) {
            $list_color_name[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
        $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
        $core_selector           = $core_selector_detail;
        $group                   = 'pid';
      }

      if (array_key_exists('size', $data_http_get)) {
        $all_size_name = explode('-',$data_http_get['size']);
        foreach ($all_size_name as $key => $value) {
            $list_size_name[] = $value;
        }
        $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
        $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
        $core_selector               = $core_selector_detail;
        $group                       = 'pid';
      }

      if ( isset($data_http_get['page']) && ! is_null($data_http_get['page'])) {
        $start = $data_http_get['page'];
      }

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = 'total_series_score+'.$data_http_get['popular'].'%2Cproduct_scoring+'.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                if ($domain_id == 1) {
                  $sort = 'launch_date_bb+'.$data_http_get['pn'].'';
                } elseif($domain_id == 2) {
                  $sort = 'launch_date_hb+'.$data_http_get['pn'].'';
                } elseif ($domain_id == 3) {
                  $sort = 'launch_date_sd+'.$data_http_get['pn'].'';
                }
            }

            if (array_key_exists('price', $data_http_get)) {
                $sort = 'real_price+'.$data_http_get['price'].'';
                if (isset($data['sale']) && ! is_null($data['sale'])) {
                    $sort = 'product_sale_price+'.$data_http_get['price'].'';
                }
            }
            if (array_key_exists('discount', $data_http_get)) {
                $sort = 'discount+'.$data_http_get['discount'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
              $sprice = $data_http_get['sprice'];
              if(isset($sprice) && !is_array($sprice) && strtolower($sprice) != 'all' && strpos($sprice, '-') !== false){
                  $priceuri = urldecode($sprice);
                  $arrayprice = explode("|", $priceuri);
                  $query_price = '';

                  if(count($arrayprice) > 1){
                    foreach ($arrayprice as $sp) {
                      $filter_price = explode('-',$sp);

                      if(empty($filter_price) || count($filter_price) < 2){
                        abort(404);
                      }
                      $min_price = $filter_price[0]*1000;

                      if($filter_price[1] === 'above'){
                          $max_price = '*';
                      }else{
                          $max_price = $filter_price[1]*1000;
                      }

                      if (isset($data['sale']) && !is_null($data['sale'])) {
                          $query_price .= urlencode("product_sale_price:[".$min_price." TO ".$max_price."] OR ");
                      } else {
                          $query_price .= urlencode("real_price:[".$min_price." TO ".$max_price."] OR ");
                      } 

                    }

                    $query_price = urldecode($query_price);

                    $query_price = substr($query_price, 0, -4);

                    $query_price =  urlencode($query_price);

                    $fq_arr['sprice'] = '&fq=' . $query_price;
                  }else{
                    $filter_price = explode('-',$sprice);
                    if(empty($filter_price) || count($filter_price) < 2){
                      abort(404);
                    }
                    $min_price = $filter_price[0]*1000;
                    if($filter_price[1] === 'above'){
                        $max_price = '*';
                    }else{
                        $max_price = $filter_price[1]*1000;
                    }


                    if (isset($data['sale']) && !is_null($data['sale'])) {
                        $fq_arr['sprice'] = '&fq='.urlencode("product_sale_price:[".$min_price." TO ".$max_price."]");
                    } else {
                        $fq_arr['sprice'] = '&fq='.urlencode("real_price:[".$min_price." TO ".$max_price."]");
                    } 
                  }
                }
              }
            // if (array_key_exists('sprice', $data_http_get)) {
            //     $sprice = $data_http_get['sprice'];
            //     if(isset($sprice) && !is_array($sprice) && strtolower($sprice) != 'all' && strpos($sprice, '-') !== false){                    
            //         $filter_price = explode('-',$sprice);
            //         $min_price = $filter_price[0]*1000;
            //         $max_price = $filter_price[1]*1000;

            //         if (isset($data['sale']) && ! is_null($data['sale'])) {
            //             $fq_arr['sprice'] = '&fq='.urlencode("product_sale_price:[".$min_price." TO ".$max_price."]");
            //         } else {
            //             $fq_arr['sprice'] = '&fq='.urlencode("real_price:[".$min_price." TO ".$max_price."]");
            //         }
            //     }
            // }
            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'total_series_score+desc%2Cproduct_scoring+desc';

                 // $sort = $domin_solr . 'desc';
            }

        }
        if($sort == "") {
        	$sort = "total_series_score+desc%2Cproduct_scoring+desc%2Cpid+desc";
          // $sort = $domin_solr . 'desc'; 
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        
        $display_oos = CheckOOS();
        if($display_oos === FALSE){
          $fq_arr['product_status']     = 1;  
        }
        //$fq_arr['product_status']     = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = isset($group) ? $group : null;
        $data['field_list']     = isset($field_list) ? $field_list : null;

        return $data;
    }

    // --------------------------------------------------------------------

  /**
     * Tag Color URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_tag_color_to_solr($data, $data_http_get)
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 20;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['tag']) && ! is_null($data['tag'])) {
            $fq_arr['tag_url']     = '"'.$data['tag'].'"';
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
              $all_brand_url = explode('--',$data_http_get['brand']);
              foreach ($all_brand_url as $key => $value) {
                  $list_brand_url[] = '"'.$value.'"';
              }
              $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
              $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
            }

            if (array_key_exists('gender', $data_http_get)) {
              $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
              $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
              $fq_arr['-product_gender']  = $product_gender;
            }

            if (array_key_exists('cat', $data_http_get)) {
              $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
              $fq_arr['url_set']   = $data_http_get['cat'];
            }

            if (array_key_exists('size', $data_http_get)) {
              $all_size_name = explode('-',$data_http_get['size']);
              foreach ($all_size_name as $key => $value) {
                  $list_size_name[] = $value;
              }
              $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
              $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
            }

                  $sorting = ['sprice','pn','popular','price'];
                  if (array_key_exists('popular', $data_http_get)) {
                      $sort = '&popular='.$data_http_get['popular'].'';
                  }
                  if (array_key_exists('pn', $data_http_get)) {
                      $sort = '&pn='.$data_http_get['pn'].'';
                  }
                  if (array_key_exists('price', $data_http_get)) {
                      $sort = '&price='.$data_http_get['price'].'';
                  }
                  if (array_key_exists('sprice', $data_http_get)) {
                      //$sort .= '&sprice='.$data_http_get['sprice'].'';
                  }
                  //pagination
                  if (array_key_exists('show', $data_http_get)) {
                      $show = $data_http_get['show'];
                  }
                  if (array_key_exists('last_id', $data_http_get)) {
                      $start = $data_http_get['last_id'];
                  }
          } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'color_name+asc';
            }

        }
        if($sort == "") {
          $sort = "color_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'color_name';
        $data['field_list']     = 'color_name,color_hex';

        return $data;
    }

  // --------------------------------------------------------------------

    /**
     * Tag Size URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_tag_size_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 100;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;


        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['tag']) && ! is_null($data['tag'])) {
            $fq_arr['tag_url']     = '"'.$data['tag'].'"';
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
      if ( isset($data_http_get['brand']) && ! is_null($data_http_get['brand'])) {
        $all_brand_url = explode('--',$data_http_get['brand']);
        foreach ($all_brand_url as $key => $value) {
            $list_brand_url[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=brand_url:('.implode('+OR+',$list_brand_url).')';
        $fq_arr['brand_url'] = '('.implode('+OR+',$list_brand_url).')';//bb_debug($fq_arr);die;
      }

      if (array_key_exists('gender', $data_http_get)) {
        $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
        $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
        $fq_arr['-product_gender']  = $product_gender;
      }

      if (array_key_exists('cat', $data_http_get)) {
        $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
        $fq_arr['url_set']   = $data_http_get['cat'];
      }

      if (array_key_exists('color', $data_http_get)) {
        $all_color_name = explode('--',$data_http_get['color']);
        foreach ($all_color_name as $key => $value) {
            $list_color_name[] = '"'.$value.'"';
        }
        $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
        $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
        $group                   = 'pid';
      }

            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'product_size_url+asc';
            }

        }
        if($sort == "") {
          $sort = "product_size_url+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'product_size_url';
        $data['field_list']     = 'product_size,product_size_url,pid';
    //$data['field_list']     = isset($field_list) ? $field_list : null;

        return $data;
    }
  //-----------------------------------------------------------------------------------------------------------

  /**
     * URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_tag_brand_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 5000;
        $fq_arr         = array();
        $sort           = null;
        $group          = null;


        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("product_detail");

        $query = "*:*";

        if ( isset($data['tag']) && ! is_null($data['tag'])) {
            $fq_arr['tag_url']     = '"'.$data['tag'].'"';
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
          if (array_key_exists('gender', $data_http_get)) {
            $product_gender             = $data_http_get['gender'] == "women" ? 2 : 1;
            $filter_query               = 'fq=-product_gender:"'.$product_gender.'"';
            $fq_arr['-product_gender']  = $product_gender;
          }

          if (array_key_exists('cat', $data_http_get)) {
            $filter_query .= '&fq=url_set:"'.$data_http_get['cat'].'"';
            $fq_arr['url_set']   = $data_http_get['cat'];
          }

          if (array_key_exists('color', $data_http_get)) {
            $all_color_name = explode('--',$data_http_get['color']);
            foreach ($all_color_name as $key => $value) {
                $list_color_name[] = '"'.$value.'"';
            }
            $filter_query .= '&fq=color_name:('.implode('+OR+',$list_color_name).')';
            $fq_arr['color_name']    = '('.implode('+OR+',$list_color_name).')';
            $group                   = 'pid';
          }

          if (array_key_exists('size', $data_http_get)) {
            $all_size_name = explode('-',$data_http_get['size']);
            foreach ($all_size_name as $key => $value) {
                $list_size_name[] = $value;
            }
            $filter_query .= '&fq=product_size_url:('.implode('+OR+',$list_size_name).')';
            $fq_arr['product_size_url']  = '('.implode('+OR+',$list_size_name).')';
          }

                $sorting = ['sprice','pn','popular','price'];
                if (array_key_exists('popular', $data_http_get)) {
                    $sort = '&popular='.$data_http_get['popular'].'';
                }
                if (array_key_exists('pn', $data_http_get)) {
                    $sort = '&pn='.$data_http_get['pn'].'';
                }
                if (array_key_exists('price', $data_http_get)) {
                    $sort = '&price='.$data_http_get['price'].'';
                }
                if (array_key_exists('sprice', $data_http_get)) {
                    //$sort .= '&sprice='.$data_http_get['sprice'].'';
                }

                //pagination
                if (array_key_exists('show', $data_http_get)) {
                    $show = $data_http_get['show'];
                }
                if (array_key_exists('last_id', $data_http_get)) {
                    $start = $data_http_get['last_id'];
                }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = 'brand_name+asc';
            }

        }
        if($sort == "") {
          $sort = "brand_name+asc%2Cpid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['eksklusif_in_promo'] = 0;
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = 'brand_url_full';
        $data['field_list']     = 'brand_name,brand_url,product_gender';

        return $data;
    }

  //----------------------------------------------------------------------------------------

  /**
     * GET Tag Gender URI Segment into SOLR Query Params
     *
     * array['query'] array Defines the value -q(query) : title:"foo bar" AND body:"quick fox") OR title:fox
     * array['filter_query'] array Defines the value -fq(filter query) : fq=category:fashion&fq=location:nyc
     * array['core_selector'] array Defines the value core selector like products, product_detail in schema.xml /products?q={keyword}&f
     * array['start'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['rows'] array Defines the value -start, rows : 10, 25 -> So this is returning items 10-35 of the total items found in the query
     * array['field_list'] array Defines the value -fl(field list) :product_name,brand_name,product_gender,type_url -> This parameter can be used to specify a set of fields to return
     * array['sort'] array Defines the value -sort : last_modified asc
     * @return array
     */
    public static function get_tag_gender_to_solr($data, $data_http_get) //data uri segment
    {
        $query          = null;
        $filter_query   = null;
        $start          = 0;
        $sort           = null;
        $show           = 1000;
        $fq_arr         = array();
        $sort           = null;
        $group          = "product_gender";

        $get_domain     = get_domain();
        $core_selector  = getCoreSelector("products");
        
        $query = "*:*";

        if ( isset($data['pagination']) && ! is_null($data['pagination'])) {
            //$start = $data['pagination'];
        }

        if ( isset($data['tag']) && ! is_null($data['tag'])) {
            $fq_arr['tag_url']     = '"'.$data['tag'].'"';
        }

        if ( isset($data['core_selector']) && ! is_null($data['core_selector'])) {
            $core_selector = $data['core_selector'];
        }

        //Sorting and pagination from GET Method
        if ( isset($data_http_get) && is_array($data_http_get)) {
            $sorting = ['sprice','pn','popular','price'];
            if (array_key_exists('popular', $data_http_get)) {
                $sort = '&popular='.$data_http_get['popular'].'';
            }
            if (array_key_exists('pn', $data_http_get)) {
                $sort = '&pn='.$data_http_get['pn'].'';
            }
            if (array_key_exists('price', $data_http_get)) {
                $sort = '&price='.$data_http_get['price'].'';
            }
            if (array_key_exists('sprice', $data_http_get)) {
                //$sort .= '&sprice='.$data_http_get['sprice'].'';
            }

            //pagination
            if (array_key_exists('show', $data_http_get)) {
                $show = $data_http_get['show'];
            }
            if (array_key_exists('last_id', $data_http_get)) {
                $start = $data_http_get['last_id'];
            }
        } else {
            if ($sort != null) {
                $sort = $sort;
            } else {
                 $sort = "pid+desc";
            }

        }
        if($sort == "") {
          $sort = "pid+desc";
        } else {
          $sort = $sort."%2Cpid+desc";
        }
        $fq_arr['product_status'] = 1;

        $data['core_selector']  = isset($core_selector) ? $core_selector : null;
        $data['query']          = isset($query) ? $query : null;
        $data['where']          = isset($fq_arr) ? $fq_arr : null;
        $data['limit']          = isset($show) ? $show : null;
        $data['offset']         = isset($start) ? $start : null;
        $data['order']          = isset($sort) ? $sort : null;
        $data['group']          = $group;
        $data['field_list']     = 'product_gender';

        return $data;
    }
  // --------------------------------------------------------

  /**
   ** UTM Source Function
   ** For create session and make ga_utmz cookie
  */
  public static function gaUtmz() {
    if((\Request::get('utm_source')) || (\Request::get('utm_medium')) || (\Request::get('utm_campaign'))) {

      /***** Create Cookies UTM SOURCE *************/
      $utm_source     = (\Request::get('utm_source'))?\Request::get('utm_source'):"";
      $utm_medium     = (\Request::get('utm_medium'))?\Request::get('utm_medium'):"";
      $utm_campaign   = (\Request::get('utm_campaign'))?\Request::get('utm_campaign'):"";
            $expire_time    = 60 * 24 * 365 * 2;  /*** In minutes ***/

            $cookie       = array(
                                'name'   => '_ga_utmz',
                                'value'  => '[{"utm_source":"'.$utm_source.'","utm_medium":"'.$utm_medium.'","utm_campaign":"'.$utm_campaign.'"}]',
                                'expire' => $expire_time,
                                'domain' => $_SERVER['HTTP_HOST'],
                                'path'   => '/'
                            );
      $cookies    = makeCookie($cookie);

      /* Create Session UTM SOURCE */
      $utm = array(
                'utm_source' => $utm_source,
                'utm_medium' => $utm_medium,
                'utm_campaign' => $utm_campaign
              );
      $utm_session = putSession($utm);
    }
  }
  // --------------------------------------------------------

  /**
   ** Tracking sale Function
   ** For tracking sale url for product detail url
   ** @return array.
  */
  public static function trackingSale() {
    if (Request::segment(1)) {
      $trc_sale = Request::segment(1);
    }
    if (Request::segment(2)) {
      $trc_sale .= "+".Request::segment(2);
    }
    /* Page Referer */
        $page_ref = array(
            'trc_sale' => isset($trc_sale) ?  'trc_sale='.$trc_sale : NULL,
                    );

    return $page_ref;
  }
  // --------------------------------------------------------

  /**
  ** Generate file Type Json
  ** @return Json file.
  **/
  public static function generateTypeCategory() {
    //Define Domain and Channel
    $get_domain     = get_domain();

    $core_selector  = getCoreSelector("front_end_type");

    try {
      $front_end_type = get_active_solr($core_selector, $query = null, null, $limit = 10000, $offset = null, $order = null, $group = null)->docs;

      if (!empty($front_end_type)) {
        $type = array();
        foreach ($front_end_type as $key => $value) {
          $type[] = $value->type_url;
        }

        //bb_debug($type);
        if (!empty($type)) {
          \Storage::disk('local')->put($get_domain['domain_name'] . '/genfile/all-type-' . $get_domain['domain_alias'] . '.json', json_encode($type));
        }
      }
    } catch (\Exception $e) {
      //\Log::error($e);
      //\Log::alert('Error docs not found with URI : ' . \Request::fullUrl());
    }

    $file = storage_path() . '/app/' . $get_domain['domain_name'] . '/genfile/all-type-' . $get_domain['domain_alias'] . '.json';

    //if (\Storage::get($key_check.'.json')) {
    if (\File::exists($file)) {
      $status = true;
    } else {
      $status = false;
    }

    return $status;
  }

  /***
  **  Check Product Detail Available.
  **  @return response
  ***/
  public static function checkProductAvailable($product_id) {
        $check_product  = \DB::table('products as P')->select(\DB::connection('read_mysql')->raw('P.product_id, P.product_status, 
                          (select SUM(inventory.quantity) from inventory where product_id = P.product_id) AS inventory'))
                        ->where('P.product_id', '=', $product_id)
                        ->where('P.product_status', '=', 1)
                        ->first();

        if (empty($check_product) || ($check_product->inventory <= 0)) {
            \Log::info('Product sold out');
            abort(404, 'soldout'); //*** Product sold out error page... ***//
        }
  }

  /****
  ** Check Parent type
  ** @return response.
  ***/
  public static function checkParentType($typeid, $gender = NULL) {
      //Define Domain and Channel
        $get_domain = get_domain();

        if ($gender == 'women') {
            $file_gender = 'parent'.'-women'.'-'.$get_domain['domain_alias'];
        } elseif ($gender == 'men') {
            $file_gender = 'parent'.'-men'.'-'.$get_domain['domain_alias'];
        } else {
            $file_gender = 'parent'.'-'.$get_domain['domain_alias'];
        }
        //echo $file_gender;
        $status = false;
        $value = Redis::get($file_gender);
        if (is_null($value)) {
           $value = \Storage::get($get_domain['domain_name'].'/catalog/'.$file_gender.'.json');
        }
        
        $get_frontend_type = json_decode($value); //bb_debug($get_frontend_type);
        
        if (is_null($get_frontend_type)) {
            $status = false;
        } else {
            foreach ($get_frontend_type as $rows) {
              $fronttype = \DB::table('front_end_type')->select(\DB::connection('read_mysql')->raw('type_id'))
                                  ->where('type_url','=',$rows)
                                  ->first();
              $parent_type[] = $fronttype->type_id;
            }
            //echo $typeid;bb_debug($parent_type);die();
            if (in_array($typeid, $parent_type)) {
                $status = true;
            }
        }
        
        return $status;
  }

  /****
  ** Check Parent type
  ** @return response.
  ***/
  public static function get_top_banner_mini()
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    
    $name_key = 'top_banner_mini_'.$domain_id;
    $result = Redis::get($name_key);

    if ($result == FALSE)
    {
      $result = \DB::table('banner_topmini')
                          ->where('status','=',1)
                          ->where('domain_id','=',$domain_id)
                          ->orderBy('id', 'DESC')
                          ->first();

      if ($result)
      {
        Redis::set($name_key, $result);
      }

    }

    return $result;
  }
}




