<?php 

namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;
use \App\Modules\Checkout\Models\Payment;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\OrderHeader;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\OrderDiscount;
use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Checkout\Models\PromotionTemplate;
use \App\Modules\Checkout\Models\PromotionCondition;
use \App\Modules\Checkout\Models\PromotionHelper;
use \App\Modules\Checkout\Models\Veritrans;
use \App\Modules\Checkout\Models\MasterPaymentLog;
use \App\Modules\Checkout\Models\Kredivo;
use \App\Modules\Checkout\Models\Tcash;
use \App\Customer;
use Auth;
use Cart;
use Log;
use DB;
use Illuminate\Http\Request;
// use Redis;
use Illuminate\Support\Facades\Redis;
use Session;
use \App\Jobs\OrderProcess;

class SubmitOrderController extends Controller {
  
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    $get_domain     = get_domain();
    $domain_id      = $get_domain['domain_id'];
    $domain_alias   = $get_domain['domain_alias'];
    $channel        = $get_domain['channel'];
    $is_desktop     = ($channel == 1 || $channel == 3 || $channel == 5) ? TRUE : FALSE ;

    //Check Authentication
    if (!Auth::check()) {
      return redirect('login/?continue='.urlencode('/checkout/cart'));
    }
    //End Check Authentication
    
    //Check Access Token
    if(!Customer::validateAccessToken()){
      Cart::destroy(); 
      Auth::logout();

      Session::forget('auth_cs');
      Session::forget('email_auth_cs');
      
      return redirect('/login?continue='. urlencode('/checkout/cart'))->with('login_error', 'Silahkan Login kembali')->withInput();
    }
    //End Check Access Token

    // check status customer aktif atau tidak
    if(!Customer::validateCustomerStatus()) {
      Cart::destroy(); 
      Auth::logout();

      Session::forget('auth_cs');
      Session::forget('email_auth_cs');
      
      return redirect('/login')->with('login_error', 'Akun anda telah dinonaktifkan')->withInput();
    }
    // end check status customer
    
    //Fetch Customer
    $customer_id = Auth::user()->customer_id;
    $customer = Customer::where('customer_id', '=', $customer_id)->first();
    if($customer){
      $customer_credit = $customer->customer_credit;
    }
    //End Fetch Customer

    //Check Cart is empty
    if(!Self::checkCart()){
      return redirect('checkout/cart');
    }
    //End Check Cart
    
    //Set Order Item
    $add_draft_order = OrderItem::addDraftOrder(); //recreate order_item from cart
    if($add_draft_order['status_inventory'] == FALSE){ //redirect to cart if one of cart item inventory is unsufficient
      return redirect('checkout/cart');
    }
    //End Set Order Item
    
    //Clear Order Session
    Order::clearOrderSession($is_desktop);

    //Get Primary Address
    $param_primary_address['get_primary'] = TRUE;
    $get_customer_address = Customer::getCustomerAddress($param_primary_address);
    if(!empty($get_customer_address)){
      session()->forget('customer_address');
      $customer_address = array();
      foreach ($get_customer_address as $row){
        if($row->address_type == 1){ //Shipping
          $customer_address['shipping']['address_id']       = $row->address_id;
          $customer_address['shipping']['address_street']   = $row->address_street;
          $customer_address['shipping']['address_city']     = $row->address_city;
          $customer_address['shipping']['address_province'] = $row->address_province;
          $customer_address['shipping']['address_phone']    = $row->address_phone;
          $customer_address['shipping']['address_postcode'] = $row->address_postcode;
          
          //Check COD
          $params_check_cod['shipping_cod']   = 1;
          $params_check_cod['shipping_area']  = $row->address_province;
          $params_check_cod['shipping_name']  = $row->address_city;
          $orderby                            = "shipping_area";
          $check_cod = Shipping::getShippingList($params_check_cod, $orderby);
          //End Check COD

          //Check Popup Store
          $params_check_popup_store['shipping_name'] = $row->address_city;
          $get_popup_store = Shipping::getPopupStore($params_check_popup_store);
          //End Check Popup Store
        }else{ //Billing
          $customer_address['billing']['address_id']        = $row->address_id;
          $customer_address['billing']['address_street']    = $row->address_street;
          $customer_address['billing']['address_city']      = $row->address_city;
          $customer_address['billing']['address_province']  = $row->address_province;
          $customer_address['billing']['address_phone']     = $row->address_phone;
          $customer_address['billing']['address_postcode']  = $row->address_postcode;
        }
      }
    }
    //End Get Primary Address

    //Get Province
    $params_province['shipping_type'] = 1;
    $params_province['type']          = 2;
    $orderby                          = "shipping_area";
    $list_province = Shipping::getShippingList($params_province, $orderby);
    //End Get Province

    // Get payment method by group
    $list_payment_method_virtual_account    = Payment::fetchPaymentMethodVirtualAccount();
    $list_payment_method_bank_transfer      = Payment::fetchPaymentMethodBankTransfer();
    $list_payment_method_kartu_kredit       = Payment::fetchPaymentMethodKartuKredit();
    $list_payment_method_internet_banking   = Payment::fetchPaymentMethodInternetBanking();    
    $list_payment_method_others             = Payment::fetchPaymentMethodOthers();

    //Get Payment Method
    $list_payment_method = Payment::fetchPaymentMethod();
    
    //Revise Payment Method
    $status_bca_payment = env('STATUS_BCA_PAYMENT', TRUE);
    $server_name        = \Request::server('SERVER_NAME');
    $is_debug = false;
    if(strpos($server_name, 'debug') !== false || strpos($server_name, '.biz') !== false){
      $is_debug = true;
    }
    
    foreach($list_payment_method_bank_transfer as $key => $payment_method){
      if(!$status_bca_payment && $payment_method->master_payment_id == 4){ //BCA KlikPay
        unset($list_payment_method_bank_transfer[$key]);
      }
      
      //if(!$is_debug && $payment_method->master_payment_id == 28){ //BCA Virtual Account
      //  unset($list_payment_method[$key]);
      //}
    }
    foreach($list_payment_method_virtual_account as $key => $payment_method){
      if(!$status_bca_payment && $payment_method->master_payment_id == 4){ //BCA KlikPay
        unset($list_payment_method_virtual_account[$key]);
      }
      
      //if(!$is_debug && $payment_method->master_payment_id == 28){ //BCA Virtual Account
      //  unset($list_payment_method[$key]);
      //}
    }
    foreach($list_payment_method_internet_banking as $key => $payment_method){
      if(!$status_bca_payment && $payment_method->master_payment_id == 4){ //BCA KlikPay
        unset($list_payment_method_internet_banking[$key]);
      }
      
      //if(!$is_debug && $payment_method->master_payment_id == 28){ //BCA Virtual Account
      //  unset($list_payment_method[$key]);
      //}
    }
    foreach($list_payment_method_kartu_kredit as $key => $payment_method){
      if(!$status_bca_payment && $payment_method->master_payment_id == 4){ //BCA KlikPay
        unset($list_payment_method_kartu_kredit[$key]);
      }
      
      //if(!$is_debug && $payment_method->master_payment_id == 28){ //BCA Virtual Account
      //  unset($list_payment_method[$key]);
      //}
    }
    foreach($list_payment_method_others as $key => $payment_method){
      if(!$status_bca_payment && $payment_method->master_payment_id == 4){ //BCA KlikPay
        unset($list_payment_method_others[$key]);
      }
      
      //if(!$is_debug && $payment_method->master_payment_id == 28){ //BCA Virtual Account
      //  unset($list_payment_method[$key]);
      //}
    }
    //End Check BCA KlikPay

    //Get Shipping Method
    $list_shipping_method = Shipping::getShippingMethod() ;
    if($list_shipping_method){
      $shipping_id = $list_shipping_method[0]['id'];
    }else{
      $shipping_id = NULL;
      
      if(!empty($get_customer_address)){ //if primary address is all empty, then shipping province and city is error
        $province = isset($customer_address['shipping']['address_province']) ? $customer_address['shipping']['address_province'] : "" ;
        $city     = isset($customer_address['shipping']['address_city']) ? $customer_address['shipping']['address_city'] : "" ;
        
        if($province != "" && $city != ""){
          Log::alert('Shipping ID is NULL on Customer : ' . $customer->customer_email . '. Province = ' . $customer_address['shipping']['address_province'] . ' | City = ' . $customer_address['shipping']['address_city'] . '');
        }
        
        session()->put('err_msg', 'Provinsi dan Kota alamat pengiriman anda salah atau kosong. Klik <a href="/user/setting/">DISINI</a> atau klik Ubah Alamat dibawah untuk memperbaiki/menambah alamat.');
      }
    }
    //End Get Shipping Method

    //Set Order Session
    $today = getdate();
    $selected_day = array();
    array_push($selected_day,$today['wday']);
    
    session()->put('customer_address', (isset($customer_address)) ? $customer_address : NULL );
    session()->put('shipping_id', $shipping_id);
    session()->put('shipping_type', 1);
    session()->put('gender', Customer::getCustomerGender());
    session()->put('platform_domain', $domain_id);
    session()->put('platform_device', ($is_desktop) ? 1 : 2 );
    session()->put('selected_day', $selected_day);
    
    session()->put('is_not_idle', 1); //Set session to prevent session lost if customer is idle
    //End Set Session Order Session
    
    //Clear Order Process
    Order::removeOrderProcess($customer->customer_email);
    
    //Check Transaction Queuing
    $queuing                = env('TRANSACTION_QUEUE', false);
    $transaction_queuing    = $queuing ? 1 : 0;
    $queuing_periodic_time  = 400; //Periodic time to check queuing process in ms
    $max_queuing_trying     = 16; //Define how many queuing check process is allowed
    //End Check Transaction Queuing
    
    //Fetch Marketing Cart Data
    $marketing_data = CheckoutCart::getMarketingCart();
    
    //Veritrans Key
    $veritrans['client_key']  = \Config::get('berrybenka.veritrans.VERITRANS_CLIENT_KEY_' . strtoupper($domain_alias)) ;
    $veritrans['js']          = env('VERITRANS_JS', 'https://api.sandbox.veritrans.co.id/v2/token');

    $data['title']                    = "Checkout";
    $data['customer_credit']          = $customer_credit;
    $data['get_customer_address']     = (isset($get_customer_address)) ? $get_customer_address : NULL ;
    $data['is_cod_available']         = (isset($check_cod) && !empty($check_cod)) ? TRUE : FALSE ;
    $data['is_popup_store_available'] = (isset($get_popup_store) && !empty($get_popup_store)) ? $get_popup_store : array() ;
    $data['list_province']            = (isset($list_province) && !empty($list_province)) ? $list_province : array() ;

    $data['list_payment_method_bank_transfer']      = (isset($list_payment_method_bank_transfer) && !empty($list_payment_method_bank_transfer)) ? $list_payment_method_bank_transfer : array() ;
    $data['list_payment_method_virtual_account']    = (isset($list_payment_method_virtual_account) && !empty($list_payment_method_virtual_account)) ? $list_payment_method_virtual_account : array() ;
    $data['list_payment_method_internet_banking'] = (isset($list_payment_method_internet_banking) && !empty($list_payment_method_internet_banking)) ? $list_payment_method_internet_banking : array() ;
    $data['list_payment_method_kartu_kredit']       = (isset($list_payment_method_kartu_kredit) && !empty($list_payment_method_kartu_kredit)) ? $list_payment_method_kartu_kredit : array() ;
    $data['list_payment_method_others']       = (isset($list_payment_method_others) && !empty($list_payment_method_others)) ? $list_payment_method_others : array() ;
    
    $data['list_payment_method']                    = (isset($list_payment_method) && !empty($list_payment_method)) ? $list_payment_method : array() ;
    $data['list_shipping_method']     = (isset($list_shipping_method) && !empty($list_shipping_method)) ? $list_shipping_method : array() ;
    $data['fetch_cart']               = CheckoutCart::fetchCart();
    
    if(!$is_desktop){
      Self::processMobile();
        
      $data['freegift_auto']          = (session('freegift_auto')) ? session('freegift_auto') : array() ;
      $data['voucher']                = (session('voucher')) ? session('voucher') : array() ;
      $data['freegift']               = (session('freegift')) ? session('freegift') : array() ;
      $data['benka_point']            = (session('benka_point')) ? session('benka_point') : array() ;
      $data['allow_benka_point']      = PromotionHelper::checkBenkaPoint() ? 1 : 0;
      $data['promotions_eksklusif']   = (session('promotions_eksklusif')) ? session('promotions_eksklusif') : 0 ; //0: no eksklusif | 1: freegift auto eksklusif | 2: voucher eksklusif | 3: freegift eksklusif
      
      $data['bin_number_mandiri_raw'] = (session('bin_number_mandiri_raw')) ? session('bin_number_mandiri_raw') : NULL ;
      $data['bin_name_mandiri']       = (session('bin_name_mandiri')) ? session('bin_name_mandiri') : NULL ;
      $data['bin_month_mandiri']      = (session('bin_month_mandiri')) ? session('bin_month_mandiri') : NULL ;
      $data['bin_year_mandiri']       = (session('bin_year_mandiri')) ? session('bin_year_mandiri') : NULL ;
      $data['bin_cvv_mandiri']        = (session('bin_cvv_mandiri')) ? session('bin_cvv_mandiri') : NULL ;
      
      $data['bin_number_raw']         = (session('bin_number_raw')) ? session('bin_number_raw') : NULL ;
      $data['bin_name']               = (session('bin_name')) ? session('bin_name') : NULL ;
      $data['bin_month']              = (session('bin_month')) ? session('bin_month') : NULL ;
      $data['bin_year']               = (session('bin_year')) ? session('bin_year') : NULL ;
      $data['bin_cvv']                = (session('bin_cvv')) ? session('bin_cvv') : NULL ;
      $data['bank_name']              = (session('bank_name')) ? session('bank_name') : NULL ;
    }
    
    $data['total']                    = Payment::calculateGrandTotal();
    $data['transaction_queuing']      = $transaction_queuing;
    $data['queuing_periodic_time']    = $queuing_periodic_time;
    $data['max_queuing_trying']       = $max_queuing_trying;
    $data['marketing_data']           = $marketing_data;
    $data['veritrans']                = $veritrans;
    $data['err_msg']                  = (session('err_msg')) ? session('err_msg') : NULL ;
    
    session()->forget('err_msg');
    
    return get_view('checkout', 'checkout.submit-order', $data);
	}

  //Function
  public function checkCart()
  {
    //Check Total Item in Cart
    $total_cart = Cart::count();
    if($total_cart <= 0){
      return false;
    }
    //End Check

    //Check Inventory in Cart
    $cart = Cart::content();
    foreach ($cart as $row){
      $param_check['SKU'] = $row->id;
      $param_check['quantity'] = $row->qty;
      $check_inventory = CheckoutCart::checkInventory($param_check);
      if(!$check_inventory['result']){
        return false;
      }
    }
    //End Check

    return true;
  }
  
  public function processMobile()
  {
    $bin_number_raw = (session('bin_number_raw')) ? session('bin_number_raw') : NULL;
    $bin_name       = (session('bin_name')) ? session('bin_name') : NULL;
    $bin_month      = (session('bin_month')) ? session('bin_month') : NULL;
    $bin_year       = (session('bin_year')) ? session('bin_year') : NULL;
    $bin_cvv        = (session('bin_cvv')) ? session('bin_cvv') : NULL;
    $voucher_code   = (session('voucher_code')) ? session('voucher_code') : NULL;
    $benka_point    = (session('benka_point')) ? session('benka_point') : NULL ;

    Self::redeemFreegiftAuto();
    
    if($voucher_code != NULL){
      Self::redeemVoucher($voucher_code);
    }
    
    if($bin_number_raw != NULL){
      if(strlen($bin_number_raw) >= 6){
        $bin_number = (strlen($bin_number_raw) >= 9) ? substr($bin_number_raw, 0, 9) : substr($bin_number_raw, 0, 6);

        $apply_bin_number = Self::applyBinNumber($bin_number);
        if(strlen($bin_number_raw) >= 9 && empty($apply_bin_number['freegift'])){
          $bin_number       = substr($bin_number_raw, 0, 6);
          $apply_bin_number = Self::applyBinNumber($bin_number);
        }
      }
      
      $payment_method = session('payment_method');
      if($payment_method == 20){
        session()->put('bin_number_mandiri_raw', $bin_number_raw);
        session()->put('bin_name_mandiri', $bin_name);
        session()->put('bin_month_mandiri', $bin_month);
        session()->put('bin_year_mandiri', $bin_year);
        session()->put('bin_cvv_mandiri', $bin_cvv);
        
        session()->forget('bin_number_raw');
        session()->forget('bin_name');
        session()->forget('bin_month');
        session()->forget('bin_year');
        session()->forget('bin_cvv');
      }
    }
    
    if($benka_point != NULL){
      Self::redeemBenkaPoint($benka_point);
    }
  }
  
  public function redeemFreegiftAuto()
  {
    session()->forget('freeshipping_promotions');
    
    $mode = 'freegift_auto';
    $customer           = array();
    $validated_freegift = array();
    
    //Set Order Item ID Applied
    $fetch_order_item   = OrderItem::fetchOrderItem();
    $order_item_applied = [];
    foreach ($fetch_order_item as $order_item) {
      $order_item_applied[] = $order_item->order_item_id;
    }
    //End Set Order Item ID Applied
    
    $get_promotion_freegift = PromotionTemplate::getFreegiftAuto();
    if ($get_promotion_freegift){
      session()->forget('order_item_applied'); //session to store order_item_id that applied on freegift auto
      foreach ($get_promotion_freegift as $key => $value) {
        session()->forget('need_bank_id');
        
        $get_template_condition         = Promotion::getPromotionsTemplateCondition($get_promotion_freegift[$key]);
        $validate_promotion_condition   = PromotionTemplate::validatePromotionCondition($get_template_condition, $customer, $order_item_applied, $mode);
        
        if ($validate_promotion_condition) {
          $get_promotion_freegift[$key]['order_id']             = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          $get_promotion_freegift[$key]['need_bank_id']         = (session('need_bank_id')) ? session('need_bank_id') : false ;
          $apply_promotions_freegift                            = PromotionTemplate::applyPromotion($get_promotion_freegift[$key], $customer);
          array_push($validated_freegift, $apply_promotions_freegift);
        }
      }
    }
    
    return $validated_freegift;
  }
  
  public function redeemFreegift($bank_id = NULL)
  {
    session()->forget('freegift');
    session()->forget('allow_benka_point');
    session()->forget('freeshipping_promotions');
    
    $mode = 'freegift';
    $customer = array();
    $validated_freegift = array();
    $attributes['bank_id'] = $bank_id;
    
    //Set Order Item ID Applied
    $fetch_order_item   = OrderItem::fetchOrderItem();
    $order_item_applied = [];
    foreach ($fetch_order_item as $order_item) {
      $order_item_applied[] = $order_item->order_item_id;
    }
    //End Set Order Item ID Applied
    
    $get_promotion_freegift = PromotionTemplate::getFreegift($attributes);

    if ($get_promotion_freegift){
      session()->forget('order_item_applied'); //session to store order_item_id that applied on freegift
      foreach ($get_promotion_freegift as $key => $value) {
        session()->forget('need_bank_id');
        
        $get_template_condition         = Promotion::getPromotionsTemplateCondition($get_promotion_freegift[$key]);
        $validate_promotion_condition   = PromotionTemplate::validatePromotionCondition($get_template_condition, $customer, $order_item_applied, $mode);
        
        if ($validate_promotion_condition) {
          $get_promotion_freegift[$key]['order_id']             = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          $get_promotion_freegift[$key]['need_bank_id']         = (session('need_bank_id')) ? session('need_bank_id') : false ;
          $apply_promotions_freegift                            = PromotionTemplate::applyPromotion($get_promotion_freegift[$key], $customer);

          array_push($validated_freegift, $apply_promotions_freegift);
        }
      }
    }

    return (isset($validated_freegift) && !empty($validated_freegift)) ? session('freegift') : array() ;
  }
  
  public function redeemVoucher( $voucher_code = NULL )
  {
    session()->forget('voucher');
    session()->forget('allow_benka_point');
    session()->forget('freeshipping_promotions');
    session()->forget('temp_err_msg');
    
    $mode = 'voucher';
    $customer = array();
    $attributes['promotions_code_number'] = $voucher_code;
    
    //Set Order Item ID Applied
    $fetch_order_item   = OrderItem::fetchOrderItem();
    $order_item_applied = [];
    foreach ($fetch_order_item as $order_item) {
      $order_item_applied[] = $order_item->order_item_id;
    }
    //End Set Order Item ID Applied
    
    $get_promotion_voucher = PromotionTemplate::getVoucher($attributes);

    if ($get_promotion_voucher){
      session()->forget('order_item_applied'); //session to store order_item_id that applied on voucher
      
      $get_template_condition       = Promotion::getPromotionsTemplateCondition($get_promotion_voucher);
      $validate_promotion_condition = PromotionTemplate::validatePromotionCondition($get_template_condition, $customer, $order_item_applied, $mode, $voucher_code);

      if ($validate_promotion_condition) {
        $get_promotion_voucher['order_id']  = (session('order_item_applied')) ? session('order_item_applied') : array() ;
        $apply_voucher                      = PromotionTemplate::applyPromotion($get_promotion_voucher, $customer);
      } else {
        Session::flash('err_msg', session('temp_err_msg') ? session('temp_err_msg') : 'Voucher tidak memenuhi persyaratan, mohon hubungi CS kami.');
      }
    }else{
      Session::flash('err_msg', session('temp_err_msg') ? session('temp_err_msg') : 'Voucher tidak terdaftar.');
    }
    
    //Reapply Freegift
    session()->forget('freegift');
    Self::redeemFreegift(session('bank_id'));
    //End Reapply Freegift
    
    return (isset($apply_voucher)) ? session('voucher') : array() ;
  }
  
  public function redeemBenkaPoint($benka_point = NULL)
  {
    session()->forget('benka_point');
    
    if (!Auth::check()) {
      return redirect('login/?continue='.urlencode('/checkout/cart'));
    }
    
    if($benka_point == NULL){
      Session::flash('err_msg', 'Benka Point harus diisi.');
      return false;
    }
    
    if(!is_numeric($benka_point)){
      Session::flash('err_msg', 'Benka Point harus berupa angka.');
      return false;
    }
    
    $customer_id = Auth::user()->customer_id;
    $customer = Customer::where('customer_id', '=', $customer_id)->first();
    
    if(!$customer){
      Session::flash('err_msg', 'Mohon maaf keranjang belanja Anda tidak bisa diproses. Silakan tunggu beberapa saat lalu coba kembali atau gunakan metode pembayaran lainnya.');
      return false;
    }
    
    $customer_credit = $customer->customer_credit;
    if($benka_point > $customer_credit){
      Session::flash('err_msg', 'Benka Point anda tidak mencukupi.');
      return false;
    }
    
    $total = Payment::calculateGrandTotal();
    if($benka_point > $total['grand_total_before_paycode']){
      Session::flash('err_msg', 'Benka Point anda melebihi subtotal.');
      return false;
    }
    
    session()->put('benka_point', $benka_point);
    return true;
  }
  
  public function applyVoucher(Request $request)
  {
    $voucher_code = $request->get('voucher_code');
    session()->put('voucher_code', $voucher_code);
    
    return redirect('checkout/submit_order');
  }
  
  public function applyBenkaPoint(Request $request)
  {
    $benka_point = $request->get('benka_point');
    session()->put('benka_point', $benka_point);
    
    return redirect('checkout/submit_order');
  }
  
  public function applyBankPromo(Request $request)
  {
    $bin_number_raw = $request->get('bin_number');
    $bin_name       = $request->get('bin_name');
    $bin_month      = $request->get('month_exp');
    $bin_year       = $request->get('year_exp');
    $bin_cvv        = $request->get('cvv');
    
    session()->put('bin_number_raw', $bin_number_raw);
    session()->put('bin_name', $bin_name);
    session()->put('bin_month', $bin_month);
    session()->put('bin_year', $bin_year);
    session()->put('bin_cvv', $bin_cvv);
    
    return redirect('checkout/submit_order');
  }
  
  public function applyBinNumber($bin_number)
  {
    $freegift = [];
    
    //Fetch Bin Number
    $param_bank_promo = [];
    $param_bank_promo['bin_number'] = $bin_number;
    $fetch_bin_number = Payment::fetchBinNumber($param_bank_promo);
    //End Fetch Bin Number

    if(!empty($fetch_bin_number)){
      //Bin Number is found
      $bank_name  = $fetch_bin_number[0]->bank;
      $bank_id    = $fetch_bin_number[0]->bank_id;

      // IF BANK IS MANDIRI DEBIT THEN WE HAVE TO CHANGE THE PAYMENT METHOD SESSION INTO MANDIRI DEBIT
      $payment_method = ($bank_id == 19) ? 20 : session('payment_method');

      //Set Order Session
      session()->put('payment_method', $payment_method);
      session()->put('bank_id', $bank_id);
      session()->put('bank_name', $bank_name);
      //End Set Session Order Session

      $freegift = Self::redeemFreegift($bank_id);
    }
    
    $data['freegift']   = $freegift;
    $data['bin_number'] = $fetch_bin_number;
    
    return $data;
  }
  
  public function insertOrderProcess(Request $request)
  {
    if(!Auth::check()) {
      return redirect('login/?continue='.urlencode('/checkout/cart'));
    }
    
    if(!Customer::validateAccessToken()){
      Cart::destroy(); 
      Auth::logout();

      Session::forget('auth_cs');
      Session::forget('email_auth_cs');
      
      return redirect('/login?continue='. urlencode('/checkout/cart'))->with('login_error', 'Ada yang menggunakan account anda')->withInput();
    }
    $customer_email = Auth::user()->customer_email;
    Order::removeOrderProcess($customer_email);
    
    $param_data['request']  = $request;
    $data_submit_order      = Order::setDataSubmitOrder($param_data);

    //Run process submit order normally
    $submit_order = Order::processSubmitOrder($data_submit_order);

    $result       = (!$submit_order || !$submit_order['result']) ? false : true ;

    //Veritrans Process
    $veritrans              = isset($submit_order['veritrans']) ? $submit_order['veritrans'] : NULL ;
    $veritrans_result_code  = isset($veritrans['code']) ? $veritrans['code'] : NULL ;
    $veritrans_result_flag  = isset($veritrans['flag']) ? $veritrans['flag'] : NULL ;
    $log_vn                 = isset($veritrans['log_vn']) ? $veritrans['log_vn'] : array() ;
    $log_vv                 = isset($veritrans['log_vv']) ? $veritrans['log_vv'] : array() ;

    if($veritrans != NULL){
      $veritrans_order_id = (isset($veritrans['order_id'])) ? $veritrans['order_id'] : NULL;
      
      if($veritrans_result_code == 400){
        //Change bank acquire if result code 400 and flag 2
        if($veritrans_result_flag == 2){
          Log::notice('Veritrans Acquiring Bank is changed');
          Payment::updateBankAcquire($data_submit_order);
        }
        
        if($veritrans_order_id != NULL){
          Log::notice('Process Cancel Veritrans caused by 400');
          $veritrans_data['domain_id']    = isset($data_submit_order['domain_id']) ? $data_submit_order['domain_id'] : NULL ;
          $veritrans_data['veritrans_id'] = $veritrans_order_id;
          Veritrans::cancelVeritrans($veritrans_data);
        }
      }else if($veritrans_result_code != 200 && $veritrans_result_code != 201 && $veritrans_result_code != 202){
        if($veritrans_order_id != NULL){
          Log::notice('Process Cancel Veritrans caused by else 200 / 201');
          
          $veritrans_data['domain_id']    = isset($data_submit_order['domain_id']) ? $data_submit_order['domain_id'] : NULL ;
          $veritrans_data['veritrans_id'] = $veritrans_order_id;
          Veritrans::cancelVeritrans($veritrans_data);
        }
      }

      if(!empty($log_vn)){ //Insert veritrans notification
        Order::createVeritransNotifications($log_vn);
      }
      
      if(!empty($log_vv)){ //Insert veritrans verifications
        Order::createVeritransVerifications($log_vv);
      }
    }
    //End Veritrans Process
    
    //START Kredivo Process
    $kredivo        = isset($submit_order['kredivo']) ? $submit_order['kredivo'] : NULL ; 
    $logKRequests   = isset($kredivo['requests']) ? $kredivo['requests'] : [] ;         
    $logKResponse   = isset($kredivo['responses']) ? $kredivo['responses'] : [] ;         
    
    if($kredivo){
        $kredivoResult  = json_decode($kredivo['result']);
        $kPurchaseCode  = $kredivo['purchase_code'];
        if (!empty($logKRequests)) { //Insert Kredivo Requests
            Order::createKredivoRequests($logKRequests);
        }

        if (!empty($logKResponse)) { //Insert Kredivo Responses
            Order::createKredivoResponses($logKResponse);
        }
        
        //update order payment kredivo url
        $kredivo_redirect = NULL;      
        if(isset($kredivoResult->redirect_url) && isset($kPurchaseCode)){
            //forget session type            
            session()->forget('kredivo_type');
            
            $updateOrderPayment['purchase_code'] = $kPurchaseCode;
            $updateOrderPayment['kredivo_url'] = $kredivoResult->redirect_url;
            $updateOP = Order::updateOrderPaymentKredivoUrl($updateOrderPayment);
            if($updateOP){
                $kredivo_redirect = $kredivoResult->redirect_url;    
            }            
        }else{
            $kredivo_message['err_msg'] = 'Mohon maaf keranjang belanja Anda tidak bisa diproses. Silakan tunggu beberapa saat lalu coba kembali atau gunakan metode pembayaran lainnya.';
            session()->put('err_msg', $kredivo_message['err_msg']);
            return redirect('checkout/submit_order');
        }                
    }
    
    //END Kredivo Process
    
    // Start T-Cash Process
    // if(isset($submit_order['payment_method'])){
    //   if($submit_order['payment_method'] == 135){
    //     $request_token = Tcash::generate_token($data_submit_order, $submit_order);
    //     if($request_token == false){
    //       session()->put('err_msg', 'Tidak dapat terhubung ke server T-Cash, Silakan ulangi beberapa saat lagi.');
    //       return redirect('checkout/submit_order');
    //     }
    //     if($request_token != false){
    //       // insert t-cash signature
    //       Tcash::insertTcashSignature($request_token);
    //     }else{
    //       session()->put('err_msg', 'Tidak dapat terhubung ke server T-Cash, Silakan ulangi beberapa saat lagi.');
    //       return redirect('checkout/submit_order');
    //     }
    //   }
    // }
    
    if($result){
      session()->put('order_finished', 1);
      $purchase_code = (isset($submit_order['create_order_header']['purchase_code'])) ? $submit_order['create_order_header']['purchase_code'] : '' ;
      
        //redirect if kredivo url set
        if(isset($kredivo_redirect) && isset($purchase_code)){                       
            session()->put('kredivo_redirect', $kredivo_redirect);
            return redirect('checkout/final_order/?po='.$purchase_code);
        }
      return redirect('checkout/final_order/?po='.$purchase_code);
    }else{
      if(isset($submit_order['false_qty']) && $submit_order['false_qty']){
        //Redirect to Cart if one of order item is out of stock
        return redirect('checkout/cart');
      }else{
        session()->put('err_msg', $submit_order['err_msg']);
        return redirect('checkout/submit_order');
      }
    }
  }
  
  public function checkAJAX()
  {
    if(!Auth::check() || (\Session::token() != \Request::header('X-CSRF-Token'))) { 
      return false;
    }
    
    if(!Customer::validateAccessToken()){
      return false;
    }
    
    $fetch_order_item = OrderItem::fetchOrderItem();
    if(empty($fetch_order_item)){
      return false;
    }
    
    return true;
  }
  
  public function updateMasterPayment(Request $request)
  {
    $master_payment_id  = ($request->get('id')) ? $request->get('id') : ''; //if empty, process update will only applied to midtrans 
    $mode               = ($request->get('mode')) ? $request->get('mode') : 'disable'; //mode : disable/revert
    $user               = ($request->get('user')) ? $request->get('user') : 'SYSTEM';
    
    $list_updated_payment = [3, 4, 5, 20, 24, 28, 85]; // 3: KlikBCA, 4: BCA KlikPay, 5: Visa/Mastercard, 20: Mandiri Debit, 24: Indomaret, 28: BCA Virtual Account, 85: Permata Virtual Account
    if($master_payment_id != ''){
      $list_updated_payment = [$master_payment_id];
    }
    
    //Check Payment Method
    $param_payment = [];
    $param_payment['master_payment_enabled']  = 1;
    
    if($master_payment_id != ''){
      $param_payment['master_payment_id'] = $master_payment_id;
    }
    
    $list_payment_method = Payment::where($param_payment)->get();

    if($list_payment_method->count() <= 0){
      $json = [];
      $json['result']   = true;
      $json['mode']     = $mode;
      $json['message']  = 'Payment Method is/are not found';

      echo json_encode($json);exit();
    }
    //End Check Payment Method
    
    $param_payment_log = [];
    $param_payment_log['status']  = 1;
    
    if($master_payment_id != ''){
      $param_payment_log['master_payment_id'] = $master_payment_id;
    }
    
    DB::beginTransaction();
    
    if($mode == 'disable'){ /* Disable Master Payment */
      //Get Payment Method Log
      $list_payment_method_log = MasterPaymentLog::where($param_payment_log)->get();
      
      if($list_payment_method_log->count() > 0){
        $json = [];
        $json['result']   = false;
        $json['mode']     = $mode;
        $json['message']  = 'Selected Payment is/are already disabled';
        
        echo json_encode($json);exit();
      }
      
      //Reset All Status to 0
      $update = MasterPaymentLog::where($param_payment_log)->update(['status' => 0]);

      foreach($list_payment_method as $key => $payment_method){
        if(in_array($payment_method->master_payment_id, $list_updated_payment)){
          //Save to Log
          $payment_log = new MasterPaymentLog;

          $payment_log->master_payment_id = $payment_method->master_payment_id;
          $payment_log->enabled_bb        = $payment_method->enabled_bb;
          $payment_log->enabled_hb        = $payment_method->enabled_hb;
          $payment_log->enabled_sd        = $payment_method->enabled_sd;
          $payment_log->status            = 1;
          $payment_log->created_at        = date('Y-m-d H:i:s');
          $payment_log->created_by        = $user;

          $log_save = $payment_log->save();
          
          if(!$log_save){
            DB::rollBack();

            $json = [];
            $json['result'] = false;
            $json['mode']     = $mode;
            $json['message']  = 'Insert to master_payment_log is failed';
            
            echo json_encode($json);exit();
          }
          //End Save to Log
          
          //Update Payment
          $update_data = [];
          $update_data['enabled_bb'] = 0;
          $update_data['enabled_hb'] = 0;
          $update_data['enabled_sd'] = 0;

          $update_payment = Payment::where('master_payment_id', '=', $payment_method->master_payment_id)
            ->update($update_data);
          //End Update Payment
        }
      }
      
    }else{ /* Revert Veritrans Payment */
      //Get Payment Method Log
      $list_payment_method_log = MasterPaymentLog::where($param_payment_log)->get();
      
      if($list_payment_method_log->count() <= 0){
        $json = [];
        $json['result']   = true;
        $json['mode']     = $mode;
        $json['message']  = 'All Payment Method Log status is 0';
        
        echo json_encode($json);exit();
      }
      
      foreach($list_payment_method_log as $key => $payment_method_log){
        //Update Payment
        $update_data = [];
        $update_data['enabled_bb'] = $payment_method_log->enabled_bb;
        $update_data['enabled_hb'] = $payment_method_log->enabled_hb;
        $update_data['enabled_sd'] = $payment_method_log->enabled_sd;
        
        $update_payment = Payment::where('master_payment_id', '=', $payment_method_log->master_payment_id)
          ->update($update_data);
        //End Update Payment
      }
      
      //Reset All Log Status to 0
      $update = MasterPaymentLog::where($param_payment_log)->update(['status' => 0]);
      
      if(!$update){
        DB::rollBack();

        $json = [];
        $json['result'] = false;
        $json['mode']     = $mode;
        $json['message']  = 'Update status master_payment_log to 0 is failed';

        echo json_encode($json);exit();
      }
    }
    
    DB::commit();
    
    $json = [];
    $json['result']   = true;
    $json['mode']     = $mode;
    $json['message']  = 'Process Success';

    echo json_encode($json);exit();
  }
  //End Function

  //JSON Function
  public function jsonGetCustomerAddress(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $params['address_id']   = $request->get('address_id');
    $params['get_primary']  = $request->get('get_primary');
    $params['address_type'] = $request->get('address_type');

    $get_customer_address = Customer::getCustomerAddress($params);

    //Set Session Address
    if($params['get_primary'] && !empty($get_customer_address)){ //if get_primary is TRUE, set session customer_address
      session()->forget('customer_address');
      $customer_address = array();
      foreach ($get_customer_address as $row){
        if($row->address_type == 1){ //Shipping Address
          $customer_address['shipping']['address_id']       = $row->address_id;
          $customer_address['shipping']['address_street']   = $row->address_street;
          $customer_address['shipping']['address_city']     = $row->address_city;
          $customer_address['shipping']['address_province'] = $row->address_province;
          $customer_address['shipping']['address_phone']    = $row->address_phone;
          $customer_address['shipping']['address_postcode'] = $row->address_postcode;

          //Check COD
          $params_check_cod['shipping_cod']   = 1;
          $params_check_cod['shipping_area']  = $row->address_province;
          $params_check_cod['shipping_name']  = $row->address_city;
          $orderby                            = "shipping_area";
          $check_cod = Shipping::getShippingList($params_check_cod, $orderby);
          //End Check COD

          //Check Popup Store
          $params_check_popup_store['shipping_name'] = $row->address_city;
          $params_check_popup_store['shipping_area'] = $row->address_province;
          $get_popup_store = Shipping::getPopupStore($params_check_popup_store);
          //End Check Popup Store

          //Set Order Session
          session()->put('shipping_city', $row->address_city);
        }else{ //Billing Address
          $customer_address['billing']['address_id']        = $row->address_id;
          $customer_address['billing']['address_street']    = $row->address_street;
          $customer_address['billing']['address_city']      = $row->address_city;
          $customer_address['billing']['address_province']  = $row->address_province;
          $customer_address['billing']['address_phone']     = $row->address_phone;
          $customer_address['billing']['address_postcode']  = $row->address_postcode;
        }
      }

      //Set Shipping Method
      session()->forget('shipping_type');
      $list_shipping_method = Shipping::getShippingMethod() ;
      if($list_shipping_method){
        $shipping_id = $list_shipping_method[0]['id'];
      }else{
        $shipping_id = NULL;
      }
      //End Set Shipping Method
      
      //Set Order Session
      session()->put('customer_address', ($customer_address) ? $customer_address : NULL );
      session()->put('shipping_id', $shipping_id);
      session()->put('shipping_type', 1);
      session()->forget('payment_method');
      //End Set Session Order Session
    }
    //End Set Session Address
    
    $json['have_address']             = (!empty($get_customer_address)) ? TRUE : FALSE ;
    $json['list_customer_address']    = (isset($get_customer_address)) ? $get_customer_address : NULL ;
    $json['list_shipping_method']     = (isset($list_shipping_method)) ? $list_shipping_method : NULL ;
    $json['is_cod_available']         = (isset($check_cod) && !empty($check_cod)) ? TRUE : FALSE ;
    $json['is_popup_store_available'] = (isset($get_popup_store) && !empty($get_popup_store)) ? $get_popup_store : array() ;
    $json['total']                    = Payment::calculateGrandTotal();
    
    return json_encode($json);
  }

  public function jsonNewCustomerAddress(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $params['multi_address'] = $request->get('multi_address');

    //Shipping Address
    $params['shipping_street']    = $request->get('shipping_street');
    $params['shipping_province']  = $request->get('shipping_province');
    $params['shipping_city']      = $request->get('shipping_city');
    $params['shipping_postcode']  = $request->get('shipping_postcode');
    $params['shipping_phone']     = $request->get('shipping_phone');

    //Billing Address
    if($params['multi_address'] == 1){
      $params['billing_street']   = $request->get('shipping_street');
      $params['billing_province'] = $request->get('shipping_province');
      $params['billing_city']     = $request->get('shipping_city');
      $params['billing_postcode'] = $request->get('shipping_postcode');
      $params['billing_phone']    = $request->get('shipping_phone');
    }else{
      $params['billing_street']   = $request->get('billing_street');
      $params['billing_province'] = $request->get('billing_province');
      $params['billing_city']     = $request->get('billing_city');
      $params['billing_postcode'] = $request->get('billing_postcode');
      $params['billing_phone']    = $request->get('billing_phone');
    }
    
    //Check Address Validity
    $check_params['type']           = 1;
    $check_params['shipping_type']  = 1;
    $check_params['shipping_area']  = $request->get('shipping_province');
    $check_params['shipping_name']  = $request->get('shipping_city');

    $check_shipping = Shipping::getShippingList($check_params);
    
    if (count($check_shipping) <= 0) {
      $json['result']         = false;
      $json['result_message'] = 'Data alamat anda salah';
      return json_encode($json);
    }
    //End Check Address Validity

    $new_customer_address = Customer::newCustomerAddress($params);

    $json['result']         = $new_customer_address['result'];
    $json['result_message'] = $new_customer_address['result_message'];

    return json_encode($json);
  }

  public function jsonAddCustomerAddress(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $params['address_type']     = $request->get('address_type');
    $params['address_street']   = $request->get('address_street');
    $params['address_province'] = $request->get('address_province');
    $params['address_city']     = $request->get('address_city');
    $params['address_postcode'] = $request->get('address_postcode');
    $params['address_phone']    = $request->get('address_phone');
    
    //Check Address Validity
    $check_params['type']           = 1;
    $check_params['shipping_type']  = 1;
    $check_params['shipping_area']  = $request->get('address_province');
    $check_params['shipping_name']  = $request->get('address_city');

    $check_shipping = Shipping::getShippingList($check_params);
    
    if (count($check_shipping) <= 0) {
      $json['result']         = false;
      $json['result_message'] = 'Data alamat anda salah';
      return json_encode($json);
    }
    //End Check Address Validity

    $add_customer_address = Customer::addCustomerAddress($params);

    $json['id']             = $add_customer_address['id'];
    $json['result']         = $add_customer_address['result'];
    $json['result_message'] = $add_customer_address['result_message'];

    return json_encode($json);
  }

  public function jsonEditCustomerAddress(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $params['address_id']       = $request->get('address_id');
    $params['address_street']   = $request->get('address_street');
    $params['address_province'] = $request->get('address_province');
    $params['address_city']     = $request->get('address_city');
    $params['address_postcode'] = $request->get('address_postcode');
    $params['address_phone']    = $request->get('address_phone');
    
    //Check Address Validity
    $check_params['type']           = 1;
    $check_params['shipping_type']  = 1;
    $check_params['shipping_area']  = $request->get('address_province');
    $check_params['shipping_name']  = $request->get('address_city');

    $check_shipping = Shipping::getShippingList($check_params);
    
    if (count($check_shipping) <= 0) {
      $json['result']         = false;
      $json['result_message'] = 'Data alamat anda salah';
      return json_encode($json);
    }
    //End Check Address Validity

    $edit_customer_address = Customer::editCustomerAddress($params);

    $json['result'] = $edit_customer_address['result'];
    $json['result_message'] = $edit_customer_address['result_message'];

    return json_encode($json);
  }

  public function jsonSetPrimaryAddress(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $params['address_id']   = $request->get('address_id');
    $params['address_type'] = $request->get('address_type');

    $set_primary_address = Customer::setPrimaryAddress($params);
    
    //Set Session Shipping ID
    $list_shipping_method = Shipping::getShippingMethod() ;
    if($list_shipping_method){
      $shipping_id = $list_shipping_method[0]['id'];
    }else{
      $shipping_id = NULL;
    }
    
    session()->put('shipping_id', $shipping_id);
    //End Set Session Shipping ID
    
    //Reapply Freegift Auto
    session()->forget('freegift_auto');
    Self::redeemFreegiftAuto();
    //End Reapply Freegift Auto
    
    //Reapply Voucher
    session()->forget('voucher');
    $voucher_code = (session()->has('voucher_code')) ? session('voucher_code') : NULL;
    if($voucher_code != NULL){
      Self::redeemVoucher($voucher_code);
    }
    //End Reapply Voucher
    
    //Reapply Freegift
    $bank_id = (session()->has('bank_id')) ? session('bank_id') : NULL;
    if($bank_id != NULL){
      Self::redeemFreegift($bank_id);
    }
    //End Reapply Freegift

    $json['result']         = $set_primary_address;
    $json['freegift_auto']  = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $json['voucher']        = (session('voucher')) ? session('voucher') : array() ;
    $json['freegift']       = (session('freegift')) ? session('freegift') : array() ;

    return json_encode($json);
  }

  public function jsonGetShippingList(Request $request)
  {
    $params['type'] = $request->get('type');
    $params['shipping_type'] = 1;
    $params['shipping_area'] = $request->get('shipping_area');
    $orderby                 = "shipping_name";

    $get_shipping = Shipping::getShippingList($params, $orderby);

    $json['list_shipping'] = $get_shipping;

    return json_encode($json);
  }

  public function jsonSetShippingMethod(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $shipping_type  = $request->get('shipping_type');
    $shipping_id    = $request->get('shipping_id');

    //Set Order Session
    session()->put('shipping_type', $shipping_type);
    session()->put('shipping_id', $shipping_id);
    //End Set Session Order Session

    $json['total'] = Payment::calculateGrandTotal();

    return json_encode($json);
  }

  public function jsonSetPaymentMethod(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      
      return json_encode($json);
    }
    
    $payment_method = $request->get('payment_method');

    //Set Order Session
    session()->put('payment_method', $payment_method);
    //End Set Session Order Session
    
    //Reset Shipping Type
    session()->put('shipping_type', 1);
    
    $list_shipping_method = Shipping::getShippingMethod() ;
    if($list_shipping_method){
      $shipping_id = $list_shipping_method[0]['id'];
    }else{
      $shipping_id = NULL;
    }
    session()->put('shipping_id', $shipping_id);
    //End Reset Shipping Type
    
    //Reset Bank Promo
    session()->forget('bank_id');
    session()->forget('freegift');
    //End Reset Bank Promo
    
    //Reapply Freegift Auto
    session()->forget('freegift_auto');
    Self::redeemFreegiftAuto();
    //End Reapply Freegift Auto
    
    //Reapply Voucher
    session()->forget('voucher');
    $voucher_code = (session()->has('voucher_code')) ? session('voucher_code') : NULL;
    if($voucher_code != NULL){
      Self::redeemVoucher($voucher_code);
    }
    //End Reapply Voucher
    
    $json['total']                = Payment::calculateGrandTotal();
    $json['freegift_auto']        = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $json['voucher']              = (session('voucher')) ? session('voucher') : array() ;
    $json['list_shipping_method'] = (isset($list_shipping_method)) ? $list_shipping_method : NULL ;    

    return json_encode($json);
  }

  public function jsonGetBankPromo(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    session()->forget('bank_id');
    session()->forget('freegift');
    session()->forget('bin_number');
    
    $bin_number_raw = $request->get('bin_number');
    
    if(strlen($bin_number_raw) >= 6){

      session()->put('bin_number', $bin_number_raw);

      $bin_number = (strlen($bin_number_raw) >= 9) ? substr($bin_number_raw, 0, 9) : substr($bin_number_raw, 0, 6);
      
      $apply_bin_number = Self::applyBinNumber($bin_number);
      if(strlen($bin_number_raw) >= 9 && empty($apply_bin_number['freegift'])){
        $bin_number       = substr($bin_number_raw, 0, 6);
        $apply_bin_number = Self::applyBinNumber($bin_number);
        
        $validated_freegift = $apply_bin_number['freegift'];
      }else{
        $validated_freegift = $apply_bin_number['freegift'];
      }
    }
    
    $bank_name  = isset($apply_bin_number['bin_number'][0]->bank) ? $apply_bin_number['bin_number'][0]->bank : NULL;
    $bank_id    = isset($apply_bin_number['bin_number'][0]->bank_id) ? $apply_bin_number['bin_number'][0]->bank_id : NULL;
    
    //Reapply Voucher
    session()->forget('voucher');
    $voucher_code = (session()->has('voucher_code')) ? session('voucher_code') : NULL;
    if($voucher_code != NULL){
      Self::redeemVoucher($voucher_code);
    }
    //End Reapply Voucher
    
    //Reapply Freegift Auto
    session()->forget('freegift_auto');
    Self::redeemFreegiftAuto();
    //End Reapply Freegift Auto
    
    $json['bank_name']          = (isset($bank_name)) ? $bank_name : NULL ;
    $json['bank_id']            = (isset($bank_id)) ? $bank_id : NULL ;
    $json['result']             = (isset($validated_freegift) && !empty($validated_freegift)) ? true : false ;
    $json['freegift_auto']      = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $json['voucher']            = (session('voucher')) ? session('voucher') : array() ;
    $json['freegift']           = (session('freegift')) ? session('freegift') : array() ;
    $json['allow_benka_point']  = PromotionHelper::checkBenkaPoint() ? 1 : 0;
    $json['total']              = Payment::calculateGrandTotal();

    return json_encode($json);
  }
  
  public function jsonApplyFreegiftAuto()
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    session()->forget('freegift_auto');
    $validated_freegift = Self::redeemFreegiftAuto();
    
    $json['result']               = (isset($validated_freegift) && !empty($validated_freegift)) ? true : false ;
    $json['freegift_auto']        = (session('freegift_auto')) ? session('freegift_auto') : array() ;
    $json['allow_benka_point']    = PromotionHelper::checkBenkaPoint() ? 1 : 0;
    $json['total']                = Payment::calculateGrandTotal();

    return json_encode($json);
  }

  public function jsonApplyVoucher(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $voucher_code   = $request->get('voucher_code');
    session()->put('voucher_code', $voucher_code);
    
    $apply_voucher  = Self::redeemVoucher($voucher_code);
    $error_voucher  = (session('err_msg')) ? session('err_msg') : NULL ;
    
    //Reapply Freegift Auto
    session()->forget('freegift_auto');
    Self::redeemFreegiftAuto();
    //End Reapply Freegift Auto

    $json['result']                     = (isset($apply_voucher) && !empty($apply_voucher)) ? true : false ;
    $json['error_msg']                  = (isset($error_voucher)) ? $error_voucher : 'Kondisi Voucher tidak valid.' ;
    $json['freegift_auto']              = session('freegift_auto');
    $json['freegift']                   = session('freegift');
    $json['voucher']                    = session('voucher');
    $json['allow_benka_point']          = PromotionHelper::checkBenkaPoint() ? 1 : 0;
    $json['total']                      = Payment::calculateGrandTotal();

    return json_encode($json);
  }
  
  public function jsonApplyBenkaPoint(Request $request)
  {
    if(!Self::checkAJAX()) {
      $json['result']       = false;
      $json['need_refresh'] = true;
      return json_encode($json);
    }
    
    $benka_point        = $request->get('benka_point');
    $apply_benka_point  = Self::redeemBenkaPoint($benka_point);
    $error_benka_point  = (session('err_msg')) ? session('err_msg') : NULL ;
    
    $json['result']     = ($apply_benka_point) ? true : false ;
    $json['error_msg']  = (isset($error_benka_point)) ? $error_benka_point : NULL ;
    $json['total']      = Payment::calculateGrandTotal();
    
    return json_encode($json);
  }
  
  public function jsonCheckOrderProcess()
  {
    $result = true;
    
    if(!Auth::check()) {
      $result = false;
    }
    
    if($result){
      $data['customer_email'] = Auth::user()->customer_email;
      $result = Order::fetchOrderProcess($data);
    }
    
    $json['result'] = $result;
    
    return json_encode($json);
  }
  
  public function jsonClearOrderProcess()
  {
    $result   = true;
    
    if(!Self::checkAJAX()) {
      $result = false;
    }
    
    if($result){
      $customer_email = Auth::user()->customer_email;
      Order::removeOrderProcess($customer_email);
      Log::error('######### Queuing failed - Transaction is now processed without queuing system. ##########');
    }
    
    $json['result'] = $result;
    
    return json_encode($json);
  }
  
  public function jsonInsertOrderProcess(Request $request)
  {
    $result   = true;
    $queuing  = env('TRANSACTION_QUEUE', false);
    
    if(!Self::checkAJAX()) {
      $result = false;
    }
    
    if($result){
      $customer_email = Auth::user()->customer_email;
      
      //Clear Order Process
      Order::removeOrderProcess($customer_email);
      
      $result = Order::insertOrderProcess($customer_email);
    }
    
    if($result){
      $param_data['request']  = $request;
      $data_submit_order      = Order::setDataSubmitOrder($param_data);
      if($queuing){ 
        //Dispatch process submit order to queuing system
        $job = (new OrderProcess($data_submit_order));
        $this->dispatch($job);
      }else{ 
        $result = false;
        $err_msg = "Terjadi masalah synchronize data queuing.";
      }
    }
    
    $json['queuing']        = $queuing;
    $json['result']         = $result;
    $json['err_msg']        = (isset($err_msg)) ? $err_msg : '' ;
    
    return json_encode($json);
  }
  
    /*
     * Eff
     * Get Payment List Kredivo
     */
    public function jsonListPaymentKredivo(Request $request){
        $result = [];
        $filterCart = [];
        $paymentCart = [];
        
        //check ajax + auth
        if (!Self::checkAJAX() || !Auth::check()) {
            $result = false;
        }        
            
        $cartCollect        = collect(CheckoutCart::fetchCart());                
        $filterCart         = $cartCollect->map(function ($row) {
            return collect($row)
                ->only(['SKU', 'name', 'price', 'url', 'type_url', 'qty'])
                ->all();
        });
        
        if(!empty($filterCart)){
            $payments = Payment::calculateGrandTotal();
            $amount = 0;
            if($payments){
                $amount = $payments['grand_total'];
            }
            
            //parameters to kredivo request 
            $RequestParams['items'] = $filterCart->toArray();    
            $RequestParams['total'] = $amount;    
            $responseKredivo = Kredivo::PaymentType($RequestParams);
            if($responseKredivo){
                //set session type                                
                if (session('kredivo_type') != NULL) {
                    $putValue = collect(json_decode($responseKredivo));
                    $responseKredivo = json_encode($putValue->put('selected_value', session('kredivo_type')));
                }
                                                               
                $result = stripslashes($responseKredivo);                
            }
        }                        

        return response()->json($result);
    }
    
    public function jsonSessionKredivoMobile(Request $request){          
        $result = true;
        if ($request->get('kredivo_type')) {
            session()->put('kredivo_type', $request->get('kredivo_type'));            
        }
        
        return response()->json($result);
    }
  
    //End JSON Function
    public function testDb() {
        if (\DB::connection()->getDatabaseName()) {
            echo "conncted sucessfully to database " . \DB::connection()->getDatabaseName();
        } else {
            echo "mateeeeeee coy??????";
        }
    }
}
