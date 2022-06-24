<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\Product;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Checkout\Models\PromotionHelper;
use Auth;
use Cart;
use DB;
use Log;
use DateTime;
use Session;

class OrderItem extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'order_item'; //Define your table name

	protected $primaryKey = 'order_item_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['order_item_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Checkout\Models\Model_name');
	// }

  public static function addDraftOrder()
  {
    $get_domain     = get_domain();
    $domain_id      = $get_domain['domain_id'];
    $customer_id    = Auth::user()->customer_id;
    $customer_email = Auth::user()->customer_email;

    //Clear Order Item
    DB::table('order_item')
      ->where('customer_id', '=', $customer_id)
      ->where('domain_id', '=', $domain_id)
      ->where('purchase_status', '=', 0)
      ->delete();
    //End Clear Order Item

    $cart_content = Cart::content();
    $status_inventory = TRUE;
    $order = array();
    $promo_page = "";
    $brand_page = "";
    $utm_source = "";
    foreach ($cart_content as $cart){
      //Check Inventory
      $params_check_inventory['SKU'] = $cart->id;
      $check_inventory = CheckoutCart::checkInventory($params_check_inventory);
      $status_inventory = $check_inventory['result'];
      
      if(!$status_inventory){
        $data['order'] = $order;
        $data['status_inventory'] = $status_inventory;
        
        return $data;
      }
      //End Check Inventory

      $product_id                   = $cart->options->product_id;
      
      $fetch_product                = Product::where('product_id', '=', $product_id)->first();
      $product_price                = $fetch_product->product_price;
      $product_sale_price           = $fetch_product->product_sale_price;
      $product_special_price        = 0;
      $product_ownership            = $fetch_product->product_ownership;
      $item_paid_price              = set_price($fetch_product->product_price, $fetch_product->product_sale_price);
      $tax                          = $item_paid_price - ($item_paid_price / 1.1);

      $data_product_ownership['where']['id']  = $product_ownership;
      $fetch_product_ownership                = Product::fetchProductOwnership($data_product_ownership); //fetch data product ownership from DB
      $product_ownership_type                 = $fetch_product_ownership[0]->product_ownership_type;

      //Skipped Process
      //Fetch product special price from session product_special_price_list
      //Fetch product sale price from session utm_source
      //End Skipped Process

      //Set Variable for Order Item
      $order['product_id']              = $product_id;
      $order['SKU']                     = $cart->id;
      $order['quantity']                = $cart->qty;
      $order['each_price']              = $product_price;
      $order['discount_price']          = $product_sale_price;
      $order['special_price']           = $product_special_price;
      $order['total_price']             = $product_price;
      $order['total_discount_price']    = $product_sale_price;
      $order['total_special_price']     = $product_special_price;
      $order['item_paid_price']         = $item_paid_price;
      $order['tax']                     = $tax;
      $order['customer_id']             = $customer_id;
      $order['customer_email']          = $customer_email;
      $order['product_ownership']       = $product_ownership;
      $order['product_ownership_type']  = $product_ownership_type;
      $order['order_status_item']       = 0;
      $order['utm_source']              = ($cart->options->utm_source == NULL) ? NULL : $cart->options->utm_source ;
      $order['utm_campaign']            = ($cart->options->utm_campaign == NULL) ? NULL : $cart->options->utm_campaign ;
      $order['utm_medium']              = ($cart->options->utm_medium == NULL) ? NULL : $cart->options->utm_medium ;
      $order['domain_id']               = $domain_id;
      $order['last_update']             = date("Y-m-d H:i:s");
      $order['promo_id']                = ($cart->options->promo_id == NULL) ? NULL : $cart->options->promo_id ;
      $order['promo_name']              = ($cart->options->promo_name == NULL) ? NULL : $cart->options->promo_name;
      $order['parent_category']         = $cart->options->parent_track_sale;
      $order['category']                = ($cart->options->child_track_sale == NULL) ? NULL : $cart->options->child_track_sale ;
      //End Set Variable for Order Item

      //Promotion Data
      if($order['promo_name'] == 'Special'){
        $promo_page = $promo_page.$order['promo_id'].',';
      }else if($order['promo_name'] == 'Brand'){
        $brand_page = $brand_page.$order['promo_id'].',';
      }
      $utm_source = ($order['utm_source'] != "") ? $utm_source.','.$order['utm_source'] : "" ;
      //End Promotion Data

      //Add Order Item
      $data_add_order_item['order'] = $order;
      $add_order_item = OrderItem::addOrderItem($data_add_order_item);
      
      if(!$add_order_item){ 
        //Failed to Sync Order Item
        $data['order'] = array();
        $data['status_inventory'] = FALSE;
        
        return $data;
      }
      //End Add Order Item
    }

    //Set Order Session
    session('promo_page', rtrim($promo_page, ","));
    session('brand_page', rtrim($brand_page, ","));
    session('utm_source', $utm_source);
    //End Set Session Order Session

    $data['order'] = $order;
    $data['status_inventory'] = $status_inventory;

    return $data;
  }

  public static function fetchOrderItem()
  {
    if(empty(Auth::user())){
      return array();
    }
    
    $customer_id = Auth::user()->customer_id;
    
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    
    DB::enableQueryLog();
    $fetch_order_item = DB::table('order_item')
      ->select(\DB::raw('
        order_item.*,
        product_variant.variant_color_id,
        product_variant.product_size,
        product_variant.variant_color_name,
        products.product_name,
        products.product_weight,
        products.product_type,
        products.product_brand,
        products.front_end_type,
        brand.brand_name
      '))
      ->leftJoin('product_variant', 'product_variant.SKU', '=', 'order_item.SKU')
      ->leftJoin('products', 'products.product_id', '=', 'order_item.product_id')
      ->leftJoin('brand', 'brand.brand_id', '=', 'products.product_brand')
      ->where('order_item.domain_id', '=', $domain_id)
      ->where('order_item.customer_id', '=', $customer_id)
      ->where('order_item.purchase_status', '=', 0)
      ->get();
    
    return ($fetch_order_item) ? $fetch_order_item : array() ;
  }
  
  public static function fetchOrderItemValue($order_item_id)
  {
    $fetch_order_item_value = DB::table('order_item')
      ->select(\DB::raw('*'))
      ->where('order_item_id', '=', $order_item_id);
    
    return $fetch_order_item_value->first();
  }
  
  public static function generateFrontEndType($front_end_type)
  {
    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    $type_url   = "";
    
    if(isset($front_end_type)){
      $front_end_type_url_data = explode(",", $front_end_type);
      foreach(array_filter($front_end_type_url_data) as $type_id){
        //Fetch Front End Type SOLR
        $core_selector  = getCoreSelector("front_end_type");
        $where['id']    = $type_id;
        $solr_data      = get_active_solr($core_selector, $query=null, $where, null, null, null, null);
        try {
          $solr_type_url  = $solr_data->docs;
          if (!empty($solr_type_url)) {
            foreach ($solr_type_url as $key => $value) {
              $type_url = $type_url.$value->type_url.',';
            }
          }
        } catch (\Exception $e) {
          \Log::error($e);
          \Log::error('Error on generateFrontEndType');
        }
        //End Fetch
      }
    }
    
    return $type_url;
  }
  
  public static function generateProductInventory($SKU = NULL)
  {
    $get_domain         = get_domain();
    $domain_id          = $get_domain['domain_id'];
    $product_inventory  = 0;
    
    if($SKU != NULL){
      //Fetch Product Inventory
      $core_selector = getCoreSelector("product_detail");
      
      $where['product_sku'] = $SKU;
      
      $solr_data = get_active_solr($core_selector, $query = null, $where, null, null, null, null);
      
      try {
        if(isset($solr_data->docs[0])){
          $solr_product_inventory = $solr_data->docs[0];
          $product_inventory      = isset($solr_product_inventory->inventory) ? $solr_product_inventory->inventory : "" ;
        }else{
          //\Log::alert('Failed to fetch inventory on generateProductInventory SOLR. Product SKU : ' .$SKU);
        }
      } catch (\Exception $e) {
        \Log::error($e);
        \Log::error('Error on generateProductInventory SOLR');
      }
      //End Fetch Product Inventory
    }
    
    return $product_inventory;
  }
  
  public static function generateProductImage($data)
  {
    $get_domain       = get_domain();
    $domain_id        = $get_domain['domain_id'];
    $product_id       = isset($data['product_id']) ? $data['product_id'] : NULL ;
    $variant_color_id = isset($data['variant_color_id']) ? $data['variant_color_id'] : NULL ;
    $product_image    = "";
    
    if($product_id != NULL && $variant_color_id != NULL){
      //Fetch Product Image SOLR
      $core_selector              = getCoreSelector("product_images");
      
      $where['pid']               = $product_id;
      $where['variant_color_id']  = $variant_color_id;
      
      $solr_data = get_active_solr($core_selector, $query = null, $where, null, null, null, null);
      
      try {
        if(isset($solr_data->docs[0])){
          $solr_product_image = $solr_data->docs[0];
          $product_image      = isset($solr_product_image->image_name) ? $solr_product_image->image_name : "" ;
        }else{
          \Log::alert('Failed to fetch image on generateProductImage SOLR. Product ID : ' .$product_id. ' | Variant Color ID: ' .$variant_color_id. '');
        }
      } catch (\Exception $e) {
        \Log::error($e);
        \Log::error('Error on generateProductImage SOLR');
      }
      //End Fetch
    }
    
    return $product_image;
  }

  public static function syncOrderItem()
  {
    DB::enableQueryLog();
    $fetch_order_item = OrderItem::fetchOrderItem();

    if(!empty($fetch_order_item)){
      foreach ($fetch_order_item as $order_item) {
        //Check Inventory
        $check_inventory = Self::generateProductInventory(str_replace('or', '/', $order_item->SKU));
        if($check_inventory <= 0){
          continue; //skip proses add to cart if product is not available anymore
        }
        //End Check Inventory
        
        $type_url       = Self::generateFrontEndType($order_item->front_end_type);
        $fetch_products = Product::where('product_id', '=', $order_item->product_id)->first();
        
        //Fetch Image
        $query_images['product_id']       = $order_item->product_id;
        $query_images['variant_color_id'] = $order_item->variant_color_id;
        $product_images = Self::generateProductImage($query_images);
        //End Fetch Image
        
        $params['product_id']             = $order_item->product_id;
        $params['product_ori_price']      = $fetch_products->product_price;
        $params['product_sale_price']     = $fetch_products->product_sale_price;
        $params['product_special_price']  = $fetch_products->product_special_price;
        $params['product_price']          = set_price($fetch_products->product_price, $fetch_products->product_sale_price) ;
        $params['product_weight']         = $order_item->product_weight;
        $params['product_name']           = $order_item->product_name;
        $params['type_id']                = $order_item->product_type;
        $params['parent_type_id']         = $order_item->product_type;
        $params['brand_id']               = $order_item->product_brand;
        $params['SKU']                    = str_replace('or', '/', $order_item->SKU);
        $params['quantity']               = $order_item->quantity;
        $params['color_category']         = $order_item->variant_color_id;
        $params['size_category']          = $order_item->product_size;
        $params['image_name']             = $product_images;
        $params['brand_name']             = $order_item->brand_name;
        $params['variant_color_name']     = $order_item->variant_color_name;
        $params['variant_color_id']       = $order_item->variant_color_id;
        $params['product_front_end_type'] = $order_item->front_end_type;
        $params['product_type_url']       = rtrim($type_url, ",");
        $params['promo_name']             = $order_item->promo_name;
        $params['promo_id']               = $order_item->promo_id;

        CheckoutCart::addCart($params);
      }
    }
  }

  public static function addOrderItem($data = NULL)
  {
    if($data != NULL){
      for ($item = 1; $item <= $data['order']['quantity']; $item++) {
        $data_insert[] = array(
          'product_id'              => $data['order']['product_id'],
          'SKU'                     => $data['order']['SKU'],
          'quantity'                => 1,
          'customer_id'             => $data['order']['customer_id'],
          'customer_email'          => $data['order']['customer_email'],
          'each_price'              => $data['order']['each_price'],
          'discount_price'          => $data['order']['discount_price'],
          'special_price'           => $data['order']['special_price'],
          'total_price'             => $data['order']['total_price'],
          'total_discount_price'    => $data['order']['total_discount_price'],
          'total_special_price'     => $data['order']['total_special_price'],
          'voucher_value'           => 0,
          'discount_value'          => 0,
          'item_paid_price'         => $data['order']['item_paid_price'],
          'tax'                     => $data['order']['tax'],
          'last_update'             => isset($data['order']['last_update']) ? $data['order']['last_update'] : NULL,
          'purchase_status'         => 0,
          'domain_id'               => $data['order']['domain_id'],
          'promo_id'                => isset($data['order']['promo_id']) ? $data['order']['promo_id'] : NULL,
          'promo_name'              => isset($data['order']['promo_name']) ? $data['order']['promo_name'] : NULL,
          'product_ownership'       => isset($data['order']['product_ownership']) ? $data['order']['product_ownership'] : NULL,
          'product_ownership_type'  => $data['order']['product_ownership_type'],
          'utm_source'              => $data['order']['utm_source'],
          'utm_campaign'            => $data['order']['utm_campaign'],
          'utm_medium'              => $data['order']['utm_medium'],
          'parent_category'         => isset($data['order']['parent_category']) ? $data['order']['parent_category'] : NULL,
          'category'                => isset($data['order']['category']) ? $data['order']['category'] : NULL,
        );
      }
      
      if(isset($data_insert) && !empty($data_insert)){
        DB::table('order_item')->insert($data_insert);
        return true;
      }
      
    }
    
    return false;
  }
  
  public static function updateOrderItemStatus($data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateOrderItemStatus: Started');
    
    $domain_id      = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $SKU            = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $updatedSKU     = session('updateOrderItemStatusSKU') ? session('updateOrderItemStatusSKU') : array() ;
    
    if($domain_id == NULL){
      Log::notice('Process updateOrderItemStatus: Domain ID is missing.');
      return false;
    }
    
    if($SKU == NULL){
      Log::notice('Process updateOrderItemStatus: SKU is missing.');
      return false;
    }
    
    if($purchase_code == NULL){
      Log::notice('Process updateOrderItemStatus: Purchase Code is missing.');
      return false;
    }
    
    if(in_array($SKU, $updatedSKU)){
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateOrderItemStatus: Success [SKU: '.$SKU.'] is already updated. Executed Time: '.$time_executed);
      return true;
    }
    
    $update_item = [];
    $update_item['purchase_code']   = $purchase_code;
    $update_item['purchase_status'] = 1;
    $update_order_item = DB::table('order_item')
      ->where('SKU', str_replace('or', '/', $SKU))
      ->where('customer_id', $customer_id)
      ->where('purchase_status', 0)
      ->where('domain_id', $domain_id)
    ->update($update_item);
    
    if(!$update_order_item){
      Log::notice('Process updateOrderItemStatus: Update failed.');
      return false;
    }
    
    //Session to prevent duplicate update
    session()->push('updateOrderItemStatusSKU', $SKU);

    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updateOrderItemStatus: Success [SKU: '.$SKU.']. Executed Time: '.$time_executed);
    return true;
  }
  
  public static function updateOrderItem($filter, array $data)
  {
  	$update_order_payment = DB::table('order_item');
    
    if($filter['with'] == 'purchase_code'){
      $update_order_payment->where('purchase_code', $filter['value']);
    }else if($filter['with'] == 'order_item_id'){
      $update_order_payment->where('order_item_id', $filter['value']);
    }
    
    $update_order_payment->update($data);

    return $update_order_payment;
  }
  
  public static function updateOrderItemHistory($purchase_code, array $data)
  {
  	$update_order_payment = DB::table('order_item_history')
      ->where('purchase_code', $purchase_code)
      ->update($data);

    return $update_order_payment;
  }
  
  public static function setItemPrice($order_item) {
    if(isset($order_item->real_price) || property_exists($order_item, 'real_price')){
      $price = $order_item->real_price;
    }else{
      $price = $order_item->each_price;
      if($order_item->special_price != NULL && $order_item->special_price != 0){
        $price = $order_item->special_price;
      }else if($order_item->discount_price != NULL && $order_item->discount_price != 0){
        $price = $order_item->discount_price;
      }
    }
    
    return $price;
  }
  
  public static function getTotalPromotionsPurchase($promotions, $fetch_order_item)
  { 
    $total_purchase_value = 0;
    foreach($fetch_order_item as $order_item){
      if(in_array($order_item->order_item_id, $promotions['promotions_order_id'])){
        $total_purchase_value += Self::setItemPrice($order_item);
      }
    }
    
    return $total_purchase_value;
  }
  
  public static function updateFreegiftAutoValue($data = array())
  {
    $time_start = microtime(true);
    
    $fetch_order_item  = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift_auto     = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $total_discount    = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($freegift_auto)){
      //Update Value Process
      foreach($freegift_auto as $key => $values){
        
        $reshape_order_item   = PromotionHelper::reshapeOrderItem($freegift_auto[$key], $fetch_order_item);
        $freegift_auto[$key]  = $reshape_order_item['promotions'];
        $fetch_order_item     = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($freegift_auto[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift_auto[$key], $fetch_order_item, $total_purchase_value);
        
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            $discount_value = 0;
            $order_price = PromotionHelper::setItemPrice($order_item);

            $par_count = array();
            $par_count['order_item_id']     = $order_item->order_item_id;
            $par_count['fetch_order_item']  = $fetch_order_item;
            $par_count['promotions']        = $freegift_auto[$key];
            $count_applied = PromotionHelper::setCountApplied($par_count);

            if($count_applied > 0){

              $par_promo_value = array();
              $par_promo_value['promotions']                  = $freegift_auto[$key];
              $par_promo_value['count_applied']               = $count_applied;
              $par_promo_value['order_item']                  = $order_item;
              $par_promo_value['price']                       = $order_price;
              $par_promo_value['total_purchase_value']        = $total_purchase_value;
              $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
              $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $order_price - $promotions_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $promotions_value       = $order_price;
                $order_item->real_price = 0;
              }
              //End Check
              
              $discount_value += $promotions_value;
              $total_discount += $promotions_value;
              
              if(isset($order_item->temp_discount) || property_exists($order_item, 'temp_discount')){
                $order_item->temp_discount += $discount_value;
              }else{
                $order_item->temp_discount = $discount_value;
              }

              //Update Order Item Value
              $update_item                    = array();
              $update_item['discount_value']  = $order_item->temp_discount ;
              $update_item['last_update']     = date('Y-m-d H:i:s');
              $update_order_item_value = DB::table('order_item')
                ->where('order_item_id', $order_item->order_item_id)
                ->update($update_item);
              
              if(!$update_order_item_value){
                $time_executed  = microtime(true) - $time_start;
                Log::notice('Process updateOrderItemValue: [Freegift Auto] Update order item is failed. Update Data : ' . json_encode($update_item) . ' . Executed Time: '. $time_executed);
              }

              $time_executed  = microtime(true) - $time_start;
              Log::notice('Process updateOrderItemValue: [Freegift Auto] Discount Value : '.$discount_value.'. Executed Time: '. $time_executed);
              //End Update Order Item Value
            }
          }
        }
      }
      //End Update Value Process
    }
    
    $data['fetch_order_item'] = $fetch_order_item;
    $data['total_discount']   = $total_discount;
    
    return $data;
  }
  
  public static function updateVoucherValue($data)
  {
    $time_start = microtime(true);
    
    $fetch_order_item   = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $voucher            = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $total_discount     = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($voucher)){
      
      $reshape_order_item   = PromotionHelper::reshapeOrderItem($voucher, $fetch_order_item);
      $voucher              = $reshape_order_item['promotions'];
      $fetch_order_item     = $reshape_order_item['fetch_order_item'];
      
      $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($voucher, $fetch_order_item);
      
      $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($voucher, $fetch_order_item, $total_purchase_value);

      // Kondisi freecheap
      $cheapItem = array();
      if(isset($voucher["promotions_function"])){
        if($voucher["promotions_function"] == "freeCheapestItems"){
          foreach ($fetch_order_item as $key => $value) {
            $price = PromotionHelper::setItemPrice($value);
            $fetch_order_item[$key]->real_price = $price;
          }

          // sort item berdasarkan harga
          usort($fetch_order_item, function ($a, $b) {
            return $a->real_price - $b->real_price;
          });

          // pisahkan cheap item
          if (isset($fetch_order_item[0]) AND $fetch_order_item[1]) {
            $cheapItem = [$fetch_order_item[0]->order_item_id, $fetch_order_item[1]->order_item_id];
          }
        }
      }
      
      //Insert Value Process  
      foreach($fetch_order_item as $order_item){
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          $order_price = PromotionHelper::setItemPrice($order_item);

          $par_count = array();
          $par_count['order_item_id']     = $order_item->order_item_id;
          $par_count['fetch_order_item']  = $fetch_order_item;
          $par_count['promotions']        = $voucher;
          $count_applied = PromotionHelper::setCountApplied($par_count);

          if($count_applied > 0){

            $par_promo_value = array();
            $par_promo_value['promotions']                  = $voucher;
            $par_promo_value['count_applied']               = $count_applied;
            $par_promo_value['order_item']                  = $order_item;
            $par_promo_value['price']                       = $order_price;
            $par_promo_value['total_purchase_value']        = $total_purchase_value;
            $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
            $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

            $order_item->real_price = $order_price - $promotions_value;

            if(isset($voucher["promotions_function"])){
              if($voucher["promotions_function"] == "freeCheapestItems"){
                if(in_array($order_item->order_item_id, $cheapItem)){
                  $promotions_value = $order_price;
                  $order_item->real_price = $order_price - $promotions_value;
                }
              }
            }

            //Check whether the price is below 0 after promotions value, then set promotions value to item price
            if($order_item->real_price < 0){
              $promotions_value       = $order_price;
              $order_item->real_price = 0;
            }
            //End Check

            $voucher_value  = $promotions_value;
            $total_discount += $promotions_value;
            
            if(isset($order_item->temp_voucher) || property_exists($order_item, 'temp_voucher')){
              $order_item->temp_voucher += $voucher_value;
            }else{
              $order_item->temp_voucher = $voucher_value;
            }

            //Update Order Item Value
            $update_item                  = array();
            $update_item['voucher_value'] = $order_item->temp_voucher;
            $update_item['last_update']   = date('Y-m-d H:i:s');
            $update_order_item_value = DB::table('order_item')
              ->where('order_item_id', $order_item->order_item_id)
              ->update($update_item);
            
            if(!$update_order_item_value){
              $time_executed  = microtime(true) - $time_start;
              Log::notice('Process updateOrderItemValue: [Voucher] Update order item is failed. Update Data : ' . json_encode($update_item) . ' . Executed Time: '. $time_executed);
            }

            $time_executed  = microtime(true) - $time_start;
            Log::notice('Process updateOrderItemValue: [Voucher] Discount Value : '.$voucher_value.'. Executed Time: '. $time_executed);
            //End Update Order Item Value
          }
        }
      }
      //End Insert Value Process
    }
    
    $data['fetch_order_item'] = $fetch_order_item;
    $data['total_discount']   = $total_discount;
    
    return $data;
  }
  
  public static function updateFreegiftValue($data = array())
  {
    $time_start = microtime(true);
    
    $fetch_order_item   = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift           = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    $total_discount     = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($freegift)){
      //Update Value Process
      foreach($freegift as $key => $values){
        
        $reshape_order_item   = PromotionHelper::reshapeOrderItem($freegift[$key], $fetch_order_item);
        $freegift[$key]       = $reshape_order_item['promotions'];
        $fetch_order_item     = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($freegift[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift[$key], $fetch_order_item, $total_purchase_value);
        
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            $discount_value = 0;
            $order_price    = PromotionHelper::setItemPrice($order_item);

            $par_count = array();
            $par_count['order_item_id']     = $order_item->order_item_id;
            $par_count['fetch_order_item']  = $fetch_order_item;
            $par_count['promotions']        = $freegift[$key];
            $count_applied = PromotionHelper::setCountApplied($par_count);

            if($count_applied > 0){

              $par_promo_value = array();
              $par_promo_value['promotions']                  = $freegift[$key];
              $par_promo_value['count_applied']               = $count_applied;
              $par_promo_value['order_item']                  = $order_item;
              $par_promo_value['price']                       = $order_price;
              $par_promo_value['total_purchase_value']        = $total_purchase_value;
              $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
              $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $order_price - $promotions_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $promotions_value       = $order_price;
                $order_item->real_price = 0;
              }
              //End Check

              $discount_value += $promotions_value;
              $total_discount += $promotions_value;
              
              if(isset($order_item->temp_discount) || property_exists($order_item, 'temp_discount')){
                $order_item->temp_discount += $discount_value;
              }else{
                $order_item->temp_discount = $discount_value;
              }

              //Update Order Item Value
              $update_item                    = array();
              $update_item['discount_value']  = $order_item->temp_discount ;
              $update_item['last_update']     = date('Y-m-d H:i:s');
              $update_order_item_value = DB::table('order_item')
                ->where('order_item_id', $order_item->order_item_id)
                ->update($update_item);
              
              if(!$update_order_item_value){
                $time_executed  = microtime(true) - $time_start;
                Log::notice('Process updateOrderItemValue: [Freegift] Update order item is failed. Update Data : ' . json_encode($update_item) . ' . Executed Time: '. $time_executed);
              }

              $time_executed  = microtime(true) - $time_start;
              Log::notice('Process updateOrderItemValue: [Freegift] Discount Value : '.$discount_value.'. Executed Time: '. $time_executed);
              //End Update Order Item Value
            }
          }
        }
      }
      //End Update Value Process
    }
    
    $data['fetch_order_item'] = $fetch_order_item;
    $data['total_discount']   = $total_discount;
    
    return $data;
  }
  
  public static function insertOrderItemLog($purchase_code, $order_item, $value)
  {
    $voucher_value    = isset($value['voucher_value']) ? $value['voucher_value'] : 0 ;
    $discount_value   = isset($value['discount_value']) ? $value['discount_value'] : 0 ;
    $item_paid_price  = isset($value['item_paid_price']) ? $value['item_paid_price'] : 0 ;
    $tax              = isset($value['tax']) ? $value['tax'] : 0 ;
    
    $create_order_item_log = [];
    $create_order_item_log['purchase_code']         = $purchase_code;
    $create_order_item_log['product_id']            = $order_item->order_item_id;
    $create_order_item_log['SKU']                   = $order_item->SKU;
    $create_order_item_log['quantity']              = 1;
    $create_order_item_log['customer_id']           = $order_item->customer_id;
    $create_order_item_log['customer_email']        = $order_item->customer_email;
    $create_order_item_log['each_price']            = $order_item->each_price;
    $create_order_item_log['discount_price']        = $order_item->discount_price;
    $create_order_item_log['special_price']         = $order_item->special_price;
    $create_order_item_log['total_price']           = $order_item->total_price;
    $create_order_item_log['total_discount_price']  = $order_item->total_discount_price;
    $create_order_item_log['total_special_price']   = $order_item->total_special_price;
    $create_order_item_log['voucher_value']         = $voucher_value;
    $create_order_item_log['discount_value']        = $discount_value;
    $create_order_item_log['item_paid_price']       = $item_paid_price;
    $create_order_item_log['tax']                   = $tax;
    $create_order_item_log['purchase_status']       = $order_item->purchase_status;
    $create_order_item_log['order_status_item']     = $order_item->order_status_item;
    $create_order_item_log['last_update']           = $order_item->last_update;
    $create_order_item_log['domain_id']             = $order_item->domain_id;

    $order_item_log_id = DB::table('order_item_log')->insertGetId($create_order_item_log);
    
    if(!$order_item_log_id){
      return false;
    }
    
    return true;
  }
  
  public static function updateOrderItemValue($data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateOrderItemValue: Started');
    
    $purchase_code        = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $fetch_order_item     = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift_auto        = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $voucher              = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $freegift             = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    $discount_value       = 0;
    $voucher_value        = 0;
    
    if(empty($fetch_order_item)){
      Log::notice('Process updateOrderItemValue: Order Item is empty');
      return false;
    }
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      Log::notice('Process updateOrderItemValue: Not using any promotions.');
      
      //Insert Log
      $log_start = microtime(true);
      foreach($fetch_order_item as $order_item){
        $order_price      = set_price($order_item->total_price, $order_item->total_discount_price);
        $order_item_value = OrderItem::where('order_item_id', $order_item->order_item_id)->first();
        
        if(is_null($order_item_value)){
          $log_executed  = microtime(true) - $log_start;
          Log::notice('Process updateOrderItemValue: Order Item ID is not found. Executed Time: '.$log_executed);
          return false;
        }
        
        $item_paid_price  = $order_price - $discount_value - $voucher_value;
        $tax              = $item_paid_price - ($item_paid_price / 1.1);
        
        $value = [];
        $value['voucher_value']   = $voucher_value;
        $value['discount_value']  = $discount_value;
        $value['item_paid_price'] = $item_paid_price;
        $value['tax']             = $tax;
        $insert_log               = Self::insertOrderItemLog($purchase_code, $order_item_value, $value);
        
        $log_executed  = microtime(true) - $log_start;
        if(!$insert_log){
          Log::notice('Process updateOrderItemValue: Insert to order_item_log failed. Executed Time: '.$log_executed);
          return false;
        }else{
          Log::notice('Process updateOrderItemValue: Success insert to order_item_log. Executed Time: '.$log_executed);
        }
      }
      //End Insert Log
      
      return true;
    }
    
    //Clear Order Item Real Price Value
    foreach($fetch_order_item as $order_item){
      unset($order_item->real_price);
    }
    //End Clear
    
    $data['total_discount'] = 0;
    
    $update_freegift_auto_value = Self::updateFreegiftAutoValue($data);
    $update_voucher_value       = Self::updateVoucherValue($update_freegift_auto_value);
    $update_freegift_value      = Self::updateFreegiftValue($update_voucher_value);
    
    //Calculate Item Paid Price & Tax
    foreach($update_freegift_value['fetch_order_item'] as $order_item){
      $order_price      = set_price($order_item->total_price, $order_item->total_discount_price);
      $order_item_value = OrderItem::where('order_item_id', $order_item->order_item_id)->first();
      
      if(empty($order_item_value)){
        Log::notice('Process updateOrderItemValue: Insert to order_item_log failed. Order Item is not found');
        return false;
      }
      
      $discount_value = (isset($order_item->temp_discount) || property_exists($order_item, 'temp_discount')) ? $order_item->temp_discount : 0 ;
      $voucher_value  = (isset($order_item->temp_voucher) || property_exists($order_item, 'temp_voucher')) ? $order_item->temp_voucher : 0 ;
      
      Log::notice('Process updateOrderItemValue: discount_value : '. json_encode($discount_value));
      Log::notice('Process updateOrderItemValue: voucher_value : '. json_encode($voucher_value));
      
      $item_paid_price  = $order_price - $discount_value - $voucher_value;
      $tax              = $item_paid_price - ($item_paid_price / 1.1);
      
      if($discount_value == 0 && $voucher_value == 0){
        Log::notice('Process updateOrderItemValue: Success. Item Paid Price is not updated because discount and voucher value is 0.');
        
        //Insert Log
        $log_start = microtime(true);
        
        $value = [];
        $value['voucher_value']   = $voucher_value;
        $value['discount_value']  = $discount_value;
        $value['item_paid_price'] = $item_paid_price;
        $value['tax']             = $tax;
        $insert_log               = Self::insertOrderItemLog($purchase_code, $order_item_value, $value);
        
        $log_executed  = microtime(true) - $log_start;
        if(!$insert_log){
          Log::notice('Process updateOrderItemValue: Insert to order_item_log failed. Executed Time: '.$log_executed);
          return false;
        }else{
          Log::notice('Process updateOrderItemValue: Success insert to order_item_log. Executed Time: '.$log_executed);
        }
        //End Insert Log
        
        continue;
      }
      
      $update_item                    = array();
      $update_item['item_paid_price'] = ($item_paid_price < 0) ? 0 : $item_paid_price;
      $update_item['tax']             = ($tax < 0) ? 0 : $tax;
      $update_item['last_update']     = date('Y-m-d H:i:s');

      $update_order_item_value = DB::table('order_item')
        ->where('order_item_id', $order_item->order_item_id)
      ->update($update_item);
      
      if(!$update_order_item_value){
        Log::notice('Process updateOrderItemValue: Update order item failed.');
        return false;
      }
      
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateOrderItemValue: update_item : '. json_encode($update_item));
      Log::notice('Process updateOrderItemValue: Update order_item is success. Executed Time: '. $time_executed);
      
      //Insert Log
      $log_start = microtime(true);

      $value = [];
      $value['voucher_value']   = $voucher_value;
      $value['discount_value']  = $discount_value;
      $value['item_paid_price'] = $item_paid_price;
      $value['tax']             = $tax;
      $insert_log               = Self::insertOrderItemLog($purchase_code, $order_item_value, $value);

      $log_executed  = microtime(true) - $log_start;
      if(!$insert_log){
        Log::notice('Process updateOrderItemValue: Insert to order_item_log failed. Executed Time: '.$log_executed);
        return false;
      }else{
        Log::notice('Process updateOrderItemValue: Success insert to order_item_log. Executed Time: '.$log_executed);
      }
      //End Insert Log
    }
    //End Calculate
    
    return $update_freegift_value;
  }
  
  public static function updateOrderItemVeritrans(array $data, $charge_veritrans)
  {
    $time_start = microtime(true);
    Log::notice('Process updateOrderItemVeritrans: Started');
    
    $payment_method = $data['payment_method'];
    
    if($payment_method != 5 && $payment_method != 4 && $payment_method != 3 && $payment_method != 24 && $payment_method != 20){
      Log::notice('Process updateOrderItemVeritrans: Success. Transaction is not using credit card.');
      return true;
    }
    
    $purchase_code  = (isset($charge_veritrans['order_id'])) ? $charge_veritrans['order_id'] : NULL;
    $status_code    = (isset($charge_veritrans['code'])) ? $charge_veritrans['code'] : NULL;
    
    if($status_code == 200){ //Veritrans is Approved
      $purchase_status = 3; //Status Approved
      $approval_date = date("Y-m-d H:i:s");
      $order_status_item = 2;
    }else{ //Veritrans is Challenged
      $purchase_status = 2;
      if($payment_method == 24 || $payment_method == 4 || $payment_method == 3){
        $purchase_status = 1;
      }
      
      $approval_date = NULL;
      $order_status_item = 0;
    }
    
    $update_item['purchase_status']       = $purchase_status;
    $update_item['approval_date']         = $approval_date;
    $update_item['order_status_item']     = $order_status_item;
    $update_item['item_warehouse_status'] = 0;

    $update_order_item_veritrans = DB::table('order_item')
      ->where('purchase_code', $purchase_code)
    ->update($update_item);
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updateOrderItemVeritrans: Success. Executed Time: '. $time_executed);
    return $update_item;
  }
  
  public static function createOrderItemHistory(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process createOrderItemHistory: Started');
    
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $order_item_id  = (isset($data['order_item']->order_item_id)) ? $data['order_item']->order_item_id : NULL ;
    $SKU            = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $payment_method = $data['payment_method'];
    $master_payment = isset($data['master_payment']) ? $data['master_payment'] : NULL;
    $type_transfer  = !is_null($master_payment) ? $master_payment->master_payment_type_transfer : NULL;
    
    if($order_item_id == NULL || $SKU == NULL){
      Log::notice('Process createOrderItemHistory: Order Item ID / SKU is missing');
      return false;
    }
    
    if($purchase_code == NULL){
      Log::notice('Process createOrderItemHistory: Purchase Code is missing');
      return false;
    }
    
    if($type_transfer == 1 || $payment_method == 3 || $payment_method == 4 || $payment_method == 19 || $payment_method == 99){
      //Create Order Item History
      $create_order_item_history['order_item_id']     = $order_item_id;
      $create_order_item_history['SKU']               = $SKU;
      $create_order_item_history['purchase_code']     = $purchase_code;
      $create_order_item_history['order_status_item'] = 0;
      $create_order_item_history['created_by']        = $customer_id;
      $create_order_item_history['created_date']      = date("Y-m-d H:i:s");
      //End Create Order Item History
      
      $order_item_history = DB::table('order_item_history')->insert($create_order_item_history);
      if(!$order_item_history){
        $time_executed  = microtime(true) - $time_start;
        Log::notice('Process createOrderItemHistory: Insert to order_item_history failed. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
        return false;
      }else{
        $time_executed  = microtime(true) - $time_start;
        Log::notice('Process createOrderItemHistory: Success. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
        return $order_item_history;
      }
    }else{
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process createOrderItemHistory: Success. Payment Method is not on condition. Executed Time: '. $time_executed);
      return true;
    }
  }
  
  public static function createOrderItemHistoryVeritrans(array $data, $charge_veritrans)
  {
    $time_start = microtime(true);
    Log::notice('Process createOrderItemHistoryVeritrans: Started');
    
    $customer_id        = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $order_item_id      = (isset($data['order_item']->order_item_id)) ? $data['order_item']->order_item_id : NULL ;
    $SKU                = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $purchase_code      = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $order_status_item  = $data['update_order_item_veritrans']['order_status_item'];
    $payment_method     = $data['payment_method'];
    $master_payment     = isset($data['master_payment']) ? $data['master_payment'] : NULL;
    $type_transfer      = !is_null($master_payment) ? $master_payment->master_payment_type_transfer : NULL;
    
    if($order_item_id == NULL || $SKU == NULL){
      Log::notice('Process createOrderItemHistoryVeritrans: Order Item ID / SKU is missing');
      return false;
    }
    
    if($purchase_code == NULL){
      Log::notice('Process createOrderItemHistoryVeritrans: Purchase Code is missing');
      return false;
    }
    
    if($order_status_item == 2){ //Status Veritrans is Approved
      $purchase_status = array(0,2);
      //Insert Double
      foreach ($purchase_status as $status) {
        //Create Order Item History
        $create_order_item_history['order_item_id']     = $order_item_id;
        $create_order_item_history['SKU']               = $SKU;
        $create_order_item_history['purchase_code']     = $purchase_code;
        $create_order_item_history['order_status_item'] = $status;
        $create_order_item_history['created_by']        = $customer_id;
        $create_order_item_history['created_date']      = date("Y-m-d H:i:s");
        //End Create Order Item History

        $order_item_history = DB::table('order_item_history')->insert($create_order_item_history);
        
        $time_executed  = microtime(true) - $time_start;
        if(!$order_item_history){
          Log::notice('Process createOrderItemHistoryVeritrans: Insert to order_item_history failed. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
          return false;
        }else{
          Log::notice('Process createOrderItemHistoryVeritrans: Success. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
        }
      }
    }else if($type_transfer != 1 && $payment_method != 3 && $payment_method != 4 && $payment_method != 19){ //Status Veritrans is Challenged
      //Create Order Item History
      $create_order_item_history['order_item_id']     = $order_item_id;
      $create_order_item_history['SKU']               = $SKU;
      $create_order_item_history['purchase_code']     = $purchase_code;
      $create_order_item_history['order_status_item'] = 0;
      $create_order_item_history['created_by']        = $customer_id;
      $create_order_item_history['created_date']      = date("Y-m-d H:i:s");
      //End Create Order Item History

      $order_item_history = DB::table('order_item_history')->insert($create_order_item_history);

      $time_executed  = microtime(true) - $time_start;
      if(!$order_item_history){
        Log::notice('Process createOrderItemHistoryVeritrans: Insert to order_item_history failed. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
        return false;
      }else{
        Log::notice('Process createOrderItemHistoryVeritrans: Success. [SKU: '.$SKU.']. Executed Time: '. $time_executed);
      }
    }
    
    return true;
  }
}
