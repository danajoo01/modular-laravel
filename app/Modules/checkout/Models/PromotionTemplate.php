<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Cart;
use DB;
use Log;
use Auth;

use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Checkout\Models\PromotionCondition;
use \App\Modules\Checkout\Models\PromotionHelper;
use \App\Modules\Checkout\Models\OrderItem;

class PromotionTemplate extends model {
  
  protected $table      = 'promotions_template'; //Define your table name

	protected $primaryKey = 'promotions_template_id'; //Define your primarykey

	public $timestamps    = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded    = ['promotions_template_id']; //Define your guarded columns
  
  /**
	 * Fetch Freegift Object
	 *
	 * @return Object
	 */
	public static function getFreegiftAuto($attributes = array())
	{
    $get_freegift = Promotion::getFreegiftAutoTemplate(); //freegift if attribute is empty
    
		if (empty($get_freegift)) {
			Log::notice('Freegift Auto not found');
			return false;
		}
    
    $validated_freegift = array();
    foreach ($get_freegift as $key => $value) {
      $validate_rule_information = self::validateRuleInformation($get_freegift[$key]);
      if ($validate_rule_information) {
        array_push($validated_freegift, $get_freegift[$key]);
      }
    }
    
    if(empty($validated_freegift)){
      Log::notice('All freegift Auto condition is not met');
			return false;
    }

		return $validated_freegift;
	}

	// --------------------------------------------------------------------
  
	/**
	 * Fetch Voucher Object
	 *
	 * @return Object
	 */
	public static function getVoucher($attributes = array())
	{
    $get_voucher = Promotion::getVoucherCodeTemplate($attributes) ;
    
		if (empty($attributes) || is_null($get_voucher)) {
			Log::notice('Invalid voucher code');
			return false;
		}
    
    if($get_voucher['customer_email'] != NULL){
      $customer_email = Auth::user()->customer_email;
      if(strtolower($get_voucher['customer_email']) != strtolower($customer_email)){
        Log::notice('Voucher is not available for customer : '.$customer_email);
        return false;
      }
    }
    
		$validate_rule_information = self::validateRuleInformation($get_voucher);
		if (! $validate_rule_information) {
			return false;
		}

		return $get_voucher;
	}

	// --------------------------------------------------------------------
  
  /**
	 * Fetch Freegift Object
	 *
	 * @return Object
	 */
	public static function getFreegift($attributes = array())
	{
    $get_freegift = Promotion::getFreegiftTemplate($attributes);
    
		if (empty($get_freegift)) {
			Log::notice('Freegift not found');
			return false;
		}    
    $validated_freegift = array();
    foreach ($get_freegift as $key => $value) {
      $validate_rule_information = self::validateRuleInformation($get_freegift[$key]);
      if ($validate_rule_information) {
        array_push($validated_freegift, $get_freegift[$key]);
      }
    }
    
    if(empty($validated_freegift)){
      Log::notice('All freegift Auto condition is not met');
			return false;
    }

		return $validated_freegift;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is voucher_code.status and voucher_template.enabled is 1
	 *
	 * @return boolean
	 */
	public static function validateRuleInformation($attributes = array())
	{
		$status = true;
    
		if (! self::validateDurationDate($attributes)) {
			Log::notice(''.$attributes['promotions_template_name'].' validateDurationDate error');
			return false;
		}
		
		if (! self::isPromotionTemplateEnabled($attributes)) {
			Log::notice(''.$attributes['promotions_template_name'].' isPromotionTemplateEnabled error');
			return false;
		}
		
		if (! self::validateStartDateEndDate($attributes)) {
			Log::notice(''.$attributes['promotions_template_name'].' validateStartDateEndDate error');
			return false;
		}
    
    if (! self::validateShopdecaPromotions($attributes)) {
			Log::notice(''.$attributes['promotions_template_name'].' validateShopdecaPromotions error');
			return false;
		}

		if (! self::isOneUserOneTransaction($attributes)) {
			Log::notice(''.$attributes['promotions_template_name'].' isOneUserOneTransaction error');
			return false;
		}
    
    //Hardcode Freegift Auto
    $app_env  = env('APP_ENV', 'development');
    if($app_env == 'production'){
      $freegift_auto_id = [9116];
    }else{
      $freegift_auto_id = [8007];
    }
    
    if(in_array($attributes['promotions_template_id'], $freegift_auto_id)){
      //Check Payment Method
      $payment_method = (session('payment_method')) ? session('payment_method') : NULL ;
      if(is_null($payment_method) || $payment_method == 19){
        Log::notice(''.$attributes['promotions_template_name'].' Payment Method error');
        return false;
      }
      //End Check Payment Method
    }
    //End Hardcode Freegift Auto

		Log::notice(''.$attributes['promotions_template_name'].' validateRuleInformation success');

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is voucher_code.status and voucher_template.enabled is 1
	 *
	 * @return boolean
	 */
	public static function validateDurationDate($attributes = array())
	{
    Log::notice(''.$attributes['promotions_template_name'].' is on validateDurationDate');
    \Log::debug(json_encode($attributes));
		$status = true;
    $duration = (isset($attributes['duration']) && $attributes['duration'] != NULL) ? $attributes['duration'] : NULL ;
    if($duration == NULL || $duration < 1){
      return $status;
    }
    
    $date_now = date("Y-m-d");
    $duration_date = date("Y-m-d", strtotime($attributes['code_createddate']) + (24 * 3600 * $duration));
    
    Log::notice('duration: '.json_encode($duration));
    Log::notice('duration_date: '.json_encode($duration_date));
    Log::notice('date_now: '.json_encode($date_now));
    
		if (strtotime($date_now) > strtotime($duration_date)) {
			return false;
		}
		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is voucher_code.status and voucher_template.enabled is 1
	 *
	 * @return boolean
	 */
	public static function isPromotionTemplateEnabled($attributes = array())
	{
		$status = true;
		if (isset($attributes['promotions_code_number'])) {
			if ($attributes['status'] != 1 && $attributes['enabled'] != 1) {
				return false;
			}
		} else {
			if ($attributes['enabled'] != 1) {
				return false;
			}
		}

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is voucher_code.status and voucher_template.enabled is 1
	 *
	 * @return boolean
	 */
	public static function validateStartDateEndDate($attributes = array())
	{
		$status = true;
		$date 	= date('Y-m-d H:i:s');
		if ($attributes['start_date'] > $date || $attributes['end_date'] < $date) {
			return false;
		}

		return $status;
	}
  
  /**
	 * Check promotions for shopdeca
	 *
	 * @return boolean
	 */
	public static function validateShopdecaPromotions($attributes = array())
	{
		$status               = true;
		$shopdeca_launch_date = '2017-04-11 00:00:00';
    $get_domain           = get_domain();
    $domain_id            = isset($get_domain['domain_id']) ? $get_domain['domain_id'] : NULL;
    
		if ($domain_id == 3 && strtotime($shopdeca_launch_date) > strtotime($attributes['start_date'])) {
			return false;
		}

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is voucher_code.status and voucher_template.enabled is 1
	 *
	 * @return boolean
	 */
	public static function isOneUserOneTransaction($attributes = array())
	{
		$status = true;
    
    if($attributes['one_transaction_per_customer'] == 0){
      return $status;
    }else{
      $attributes['customer_email'] = Auth::user()->customer_email;
      $attributes['voucher_code']   = isset($attributes['promotions_code_number']) ? $attributes['promotions_code_number'] : NULL ;
      $get_discount = Promotion::getOrderDiscount($attributes);
      if ($get_discount) {
        \Session::put('temp_err_msg', 'Promo ini sudah melebihi batas pemakaian kuota.');
        return false;
      }
    }

		return $status;
	}

	// --------------------------------------------------------------------

	/**
	 * Check is domain match with voucher_template.domain_id [1 => bb, 2 => hb, 3 => multi domain]
	 *
	 * @return boolean
	 */
	public static function isDomainIdMatch($attributes = array())
	{
		$status = false;
		//Define Domain and Channel
    $get_domain	= get_domain();
    $domain_id	= isset($get_domain['domain_id']) ? $get_domain['domain_id'] : NULL;
    if ($domain_id == $attributes['domain_id'] || $attributes['domain_id'] == 3) {
      $status = true;
    }
    return $status;
	}

	// --------------------------------------------------------------------

	public static function validatePromotionCondition(array $promotions, array $customer, array $order_item_applied, $mode, $voucher_code = NULL)
	{
		$status = true;
    session()->put('order_item_applied', $order_item_applied);
    
		foreach ($promotions as $promotion) {
      $promotion['mode']          = $mode;
      $promotion['voucher_code']  = $voucher_code;
      
			$method = $promotion['promotions_function'];
     
      $status = PromotionCondition::$method($promotion, $customer);

      if (!$status) {
        Log::notice(''.$promotion['promotions_template_name'].' PromotionCondition error');
        return false;
      }
		}
    
		return $status;
	}
  
  // --------------------------------------------------------------------
  /**
	 * To reset freegift session and reapply freegift into session
	 *
	 * @return array
  */
  public static function applyFreegiftAuto(array $data)
  {

    $fetch_order_item = $data['fetch_order_item'];
    $freegift         = $data['freegift'];
    
    $session_freegift = session('freegift_auto');
    session()->forget('freegift_auto');
    
    //Reapply All Freegift Session
    if(!empty($session_freegift)){
      
      Log::notice('Reapply All Freegift Auto');
      foreach($session_freegift as $key => $values){
        Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].']');
        
        //Check Eksklusif
        $promotions_eksklusif = (session()->has('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ;
        if($promotions_eksklusif != 0){  //skip if eksklusif
          return $fetch_order_item;
        }
        $fetch_order_item     = PromotionHelper::checkExclusive($session_freegift[$key], $fetch_order_item);
        //End Check Eksklusif
        
        //Check Total Order Item that met condition
        $total_validated_order_item = PromotionHelper::totalValidatedOrderItem($session_freegift[$key], $fetch_order_item);
        if($total_validated_order_item <= 0){
          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is failed. No order item met the condition or item price is 0.');
          return $fetch_order_item;
        }
        //End Check Total Order Item
        
        $reshape_order_item     = PromotionHelper::reshapeOrderItem($session_freegift[$key], $fetch_order_item);
        $session_freegift[$key] = $reshape_order_item['promotions'];
        $fetch_order_item       = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value   = PromotionHelper::getTotalPromotionsPurchase($session_freegift[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($session_freegift[$key], $fetch_order_item, $total_purchase_value);
        
        $total_freegift_value = 0;
        foreach ($fetch_order_item as $order_item) {
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            $price = PromotionHelper::setItemPrice($order_item);

            $par_count = array();
            $par_count['order_item_id']     = $order_item->order_item_id;
            $par_count['fetch_order_item']  = $fetch_order_item;
            $par_count['promotions']        = $session_freegift[$key];
            $count_applied = PromotionHelper::setCountApplied($par_count);

            if($count_applied > 0){

              $par_promo_value = array();
              $par_promo_value['promotions']                  = $session_freegift[$key];
              $par_promo_value['count_applied']               = $count_applied;
              $par_promo_value['order_item']                  = $order_item;
              $par_promo_value['price']                       = $price;
              $par_promo_value['total_purchase_value']        = $total_purchase_value;
              $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
              $freegift_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $price - $freegift_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $freegift_value         = $price;
                $order_item->real_price = 0;
              }
              //End Check

              $total_freegift_value += $freegift_value;
            }
          }
        }
        
        $promotions_value = PromotionHelper::validatePromotionsValue($session_freegift[$key], $total_freegift_value);
        
        if($promotions_value > 0 || $session_freegift[$key]['promotions_mode'] == 4 || $session_freegift[$key]['promotions_mode'] == 5){
          $session_freegift[$key]['promotions_notice']  = PromotionHelper::setPromotionsNotice($session_freegift[$key]);
          $session_freegift[$key]['promotions_value']   = $promotions_value;
          session()->push('freegift_auto', $session_freegift[$key]);

          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is success');
        }else{
          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is success');
        }
      }
    }
    //End Reapply All Freegift Session
    
    //Apply New Freegift
    if(!empty($freegift)){
      Log::notice('Apply Freegift Auto');
      Log::notice('Apply ['.$freegift['promotions_name'].']');
      
      //Check Eksklusif
      $promotions_eksklusif = (session()->has('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ;
      if($promotions_eksklusif != 0){  //skip if eksklusif
        return $fetch_order_item;
      }
      $fetch_order_item     = PromotionHelper::checkExclusive($freegift, $fetch_order_item);
      //End Check Eksklusif
      
      //Check Total Order Item that met condition
      $total_validated_order_item = PromotionHelper::totalValidatedOrderItem($freegift, $fetch_order_item);
      if($total_validated_order_item <= 0){
        Log::notice('Apply ['.$freegift['promotions_name'].'] is failed. No order item met the condition or item price is 0.');
        return $fetch_order_item;
      }
      //End Check Total Order Item
      
      $reshape_order_item     = PromotionHelper::reshapeOrderItem($freegift, $fetch_order_item);
      $freegift               = $reshape_order_item['promotions'];
      $fetch_order_item       = $reshape_order_item['fetch_order_item'];
      
      $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($freegift, $fetch_order_item);
      
      $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift, $fetch_order_item, $total_purchase_value);
      
      $total_freegift_value = 0;
      foreach ($fetch_order_item as $order_item) {
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          $price = PromotionHelper::setItemPrice($order_item);

          $par_count = array();
          $par_count['order_item_id']     = $order_item->order_item_id;
          $par_count['fetch_order_item']  = $fetch_order_item;
          $par_count['promotions']        = $freegift;
          $count_applied = PromotionHelper::setCountApplied($par_count);

          if(isset($count_applied) && $count_applied > 0){

            $par_promo_value = array();
            $par_promo_value['promotions']                  = $freegift;
            $par_promo_value['count_applied']               = $count_applied;
            $par_promo_value['order_item']                  = $order_item;
            $par_promo_value['price']                       = $price;
            $par_promo_value['total_purchase_value']        = $total_purchase_value;
            $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
            $freegift_value = PromotionHelper::setPromotionsValue($par_promo_value);

            $order_item->real_price = $price - $freegift_value;

            //Check whether the price is below 0 after promotions value, then set promotions value to item price
            if($order_item->real_price < 0){
              $freegift_value         = $price;
              $order_item->real_price = 0;
            }
            //End Check

            $total_freegift_value += $freegift_value;
          }
        }
      }
      
      $promotions_value = PromotionHelper::validatePromotionsValue($freegift, $total_freegift_value);

      if($promotions_value > 0 || $freegift['promotions_mode'] == 4 || $freegift['promotions_mode'] == 5){
        $freegift['promotions_notice']  = PromotionHelper::setPromotionsNotice($freegift);
        $freegift['promotions_value']   = $promotions_value;
        session()->push('freegift_auto', $freegift);

        Log::notice('Apply ['.$freegift['promotions_name'].'] is success');
      }else{
        Log::notice('Apply ['.$freegift['promotions_name'].'] is success');
      }
    }
    //End Apply New Freegift
    
    return $fetch_order_item;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To reset voucher session and reapply voucher into session
	 *
	 * @return array
  */
  public static function applyVoucher(array $data)
  {

    $fetch_order_item = $data['fetch_order_item'];
    $voucher          = $data['voucher'];
    
    session()->forget('voucher');
    
    if(!empty($voucher)){
      Log::notice('Apply Voucher');
      
      //Check Eksklusif
      $fetch_order_item     = PromotionHelper::checkExclusive($voucher, $fetch_order_item);
      $promotions_eksklusif = (session('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ;
      if($promotions_eksklusif != 0 && $promotions_eksklusif != 2){  //skip if eksklusif
        return $fetch_order_item;
      }
      //End Check Eksklusif
      
      //Check Total Order Item that met condition
      $total_validated_order_item = PromotionHelper::totalValidatedOrderItem($voucher, $fetch_order_item);
      if($total_validated_order_item <= 0){
        Log::notice('Apply ['.$voucher['promotions_name'].'] is failed. No order item met the condition or item price is 0.');
        return $fetch_order_item;
      }
      //End Check Total Order Item
      
      $reshape_order_item = PromotionHelper::reshapeOrderItem($voucher, $fetch_order_item);
      $voucher            = $reshape_order_item['promotions'];
      $fetch_order_item   = $reshape_order_item['fetch_order_item'];
      
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
      
      $voucher_promotions_value = 0;
      foreach ($fetch_order_item as $order_item) {
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          $price = PromotionHelper::setItemPrice($order_item);

          $par_count = array();
          $par_count['order_item_id']     = $order_item->order_item_id;
          $par_count['fetch_order_item']  = $fetch_order_item;
          $par_count['promotions']        = $voucher;
          $count_applied = PromotionHelper::setCountApplied($par_count);
          Log::notice('Apply ['.$voucher['promotions_name'].'] Count Applied: '.$count_applied);
          if($count_applied > 0){

            $par_promo_value = array();
            $par_promo_value['promotions']                  = $voucher;
            $par_promo_value['count_applied']               = $count_applied;
            $par_promo_value['order_item']                  = $order_item;
            $par_promo_value['price']                       = $price;
            $par_promo_value['total_purchase_value']        = $total_purchase_value;
            $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
            $voucher_value = PromotionHelper::setPromotionsValue($par_promo_value);

            $order_item->real_price   = $price - $voucher_value;

            if(isset($voucher["promotions_function"])){
              if($voucher["promotions_function"] == "freeCheapestItems"){
                if(in_array($order_item->order_item_id, $cheapItem)){
                  $voucher_value = $price;
                  $order_item->real_price = $price - $voucher_value;
                }
              }
            }

            //Check whether the price is below 0 after promotions value, then set promotions value to item price
            if($order_item->real_price < 0){
              $voucher_value            = $price;
              $order_item->real_price   = 0;
            }
            //End Check

            $voucher_promotions_value += $voucher_value;
          }
        }
      }

      // // Kondisi freecheap
      // if(isset($voucher["promotions_function"]) && $voucher["promotions_function"] == "freeCheapestItems"){
      //   // populate cheap item
      //   foreach ($fetch_order_item as $key => $value) {
      //     $price = set_price($value->each_price, $value->discount_price);
      //     $fetch_order_item[$key]->real_price = $price;
      //   }

      //   // sort item berdasarkan harga
      //   usort($fetch_order_item, function ($a, $b) {
      //     return $a->real_price - $b->real_price;
      //   });

      //   // pisahkan cheap item
      //   $cheapItem = array();
      //   if (isset($fetch_order_item[0]) AND $fetch_order_item[1]) {
      //     $cheapItem = [$fetch_order_item[0], $fetch_order_item[1]];
      //   }

      //   $voucher_value = $fetch_order_item[0]->real_price + $fetch_order_item[1]->real_price;
      //   $voucher_promotions_value += $voucher_value;
      // }
      
      $promotions_value = PromotionHelper::validatePromotionsValue($voucher, $voucher_promotions_value);
      
      if($promotions_value > 0 || $voucher['promotions_mode'] == 4 || $voucher['promotions_mode'] == 5){
        $voucher['promotions_notice'] = PromotionHelper::setPromotionsNotice($voucher);

        if(isset($voucher["promotions_function"])){
          if($voucher["promotions_function"] == "freeCheapestItems"){
            $notice = "Selamat anda mendapat promo " . $voucher['promotions_name'] . ".";
            $voucher['promotions_notice'] = $notice;
          }
        }

        $voucher['promotions_value']  = $promotions_value;
        session()->put('voucher', $voucher);

        Log::notice('Apply ['.$voucher['promotions_name'].'] is success');
      }else{
        Log::notice('Apply ['.$voucher['promotions_name'].'] is failed. Promotions value is 0');
      }
    }
    
    return $fetch_order_item;
  }
  //--------------------------------------------------------------------
  
  /**
	 * To reset freegift session and reapply freegift into session
	 *
	 * @return array
  */
  public static function applyFreegift(array $data)
  {

    $fetch_order_item = $data['fetch_order_item'];
    $freegift         = $data['freegift'];
    
    $session_freegift = session('freegift');
    session()->forget('freegift');
    
    //Reapply All Freegift Session
    if(!empty($session_freegift)){
      foreach($session_freegift as $key => $values){  
        Log::notice('Reapply Freegift');
        
        //Check Eksklusif
        $promotions_eksklusif = (session('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ;
        if($promotions_eksklusif != 0 || ($session_freegift[$key]['promotions_eksklusif'] == 1 && ($promotions_eksklusif == 1 || $promotions_eksklusif == 2))){  //skip if eksklusif
          return $fetch_order_item;
        }
        $fetch_order_item = PromotionHelper::checkExclusive($session_freegift[$key], $fetch_order_item);
        //End Check Eksklusif
        
        //Check Total Order Item that met condition
        $total_validated_order_item = PromotionHelper::totalValidatedOrderItem($session_freegift[$key], $fetch_order_item);
        if($total_validated_order_item <= 0){
          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is failed. No order item met the condition or item price is 0.');
          return $fetch_order_item;
        }
        //End Check Total Order Item
        
        $reshape_order_item     = PromotionHelper::reshapeOrderItem($session_freegift[$key], $fetch_order_item);
        $session_freegift[$key] = $reshape_order_item['promotions'];
        $fetch_order_item       = $reshape_order_item['fetch_order_item'];
        
        $total_purchase_value   = PromotionHelper::getTotalPromotionsPurchase($session_freegift[$key], $fetch_order_item);
        
        $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($session_freegift[$key], $fetch_order_item, $total_purchase_value);
        
        $total_freegift_value = 0;
        foreach ($fetch_order_item as $order_item) {
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            $price = PromotionHelper::setItemPrice($order_item);

            $par_count = array();
            $par_count['order_item_id']     = $order_item->order_item_id;
            $par_count['fetch_order_item']  = $fetch_order_item;
            $par_count['promotions']        = $session_freegift[$key];
            $count_applied = PromotionHelper::setCountApplied($par_count);

            if($count_applied > 0){

              $par_promo_value = array();
              $par_promo_value['promotions']                  = $session_freegift[$key];
              $par_promo_value['count_applied']               = $count_applied;
              $par_promo_value['order_item']                  = $order_item;
              $par_promo_value['price']                       = $price;
              $par_promo_value['total_purchase_value']        = $total_purchase_value;
              $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
              $freegift_value = PromotionHelper::setPromotionsValue($par_promo_value);

              $order_item->real_price = $price - $freegift_value;

              //Check whether the price is below 0 after promotions value, then set promotions value to item price
              if($order_item->real_price < 0){
                $freegift_value         = $price;
                $order_item->real_price = 0;
              }
              //End Check

              $total_freegift_value += $freegift_value;
            }
          }
        }
        
        $promotions_value = PromotionHelper::validatePromotionsValue($session_freegift[$key], $total_freegift_value);
        
        if($promotions_value > 0 || $session_freegift[$key]['promotions_mode'] == 4 || $session_freegift[$key]['promotions_mode'] == 5){
          $session_freegift[$key]['promotions_notice']  = PromotionHelper::setPromotionsNotice($session_freegift[$key]);
          $session_freegift[$key]['promotions_value']   = (is_float($total_freegift_value)) ? round($total_freegift_value) : $total_freegift_value;
          session()->push('freegift', $session_freegift[$key]);

          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is success');
        }else{
          Log::notice('Reapply ['.$session_freegift[$key]['promotions_name'].'] is failed. Promotions value is 0');
        }
      }
    }
    //End Reapply All Freegift Session
    
    //Apply New Freegift
    if(!empty($freegift)){
      Log::notice('Apply Freegift');
      
      //Check Eksklusif Promotions
      $promotions_eksklusif = (session('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ;
      if($promotions_eksklusif != 0 || ($freegift['promotions_eksklusif'] == 1 && ($promotions_eksklusif == 1 || $promotions_eksklusif == 2))){  //skip if eksklusif
        return $fetch_order_item;
      }
      $fetch_order_item = PromotionHelper::checkExclusive($freegift, $fetch_order_item);
      //End Check Eksklusif Promotions
      
      //Check Total Order Item that met condition
      $total_validated_order_item = PromotionHelper::totalValidatedOrderItem($freegift, $fetch_order_item);
      if($total_validated_order_item <= 0){
        Log::notice('Apply ['.$freegift['promotions_name'].'] is failed. No order item met the condition or item price is 0.');
        return $fetch_order_item;
      }
      //End Check Total Order Item
      
      $reshape_order_item     = PromotionHelper::reshapeOrderItem($freegift, $fetch_order_item);
      $freegift               = $reshape_order_item['promotions'];
      $fetch_order_item       = $reshape_order_item['fetch_order_item'];
      
      $total_purchase_value = PromotionHelper::getTotalPromotionsPurchase($freegift, $fetch_order_item);
      
      $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($freegift, $fetch_order_item, $total_purchase_value);
      
      $total_freegift_value = 0;
      foreach ($fetch_order_item as $order_item) {
        if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
          $price = PromotionHelper::setItemPrice($order_item);

          $par_count = array();
          $par_count['order_item_id']     = $order_item->order_item_id;
          $par_count['fetch_order_item']  = $fetch_order_item;
          $par_count['promotions']        = $freegift;
          $count_applied = PromotionHelper::setCountApplied($par_count);

          if($count_applied > 0){

            $par_promo_value = array();
            $par_promo_value['promotions']                  = $freegift;
            $par_promo_value['count_applied']               = $count_applied;
            $par_promo_value['order_item']                  = $order_item;
            $par_promo_value['price']                       = $price;
            $par_promo_value['total_purchase_value']        = $total_purchase_value;
            $par_promo_value['use_maximum_value_discount']  = $use_maximum_value_discount;
            $freegift_value = PromotionHelper::setPromotionsValue($par_promo_value);

            $order_item->real_price = $price - $freegift_value;

            //Check whether the price is below 0 after promotions value, then set promotions value to item price
            if($order_item->real_price < 0){
              $freegift_value         = $price;
              $order_item->real_price = 0;
            }
            //End Check

            $total_freegift_value += $freegift_value;
          }
        }
      }
      
      $promotions_value = PromotionHelper::validatePromotionsValue($freegift, $total_freegift_value);
      
      if($promotions_value > 0 || $freegift['promotions_mode'] == 4 || $freegift['promotions_mode'] == 5){
        $freegift['promotions_notice']  = PromotionHelper::setPromotionsNotice($freegift);
        $freegift['promotions_value']   = $promotions_value;
        session()->push('freegift', $freegift);

        Log::notice('Apply ['.$freegift['promotions_name'].'] is success');
      }else{
        Log::notice('Apply ['.$freegift['promotions_name'].'] is failed. Promotions value is 0');
      }
    }
    
    //End Apply New Freegift
    
    return $freegift;
  }
  //--------------------------------------------------------------------
  
  /**
	 * Main function to apply voucher or freegift to session
	 *
	 * @return array
  */
	public static function applyPromotion(array $promotions, array $customer)
	{
    $promotions_data['promotions_id']                       = $promotions['promotions_template_id'];
		$promotions_data['promotions_code']                     = (isset($promotions['promotions_code_number'])) ? $promotions['promotions_code_number'] : NULL ;
		$promotions_data['promotions_name']                     = $promotions['promotions_template_name'];
		$promotions_data['promotions_name_for_customer']        = $promotions['promotions_template_name_for_customer'];
		$promotions_data['promotions_mode']                     = $promotions['promotions_template_mode']; //1: Percentage | 2: Nominal | 3: Free Cheapest Item | 4: Freeshipping | 5: Freeitem
		$promotions_data['promotions_mode_value']               = $promotions['promotions_template_mode_value'];
    $promotions_data['promotions_max_discount_value']       = $promotions['max_discount_value'];
    $promotions_data['promotions_applicable']               = $promotions['promotions_template_applicable'];
    $promotions_data['promotions_eksklusif']                = $promotions['eksklusif_voucher'];
    $promotions_data['promotions_exclude_sale_item']        = $promotions['exclude_sale_item'];
    $promotions_data['promotions_exclude_normal_item']      = $promotions['exclude_normal_item'];
    $promotions_data['promotions_lowest_price']             = $promotions['lowest_price'];
    $promotions_data['promotions_allow_benka_point']        = $promotions['allow_benka_point'];
    $promotions_data['promotions_allow_exclusive_product']  = $promotions['allow_exclusive_product'];
    $promotions_data['promotions_maximum_quota']            = $promotions['promotions_maximum_quota'];
    $promotions_data['promotions_quota_usage']              = $promotions['promotions_quota_usage'];
    $promotions_data['promotions_order_id']                 = $promotions['order_id'];
    $promotions_data['promotions_need_bank_id']             = (isset($promotions['need_bank_id'])) ? $promotions['need_bank_id'] : false;
    $promotions_data['promotions_need_cache']               = (isset($promotions['need_cache'])) ? $promotions['need_cache'] : false;
    
		$promotions_type = 'voucher';
    if(is_null($promotions_data['promotions_code'])){
      $promotions_type = 'freegift';
      if(!$promotions_data['promotions_need_bank_id']){
        $promotions_type = 'freegift_auto';
      }
    }
    
    $promotions_data['promotions_type'] = $promotions_type;

    // Tambah kondisi untuk freecheap
    $get_template_condition = Promotion::getPromotionsTemplateCondition(array("promotions_template_id" => $promotions['promotions_template_id']));

    foreach ($get_template_condition as $z) {
      if($z['promotions_function'] == "freeCheapestItems"){
        $promotions_data['promotions_function'] = $z['promotions_function'];
      }
    }
    
    Log::notice('##### '.$promotions['promotions_template_name'].' applyPromotion ['.$promotions_type.'] started #####');
    
    $fetch_order_item = OrderItem::fetchOrderItem();
    if(count($fetch_order_item) > 0){
      session()->forget('promotions_eksklusif');
      
      $freegift_auto  = ($promotions_type == 'freegift_auto') ? $promotions_data : array();
      $voucher        = ($promotions_type == 'voucher') ? $promotions_data : session('voucher');
      $freegift       = ($promotions_type == 'freegift') ? $promotions_data : array();
      
      //Apply or Freegift Auto
      $data_freegift_auto['fetch_order_item'] = $fetch_order_item;
      $data_freegift_auto['freegift']         = (!empty($freegift_auto)) ? $freegift_auto : array();
      $freegift_auto_reshape                  = Self::applyFreegiftAuto($data_freegift_auto);
      
      //Apply or Reapply Voucher
      $data_voucher['fetch_order_item']       = $freegift_auto_reshape;
      $data_voucher['voucher']                = (!empty($voucher)) ? $voucher : array();
      $voucher_reshape                        = Self::applyVoucher($data_voucher);

      //Apply or Reapply Freegift
      $data_freegift['fetch_order_item']      = $voucher_reshape;
      $data_freegift['freegift']              = (!empty($freegift)) ? $freegift : array();
      Self::applyFreegift($data_freegift);
    }

		Log::notice('##### '.$promotions['promotions_template_name'].' applyPromotion ['.$promotions_type.'] ended #####');

		return (isset($promotions_data) && !empty($promotions_data)) ? $promotions_data : array() ;
	}
  //--------------------------------------------------------------------

	public static function filterPromotions(array $promotions, array $customer)
  {
    return array_filter($promotions, function ($promotion) use ($customer) {
      //return $account->isOfType($accountType);
      return ($promotion['type'] == $customer && $promotion['status'] === 'active');
    });
  }

}
