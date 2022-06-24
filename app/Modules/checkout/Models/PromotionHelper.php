<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Cart;
use DB;
use Log;
use Auth;

use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Checkout\Models\PromotionCondition;
use \App\Modules\Checkout\Models\OrderItem;

class PromotionHelper extends model {
  
  /**
	 * To check benka point is allowed or not
	 *
	 * @return boolean
  */
  
  public static function checkBenkaPoint(){
    Log::notice('checkBenkaPoint is started') ;
    $allowed = true;
    
    //Check is Benka Point is allowed or not
    $freegift_auto  = session('freegift_auto');
    $voucher        = session('voucher');
    $freegift       = session('freegift');
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      Log::notice('checkBenkaPoint is set to '.json_encode($allowed).' because no promotions is active') ;
      return $allowed;
    }
    
    if(!empty($freegift_auto)){
      Log::notice('checkBenkaPoint is set to FALSE because freegift auto is not empty') ;
      $allowed = false;
      foreach($freegift_auto as $key => $values){
        if($freegift_auto[$key]['promotions_allow_benka_point'] == 1){
          Log::notice('checkBenkaPoint is set to TRUE because freegift auto ['.$freegift_auto[$key]['promotions_name'].'] is enabling benka point ') ;
          return true;
        }
      }
    }
    
    if(!empty($voucher)){
      Log::notice('checkBenkaPoint is set to FALSE because voucher is not empty') ;
      $allowed = false;
      if($voucher['promotions_allow_benka_point'] == 1){
        Log::notice('checkBenkaPoint is set to TRUE because voucher ['.$voucher['promotions_name'].'] is enabling benka point ') ;
        return true;
      }
    }
    
    if(!empty($freegift)){
      Log::notice('checkBenkaPoint is set to FALSE because freegift is not empty') ;
      $allowed = false;
      foreach($freegift as $key => $values){
        if($freegift[$key]['promotions_allow_benka_point'] == 1){
          Log::notice('checkBenkaPoint is set to TRUE because freegift ['.$freegift[$key]['promotions_name'].'] is enabling benka point ') ;
          return true;
        }
      }
    }
    
    //Remove session benka point if promotions is not allowing
    if(!$allowed){
      session()->forget('benka_point');
    }
    //End Remove Session
    
    Log::notice('checkBenkaPoint is set to '.json_encode($allowed).' because no promotions is active') ;
    return $allowed;
  }
  
  /**
	 * To check whether promotions is exclusive or not
	 *
	 * @return boolean
  */
  public static function checkExclusive($promotions, $fetch_order_item)
  {
    /*
      $promotions_eksklusif
      1 = freegift auto eksklusif
      2 = voucher eksklusif
      3 = freegift eksklusif
    */
    
    $promotions_eksklusif = (session()->has('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ; //no eksklusif promotions
    $promotions_type      = isset($promotions['promotions_type']) ? $promotions['promotions_type'] : NULL ;
    
    if($promotions_eksklusif == 0 && $promotions['promotions_eksklusif'] == 1){
      
      if($promotions_type == 'freegift_auto'){
        $promotions_eksklusif = 1;
      }else if($promotions_type == 'voucher'){
        $promotions_eksklusif = 2;
      }else if($promotions_type == 'freegift'){
        $promotions_eksklusif = 3;
      }
      
      //Reset Promotions Session if eksklusif
      Log::notice('['.$promotions['promotions_name'].'] is now resetting promotions session') ;
      session()->forget('freegift_auto');
      session()->forget('freegift');
      session()->forget('voucher');
      
      //Reset Order Item if promotions is eksklusif
      Log::notice('['.$promotions['promotions_name'].'] is now resetting order item') ;
      $fetch_order_item = OrderItem::fetchOrderItem();
      
      session()->put('promotions_eksklusif', $promotions_eksklusif);
    }
    
    Log::notice('['.$promotions['promotions_name'].'] Exclusive: '. json_encode((session('promotions_eksklusif')) ? session('promotions_eksklusif') : 1) .'[1: Freegift Auto Eksklusif | 2: Voucher Eksklusif | 3: Freegift Eksklusif]') ;
    return $fetch_order_item;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To set item price on order item
	 *
	 * @return integer
  */
  public static function setItemPrice($order_item)
  {
    /*
      Real Price: Price that has been updated with promotions flow.
    */
    
    if(isset($order_item->temp_price) || property_exists($order_item, 'temp_price')){
      $price = $order_item->temp_price;
    }else if(isset($order_item->real_price) || property_exists($order_item, 'real_price')){
      $price = $order_item->real_price;
    }else{
      $price = $order_item->each_price;
      if($order_item->discount_price != NULL && $order_item->discount_price != 0){
        $price = $order_item->discount_price;
      }
    }
    
    return $price;
  }
  
  public static function fetchExclusiveProduct()
  {
    $exclusive_product  = [];
    $core_selector      = getCoreSelector("promotions_exclusive_products");

    $solr_data  = get_active_solr($core_selector, $query = null, null, null, null, null, null);

    try {
      if(!empty($solr_data->docs)){
        foreach($solr_data->docs as $data){
          $exclusive_product[] = $data->product_id;
        }
      }
    } catch (\Exception $e) {
      \Log::error($e);
      \Log::error('Error on SOLR fetchExclusiveProduct');
    }
    
    return $exclusive_product;
  }
  
  //--------------------------------------------------------------------
  
  /**
	 * To modify order item for cheapest item or exclude sale item
	 *
	 * @return array
  */
  public static function reshapeOrderItem($promotions, $fetch_order_item)
  {
    $data = array();
    
    if(empty($promotions)){
      $data['promotions']       = array();
      $data['fetch_order_item'] = $fetch_order_item;
      
      return $data;
    }
    
    $promotions_name = NULL;
    if(isset($promotions['promotions_name'])){
      $promotions_name = $promotions['promotions_name'];
    }else if(isset($promotions['promotions_template_name'])){
      $promotions_name = $promotions['promotions_template_name'];
    }
    
    $exclude_sale_item = 0;
    if(isset($promotions['promotions_exclude_sale_item'])){
      $exclude_sale_item = $promotions['promotions_exclude_sale_item'];
    }else if(isset($promotions['exclude_sale_item'])){
      $exclude_sale_item = $promotions['exclude_sale_item'];
    }
    
    $exclude_normal_item = 0;
    if(isset($promotions['promotions_exclude_normal_item'])){
      $exclude_normal_item = $promotions['promotions_exclude_normal_item'];
    }else if(isset($promotions['exclude_normal_item'])){
      $exclude_normal_item = $promotions['exclude_normal_item'];
    }
    
    $promotions_mode = NULL;
    if(isset($promotions['promotions_mode'])){
      $promotions_mode = $promotions['promotions_mode'];
    }else if(isset($promotions['promotions_template_mode'])){
      $promotions_mode = $promotions['promotions_template_mode'];
    }
    
    $promotions_applicable = 1;
    if(isset($promotions['promotions_applicable'])){
      $promotions_applicable = $promotions['promotions_applicable'];
    }else if(isset($promotions['promotions_template_applicable'])){
      $promotions_applicable = $promotions['promotions_template_applicable'];
    }
    
    $lowest_price = 0;
    if(isset($promotions['promotions_lowest_price'])){
      $lowest_price = $promotions['promotions_lowest_price'];
    }else if(isset($promotions['lowest_price'])){
      $lowest_price = $promotions['lowest_price'];
    }
    
    $allow_exclusive_product = 0;
    if(isset($promotions['promotions_allow_exclusive_product'])){
      $allow_exclusive_product = $promotions['promotions_allow_exclusive_product'];
    }else if(isset($promotions['allow_exclusive_product'])){
      $allow_exclusive_product = $promotions['allow_exclusive_product'];
    }
    
    $promotions_order_id  = isset($promotions['promotions_order_id']) ? $promotions['promotions_order_id'] : array() ;
    
    //Exclude Sale / Normal Item
    //Unset order item key if the item is sale item
    if($exclude_sale_item == 1){
      foreach ($fetch_order_item as $key => $order_item) {
        if($order_item->discount_price > 0 && ($order_item->special_price == 0 || $order_item->special_price == NULL || $order_item->special_price == '')){
          Log::notice('['.$promotions_name.'] is excluding sale item. Removed Order Item ID: '. $order_item->order_item_id) ;
          $order_item->excluded = 1;
        }
      }
    //Unset order item key if the item is normal item
    }else if($exclude_normal_item == 1){
      foreach ($fetch_order_item as $key => $order_item) {
        if($order_item->discount_price == 0 && ($order_item->special_price == 0 || $order_item->special_price == NULL || $order_item->special_price == '')){
          Log::notice('['.$promotions_name.'] is excluding normal item. Removed Order Item ID: '. $order_item->order_item_id) ;
          $order_item->excluded = 1;
        }
      }
    //Reset exluded item if not exclude sale/normal item
    }else{
      foreach($fetch_order_item as $order_item){
        unset($order_item->excluded);
      }
      //End Unset
    }
    //End Exclude Sale / Normal Item
    
    //Exclude Exclusive Products
    if($allow_exclusive_product == 0){
      $exclusive_product = Self::fetchExclusiveProduct();
      foreach ($fetch_order_item as $order_item) {
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          $product_id = $order_item->product_id;
          if(in_array($product_id, $exclusive_product)){
            Log::notice('['.$promotions_name.'] is excluding exclusive products. Removed Order Item ID: '. $order_item->order_item_id) ;
            $order_item->excluded = 1;
          }
        }
      }
    }
    //End Exclude Exclusive Products
    
    //Cheapest Item Price
    if(($promotions_mode == 3 || $lowest_price == 1) && !isset($promotions["promotions_function"])){
      $order_id = array();
      $temp_price = 0;
      foreach ($fetch_order_item as $order_item) {
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          if($promotions_applicable == 2){ //if applicable = 2 (whole cart), find cheapest item on all items
            $price = PromotionHelper::setItemPrice($order_item);

            //Find Cheapest Item Price
            if($temp_price == 0){
              $order_id   = array();
              $order_id[] = $order_item->order_item_id;
              $temp_price = $price;
            }else if($temp_price > $price){
              $order_id   = array();
              $order_id[] = $order_item->order_item_id;
              $temp_price = $price;
            }
            //End Find Cheapest Item Price

          }else if (in_array($order_item->order_item_id, $promotions_order_id)) {
            $price = PromotionHelper::setItemPrice($order_item);

            //Find Cheapest Item Price
            if($temp_price == 0){
              $order_id   = array();
              $order_id[] = $order_item->order_item_id;
              $temp_price = $price;
            }else if($temp_price > $price){
              $order_id   = array();
              $order_id[] = $order_item->order_item_id;
              $temp_price = $price;
            }
            //End Find Cheapest Item Price
          }
        }
      }
      
      $promotions['promotions_order_id'] = $order_id;
      Log::notice('['.$promotions_name.'] value is Cheapest Item. Order ID: '.json_encode($order_id).' | Price: '. $temp_price) ;
    }
    //End Cheapest Item Price

    // 2 cheapest item
    if(isset($promotions["promotions_function"])){
      if($promotions["promotions_function"] == "freeCheapestItems"){
        foreach ($fetch_order_item as $key => $value) {
          $price = PromotionHelper::setItemPrice($value);
          $fetch_order_item[$key]->real_price = $price;
        }

        usort($fetch_order_item, function ($a, $b) {
          return $a->real_price - $b->real_price;
        });

        // pisahkan cheap item
        $cheapItem = array();
        if (isset($fetch_order_item[0]) AND $fetch_order_item[1]) {
          $cheapItem = [$fetch_order_item[0]->order_item_id, $fetch_order_item[1]->order_item_id];
        }

        $promotions['promotions_order_id'] = $cheapItem;
      }
      
    }
    // 2 cheapest item
    
    $data['promotions']       = $promotions;
    $data['fetch_order_item'] = $fetch_order_item;
      
    return $data;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To set promotions value whether nominal, percentage, free cheapest item, or freeshipping
	 *
	 * @return integer
  */
  public static function setPromotionsValue($data = array())
  { 
    $promotions                   = isset($data['promotions']) ? $data['promotions'] : array() ;
    $count_applied                = isset($data['count_applied']) ? $data['count_applied'] : NULL ;
    $order_item                   = isset($data['order_item']) ? $data['order_item'] : array() ;
    $price                        = isset($data['price']) ? $data['price'] : NULL ;
    $total_purchase_value         = isset($data['total_purchase_value']) ? $data['total_purchase_value'] : NULL ;
    $use_maximum_value_discount   = isset($data['use_maximum_value_discount']) ? $data['use_maximum_value_discount'] : false ;
    
    $promotions_value = 0;
    if($use_maximum_value_discount){
      $promotions_value = $price / $total_purchase_value * $promotions['promotions_max_discount_value'];
      
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: '. $price .'/'. $total_purchase_value .'*'. $promotions['promotions_max_discount_value'] .' = ' .$promotions_value);
      
    }else if($promotions['promotions_mode'] == 1){ //nominal
      $promotions_value = $price / $total_purchase_value * $promotions['promotions_mode_value'];
      
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: '. $price .'/'. $total_purchase_value .'*'. $promotions['promotions_mode_value'] .' = ' .$promotions_value);
      
    }else if($promotions['promotions_mode'] == 2){ //percentage
      $promotions_value = $promotions['promotions_mode_value'] * (int) $price / 100;
      
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: '. $promotions['promotions_mode_value'] .'*'. (int) $price .'/'. 100 .' = ' .$promotions_value);
      
    }else if($promotions['promotions_mode'] == 3){ //free cheapest item

      // if(!isset($promotions['promotions_function']) && $promotions['promotions_function'] != "freeCheapestItems")
      // {
      //   //Find Cheapest Item Price on Order Item
      //   if(in_array($order_item->order_item_id, $promotions['promotions_order_id'])){
      //     $promotions_value = ceil(PromotionHelper::setItemPrice($order_item));
      //   }
        
      //   Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: [Cheapest Item]'. $promotions_value);
      //   //End Find Cheapest Item Price
      // }

      //Find Cheapest Item Price on Order Item
      if(in_array($order_item->order_item_id, $promotions['promotions_order_id'])){
        $promotions_value = ceil(PromotionHelper::setItemPrice($order_item));
      }
      
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: [Cheapest Item]'. $promotions_value);
      
    }else if($promotions['promotions_mode'] == 4){ //freeshipping
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: [Freeshipping]'. $promotions_value);
      
    }else if($promotions['promotions_mode'] == 5){ //freeitem
      Log::notice('['.$promotions['promotions_name'].'] Promotions Calculation: [Freeitem]'. $promotions_value);
    }
    
    return (isset($promotions_value)) ? $promotions_value : 0;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To set how many promotions value is divided to order item 
	 *
	 * @return integer
  */
  public static function setCountApplied($data = array())
  {
    $order_item_id          = isset($data['order_item_id']) ? $data['order_item_id'] : NULL ;
    $fetch_order_item       = isset($data['fetch_order_item']) ? $data['fetch_order_item'] : NULL ;
    $promotions             = isset($data['promotions']) ? $data['promotions'] : array() ;
    
    $promotions_applicable  = isset($promotions['promotions_applicable']) ? $promotions['promotions_applicable'] : $promotions['promotions_template_applicable'] ;
    $promotions_order_id    = isset($promotions['promotions_order_id']) ? $promotions['promotions_order_id'] : array() ;

    $count_applied = 0;
    foreach ($fetch_order_item as $order_item) {
      if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
        if($promotions_applicable == 2 && $promotions['promotions_mode'] != 3){
          $count_applied++;
        }else if(in_array($order_item_id, $promotions_order_id)){
          $count_applied = count($promotions_order_id);
        }
      }
    }
    
    return $count_applied;
  }
  
  //--------------------------------------------------------------------
  
  /**
	 * To get total item price for order item that has met the condition
	 *
	 * @return integer
  */
  public static function getTotalPromotionsPurchase($promotions, $fetch_order_item)
  { 
    $total_purchase_value = 0;
    foreach($fetch_order_item as $order_item){
      if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
        if($promotions['promotions_applicable'] == 2 && $promotions['promotions_mode'] != 3){
          //If it's applicable is 2 (whole cart), all item is included
          Log::notice('['.$promotions['promotions_name'].'] [Whole Cart] Promotion Purchase = '. $total_purchase_value .' + ' . PromotionHelper::setItemPrice($order_item)) ;
          $total_purchase_value += PromotionHelper::setItemPrice($order_item);
        }else if(in_array($order_item->order_item_id, $promotions['promotions_order_id'])){
          //If it's not, then only order item id that has met the condition is included
          Log::notice('['.$promotions['promotions_name'].'] [Rule Select] Promotion Purchase = '. $total_purchase_value .' + ' . PromotionHelper::setItemPrice($order_item)) ;
          $total_purchase_value += PromotionHelper::setItemPrice($order_item);
        }
      }
    }
    
    Log::notice('['.$promotions['promotions_name'].'] Total Promotions Purchase: ' . $total_purchase_value) ;
    return $total_purchase_value;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To check total order item that has met the condition
	 *
	 * @return integer
  */
  public static function totalValidatedOrderItem($promotions, $fetch_order_item)
  { 
    $order_item_applied     = (session('order_item_applied')) ? session('order_item_applied') : array() ;
    //$promotions_applicable  = isset($promotions['promotions_applicable']) ? $promotions['promotions_applicable'] : $promotions['promotions_template_applicable'] ;
    $exclude_sale_item      = isset($promotions['promotions_exclude_sale_item']) ? $promotions['promotions_exclude_sale_item'] : $promotions['exclude_sale_item'];
    
    $total_validated_order_item = 0;
    foreach ($fetch_order_item as $key => $order_item) {
      $price = Self::setItemPrice($order_item);
      if($price > 0 && in_array($order_item->order_item_id, $order_item_applied)){ //Only counts item that has price greater than 0 and included on order item applied
        if($exclude_sale_item == 1){ //Exclude sale item from counting
          if($order_item->discount_price == 0 && ($order_item->special_price == 0 || $order_item->special_price == NULL || $order_item->special_price == '')){
            $total_validated_order_item ++ ;
          }
        }else{
          $total_validated_order_item ++ ;
        }
      }
    }
    
    return $total_validated_order_item;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To check total promotions value is exceeding maximum value discount
	 *
	 * @return boolean
  */
  public static function useMaximumValueDiscount($promotions, $fetch_order_item, $total_purchase_value)
  { 
    $max_discount_value     = $promotions['promotions_max_discount_value'];
    $total_promotions_value = 0;
    
    if($max_discount_value == null || empty($max_discount_value)){
      return false;
    }
    
    foreach ($fetch_order_item as $order_item) {
      if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
        $price = Self::setItemPrice($order_item);

        $par_count = array();
        $par_count['order_item_id']     = $order_item->order_item_id;
        $par_count['fetch_order_item']  = $fetch_order_item;
        $par_count['promotions']        = $promotions;
        $count_applied = Self::setCountApplied($par_count);

        if($count_applied > 0){

          $par_promo_value = array();
          $par_promo_value['promotions']            = $promotions;
          $par_promo_value['count_applied']         = $count_applied;
          $par_promo_value['order_item']            = $order_item;
          $par_promo_value['price']                 = $price;
          $par_promo_value['total_purchase_value']  = $total_purchase_value;
          $promotions_value = Self::setPromotionsValue($par_promo_value);

          $order_item->temp_price = $price - $promotions_value;

          //Check whether the price is below 0 after promotions value, then set promotions value to item price
          if($order_item->temp_price < 0){
            $promotions_value         = $price;
            $order_item->temp_price   = 0;
          }
          //End Check

          $total_promotions_value += $promotions_value;
        }
      }
    }
    
    foreach($fetch_order_item as $order_item){
      unset($order_item->temp_price);
    }
    
    Log::notice('['.$promotions['promotions_name'].'] useMaximumValueDiscount: ('.$total_promotions_value.' > '.$max_discount_value.')' . json_encode($total_promotions_value > $max_discount_value)) ;
    return $total_promotions_value > $max_discount_value ? true : false ;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To validate promotions value
	 *
	 * @return boolean
  */
  public static function validatePromotionsValue($promotions, $promotions_value)
  { 
    $time_start = microtime(true);
    Log::notice('['.$promotions['promotions_name'].'] validatePromotionsValue started') ;
    
    $real_promotions_value  = (is_float($promotions_value)) ? round($promotions_value) : $promotions_value;
    $promotions_id          = $promotions['promotions_id'];
    $maximum_quota          = $promotions['promotions_maximum_quota'];
    
    //$quota_usage            = $promotions['promotions_quota_usage'];
    
    $quota_usage  = DB::table('promotions_quota_log')
            ->where('promotions_template_id', $promotions_id)
            ->sum('promotions_used');
    
    $temp_quota = $quota_usage + $real_promotions_value;
    
    if($maximum_quota > 0 && $temp_quota > $maximum_quota){
      Log::notice('['.$promotions['promotions_name'].'] is exceeding maximum quota. Maximum Quota is ['.$maximum_quota.'] while quota usage after using this promotions is ['.$temp_quota.'].') ;
      $real_promotions_value = 0;
    }
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('['.$promotions['promotions_name'].'] validatePromotionsValue time executed is : ' . $time_executed) ;
    
    return $real_promotions_value;
  }
  
  //--------------------------------------------------------------------
  
  /**
	 * To set promotions notice
	 *
	 * @return boolean
  */
  public static function setPromotionsNotice($promotions)
  { 
    $notice             = "";
    $benka_point_notice = "";
    $exclusive_notice   = "";
    
    if($promotions['promotions_allow_benka_point'] == 0){
      $benka_point_notice = " benka point";
    }
    
    if($promotions['promotions_eksklusif'] == 1){
      $exclusive_notice = " promo lain";
      if($benka_point_notice != ""){
        $exclusive_notice = " dan promo lain";
      }
    }
    
    if($benka_point_notice != "" || $exclusive_notice != ""){
      $notice = "Promo " . $promotions['promotions_name'] . " ini tidak bisa digabungkan dengan " . $benka_point_notice . $exclusive_notice . ".";
    }
    
    return $notice;
  }
  
  //--------------------------------------------------------------------
}
