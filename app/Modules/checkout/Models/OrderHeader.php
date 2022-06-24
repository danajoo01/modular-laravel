<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Cart;
use DB;
use Log;
use Datetime;
use \App\Customer;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\Payment;
use \App\Modules\Checkout\Models\Shipping;

class OrderHeader extends Model {
  
  public static function createOrderHeader(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process OrderHeader: Started');
    
    $domain_id        = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $payment_method   = (isset($data['payment_method'])) ? $data['payment_method'] : NULL ;
    $master_payment   = (isset($data['master_payment'])) ? $data['master_payment'] : NULL;
    $channel          = (isset($data['channel'])) ? $data['channel'] : NULL ;
    $customer_id      = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $customer_email   = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $customer_address = ($data['customer_address'] != NULL || !empty($data['customer_address'])) ? $data['customer_address'] : array() ;
    $shipping_id      = (isset($data['shipping_id'])) ? $data['shipping_id'] : NULL ;
    $shipping_type    = (isset($data['shipping_type'])) ? $data['shipping_type'] : NULL ;
    $auth_cs          = (isset($data['auth_cs'])) ? $data['auth_cs'] : 0 ;
    $ip_address       = (isset($data['ip_address'])) ? $data['ip_address'] : NULL ;
    
    if(empty($customer_address) || !isset($customer_address['shipping']) || !isset($customer_address['billing'])){
      Log::notice('Process OrderHeader: Session Customer Address is empty');
      return false;
    }
    
    //Set Flag Purchase
    $flag_purchase  = NULL;
    $type_transfer  = !is_null($master_payment) ? $master_payment->master_payment_type_transfer : NULL;
    if($type_transfer == 4){
      $flag_purchase = "OS";
    }
    //End Set Flag Purchase
    
    //Grand Total
    $shipping_finance = $data['total']['shipping_cost'];
    $shipping_cost    = $data['total']['shipping_cost_raw'];
    $paycode          = $data['total']['paycode'];
    $purchase_price   = $data['total']['base_subtotal'];
    $discount         = $data['total']['total_freegift_auto_value'] + $data['total']['total_freegift_value'] + $data['total']['total_voucher_value'];
    $benka_point      = $data['total']['benka_point'];
    $grand_total      = $data['total']['grand_total'];
    //End Grand Total
    
    //Check Popup Store
    $params_check_popup_store = array();
    $params_check_popup_store['shipping_name'] = $customer_address['shipping']['address_city'];
    $get_popup_store = Shipping::getPopupStore($params_check_popup_store);
    if($get_popup_store){
      foreach($get_popup_store as $popup_store){
        if($get_popup_store && $payment_method == $popup_store->master_payment_id){
          $popup_address  = $popup_store->address;
          $popup_province = $popup_store->province;
          $popup_city     = $popup_store->city;
          $popup_postcode = $popup_store->postcode;
          $popup_phone    = $popup_store->phone;
        }
      }
    }
    //End Check
    
    //Set Priority
    if($shipping_type == 3 || $shipping_type == 4){
      $datetime = new DateTime('tomorrow');
      $priority_status  = ($shipping_type == 3) ? 1 : 2 ; //1: Same Day Delivery | 2: Next Day Delivery
      $priority_date    = ($shipping_type == 3) ? date('Y-m-d') : $datetime->format('Y-m-d') ;
    }else{
      $priority_status  = 0;
      $priority_date    = '';
    }
    //End Set Priority
    
    $purchase_code = Self::createPurchaseCode();
    $purchase_date = date("Y-m-d H:i:s");        
    
    //Create Order Header
    $create_order_header = array();
    $create_order_header['customer_id']     = $customer_id;
    $create_order_header['customer_email']  = $customer_email;
    $create_order_header['channel']         = $channel;
    $create_order_header['domain_id']       = $domain_id;
    $create_order_header['purchase_code']   = $purchase_code;
    $create_order_header['purchase_date']   = $purchase_date;
    $create_order_header['flag_purchase']   = $flag_purchase;
    
    $create_order_header['shipping_id']             = $shipping_id;
    $create_order_header['shipping_address']        = isset($popup_address) ? $popup_address : $customer_address['shipping']['address_street'];
    $create_order_header['order_shipping_address']  = isset($popup_address) ? $popup_address : $customer_address['shipping']['address_street'];
    $create_order_header['order_province']          = isset($popup_province) ? $popup_province : $customer_address['shipping']['address_province'];
    $create_order_header['order_city']              = isset($popup_city) ? $popup_city : $customer_address['shipping']['address_city'];
    $create_order_header['order_postcode']          = isset($popup_postcode) ? $popup_postcode : $customer_address['shipping']['address_postcode'];
    $create_order_header['order_phone']             = isset($popup_phone) ? $popup_phone : $customer_address['shipping']['address_phone'];
    $create_order_header['billing_address']         = $customer_address['billing']['address_street'];
    
    $create_order_header['shipping_cost']     = $shipping_cost ;
    $create_order_header['shipping_finance']  = $shipping_finance;
    $create_order_header['paycode']           = $paycode;
    $create_order_header['credit_use']        = $benka_point;
    $create_order_header['purchase_price']    = $purchase_price;
    $create_order_header['discount']          = $discount;
    $create_order_header['grand_total']       = $grand_total;
    
    $create_order_header['priority']          = $priority_status;
    $create_order_header['priority_date']     = $priority_date;
    
    $create_order_header['auth_cs']           = $auth_cs;
    $create_order_header['ip_address']        = $ip_address;
    
    //stamp value currency
    $stampValue                                     = Self::benkaStampCurrency();    
    $create_order_header['stamp_currency']          = isset($stampValue->config_value) ? $stampValue->config_value : null;
    //End Create Order Header
    
    $order_header_id = DB::table('order_header')->insertGetId($create_order_header);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$order_header_id){
      Log::notice('Process OrderHeader: Insert to order_header failed. Executed Time: '.$time_executed);
      return false;
    }else{
      Log::notice('Process OrderHeader: Success insert to order_header. Executed Time: '.$time_executed);
    }
    
    //Insert Log
    $log_start = microtime(true);
    
    $create_order_header_log = array();
    $create_order_header_log['purchase_code']     = $purchase_code;
    $create_order_header_log['purchase_date']     = $purchase_date;
    $create_order_header_log['customer_id']       = $customer_id;
    $create_order_header_log['customer_email']    = $customer_email;
    $create_order_header_log['shipping_id']       = $shipping_id;
    $create_order_header_log['shipping_address']  = $customer_address['shipping']['address_street'];
    $create_order_header_log['billing_address']   = $customer_address['billing']['address_street'];
    $create_order_header_log['purchase_price']    = $purchase_price;
    $create_order_header_log['shipping_cost']     = $shipping_cost;
    $create_order_header_log['shipping_finance']  = $shipping_finance;
    $create_order_header_log['paycode']           = $paycode;
    $create_order_header_log['discount']          = $discount;
    $create_order_header_log['credit_use']        = $benka_point;
    $create_order_header_log['grand_total']       = $grand_total;
    $create_order_header_log['channel']           = $channel;
    $create_order_header_log['domain_id']         = $domain_id;
    
    $order_header_log_id = DB::table('order_header_log')->insertGetId($create_order_header_log);
    
    $log_executed  = microtime(true) - $log_start;
    if(!$order_header_log_id){
      Log::notice('Process OrderHeader: Insert to order_header_log failed. Executed Time: '.$log_executed);
      return false;
    }else{
      Log::notice('Process OrderHeader: Success insert to order_header_log. Executed Time: '.$log_executed);
      $create_order_header['order_header_id'] = $order_header_id;
      return $create_order_header;
    }
    //End Insert Log
  }
  
  //benka stamp currency
  protected static function benkaStampCurrency(){            
      return DB::table('benka_stamp_config')->select('config_value')->first();      
  }
  //end benka stamp currency
  
  public static function createPurchaseCode()
  {
    do{
      $purchase_code = mt_rand(1,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);
      $is_purchase_code_exist = DB::table('order_header')
        ->select(DB::raw('order_id'))
        ->where('purchase_code', '=', $purchase_code)
        ->count();
      
    }while ($is_purchase_code_exist > 0);
    
    return $purchase_code;
  }
  
  public static function fetchOrderHeader(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_header = DB::table('order_header')
      ->join('customer', 'customer.customer_id', '=', 'order_header.customer_id')
      ->select(DB::raw('order_header.*, customer.customer_fname, customer.customer_lname, customer.customer_email'))
      ->where($data)
      ->first();
  	$queries = DB::getQueryLog();
  	return $read_order_header;
  }
}
