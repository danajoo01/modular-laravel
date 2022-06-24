<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\Product;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Checkout\Models\PromotionHelper;
use Auth;
use Cart;
use DB;

class Payment extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'master_payment'; //Define your table name

	protected $primaryKey = 'master_payment_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['primarykey']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Checkout\Models\Model_name');
	// }

  public static function fetchBinNumber($params = NULL)
  {
    $bin_number = (isset($params['bin_number'])) ? $params['bin_number'] : NULL ;

    DB::enableQueryLog();
    $fetch_bin_number = DB::table('bin_number')
      ->select(\DB::raw('*'));

    if($bin_number != NULL){
      $fetch_bin_number->where('bin_number', '=', $bin_number);
    }

    return $fetch_bin_number->take(1)->get();
  }

  public static function fetchPaymentMethod()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('*'))
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }

    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }
  
  public static function getBankAcquire()
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];

    $get_bank_acquire = DB::table('bank_acquire')
      ->select(DB::raw('name'));
    
    if($domain_id == 1){
      $get_bank_acquire->where('status_bb', '=', 1);
    }elseif($domain_id == 2){
      $get_bank_acquire->where('status_hb', '=', 1);
    }else{
      $get_bank_acquire->where('status_sd', '=', 1);
    }
    
    $bank_acquire = $get_bank_acquire->take(1)->value('name');
    return count($bank_acquire) ? $bank_acquire : 'BNI' ;
  }
  
  public static function updateBankAcquire(array $data)
  {
    $customer_id    = (isset($data['customer_id'])) ? $data['customer_id'] : NULL ;
    $domain_id      = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $acquiring_bank = Self::getBankAcquire();
    $updated_bank   = ($acquiring_bank == 'BNI') ? 'CIMB' : 'BNI' ;
    
    if($domain_id == 1){
      //Disable Acquiring Bank
      $reset_item = [];
      $reset_item['status_bb']                = 0;
      $reset_item['last_modified_cust_date']  = date('Y-m-d H:i:s');
      $reset_item['last_modified_cust_id']    = $customer_id;
      DB::table('bank_acquire')
        ->where('name', $acquiring_bank)
      ->update($reset_item);
      //End Disable

      //Enable updated bank
      $update_item = [];
      $update_item['status_bb']               = 1;
      $update_item['last_modified_cust_date'] = date('Y-m-d H:i:s');
      $update_item['last_modified_cust_id']   = $customer_id;
      DB::table('bank_acquire')
        ->where('name', $updated_bank)
      ->update($update_item);
      //End Enable
    }
    
    return true;
  }

  public static function setPaycode()
  {
    $payment_method = (session('payment_method')) ? session('payment_method') : NULL;
    if($payment_method == 1 || $payment_method == 2 || $payment_method == 29 || $payment_method == 30){
      $paycode = (session('paycode')) ? session('paycode') : rand(100, 300) ;
      session()->put('paycode', $paycode);
    }else{
      $paycode = 0;
      session()->forget('paycode');
    }

    return $paycode;
  }
  
  public static function processFreegiftAuto()
  {
    $total_freegift_value = 0;
    
    $freegift = session('freegift_auto');
    if(!empty($freegift)){
      foreach($freegift as $key => $values){        
        $total_freegift_value += $freegift[$key]['promotions_value'];
        
        //Check Freeshipping
        if($freegift[$key]['promotions_mode'] == 4){
          session()->put('freeshipping_promotions', 1);
        }
        //End Check Freeshipping
      }
    }
    
    return $total_freegift_value;
  }
  
  public static function processFreegift()
  {
    $total_freegift_value = 0;
    
    $freegift = session('freegift');
    if(!empty($freegift)){
      foreach($freegift as $key => $values){        
        $total_freegift_value += $freegift[$key]['promotions_value'];
        
        //Check Freeshipping
        if($freegift[$key]['promotions_mode'] == 4){
          session()->put('freeshipping_promotions', 1);
        }
        //End Check Freeshipping
      }
    }
    
    return $total_freegift_value;
  }
  
  public static function processVoucher()
  {
    $total_voucher_value = 0;
    
    $voucher = session('voucher');
    if(!empty($voucher)){
      $total_voucher_value += $voucher['promotions_value'] ;
      
      //Check Freeshipping
      if($voucher['promotions_mode'] == 4){
        session()->put('freeshipping_promotions', 1);
      }
      //End Check Freeshipping
    }
    
    return $total_voucher_value;
  }

  public static function calculateGrandTotal($fetch_order_item = array())
  {
    $subtotal = 0;
    
    //Calculate Subtotal
    if(empty($fetch_order_item)){
      $fetch_order_item = OrderItem::fetchOrderItem();
    }
    
    $count_order_item = count($fetch_order_item);
    if($count_order_item > 0){
      foreach ($fetch_order_item as $order_item) {
        $price    = set_price($order_item->each_price, $order_item->discount_price);
        $subtotal += $price * $order_item->quantity;
      }
    }else{
      return false;
    }
    
    $base_subtotal = $subtotal;
    //End Calculate Subtotal
    
    //Promotions
    $total_freegift_auto_value  = Self::processFreegiftAuto();
    $total_voucher_value        = Self::processVoucher();
    $total_freegift_value       = Self::processFreegift();
    //End Promotions
    
    $grand_total_before_freegift_auto = $subtotal;
    $subtotal                         -= $total_freegift_auto_value;
    $grand_total_before_voucher       = $subtotal;
    $subtotal                         -= $total_voucher_value;
    $grand_total_before_freegift      = $subtotal;
    $subtotal                         -= $total_freegift_value;
    //End Calculate Order Item
    
    //Calculate Benka Point
    $grand_total_before_benka_point   = $subtotal;
    $benka_point = 0;
    if(PromotionHelper::checkBenkaPoint()){
      $benka_point = (session('benka_point')) ? session('benka_point') : 0 ;
    }
    
    $subtotal -= $benka_point;
    //End Calculate Benka Point

    //Calculate Shipping Cost
    $grand_total_before_shipping  = $subtotal;
    $shipping_type                = (session('shipping_type')) ? session('shipping_type') : 1 ; //Default Shipping Method is Regular
    
    $is_freeshipping            = (Shipping::getStatusFreeshipping($grand_total_before_benka_point)) ? TRUE : FALSE ;
    $is_freeshipping_promotions = (session('freeshipping_promotions')) ? TRUE : FALSE ;
    
    $data['shipping_type']      = $shipping_type;
    $shipping_cost              = Shipping::getShippingCost($data);
    $shipping_cost_raw          = $shipping_cost;
    
    if(($is_freeshipping || $is_freeshipping_promotions) && $shipping_type == 1){ //Set Shipping Cost to 0 if freeshipping and shipping type is not same day / next day delivery
      $shipping_cost = 0;
    }
    
    $subtotal += $shipping_cost;
    //End Calculate Shipping Cost
    
    //Sync Benka Point
    if($subtotal < 0 && $benka_point > 0){ //Resync benka point if subtotal is minus
      $subtotal += $benka_point;
      
      $benka_point = abs($subtotal);
      session()->put('benka_point', $benka_point);
      
      $subtotal -= $benka_point;
    }
    //End Sync Benka Point
    
    //Calculate Paycode
    $grand_total_before_paycode  = $subtotal;
    
    $paycode  = Payment::setPaycode();
    $subtotal += $paycode;
    //End Calculate Paycode
    
    $data['base_subtotal']                        = $base_subtotal;
    $data['total_freegift_auto_value']            = $total_freegift_auto_value;
    $data['grand_total_before_freegift_auto']     = $grand_total_before_freegift_auto;
    $data['total_voucher_value']                  = $total_voucher_value;
    $data['grand_total_before_voucher']           = $grand_total_before_voucher;
    $data['total_freegift_value']                 = $total_freegift_value;
    $data['grand_total_before_freegift']          = $grand_total_before_freegift;
    $data['shipping_cost']                        = $shipping_cost;
    $data['shipping_cost_raw']                    = $shipping_cost_raw;
    $data['is_freeshipping']                      = $is_freeshipping;
    $data['is_freeshipping_promotions']           = $is_freeshipping_promotions;
    $data['grand_total_before_shipping']          = $grand_total_before_shipping;
    $data['benka_point']                          = $benka_point;
    $data['grand_total_before_benka_point']       = $grand_total_before_benka_point;
    $data['paycode']                              = $paycode;
    $data['grand_total_before_paycode']           = $grand_total_before_paycode;
    $data['payment_method']                       = (session('payment_method')) ? session('payment_method') : NULL;
    $data['grand_total']                          = $subtotal;
    
    return $data;
  }

  /* Payment method grouping
  *  1 = Bank Transfer 
  */
  public static function fetchPaymentMethodBankTransfer()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('*'))
      ->where('master_payment_group', '=', 1)
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }

    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }

  /* Payment method grouping
  *  2 = Virtual Account 
  */
  public static function fetchPaymentMethodVirtualAccount()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('*'))
      ->where('master_payment_group', '=', 2)
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }
    
    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }

  /* Payment method grouping
  *  3 = Pembayaran Instan 
  */
  public static function fetchPaymentMethodInternetBanking()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('*'))
      ->where('master_payment_group', '=', 3)
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }
    
    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }

  /* Payment method grouping
  *  4 = Kartu Kredit 
  */
  public static function fetchPaymentMethodKartuKredit()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('*'))
      ->where('master_payment_group', '=', 4)
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }
    
    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }

  public static function fetch_bank($bank_id){
    DB::enableQueryLog();
    DB::setFetchMode(\PDO::FETCH_ASSOC);
    $fetch_bank = DB::table('bank')
      ->select(\DB::raw('bank_id'))
      ->whereIn('bank_id', $bank_id)
      ->where('status', '=', 1)->get();
    DB::setFetchMode(\PDO::FETCH_CLASS);

    return $fetch_bank;
  }

  /* Payment method grouping
  *  5 = Others
  */
  public static function fetchPaymentMethodOthers()
  {
    $get_domain                 = get_domain();
    $domain_id                  = $get_domain['domain_id'];
    $BlockedPaymentProduction   = \Config::get('berrybenka.berrybenka_block_payment_id'); //return array

    DB::enableQueryLog();
    $fetch_payment_method = DB::table('master_payment')
      ->select(\DB::raw('master_payment.master_payment_id,master_payment.master_payment_name,master_payment.master_payment_description,master_payment.master_payment_image,master_payment.master_payment_type_transfer,master_payment.maximum_grand_total,master_payment.minimum_grand_total,master_payment.master_payment_enabled,master_payment.master_payment_affiliate,master_payment.enabled_bb,master_payment.enabled_hb,master_payment.enabled_sd,master_payment.channel_active,master_payment.show_order,master_payment.master_payment_group,location_store.header_name,location_store.enddate'))
      ->leftJoin('location_store', 'location_store.master_payment_id', '=', 'master_payment.master_payment_id')
      ->where('master_payment_group', '=', 5)
      ->where('master_payment_type_transfer', '!=', 3);

    if($domain_id == 1){
      $fetch_payment_method->where('master_payment.enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $fetch_payment_method->where('master_payment.enabled_hb', '=', 1);
    }else{
      $fetch_payment_method->where('master_payment.enabled_sd', '=', 1);
    }
    
    if(!empty($BlockedPaymentProduction)){ // production exclude debug.
        $server_name    = \Request::server('SERVER_NAME');
        $domainLiveList = [
            1 => 'www.berrybenka.com',
            2 => 'berrybenka.com',
            3 => 'm.berrybenka.com',
            4 => 'www.hijabenka.com',
            5 => 'hijabenka.com',
            6 => 'm.hijabenka.com',
            7 => 'www.shopdeca.com',
            8 => 'shopdeca.com',
            9 => 'm.shopdeca.com'
        ];  
        if(!empty(array_search($server_name, $domainLiveList))){
            \Log::alert('List blocked Payment production ' . json_encode($BlockedPaymentProduction) );
            $fetch_payment_method->whereNotIn('master_payment.master_payment_id', $BlockedPaymentProduction);    
        }        
    }
    
    $fetch_payment_method->orderBy('show_order', 'ASC');

    return $fetch_payment_method->get();
  }

}
