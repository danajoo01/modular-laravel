<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Cart;
use DB;
use Log;
use \App\Customer;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\Payment;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\PromotionHelper;

class OrderDiscount extends Model {
  
  public static function insertFreegiftAutoValue($data = array())
  {
    $time_start     = microtime(true);
    
    $domain_id                  = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_id                = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $customer_email             = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $purchase_code              = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $fetch_order_item           = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift_auto              = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $total_discount             = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($freegift_auto)){
      //Insert Value Processs
      foreach($freegift_auto as $key => $values){
        
        $reshape_order_item   = PromotionHelper::reshapeOrderItem($freegift_auto[$key], $fetch_order_item);
        $freegift_auto[$key]  = $reshape_order_item['promotions'];
        $fetch_order_item     = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($freegift_auto[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift_auto[$key], $fetch_order_item, $total_purchase_value);
        
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
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
              $discount_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $order_price - $discount_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $discount_value         = $order_price;
                $order_item->real_price = 0;
              }
              //End Check

              //Insert Database
              $create_order_discount = array();
              $create_order_discount['order_item_id']             = $order_item->order_item_id;
              $create_order_discount['SKU']                       = $order_item->SKU;
              $create_order_discount['quantity']                  = 1;
              $create_order_discount['purchase_code']             = $purchase_code;
              $create_order_discount['discount_id']               = $freegift_auto[$key]['promotions_id'];
              $create_order_discount['discount_name']             = $freegift_auto[$key]['promotions_name'];
              $create_order_discount['discount_nfc_or_discount']  = $freegift_auto[$key]['promotions_name_for_customer'];
              $create_order_discount['discount_value']            = $discount_value;
              $create_order_discount['discount_type']             = 1;
              $create_order_discount['customer_email']            = $customer_email;
              $create_order_discount['customer_id']               = $customer_id;
              $create_order_discount['domain_id']                 = $domain_id;
              $create_order_discount['is_laravel']                = 1;
              
              $total_discount += $discount_value;

              $order_discount = DB::table('order_discount')->insert($create_order_discount);
              $time_executed  = microtime(true) - $time_start;
              if(!$order_discount){
                Log::notice('Process OrderDiscount: [Freegift Auto] Insert to order_discount failed. Executed Time: '. $time_executed);
                return false;
              }else{
                Log::notice('Process OrderDiscount: [Freegift Auto] Success. Executed Time: '. $time_executed);
              }
              //End Insert Database

              //Insert Log
              $log_start = microtime(true);

              $create_order_discount_log = array();
              $create_order_discount_log['purchase_code']             = $purchase_code;
              $create_order_discount_log['order_item_id']             = $order_item->order_item_id;
              $create_order_discount_log['SKU']                       = $order_item->SKU;
              $create_order_discount_log['quantity']                  = 1;
              $create_order_discount_log['discount_id']               = $freegift_auto[$key]['promotions_id'];
              $create_order_discount_log['customer_id']               = $customer_id;
              $create_order_discount_log['customer_email']            = $customer_email;
              $create_order_discount_log['discount_name']             = $freegift_auto[$key]['promotions_name'];
              $create_order_discount_log['discount_nfc_or_discount']  = $freegift_auto[$key]['promotions_name_for_customer'];
              $create_order_discount_log['discount_value']            = $discount_value;
              $create_order_discount_log['discount_type']             = 1;
              $create_order_discount_log['domain_id']                 = $domain_id;

              $order_discount_log_id = DB::table('order_discount_log')->insertGetId($create_order_discount_log);

              $log_executed  = microtime(true) - $log_start;
              if(!$order_discount_log_id){
                Log::notice('Process OrderDiscount: [Freegift Auto] Insert to order_discount_log failed. Executed Time: '.$log_executed);
                return false;
              }else{
                Log::notice('Process OrderDiscount: [Freegift Auto] Success insert to order_discount_log. Executed Time: '.$log_executed);
              }
              //End Insert Log
            }
          }
        }
        
      }
      //End Insert Value Process
    }
    
    $data['fetch_order_item']     = $fetch_order_item;
    $data['total_discount']       = $total_discount;
    
    return $data;
  }
  
  public static function insertVoucherValue($data = array())
  {
    $time_start     = microtime(true);
    
    $domain_id            = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_id          = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $customer_email       = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $purchase_code        = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $fetch_order_item     = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $voucher              = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $total_discount       = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($voucher)){
      //Insert Value Process
      
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
            $discount_value = PromotionHelper::setPromotionsValue($par_promo_value);

            $order_item->real_price = $order_price - $discount_value;

            if(isset($voucher["promotions_function"])){
              if($voucher["promotions_function"] == "freeCheapestItems"){
                if(in_array($order_item->order_item_id, $cheapItem)){
                  $discount_value = $order_price;
                  $order_item->real_price = $order_price - $discount_value;
                }
              }
            }

            //Check whether the price is below 0 after promotions value, then set promotions value to item price
            if($order_item->real_price < 0){
              $discount_value         = $order_price;
              $order_item->real_price = 0;
            }
            //End Check

            //Insert Database
            $create_order_discount = array();
            $create_order_discount['order_item_id']             = $order_item->order_item_id;
            $create_order_discount['SKU']                       = $order_item->SKU;
            $create_order_discount['quantity']                  = 1;
            $create_order_discount['purchase_code']             = $purchase_code;
            $create_order_discount['voucher_code']              = $voucher['promotions_code'];
            $create_order_discount['discount_id']               = $voucher['promotions_id'];
            $create_order_discount['customer_id']               = $customer_id;
            $create_order_discount['discount_name']             = $voucher['promotions_name'];
            $create_order_discount['discount_nfc_or_discount']  = $voucher['promotions_name_for_customer'];
            $create_order_discount['discount_value']            = $discount_value;
            $create_order_discount['discount_type']             = 2;
            $create_order_discount['customer_email']            = $customer_email;
            $create_order_discount['domain_id']                 = $domain_id;
            $create_order_discount['is_laravel']                = 1;
            
            $total_discount += $discount_value;

            $order_discount = DB::table('order_discount')->insert($create_order_discount);
            $time_executed  = microtime(true) - $time_start;
            if(!$order_discount){
              Log::notice('Process OrderDiscount: [Voucher] Insert to order_discount failed. Executed Time: '. $time_executed);
              return false;
            }else{
              Log::notice('Process OrderDiscount: [Voucher] Success. Executed Time: '. $time_executed);
            }
            //End Insert Database

            //Insert Log
            $log_start = microtime(true);

            $create_order_discount_log = array();
            $create_order_discount_log['purchase_code']             = $purchase_code;
            $create_order_discount_log['order_item_id']             = $order_item->order_item_id;
            $create_order_discount_log['SKU']                       = $order_item->SKU;
            $create_order_discount_log['quantity']                  = 1;
            $create_order_discount_log['voucher_code']              = $voucher['promotions_code'];
            $create_order_discount_log['discount_id']               = $voucher['promotions_id'];
            $create_order_discount_log['customer_id']               = $customer_id;
            $create_order_discount_log['customer_email']            = $customer_email;
            $create_order_discount_log['discount_name']             = $voucher['promotions_name'];
            $create_order_discount_log['discount_nfc_or_discount']  = $voucher['promotions_name_for_customer'];
            $create_order_discount_log['discount_value']            = $discount_value;
            $create_order_discount_log['discount_type']             = 2;
            $create_order_discount_log['domain_id']                 = $domain_id;

            $order_discount_log_id = DB::table('order_discount_log')->insertGetId($create_order_discount_log);

            $log_executed  = microtime(true) - $log_start;
            if(!$order_discount_log_id){
              Log::notice('Process OrderDiscount: [Voucher] Insert to order_discount_log failed. Executed Time: '.$log_executed);
              return false;
            }else{
              Log::notice('Process OrderDiscount: [Voucher] Success insert to order_discount_log. Executed Time: '.$log_executed);
            }
            //End Insert Log
          }
        }
      }
      //End Insert Value Process
    }
    
    $data['fetch_order_item']     = $fetch_order_item;
    $data['total_discount']       = $total_discount;
    
    return $data;
  }
  
  public static function insertFreegiftValue($data = array())
  {
    $time_start     = microtime(true);
    
    $domain_id            = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_id          = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $customer_email       = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $purchase_code        = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $fetch_order_item     = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift             = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    $total_discount       = (isset($data['total_discount'])) ? $data['total_discount'] : 0 ;
    
    if(!empty($freegift)){
      //Insert Value Process
      foreach($freegift as $key => $values){
        
        $reshape_order_item   = PromotionHelper::reshapeOrderItem($freegift[$key], $fetch_order_item);
        $freegift[$key]       = $reshape_order_item['promotions'];
        $fetch_order_item     = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value   = PromotionHelper::getTotalPromotionsPurchase($freegift[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift[$key], $fetch_order_item, $total_purchase_value);
        
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            $order_price = PromotionHelper::setItemPrice($order_item);

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
              $discount_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $order_price - $discount_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $discount_value         = $order_price;
                $order_item->real_price = 0;
              }
              //End Check

              //Insert Database
              $create_order_discount = array();
              $create_order_discount['order_item_id']             = $order_item->order_item_id;
              $create_order_discount['SKU']                       = $order_item->SKU;
              $create_order_discount['quantity']                  = 1;
              $create_order_discount['purchase_code']             = $purchase_code;
              $create_order_discount['discount_id']               = $freegift[$key]['promotions_id'];
              $create_order_discount['discount_name']             = $freegift[$key]['promotions_name'];
              $create_order_discount['discount_nfc_or_discount']  = $freegift[$key]['promotions_name_for_customer'];
              $create_order_discount['discount_value']            = $discount_value;
              $create_order_discount['discount_type']             = 1;
              $create_order_discount['customer_email']            = $customer_email;
              $create_order_discount['customer_id']               = $customer_id;
              $create_order_discount['domain_id']                 = $domain_id;
              $create_order_discount['is_laravel']                = 1;
              
              $total_discount += $discount_value;

              $order_discount = DB::table('order_discount')->insert($create_order_discount);
              $time_executed  = microtime(true) - $time_start;
              if(!$order_discount){
                Log::notice('Process OrderDiscount: [Freegift] Insert to order_discount failed. Executed Time: '. $time_executed);
                return false;
              }else{
                Log::notice('Process OrderDiscount: [Freegift] Success. Executed Time: '. $time_executed);
              }
              //End Insert Database

              //Insert Log
              $log_start = microtime(true);

              $create_order_discount_log = array();
              $create_order_discount_log['purchase_code']             = $purchase_code;
              $create_order_discount_log['order_item_id']             = $order_item->order_item_id;
              $create_order_discount_log['SKU']                       = $order_item->SKU;
              $create_order_discount_log['quantity']                  = 1;
              $create_order_discount_log['discount_id']               = $freegift[$key]['promotions_id'];
              $create_order_discount_log['customer_id']               = $customer_id;
              $create_order_discount_log['customer_email']            = $customer_email;
              $create_order_discount_log['discount_name']             = $freegift[$key]['promotions_name'];
              $create_order_discount_log['discount_nfc_or_discount']  = $freegift[$key]['promotions_name_for_customer'];
              $create_order_discount_log['discount_value']            = $discount_value;
              $create_order_discount_log['discount_type']             = 1;
              $create_order_discount_log['domain_id']                 = $domain_id;

              $order_discount_log_id = DB::table('order_discount_log')->insertGetId($create_order_discount_log);

              $log_executed  = microtime(true) - $log_start;
              if(!$order_discount_log_id){
                Log::notice('Process OrderDiscount: [Freegift] Insert to order_discount_log failed. Executed Time: '.$log_executed);
                return false;
              }else{
                Log::notice('Process OrderDiscount: [Freegift] Success insert to order_discount_log. Executed Time: '.$log_executed);
              }
              //End Insert Log
            }
          }
        }
        
      }
      //End Insert Value Process
    }
    
    $data['fetch_order_item']     = $fetch_order_item;
    $data['total_discount']       = $total_discount;
    
    return $data;
  }
  
  public static function getDiscount(array $data)
  {
    $purchase_code = (isset($data['purchase_code'])) ? $data['purchase_code'] : NULL ;
    
    $get_discount = 
      DB::connection('read_mysql')->table('order_discount')
        ->select(\DB::raw('purchase_code, discount_name, promotions_template_mode, SUM(discount_value) AS total_discount'))
        ->leftJoin('promotions_template', 'promotions_template.promotions_template_id', '=', 'order_discount.discount_id')
        ->where('purchase_code', '=',  $purchase_code)
        ->groupBy('purchase_code')
        ->groupBy('discount_name')
        ->groupBy('promotions_template_mode')
        ->get();
    
    return $get_discount;
  }
  
  public static function createOrderDiscount(array $data)
  {
    Log::notice('Process OrderDiscount: Started');
    
    $fetch_order_item = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : array() ;
    $freegift_auto    = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $voucher          = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $freegift         = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    
    if(empty($fetch_order_item)){
      Log::notice('Process OrderDiscount: Order Item is empty');
      return false;
    }
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      Log::notice('Process OrderDiscount: Success. Order not using any promotions.');
      return true;
    }
    
    //Clear Order Item Real Price Value
    foreach($fetch_order_item as $order_item){
      unset($order_item->real_price);
    }
    //End Clear
    
    $data['total_discount'] = 0;
    $insert_freegift_auto = Self::insertFreegiftAutoValue($data);
    $insert_voucher       = Self::insertVoucherValue($insert_freegift_auto);
    $insert_freegift      = Self::insertFreegiftValue($insert_voucher);
    
    return $insert_freegift;
  }
}