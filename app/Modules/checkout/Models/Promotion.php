<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Cart;
use DB;
use Log;

use Illuminate\Http\Request;

use \App\Modules\Checkout\Models\PromotionCondition;

class Promotion extends model {

	/**
	 * Fetch Voucher Object
	 *
	 * @return Object
	 */
	public static function getVoucher($attributes = array())
	{
		DB::enableQueryLog();
    $voucher_object = 
      DB::table('promotions_code as prom_code')
        ->leftjoin('promotions_template as prom_template', 'prom_code.promotions_template_id', '=', 'prom_template.promotions_template_id')
        ->leftjoin('promotions_condition as prom_cond', 'prom_template.promotions_template_id', '=', 'prom_cond.promotions_template_id')
        ->leftjoin('promotions_type_condition as d', 'prom_cond.promotions_type_condition', '=', 'prom_type.promotions_type_id')
      ->select(DB::raw('
          prom_code.promotions_code_number, 
          prom_code.`promotions_template_id`, 
          prom_code.promotions_code_usage, 
          prom_code.`customer_email`, 
          prom_code.status, 
          prom_code.`duration`, 
          prom_code.`createddate`,
          prom_template.`promotions_template_name`, 
          prom_template.`promotions_template_mode`, 
          prom_template.`promotions_template_mode_value`, 
          prom_template.`promotions_template_one_multiple`, 
          prom_template.`promotions_template_applicable`, 
          prom_template.start_date, 
          prom_template.end_date, 
          prom_template.enabled, 
          prom_template.createddate, 
          prom_template.updateddate, 
          prom_template.domain_id, 
          prom_template.free_shipping, 
          prom_template.free_cheapest_item, 
          prom_template.max_discount_value, 
          prom_template.eksklusif_voucher,
          prom_template.is_freegift_or_voucher,
          prom_template.exclude_sale_item,
          prom_template.exclude_normal_item,
          prom_template.lowest_price,
          prom_template.allow_benka_point,
          prom_template.allow_exclusive_product,
          prom_template.one_transaction_per_customer,
          prom_template.promotions_maximum_quota,
          prom_template.promotions_quota_usage,
          prom_cond.`promotions_condition_id`, 
          prom_cond.`promotions_condition_parent_id`, 
          prom_cond.`promotions_type_condition`, 
          prom_cond.`promotions_type_all_required`, 
          prom_cond.`promotions_type_rules_type`, 
          prom_cond.`promotions_type_equal_type`, 
          prom_cond.`promotions_type_equal_value`, 
          prom_cond.`use_as_action_condition`,
          prom_type.`promotions_type_id`, 
          prom_type.`promotions_type_is_child`, 
          prom_type.`promotions_type_name`, 
          prom_type.`promotions_type_have_child`, 
          prom_type.`promotions_type_status`
        '))
      ->where('prom_code.promotions_code_number', '=', $attributes['promotions_code_number'])
      ->where('prom_code.status', '=', 1)
      ->where('prom_type.promotions_type_status', '=', 1)
      ->get();

    return $voucher_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Voucher Object
	 *
	 * @return Object
	 */
	public static function getVoucherCodeTemplate($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
		DB::enableQueryLog();
    $voucher_object = 
      DB::table('promotions_code as prom_code')
        ->leftjoin('promotions_template as prom_template', 'prom_code.promotions_template_id', '=', 'prom_template.promotions_template_id')
      ->select(DB::raw('
        prom_code.promotions_code_number, 
        prom_code.`promotions_template_id`, 
        prom_code.promotions_code_usage, 
        prom_code.`customer_email`, 
        prom_code.status, 
        prom_code.`duration`, 
        prom_code.`createddate` AS code_createddate,
        prom_template.`promotions_template_name`,
        prom_template.`promotions_template_name_for_customer`,
        prom_template.`promotions_template_mode`,
        prom_template.`promotions_template_mode_value`,
        prom_template.`promotions_template_one_multiple`,
        prom_template.`promotions_template_applicable`,
        prom_template.start_date, 
        prom_template.end_date, 
        prom_template.enabled, 
        prom_template.createddate, 
        prom_template.updateddate, 
        prom_template.domain_id, 
        prom_template.free_shipping, 
        prom_template.free_cheapest_item, 
        prom_template.max_discount_value,
        prom_template.eksklusif_voucher,
        prom_template.is_freegift_or_voucher,
        prom_template.exclude_sale_item,
        prom_template.exclude_normal_item,
        prom_template.lowest_price,
        prom_template.allow_benka_point,
        prom_template.allow_exclusive_product,
        prom_template.one_transaction_per_customer,
        prom_template.promotions_maximum_quota,
        prom_template.promotions_quota_usage
      '))
      ->where('prom_code.promotions_code_number', '=', $attributes['promotions_code_number'])
      ->where('prom_code.status', '=', 1)
      ->where('prom_template.enabled', '=', 1)
      ->first();

    DB::setFetchMode(\PDO::FETCH_CLASS);

    return $voucher_object;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch voucher_code
	 *
	 * @return Array
	 */
	public static function getVoucherCode($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
		$voucher_code = 
      DB::table('promotions_code')
      ->select(DB::raw('
        promotions_code_number, 
        promotions_template_id, 
        promotions_code_usage, 
        customer_email, 
        status, 
        duration, 
        createddate
      '))
      ->where('promotions_code_number', '=', $attributes['promotions_code_number'])
      -f>where('status', '=', 1)
      ->first();
    DB::setFetchMode(\PDO::FETCH_CLASS);

		return $voucher_code;
	}

	// --------------------------------------------------------------------
  
  /**
	 * Fetch promotions_template for freegift
	 *
	 * @return Array
	 */
	public static function getFreegiftTemplate($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
    DB::enableQueryLog();
		$voucher_template = 
      DB::table('promotions_template')
      ->join('promotions_condition', 'promotions_template.promotions_template_id', '=', 'promotions_condition.promotions_template_id')
      ->select('promotions_template.*')
      ->where('promotions_condition.promotions_type_condition', '=', 7)
      ->where('is_freegift_or_voucher', '=', 1)
      ->where('enabled', '=', 1)
      ->whereRaw('promotions_template.end_date >= "'.date("Y-m-d H:i:s").'"')
      ->whereRaw('(promotions_condition.promotions_type_equal_value = "'.$attributes['bank_id'].'" OR promotions_condition.promotions_type_equal_value IN (39, 47, 48))')
      ->distinct()
      ->get();
    DB::setFetchMode(\PDO::FETCH_CLASS);
    \Log::notice(DB::getQueryLog());

		return $voucher_template;
	}

	// --------------------------------------------------------------------
  
	/**
	 * Fetch promotions_template for freegift
	 *
	 * @return Array
	 */
	public static function getFreegiftAutoTemplate($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
		$voucher_template = 
      DB::table('promotions_template')
      ->select(DB::raw('
        promotions_template_id, 
        promotions_template_name,
        promotions_template_name_for_customer,
        promotions_template_mode, 
        promotions_template_mode_value,
        promotions_template_one_multiple,
        promotions_template_applicable,
        start_date, 
        end_date, 
        enabled, 
        createddate, 
        updateddate, 
        domain_id, 
        free_shipping, 
        free_cheapest_item, 
        max_discount_value, 
        eksklusif_voucher,
        is_freegift_or_voucher,
        exclude_sale_item,
        exclude_normal_item,
        lowest_price,
        allow_benka_point,
        allow_exclusive_product,
        one_transaction_per_customer,
        promotions_maximum_quota,
        promotions_quota_usage
      '))
      ->where('is_freegift_or_voucher', '=', 1)
      ->where('enabled', '=', 1)
      ->whereRaw('promotions_template.end_date >= "'.date("Y-m-d H:i:s").'"')
      ->whereRaw('promotions_template_id NOT IN (SELECT promotions_template_id FROM promotions_condition WHERE promotions_type_condition = 7)')
      ->distinct()
      ->get();
    DB::setFetchMode(\PDO::FETCH_CLASS);
    
		return $voucher_template;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch Voucher Object
	 *
	 * @return Object
	 */
	public static function getPromotionsTemplateCondition($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
		DB::enableQueryLog();
    $voucher_template_condition = 
      DB::table('promotions_template as prom_template')
        ->leftjoin('promotions_condition as prom_cond', 'prom_template.promotions_template_id', '=', 'prom_cond.promotions_template_id')
        ->leftjoin('promotions_type_condition as prom_type', 'prom_cond.promotions_type_condition', '=', 'prom_type.promotions_type_id')
        //->leftjoin('promotions_code as prom_code', 'prom_template.promotions_template_id', '=', 'prom_code.promotions_template_id')
      ->select(DB::raw('
        prom_template.`promotions_template_id`, 
        prom_template.`promotions_template_name`,
        prom_template.`promotions_template_mode`, 
        prom_template.`promotions_template_mode_value`, 
        prom_template.`promotions_template_one_multiple`,
        prom_template.`promotions_template_applicable`,
        prom_template.start_date, 
        prom_template.end_date, 
        prom_template.enabled, 
        prom_template.createddate, 
        prom_template.updateddate, 
        prom_template.domain_id, 
        prom_template.free_shipping, 
        prom_template.free_cheapest_item, 
        prom_template.max_discount_value, 
        prom_template.eksklusif_voucher,
        prom_template.is_freegift_or_voucher,
        prom_template.exclude_sale_item,
        prom_template.exclude_normal_item,
        prom_template.lowest_price,
        prom_template.allow_benka_point,
        prom_template.allow_exclusive_product,
        prom_template.one_transaction_per_customer,
        prom_template.promotions_maximum_quota,
        prom_template.promotions_quota_usage,
        prom_cond.`promotions_condition_id`, 
        prom_cond.`promotions_condition_parent_id`, 
        prom_cond.`promotions_type_condition`, 
        prom_cond.`promotions_type_all_required`, 
        prom_cond.`promotions_type_rules_type`, 
        prom_cond.`promotions_type_equal_type`, 
        prom_cond.`promotions_type_equal_value`, 
        prom_cond.`use_as_action_condition`,
        prom_type.`promotions_type_id`, 
        prom_type.`promotions_type_is_child`, 
        prom_type.`promotions_type_name`, 
        prom_type.`promotions_type_have_child`, 
        prom_type.`promotions_type_status`, 
        prom_type.promotions_function
      '))
      ->where('prom_template.promotions_template_id', '=', $attributes['promotions_template_id'])
      ->where('prom_type.promotions_type_status', '=', 1)
      ->orderBy('prom_cond.promotions_type_condition', 'desc')
      ->get();
    DB::setFetchMode(\PDO::FETCH_CLASS);
        
    return $voucher_template_condition;
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch voucher_template
	 *
	 * @return Array
	 */
	public static function getOrderDiscount($attributes = array())
	{
    DB::setFetchMode(\PDO::FETCH_ASSOC);
		$voucher_result = DB::table('order_discount')
      ->select(DB::raw('purchase_code, voucher_code, discount_id, customer_id, customer_email, discount_type, domain_id'))
      ->where('is_laravel', '=', 1);
    
    if(isset($attributes['voucher_code']) && $attributes['voucher_code'] != NULL){
      $voucher_result->where('voucher_code', '=', $attributes['voucher_code']);
    }else{
      $voucher_result->where('discount_id', '=', $attributes['promotions_template_id']);
    }
    
    if(isset($attributes['customer_email'])){
      $voucher_result->where('customer_email', '=', $attributes['customer_email']);
    }
                     
    if(isset($attributes['get_total_order']) && $attributes['get_total_order'] == TRUE){
      $voucher_template = $voucher_result->distinct()->get();
    }else{
      $voucher_template = $voucher_result->get();
    }
    
    DB::setFetchMode(\PDO::FETCH_CLASS);

		return $voucher_template;
	}

  /**
   * Last ID Promotion Code exist
   *
   * @access public
   * @param string $subscriber_email
   * @return array of object(brand object)
   */
  public static function last_id_promotion_code() {
    $lastpromotion = DB::table('promotions_code')
                      ->orderBy('promotions_code_id','desc')
                      ->first();

    if (!empty($lastpromotion)) {
      return $lastpromotion->promotions_code_id;
    } else {
      return false;
    }    
  }

  /**
   * Create Promotion Code
   *
   * @access public
   * @param string $subscriber_email
   * @return array of object(brand object)
   */
  public static function create_promotion_code($input) {
      $insert_promotion = DB::table('promotions_code')->insert(
                            [
                              'promotions_template_id' => $input['promotion_template_id'], 
                              'promotions_code_number' => $input['promotion_code_number'], 
                              'customer_email' => $input['customer_email'], 
                              'status' => $input['status'], 
                              'duration' => $input['duration'], 
                              'created_by' => $input['created_by'], 
                              'createddate' => $input['createddate']                             
                            ]
                          );

      if (!empty($insert_promotion)) {
        return $insert_promotion;
      } else {
        return false;
      }
  }

}
