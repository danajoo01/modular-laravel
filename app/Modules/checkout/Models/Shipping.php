<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\Product;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\OrderItem;
use Auth;
use \App\Customer;
use Cart;
use DB;
use DateTime;
use Log;
use App;

class Shipping extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'shipping'; //Define your table name

	protected $primaryKey = 'shipping_id'; //Define your primarykey

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

  public static function getShippingList($params, $order = "shipping_type"){
    $type = (isset($params['type'])) ? $params['type'] : 1 ; //1: City | 2: Province
    $shipping_type = (isset($params['shipping_type'])) ? $params['shipping_type'] : NULL ; //1: Regular | 2: Economy
    $shipping_area = (isset($params['shipping_area'])) ? $params['shipping_area'] : NULL ;
    $shipping_name = (isset($params['shipping_name'])) ? $params['shipping_name'] : NULL ;
    $shipping_cod = (isset($params['shipping_cod'])) ? $params['shipping_cod'] : NULL ;

    $get_shipping = DB::table('shipping')
      ->select(DB::raw('*'))
      ->where('enabled', '=', 1);

    if($type == 2){
      $get_shipping->groupBy('shipping_area');
    }

    if($shipping_type != NULL){
      $get_shipping->where('shipping_type', '=', $shipping_type);
    }

    if($shipping_area != NULL){
      $get_shipping->where('shipping_area', '=', $shipping_area);
    }

    if($shipping_name != NULL){
      $get_shipping->where('shipping_name', '=', $shipping_name);
    }

    if($shipping_cod != NULL){
      $get_shipping->where('shipping_cod', '=', $shipping_cod);
    }

    $get_shipping->orderBy($order, 'asc');

    return $get_shipping->get();
  }

  public static function getShippingWeight()
  {
    $shipping_weight = 0;
    $cart_content = Cart::content();
    foreach ($cart_content as $cart){
      $type = explode(",", $cart->options->type_url);
      $shipping_weight += ($type[0] == 'bags' || $type[0] == 'shoes') ? $cart->qty * 4 : $cart->qty ;
    }

    return ceil($shipping_weight / 5);
  }

  public static function getShippingCost($data)
  {
    $shipping_type = $data['shipping_type'];

    $params['get_primary'] = TRUE;
    $get_primary_address = Customer::getCustomerAddress($params);

    if(count($get_primary_address > 0)){
      foreach ($get_primary_address as $row){
        if($row->address_type == 1){
          $params_shipping['shipping_type'] = $shipping_type;
          $params_shipping['shipping_area'] = $row->address_province;
          $params_shipping['shipping_name'] = $row->address_city;

          $additional_fee = 2000;
          $shipping_weight = Self::getShippingWeight();

          //Set Order Session
          session()->put('shipping_weight', $shipping_weight);
          //End Set Session Order Session

          $get_shipping = Self::getShippingList($params_shipping);
          $final_cost = ( (isset($get_shipping[0]->shipping_price)) ? $get_shipping[0]->shipping_price : 0  * $shipping_weight) + $additional_fee ;
        }
      }
    }

    return (isset($final_cost)) ? $final_cost : 0 ;
  }

  public static function getPopupStore($params)
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    $shipping_name = (isset($params['shipping_name'])) ? $params['shipping_name'] : NULL ;
    $shipping_area = (isset($params['shipping_area'])) ? $params['shipping_area'] : NULL ;

    $get_location_store = DB::table('location_store')
      ->select(DB::raw('location_store.*'))
      ->join('shipping', 'shipping.shipping_id', '=', 'location_store.shipping_id');

    if($domain_id == 1){
      $get_location_store->where('location_store.enabled_bb', '=', 1);
    }elseif($domain_id == 2){
      $get_location_store->where('location_store.enabled_hb', '=', 1);
    }else{
      $get_location_store->where('location_store.enabled_sd', '=', 1);
    }

    if($shipping_name != NULL){
      $get_location_store->where('shipping.shipping_name', '=', $shipping_name);
    }
    
    if($shipping_area != NULL){
      $get_location_store->where('shipping.shipping_area', '=', $shipping_area);
    }

    return $get_location_store->get();
  }

  public static function checkAllowedPaymentMethod()
  {
    if(!session('payment_method')){
      return false;
    }else if(session('payment_method') != 20 && session('payment_method') != 4 && session('payment_method') != 5){
      //Only auto approval payment method is allowed for same-day-shipping or next-day-shipping, thus only Mandiri Debit, BCA KlikPay, and Credit Card Transaction is allowed.
      return false;
    }else if(session('payment_method') == 19){
      return false;
    }

    return true;
  }

  public static function checkCrossDock()
  {
    if(Cart::count() > 0){
      $cart = Cart::content();
      foreach ($cart as $row){
        $product_id = $row->options->product_id;
        $product_ownership_type = DB::table('product_ownership')
          ->select(DB::raw('product_ownership_type'))
          ->join('products', 'products.product_ownership', '=', 'product_ownership.product_ownership_id')
          ->where('products.product_id', '=', $product_id)
          ->take(1)
          ->value('product_ownership_type');
        
        if($product_ownership_type == 2){
          return false;
        }
      }
    }else{
      return false;
    }
    
    return true;
  }

  public static function checkShippingMethod($shipping_type)
  {
    if(Self::checkCrossDock()){
      $datetime               = new DateTime('tomorrow');
      $shipping_type          = (isset($shipping_type)) ? $shipping_type : 3 ; //3: Same Day Delivery | 4: Next Day Delivery
      $same_next_delivery_id  = ($shipping_type == 3) ? date("N") : $datetime->format('N') ;

      $fetch_same_next_delivery = DB::table('same_next_delivery')
        ->select(\DB::raw('*'))
        ->where('same_next_delivery.id', '=', $same_next_delivery_id)
        ->where('same_next_delivery.status', '=', 1)
        ->first();

      if(!empty($fetch_same_next_delivery)){
        if($shipping_type == 3){
          $date_now   = date('Y-m-d H:i:s');
          $start_date = date('Y-m-d 00:00:00');
          $end_date   = date('Y-m-d 11:00:00');
          
          if(date("N") == 5): //Batas same day untuk hari Jumat adalah 10:30
            $end_date = date('Y-m-d 10:30:00');
          endif;

          $start_ts = strtotime($start_date);
          $end_ts   = strtotime($end_date);
          $user_ts  = strtotime($date_now);

          // Check that user date is between start & end
          return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
        }
        
        //All condition is met
        return true;
      }
    }
    
    //Order Item has crossdock product or Day is not active for either same-day shipping or next-day shipping
    return false;
  }

  public static function getShippingMethod()
  {
    $get_domain = get_domain();
    $domain_id   = $get_domain['domain_id'];
    
    $params['get_primary'] = TRUE;
    $get_primary_address = Customer::getCustomerAddress($params);

    if(count($get_primary_address > 0)){
      foreach ($get_primary_address as $row){
        if($row->address_type == 1){
          $params_shipping['shipping_area'] = $row->address_province;
          $params_shipping['shipping_name'] = $row->address_city;
          $get_shipping = Self::getShippingList($params_shipping);

          $shipping_method  = array();
          $shipping_type    = (session('shipping_type')) ? session('shipping_type') : 1 ;
          
          $temp = 0;
          foreach ($get_shipping as $shipping) {
            $data['shipping_type'] = $shipping->shipping_type;
            
            if($shipping->shipping_type == 1 && session('payment_method') == 19){
              //COD
              $shipping_method[$temp]['shipping_type']  = $shipping->shipping_type;
              $shipping_method[$temp]['id']             = $shipping->shipping_id;
              $shipping_method[$temp]['shipping_price'] = $shipping->shipping_price;
              $shipping_method[$temp]['is_primary']     = ($shipping_type == $shipping->shipping_type) ? TRUE : FALSE ;
              $shipping_method[$temp]['is_available']   = TRUE;
              if ($domain_id == 3) {
                $shipping_method[$temp]['text']           = trans('shipping.text_cod', ['price' => number_format(Self::getShippingCost($data))]);
              } else {
                $shipping_method[$temp]['text']           = trans('shipping.text_cod_id', ['price' => number_format(Self::getShippingCost($data))]);
              }
            }else if($shipping->shipping_type == 1 && session('payment_method') != 19){
              //Regular
              $shipping_method[$temp]['shipping_type']  = $shipping->shipping_type;
              $shipping_method[$temp]['id']             = $shipping->shipping_id;
              $shipping_method[$temp]['shipping_price'] = $shipping->shipping_price;
              $shipping_method[$temp]['is_primary']     = ($shipping_type == $shipping->shipping_type) ? TRUE : FALSE ;
              $shipping_method[$temp]['is_available']   = TRUE;
              if ($domain_id == 3) {
                $shipping_method[$temp]['text']           = trans('shipping.text_reg', ['price' => number_format(Self::getShippingCost($data))]);
              } else {
                $shipping_method[$temp]['text']           = trans('shipping.text_reg_id', ['price' => number_format(Self::getShippingCost($data))]);
              }
            }else if($shipping->shipping_type == 3 && self::checkAllowedPaymentMethod() && Self::checkShippingMethod(3)){
              //Same Day Shipping
              $is_available = Self::checkShippingMethod(3);
              $info = ($is_available) ? '' : '<em style="float: left;color: red;margin-bottom: 15px;">'.trans('shipping.text_err_info').'</em>' ;
              
              $shipping_method[$temp]['shipping_type']  = $shipping->shipping_type;
              $shipping_method[$temp]['id']             = $shipping->shipping_id;
              $shipping_method[$temp]['shipping_price'] = $shipping->shipping_price;
              $shipping_method[$temp]['is_primary']     = ($shipping_type == $shipping->shipping_type) ? TRUE : FALSE ;
              $shipping_method[$temp]['is_available']   = $is_available;
              if ($domain_id == 3) {
                $shipping_method[$temp]['text']           = trans('shipping.text_same_day', ['price' => number_format(Self::getShippingCost($data)), 'info' => $info]);
              } else {
                $shipping_method[$temp]['text']           = trans('shipping.text_same_day_id', ['price' => number_format(Self::getShippingCost($data)), 'info' => $info]);
              }
            }else if($shipping->shipping_type == 4 && self::checkAllowedPaymentMethod() && Self::checkShippingMethod(4)){
              //Next Day Shipping
              $is_available = Self::checkShippingMethod(4);
              $info = ($is_available) ? '' : '<em style="float: left;color: red;margin-bottom: 15px;">'.trans('shipping.text_err_info').'</em>' ;
              
              $shipping_method[$temp]['shipping_type']  = $shipping->shipping_type;
              $shipping_method[$temp]['id']             = $shipping->shipping_id;
              $shipping_method[$temp]['shipping_price'] = $shipping->shipping_price;
              $shipping_method[$temp]['is_primary']     = ($shipping_type == $shipping->shipping_type) ? TRUE : FALSE ;
              $shipping_method[$temp]['is_available']   = $is_available;
              if ($domain_id == 3) {
                $shipping_method[$temp]['text']           = trans('shipping.text_next_day', ['price' => number_format(Self::getShippingCost($data)), 'info' => $info]);
              } else {
                $shipping_method[$temp]['text']           = trans('shipping.text_next_day_id', ['price' => number_format(Self::getShippingCost($data)), 'info' => $info]);
              }
                          
            }
            
            $temp++;
          }
        }
      }
    }

    return (isset($shipping_method) && !empty($shipping_method)) ? $shipping_method : false;
  }

  public static function getFreeshipping()
  {
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];

    $get_freeshipping = DB::table('freeshipping')
      ->select(DB::raw('freeshipping_price'))
      ->where('domain_id', '=', $domain_id)
      ->where('enabled', '=', 1)
      ->orderBy('modified_date', 'DESC')
      ->take(1)
      ->value('freeshipping_price');
    $freeshipping_value = ($get_freeshipping > 0) ? $get_freeshipping : 0 ;

    return $freeshipping_value;
  }

  public static function getStatusFreeshipping($grand_total_before_shipping)
  {
    if($grand_total_before_shipping >= Self::getFreeshipping()){ //Check from Admin
      return true;
    }else{
      return false;
    }
  }

  public static function automate_location_store_master_payment()
  {
    //Fetch Location Store
    $get_location_store = DB::table('location_store')
                            ->where('status_deleted', '=', 0)->get();
    //When data is found
    if (array_filter($get_location_store)) {
      foreach ($get_location_store as $location_store) {
        // Validate Current Day with Startdate and Enddate from location store
        $current_date = date('Y-m-d');
        //Data location store
        $location_id        = $location_store->location_id;
        $enabled_bb         = isset($location_store->enabled_bb) ? $location_store->enabled_bb : 0;
        $enabled_hb         = isset($location_store->enabled_hb) ? $location_store->enabled_hb : 0;
        $enabled_sd         = isset($location_store->enabled_sd) ? $location_store->enabled_sd : 0;
        $startdate          = $location_store->startdate;
        $enddate            = $location_store->enddate;
        $master_payment_id  = $location_store->master_payment_id;

        //condition 1 startdate and enddate
        if (check_in_range($startdate, $enddate, $current_date)) {
          \Log::alert('Location store in range : ' . $location_id);
          
            // enable master payment yg disabled jika location store aktif
            if($enabled_bb == 1 || $enabled_hb == 1 || $enabled_sd == 1){
                DB::enableQueryLog();
                DB::table('master_payment')
                      ->where('master_payment_id', $master_payment_id)
                      ->update(['enabled_bb' => $enabled_bb, 'enabled_hb' => $enabled_hb, 'enabled_sd' => $enabled_sd]);  
                $laQuery = DB::getQueryLog();
                $query = $laQuery[0]['query'];
                $binding = $laQuery[0]['bindings'];
                
                $string = '[DATE IN RANGE] Master payment id = '.$master_payment_id .', enabled_hb = '. $enabled_bb .', enabled_bb = '. $enabled_hb .', enabled_sd = '. $enabled_sd .''; 
                DB::disableQueryLog();
                DB::table('location_store_log')->insert(
                    ['query' => $query, 'binding' => $string, 'created_by' => 'cron', 'created_date' => date('Y-m-d H:i:s')]);
            }                    
        } else {
          $hold_master[] = $master_payment_id;
          \Log::alert('Location store not in range : ' . $location_id);
          //Disable master_payment_enabled, enabled_bb, enabled_hb if payment enabl
          DB::enableQueryLog();
          DB::table('master_payment')
                ->where('master_payment_id', $master_payment_id)
                ->update(['enabled_bb' => 0, 'enabled_hb' => 0, 'enabled_sd' => 0]);
          $laQuery = DB::getQueryLog();
          $query = $laQuery[0]['query'];
          $binding = $laQuery[0]['bindings'];
          //$string = var_export($binding, true);
          $string = 'master payment id = '.$master_payment_id .', enabled_hb = 0, enabled_bb = 0'; 
          # optionally disable the query log:
          DB::disableQueryLog();
          DB::table('location_store_log')->insert(
              ['query' => $query, 'binding' => $string, 'created_by' => 'cron', 'created_date' => date('Y-m-d H:i:s')]);
        }
      }
      $master_payment = $hold_master;
      $master_payment_comma_separated = implode(",", $master_payment);
      \Log::alert('data cron automate_location_store_master_payment master_payment_id : ' . $master_payment_comma_separated);
      echo 'data process master_payment_id '.$master_payment_comma_separated;
    } else {
      echo 'no data process';
      \Log::alert('no data process for cron automate_location_store_master_payment');
    }
  }

}
