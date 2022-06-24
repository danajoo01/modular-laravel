<?php namespace App\Modules\Product\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Product\Models\Product;
use \App\Modules\Product\Models\Tag;
use \App\Modules\Product\Models\Wishlist;

use \App\Jobs\ProductVisitJob;

use Input;
use Validatoor;
use Session;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Cart;
use Auth;

class ProductDetailController extends Controller {

    /**
     * Display product detail info.
     *
     * @return view
     */
    public function index($parent, $type, $id, $product_name){
        if(!empty(\Request::segment(1)) && !empty(\Request::segment(2)) && \Request::segment(3) == '0'){
          return redirect("/".\Request::segment(1)."/".\Request::segment(2)."");
        }
                
        // Cart::destroy();	
        //Define Domain and Channel
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
    
        //convert channel
        $channel = ($channel == 1 || $channel == 3 || $channel == 5) ? 1 : 2 ;
		
        if ($domain_id == 1) {
            $fields['default'] = 'default_bb';
            $fields['own'] = 'own_bb';
            $fields['default_promo'] = 'default_promo_bb';
            $fields['own_promo'] = 'own_promo_bb';
        } elseif ($domain_id == 2) {
            $fields['default'] = 'default_hb';
            $fields['own'] = 'own_hb';
            $fields['default_promo'] = 'default_promo_hb';
            $fields['own_promo'] = 'own_promo_hb';
        } else {
            $fields['default'] = 'default_sd';
            $fields['own'] = 'own_sd';
            $fields['default_promo'] = 'default_promo_sd';
            $fields['own_promo'] = 'own_promo_sd';
        }

        if (!isset($id) || $id == '' || empty($id)) {
            \Log::alert('Product ID is not found on Product Detail on URL : ' . \Request::fullUrl());
        }

        //***** FOR CHECK PRODUCT STATUS AVAILABLE ****//
        //$check_product = Product::checkProductAvailable($id);
        //***** END FOR CHECK PRODUCT STATUS AVAILABLE ****//

        $product_detail = Product::fetch_product_detail($id, $fields);
        //\Log::error(json_encode($product_detail));

        if (empty($product_detail['fetch_product']) || is_null($product_detail['fetch_product'])){
            abort(404);
        }
            

        // START GET PRODUCT TAG
        if ($domain_id == 1) {
            $product_tag = (isset($product_detail['fetch_product']->product_tag_bb) && $product_detail['fetch_product']->product_tag_bb != NULL) ? explode(',', $product_detail['fetch_product']->product_tag_bb) : array();
        } elseif ($domain_id == 2) {
            $product_tag = (isset($product_detail['fetch_product']->product_tag_hb) && $product_detail['fetch_product']->product_tag_hb != NULL) ? explode(',', $product_detail['fetch_product']->product_tag_hb) : array();
        } else {
            $product_tag = (isset($product_detail['fetch_product']->product_tag_sd) && $product_detail['fetch_product']->product_tag_sd != NULL) ? explode(',', $product_detail['fetch_product']->product_tag_sd) : array();
        }

        $tag['tag_id'] 		= $product_tag;
        $tag['tag_name'] = isset($product_detail['fetch_product']->tag_name) ? $product_detail['fetch_product']->tag_name : NULL;
        $tag['tag_url'] = isset($product_detail['fetch_product']->tag_url) ? $product_detail['fetch_product']->tag_url : NULL;

        $tag_name = Tag::get_tag_solr($tag);
        // END GET PRODUCT TAG
        // product recommended
        $product_recommended = $this->productRecommended($product_detail);
        // end product recommended
        
        
        // check wishlist
        $wishlist_status = false;
        if (!Auth::check()) {
            $user = Auth::user();
            $wishlist_status = $this->checkWishlist($user, $product_detail);
        }

        /*         * * UTM SOURCE ** */
        $utmCookie = Product::gaUtmz();
        //-----------------------------------------------------------------------------

        /*** Product visit ***/
        $page_refferer = \URL::previous() != "" ? \URL::previous() : '';
//        $visit_data = [
//            'product_id'    => $id,
//            'domain_id'     => $domain_id,
//            'channel'       => $channel,
//            'page_referrer' => $page_refferer
//        ];
//        
//        try{
//          $job = (new ProductVisitJob($visit_data))->delay(5);
//          $this->dispatch($job);                          
//        } catch (\Exception $ex) {
//          \Log::error('Product Visit Job Error');       
//        }     
        
        $product_visit = Cache::rememberForever('product-visit', function() {
          return "";
        });
        
        $product_visit_counter = Cache::rememberForever('product-visit-counter', function() {
          return 0;
        });
        
        $app_env = env('APP_ENV', 'production');
        $product_visit_iteration  = env('VISIT_ITERATION', 1000);
        $product_visit            .= '"'.$id.'","'.date('Y-m-d H:i:s').'","'.$domain_id.'","'.$channel.'","'.$page_refferer.'";';
        $product_visit_counter++;
        
        Cache::forever('product-visit', $product_visit);
        Cache::forever('product-visit-counter', $product_visit_counter);
        
        // if ($product_visit_counter >= $product_visit_iteration) {
        //     \Log::critical('Product Visit Counter is reached : ' . $product_visit_counter);

        //     if ($app_env == 'development') {
        //         $dir = "/usr/share/nginx/visit-dev/visit-" . date('Y-m-d-H:i:s') . ".csv";
        //     } else {
        //         $dir = "/usr/share/nginx/visit/visit-" . date('Y-m-d-H:i:s') . ".csv";
        //     }

        //     $write_csv  = writeCSV($product_visit, $dir);
          
        //     if ($write_csv) {
        //         //Clear cache when csv is written
        //         Cache::forget('product-visit');
        //         Cache::forget('product-visit-counter');

        //         \Log::critical("CSV is succesfully created at " . $dir);
        //     } else {
        //         \Log::critical("CSV is failed to create");
        //     }
        // }
        Cache::forget('product-visit');
        Cache::forget('product-visit-counter');
        //-----------------------------------------------------------------------------
        
        // Product Related
        $data['product_related'] = array();
        // $product_detail['fetch_product']->product_related = "251262,251027,250783,250778,250205,253082,";
        if($domain_id == 2){
            if(isset($product_detail['fetch_product']->product_related)){
                $data['product_related'] = Product::fetch_product_related($product_detail['fetch_product']->product_related);
            }
        }
        // Product Related

        $data['fetch_product'] = $product_detail['fetch_product'];
        $mkt_sale_price = $data['fetch_product']->product_price;

        if ($data['fetch_product']) {
            if (isset($data['fetch_product']->product_sale_price) && $data['fetch_product']->product_sale_price > 0) {
                $mkt_sale_price = $data['fetch_product']->product_sale_price;
            }
        }

        $variant_color_name_default = isset($product_detail['fetch_product_color'][0]->variant_color_name) ? $product_detail['fetch_product_color'][0]->variant_color_name : 'color name not set';
        $variant_color_name = isset($product_detail['fetch_product_color'][0]->variant_color_name_custom) ? $product_detail['fetch_product_color'][0]->variant_color_name_custom : $variant_color_name_default;

        //breakdown fetch product
        $data['product_name']                   = isset($product_detail['fetch_product']->product_name) ? $product_detail['fetch_product']->product_name : '';
        $data['product_description']            = isset($product_detail['fetch_product']->product_description) ? trim(strip_tags($product_detail['fetch_product']->product_description)) : '';
        $data['product_brand_name']             = isset($product_detail['fetch_product']->brand_name) ? $product_detail['fetch_product']->brand_name : '';
        $data['product_type_name']              = isset($product_detail['fetch_product']->type_name) ? $product_detail['fetch_product']->type_name : '';

        $data['image_def_name']                 = isset($product_detail['fetch_product_image_def']->image_name) ? $product_detail['fetch_product_image_def']->image_name : '';
        //end breakdown fethc product

        $data['mkt_sale_price']                 = $mkt_sale_price;        

        $data['fetch_product_vari']             = $product_detail['fetch_product_vari'];
        $data['fetch_product_vari_off']         = $product_detail['fetch_product_vari_off'];
        $data['fetch_product_size']             = $product_detail['fetch_product_size'];
        $data['fetch_product_image']            = $product_detail['fetch_product_image'];
        $data['fetch_product_image_def']        = $product_detail['fetch_product_image_def'];
        $data['fetch_product_image_all_off']    = $product_detail['fetch_product_image_all_off'];
        $data['fetch_product_color']            = $product_detail['fetch_product_color'];
        $data['fetch_product_color_others']     = $product_detail['fetch_product_color_others'];
        $data['fetch_product_color_zero']       = $product_detail['fetch_product_color_zero'];                
        $data['wishlist_status']                = $wishlist_status;
        $data['variant_color_name']             = $variant_color_name;
        $data['tag_name']                       = isset($tag_name) ? $tag_name : NULL;
        $data['product_recommended']            = isset($product_recommended) ? $product_recommended : NULL;
        
        //count all inventory
        $allInventory = 0;        
        if(isset($product_detail['fetch_product_vari']->inventory)){
            $allInventory += $product_detail['fetch_product_vari']->inventory;
        }
        
        if(isset($product_detail['fetch_product_vari_off'])){
            foreach($product_detail['fetch_product_vari_off'] as $row){
                $allInventory += isset($row->inventory) ? $row->inventory : 0;    
            }            
        }
        
        $data['product_is_oos']                 = ($product_detail['fetch_product']->product_status == 2 || $allInventory == 0) ? true : false;       
        //dd($data['variant_color_name']);    
        // dd($data['fetch_product_image_def']);
        return get_view('product', 'productdetail.index', $data);
    }
	
	
	/**
	***	Set Wishlist
	**	insert/delete wishlist product
	**	@return true/false, Json.
	*/
  public function set_wishlist(Request $request) {
    // DEFINE DOMAIN
    $get_domain = get_domain();
    $channel = $get_domain['channel'];
    $domain = $get_domain['domain'];
    $domain_id = $get_domain['domain_id'];
    $domain_alias   = $get_domain['domain_alias'];

    $result = 'error';
    if (!Auth::check()) {
      $result = 'not-login';
      
      return json_encode($result);
    }

    $user = Auth::user();
    $customer_email         = $user->customer_email;
    $customer_id      = $user->customer_id;
    $product_id       = $request->get('product_id');
    $type             = $request->get('type');
    $variant_color_id = ($request->get('variant_color_id')) ? $request->get('variant_color_id') : NULL;
    $time             = microtime(true); // Gets microseconds

    if ($customer_id <> '' && $product_id <> '') {
      if ($type == 1) {
        //cek product_id
        $cek = Wishlist::get_count_wishlist($customer_id, $domain_id, $product_id, $variant_color_id);
        if (count($cek) == 0) {
          $wishlist = new Wishlist();
          $wishlist->customer_id = $customer_id;
          $wishlist->product_id = $product_id;
          $wishlist->product_variant_color_id = $variant_color_id;
          $wishlist->domain_id = $domain_id;
          $wishlist->created_date = date("Y-m-d H:i:s");
          if ($wishlist->save()) {
            $result = 'success';
          }
          \Log::info('Add to wishlist ' . $product_id . ': ' . (microtime(true) - $time) . 's');
        } else {
          $result = "exist";
        }
      } else if ($type == 2) {
        $cek = Wishlist::get_count_wishlist($customer_id, $domain_id, $product_id, $variant_color_id);
        if (count($cek) > 0) {
            try {
                $wishlist = Wishlist::where('id', '=', $cek->id)->first();
                if ($wishlist->delete()) {
                    $cacheWishlist          = 'wishlist'.$domain_alias.'-' . $customer_id . '-' . $customer_email . '-page:';  
                    clearRedisContains($cacheWishlist);               
                    $result = 'success';
                }
                \Log::info('Remove From wishlist ' . $product_id . ': ' . (microtime(true) - $time) . 's');
            } catch (\Exception $e) {
                \Log::alert('Error while removing '.$product_id.' from wishlist. Count wishlist: '.count($cek));
            }
            
        } else {
          $result = 'not-exist';
        }
      }
    }

    return json_encode($result);
  }

     /**
	***	Ajax GET images and Size product detail
	**	fetch images and size product detail by variant color id.
	** 	@params product_id, variant_color_id
	**	@return	JSON : image html, size html.
	*/
	public function get_image_color(Request $request) {
		$get_domain = get_domain();		
		$product_id = $request->get('product_id');
                               
                //check isset prodid
                if(!isset($product_id) || $product_id==''){
                  return response()->json(array());                
                }
                
		$variant_color_id = $request->get('variant_color_id');
		
		$return = NULL;		
		$image	= NULL;		
		$where['pid']               = $product_id;
		$where['variant_color_id']  = $variant_color_id;
    
		//*** Fetch Images ***********************//
                $core_selector  = getCoreSelector("product_images");
		$get_imagesSolr = get_active_solr($core_selector, $query = null, $where, $limit = 5, $offset = null, $order = null, $group = null)->docs;
		$get_img_def    = search_in_array($get_imagesSolr,"default_image",1);
		$get_image_def  = array_shift($get_img_def);     
    $color_name = '';
    if($get_image_def){
      $color_name = isset($get_image_def->variant_color_name_custom) ? $get_image_def->variant_color_name_custom : $get_image_def->variant_color_name;    		                                  
    }       
                		
		$data['get_image_def']  = $get_image_def;
		$data['get_imagesSolr'] = $get_imagesSolr;
		$data['asset_path']     = ASSETS_PATH;
		$data['color_name']     = $color_name;

		//******************************************************************//
		
		//*** Fetch Size ********************************//
    $core_selector  = getCoreSelector("product_detail");
		$get_sizeSolr   = get_active_solr($core_selector, $query = null, $where, $limit = 10, $offset = null, $order = null, $group = null)->docs;
		$size	= NULL;
		if ($get_sizeSolr) {
			$data['get_sizeSolr'] = $get_sizeSolr;
		}
		//*****************************************************************//
				
		return response()->json($data);
	}
	
	/**
	***	Ajax GET images and Size product detail for mobile web
	**	fetch images and size product detail by variant color id.
	** 	@params product_id, variant_color_id
	**	@return	JSON : image html, size html.
	*/
	public function get_image_color_mobile(Request $request) {
		$get_domain = get_domain();
		$product_id = $request->get('product_id');
                
    //check isset prodid
    if(!isset($product_id) || $product_id==''){
      return response()->json(array());                
    }
                
		$variant_color_id = $request->get('variant_color_id');
		
		$return = NULL;		
		$image	= NULL;		
		$where['pid'] = $product_id;
		$where['variant_color_id'] = $variant_color_id;
		
		//*** Fetch Images ***********************//
    $core_selector = getCoreSelector("product_images");
		$get_imagesSolr = get_active_solr($core_selector, $query = null, $where, $limit = 5, $offset = null, $order = null, $group = null)->docs;
		$get_img_def = search_in_array($get_imagesSolr,"default_image",1);
		$get_image_def = array_shift($get_img_def);
                $color_name = '';
                if($get_image_def){
                    $color_name = isset($get_image_def->variant_color_name_custom) ? $get_image_def->variant_color_name_custom : $get_image_def->variant_color_name;    
                    $image .= '<li ><img src="'.ASSETS_PATH.'upload/product/zoom/'.$get_image_def->image_name.'"></li>';		
                    
                    if(isset($get_imagesSolr)){
                        foreach ($get_imagesSolr as $rows) {
                            if ($rows->id != $get_image_def->id) {
                                $image .= '<li><img src="'.ASSETS_PATH.'upload/product/zoom/'.$rows->image_name.'"></li>';
                            }
                        }    
                    }                                        
                }
							
		
		//******************************************************************//
		
		//*** Fetch Size ********************************//
    $core_selector  = getCoreSelector("product_detail");
		$get_sizeSolr   = get_active_solr($core_selector, $query = null, $where, $limit = 10, $offset = null, $order = null, $group = null)->docs;
		$size	= NULL;
		if ($get_sizeSolr) {
                    $total_inventory = 0;
                    $size .= '<ul>';
                    
                    foreach($get_sizeSolr as $rows) {                                                

                        try{
                            $disabled = ($rows->inventory <= 0) ? "disabled":"";
                            $titleoos = ($rows->inventory <=0) ? "title='Habis Terjual'" : "";
                            $greybg = ($rows->inventory <= 0) ? 'style="background-color:#dedede;"' : '';
                            $onclicksku = ($rows->inventory > 0) ? 'onclick="getSKU(this.id); _gaq.push([\'_trackEvent\',\'Product\',\'Button\',\'sizeSelect\']);"' : '';

                            $prod_size = isset($rows->product_size) ? $rows->product_size : NULL;
                            $prod_size_url = isset($rows->product_size_url) ? $rows->product_size_url : NULL;

                            $size_url   = isset($prod_size_url) ? $prod_size_url : str_replace(' ','_',strtolower($prod_size));
                            $size_name  = isset($prod_size) ? $prod_size : str_replace('_',' ',strtoupper($prod_size_url));

                            if(isset($size_name) && isset($size_url)){
                                $size .= '<li '.$titleoos.'><div '.$greybg.'><input type="radio" name="size_category" value="'.$size_name.'" id="size-'.$size_url.'" class="size-filter size-'.$prod_size_url.'" '.$disabled.'><label for="size-'.$prod_size_url.'" id="'.$rows->product_sku.'" '.$onclicksku.'>'.$prod_size.'</label></div></li>';    
                            }


                            $total_inventory = $total_inventory + $rows->inventory;                            
                        } catch (\Exception $ex) {
                            \Log::error($ex);
                            \Log::error('error Exception with URI : ' . \Request::fullUrl());       
                        }                            

                    }
//                    if ($total_inventory <= 0) {
//                        $size .= "<span style='font:bold; color:red'>Maaf, Stok Barang Habis</span><br>";
//                    }
                    $size .= '</ul>';
                        
                    if($color_name != ''){
                        $size .= '<input type="hidden" name="variant_color_name" id="variant_color_name" value="'.$color_name.'">';    
                    }
                    
                    if(isset($get_image_def->image_name)){
                        $size .= '<input type="hidden" name="image_name" id="image_name" value="'.$get_image_def->image_name.'">';    
                    }
                    
		}
		//*****************************************************************//
		
		$return['image'] = $image;
		$return['size']  = $size;
		
		echo json_encode($return);
	}
	
	/**
	***	Ajax Add to Cart
	**	add to cart process to add in cart session.
	** 	@params $_POST parameter.
	**	@return	HTML.
	*/
	public function add_to_cart(Request $request) {
		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
		
		$params['product_id']				= $request->get('product_id');
		$params['product_ori_price']		= $request->get('product_price');       
        $params['product_sale_price'] 		= $request->get('product_sale_price');
        $params['product_special_price'] 	= $request->get('product_special_price');		
		if ($params['product_sale_price'] != 0) {
			 $params['product_price'] 	= $params['product_sale_price'];
		} else {
			 $params['product_price'] 	= $params['product_ori_price'];
		}		
		$params['product_weight'] 		= $request->get('product_weight');        
        $params['product_name'] 		= $request->get('product_name');
        $params['type_id'] 				= $request->get('type_id');
        $params['parent_type_id'] 		= $request->get('parent_type_id');
        $params['type_id_real'] 		= $request->get('type_id_real');
        $params['parent_type_id_real'] 	= $request->get('parent_type_id_real');
        $params['brand_id'] 			= $request->get('brand_id');
        $params['SKU'] 					= str_replace('or', '/', $request->get('SKU'));
        $params['quantity'] 			= $request->get('quantity');        
        $params['color_category'] 		= $request->get('variant_color_id');
        $params['size_category'] 		= str_replace('or', '/', $request->get('size_category'));
        $params['image_name'] 			= $request->get('image_name');
        $params['product_inv'] 			= $request->get('product_inv');
        $params['brand_name'] 			= $request->get('brand_name');
        $params['variant_color_name'] 	= $request->get('variant_color_name');
        $params['variant_color_id'] 	= $request->get('variant_color_id');		
        $params['product_front_end_type'] = $request->get('product_front_end_type');
        $params['product_type_url'] 	= $request->get('product_type_url');
        $params['image_name']		 	= $request->get('image_name');
        $params['product_gender']		 	= $request->get('product_gender');
        
        //s: set get special promo
        $params['promo_name'] 			= $request->get('Promo');
        $params['promo_id'] 			= $request->get('Promo_ID');

        // SET TRACKING SALE
        $tracking_sale					= $request->get('sale_tracking');

		//bb_debug($tracking_sale);die();
		// CHECK CONTAIN "+" OR NOT
        $trc_sale_post = strpos($tracking_sale, ' ');
		
		// SET VARIABLE TRACKING SALE FOR DESKTOP
		if($trc_sale_post === false) {
			$parent_track_sale = $tracking_sale;
			$child_track_sale = NULL;
		}
		else {
			//list($parent_category, $category) = explode('+', $tracking_sale);
			list($parent_track_sale, $child_track_sale) = explode(' ', $tracking_sale);
		}
		
		$params['parent_track_sale'] 	= ucwords($parent_track_sale);
        $params['child_track_sale'] 	= ucwords($child_track_sale);
		//dd($params);die();
        $addcarts = FALSE;
        $errormsg = NULL;

        if (!empty($params['size_category'])) {
        	$addcarts = Product::addtocart($params);
        } else {
        	$errormsg = "size_null";
        }
		
		$totalcarts = Cart::count();
		
		$bags = NULL;
		if ($addcarts == TRUE) {
			$carts = Cart::content();							
			if ($carts) {
				$index = 0;
				foreach ($carts as $row) {
					// $bags .= '<li id="bags-item">';
					// $bags .= '<div class="nav-check-img left"><img src="'.IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH.$row->options->image.'"></div>';
					// $bags .= '<div class="nav-check-detail left">';
					// $bags .= '<div class="item-info">';
					// $bags .= '<h4>'.$row->name.'</h4>';
					// $bags .= '<small>'.$row->options->brand_name.'</small>';
					// $bags .= '<div class="detail"><b>Color</b><p>: '.$row->options->color_name.'</p></div>';
					// $bags .= '<div class="detail"><b>Size</b><p>: '.$row->options->size.'</p></div>';
					// $bags .= '<div class="detail"><b>QTY</b><p>: '.$row->qty.'</p></div>';
					// $bags .= '<div class="detail"><strong>IDR '.number_format(($row->price), 0, '.', '.').'</strong></div>';
					// $bags .= '</div></div>';
					// $bags .= '</li>';
                    $bags .= '<li id="bags-item">';
                    $bags .= '<a href="#">';
                    $bags .= '<img src="'.IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH.$row->options->image.'">';
                    $bags .= '<div class="cart-list-detail">';
                    $bags .= '<h2>'.$row->name.'</h2>';
                    $bags .= '<p><span>color</span>: '.$row->options->color_name.'</p>';
                    $bags .= '<p><span>Size</span>: '.$row->options->size.'</p>';
                    $bags .= '<p><span>Quantity</span>: '.$row->qty.'</p>';
                    $bags .= '<p class="price">IDR '.number_format(($row->price), 0, '.', '.').'</p>';
                    $bags .= '</div></a></li>';
					$index ++;
				}
			} else {
				 $bags .= '<li id="bags-item">Tas Belanja Anda Kosong</li>';
			}
		}	
		
		$return['bags'] = $bags;
		$return['t_qty'] = $totalcarts;
		$return['errormsg'] = $errormsg;
		
		echo json_encode($return);
	}
	
	private function checkWishlist($user,$product_detail) {
		$wishlist_status = FALSE;
		
		if ($user) {
			$product_wishlist = Wishlist::where('customer_id','=',$user->customer_id)
								->where('product_id','=',$product_detail['fetch_product']->pid)
								->first();
								
			if ($product_wishlist) {
				$wishlist_status = TRUE;
			}
		}
		
		return $wishlist_status;
	}
	
	private function productRecommended($product) {
            if(isset($product['fetch_product']->product_recommended) && !empty($product['fetch_product']->product_recommended)){
                $str_prod_rec = $product['fetch_product']->product_recommended;
                $arr_prod_rec = explode(',', $str_prod_rec);
                $id_recomend = trim(implode('+OR+', $arr_prod_rec));
                $where_recommended['pid'] = "(".$id_recomend.")";
                $where_recommended['product_gender'] = $product['fetch_product']->product_gender;

                $product_recommended = Product::fetch_product_recommended($query=NULL, $where_recommended, $rows = 5);

                return $product_recommended;   
            }else{
                return null;
            }            
	}

    public function migrateFileProductVisit()
    {
        $product_visit = "";

        $product_migrate = \DB::table('product_visit_migrate')
                          ->orderBy('id', 'desc')
                          ->first();

        $id_last = $product_migrate->id_old;
        
        if (Cache::has('product-visit-migrate-counter'))
        {
            $product_visit_counter = Cache::get('product-visit-migrate-counter');
        }else{
            $product_visit_counter = Cache::rememberForever('product-visit-migrate-counter', function() use ($id_last) {
                return $id_last;
            });
        }
        
        $limit = 20000;
        $product_visit_iteration = 100000;

        $products = \DB::table('product_visit')
                          ->where('id', '>', $product_visit_counter)
                          ->limit($limit)
                          ->get();

        $app_env = env('APP_ENV', 'production');
        
        foreach ($products as $product) {
            $product_visit  .= '"'.$product->product_id.'","'.$product->date.'","'.$product->domain_id.'","'.$product->channel.'","'.$product->page_referrer.'","'.$product->id.'";';

            $last_id = $product->id;  
        }
        
        $last_id_next = $product_visit_counter + $limit;

        $product_visit_counter = $product_visit_counter + $limit;

        Cache::forever('product-visit-migrate-counter', $last_id);
        
        //if ($product_visit_counter <= $product_visit_iteration) {
            \Log::critical('Product Visit Counter is reached : ' . $last_id);

            if ($app_env == 'development') {
                $dir = "/usr/share/nginx/visit-migration-dev/visit-" . date('Y-m-d-H:i:s') . ".csv";
            } else {
                $dir = "/usr/share/nginx/visit-migration/visit-" . date('Y-m-d-H:i:s') . ".csv";
            }
            
            $write_csv  = writeCSV($product_visit, $dir);
          
            if ($write_csv) {
                //Clear cache when csv is written
                
                \Log::critical("CSV is succesfully created at " . $dir);

                $redirectTo = '/migrate-file-visit/'.$last_id;

                //return redirect($redirectTo);
                echo "done";
            } else {
                \Log::critical("CSV is failed to create");
            }
        //}else{
            \Log::critical("Created CSV done ID : ".$last_id);

            //Cache::forget('product-visit-migrate-counter');
        //}
    }
}
