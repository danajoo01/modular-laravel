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

class OrderPayment extends Model {
  
  public static function createOrderPayment(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process OrderPayment: Started');
    
    $domain_id        = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_email   = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $purchase_code    = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $payment_method   = (isset($data['payment_method'])) ? $data['payment_method'] : NULL ;
    $klikBCA_user_id  = (isset($data['klikbca_user_id'])) ? $data['klikbca_user_id'] : NULL ;
    $payment_status   = ($klikBCA_user_id != NULL) ? 'new' : NULL ;
    $grand_total      = $data['total']['grand_total'];
    $transactionDate  = date('Y-m-d H:i:s', strtotime("now"));
    
    if($purchase_code == NULL){
      Log::notice('Process OrderPayment: Purchase Code is missing');
      return false;
    }
    
    if($payment_method == NULL){
      Log::notice('Process OrderPayment: Payment Method is missing');
      return false;
    }
    
    $payment_type_transfer = DB::table('master_payment')->where('master_payment_id', '=',  $payment_method)->value('master_payment_type_transfer');
    if($payment_type_transfer != 1){
      $payment_type_transfer = 2;
    }
    
    //**Create Order Payment**//
    $create_order_payment['purchase_code']          = $purchase_code;
    $create_order_payment['customer_email']         = $customer_email;
    $create_order_payment['master_payment_id']      = $payment_method;
    $create_order_payment['payment_type_transfer']  = $payment_type_transfer;
    $create_order_payment['payment_status']         = $payment_status;
    $create_order_payment['klikbcaUserId']          = $klikBCA_user_id;
    $create_order_payment['totalAmount']            = $grand_total;
    $create_order_payment['transactionDate']        = $transactionDate;
    $create_order_payment['status']                 = 0;
    $create_order_payment['domain_id']              = $domain_id;
    //**End Create Order Payment**//
    DB::enableQueryLog();
    $order_payment_id = DB::table('order_payment')->insertGetId($create_order_payment);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$order_payment_id){
      Log::notice('Process OrderPayment: Insert to order_payment failed. Executed Time: '.$time_executed);
      return false;
    }else{
      Log::notice('Process OrderPayment: Success. Executed Time: '.$time_executed);
    }
    
    //Insert Log
    $log_start = microtime(true);
    
    $create_order_payment_log = array();
    $create_order_payment_log['purchase_code']          = $purchase_code;
    $create_order_payment_log['customer_email']         = $customer_email;
    $create_order_payment_log['master_payment_id']      = $payment_method;
    $create_order_payment_log['klikbcaUserId']          = $klikBCA_user_id;
    $create_order_payment_log['totalAmount']            = $grand_total;
    $create_order_payment_log['transactionDate']        = $transactionDate;
    $create_order_payment_log['payment_type_transfer']  = $payment_type_transfer;
    $create_order_payment_log['domain_id']              = $domain_id;
    
    $order_payment_log_id = DB::table('order_payment_log')->insertGetId($create_order_payment_log);
    
    $log_executed  = microtime(true) - $log_start;
    if(!$order_payment_log_id){
      Log::notice('Process OrderPayment: Insert to order_payment_log failed. Executed Time: '.$log_executed);
      return false;
    }else{
      Log::notice('Process OrderPayment: Success insert to order_payment_log. Executed Time: '.$log_executed);
      $create_order_payment['order_payment_id'] = $order_payment_id;
      return $create_order_payment;
    }
    //End Insert Log
  }
  
  public static function updateOrderPayment($purchase_code, array $data)
  {
  	$update_order_payment = DB::table('order_payment')
      ->where('purchase_code', $purchase_code)
      ->update($data);

    return $update_order_payment;
  }
  
  public static function updateOrderPaymentVeritrans(array $data, $charge_veritrans)
  {

    $time_start = microtime(true);
    Log::notice('Process updateOrderPayment: Started');
    
    $payment_method = $data['payment_method'];
    
    $purchase_code  = (isset($charge_veritrans['order_id'])) ? $charge_veritrans['order_id'] : NULL;
    $status_code    = (isset($charge_veritrans['code'])) ? $charge_veritrans['code'] : NULL;
    $bin_number     = (isset($charge_veritrans['masked_card'])) ? $charge_veritrans['masked_card'] : NULL;
    $cc_holder      = ucwords(strtolower($data['cc_holder']));
    
    $status = ($status_code == 200) ? 1 : 3 ; //Set 1 if approved
    
    if($status_code == 201 && ($payment_method == 24 || $payment_method == 4 || $payment_method == 3 || $payment_method == 343)){ //24: Indomaret | 4: KlikPay | 3: KlikBCA
      $status = 0;
      $update_item['payment_status'] = 'new';
    }
    
    $update_item['status']      = $status;
    $update_item['bin_number']  = $bin_number;
    $update_item['cc_holder']   = $cc_holder;
    $update_order_payment = Self::updateOrderPayment($purchase_code, $update_item);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$update_order_payment){
      Log::notice('Process updateOrderPayment: Update order_payment is failed. Executed Time: '. $time_executed);
      return false;
    }
    
    Log::notice('Process updateOrderPayment: Success. Executed Time: '. $time_executed);
    return $update_order_payment;
  }
}