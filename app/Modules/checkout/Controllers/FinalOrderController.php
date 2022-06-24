<?php

namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;
use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\OrderDiscount;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\OrderPayment;
use \App\Modules\Checkout\Models\Banner;
use \App\Modules\Checkout\Models\OrderHeader;
use \App\Modules\Product\Models\Product;
use \App\Modules\Checkout\Models\Veritrans;
use \App\Modules\Checkout\Models\Kredivo;
use \App\Modules\Checkout\Models\Tcash;
use Cart;
use \App\Mailchimp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Session;
use Log;
use Auth;
use App\Jobs\OrderProcess;

class FinalOrderController extends Controller {

  /**
   * Display a listing of the resource.  
   *
   * @return Response
   */
  public function index(Request $request) {    

    $ByPassFinalOrder = false;  
    if($request->get('tr_id') != '' && $request->get('tr_status') != '' && $request->get('sign_key') != '' && $request->get('order_id') != ''){
        $kredivo = true;
        
        //trigger session if false;
        if(session('order_finished') == NULL){
            $ByPassFinalOrder = true;
            session()->put('order_finished', 1);    
        }        
    }else{
        $kredivo = false;
        
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/checkout/cart'));
        }
    }            
    
    $order_finished     = (session('order_finished')) ? session('order_finished') : NULL ;    
    if($order_finished == NULL){
      return redirect('checkout/cart');
    }    
    
    $kredivo_redirect  = (session('kredivo_redirect')) ? session('kredivo_redirect') : NULL ;
    
    $get_domain   = get_domain();
    $domain_id    = $get_domain['domain_id'];
    $channel      = $get_domain['channel'];
    $domain_name  = $get_domain['domain_name'];

    $data['title'] = "Final Order";

    $fetch_cart = CheckoutCart::fetchCart();

    //Fetch Order Session
    $order_session = Order::getOrderSession();

    $payment_method = $order_session['payment_method'];
    
    //by pass if kredivo parameters is available
    if(!isset($payment_method)){
        if($kredivo == true){
            $payment_method = 99;
        }
    }
    //End Fetch
    
    //Marketing Tag
    $tag_products = array();
    $index = 0;
    if($fetch_cart){
        foreach ($fetch_cart as $cart) {
            $tag_products[$index]["name"]           = $cart['name'];
            $tag_products[$index]["id"]             = $cart['product_id'];
            $tag_products[$index]["price"]          = $cart['subtotal'];
            $tag_products[$index]["brand"]          = $cart['brand_id'];
            $tag_products[$index]["brand-name"]     = $cart['brand_name'];
            //$tag_products[$index]["category"]     = $cart['type_id'];
            //additional for gtm       
            $tag_products[$index]['category']       = $cart['type_id_real'];
            $tag_products[$index]['category-name']  = $cart['type_id'];     

            $tag_products[$index]["variant"]        = $cart['color_name'];
            $tag_products[$index]["quantity"]       = $cart['qty'];
            $tag_products[$index]["type_url"]       = $cart['type_url'];
            $index++;
        }    
    }
    
    //End Marketing Tag
    
    //Fetch Purchase Code
    $purchase_code = NULL;
    if ($payment_method == 4 && $request->get('id')) {
        $veritrans_id = ($request->get('id')) ? $request->get('id') : NULL;
        if ($veritrans_id != NULL) {
        //Fetch Purchase Code
        $notifications_data = array();
        $notifications_data['veritrans_id'] = $veritrans_id;
        $fetch_veritrans_notification = Order::fetchVeritransNotification($notifications_data);
        if ($fetch_veritrans_notification) {
          $purchase_code = $fetch_veritrans_notification->orderId;
        }
        //End Fetch Purchase Code
        }
    }elseif($payment_method == 99 && $request->get('order_id')){ // KREDIVO
        $purchase_code = ($request->get('order_id')) ? $request->get('order_id') : NULL;
    }else{
        $purchase_code = ($request->get('po')) ? $request->get('po') : NULL;
    }
    
    if($purchase_code == NULL){
      return redirect('checkout/cart');
    }
    
    //Fetch Order Header
    $param_oh['purchase_code'] = $purchase_code;
    $fetch_order_header = OrderHeader::fetchOrderHeader($param_oh);

    if (!$fetch_order_header) {
      return redirect('checkout/cart');
    }
    //End Fetch Order Header
    
    //Fetch Discount
    $param_discount['purchase_code'] = $purchase_code;
    $get_discount = OrderDiscount::getDiscount($param_discount);
    //End Fetch Discount
    
    //Fetch Veritrans Notification
    $veritrans_payment_code         = NULL;
    $veritrans_redirect_url         = NULL;
    $veritrans_va_number            = NULL;
    $veritrans_permata_va_number    = NULL;
    
    $notifications_data = array();
    $notifications_data['orderId'] = $purchase_code;
    $fetch_veritrans_notification = Order::fetchVeritransNotification($notifications_data);
    if ($fetch_veritrans_notification) {        
      $veritrans_payment_code       = $fetch_veritrans_notification->payment_code;
      $veritrans_redirect_url       = $fetch_veritrans_notification->redirect_url;
      $veritrans_va_number          = isset($fetch_veritrans_notification->va_number) ? $fetch_veritrans_notification->va_number : "";      
    }
    //End Fetch Veritrans Notification
    
    //Check BCA KlikPay
    if ($payment_method == 4 && !isset($veritrans_id)) {
      if($veritrans_redirect_url == NULL){
        Session::flash('err_msg', 'Transaksi menggunakan BCA KlikPay gagal.');
        return redirect('checkout/submit_order');
      }else{
        return redirect()->away($veritrans_redirect_url);
      }
    }
    //End Check BCA KlikPay

    //Check GoPay
    if ($payment_method == 343 && !isset($veritrans_id)) {
      if($fetch_veritrans_notification->veritrans_post == NULL){
        Session::flash('err_msg', 'Transaksi menggunakan GoPay gagal.');
        return redirect('checkout/submit_order');
      }
    }
    //Check GoPay
    
    //CHECK KREDIVO STATUS
    if($kredivo_redirect == NULL){
        $kredivo_payment_status = 'pending';
        if($payment_method == 99){
            try{            
                $kredivoStatus  = Kredivo::StatusByPO($purchase_code);
                if($kredivoStatus){
                    $transk_status = json_decode(stripslashes($kredivoStatus->json_data), true);
                    if($transk_status['transaction_status'] && $transk_status['transaction_id'] == $request->get('tr_id')){
                        $kredivo_payment_status = $transk_status['transaction_status'];
                    }else{
                        return redirect('checkout/cart');
                    }                
                }
            } catch (Exception $ex) {
                Log::error($ex);
            }
        }                
    }else{
        $data['kredivo_redirect'] = $kredivo_redirect;
    }    
    
    //Check Veritrans Status from BCA KlikPay
    if(isset($veritrans_id) && $veritrans_id != NULL){
      $veritrans_data = [];
      $veritrans_data['domain_id']      = $domain_id;
      $veritrans_data['purchase_code']  = $purchase_code;
      $veritrans_data['veritrans_id']   = $veritrans_id;
      
      try {
        $status_veritrans = Veritrans::status($veritrans_data);
        
        if($status_veritrans->transaction_status == 'settlement'){
          //Update Order Payment, Order Item, and Order Item History if transaction status is settled            
          //Update Order Payment
          $update_op = array();
          $update_op['status']          = 1;
          $update_op['payment_status']  = $status_veritrans->transaction_status;

          OrderPayment::updateOrderPayment($purchase_code, $update_op);
          //End Update Order Payment

          //Update Order Item
          $filter_oi = array();
          $filter_oi['with']  = 'purchase_code';
          $filter_oi['value'] = $purchase_code;

          $update_oi = array();
          $update_oi['purchase_status']   = 3;
          $update_oi['order_status_item'] = 2;
          $update_oi['approval_date'] = isset($status_veritrans->transaction_time) ? $status_veritrans->transaction_time : null;

          OrderItem::updateOrderItem($filter_oi, $update_oi);
          //End Update Order Item

          //Update Order Item History => Disable karena sudah dilakukan di API
          //$update_oih = array();
          //$update_oih['order_status_item'] = 2;

          //OrderItem::updateOrderItemHistory($purchase_code, $update_oih);
          //End Update Order Item History
        }
      } catch(\Exception $e) {
        Log::error($e);
      }
    }
    //End Check Veritrans Status from BCA KlikPay
    /* - Update by Boan, Request By SAL
    if($fetch_cart){
        Self::sendMailChimp($fetch_cart, $fetch_order_header);      
    }*/

    // T-Cash Get Signature for redirect to webcheckout
    if($payment_method == 135){
      $get_signature = Tcash::createTcashSignature($purchase_code);
      $data['tcash_signature'] = (isset($get_signature->pgpToken) ? $get_signature->pgpToken : '');
      $data['tcash_webcheckout'] = env('T-CASH_WEBCHECKOUT');


      // $get_signature = Tcash::getTcashSignature($purchase_code);
      // $data['tcash_signature'] = (isset($get_signature['tcash_signature']) ? $get_signature['tcash_signature'] : '');
      // $data['tcash_webcheckout'] = env('T-CASH_WEBCHECKOUT');
    }
    

    //Send Mail to Customer
    if(Cart::count() > 0){
      //Set Email Banner
      $hbon_banner = "";
      $current_date = strtotime(date('Y-m-d H:i:s'));
      $hbon_date    = strtotime('2016-12-12 00:00:00');
      if(($payment_method == 5 || $payment_method == 20) && $hbon_date > $current_date){
        $hbon_banner = "http://im.onedeca.com/assets/banner/" . $domain_name . ".jpeg";
      }
      //End Set Email Banner
      
      $mail_data = array();
      $mail_data['purchase_code']           = $fetch_order_header->purchase_code;
      $mail_data['customer_fname']          = $fetch_order_header->customer_fname;
      $mail_data['customer_lname']          = $fetch_order_header->customer_lname;
      $mail_data['purchase_date']           = $fetch_order_header->purchase_date;
      $mail_data['grand_total']             = $fetch_order_header->grand_total;
      $mail_data['purchase_price']          = $fetch_order_header->purchase_price;
      $mail_data['paycode']                 = $fetch_order_header->paycode;
      $mail_data['shipping_finance']        = $fetch_order_header->shipping_finance;
      $mail_data['credit_use']              = $fetch_order_header->credit_use;
      $mail_data['order_shipping_address']  = $fetch_order_header->order_shipping_address;
      $mail_data['order_city']              = $fetch_order_header->order_city;
      $mail_data['order_province']          = $fetch_order_header->order_province;
      $mail_data['order_postcode']          = $fetch_order_header->order_postcode;
      $mail_data['order_phone']             = $fetch_order_header->order_phone;
      $mail_data['get_discount']            = $get_discount;
      $mail_data['payment_method']          = $payment_method;
      $mail_data['veritrans_payment_code']  = $veritrans_payment_code;
      $mail_data['veritrans_va_number']     = $veritrans_va_number;
      $mail_data['email_banner']            = $hbon_banner;
      $mail_data['kredivo_url']             = $kredivo_redirect;
      $mail_data['tcash_redirector']        = Tcash::build_domain_url($fetch_order_header->domain_id, $fetch_order_header->channel);
      $mail_data['gopay_url']               = url('/') . '/checkout/gopay/qrcode?po=' . $purchase_code;
      
      if($payment_method == 99){
            Order::sendMailKredivoURL($mail_data);
      }else if($payment_method == 135){
            Order::sendMailTcashURL($mail_data);
      }else if($payment_method == 343){
            Order::sendMailGopayURL($mail_data);
      }else{
            Order::sendMail($mail_data);    
      }      
    }else{
        if($payment_method != 99){ //kredivo can access without set cart
            return redirect('checkout/cart');    
        }        
    }
    //End Send Mail
    
    session()->forget('order_finished');    
    Cart::destroy();   
    
    //Thank you Banner
    $where_page_banner = array( 
      "ty_shipping_name" => $fetch_order_header->order_city,
      "ty_shipping_area" => $fetch_order_header->order_province,                                
    );
    $cacheName      = "ThankYou-Banner-".$domain_name."-".$channel;  
    $expiresAt      = Carbon::now()->addMinutes(60);    
    $fetch_banner   = Cache::remember($cacheName, $expiresAt, function() use($where_page_banner){                            
        return Banner::ThankYouBanner($where_page_banner);
    });      
    //End Thank you Banner
    
    //Fetch Tax
    $tax = OrderItem::where('purchase_code', '=', $fetch_order_header->purchase_code)->sum('tax');
    
    $data['domain_name']        = strtoupper($domain_name) . '.COM';
    $data['fetch_cart']         = $fetch_cart;
    $data['tag_products']       = $tag_products;
    $data['purchase_code']      = $purchase_code;
    if (getAppEnv() == 'production') {
      $data['purchase_date']      = date("Y-m-d", strtotime($fetch_order_header->purchase_date.' + 4 day'));
    } else {
      $data['purchase_date']      = date("Y-m-d", strtotime($fetch_order_header->purchase_date.' + 1 day'));
    }
    $data['tax']                = $tax ? $tax : 0;
    $data['grand_total']        = $fetch_order_header->grand_total;
    $data['shipping_finance']   = $fetch_order_header->shipping_finance;
    $data['city']               = $fetch_order_header->order_city;
    $data['province']           = $fetch_order_header->order_province;
    $data['payment_method']     = $payment_method;
    $data['redirect_url']       = $veritrans_redirect_url;
    $data['payment_code']       = $veritrans_payment_code;
    $data['va_number']          = $veritrans_va_number;    
    $data['thankyou_banner']    = $fetch_banner;
    
    //validate stamp message to final order    
    $grand_total                    = isset($fetch_order_header->grand_total) ? $fetch_order_header->grand_total : 0;
    $stamp_estimate                 = 0;
    $stamp_currency                 = OrderHeader::benkaStampCurrency()->config_value;    
    
    if(isset($stamp_currency) && $stamp_currency > 0 && $grand_total > $stamp_currency){        
        $stamp_estimate             = floor($fetch_order_header->grand_total / $stamp_currency);
        
        //send mail to info stamp
        if ($domain_id != 3) { //except shopdeca for now
            $mail_data['purchase_code']           = $fetch_order_header->purchase_code;
            $mail_data['customer_fname']          = $fetch_order_header->customer_fname;
            $mail_data['customer_lname']          = $fetch_order_header->customer_lname;
            $mail_data['stamp_currency']    = $stamp_currency;
            $mail_data['stamp_estimate']    = $stamp_estimate;
            $mail_data['stamp_type']        = 'PENDING';
            Order::sendMailBenkaStamp($mail_data);
        }
        //end send mail to info stamp
    }    
    
    $data['get_stamp_info']         = '';    
    $data['get_stamp_info_mobile']  = '';    
    if($grand_total >= $stamp_currency){        
        $data['get_stamp_info']         = '<h2 class="payment-info" style="margin-top:20px !important;">Anda akan mendapatkan pending Benka Stamp setelah item belanja Anda sampai ditangan Anda</h2>';    
        $data['get_stamp_info_mobile']  = '<h2 class="mt20">Anda akan mendapatkan pending Benka Stamp setelah item belanja Anda sampai ditangan Anda.</h2>';    
    }
    //end validate stamp message to final order 
        
    //kredivo status
    if($payment_method == 99 && $kredivo_redirect == NULL){
        $data['transaction_status'] = $kredivo_payment_status;        
    }else{
        $data['transaction_status'] = isset($status_veritrans->transaction_status) ? $status_veritrans->transaction_status : NULL ;          
    }  
    session()->forget('kredivo_redirect');
    
    $data['status_code']        = isset($status_veritrans->status_code) ? $status_veritrans->status_code : NULL ;
    $data['status_message']     = isset($status_veritrans->status_message) ? $status_veritrans->status_message : NULL ;

    // Jika Gopay tampilkan halaman scan QRCode
    if ($payment_method == 343) {
      return redirect('checkout/gopay/qrcode?po=' . $purchase_code);
    }  

    return get_view('checkout', 'checkout.final-order', $data);
  }

  //Function
  public function sendMailChimp($fetch_cart, $fetch_order_header) {
    try {
      $items_chimp = [];
      foreach ($fetch_cart as $cart) {
        //Fetch Type
        $data_fetch_type['product_id'] = $cart['product_id'];
        $fetch_type = Product::fetchProductOldType($data_fetch_type);
        //End Fetch Type

        $items_chimp[] = array(
            "line_num" => NULL,
            "product_id" => $cart['product_id'],
            "sku" => $cart['SKU'],
            "product_name" => $cart['name'],
            "category_id" => (isset($fetch_type->type_id)) ? $fetch_type->type_id : 0,
            "category_name" => (isset($fetch_type->type_name)) ? $fetch_type->type_name : 0,
            "qty" => $cart['qty'],
            "cost" => $cart['subtotal']
        );
      }

      $params_chimp["purchase_codes"] = $fetch_order_header->purchase_code;
      $params_chimp["customer_email"] = $fetch_order_header->customer_email;
      $params_chimp["chimp_grand_total"] = $fetch_order_header->grand_total;
      $params_chimp["chimp_purchase_date"] = $fetch_order_header->purchase_date;
      $params_chimp["chimp_shipping_cost"] = $fetch_order_header->shipping_cost;
      $params_chimp["items"] = $items_chimp;

      Log::info('Mailchimp: Parameter Data');
      Log::Info(json_encode($params_chimp));
      Log::info('=========================');

      $send_mailchimp = Mailchimp::order_add($params_chimp);   

      Log::info('Result Mailchimp : '. $send_mailchimp);
    } catch (\Exception $e) {
      
    }
  }

  //End Function
  //JSON Function
  //End JSON Function
}
