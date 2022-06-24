<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use Cart;
use DB;
use Log;
use \App\Customer;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\OrderHeader;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\OrderDiscount;
use \App\Modules\Checkout\Models\PromotionTemplate;
use \App\Modules\Checkout\Models\PromotionCondition;
use \App\Modules\Checkout\Models\Veritrans;
use \App\Modules\Checkout\Models\Kredivo;
use \App\Modules\Checkout\Models\KlikPay;
use \App\Modules\Checkout\Models\KlikPayKeys;
use \App\Modules\Product\Models\Product;
use \App\Modules\Product\Models\SolrSync;
use Datetime;
use Session;
// use Mail;
use App\Libraries\Mail;
use Veritrans_Transaction;
use Request;

class Order extends Model {

	public static function getOrderSession()
	{
    $order_session = array();

    $order_session['customer_address']  = (session('customer_address')) ? session('customer_address') : NULL ;
    
    $order_session['shipping_weight']   = (session('shipping_weight')) ? session('shipping_weight') : NULL ; //kg
    $order_session['shipping_id']       = (session('shipping_id')) ? session('shipping_id') : NULL ;
    $order_session['shipping_type']     = (session('shipping_type')) ? session('shipping_type') : NULL ; //1: Regular | 3: Same Day Shipping | 4: Next Day Shipping
    $order_session['payment_method']    = (session('payment_method')) ? session('payment_method') : NULL ;
    $order_session['bank_id']           = (session('bank_id')) ? session('bank_id') : NULL ;
    $order_session['bank_name']         = (session('bank_name')) ? session('bank_name') : NULL ;
    $order_session['selected_day']      = (session('selected_day')) ? session('selected_day') : NULL ; //Day Number
    $order_session['gender']            = (session('gender')) ? session('gender') : NULL ; //1: Male | 2:Female
    $order_session['shipping_city']     = (session('shipping_city')) ? session('shipping_city') : NULL ;
    $order_session['platform_domain']   = (session('platform_domain')) ? session('platform_domain') : NULL ;
    $order_session['platform_device']   = (session('platform_device')) ? session('platform_device') : NULL ;
    $order_session['promo_page']        = (session('promo_page')) ? session('promo_page') : NULL ;
    $order_session['brand_page']        = (session('brand_page')) ? session('brand_page') : NULL ;
    $order_session['utm_source']        = (session('utm_source')) ? session('utm_source') : NULL ;

    return $order_session;
	}

	public static function clearOrderSession($is_desktop = TRUE)
	{
    session()->forget('shipping_id');
    session()->forget('shipping_type');
    session()->forget('freeshipping_promotions');
    session()->forget('paycode');
    session()->forget('freegift_auto');
    session()->forget('freegift');
    session()->forget('promotions_eksklusif');
    session()->forget('allow_benka_point');
    if($is_desktop){ //Clear voucher session if on desktop
      session()->forget('payment_method');
      session()->forget('bank_id');
      session()->forget('voucher');
      session()->forget('voucher_code');
      session()->forget('benka_point');
    }
    if(!session('bin_number_raw') && !session('bin_number_mandiri_raw') && !session('voucher')){
      session()->forget('bank_id');
    }
    session()->forget('is_not_idle');
	}
  
  public static function setDataSubmitOrder(array $data)
  {
    $data_submit_order = array();    
    //Request
    $data_submit_order['klikbca_user_id']       = $data['request']->get('klikbca-user-id');
    $data_submit_order['token_id']              = $data['request']->get('token-id');
    $data_submit_order['cc_holder']             = $data['request']->get('cc-holder');
    $data_submit_order['kredivo_payment_type']  = $data['request']->get('kredivo-payment-type');
    
    //Domain
    $domain = get_domain();
    $data_submit_order['domain_id'] = $domain['domain_id'];
    
    //Set Channel
    $channel        = $domain['channel'];
    $order_channel  = $channel == 1 || $channel == 3 || $channel == 5 ? 1 : 2; //1: Desktop | 2: Mobile
    
    $data_submit_order['channel']   = $order_channel;
    //End Set Channel
    
    //Customer Detail
    $auth = Auth::user();
    $data_submit_order['customer_id']     = $auth->customer_id;
    $data_submit_order['customer_email']  = $auth->customer_email;
    $data_submit_order['customer_fname']  = $auth->customer_fname;
    $data_submit_order['customer_lname']  = $auth->customer_lname;    
    //Order Session
    $order_session = Self::getOrderSession();
    $data_submit_order['shipping_type']     = $order_session['shipping_type']; //1: Regular | 2: Same Day Shipping | 3: Next Day Shipping
    $data_submit_order['payment_method']    = $order_session['payment_method'];
    $data_submit_order['customer_address']  = $order_session['customer_address'];
    $data_submit_order['master_payment']    = DB::table('master_payment')->where('master_payment_id', '=',  $order_session['payment_method'])->first();
    
    //Check Shipping ID, set default if shipping ID is NULL
    $data_submit_order['shipping_id']  = $order_session['shipping_id'];
    if($order_session['shipping_id'] == NULL){
      try {
        $list_shipping_method = Shipping::getShippingMethod() ;
        $data_submit_order['shipping_id'] = $list_shipping_method[0]['id'];
      } catch (\Exception $e) {
        //Customer address data is not match with shipping
        Log::error($e);
        $data_submit_order['shipping_id'] = NULL;
      }
    }
    
    //Cart & Order Item  
    $fetch_cart       = CheckoutCart::fetchCart();
    $fetch_order_item = OrderItem::fetchOrderItem();
    
    $data_submit_order['fetch_cart']        = $fetch_cart;
    $data_submit_order['fetch_order_item']  = $fetch_order_item;
    
    //Promotions
    $data_submit_order['freegift_auto']     = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $data_submit_order['voucher']           = (session('voucher')) ? session('voucher') : array() ;
    $data_submit_order['freegift']          = (session('freegift')) ? session('freegift') : array() ;
    $data_submit_order['benka_point']       = (session('benka_point')) ? session('benka_point') : NULL ;
    
    //Calculate Total
    $data_submit_order['total']             = Payment::calculateGrandTotal($fetch_order_item);
    
    $data_submit_order['auth_cs']           = (session()->has('auth_cs')) ? session('auth_cs') : 0;
    $data_submit_order['ip_address']        = (session()->has('email_auth_cs')) ? session('email_auth_cs') : getIp();
    
    $data_submit_order['is_not_idle']       = (session()->has('is_not_idle')) ? session('is_not_idle') : 0;
    
    Log::notice('Data Submit Order : ' . json_encode($data_submit_order));    
    return $data_submit_order;
  }

  public static function processSubmitOrder(array $data)
  {
    $time_start = microtime(true);
    date_default_timezone_set('Asia/Jakarta');
    
    $max_try          = 3; //Maximum number customer trying to do an order
    $reload_try_time  = 500000; //500ms, time wait before trying to do an order (in microsecond)
    $err_msg          = "Mohon maaf keranjang belanja Anda tidak bisa diproses. Silakan tunggu beberapa saat lalu coba kembali atau gunakan metode pembayaran lainnya."; //General error message
    
    for ($try = 1; $try <= $max_try; $try++) {
      Log::notice('########## processSubmitOrder: Started ########## | Try number '.$try.' ');
      
      try {
        DB::beginTransaction();
        
        //Clear Session
        session()->forget('updateInventorySKU');
        session()->forget('updateOrderItemStatusSKU');
        session()->forget('updateLastModifiedSKU');

        //Validate Order
        $validate_order = Self::validateOrder($data);
        if(!$validate_order['result']){
          $err_data['result']   = $validate_order['result'];
          $err_data['err_msg']  = $validate_order['err_msg'];
          DB::rollBack();
          return $err_data;
        }

        //Create Order Header
        $create_order_header = OrderHeader::createOrderHeader($data);
        if(!$create_order_header){
          $err_data['result']   = false;
          $err_data['err_msg']  = $err_msg;
          DB::rollBack();

          $time_executed  = microtime(true) - $time_start;
          Log::notice('########## processSubmitOrder: Failed ########## | Failed on createOrderHeader | Total Executed Time: '.$time_executed);
          return $err_data;
        }

        $data['order_header'] = $create_order_header;

        //Create Order Payment
        $create_order_payment = OrderPayment::createOrderPayment($data);
        if(!$create_order_payment){
          $err_data['result']   = false;
          $err_data['err_msg']  = $err_msg;
          DB::rollBack();

          $time_executed  = microtime(true) - $time_start;
          Log::notice('########## processSubmitOrder: Failed ########## | Failed on createOrderPayment | Total Executed Time: '.$time_executed);
          return $err_data;
        }

        //Create Priority History
        $create_priority_history = Self::createPriorityHistory($data);
        if(!$create_priority_history){
          $err_data['result']   = false;
          $err_data['err_msg']  = $err_msg;
          DB::rollBack();

          $time_executed  = microtime(true) - $time_start;
          Log::notice('########## processSubmitOrder: Failed ########## | Failed on createPriorityHistory | Total Executed Time: '.$time_executed);
          return $err_data;
        }

        $fetch_order_item = (isset($data['fetch_order_item'])) ? $data['fetch_order_item'] : NULL ;
        if(empty($fetch_order_item)){
          Log::notice('Process Order: Order Item is empty');
          $err_data['result']   = false;
          $err_data['err_msg']  = 'Keranjang belanja anda bermasalah. Harap refresh browser anda.';
          DB::rollBack();

          $time_executed  = microtime(true) - $time_start;
          Log::notice('########## processSubmitOrder: Failed ########## | Failed on Order Item is empty | Total Executed Time: '.$time_executed);
          return $err_data;
        }else{
          $data['count_order_item'] = count($fetch_order_item);
          foreach ($fetch_order_item as $order_item) {
            $data['fetch_order_item']           = $fetch_order_item;
            $data['order_item']                 = $order_item;

            //Update Order Item
            $update_order_item_status = OrderItem::updateOrderItemStatus($data);
            if(!$update_order_item_status){
              $err_data['result']   = false;
              $err_data['err_msg']  = $err_msg;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateOrderItemStatus | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            //Create Inventory Logs
            $create_inventory_logs = Self::createInventoryLogs($data);
            if(!$create_inventory_logs){
              $err_data['result']   = false;
              $err_data['err_msg']  = $err_msg;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on createInventoryLogs | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            //Update Inventory
            $update_inventory = Self::updateInventory($data);
            if(!$update_inventory['result']){
              $err_data['result']     = false;
              $err_data['err_msg']    = $err_msg;
              $err_data['false_qty']  = $update_inventory['false_qty'];
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateInventory | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            //Update Last Modified
            $update_last_modified = Self::updateLastModified($data);
            if(!$update_last_modified){
              $err_data['result']   = false;
              $err_data['err_msg']  = $err_msg;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateLastModified | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            //Update Product Status
            $update_product_status = Self::updateProductStatus($data);
            if(!$update_product_status){
              $err_data['result']   = false;
              $err_data['err_msg']  = $err_msg;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateProductStatus | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            //Create Order Item History
            $create_order_item_history = OrderItem::createOrderItemHistory($data);
            if(!$create_order_item_history){
              $err_data['result']   = false;
              $err_data['err_msg']  = $err_msg;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on createOrderItemHistory | Total Executed Time: '.$time_executed);
              return $err_data;
            }
          }
          
          //Update Promotions Usage
          $update_promotions_usage = Self::updatePromotionsUsage();
          if(!$update_promotions_usage['result']){
            $err_data['result']   = false;
            $err_data['err_msg']  = isset($update_promotions_usage['result_message']) && $update_promotions_usage['result_message'] != '' ? $update_promotions_usage['result_message'] : $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on updatePromotionsUsage | Total Executed Time: '.$time_executed);
            return $err_data;
          }
          
          //Insert Promotions Quota Log
          $insert_promotions_quota_log = Self::insertPromotionsQuotaLog($data);
          if(!$insert_promotions_quota_log){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on insertPromotionsQuotaLog | Total Executed Time: '.$time_executed);
            return $err_data;
          }
          
          //Update Promotions Quota
//          $update_promotions_quota = Self::updatePromotionsQuotaLog($data);
//          if(!$update_promotions_quota){
//            $err_data['result']   = false;
//            $err_data['err_msg']  = $err_msg;
//            DB::rollBack();
//
//            $time_executed  = microtime(true) - $time_start;
//            Log::notice('########## processSubmitOrder: Failed ########## | Failed on updatePromotionsQuotaLog | Total Executed Time: '.$time_executed);
//            return $err_data;
//          }

          //Create Order Discount
          $create_order_discount = OrderDiscount::createOrderDiscount($data);
          if(!$create_order_discount){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on createOrderDiscount | Total Executed Time: '.$time_executed);
            return $err_data;
          }

          //Update Order Item Value
          $update_order_item_value = OrderItem::updateOrderItemValue($data);
          if(!$update_order_item_value){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateOrderItemValue | Total Executed Time: '.$time_executed);
            return $err_data;
          }
          
          //Validate Purchase Price
          $validate_purchase_price = Self::validatePurchasePrice($data);
          if(!$validate_purchase_price){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on validatePurchasePrice | Total Executed Time: '.$time_executed);
            return $err_data;
          }
          
          //Validate Discount Value
          $data['order_discount_total_discount']  = $create_order_discount['total_discount'];
          $data['order_item_total_discount']      = $update_order_item_value['total_discount'];
          $validate_discount_value = Self::validateDiscountValue($data);
          if(!$validate_discount_value){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on validateDiscountValue | Total Executed Time: '.$time_executed);
            return $err_data;
          }

          //Update Customer Credit / Benka Point
          $update_customer_credit = Self::updateCustomerCredit($data);
          if(!$update_customer_credit){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateCustomerCredit | Total Executed Time: '.$time_executed);
            return $err_data;
          }
          
          //Update Referral
          $update_referral = Self::updateReferral($data);
          if(!$update_referral){
            $err_data['result']   = false;
            $err_data['err_msg']  = $err_msg;
            DB::rollBack();

            $time_executed  = microtime(true) - $time_start;
            Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateReferral | Total Executed Time: '.$time_executed);
            return $err_data;
          }

          //Veritrans
          $payment_method         = $data['payment_method'];
          $list_veritrans_method  = [5, 24, 20, 4, 3, 28, 98, 343];
          $kredivo_method           = 99;                       //Kredivo
          
          /*
           * 5: Credit Card 
           * 24: Indomaret 
           * 20: Mandiri Debit 
           * 4: klikPay 
           * 343: gopay 
           * 3: klikBCA 
           * 28: BCA Virtual Account
           * 98: Permata Virtual Account
          */
          
          /*
           * VERITRANS METHOD
           */
          if(in_array($payment_method, $list_veritrans_method)){ 
            $charge_veritrans = Veritrans::chargeVeritrans($data);

            if(!empty($charge_veritrans) && $charge_veritrans['code'] != 200 && $charge_veritrans['code'] != 201){ //Transaction Failed
              $veritrans_status_msg = isset($charge_veritrans['status_msg']) ? $charge_veritrans['status_msg'] : NULL;
              $payment_method       = $data['payment_method'];
              
              if($veritrans_status_msg == NULL){
                $veritrans_status_msg = "Payment gateway validation error";
                
                Log::alert('Process integrationVeritrans: Status Message is missing using payment method : ' . json_encode($payment_method));
              }
              
              $err_data['result']     = false;
              $err_data['err_msg']    = $err_msg . " [" . $veritrans_status_msg . "]";
              $err_data['veritrans']  = $charge_veritrans;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on chargeVeritrans | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            $update_order_payment = OrderPayment::updateOrderPaymentVeritrans($data, $charge_veritrans);
            if(!$update_order_payment){
              $err_data['result']           = false;
              $err_data['err_msg']          = $err_msg;
              $err_data['veritrans']        = $charge_veritrans;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateOrderPaymentVeritrans | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            $update_order_item_veritrans = OrderItem::updateOrderItemVeritrans($data, $charge_veritrans);
            if(!$update_order_item_veritrans){
              $err_data['result']           = false;
              $err_data['err_msg']          = $err_msg;
              $err_data['veritrans']        = $charge_veritrans;
              DB::rollBack();

              $time_executed  = microtime(true) - $time_start;
              Log::notice('########## processSubmitOrder: Failed ########## | Failed on updateOrderItemVeritrans | Total Executed Time: '.$time_executed);
              return $err_data;
            }

            $data['update_order_item_veritrans'] = $update_order_item_veritrans;

            foreach ($fetch_order_item as $order_item) {
              $data['order_item'] = $order_item;

              //Create Order Item History Veritrans
              $create_order_item_history_veritrans = OrderItem::createOrderItemHistoryVeritrans($data, $charge_veritrans);
              if(!$create_order_item_history_veritrans){
                $err_data['result']           = false;
                $err_data['err_msg']          = $err_msg;
                $err_data['veritrans']        = $charge_veritrans;
                DB::rollBack();

                $time_executed  = microtime(true) - $time_start;
                Log::notice('########## processSubmitOrder: Failed ########## | Failed on createOrderItemHistoryVeritrans | Total Executed Time: '.$time_executed);
                return $err_data;
              }
            }
          }elseif($payment_method == $kredivo_method){
            /*
            * KREDIVO METHOD
            */ 
              
            $chargeKredivo = Kredivo::charge($data);
            if(!$chargeKredivo){
                DB::rollBack();
                
                $time_executed  = microtime(true) - $time_start;
                Log::notice('########## processSubmitOrder: Failed ########## | Failed on chargeKredivo | Total Executed Time: '.$time_executed);
            }
          }
        }

        $order_data['result']               = true;
        $order_data['create_order_header']  = $create_order_header;
        $order_data['payment_method']       = $data['payment_method'];
        $order_data['veritrans']            = (isset($charge_veritrans)) ? $charge_veritrans : array() ;
        $order_data['kredivo']              = (isset($chargeKredivo)) ? $chargeKredivo : array() ;

        DB::commit();

        //Update SOLR
        //Self::updateSOLR($data); //Comment, masih ada bug sorting item

        $time_executed  = microtime(true) - $time_start;
        Log::notice('########## processSubmitOrder: Ended ########## | Total Executed Time: '.$time_executed);
        
        return $order_data;
      } catch(\Exception $e) {
        Log::error($e);
        
        $error_message = $e->getMessage();
        if (strpos($error_message, 'Deadlock found when trying to get lock; try restarting transaction') !== false && $try < 3) {
          Log::notice('########## processSubmitOrder: Deadlock Detected, Retrying transaction in '.($reload_try_time/1000).'ms ... ########## | ');
          DB::rollBack();
          usleep($reload_try_time); //Wait before retrying transaction
        }else{
          Log::notice('########## processSubmitOrder: Failed ########## | '.json_encode($error_message));
          $err_data['result']   = false;
          $err_data['err_msg']  = $err_msg;
          DB::rollBack();
          return $err_data;
        }
      }
    }
  }

  public static function validateOrder(array $data)
  {
    $return_data['result']  = true;
    $return_data['err_msg'] = "";
    
    $is_not_idle      = $data['is_not_idle'];
    $payment_method   = $data['payment_method'];
    $domain_id        = $data['domain_id'];
    $benka_point      = $data['benka_point'];
    $grand_total      = $data['total']['grand_total'];
    $fetch_cart       = isset($data['fetch_cart']) ? $data['fetch_cart'] : array() ;
    $fetch_order_item = isset($data['fetch_order_item']) ? $data['fetch_order_item'] : array() ;
    $freegift_auto    = isset($data['freegift_auto']) ? $data['freegift_auto'] : array();
    $voucher          = isset($data['voucher']) ? $data['voucher'] : array();
    $freegift         = isset($data['freegift']) ? $data['freegift'] : array();
    $master_payment   = isset($data['master_payment']) ? $data['master_payment'] : NULL;
    
    //Validate Customer ID
    if(!isset($data['customer_id']) || !isset($data['customer_email']) || !isset($data['customer_fname'])){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Mohon maaf keranjang belanja Anda tidak bisa diproses. Silakan tunggu beberapa saat lalu coba kembali atau gunakan metode pembayaran lainnya.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because Auth is empty');
      return $return_data;
    }
    //End Validate Customer ID
    
    //Validate Customer Shipping & Billing Address
    if(
      !isset($data['customer_address']['shipping']['address_street']) || $data['customer_address']['shipping']['address_street'] == '' ||
      !isset($data['customer_address']['shipping']['address_province']) || $data['customer_address']['shipping']['address_province'] == '' ||
      !isset($data['customer_address']['shipping']['address_city']) || $data['customer_address']['shipping']['address_city'] == '' ||
      !isset($data['customer_address']['shipping']['address_postcode']) || $data['customer_address']['shipping']['address_postcode'] == '' ||
      !isset($data['customer_address']['shipping']['address_phone']) || $data['customer_address']['shipping']['address_phone'] == ''
    ){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Mohon mengisi alamat pengiriman.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because Shipping Address is empty');
      return $return_data;
    }
    
    if(
      !isset($data['customer_address']['billing']['address_street']) || $data['customer_address']['billing']['address_street'] == '' ||
      !isset($data['customer_address']['billing']['address_province']) || $data['customer_address']['billing']['address_province'] == '' ||
      !isset($data['customer_address']['billing']['address_city']) || $data['customer_address']['billing']['address_city'] == '' ||
      !isset($data['customer_address']['billing']['address_postcode']) || $data['customer_address']['billing']['address_postcode'] == '' ||
      !isset($data['customer_address']['billing']['address_phone']) || $data['customer_address']['billing']['address_phone'] == ''
    ){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Mohon mengisi alamat penagihan.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because Billing Address is empty');
      return $return_data;
    }
    
    if(!isset($data['shipping_id']) || $data['shipping_id'] == NULL){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Data alamat anda salah. Klik <a href="/user/setting/">DISINI</a> untuk mengubah data anda.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because shipping ID is NULL');
      return $return_data;
    }
    //End Validate Customer Shipping & Billing Address
    
    //Validate Benka Point
    if($benka_point != NULL && !is_numeric($benka_point)){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Benka Point harus berupa angka';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because benka point is not numeric');
      return $return_data;
    }
    //End Validate Benka Point
    
    //Validate COD Availability
    if($payment_method == 19){
      $cod_available = DB::table('shipping')->where('shipping_id', '=',  $data['shipping_id'])->value('shipping_cod');
      if($cod_available == 0){
        $return_data['result']   = false;
        $return_data['err_msg']  = 'Alamat pengiriman anda tidak mendukung metode pembayaran COD.';

        Log::notice('########## processSubmitOrder: Failed ########## | Failed because shipping method is not supported');
        return $return_data;
      }
    }
    //End Validate COD Availability
    
    //Validate payment method
    if($payment_method == NULL){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Mohon memilih metode pembayaran.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed on payment method is NULL');
      return $return_data;
    }
    //End Validate payment method
    
    //Validate PopUp Store Availability
    $type_transfer = !is_null($master_payment) ? $master_payment->master_payment_type_transfer : NULL;
    if($type_transfer == 4){
      $pop_up_store_available = 
        DB::table('location_store')
          ->where('shipping_id', '=', $data['shipping_id'])
          ->where('status_deleted', '=', 0);
      
      if($domain_id == 1){
        $pop_up_store_available->where('enabled_bb', '=', 1);
      }elseif($domain_id == 2){
        $pop_up_store_available->where('enabled_hb', '=', 1);
      }else{
        $pop_up_store_available->where('enabled_sd', '=', 1);
      }
      
      if($pop_up_store_available->get()){
        // Do Nothing
      }else{
        $return_data['result']   = false;
        $return_data['err_msg']  = 'Alamat pengiriman anda tidak mendukung metode pembayaran Bayar di Toko.';

        Log::notice('########## processSubmitOrder: Failed ########## | Failed because shipping method is not supported');
        return $return_data;
      }
    }
    //End Validate PopUp Store Availability
    
    //Validate Minimum / Maximum Grand Total Payment Method
    $maximum_grand_total = !is_null($master_payment) ? $master_payment->maximum_grand_total : 0 ;
    $minimum_grand_total = !is_null($master_payment) ? $master_payment->minimum_grand_total : 0 ;
    
    if($maximum_grand_total > 0 && $grand_total > $maximum_grand_total){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Maximum grand total untuk metode pembayaran yang anda pilih adalah ' . number_format($maximum_grand_total);
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because grand total is above payment method maximum grand total');
      return $return_data;
    }
    
    if($minimum_grand_total > 0 && $grand_total < $minimum_grand_total){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Minimum grand total untuk metode pembayaran yang anda pilih adalah ' . number_format($minimum_grand_total);
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because grand total is below payment method minimum grand total');
      return $return_data;
    }
    //End Validate Minimum / Maximum Grand Total Payment Method
    
    //Validate Session Cart & Order Item
    if(empty($fetch_cart) || empty($fetch_order_item)){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Keranjang belanja anda bermasalah. Mohon ulangi proses pembelian anda.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed because cart and order item is empty');
      return $return_data;
    }else{
      $cart_SKU_list        = [];
      $order_item_SKU_list  = [];

      foreach($fetch_cart as $cart){
        $qty = $cart['qty'];
        for($i = 0; $i < $qty; $i++){
          $cart_SKU_list[] = $cart['SKU'];
        }
      }

      foreach($fetch_order_item as $order_item){
        $order_item_SKU_list[] = $order_item->SKU;
      }
      
      $check_diff = array_diff($cart_SKU_list, $order_item_SKU_list);
      if(!empty($check_diff)){
        $return_data['result']   = false;
        $return_data['err_msg']  = 'Keranjang belanja anda bermasalah. Mohon ulangi proses pembelian anda.';

        Log::notice('########## processSubmitOrder: Failed ########## | Failed because cart and order item content is not same');
        return $return_data;
      }
    }
    //End Validate Session Cart & Order Item
    
    //Validate wether customer is idle or not
    if($is_not_idle == NULL || $is_not_idle == 0){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Session anda telah habis. Mohon ulangi proses pembelian anda.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed on state order session is missing');
      return $return_data;
    }
    //End Validate wether customer is idle or not
    
    //Validate KlikBCA
    if($payment_method == 3 && $data['klikbca_user_id'] == NULL){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Mohon mengisi ID KlikBCA.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Failed on KlikBCA User ID is NULL');
      return $return_data;
    }
    //End Validate KlikBCA
    
    //Validate Veritrans
    if($payment_method == 5 && $data['token_id'] == NULL){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Token ID Missing.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | Token ID is missing using Veritrans');
      return $return_data;
    }

    if($payment_method == 5 && $data['cc_holder'] == NULL){
      $return_data['result']   = false;
      $return_data['err_msg']  = 'Nama Kartu Kredit belum diisi.';
      
      Log::notice('########## processSubmitOrder: Failed ########## | CC Holder is NULL');
      return $return_data;
    }
    //End Validate Veritrans
    
    return $return_data;
  }
  
  public static function createPriorityHistory(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process PriorityHistory: Started');
    
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $priority       = (isset($data['order_header']['priority'])) ? $data['order_header']['priority'] : NULL ;
    $priority_date  = (isset($data['order_header']['priority_date'])) ? $data['order_header']['priority_date'] : NULL ;
    
    if($priority == 0){
      Log::notice('Process PriorityHistory: Success. Order is not same day or next day shipping.');
      return true;
    }
    
    if($customer_id == NULL){
      Log::notice('Process PriorityHistory: Customer ID is missing.');
      return false;
    }
    
    if($purchase_code == NULL){
      Log::notice('Process PriorityHistory: Purchase Code is missing.');
      return false;
    }
      
    $create_priority_history['purchase_code']   = $purchase_code;
    $create_priority_history['priority_status'] = $priority;
    $create_priority_history['priority_date']   = $priority_date;
    $create_priority_history['created_by']      = $customer_id;
    $create_priority_history['created_date']    = date('Y-m-d H:i:s');

    $priority_history_id = DB::table('priority_history')->insertGetId($create_priority_history);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$priority_history_id){
      Log::notice('Process PriorityHistory: Insert to priority_history failed. Executed Time: '.$time_executed);
      return false;
    }else{
      Log::notice('Process PriorityHistory: Success. Executed Time: '.$time_executed);
      $create_priority_history['priority_history_id'] = $priority_history_id;
      return $create_priority_history;
    }
  }
  
  public static function createInventoryLogs(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process createInventoryLogs: Started');
    
    $customer_fname = (isset($data['customer_fname'])) ? $data['customer_fname'] : NULL ;
    $SKU            = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $order_item_id  = (isset($data['order_item']->order_item_id)) ? $data['order_item']->order_item_id : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    
    if($SKU == NULL || $order_item_id == NULL){
      Log::notice('Process createInventoryLogs: SKU / Order Item ID is NULL');
      return false;
    }
    
    if($purchase_code == NULL){
      Log::notice('Process createInventoryLogs: Purchase Code is NULL');
      return false;
    }
    
    $inventory_id = DB::table('inventory')->where('SKU', $SKU)->value('inventory_id');
    
    if(!$inventory_id){
      Log::notice('Process createInventoryLogs: Inventory ID is empty');
      return false;
    }
    
    $create_inventory_log['inventory_id']         = $inventory_id;
    $create_inventory_log['user_name']            = $customer_fname;
    $create_inventory_log['order_item_id']        = $order_item_id;
    $create_inventory_log['SKU']                  = $SKU;
    $create_inventory_log['quantity']             = 1;
    $create_inventory_log['history_type']         = 2;
    $create_inventory_log['history_date']         = date('Y-m-d H:i:s');
    $create_inventory_log['history_category']     = 1;
    $create_inventory_log['history_description']  = 'Order from #' . $purchase_code . '';
    
    $inventory_logs_id = DB::table('inventory_logs')->insertGetId($create_inventory_log);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$inventory_logs_id){
      Log::notice('Process createInventoryLogs: Insert to inventory_logs failed. Executed Time: '.$time_executed);
      return false;
    }else{
      Log::notice('Process createInventoryLogs: Success. Executed Time: '.$time_executed);
      $create_inventory_log['inventory_logs_id'] = $inventory_logs_id;
      return $create_inventory_log;
    }
  }
  
  public static function updateInventory(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateInventory: Started');
    
    $domain_id    = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_id  = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $SKU          = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $fetch_cart   = (isset($data['fetch_cart'])) ? $data['fetch_cart'] : array() ;
    $updatedSKU   = session('updateInventorySKU') ? session('updateInventorySKU') : array() ;
    
    $return = array();
    $return['result']     = false;
    $return['false_qty']  = false;
    
    if($SKU == NULL){
      Log::notice('Process updateInventory: SKU is NULL');
      return $return;
    }
    
    if(empty($fetch_cart)){
      Log::notice('Process updateInventory: Cart is empty');
      return $return;
    }
    
    if(in_array($SKU, $updatedSKU)){
      $return['result'] = true;
      
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateInventory: Success [SKU: '.$SKU.'] is already updated. Executed Time: '.$time_executed);
      return $return;
    }
    
    //Manage Quantity
    $get_row_id = Cart::search(array('id' => $SKU));
		$cart       = Cart::get($get_row_id[0]);
    if($cart){
			$cart_qty = $cart->qty;
    }else{
      Log::notice('Process updateInventory: Item is not found on cart');
      return $return;
    }
    
    $quantity           = DB::table('inventory')->where('SKU', '=',  $SKU)->value('quantity');
    $updated_inventory  = $quantity - $cart_qty;
    //End Manage Quantity
    
    if($updated_inventory < 0){
      Log::notice('Process updateInventory: Cart Qty is larger than Inventory. | SKU: '.$SKU.' | Quantity: '.$quantity.' ');

      $return['false_qty'] = true;
      return $return;
    }else if($updated_inventory == 0){
      $update_item['inventory_status'] = 2; //Status Out of Stock
    }

    $update_item['quantity'] = $updated_inventory;
    $update_inventory = DB::table('inventory')
      ->where('SKU', str_replace('or', '/', $SKU))
    ->update($update_item);
    
    if(!$update_inventory){
      Log::notice('Process updateInventory: Update failed.');
      return $return;
    }
    
    //Session to prevent duplicate update
    session()->push('updateInventorySKU', $SKU);

    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updateInventory: Success [SKU: '.$SKU.'] [Quantity: '.$quantity.'] [Count: '.$cart_qty.']. Executed Time: '.$time_executed);

    $return['result'] = true;
    return $return;
  }
  
  public static function updateLastModified(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateLastModified: Started');
    
    $SKU          = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    $updatedSKU   = session('updateLastModifiedSKU') ? session('updateLastModifiedSKU') : array() ;
    
    if($SKU == NULL){
      Log::notice('Process updateLastModified: SKU is NULL');
      return false;
    }
    
    if(in_array($SKU, $updatedSKU)){
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateLastModifiedSKU: Success [SKU: '.$SKU.'] is already updated. Executed Time: '.$time_executed);
      return true;
    }
    
    $product_id = DB::table('inventory')->where('SKU', $SKU)->value('product_id');
    if(!$product_id){
      Log::notice('Process updateLastModified: Product ID is empty');
      return false;
    }
    
    $update_last_modified = Product::where('product_id', '=', $product_id)->first();
    $update_last_modified->last_modified = date('Y-m-d H:i:s');
    $update_last_modified->save();
    
    if(!$update_last_modified){
      Log::notice('Process updateLastModified: Update is failed.');
      return false;
    }
    
    //Session to prevent duplicate update
    session()->push('updateLastModifiedSKU', $SKU);

    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updateLastModified: Success. Executed Time: '.$time_executed);
    return true;
  }
  
  public static function updateProductStatus(array $data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateProductStatus: Started');
    
    $SKU = (isset($data['order_item']->SKU)) ? $data['order_item']->SKU : NULL ;
    
    if($SKU == NULL){
      Log::notice('Process updateProductStatus: SKU is NULL');
      return false;
    }
    
    $product_id = DB::table('inventory')->where('SKU', $SKU)->value('product_id');
    if(!$product_id){
      Log::notice('Process updateProductStatus: Product ID is empty');
      return false;
    }
    
    $total_inventory = DB::table('inventory')
      ->where('product_id', $product_id)
      ->sum('quantity');

    if($total_inventory <= 0){
      $update_product_status = Product::where('product_id', '=', $product_id)->first();
      $update_product_status->product_status = 2;
      $update_product_status->save();

      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateProductStatus: Success. Product ID: '.$product_id.' is now out of stock. Executed Time: '.$time_executed);
      return true;
    }else{
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateProductStatus: Success. Product ID: '.$product_id.' stock is not empty. Executed Time: '.$time_executed);
      return true;
    }
  }
  
  public static function validatePurchasePrice($data)
  {
    $time_start = microtime(true);
    Log::notice('Process validatePurchasePrice: Started');
    
    $order_header                 = isset($data['order_header']) ? $data['order_header'] : [] ;
    $order_header_purchase_price  = isset($order_header['purchase_price']) ? $order_header['purchase_price'] : 0;
    $fetch_order_item             = isset($data['fetch_order_item']) ? $data['fetch_order_item'] : [] ;
    $total_price_order_item       = 0;
    
    if(empty($fetch_order_item)){
      Log::notice('Process validatePurchasePrice: Order Item is empty');
      return false;
    }
    
    foreach($fetch_order_item as $order_item){
      $order_price            = set_price($order_item->total_price, $order_item->total_discount_price);
      $total_price_order_item += $order_price;
    }
    
    if($order_header_purchase_price != $total_price_order_item){
      Log::emergency('Process validatePurchasePrice: Failed. Order Header Purchase Price ['.$order_header_purchase_price.'] | Total Price Order Item ['.$total_price_order_item.']' );
      Log::emergency('Process validatePurchasePrice: Failed. Order Header Detail : ' . json_encode($order_header) );
      Log::emergency('Process validatePurchasePrice: Failed. Order Item Detail : ' . json_encode($fetch_order_item) );

      return false;
    }
    
    Log::notice('Process validatePurchasePrice: Success. Order Header Purchase Price ['.$order_header_purchase_price.'] | Total Price Order Item ['.$total_price_order_item.']');
    return true;
  }
  
  public static function validateDiscountValue($data)
  {
    $time_start = microtime(true);
    Log::notice('Process validateDiscountValue: Started');
    
    $order_header_discount          = isset($data['order_header']['discount']) ? $data['order_header']['discount'] : 0;
    $order_discount_total_discount  = isset($data['order_discount_total_discount']) ? round($data['order_discount_total_discount']) : 0;
    $order_item_total_discount      = isset($data['order_item_total_discount']) ? round($data['order_item_total_discount']) : 0;
    
    if($order_header_discount != $order_discount_total_discount || $order_header_discount != $order_item_total_discount){
      if(abs($order_header_discount - $order_discount_total_discount) > 1 || abs($order_header_discount - $order_item_total_discount) > 1){
        Log::emergency('Process validateDiscountValue: Failed. Order Header Discount ['.$order_header_discount.'] | Order Discount ['.$order_discount_total_discount.'] | Order Item ['.$order_item_total_discount.']' );
        Log::emergency('Data: '. json_encode($data));
        Log::emergency('Session Freegift Auto: '. json_encode(session('freegift_auto')));
        Log::emergency('Session Voucher: '. json_encode(session('voucher')));
        Log::emergency('Session Freegift: '. json_encode(session('freegift')));

        return false;
      }else{
        Log::notice('Process validateDiscountValue: Success. Order Header Discount ['.$order_header_discount.'] | Order Discount ['.$order_discount_total_discount.'] | Order Item ['.$order_item_total_discount.']');
        return true;
      }
    }else{
      Log::notice('Process validateDiscountValue: Success. Order Header Discount ['.$order_header_discount.'] | Order Discount ['.$order_discount_total_discount.'] | Order Item ['.$order_item_total_discount.']');
      return true;
    }
  }
  
  public static function updatePromotionsUsage()
  {
    $time_start = microtime(true);
    Log::notice('Process updatePromotionsUsage: Started');
    
    $return['result']         = true;
    $return['result_message'] = '';
    
    $freegift_auto  = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $voucher        = (session('voucher')) ? session('voucher') : array() ;
    $freegift       = (session('freegift')) ? session('freegift') : array() ;
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      Log::notice('Process updatePromotionsUsage: Success. No freegift auto / voucher / freegift found.');
      return $return;
    }
    
    if(!empty($freegift_auto)){
      foreach($freegift_auto as $key => $values){
        //Check Maximum Usage
        $attributes     = [];
        $promotions_id  = $freegift_auto[$key]['promotions_id'];
        
        $fetch_promotions_template  = PromotionTemplate::where('promotions_template_id', $promotions_id)->first();
        $fetch_promotions_condition = 
          PromotionCondition::where('promotions_template_id', $promotions_id)
          ->where('promotions_type_condition', 21)
          ->first();
        
        if(!is_null($fetch_promotions_condition)){
          $attributes['promotions_template_id']       = $promotions_id;
          $attributes['promotions_template_name']     = $fetch_promotions_template->promotions_template_name;
          $attributes['promotions_type_equal_value']  = $fetch_promotions_condition->promotions_type_equal_value;
          $attributes['promotions_type_equal_type']   = $fetch_promotions_condition->promotions_type_equal_type;
          
          $check_maximum_usage  = PromotionCondition::maximumUsage($attributes, []);
          if(!$check_maximum_usage){
            $return['result']         = false;
            $return['result_message'] = 'Maaf, promo ' . $freegift_auto[$key]['promotions_name'] . ' sudah melebihi kuota.';
            
            Log::notice('########## processSubmitOrder: Failed ########## | Failed because promotions is exceeding maximum usage');
            return $return;
          }
        }
        //End Check Maximum Usage
        
        $freegift_auto_usage = DB::table('promotions_template')->where('promotions_template_id', $freegift_auto[$key]['promotions_id'])->value('promotions_usage');
        
        $update_item = [];
        $update_item['promotions_usage']  = $freegift_auto_usage + 1;
        
        $update_freegift_auto_usage = DB::table('promotions_template')
          ->where('promotions_template_id', $freegift_auto[$key]['promotions_id'])
        ->update($update_item);
        
        if(!$update_freegift_auto_usage){
          $return['result'] = false;
          
          Log::notice('Process updatePromotionsUsage: [Freegift Auto] Failed to update.');
          return $return;
        }
        
        //Remind Max Usage
        $param_reminder = [];
        $param_reminder['promotions_name']          = isset($attributes['promotions_template_name']) ? $attributes['promotions_template_name'] : NULL ;
        $param_reminder['promotions_max_usage']     = isset($attributes['promotions_type_equal_value']) ? $attributes['promotions_type_equal_value'] : NULL ;
        $param_reminder['promotions_current_usage'] = isset($freegift_auto_usage) ? $freegift_auto_usage + 1 : 0 ;
        Self::sendReminderMaxUsageMail($param_reminder);
        //End Remind Max Usage
      }
    }
    
    if(!empty($voucher)){
      //Check Maximum Usage
      $attributes     = [];
      $promotions_id  = $voucher['promotions_id'];
      $voucher_code   = $voucher['promotions_code'];
      
      $fetch_promotions_template  = PromotionTemplate::where('promotions_template_id', $promotions_id)->first();
      $fetch_promotions_condition = 
        PromotionCondition::where('promotions_template_id', $promotions_id)
        ->where('promotions_type_condition', 21)
        ->first();

      if(!is_null($fetch_promotions_condition)){
        $attributes['promotions_template_id']       = $promotions_id;
        $attributes['promotions_template_name']     = $fetch_promotions_template->promotions_template_name;
        $attributes['promotions_type_equal_value']  = $fetch_promotions_condition->promotions_type_equal_value;
        $attributes['promotions_type_equal_type']   = $fetch_promotions_condition->promotions_type_equal_type;
        $attributes['voucher_code']                 = $voucher_code;
        
        $check_maximum_usage  = PromotionCondition::maximumUsage($attributes, []);
        if(!$check_maximum_usage){
          $return['result']         = false;
          $return['result_message'] = 'Maaf, promo ' . $voucher['promotions_name'] . ' sudah melebihi kuota.';

          Log::notice('########## processSubmitOrder: Failed ########## | Failed because promotions is exceeding maximum usage');
          return $return;
        }
      }
      //End Check Maximum Usage
      
      $voucher_code_usage = DB::table('promotions_code')->where('promotions_code_number', $voucher['promotions_code'])->value('promotions_code_usage');
      
      $update_item  = [];
      $update_item['promotions_code_usage'] = $voucher_code_usage + 1;
      $update_voucher_usage                 = DB::table('promotions_code')
        ->where('promotions_code_number', $voucher['promotions_code'])
      ->update($update_item);
      
      if(!$update_voucher_usage){
        $return['result'] = false;
        
        Log::notice('Process updatePromotionsUsage: [Voucher] Failed to update.');
        return $return;
      }
      
      //Update Template Usage
      $voucher_template_usage = DB::table('promotions_template')->where('promotions_template_id', $voucher['promotions_id'])->value('promotions_usage');
      
      $update_item  = [];
      $update_item['promotions_usage']  = $voucher_template_usage + 1;
      $update_voucher_template_usage    = DB::table('promotions_template')
        ->where('promotions_template_id', $voucher['promotions_id'])
      ->update($update_item);
      
      if(!$update_voucher_template_usage){
        $return['result'] = false;
        
        Log::notice('Process updatePromotionsUsage: [Voucher] Failed to update.');
        return $return;
      }
      
      //Remind Max Usage
      $param_reminder = [];
      $param_reminder['promotions_name']          = isset($attributes['promotions_template_name']) ? $attributes['promotions_template_name'] : NULL ;
      $param_reminder['promotions_code']          = isset($voucher['promotions_code']) ? $voucher['promotions_code'] : NULL ;
      $param_reminder['promotions_max_usage']     = isset($attributes['promotions_type_equal_value']) ? $attributes['promotions_type_equal_value'] : NULL ;
      $param_reminder['promotions_current_usage'] = isset($voucher_code_usage) ? $voucher_code_usage + 1 : 0 ;
      Self::sendReminderMaxUsageMail($param_reminder);
      //End Remind Max Usage
    }
    
    if(!empty($freegift)){
      foreach($freegift as $key => $values){
        //Check Maximum Usage
        $attributes     = [];
        $promotions_id  = $freegift[$key]['promotions_id'];
        
        $fetch_promotions_template  = PromotionTemplate::where('promotions_template_id', $promotions_id)->first();
        $fetch_promotions_condition = 
          PromotionCondition::where('promotions_template_id', $promotions_id)
          ->where('promotions_type_condition', 21)
          ->first();
        
        if(!is_null($fetch_promotions_condition)){
          $attributes['promotions_template_id']       = $promotions_id;
          $attributes['promotions_template_name']     = $fetch_promotions_template->promotions_template_name;
          $attributes['promotions_type_equal_value']  = $fetch_promotions_condition->promotions_type_equal_value;
          $attributes['promotions_type_equal_type']   = $fetch_promotions_condition->promotions_type_equal_type;
          
          $check_maximum_usage  = PromotionCondition::maximumUsage($attributes, []);
          if(!$check_maximum_usage){
            $return['result']         = false;
            $return['result_message'] = 'Maaf, promo ' . $freegift[$key]['promotions_name'] . ' sudah melebihi kuota.';
            
            Log::notice('########## processSubmitOrder: Failed ########## | Failed because promotions is exceeding maximum usage');
            return $return;
          }
        }
        //End Check Maximum Usage
        
        $freegift_usage = DB::table('promotions_template')->where('promotions_template_id', $freegift[$key]['promotions_id'])->value('promotions_usage');
        
        $update_item = [];
        $update_item['promotions_usage']  = $freegift_usage + 1;
        
        $update_freegift_usage = DB::table('promotions_template')
          ->where('promotions_template_id', $freegift[$key]['promotions_id'])
        ->update($update_item);
        
        if(!$update_freegift_usage){
          $return['result'] = false;
          
          Log::notice('Process updatePromotionsUsage: [Freegift] Failed to update.');
          return $return;
        }
        
        //Remind Max Usage
        $param_reminder = [];
        $param_reminder['promotions_name']          = isset($attributes['promotions_template_name']) ? $attributes['promotions_template_name'] : NULL ;
        $param_reminder['promotions_max_usage']     = isset($attributes['promotions_type_equal_value']) ? $attributes['promotions_type_equal_value'] : NULL ;
        $param_reminder['promotions_current_usage'] = isset($freegift_usage) ? $freegift_usage + 1 : 0 ;
        Self::sendReminderMaxUsageMail($param_reminder);
        //End Remind Max Usage
      }
    }
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updatePromotionsUsage: Success. Executed Time: '. $time_executed);
    return $return;
  }
  
  public static function updateCustomerCredit($data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateCustomerCredit: Started');
    
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $benka_point    = (isset($data['benka_point'])) ? $data['benka_point'] : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    $grand_total    = $data['total']['grand_total'];
    
    if($benka_point == NULL){
      Log::notice('Process updateCustomerCredit: Success. Customer not using benka point.');
      return true;
    }
    
    $customer = Customer::where('customer_id', '=', $customer_id)->first();
    if(!$customer){
      Log::notice('Process updateCustomerCredit: Customer ID tidak ditemukan.');
      return false;
    }
    
    $customer_credit = $customer->customer_credit;
    $remaining_usage = $customer_credit - $benka_point;
    
    $update_item                    = array();
    $update_item['customer_credit'] = $remaining_usage;
    $update_customer_credit         = DB::table('customer')
      ->where('customer_id', $customer_id)
    ->update($update_item);
    
    if(!$update_customer_credit){
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateCustomerCredit: Update customer is failed. Executed Time: '. $time_executed);
      return false;
    }
    
    $credit_history['customer_id']               = $customer_id;
    $credit_history['credithistory_type']        = 'DB';
    $credit_history['credithistory_date']        = date('Y-m-d H:i:s');
    $credit_history['credithistory_transaction'] = $grand_total;
    $credit_history['credithistory_amount']      = $benka_point;
    $credit_history['credithistory_desc']        = '#' . $purchase_code . ' - Penggunaan Credit - IDR. ' . number_format($benka_point, 0, ".", ".") . '';
    $credit_history['credithistory_balance']     = $remaining_usage;
    
    $create_credit_history = DB::table('customer_credit_history')->insertGetId($credit_history);
    
    $time_executed  = microtime(true) - $time_start;
    if(!$create_credit_history){
      Log::notice('Process updateCustomerCredit: Create customer_credit_history is failed. Executed Time: '. $time_executed);
      return false;
    }
    
    Log::notice('Process updateCustomerCredit: Success. Executed Time: '. $time_executed);
    return true;
  }
  
  public static function updateReferral($data)
  {
    $time_start = microtime(true);
    Log::notice('Process updateReferral: Started');
    
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $purchase_code  = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    
    $customer = Customer::where('customer_id', '=', $customer_id)->first();
    if(!$customer){
      Log::notice('Process updateReferral: Customer ID tidak ditemukan.');
      return false;
    }
    
    $get_referral = 
      DB::table('referral_program')
      ->where('invited_customer_id', '=',  $customer_id)
      ->where('status', '=',  0)
      ->whereRaw('(purchase_code IS NULL OR purchase_code = "")')
      ->get();
    
    if($get_referral){
      $update_item                    = array();
      $update_item['purchase_code']   = $purchase_code;
      $update_item['status']          = 1;
      $update_referral_program        = DB::table('referral_program')
        ->where('invited_customer_id', $customer_id)
      ->update($update_item);
      
      if(!$update_referral_program){
        $time_executed  = microtime(true) - $time_start;
        Log::notice('Process updateReferral: Update referral program is failed. Executed Time: '. $time_executed);
        return false;
      }
      
    }else{
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updateReferral: Success. Customer is not on referral program. Executed Time: '. $time_executed);
      return true;
    }
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updateReferral: Success. Executed Time: '. $time_executed);
    return true;
  }
  
  public static function insertPromotionsQuotaLog($data)
  {
    $time_start = microtime(true);
    Log::notice('Process insertPromotionsQuotaLog: Started');
    
    $customer_id      = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $customer_email   = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $freegift_auto    = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $voucher          = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $freegift         = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    $purchase_code    = (isset($data['order_header']['purchase_code'])) ? $data['order_header']['purchase_code'] : NULL ;
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process insertPromotionsQuotaLog: Success. No freegift auto / voucher / freegift found. Executed Time: '. $time_executed);
      return true;
    }
    
    if(!empty($freegift_auto)){
      foreach($freegift_auto as $key => $values){
        $promotions_id            = $freegift_auto[$key]['promotions_id'];
        $promotions_maximum_quota = $freegift_auto[$key]['promotions_maximum_quota'];
        $promotions_value         = $freegift_auto[$key]['promotions_value'];
        
        if($promotions_maximum_quota > 0){
          $create_promotions_quota_log = [];
          $create_promotions_quota_log['promotions_template_id']  = $promotions_id;
          $create_promotions_quota_log['purchase_code']           = $purchase_code;
          $create_promotions_quota_log['customer_id']             = $customer_id;
          $create_promotions_quota_log['customer_email']          = $customer_email;
          $create_promotions_quota_log['promotions_used']         = $promotions_value;
          $create_promotions_quota_log['created_date']            = date('Y-m-d H:i:s');

          $insert_promotions_quota_log = DB::table('promotions_quota_log')->insertGetId($create_promotions_quota_log);
          
          if(!$insert_promotions_quota_log){
            $time_executed  = microtime(true) - $time_start;
            Log::notice('Process insertPromotionsQuotaLog: Failed. Insert to DB failed. Executed Time: '. $time_executed);
            return false;
          }
        }
      }
    }
    
    if(!empty($voucher)){
      $promotions_id            = $voucher['promotions_id'];
      $promotions_maximum_quota = $voucher['promotions_maximum_quota'];
      $promotions_value         = $voucher['promotions_value'];

      if($promotions_maximum_quota > 0){
        $create_promotions_quota_log = [];
        $create_promotions_quota_log['promotions_template_id']  = $promotions_id;
        $create_promotions_quota_log['purchase_code']           = $purchase_code;
        $create_promotions_quota_log['customer_id']             = $customer_id;
        $create_promotions_quota_log['customer_email']          = $customer_email;
        $create_promotions_quota_log['promotions_used']         = $promotions_value;
        $create_promotions_quota_log['created_date']            = date('Y-m-d H:i:s');

        $insert_promotions_quota_log = DB::table('promotions_quota_log')->insertGetId($create_promotions_quota_log);

        if(!$insert_promotions_quota_log){
          $time_executed  = microtime(true) - $time_start;
          Log::notice('Process insertPromotionsQuotaLog: Failed. Insert to DB failed. Executed Time: '. $time_executed);
          return false;
        }
      }
    }
    
    if(!empty($freegift)){
      foreach($freegift as $key => $values){
        $promotions_id            = $freegift[$key]['promotions_id'];
        $promotions_maximum_quota = $freegift[$key]['promotions_maximum_quota'];
        $promotions_value         = $freegift[$key]['promotions_value'];
        
        if($promotions_maximum_quota > 0){
          $create_promotions_quota_log = [];
          $create_promotions_quota_log['promotions_template_id']  = $promotions_id;
          $create_promotions_quota_log['purchase_code']           = $purchase_code;
          $create_promotions_quota_log['customer_id']             = $customer_id;
          $create_promotions_quota_log['customer_email']          = $customer_email;
          $create_promotions_quota_log['promotions_used']         = $promotions_value;
          $create_promotions_quota_log['created_date']            = date('Y-m-d H:i:s');

          $insert_promotions_quota_log = DB::table('promotions_quota_log')->insertGetId($create_promotions_quota_log);
          
          if(!$insert_promotions_quota_log){
            $time_executed  = microtime(true) - $time_start;
            Log::notice('Process insertPromotionsQuotaLog: Failed. Insert to DB failed. Executed Time: '. $time_executed);
            return false;
          }
        }
      }
    }
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process insertPromotionsQuotaLog: Success. Executed Time: '. $time_executed);
    return true;
  }
  
  public static function updatePromotionsQuotaLog($data)
  {
    $time_start = microtime(true);
    Log::notice('Process updatePromotionsQuotaLog: Started');
    
    $freegift_auto  = (isset($data['freegift_auto'])) ? $data['freegift_auto'] : array() ;
    $voucher        = (isset($data['voucher'])) ? $data['voucher'] : array() ;
    $freegift       = (isset($data['freegift'])) ? $data['freegift'] : array() ;
    
    if(empty($freegift_auto) && empty($voucher) && empty($freegift)){
      $time_executed  = microtime(true) - $time_start;
      Log::notice('Process updatePromotionsQuotaLog: Success. No freegift auto / voucher / freegift found. Executed Time: '. $time_executed);
      return true;
    }
    
    if(!empty($freegift_auto)){
      foreach($freegift_auto as $key => $values){
        $promotions_id            = $freegift_auto[$key]['promotions_id'];
        $promotions_maximum_quota = $freegift_auto[$key]['promotions_maximum_quota'];
        $promotions_value         = $freegift_auto[$key]['promotions_value'];
        
        if($promotions_maximum_quota > 0){
          $promotions_quota_usage = DB::table('promotions_template')->where('promotions_template_id', $promotions_id)->value('promotions_quota_usage');
          
          $update_item = [];
          $update_item['promotions_quota_usage']  = $promotions_quota_usage + $promotions_value;

          $update_promotions_quota = DB::table('promotions_template')
            ->where('promotions_template_id', $promotions_id)
          ->update($update_item);
          
          if(!$update_promotions_quota){
            $time_executed  = microtime(true) - $time_start;
            Log::notice('Process updatePromotionsQuotaLog: Failed. Update to DB failed. Executed Time: '. $time_executed);
            return false;
          }
        }
      }
    }
    
    if(!empty($voucher)){
      $promotions_id            = $voucher['promotions_id'];
      $promotions_maximum_quota = $voucher['promotions_maximum_quota'];
      $promotions_value         = $voucher['promotions_value'];

      if($promotions_maximum_quota > 0){
        $promotions_quota_usage = DB::table('promotions_template')->where('promotions_template_id', $promotions_id)->value('promotions_quota_usage');

        $update_item = [];
        $update_item['promotions_quota_usage']  = $promotions_quota_usage + $promotions_value;

        $update_promotions_quota = DB::table('promotions_template')
          ->where('promotions_template_id', $promotions_id)
        ->update($update_item);

        if(!$update_promotions_quota){
          $time_executed  = microtime(true) - $time_start;
          Log::notice('Process updatePromotionsQuotaLog: Failed. Update to DB failed. Executed Time: '. $time_executed);
          return false;
        }
      }
    }
    
    if(!empty($freegift)){
      foreach($freegift as $key => $values){
        $promotions_id            = $freegift[$key]['promotions_id'];
        $promotions_maximum_quota = $freegift[$key]['promotions_maximum_quota'];
        $promotions_value         = $freegift[$key]['promotions_value'];
        
        if($promotions_maximum_quota > 0){
          $promotions_quota_usage = DB::table('promotions_template')->where('promotions_template_id', $promotions_id)->value('promotions_quota_usage');
          
          $update_item = [];
          $update_item['promotions_quota_usage']  = $promotions_quota_usage + $promotions_value;

          $update_promotions_quota = DB::table('promotions_template')
            ->where('promotions_template_id', $promotions_id)
          ->update($update_item);
          
          if(!$update_promotions_quota){
            $time_executed  = microtime(true) - $time_start;
            Log::notice('Process updatePromotionsQuotaLog: Failed. Update to DB failed. Executed Time: '. $time_executed);
            return false;
          }
        }
      }
    }
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process updatePromotionsQuotaLog: Success. Executed Time: '. $time_executed);
    return true;
  }
  
  public static function updateSOLR($data)
  {
    $fetch_cart = (isset($data['fetch_cart'])) ? $data['fetch_cart'] : array() ;
    
    foreach($fetch_cart as $key => $value){
      $solr_item['quantity']  = $fetch_cart[$key]['qty'];
      $solr_item['sku']       = $fetch_cart[$key]['SKU'];
      $solr_item['pid']       = $fetch_cart[$key]['product_id'];
      $update_solr = SolrSync::updateSolr($solr_item);
    }
    
    return true;
  }
    
  public static function createVeritransNotifications(array $data)
  {
    $insert_veritrans_notifications = DB::table('veritrans_notifications')->insertGetId($data);
        
    return $insert_veritrans_notifications;
  }
  
  public static function createVeritransVerifications(array $data)
  {
    $insert_veritrans_verifications = DB::table('veritrans_verifications')->insertGetId($data);
        
    return $insert_veritrans_verifications;
  }

  public static function createKlikbcaNotifications(array $data)
  {
  	$insert_klikbca_notifications = DB::table('klikbca_notification')->insert($data);
    
    //$lastInsertedId= $insert_klikbca_notifications->id;
    
    return $insert_klikbca_notifications;
  }

  public static function createOrderItemHistory(array $data)
  {
  	$insert_order_item_history = DB::table('order_item_history')->insert($data);
  	//$lastInsertedId= $insert_order_item_history->id;
    
    return $insert_order_item_history;
  }

  public static function createKlikpayNotifications(array $data)
  {
    $insert_klipay_notifications = DB::table('klikpays_notifications')->insert($data);
    
    //$lastInsertedId= $insert_klipay_notifications->id;
    
    return $insert_klipay_notifications;
  }

  public static function fetchOrderPayment(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_payment = DB::table('order_payment')->where($data)->get();
  	$queries = DB::getQueryLog();
  	return $read_order_payment;
  }

  public static function fetchOrderHeader(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_header = DB::table('order_header')->where($data)->get();
  	$queries = DB::getQueryLog();
  	return $read_order_header;
  }
  
  public static function fetchVeritransNotification(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_header = DB::table('veritrans_notifications')->where($data)->first();
  	$queries = DB::getQueryLog();
  	return $read_order_header;
  }

  public static function fetchOrderItem(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_item = DB::table('order_item')->where($data)->get();
  	$queries = DB::getQueryLog();
  	return $read_order_item;
  }

  public static function updateOrderPaymentWithPurchaseCode($purchase_code, array $data)
  {
  	$update_order_payment = DB::table('order_payment')
            ->where('purchase_code', $purchase_code)
            ->update($data);

    return $update_order_payment;
  }

  public static function updateOrderItemWithPurchaseCode($purchase_code, array $data)
  {
  	$update_order_item = DB::table('order_item')
            ->where('purchase_code', $purchase_code)
            ->update($data);

    return $update_order_item;
  }
  
  public static function setOrderProcess($customer_email, array $data)
  {
    $update_order_process = DB::table('order_process')
      ->where('customer_email', $customer_email)
      ->update($data);
    
    return $update_order_process;
  }
  
  public static function removeOrderProcess($customer_email)
  {
    $remove_order_process = DB::table('order_process')
      ->where('customer_email', $customer_email)
      ->delete();
    
    return $remove_order_process;
  }
  
  public static function fetchOrderProcess(array $data)
  {
  	DB::connection()->enableQueryLog();
  	$read_order_item = DB::table('order_process')->where($data)->first();
  	$queries = DB::getQueryLog();
  	return $read_order_item;
  }
  
  public static function insertOrderProcess($customer_email)
  {
    $order_process['customer_email']  = $customer_email;
    $order_process['status']          = 0;
    $order_process['created_time']    = date('Y-m-d H:i:s');
    
    $create_order_process = DB::table('order_process')->insertGetId($order_process);
    if(!$create_order_process){
      return false;
    }
    
    return true;
  }
  
  public static function sendReminderMaxUsageMail($param = [])
  {
    $app_env = env('APP_ENV', 'production');
    
    $get_domain   = get_domain();
		$domain_name  = $get_domain['domain_name'];
    
    $promotions_name          = isset($param['promotions_name']) ? $param['promotions_name'] : NULL ;
    $promotions_code          = isset($param['promotions_code']) ? " [".$param['promotions_code']."]" : NULL ;
    $promotions_max_usage     = isset($param['promotions_max_usage']) ? $param['promotions_max_usage'] : NULL ;
    $promotions_current_usage = isset($param['promotions_current_usage']) ? $param['promotions_current_usage'] : NULL ;
    
    $usage_rate = 0;
    if(!is_null($promotions_name) && !is_null($promotions_max_usage) && !is_null($promotions_current_usage)){
      $usage_rate = round($promotions_current_usage / $promotions_max_usage * 100);
    }
    
    if($usage_rate > 80 && $app_env == 'production'){
      $mail_headers = "MIME-Version: 1.0" . "\r\n";
      $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      $mail_headers .= 'From: '.strtoupper($domain_name) .' <cs@berrybenka.com>' . "\r\n";

      $mail_subject = "[Reminder Promotion] " . $promotions_name . $promotions_code . " usage is now at " . $usage_rate . "% [" . $promotions_current_usage . "/" . $promotions_max_usage . "]";

      $mail_message = "Dear Onsite Team , <br> <br> " . $promotions_name . $promotions_code . " usage is now at " . $usage_rate . "% [" . $promotions_current_usage . "/" . $promotions_max_usage . "]";

      $onsite_emails = array(
        "bismar@berrybenka.com"
      );

      foreach ($onsite_emails as $email) {
        $body = array(
          "personalizations"=>array(
            array("recipient"=>$email)
          ),
          "from"=>array(
            "fromEmail"=>"bismar@berrybenka.com",
            "fromName"=>strtoupper($domain_name)
          ),
          "subject"=>$mail_subject,
          "content"=>$mail_message
        );
    
        $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
        $Mail->SendMail($body);
        // $sendmail = mail($email, $mail_subject, $mail_message, $mail_headers);
      }
    }
  }
  
  public static function sendMail($params = array(), $recipient = null) 
  { 
    $get_domain     = get_domain();
		$domain_name    = $get_domain['domain_name'];
    $domain_id      = $get_domain['domain_id'];
    
    $payment_method = $params['payment_method'];
    $purchase_code  = $params['purchase_code'];
    
    $mail_headers = "MIME-Version: 1.0" . "\r\n";
		$mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$mail_headers .= 'From: '.strtoupper($domain_name) .' <cs@berrybenka.com>' . "\r\n";
    
    if($domain_id == 3){ //Shopdeca
      $params['mail_message']         = Self::setMailMessageShopDeca($params);
      if ($payment_method == 99) {
        $params['mail_message_product'] = Self::setMailMessageProductShopDecaKredivo($params);
      } else {
        $params['mail_message_product'] = Self::setMailMessageProductShopDeca($params);
      }      
      $params['mail_message_value']   = Self::setMailMessageValueShopDeca($params);
      $params['mail_message_address'] = Self::setMailMessageAddressShopDeca($params);
      $params['mail_message_payment'] = Self::setMailMessagePaymentShopDeca($params);
      $params['mail_message_CS']      = Self::setMailMessageCSShopDeca($params);
      $params['mail_message_SM']      = Self::setMailMessageSocialMediaShopDeca($params);
      $params['mail_message_footer']  = Self::setMailMessageFooterShopDeca($params);

      $mail_subject   = "Order Received & Payment Received - " . $purchase_code . "";
      if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24 || $payment_method == 3){
        $mail_subject = "Order Received - Please Make Payment - " . $purchase_code . "";
      }else if($payment_method == 19){
        $mail_subject = "Order Received & Payment on COD - " . $purchase_code . "";
      }
    }else{ //Berrybenka or Hijabenka
      $params['mail_message']         = Self::setMailMessage($params);
      if ($payment_method == 99) {
        $params['mail_message_product'] = Self::setMailMessageProductKredivo($params);
      } else if($payment_method == 135){
        $params['mail_message_product'] = Self::setMailMessageProductTcash($params);
      } else {
        $params['mail_message_product'] = Self::setMailMessageProduct($params);
      }      
      $params['mail_message_value']   = Self::setMailMessageValue($params);
      $params['mail_message_address'] = Self::setMailMessageAddress($params);
      $params['mail_message_payment'] = Self::setMailMessagePayment($params);
      $params['mail_message_CS']      = Self::setMailMessageCS($params);
      $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
      $params['mail_message_footer']  = Self::setMailMessageFooter($params);

      $mail_subject   = "Order telah Diterima & Pembayaran telah Diterima - " . $purchase_code . "";
      if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24 || $payment_method == 3){
        $mail_subject = "Order Telah Diterima - Silahkan Melakukan Pembayaran - " . $purchase_code . "";
      }else if($payment_method == 19){
        $mail_subject = "Order telah Diterima & Pembayaran dilakukan di Tempat - " . $purchase_code . "";
      }
    }
    
    if ($recipient == null) {
      $recipient = Auth::user()->customer_email;
    }

		$message 	= response()->view('mailtemplates.mailtemplates_order', $params)->content();
    // $sendmail = mail($recipient, $mail_subject, $message, $mail_headers);
    
    $body = array(
      "personalizations"=>array(
        array("recipient"=>$recipient)
      ),
      "from"=>array(
        "fromEmail"=>"cs@berrybenka.com",
        "fromName"=>strtoupper($domain_name)
      ),
      "subject"=>$mail_subject,
      "content"=>$message
    );

    $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
    $Mail->SendMail($body);
    
    /*Send Email to On-Site Team if order is from beta domain
    $onsite_emails = array(
      "boan@berrybenka.com", 
      "alief@berrybenka.com", 
      "herman@berrybenka.com", 
      "irfan@berrybenka.com", 
      "effendy@berrybenka.com", 
      "dhima@berrybenka.com", 
      "fathan@berrybenka.com", 
      "bismar@berrybenka.com", 
      "adhika@berrybenka.com"
    );
    
    $email_auth_cs = (session()->has('email_auth_cs')) ? session('email_auth_cs') : NULL;
    
    $server_name = \Request::server('SERVER_NAME');
    $order_beta = false;
    if(strpos($server_name, 'beta') !== false || strpos($server_name, 'm-beta') !== false){
      $order_beta = true;
    }
    
    if($email_auth_cs != NULL || $order_beta){
      $onsite_mail_subject = $email_auth_cs == NULL ? '[BETA Order] ' : '[CS Order - '.$email_auth_cs.'] ';
      
      foreach ($onsite_emails as $email) {
        $sendmail 	= mail($email, $onsite_mail_subject . $mail_subject, $message, $mail_headers);
      }
    }
    /*End Send*/
	}
  
  public static function setMailMessage($params = array())
  {
    $payment_method         = $params['payment_method'];
    $customer_fname         = $params['customer_fname'];
    $customer_lname         = $params['customer_lname'];
    $purchase_code          = $params['purchase_code'];
    $purchase_date          = $params['purchase_date'];
    $veritrans_payment_code = $params['veritrans_payment_code'];
    $veritrans_va_number    = $params['veritrans_va_number'];
    $email_banner           = isset($params['email_banner']) ? $params['email_banner'] : '';
    
    $message = "
      Dear <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
      <br/><br/>
    ";
    
    if(isset($email_banner) && $email_banner != ''){
      $message .= "
        <img src='".$email_banner."' width='100%' />
        <br/><br/>
      ";
    }
    
    if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24){
      if($payment_method == 24){ //Indomaret
        $message .= "
          Cara melakukan pembayaran di Indomaret:
          <br/><br/>
          1.	Pergi ke Indomaret terdekat dan berikan Kode Pembayaran Anda <strong>".$veritrans_payment_code."</strong> ke kasir.<br/>
          2.	Kasir Indomaret akan mengkonfirmasi transaksi dengan menanyakan Jumlah Tagihan dan Nama Toko.<br/>
          3.	Bayar sesuai Jumlah Tagihan Anda.<br/>
          4.	Setelah pembayaran diterima, Anda akan menerima konfirmasi yang dikirimkan ke email.<br/>
          5.	Simpanlah struk transaksi Indomaret Anda sebagai bukti pembayaran.<br/>
          <br/>
        ";
      }else if($payment_method == 28){ //BCA Virtual Account
        $message .= "
          Terima kasih sudah melakukan pemesanan. Silakan lakukan pembayaran dalam 2x24 jam untuk
          <br/>
          menghindari pembatalan pesanan.
          <br/>
          Berikut adalah nomor BCA Virtual Account kamu:
          <br/>
          <div class='thx-wrapper'>
            <img class='logo-bca-thx' src='" . asset('berrybenka/desktop/img/bca-logo.png') . "' alt=''>
              <p>Kode Pembayaran BCA Virtual Account</p>
              <h1>".$veritrans_va_number."</h1>
          </div>
          <br>
          Cara pembayaran menggunakan <strong>ATM BCA</strong>:
          <br/><br/>
          1.	Pilih <strong>Transaksi Lainnya</strong> > <strong>Transfer</strong> > <strong>Ke Rek BCA Virtual Account</strong><br/>
          2.	Masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan pilih <strong>Benar</strong><br/>
          3.	Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian pilih <strong>Ya</strong><br/>
          <br/>
          
          Cara pembayaran menggunakan <strong>KlikBCA</strong>:
          <br/><br/>
          1. Pilih <strong>Transfer Dana</strong> > <strong>Transfer ke BCA Virtual Account</strong><br/>
          2. Centang No. Virtual Account lalu masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan klik <strong>Lanjutkan</strong><br/>
          3. Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>Lanjutkan</strong><br/>
          4. Ambil BCA Token kamu dan masukkan respons KeyBCA Appli 1, kemudian klik <strong>Kirim</strong><br/>
          <br/>
          
          Cara pembayaran menggunakan <strong>m-BCA</strong>:
          <br/><br/>
          1. Pilih <strong>m-Transfer</strong> > <strong>BCA Virtual Account</strong><br/>
          2. Masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan klik <strong>OK</strong> > <strong>Send</strong><br/>
          3. Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>OK</strong><br/>
          4. Masukkan PIN m-BCA kamu dan klik <strong>OK</strong><br/>
          <br/>
          
          Berikut adalah detail pesanan kamu:
          <br>
        ";
      }else if($payment_method == 98){ //Permata Virtual Account
        $message .= "
          Terima kasih sudah melakukan pemesanan. Silakan lakukan pembayaran dalam 2x24 jam untuk
          <br/>
          menghindari pembatalan pesanan.
          <br/>
          Berikut adalah nomor Virtual Account kamu:
          <br/>
          <div class='thx-wrapper'>
            <img class='logo-bca-thx' src='" . asset('berrybenka/desktop/img/permata_va.jpg') . "' alt=''>
              <p>Kode Pembayaran Virtual Account</p>
              <h1>".$veritrans_va_number."</h1>
          </div>
          <br>
          
          Berikut adalah detail pesanan kamu:
          <br>
        ";
      }else{
        $message .= "
          Kami telah menerima pesanan Anda. Silahkan lakukan pembayaran dalam 2x24 jam dengan metode pembayaran yang telah anda pilih untuk memproses pesanan Anda. Pesanan akan otomatis dibatalkan jika pembayaran tidak dilakukan.
          <br/><br/>
          Hal-hal yang harus diperhatikan ketika melakukan pembayaran:<br/>
          1.	Mohon transfer tanpa pembulatan sampai ke 3 (tiga) digit kode pembayaran<br/>
          2.	Saat transfer via internet banking/teller/ATM non tunai, mohon cantumkan Kode Pembelian pada keterangan berita transfer<br/>
          3.	Pembayaran untuk Kode Pembelian berbeda harus dilakukan secara terpisah<br/>
          4.	Mohon lakukan konfirmasi setelah melakukan pembayaran, dengan menekan tombol <strong>KONFIRMASI PEMBAYARAN</strong> di bawah<br/>
          <br/>
        ";
      }
    }else if($payment_method == 3){
      $message .= "
        Kami telah menerima pesanan Anda. Silahkan melakukan pembayaran sesuai dengan metode pembayaran yang Anda sudah pilih. Mohon bayar dalam 2 jam setelah transaksi untuk mendapatkan barang pemesanan Anda atau order Anda akan dibatalkan.
				<br/>
      ";
    }else{
      $message .= "
        Kami telah menerima pesanan Anda. Kami akan memproses pesanan Anda dan silahkan menunggu pengiriman barang Anda.
				<br/>
      ";
    }
    
    $message .= "
      <table>
        <tr>
          <td>Kode Pembelian</td>
          <td>:</td>
          <td><b>" . $purchase_code . "</b></td>
        </tr>
        <tr>
          <td>Tanggal Pembelian</td>	
          <td>:</td>
          <td><b>" . $purchase_date . "</b></td>
        </tr>
    ";
    
    if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24 || $payment_method == 3){
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Menunggu Pembayaran</b></td>
					</tr>
				</table>
      ";
    }else if($payment_method == 19){
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Pembayaran dilakukan di tempat</b></td>
					</tr>
				</table>
      ";
    }else{
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Pembayaran sudah diterima</b></td>
					</tr>
				</table>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessageShopDeca($params = array())
  {
    $payment_method         = $params['payment_method'];
    $customer_fname         = $params['customer_fname'];
    $customer_lname         = $params['customer_lname'];
    $purchase_code          = $params['purchase_code'];
    $purchase_date          = $params['purchase_date'];
    $veritrans_payment_code = $params['veritrans_payment_code'];
    $veritrans_va_number    = $params['veritrans_va_number'];
    $email_banner           = isset($params['email_banner']) ? $params['email_banner'] : '';
    
    $message = "
      Dear <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
      <br/><br/>
    ";
    
    if(isset($email_banner) && $email_banner != ''){
      $message .= "
        <img src='".$email_banner."' width='100%' />
        <br/><br/>
      ";
    }
    
    if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24){
      if($payment_method == 24){ //Indomaret
        $message .= "
          Your payment code is: <strong>".$veritrans_payment_code."</strong>
          <br><br>
          How to make your payment at Indomaret store:
          <br><br>
          1.	Show your payment code to the cashier. <br/>
          2.	The cashier will confirm your transaction by asking the following details: store name and the transaction amount. <br/>
          3.	Make your payment at the cashier. <br/>
          4.	After the payment is made, you will receive an e-mail notification. <br/>
          5.	Please keep the receipt of your payment at Indomaret as a proof of payment. <br/>
          <br><br>
          Please complete your payment within <strong>2 x 24</strong> hours to avoid cancellation.
          <br><br>
          Please keep your Username/Password secure and confidential at all times.

          Feel free to contact us for further information and assistance.
        ";
      }else if($payment_method == 28){ //BCA Virtual Account
        $message .= "
          Terima kasih sudah melakukan pemesanan. Silakan lakukan pembayaran dalam 2x24 jam untuk
          <br/>
          menghindari pembatalan pesanan.
          <br/>
          Berikut adalah nomor BCA Virtual Account kamu:
          <br/>
          <div class='thx-wrapper'>
            <img class='logo-bca-thx' src='" . asset('berrybenka/desktop/img/bca-logo.png') . "' alt=''>
              <p>Kode Pembayaran BCA Virtual Account</p>
              <h1>".$veritrans_va_number."</h1>
          </div>
          <br>
          Cara pembayaran menggunakan <strong>ATM BCA</strong>:
          <br/><br/>
          1.	Pilih <strong>Transaksi Lainnya</strong> > <strong>Transfer</strong> > <strong>Ke Rek BCA Virtual Account</strong><br/>
          2.	Masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan pilih <strong>Benar</strong><br/>
          3.	Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian pilih <strong>Ya</strong><br/>
          <br/>
          
          Cara pembayaran menggunakan <strong>KlikBCA</strong>:
          <br/><br/>
          1. Pilih <strong>Transfer Dana</strong> > <strong>Transfer ke BCA Virtual Account</strong><br/>
          2. Centang No. Virtual Account lalu masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan klik <strong>Lanjutkan</strong><br/>
          3. Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>Lanjutkan</strong><br/>
          4. Ambil BCA Token kamu dan masukkan respons KeyBCA Appli 1, kemudian klik <strong>Kirim</strong><br/>
          <br/>
          
          Cara pembayaran menggunakan <strong>m-BCA</strong>:
          <br/><br/>
          1. Pilih <strong>m-Transfer</strong> > <strong>BCA Virtual Account</strong><br/>
          2. Masukkan nomor BCA Virtual Account kamu <strong>".$veritrans_va_number."</strong> dan klik <strong>OK</strong> > <strong>Send</strong><br/>
          3. Pastikan informasi nama dan total tagihan yang tertera sudah benar, kemudian klik <strong>OK</strong><br/>
          4. Masukkan PIN m-BCA kamu dan klik <strong>OK</strong><br/>
          <br/>
          
          Berikut adalah detail pesanan kamu:
          <br>
        ";
      }else if($payment_method == 98){ //Permata Virtual Account
        $message .= "
          Terima kasih sudah melakukan pemesanan. Silakan lakukan pembayaran dalam 2x24 jam untuk
          <br/>
          menghindari pembatalan pesanan.
          <br/>
          Berikut adalah nomor Virtual Account kamu:
          <br/>
          <div class='thx-wrapper'>
            <img class='logo-bca-thx' src='" . asset('berrybenka/desktop/img/permata_va.jpg') . "' alt=''>
              <p>Kode Pembayaran Virtual Account</p>
              <h1>".$veritrans_va_number."</h1>
          </div>          
          <br/>
          Berikut adalah detail pesanan kamu:
          <br>
        ";
      }else{
        $message .= "
          We have received your order. You may now make your payment through bank transfer. Please kindly complete your payment within 2 x 24 hours since this transaction is made, to avoid cancelation of your order.
          <br/><br/>
          Please note: <br/>
          1.	Please transfer the exact amount stated in the grand total below without rounding the last 3 digits that contain the payment code. <br/>
          2.	Please input your purchase code as the remark when making the transaction via ATM/Internet Banking/Bank Teller. <br/>
          3.	Payment for different purchase code should be made in separate transactions. <br/>
          4.	After the payment is made, please confirm your payment by clicking the 'CONFIRM PAYMENT' button below.<br/>
          <br/>
        ";
      }
    }else if($payment_method == 3){
      $message .= "
        We hereby notify you that we have received your payment and we will process your order. Please kindly await for the arrival of your purchased item(s).
				<br/>
      ";
    }else{
      $message .= "
        We hereby notify you that we have received your payment and we will process your order. Please kindly await for the arrival of your purchased item(s).
				<br/>
      ";
    }
    
    $message .= "
      <table>
        <tr>
          <td>Purchase Code</td>
          <td>:</td>
          <td><b>" . $purchase_code . "</b></td>
        </tr>
        <tr>
          <td>Purchase Date</td>	
          <td>:</td>
          <td><b>" . $purchase_date . "</b></td>
        </tr>
    ";
    
    if($payment_method == 1 || $payment_method == 2 || $payment_method == 28 || $payment_method == 98 || $payment_method == 29 || $payment_method == 30 || $payment_method == 24 || $payment_method == 3){
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Awaiting Payment</b></td>
					</tr>
				</table>
      ";
    }else if($payment_method == 19){
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Cash on Delivery</b></td>
					</tr>
				</table>
      ";
    }else{
      $message .= "
        <tr>
						<td>Status</td>
						<td>:</td>
						<td><b>Payment Received</b></td>
					</tr>
				</table>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessageProduct($params = array())
  {
    $message    = "";
    $fetch_cart = CheckoutCart::fetchCart();
    
    foreach($fetch_cart as $cart){
      $message .= "
        <tr style='color:#444; line-height:18px; border-bottom:1px'>
          <td width='300' style='padding:15px;'>
            <span><b>" . stripslashes($cart['name']) . "</b></span><br/>
            <span><b>" . $cart['brand_name'] . "</b></span><br/>
            <span>Warna: " . $cart['color_name'] . "</span><br/>
            <span>Ukuran: " . $cart['size'] . " </span>
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($cart['price'], 0, ".", ".") . "
          </td>
          <td width='100' style='padding:15px; text-align:center;'>
            " . $cart['qty'] . "
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($cart['price']*$cart['qty'], 0, ".", ".") . "
          </td>
        </tr>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessageProductShopDeca($params = array())
  {
    $message    = "";
    $fetch_cart = CheckoutCart::fetchCart();
    
    foreach($fetch_cart as $cart){
      $message .= "
        <tr style='color:#444; line-height:18px; border-bottom:1px'>
          <td width='300' style='padding:15px;'>
            <span><b>" . stripslashes($cart['name']) . "</b></span><br/>
            <span><b>" . $cart['brand_name'] . "</b></span><br/>
            <span>Color: " . $cart['color_name'] . "</span><br/>
            <span>Size: " . $cart['size'] . " </span>
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($cart['price'], 0, ".", ".") . "
          </td>
          <td width='100' style='padding:15px; text-align:center;'>
            " . $cart['qty'] . "
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($cart['price']*$cart['qty'], 0, ".", ".") . "
          </td>
        </tr>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessageValue($params = array())
  {
    $grand_total      = $params['grand_total'];
    $purchase_price   = $params['purchase_price'];
    $paycode          = $params['paycode'];
    $shipping_finance = $params['shipping_finance'];
    $get_discount     = $params['get_discount'];
    $benka_point      = $params['credit_use'];
    
    $message = "
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          TOTAL PEMBELIAN
        </td>
        <td width='100' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($purchase_price, 0, ".", ".") . "
        </td>
      </tr>
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          KODE PEMBAYARAN
        </td>
        <td width='100' style='padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($paycode, 0, ".", ".") . "
        </td>
      </tr>
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          BIAYA PENGIRIMAN
        </td>
        <td width='100' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($shipping_finance, 0, ".", ".") . "
        </td>
      </tr>
    ";
    
    if(!empty($get_discount)){
      foreach($get_discount as $discount){
        if($discount->total_discount > 0 || $discount->promotions_template_mode == 4){
          $discount_value = (is_float($discount->total_discount)) ? round($discount->total_discount) : $discount->total_discount;
          $message .= "
            <tr style='color:#fff; line-height:18px; border-bottom:1px'>
              <td colspan='3' width='140' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
                <b>" . $discount->discount_name . "</b>
              </td>
              <td width='100' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
                <b>(-) IDR " . number_format($discount_value, 0, ".", ".") . "</b>
              </td>
            </tr>
          ";
        }
      }
    }
    
    $message .= "
      <tr style='color:#fff; line-height:18px; border-bottom:1px'>
        <td colspan='3' width='140' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>Gunakan sisa kredit Anda</b>
        </td>
        <td width='100' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>(-) IDR " . number_format($benka_point, 0, ".", ".") . "</b>
        </td>
      </tr>
      <tr style='color:#fff; line-height:18px; border-bottom:1px'>
        <td colspan='3' width='140' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>TOTAL HARGA</b>
        </td>
        <td width='100' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>IDR " . number_format($grand_total, 0, ".", ".") . "</b>
        </td>
        </td>
      </tr>
    ";
    
    return $message;
  }
  
  public static function setMailMessageValueShopDeca($params = array())
  {
    $grand_total      = $params['grand_total'];
    $purchase_price   = $params['purchase_price'];
    $paycode          = $params['paycode'];
    $shipping_finance = $params['shipping_finance'];
    $get_discount     = $params['get_discount'];
    $benka_point      = $params['credit_use'];
    
    $message = "
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          SUBTOTAL
        </td>
        <td width='100' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($purchase_price, 0, ".", ".") . "
        </td>
      </tr>
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          PAYMENT CODE
        </td>
        <td width='100' style='padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($paycode, 0, ".", ".") . "
        </td>
      </tr>
      <tr style='color:#444; line-height:18px; font-size:12px;'>
        <td colspan='3' width='140' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          SHIPPING COST
        </td>
        <td width='100' style='padding:10px 15px; text-align:right; border-top:1px solid  #ddd;'>
          IDR " . number_format($shipping_finance, 0, ".", ".") . "
        </td>
      </tr>
    ";
    
    if(!empty($get_discount)){
      foreach($get_discount as $discount){
        if($discount->total_discount > 0 || $discount->promotions_template_mode == 4){
          $discount_value = (is_float($discount->total_discount)) ? round($discount->total_discount) : $discount->total_discount;
          $message .= "
            <tr style='color:#fff; line-height:18px; border-bottom:1px'>
              <td colspan='3' width='140' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
                <b>" . $discount->discount_name . "</b>
              </td>
              <td width='100' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
                <b>(-) IDR " . number_format($discount_value, 0, ".", ".") . "</b>
              </td>
            </tr>
          ";
        }
      }
    }
    
    $message .= "
      <tr style='color:#fff; line-height:18px; border-bottom:1px'>
        <td colspan='3' width='140' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>GRAND TOTAL</b>
        </td>
        <td width='100' style='background-color:#444444; padding:15px; text-align:right; border-top:1px solid  #ddd;'>
          <b>IDR " . number_format($grand_total, 0, ".", ".") . "</b>
        </td>
        </td>
      </tr>
    ";
    
    return $message;
  }
  
  public static function setMailMessageAddress($params = array())
  {
    $message = "";

    $address    = $params['order_shipping_address'];
    $city       = $params['order_city'];
    $province   = $params['order_province'];
    $postcode   = $params['order_postcode'];
    $phone      = $params['order_phone'];
    
    /*$message .= "<p style='padding-left:15px;'>Terkait libur Idul Fitri tahun ini yang akan jatuh di tanggal 25-26 Juni 2017, sebagian besar partner logistik pengiriman yang bekerjasama dengan Berrybenka akan menjalani libur operasional pada tanggal 24 Juni-2 Juli 2017. Dalam rangka mengantisipasi kendala pengiriman baik reguler ataupun COD yang mungkin terjadi selama peak season tersebut, untuk memastikan bahwa pesanan sampai di tangan kamu sebelum Idul Fitri maka mohon agar memperhatikan timeline sebagai berikut:<br><br></p>
                <table style='font-family:helvetica,arial;font-size:12px'>
                <tr>
                  <td style='width:150px'>Area tujuan</td>
                  <td>Tanggal terakhir memesan agar  pesanan sampai sebelum libur Idul Fitri</td>
                </tr>
                <tr>
                  <td>Jabodetabek</td>
                  <td>21 Juni 2017</td>
                </tr>
                <tr>
                  <td>Jawa & Sumatera</td>
                  <td>19 Juni 2017</td>
                </tr>
                <tr>
                  <td>Luar Jawa & Sumatera</td>
                  <td>15 Juni 2017</td>
                </tr>
              </table><br><br>
  
              <p style='padding-left:15px;'>Pengiriman akan kembali normal per tanggal 3 Juli 2017. Sementara itu, semua offline stores kami akan beroperasional seperti biasa selama libur Idul Fitri. Kami mohon maaf atas ketidaknyamanan tersebut, apabila ada pertanyaan lebih lanjut dapat dikirimkan via email ke cs@berrybenka.com.</p>";*/
    
    $message .= "
      <tr style='color:#444; line-height:18px;'>
        <td colspan='4' style='padding:15px ;'>
          " . $address . "<br/>
          " . $city . "<br/>
          " . $province . " " . $postcode . "<br/>
          " . $phone . "<br/>
        </td>
      </tr>
    ";
    
    return $message;
  }
  
  public static function setMailMessageAddressShopDeca($params = array())
  {
    $message = "";

    $address    = $params['order_shipping_address'];
    $city       = $params['order_city'];
    $province   = $params['order_province'];
    $postcode   = $params['order_postcode'];
    $phone      = $params['order_phone'];
    
    /*$message .= "<p style='padding-left:15px;'>In regards to the Ramadan holiday season this year, we will experience slowdown of delivery process closer to Eid al-Fitr as most of logistics partners will pause their operations between 24 June-2 July 2017. As such, to ensure that your order (either regular or COD) gets delivered before the Eid al-Fitr, please follow the order timeline as follow:<br><br></p> 
        <table style='font-family:helvetica,arial;font-size:12px'>
          <tr>
            <td style='width:150px'>Jabodetabek</td>
            <td style='width:10px'>:</td>
            <td>latest by 21 June 2017</td>
          </tr>
          <tr>
            <td>Java & Sumatera</td>
            <td>:</td>
            <td>latest by 19 June 2017</td>
          </tr>
          <tr>
            <td>Outside of Java & Sumatera</td>
            <td>:</td>
            <td>latest by 15 June 2017</td>
          </tr>
        </table><br><br>
      <p style='padding-left:15px;'>For orders done after the aforementioned dates will be delivered after the Eid al-Fitr. All operations will resume normal by 3 July 2017. Meanwhile, all of our offline stores will continue operate normally during the Ramadan holiday. Should you have any question, please reach out to us via shopdeca@berrybenka.com.</p>";*/

    $message .= "
      <tr style='color:#444; line-height:18px;'>
        <td colspan='4' style='padding:15px ;'>
          " . $address . "<br/>
          " . $city . "<br/>
          " . $province . " " . $postcode . "<br/>
          " . $phone . "<br/>
        </td>
      </tr>
    ";
    
    return $message;
  }
  
  public static function setMailMessagePayment($params = array())
  {
    $payment_method         = $params['payment_method'];
    $purchase_code          = $params['purchase_code'];
    $veritrans_payment_code = $params['veritrans_payment_code'];
    $veritrans_va_number    = $params['veritrans_va_number'];
    
    $message_confirmation = ""
      . "<p>Apabila Anda telah melakukan pembayaran, konfirmasi pembayaran Anda dengan klik tombol dibawah ini <br/><br/> "
      . "<center><a style='background-color: LightGrey;font-size: 16px;color: #000;font-weight: bold;padding: 12px;border-radius: 10px;text-decoration: none;' href=\"".url('/')."/user/order_history_detail/" . $purchase_code . "\" target=\"_blank\">KONFIRMASI PEMBAYARAN</a></center>"
      . "</p> <br/>"
    . "";
    
    $message = "";
    
    if($payment_method == 1){
      $message .= "
        <p>
          Pembayaran dapat dilakukan ke:<br/>
          Bank BCA<br/>
          a/n PT. Berrybenka<br/>
          No. Rekening : 546 032 7077
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 2){
      $message .= "
        <p>
            Pembayaran dapat dilakukan ke:<br/>
            Bank Mandiri<br/>
            a/n PT. Berrybenka<br/>
            No. Rekening : 165 000 042 7964
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 29){
      $message .= "
        <p>
            Pembayaran dapat dilakukan ke:<br/>
            Bank BNI<br/>
            a/n PT. Berrybenka<br/>
            No. Rekening : 290 222 0008
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 30){
      $message .= "
        <p>
            Pembayaran dapat dilakukan ke:<br/>
            Bank BRI<br/>
            a/n PT. Berrybenka<br/>
            No. Rekening : 0505 01 000 151302
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 3){
      $message .= "
        <p>
        Pembayaran dapat dilakukan ke:<br/>
        <b>KlikBCA</b><br/> 
        pembayaran dilakukan paling lambat <b>2 jam</b> setelah Anda melakukan order.
        </p>
      ";
    }else if($payment_method == 4){
      $message .= "
        <p>
          Pembayaran menggunakan BCA KlikPay yang disediakan Bank BCA            
        </p>
      ";
    }else if($payment_method == 5){
      $message .= "
        <p>
          Pembayaran menggunakan Visa / Mastercard yang disediakan Veritrans.           
        </p>
      ";
    }else if($payment_method == 20){
      $message .= "
        <p>
          Anda menggunakan Mandiri Debit Online<br/>
          Transaksi Online eCommerce Verified by Visa (3D Secure)
        </p>
      ";
    }else if($payment_method == 24){
      $message .= "
        Harap melakukan pembayaran maksimal 2x24 jam di Indomaret terdekat, 
        <br/>jika tidak maka transaksi akan dibatalkan. 
        <br/>Kode pembayaran anda  : ".$veritrans_payment_code."
      ";
    }else if($payment_method == 28){
      $message .= "
        Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan.
      ";
    }else if($payment_method == 98){
      $message .= "
        <p>Cara pembayaran menggunakan ATM Bersama & Mandiri:
          <ol>
            <li>Pilih Transaksi Lainnya > Transfer > Antar Bank Online</li>
            <li>Masukkan nomor Virtual Account kamu 013 ". $veritrans_va_number ." (kode bank Permata beserta 16 digit VA), kemudian pilih Benar</li>
            <li>Masukkan jumlah harga yang akan Anda bayar secara lengkap tanpa pembulatan (penting: jumlah nominal yang tidak sesuai dengan tagihan akan menyebabkan transaksi gagal), kemudian pilih Benar</li>
            <li>Kosongkan nomor referensi, kemudian pilih Benar</li>
          <ol>
        </p>
        <p>Cara pembayaran menggunakan ATM Alto & Permata:
          <ol>
            <li>Pilih Transaksi Lainnya > Pembayaran > Pembayaran Lainnya > Virtual Account</li>
            <li>Masukkan nomor Virtual Account kamu ". $veritrans_va_number ." (16 digit VA), kemudian pilih Benar</li>
            <li>Pastikan informasi total tagihan, rekening tujuan, dan nama toko yang tertera sudah benar, kemudian pilih Benar</li>
            <li>Pilih rekening pembayaran Anda, kemudian pilih Benar</li>
          </ol>
        </p>
        <p>Cara pembayaran via ATM Prima & BCA
          <ol>
            <li>Pilih Transaksi Lainnya > Transfer > Ke Rek Bank Lain</li>
            <li>Masukkan 013 (kode bank Permata), kemudian pilih Benar</li>
            <li>Masukkan jumlah harga yang akan Anda bayar secara lengkap tanpa pembulatan (penting: jumlah nominal yang tidak sesuai dengan tagihan akan menyebabkan transaksi gagal), kemudian pilih Benar</li>
            <li>Masukkan nomor Virtual Account kamu ". $veritrans_va_number ." (16 digit VA), kemudian pilih Benar</li>
            <li>Pastikan informasi total tagihan dan rekening tujuan yang tertera sudah benar, kemudian pilih Benar</li>
          </ol>
        </p>
        Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan.
      ";
    }else if($payment_method >= 6 || $payment_method != 19){
      //$message .= "
      //  <p>Pembayaran cicilan menggunakan Visa / Mastercard yang disediakan oleh Veritrans.
      //    " . $fetch_payment -> master_payment_name . " Installment<br/>
      //    Subtotal: IDR " . number_format($params["invoice_detail"] -> grand_total, 0, ".", ".") . "<br/>
      //    Cicilan " . $fetch_payment -> master_payment_name . " Installment angsuran " . $fetch_payment -> master_payment_description . ", bunga 0% - IDR " . number_format($cicil_bulan, 0, ".", ".") . "
      //  </p>
      //";
    }else if($payment_method == 19){
      $message .= "
        <p>
          Anda menggunakan Cash On Delivery<br/>
          Pembayaran dilakukan kepada petugas kami
        </p>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessagePaymentShopDeca($params = array())
  {
    $payment_method         = $params['payment_method'];
    $purchase_code          = $params['purchase_code'];
    $veritrans_payment_code = $params['veritrans_payment_code'];
    $veritrans_va_number    = $params['veritrans_va_number'];
    
    $message_confirmation = ""
      . "<p>Once the payment is made, please confirm your payment by clicking the following button <br/><br/> "
      . "<center><a style='background-color: LightGrey;font-size: 16px;color: #000;font-weight: bold;padding: 12px;border-radius: 10px;text-decoration: none;' href=\"".url('/')."/user/order_history_detail/" . $purchase_code . "\" target=\"_blank\">CONFIRM PAYMENT</a></center>"
      . "</p> <br/>"
    . "";
    
    $message = "";
    
    if($payment_method == 1){
      $message .= "
        <p>
          Please make your payment to the following bank account::<br/>
          Bank Name: Bank BCA<br/>
          Account Name: PT. Berrybenka<br/>
          Account Number: 546 032 7077
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 2){
      $message .= "
        <p>
          Please make your payment to the following bank account:<br/>
          Bank Name: Bank Mandiri<br/>
          Account Name: PT. Berrybenka<br/>
          Account Number: 165 000 042 7964
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 29){
      $message .= "
        <p>
          Please make your payment to the following bank account:<br/>
          Bank Name: Bank BNI<br/>
          Account Name: PT. Berrybenka<br/>
          Account Number: 290 222 0008
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 30){
      $message .= "
        <p>
          Please make your payment to the following bank account:<br/>
          Bank Name: Bank BRI<br/>
          Account Name: PT. Berrybenka<br/>
          Account Number: 0505 01 000 151302
        </p>
        ".$message_confirmation."
      ";
    }else if($payment_method == 3){
      $message .= "
        <p>
          Payment Method: <b>KlikBCA</b> <br/>
          Please complete your payment within 2 hours to avoid cancellation
        </p>
      ";
    }else if($payment_method == 4){
      $message .= "
        <p>
          Payment made through BCA KlikPay            
        </p>
      ";
    }else if($payment_method == 5){
      $message .= "
        <p>
          Payment made using VISA / MasterCard supported by Midtrans.           
        </p>
      ";
    }else if($payment_method == 20){
      $message .= "
        <p>
          Payment made using Mandiri Debit Online supported by Midtrans. 
        </p>
      ";
    }else if($payment_method == 24){
      $message .= "
        Please complete your payment within <strong> 2 x 24 hours </strong> to avoid cancellation
      ";
    }else if($payment_method == 28){
      $message .= "
        Please complete your payment within <strong> 2 x 24 hours </strong> to avoid cancellation
      ";
    }else if($payment_method == 98){
      $message .= "
        <p> How to pay by ATM Bersama & Mandiri:
          <ol>
            <li>Select Other Transactions> Transfer> Inter Online Bank</li>
            <li>Enter your Virtual Account number 013 ". $veritrans_va_number ." (Permata bank code along with 16 digits VA), then select True</li>
            <li>Enter the amount of the price you would pay completely without rounding (important: the nominal amount that does not match the bill will cause the transaction to fail), then select True</li>
            <li>Clear the reference number, then select True</li>
          <ol>
        </p>
        <p>How to pay by ATM Alto & Permata:
          <ol>
            <li>Select Other Transactions> Payments> Other Payments> Virtual Accounts</li>
            <li>Enter your Virtual Account number ". $veritrans_va_number ." (16 digits VA), then select true</li>
            <li>Make sure the total billing information, destination account, and store name listed are correct, then select True</li>
            <li>Choose your payment account, then select True</li>
          </ol>
        </p>
        <p>How to pay via ATM Prima & BCA
          <ol>
            <li>Enter 013 (Permata bank code), then select True</li>
            <li>Select Other Transactions> Transfer> To Other Bank Records</li>
            <li>Enter the amount of the price you would pay completely without rounding (important: the nominal amount that does not match the bill will cause the transaction to fail), then select True</li>
            <li>Enter your Virtual Account number ". $veritrans_va_number ." (16 digits VA), then select True</li>
            <li>Make sure the total billing and account information listed is correct, then select True</li>
          </ol>
        </p>
        Please complete your payment within <strong> 2 x 24 hours </strong> to avoid cancellation
      ";
    }else if($payment_method >= 6 || $payment_method != 19){
      //$message .= "
      //  <p>Pembayaran cicilan menggunakan Visa / Mastercard yang disediakan oleh Veritrans.
      //    " . $fetch_payment -> master_payment_name . " Installment<br/>
      //    Subtotal: IDR " . number_format($params["invoice_detail"] -> grand_total, 0, ".", ".") . "<br/>
      //    Cicilan " . $fetch_payment -> master_payment_name . " Installment angsuran " . $fetch_payment -> master_payment_description . ", bunga 0% - IDR " . number_format($cicil_bulan, 0, ".", ".") . "
      //  </p>
      //";
    }else if($payment_method == 19){
      $message .= "
        <p>
          Payment Method: <b>Cash on Delivery</b>
        </p>
      ";
    }
    
    return $message;
  }
  
  public static function setMailMessageCS($params = array())
  {
    if ($params['payment_method'] == 99 && isset($params['kredivo_payment_type'])) {
      $get_domain = get_domain();
      $domain_id  = $get_domain['domain_id'];
      if ($domain_id == 2) {
        $kredivo_payment_info = "http://hijabenka.com/home/kredivo";
      } else {
        $kredivo_payment_info = "http://berrybenka.com/home/kredivo";
      }
      /*
      $message_CS = "
      <div mc:edit='std_footer'>Pembayaran cicilan menggunakan Kredivo, dengan opsi pembayaran ".str_replace("_", " ", $params['kredivo_payment_type']).".<br/>Untuk detil pembayaran cicilan, silahkan klik link berikut ini: <a href='".$kredivo_payment_info."'>Info Kredivo</a>
      <br/><br/>
        <b>BERRYBENKA CUSTOMER SERVICE</b><br/>
        <table style='font-family:helvetica,arial;font-size:12px'>
          <!--
          <tr>
            <td style='width:150px'>Telp</td>
            <td style='width:10px'>:</td>
            <td>(021) 2520555</td>
          </tr>
          -->
          <tr>
            <td>Email</td>
            <td>:</td>
            <td>cs [at] berrybenka [dot] com</td>
          </tr>
        </table>
        <br/><br/>
      </div>
    ";*/
      $message_CS = "
      <div mc:edit='std_footer'>Pembayaran cicilan menggunakan Kredivo, dengan opsi pembayaran ".str_replace("_", " ", $params['kredivo_payment_type']).".<br/>Untuk detil pembayaran cicilan, silahkan klik link berikut ini: <a href='".$kredivo_payment_info."'>Info Kredivo</a>
      <br/><br/>
        <b>BERRYBENKA CUSTOMER SERVICE</b><br/>
        <table style='font-family:helvetica,arial;font-size:12px'>
          <tr>
            <td>Email</td>
            <td>:</td>
            <td>cs [at] berrybenka [dot] com</td>
          </tr>
        </table>
        <br/><br/>
      </div>
    ";
    } else {
      /*
      $message_CS = "
        <div mc:edit='std_footer'>Pastikan akun Anda aman setiap saat. Apabila Anda memiliki pertanyaan atau kesulitan, silahkan hubungi kami di  :<br/><br/>
          <b>BERRYBENKA CUSTOMER SERVICE</b><br/>
          <table style='font-family:helvetica,arial;font-size:12px'>
            <tr>
              <td style='width:150px'>Telp</td>
              <td style='width:10px'>:</td>
              <td>(021) 2520555</td>
            </tr>
            <tr>
              <td>Email</td>
              <td>:</td>
              <td>cs [at] berrybenka [dot] com</td>
            </tr>
          </table>
          <br/><br/>
        </div>
      ";
      */
      $message_CS = "
        <div mc:edit='std_footer'>Pastikan akun Anda aman setiap saat. Apabila Anda memiliki pertanyaan atau kesulitan, silahkan hubungi kami di  :<br/><br/>
          <b>BERRYBENKA CUSTOMER SERVICE</b><br/>
          <table style='font-family:helvetica,arial;font-size:12px'>
            <tr>
              <td>Email</td>
              <td>:</td>
              <td>cs [at] berrybenka [dot] com</td>
            </tr>
          </table>
          <br/><br/>
        </div>
      ";
    }
    
    return $message_CS;
  }
  
  public static function setMailMessageCSShopDeca($params = array())
  {
    if ($params['payment_method'] == 99 && isset($params['kredivo_payment_type'])) {
      $kredivo_payment_info = "http://shopdeca.com/home/kredivo";
      /*
      $message_CS = "
        <div mc:edit='std_footer'>Installment payment by using Kredivo, with ".str_replace("_", " ", $params['kredivo_payment_type'])." payment option.<br/>For installment payment detail, please click the following link: <a href='".$kredivo_payment_info."'>Info Kredivo</a> <br/><br/>
          <b>SHOPDECA CUSTOMER SERVICE</b><br/>
          <table style='font-family:helvetica,arial;font-size:12px'>
          <tr>
            <td style='width:150px'>Phone</td>
            <td style='width:10px'>:</td>
            <td>(021) 2520 555 <br> Monday-Friday (8.00 am- 5.00pm)</td>
          </tr>
          <tr>
            <td>Email</td>
            <td>:</td>
            <td>cs@berrybenka.com <br> Monday-Friday (8.00 am- 8.00pm)<br>Saturday-Sunday (8.00 am- 5.00pm)</td>
          </tr>
          </table>
          <br/><br/>
        </div>
      ";*/
      $message_CS = "
        <div mc:edit='std_footer'>Installment payment by using Kredivo, with ".str_replace("_", " ", $params['kredivo_payment_type'])." payment option.<br/>For installment payment detail, please click the following link: <a href='".$kredivo_payment_info."'>Info Kredivo</a> <br/><br/>
          <b>SHOPDECA CUSTOMER SERVICE</b><br/>
          <table style='font-family:helvetica,arial;font-size:12px'>
          <tr>
            <td>Email</td>
            <td>:</td>
            <td>cs@berrybenka.com <br> Monday-Friday (8.00 am- 8.00pm)<br>Saturday-Sunday (8.00 am- 5.00pm)</td>
          </tr>
          </table>
          <br/><br/>
        </div>
      ";
    } else {
      // $message_CS = "
      //   <div mc:edit='std_footer'>Please keep your Username/Password secure and confidential at all times. <br><br> Feel free to contact us for further information and assistance. <br/><br/>
      //     <b>SHOPDECA CUSTOMER SERVICE</b><br/>
      //     <table style='font-family:helvetica,arial;font-size:12px'>
      //     <tr>
      //       <td style='width:150px'>Phone</td>
      //       <td style='width:10px'>:</td>
      //       <td>(021) 2520 555 <br> Monday-Friday (8.00 am- 5.00pm)</td>
      //     </tr>
      //     <tr>
      //       <td>Email</td>
      //       <td>:</td>
      //       <td>cs@berrybenka.com <br> Monday-Friday (8.00 am- 8.00pm)<br>Saturday-Sunday (8.00 am- 5.00pm)</td>
      //     </tr>
      //     </table>
      //     <br/><br/>
      //   </div>
      // ";
      $message_CS = "
        <div mc:edit='std_footer'>Please keep your Username/Password secure and confidential at all times. <br><br> Feel free to contact us for further information and assistance. <br/><br/>
          <b>SHOPDECA CUSTOMER SERVICE</b><br/>
          <table style='font-family:helvetica,arial;font-size:12px'>
          <tr>
            <td>Email</td>
            <td>:</td>
            <td>cs@berrybenka.com <br> Monday-Friday (8.00 am- 8.00pm)<br>Saturday-Sunday (8.00 am- 5.00pm)</td>
          </tr>
          </table>
          <br/><br/>
        </div>
      ";
    }
    
    return $message_CS;
  }
  
  public static function setMailMessageSocialMedia($params = array())
  {
    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    
    $message_SM = "
      <td valign='middle' style='background-color:#777777; padding:10px 15px;'>
        <div mc:edit='std_social' style='text-align:center;'>
          &nbsp;<a style='color:#fff;' href='http://www.twitter.com/BerrybenkaShop'><img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_twitter.png' style='margin:0 !important;' /> Follow Twitter BERRYBENKA</a> |    
          <a style='color:#fff;' href='http://www.facebook.com/BerrybenkaShop'> <img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_facebook.png' style='margin:0 !important;' />&nbsp; Like Fan Page BERRYBENKA</a> &nbsp;
        </div>
      </td>
    "; //Berrybenka
    
    if($domain_id == 2){ //Hijabenka
      $message_SM = "
        <td valign='middle' style='background-color:#777777; padding:10px 15px;'>
          <div mc:edit='std_social' style='text-align:center;'>
            &nbsp;<a style='color:#fff;' href='http://www.twitter.com/Hijabenkacom'><img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_twitter.png' style='margin:0 !important;' /> Follow Twitter HIJABENKA</a> |    
            <a style='color:#fff;' href='http://www.facebook.com/hijabenka'> <img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_facebook.png' style='margin:0 !important;' />&nbsp; Like Fan Page HIJABENKA</a> &nbsp;
          </div>
        </td>
      ";
    }
    
    return $message_SM;
  }
  
  public static function setMailMessageSocialMediaShopDeca($params = array())
  {
    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    
    $message_SM = "
      <td valign='middle' style='background-color:#777777; padding:10px 15px;'>
        <div mc:edit='std_social' style='text-align:center;'>
          &nbsp;<a style='color:#fff;' href='http://www.twitter.com/Shopdeca'><img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_twitter.png' style='margin:0 !important;' /> Follow Twitter SHOPDECA</a> |    
          <a style='color:#fff;' href='http://www.facebook.com/Shopdeca'> <img src='http://gallery.mailchimp.com/653153ae841fd11de66ad181a/images/sfs_icon_facebook.png' style='margin:0 !important;' />&nbsp; Like Fan Page SHOPDECA</a> &nbsp;
        </div>
      </td>
    ";
    
    return $message_SM;
  }
  
  public static function setMailMessageFooter(){
    $footer = "PT. BERRYBENKA. Jl. KH. Mas Mansyur no. 19
RT 09 / RW 06, Tanah Abang, Jakarta Pusat 10250, Indonesia ";
    
    return $footer;
  }
  
  public static function setMailMessageFooterShopDeca(){
    $footer = "PT. SHOPDECA. Jl. KH. Mas Mansyur no. 19
RT 09 / RW 06, Tanah Abang, Jakarta Pusat 10250, Indonesia ";
    
    return $footer;
  }

  public static function time_trx($trxDate)
  {
  	$results = DB::connection('read_mysql')->select('SELECT TIME_TO_SEC(TIMEDIFF(now(), :trxDate)) as bedajam', ['trxDate' => $trxDate]);

  	return $results;
  }

  public static function autoApproveKlikbca($transno, $userid)
  {
  	DB::beginTransaction();

  	try {
  	    // update order payment
  	    $data_order_payment['status'] 			= 1;
  	    $data_order_payment['payment_status']	= 'success';
  	    $update_order_payment = Self::updateOrderPaymentWithPurchaseCode($transno, $data_order_payment);
  	    if (! $update_order_payment) {
  	        DB::rollback();
  	        return FALSE;
  	    }

  	    // order item
  	    $data_order_item['purchase_status'] 		= 3;
  	    $data_order_item['order_status_item'] 		= 2;
  	    $data_order_item['item_warehouse_status'] 	= 0;
  	    $data_order_item['approval_date'] 			= date("Y-m-d H:i:s");
  	    $update_order_item = Self::updateOrderItemWithPurchaseCode($transno, $data_order_item);
  	    if (! $update_order_item) {
  	        DB::rollback();
  	        return FALSE;
  	    }

  	    // fetch order item
  	    $where_order_item[] = ['purchase_code', $transno];
    		$fetch_order_item = Self::fetchOrderItem($where_order_item);
    		if (! $fetch_order_item) {
    	        DB::rollback();
    	        return FALSE;
    	   }

  	    // create order_item_history
  	    foreach ($fetch_order_item as $key => $value) {
  	        $order_item_history['order_item_id']     = $value->order_item_id;
  	        $order_item_history['SKU']               = $value->SKU;
  	        $order_item_history['purchase_code']     = $transno;
  	        $order_item_history['order_status_item'] = 2;
  	        $order_item_history['created_by']        = $userid;
  	        $order_item_history['created_date']      = date('Y-m-d H:i:s');
  			    $createOrderItemHistory = Order::createOrderItemHistory($order_item_history);
  			   if (! $createOrderItemHistory) {
                  DB::rollback();
                  return FALSE;
            }
  	    }  

  	    DB::commit();
  	    // all good
  	    $return["status"] = TRUE;
        $return["message"] = "Sukses auto approve Klikbca";

        return $return;
  	} catch (\Exception $e) {
  	    DB::rollback();
  	    // something went wrong
  	    $return["status"] = FALSE;
        $return["message"] = "There was an error completing your request. Please try again. " . $e;

        return $return;
  	}
  
  }
    /*
     * EFF
     * Kredivo log requests
     */
    
    public static function createKredivoRequests($data = []){
        $insert_kredivo_requests = DB::table('kredivo_requests')->insertGetId($data);
        return $insert_kredivo_requests;        
    }
    
    /*
     * EFF
     * Kredivo log responses
     */
    
    public static function createKredivoResponses($data = []){
        $insert_kredivo_responses = DB::table('kredivo_responses')->insertGetId($data);
        return $insert_kredivo_responses;        
    }
    
    public static function updateOrderPaymentKredivoUrl($params = []){
        $result                 = false;
        $kredivo_payment_method = 99;
        if(!empty($params) && isset($params['kredivo_url'])){                                    
            $update['kredivo_redirect_uri'] = $params['kredivo_url'];
            
            $result = DB::table('order_payment')
                    ->where('purchase_code', '=', $params['purchase_code'])
                    ->where('master_payment_id', '=', $kredivo_payment_method)
                    ->update($update);
        }
        
        return $result;        
    }
    
    /* mail benka stamp*/
    public static function sendMailBenkaStamp($params = []){
        $domain         = get_domain();        
        $purchase_code  = $params['purchase_code'];
        $stamp_type     = $params['stamp_type'];                
        
        //MAIL HEADERS        
        if($stamp_type == 'PENDING'){
            $mail_subject   = "Loyalty Program - Pending Benka Stamp from #" . $purchase_code . "";
        }else{
            $mail_subject   = "Loyalty Program - Active Benka Stamp from #" . $purchase_code . "";
        }
        // $mail_headers   = "MIME-Version: 1.0" . "\r\n";
      	// $mail_headers   .= "Content-type:text/html;charset=UTF-8" . "\r\n";
      	// $mail_headers   .= 'From: '.strtoupper($domain['domain_name']) .' <cs@berrybenka.com>' . "\r\n"; 
        
        $params['mail_message']         = Self::setMailMessageStamp($params);                                           

        $message 	= response()->view('mailtemplates.mailtemplates', $params)->content();

        if (isset($params['from_cron']) && $params['from_cron'] == true) {
          $recipient  = $params['customer_email'];
        } else {
          $recipient  = Auth::user()->customer_email;
        }

        $body = array(
          "personalizations"=>array(
            array("recipient"=>$recipient)
          ),
          "from"=>array(
            "fromEmail"=>"cs@berrybenka.com",
            "fromName"=>strtoupper($domain['domain_name'])
          ),
          "subject"=>$mail_subject,
          "content"=>$message
        );
    
        $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
        $Mail->SendMail($body);
        
        // return $sendmail;
    }
    
    public static function setMailMessageStamp($params){
        $domain                 = get_domain();        
        $customer_fname         = $params['customer_fname'];
        $customer_lname         = $params['customer_lname'];
        //$purchase_code          = $params['purchase_code'];
        //$purchase_date          = $params['purchase_date'];      
        $stamp_estimate         = $params['stamp_estimate'];
        $stamp_currency         = $params['stamp_currency'];
        $stamp_type             = $params['stamp_type']; 
        
        if($stamp_type == 'PENDING'){
            $message = "
                Hi <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
                <br/><br/>
                Terimakasih telah berbelanja di Berrybenka, Hijabenka dan Berrybenka Pop Up Store.
                <br/><br/>
                Selamat Anda akan mendapatkan ". $stamp_estimate ." Benka Stamp untuk setiap pembelian senilai Rp ". number_format($stamp_currency, 0) ." (setelah diskon dan ongkos kirim) berlaku kelipatan. <br />
                Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan customer , <br />
                setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund.<br /><br />
                Sebelum ketentuan terpenuhi Benka Stamp anda dalam status pending dan dapat di cek di <a href='https://". $domain['domain_name'] .".com/user/account_dashboard'>akun</a> anda dan akan kami konfirmasi melalui email. <br /><br />
                Tukar Benka Stamp kamu Untuk Deals Berikut. <br />
                <a href='https://". $domain['domain_name'] .".com/user/stamp/deals'>List Deals</a>
                <br /><br />
                <strong>SYARAT & KETENTUAN BENKA STAMP</strong><br /><br />
                - Anda berhak mendapatkan Benka Stamp untuk setiap pembelanjaan yang sudah terkonfirmasi senilai Rp ". number_format($stamp_currency, 0) ." (setelah diskon dan ongkos kirim) atau mungkin berubah sesuai kebijakan perusahaan dan berlaku kelipatan
                Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan pelanggan. <br />
                - Setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund. Untuk pembelanjaan di Pop Up Store Berrybenka, Benka Stamp akan langsung ditambahkan ke akun Anda. <br />
                - Perolehan Benka Stamp berlaku untuk semua pembelian di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store (dengan menggunakan alamat email yang sama) <br />
                - Tukarkan Benka Stamp Anda dengan berbagai produk menarik yang akan terus kami update secara berkala <br />
                - Hadiah yang sudah ditukar tidak dapat dikembalikan dengan alasan apapun, kecuali telah ditemukan kelalaian atau kesalahan dari pihak Berrybenka
            ";
        }else{
            $message = "
                Hi <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,                                
                <br/><br/>
                Selamat Anda telah mendapatkan ". $stamp_estimate ." Active Benka Stamp untuk setiap pembelian senilai Rp ". number_format($stamp_currency, 0) ." (setelah diskon dan ongkos kirim) berlaku kelipatan. <br />
                
                Tukar Active Benka Stamp kamu Untuk Deals Berikut. <br />
                <a href='https://". $domain['domain_name'] .".com/user/stamp/deals'>List Deals</a>
            ";
        }             
        
        return $message;
    }
    /* end mail benka stamp*/

    // T-Cash Email
    public static function sendMailTcashURL($params = []){
        $domain         = get_domain();        
        $purchase_code  = $params['purchase_code'];
        
        //MAIL HEADERS
        $mail_subject   = "Order Received - Please Make Payment [T-CASH] - " . $purchase_code . "";
        $mail_headers   = "MIME-Version: 1.0" . "\r\n";
        $mail_headers   .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $mail_headers   .= 'From: '.strtoupper($domain['domain_name']) .' <cs@berrybenka.com>' . "\r\n";

        switch($domain['domain_id']){
            //BERRYBENKA
            case 1 :
                $params['mail_message']         = Self::setMailMessageTcash($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
                break;
            //HIJABENKA
            case 2 :
                $params['mail_message']         = Self::setMailMessageTcash($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);           
                break;
            default :
                $params['mail_message']         = Self::setMailMessageTcash($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
        }
        
        $message  = response()->view('mailtemplates.mailtemplates_order', $params)->content();
        $body = array(
          "personalizations"=>array(
            array("recipient"=>Auth::user()->customer_email)
          ),
          "from"=>array(
            "fromEmail"=>"cs@berrybenka.com",
            "fromName"=>strtoupper($domain['domain_name'])
          ),
          "subject"=>$mail_subject,
          "content"=>$message
        );
    
        $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
        $Mail->SendMail($body);
        // $sendmail       = mail(Auth::user()->customer_email, $mail_subject, $message, $mail_headers);
        // return $sendmail; 
    }

    // Go-Pay Email
    public static function sendMailGopayURL($params = []){
        $domain         = get_domain();        
        $purchase_code  = $params['purchase_code'];
        
        //MAIL HEADERS
        $mail_subject   = "Order Received - Please Make Payment [Go-Pay] - " . $purchase_code . "";
        $mail_headers   = "MIME-Version: 1.0" . "\r\n";
        $mail_headers   .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $mail_headers   .= 'From: '.strtoupper($domain['domain_name']) .' <cs@berrybenka.com>' . "\r\n";

        switch($domain['domain_id']){
            //BERRYBENKA
            case 1 :
                $params['mail_message']         = Self::setMailMessageGopay($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
                break;
            //HIJABENKA
            case 2 :
                $params['mail_message']         = Self::setMailMessageGopay($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);           
                break;
            default :
                $params['mail_message']         = Self::setMailMessageGopay($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
        }

        $message  = response()->view('mailtemplates.mailtemplates_order', $params)->content();

        $body = array(
          "personalizations"=>array(
            array("recipient"=>Auth::user()->customer_email)
          ),
          "from"=>array(
            "fromEmail"=>"cs@berrybenka.com",
            "fromName"=>strtoupper($domain['domain_name'])
          ),
          "subject"=>$mail_subject,
          "content"=>$message
        );
    
        $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
        $Mail->SendMail($body);
    }
    
    public static function sendMailKredivoURL($params = []){
        $domain         = get_domain();        
        $purchase_code  = $params['purchase_code'];
        
        //MAIL HEADERS
        $mail_subject   = "Order Received - Please Make Payment [KREDIVO] - " . $purchase_code . "";
        $mail_headers   = "MIME-Version: 1.0" . "\r\n";
	$mail_headers   .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$mail_headers   .= 'From: '.strtoupper($domain['domain_name']) .' <cs@berrybenka.com>' . "\r\n";                
                
        switch($domain['domain_id']){
            //BERRYBENKA
            case 1 :
                $params['mail_message']         = Self::setMailMessageKredivo($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
               
                break;
            //HIJABENKA
            case 2 :
                $params['mail_message']         = Self::setMailMessageKredivo($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);           
                break;
            //SHOPDECA
            case 3 :
                $params['mail_message']         = Self::setMailMessageKredivo($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);              
                break;
            default :
                $params['mail_message']         = Self::setMailMessageKredivo($params);
                $params['mail_message_product'] = Self::setMailMessageProduct($params);
                $params['mail_message_value']   = Self::setMailMessageValue($params);
                $params['mail_message_address'] = Self::setMailMessageAddress($params);
                $params['mail_message_payment'] = 'Harap melakukan pembayaran maksimal 2x24 jam, jika tidak maka transaksi akan dibatalkan';
                $params['mail_message_CS']      = Self::setMailMessageCS($params);
                $params['mail_message_SM']      = Self::setMailMessageSocialMedia($params);
                $params['mail_message_footer']  = Self::setMailMessageFooter($params);
        }
        $message 	= response()->view('mailtemplates.mailtemplates_order', $params)->content();
        $body = array(
          "personalizations"=>array(
            array("recipient"=>Auth::user()->customer_email)
          ),
          "from"=>array(
            "fromEmail"=>"cs@berrybenka.com",
            "fromName"=>strtoupper($domain['domain_name'])
          ),
          "subject"=>$mail_subject,
          "content"=>$message
        );
    
        $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
        $Mail->SendMail($body);
        
        // $sendmail       = mail(Auth::user()->customer_email, $mail_subject, $message, $mail_headers);
        // return $sendmail;
    }
    
    // T-cash Mail Body
    public static function setMailMessageTcash($params = array()){
        $customer_fname         = $params['customer_fname'];
        $customer_lname         = $params['customer_lname'];
        $purchase_code          = $params['purchase_code'];
        $purchase_date          = $params['purchase_date'];
        $tcash_redirector       = $params['tcash_redirector'] . "/checkout/tcash_redirect" . "?trxId=" . $purchase_code;
        $email_banner           = isset($params['email_banner']) ? $params['email_banner'] : '';

        $message = "
          Dear <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
          <br/><br/>
        ";

        if(isset($email_banner) && $email_banner != ''){
          $message .= "
            <img src='".$email_banner."' width='100%' />
            <br/><br/>
          ";
        }

        $message .= "
            Silahkan melanjutkan pembayaran T-CASH anda dengan klik tombol di bawah ini:
            <br/><br/>
            <center><a class='btn-kredivo' href=\"". $tcash_redirector ."\" target='_blank'>LANJUT KE T-CASH</a></center>
            <br/><br/>
        ";
    
        $message .= "
            <table>
                <tr>
                    <td>Purchase Code</td>
                    <td>:</td>
                    <td><b>" . $purchase_code . "</b></td>
                </tr>
                <tr>
                    <td>Purchase Date</td>  
                    <td>:</td>
                    <td><b>" . $purchase_date . "</b></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td><b>Awaiting Payment</b></td>
                </tr>
            </table>
        ";       
    
        return $message;

    }

    // Gopay Mail Body
    public static function setMailMessageGopay($params = array()){
        $customer_fname         = $params['customer_fname'];
        $customer_lname         = $params['customer_lname'];
        $purchase_code          = $params['purchase_code'];
        $purchase_date          = $params['purchase_date'];
        $tcash_redirector       = $params['gopay_url'];
        $email_banner           = isset($params['email_banner']) ? $params['email_banner'] : '';

        $message = "
          Dear <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
          <br/><br/>
        ";

        if(isset($email_banner) && $email_banner != ''){
          $message .= "
            <img src='".$email_banner."' width='100%' />
            <br/><br/>
          ";
        }

        $message .= "
            Silahkan melanjutkan pembayaran Go-Pay anda dengan klik tombol di bawah ini:
            <br/><br/>
            <center><a class='btn-kredivo' href=\"". $tcash_redirector ."\" target='_blank'>BAYAR DENGAN GOPAY</a></center>
            <br/><br/>
        ";
    
        $message .= "
            <table>
                <tr>
                    <td>Purchase Code</td>
                    <td>:</td>
                    <td><b>" . $purchase_code . "</b></td>
                </tr>
                <tr>
                    <td>Purchase Date</td>  
                    <td>:</td>
                    <td><b>" . $purchase_date . "</b></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td><b>Awaiting Payment</b></td>
                </tr>
            </table>
        ";       
    
        return $message;

    }
    
    public static function setMailMessageKredivo($params = array()){
        $customer_fname         = $params['customer_fname'];
        $customer_lname         = $params['customer_lname'];
        $purchase_code          = $params['purchase_code'];
        $purchase_date          = $params['purchase_date'];
        $kredivo_url            = $params['kredivo_url'];
        $email_banner           = isset($params['email_banner']) ? $params['email_banner'] : '';
    
        $message = "
          Dear <b>" . ucfirst($customer_fname) . " " . ucfirst($customer_lname) . "</b>,
          <br/><br/>
        ";
    
        if(isset($email_banner) && $email_banner != ''){
          $message .= "
            <img src='".$email_banner."' width='100%' />
            <br/><br/>
          ";
        }
        
        $message .= "
            Silahkan melanjutkan pembayaran anda ke website KREDIVO dengan klik tombol di bawah ini:
            <br/><br/>
            <center><a class='btn-kredivo' href=\"". $kredivo_url ."\" target='_blank'>LANJUT KE KREDIVO.COM</a></center>
            <br/><br/>
        ";
    
        $message .= "
            <table>
                <tr>
                    <td>Purchase Code</td>
                    <td>:</td>
                    <td><b>" . $purchase_code . "</b></td>
                </tr>
                <tr>
                    <td>Purchase Date</td>	
                    <td>:</td>
                    <td><b>" . $purchase_date . "</b></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td><b>Awaiting Payment</b></td>
                </tr>
            </table>
        ";       
    
    return $message;
  }

  public static function setMailMessageProductShopDecaKredivo($params)
  {
    $message    = "";
    $fetch_cart = DB::table('order_item')
                  ->select(\DB::raw('products.product_name, brand.brand_name, product_variant.variant_color_name_custom, product_variant.product_size, order_item.total_price, order_item.total_discount_price, order_item.quantity, COUNT(order_item.quantity) as qty')) 
                  ->leftJoin('products', 'products.product_id', '=', 'order_item.product_id')
                  ->leftJoin('brand', 'products.product_brand', '=', 'brand.brand_id')
                  ->leftJoin('product_variant', 'order_item.SKU', '=', 'product_variant.SKU')
                  ->where('order_item.purchase_code', '=', $params['purchase_code'])
                  ->groupBy('order_item.SKU')
                  ->orderBy('order_item.order_item_id')
                  ->get();
    
    foreach($fetch_cart as $cart){
      $price = (isset($cart->total_discount_price) && $cart->total_discount_price != 0) ? $cart->total_discount_price : $cart->total_price ;
      $message .= "
        <tr style='color:#444; line-height:18px; border-bottom:1px'>
          <td width='300' style='padding:15px;'>
            <span><b>" . stripslashes($cart->product_name) . "</b></span><br/>
            <span><b>" . $cart->brand_name . "</b></span><br/>
            <span>Warna: " . $cart->variant_color_name_custom . "</span><br/>
            <span>Ukuran: " . $cart->product_size . " </span>
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price, 0, ".", ".") . "
          </td>
          <td width='100' style='padding:15px; text-align:center;'>
            " . $cart->qty . "
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price*$cart->qty, 0, ".", ".") . "
          </td>
        </tr>
      ";
    }
    
    return $message;
  }

  public static function setMailMessageProductKredivo($params)
  {
    $message    = "";
    $fetch_cart = DB::table('order_item')
                  ->select(\DB::raw('products.product_name, brand.brand_name, product_variant.variant_color_name_custom, product_variant.product_size, order_item.total_price, order_item.total_discount_price, order_item.quantity, COUNT(order_item.quantity) as qty'))
                  ->leftJoin('products', 'products.product_id', '=', 'order_item.product_id')
                  ->leftJoin('brand', 'products.product_brand', '=', 'brand.brand_id')
                  ->leftJoin('product_variant', 'order_item.SKU', '=', 'product_variant.SKU')
                  ->where('order_item.purchase_code', '=', $params['purchase_code'])
                  ->groupBy('order_item.SKU')
                  ->orderBy('order_item.order_item_id')
                  ->get();
    
    foreach($fetch_cart as $cart){
      $price = (isset($cart->total_discount_price) && $cart->total_discount_price != 0) ? $cart->total_discount_price : $cart->total_price ;
      $message .= "
        <tr style='color:#444; line-height:18px; border-bottom:1px'>
          <td width='300' style='padding:15px;'>
            <span><b>" . stripslashes($cart->product_name) . "</b></span><br/>
            <span><b>" . $cart->brand_name . "</b></span><br/>
            <span>Warna: " . $cart->variant_color_name_custom . "</span><br/>
            <span>Ukuran: " . $cart->product_size . " </span>
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price, 0, ".", ".") . "
          </td>
          <td width='100' style='padding:15px; text-align:center;'>
            " . $cart->qty . "
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price*$cart->qty, 0, ".", ".") . "
          </td>
        </tr>
      ";
    }
    
    return $message;
  }

  public static function setMailMessageProductTcash($params)
  {
    $message    = "";
    $fetch_cart = DB::table('order_item')
                  ->select(\DB::raw('products.product_name, brand.brand_name, product_variant.variant_color_name_custom, product_variant.product_size, order_item.total_price, order_item.total_discount_price, order_item.quantity, COUNT(order_item.quantity) as qty'))
                  ->leftJoin('products', 'products.product_id', '=', 'order_item.product_id')
                  ->leftJoin('brand', 'products.product_brand', '=', 'brand.brand_id')
                  ->leftJoin('product_variant', 'order_item.SKU', '=', 'product_variant.SKU')
                  ->where('order_item.purchase_code', '=', $params['purchase_code'])
                  ->groupBy('order_item.SKU')
                  ->orderBy('order_item.order_item_id')
                  ->get();
    
    foreach($fetch_cart as $cart){
      $price = (isset($cart->total_discount_price) && $cart->total_discount_price != 0) ? $cart->total_discount_price : $cart->total_price ;
      $message .= "
        <tr style='color:#444; line-height:18px; border-bottom:1px'>
          <td width='300' style='padding:15px;'>
            <span><b>" . stripslashes($cart->product_name) . "</b></span><br/>
            <span><b>" . $cart->brand_name . "</b></span><br/>
            <span>Warna: " . $cart->variant_color_name_custom . "</span><br/>
            <span>Ukuran: " . $cart->product_size . " </span>
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price, 0, ".", ".") . "
          </td>
          <td width='100' style='padding:15px; text-align:center;'>
            " . $cart->qty . "
          </td>
          <td width='140' style='padding:15px; text-align:right;'>
            IDR " . number_format($price*$cart->qty, 0, ".", ".") . "
          </td>
        </tr>
      ";
    }
    
    return $message;
  }

  public static function insertTcashSignature ($data = []){
      $field = array(
        'po_number' => $data->po_number,
        'tcash_signature' => $data->pgpToken,
        'tcash_refnum' => $data->refNum
      );

      $insert_signature = DB::table('tcash_signature')->insertGetId($field);
      return $insert_signature;        
  }

}
