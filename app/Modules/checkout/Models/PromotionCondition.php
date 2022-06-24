<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\Product;
use Cart;
use DB;
use Log;
use \App\Modules\Checkout\Models\Payment;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\PromotionHelper;
use Auth;

class PromotionCondition extends model {
  
    protected $table      = 'promotions_condition'; //Define your table name

    protected $primaryKey = 'promotions_condition_id'; //Define your primarykey

    public $timestamps    = false; //Define yout timestamps

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded    = ['promotions_condition_id']; //Define your guarded columns


    public static function freeCheapestItems(array $promotions, array $customer)
    {
        // load service
        Log::notice($promotions['promotions_template_name'].' was in freeCheapestItems');

        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $order_items     = OrderItem::fetchOrderItem();

        $equal_value    = str_replace("^", "", $equal_value);
        $total_item     = count($order_items);

        // Check apakan order minimum 4 item.
        $minimumItems   = $equal_value * 2;

        if ($total_item < $minimumItems) {
            Log::notice($promotions['promotions_template_name'].' freeCheapestItems error');
            \Session::put('temp_err_msg', 'Jumlah total item tidak memenuhi persyaratan.');
            return false;
        }

        // populate cheap item
        foreach ($order_items as $key => $value) {
          $price = set_price($value->each_price, $value->discount_price);
          $order_items[$key]->real_price = $price;
        }

        // sort item berdasarkan harga
        usort($order_items, function ($a, $b) {
          return $a->real_price - $b->real_price;
        });

        // pisahkan cheap item
        $cheapItem = array();
        if (isset($order_items[0]) AND $order_items[1]) {
          $cheapItem = [$order_items[0], $order_items[1]];
        }

        // validasi apakan total cheap item == $equal_value
        $total_value = count($cheapItem);
        $status   = ($total_value == $equal_value) ? true : false;;
        
        if (! $status) {
          Log::notice(''.$promotions['promotions_template_name'].' freeCheapestItems error');
          \Session::put('temp_err_msg', 'Jumlah total item tidak memenuhi persyaratan.');
        }
        
        return $status;
      
    }

    public static function subtotal(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in subtotal');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        //Calculate Total Value yang terkena promotions
        $freegift_auto        = (session('freegift_auto')) ? session('freegift_auto') : array() ;
        $voucher              = (session('voucher')) ? session('voucher') : array() ;
        $freegift             = (session('freegift')) ? session('freegift') : array() ;
        $total_value          = 0;
        $order_item_applied   = (session('order_item_applied')) ? session('order_item_applied') : array() ;
        $mode                 = isset($promotions['mode']) ? $promotions['mode'] : NULL;
        $fetch_order_item     = OrderItem::fetchOrderItem();
        
        //Clear Order Item Real Price
        foreach($fetch_order_item as $order_item){
          unset($order_item->real_price);
        }
        //End Clear
        
        //Calculate freegift auto total item price
        if(!empty($freegift_auto)){
          foreach($freegift_auto as $key => $values){
            $reshape_order_item     = PromotionHelper::reshapeOrderItem($freegift_auto[$key], $fetch_order_item);
            $freegift_auto[$key]    = $reshape_order_item['promotions'];
            $fetch_order_item       = $reshape_order_item['fetch_order_item'];
            
            $total_purchase_value   = PromotionHelper::getTotalPromotionsPurchase($freegift_auto[$key], $fetch_order_item);
            
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
                  $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

                  $order_item->real_price = $order_price - $promotions_value;

                  //Check whether the price is below 0 after promotions value, then set promotions value to item price
                  if($order_item->real_price < 0){
                    $order_item->real_price = 0;
                  }
                  //End Check
                }
              }
            }
          }
        }
        //End Calculate freegift auto total item price
        
        //Calculate voucher total item price
        if(!empty($voucher) && $mode != 'freegift_auto' && $mode != 'voucher'){
          $reshape_order_item     = PromotionHelper::reshapeOrderItem($voucher, $fetch_order_item);
          $voucher                = $reshape_order_item['promotions'];
          $fetch_order_item       = $reshape_order_item['fetch_order_item'];
            
          $total_purchase_value   = PromotionHelper::getTotalPromotionsPurchase($voucher, $fetch_order_item);
          
          $use_maximum_value_discount = PromotionHelper::useMaximumValueDiscount($voucher, $fetch_order_item, $total_purchase_value);

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
                $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

                $order_item->real_price = $order_price - $promotions_value;

                //Check whether the price is below 0 after promotions value, then set promotions value to item price
                if($order_item->real_price < 0){
                  $order_item->real_price = 0;
                }
                //End Check
              }
            }
          }
        }
        //End Calculate voucher total item price
        
        //Calculate freegift total item price
        if(!empty($freegift) && $mode != 'freegift_auto' && $mode != 'voucher'){
          foreach($freegift as $key => $values){
            $reshape_order_item = PromotionHelper::reshapeOrderItem($freegift[$key], $fetch_order_item);
            $freegift[$key]     = $reshape_order_item['promotions'];
            $fetch_order_item   = $reshape_order_item['fetch_order_item'];
            
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
                  $promotions_value = PromotionHelper::setPromotionsValue($par_promo_value);

                  $order_item->real_price = $order_price - $promotions_value;

                  //Check whether the price is below 0 after promotions value, then set promotions value to item price
                  if($order_item->real_price < 0){
                    $order_item->real_price = 0;
                  }
                  //End Check
                }
              }
            }
          }
        }
        //End Calculate freegift total item price
        
        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            if(in_array($order_item->order_item_id, $order_item_applied)){
              $order_price = PromotionHelper::setItemPrice($order_item);
              $total_value += $order_price;
            }
          }
        }
        //End Calculate
        
        $status   = self::conditionEqual($total_value, $equal_value, $equal_type);
        
        if (! $status) {
          Log::notice(''.$promotions['promotions_template_name'].' subtotal error');
        }
        
        return $status;
    }

    public static function totalItemQuantity(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in totalItemQuantity');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        $promotions_applicable  = isset($promotions['promotions_template_applicable']) ? $promotions['promotions_template_applicable'] : 1; //1: Rule Select | 2: Whole Cart
        
        $fetch_order_item     = OrderItem::fetchOrderItem();
        $reshape_order_item   = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item     = $reshape_order_item['fetch_order_item'];
        $order_item_applied   = (session('order_item_applied')) ? session('order_item_applied') : array() ;
        
        $total_item = 0;
        foreach($fetch_order_item as $order_item){
          if(!isset($order_item->excluded) && !property_exists($order_item, 'excluded')){
            if(in_array($order_item->order_item_id, $order_item_applied)){
              $total_item++;
            }
          }
        }
        
        $status = self::conditionEqual($total_item, $equal_value, $equal_type);
        
        if (! $status) {
          Log::notice(''.$promotions['promotions_template_name'].' totalItemQuantity error');
        }

        return $status;
    }

    public static function totalWeight(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in totalWeight');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        $total_weight       = $get_order_session['shipping_weight'];
        $status             = self::conditionEqual($total_weight, $equal_value, $equal_type);
        
        if (! $status) {
          Log::notice(''.$promotions['promotions_template_name'].' totalWeight error');
        }

        return $status;
    }

    public static function creditCardBankPromo(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in creditCardBankPromo');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $typeCardBankId = array(39,47,48); // ids of bank jcb/visa/mastercard

        if (in_array($equal_value, $typeCardBankId)) {

          $binNumber = session('bin_number');

          $binIdentifier  = [(int)substr($binNumber, 0, 1)]; // first occurence of bin number is identifier of type card

          $bankIdentifier = [];

          $binTypeCard = array(

              39 => 3, // type card JCB

              47 => 4, // type card Visa

              48 => 5, // type card Master card

          );

          $getBank = Payment::fetch_bank(array($equal_value));

          foreach ($getBank as $value) {
            

              $bankIdentifier[] = $binTypeCard[$value['bank_id']];

          }

          // force check condition equal to type = 9 to check wether binIdentifier is in registered bankIdentifier

          $status = self::conditionEqual($binIdentifier, $bankIdentifier, 9);
          session()->put('need_bank_id', TRUE);
        }else{
          $get_order_session  = Order::getOrderSession();
          $bank_id            = $get_order_session['bank_id'];
          $status             = self::conditionEqual($bank_id, $equal_value, $equal_type);
          session()->put('need_bank_id', TRUE);
        }

        if (!$status){
          Log::notice(''.$promotions['promotions_template_name'].' creditCardBankPromo error');
        }

        return $status;
    }

    public static function productIdIn(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in productIdIn');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        //Order Item
        $fetch_order_item   = OrderItem::fetchOrderItem();

        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        
        if(empty($fetch_order_item)){
          Log::notice(''.$promotions['promotions_template_name'].' productIdIn Order Item is missing/empty or all products is not on condition');
          return false;
        }else{
          $product_id_compare         = [];
          $order_item_applied         = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          
          $product_id_equal           = $equal_value;
          $product_id_no_tag_equal    = str_replace("^", "", $product_id_equal);
          $equal_value                = explode(",", $product_id_no_tag_equal);
          
          //Check per order_item and store the order_item_id is condition is met
          foreach ($fetch_order_item as $order_item) {
            $product_id_compare[] = $order_item->product_id;
            $status               = self::conditionEqual($product_id_compare, $equal_value, $equal_type);
            if ($status) {
              Log::notice(''.$promotions['promotions_template_name'].' productIdIn '.$order_item->product_id.' is on condition');
            }else{
              Log::notice(''.$promotions['promotions_template_name'].' productIdIn '.$order_item->product_id.' is not on condition');
              
              //Remove Order Item ID From Promotions
              if(($key = array_search($order_item->order_item_id, $order_item_applied)) !== false) {
                unset($order_item_applied[$key]);
              }
            }
            $product_id_compare = [];
          }
          
          if (empty($order_item_applied)) {
            Log::notice(''.$promotions['promotions_template_name'].' productIdIn error. All products is not on condition');
            return false;
          }else{
            session()->put('order_item_applied', $order_item_applied);
            return true;
          }
        }
    }

    public static function brandIdIn(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in brandIdIn');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        //Order Item
        $fetch_order_item   = OrderItem::fetchOrderItem();
        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        if(empty($fetch_order_item)){
          Log::notice(''.$promotions['promotions_template_name'].' brandIdIn Order Item is missing/empty');
          return false;
        }else{
          $brand_id_compare           = [];
          $order_item_applied         = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          
          $brand_id_equal             = $equal_value;
          $brand_id_no_tag_equal      = str_replace("^", "", $brand_id_equal);
          $equal_value                = explode(",", $brand_id_no_tag_equal);
          
          //Check per order_item and store the order_item_id is condition is met
          foreach ($fetch_order_item as $order_item) {
            $brand_id_compare[]  = $order_item->product_brand;
            $status             = self::conditionEqual($brand_id_compare, $equal_value, $equal_type);
            if ($status) {
              Log::notice(''.$promotions['promotions_template_name'].' brandIdIn '.$order_item->product_id.' is on condition');
            }else{
              Log::notice(''.$promotions['promotions_template_name'].' brandIdIn '.$order_item->product_id.' is not on condition');
              
              //Remove Order Item ID From Promotions
              if(($key = array_search($order_item->order_item_id, $order_item_applied)) !== false) {
                unset($order_item_applied[$key]);
              }
            }
            $brand_id_compare = [];
          }
          
          if (empty($order_item_applied)) {
            Log::notice(''.$promotions['promotions_template_name'].' brandIdIn error. All products is not on condition');
            return false;
          }else{
            session()->put('order_item_applied', $order_item_applied);
            return true;
          }
        }
    }
    
    public static function categoryId(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in categoryId');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        //Order Item
        $fetch_order_item   = OrderItem::fetchOrderItem();
        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        
        if(empty($fetch_order_item)){
          Log::notice(''.$promotions['promotions_template_name'].' categoryId Order Item is missing/empty or all product is not on condition.');
          return false;
        }else{
          $category_id                = [];
          $order_item_applied         = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          
          $category_id_equal          = $equal_value;
          $category_id_no_tag_equal   = str_replace("^", "", $category_id_equal);
          $equal_value                = explode(",", $category_id_no_tag_equal);
          
          //Check per order_item and store the order_item_id is condition is met
          foreach ($fetch_order_item as $order_item) {
            $front_end_type       = $order_item->front_end_type;
            $category_array       = explode(",", $front_end_type);
            $category_id_compare  = array_unique(array_merge($category_array,$category_id), SORT_REGULAR);
            
            $status                 = self::conditionEqual($category_id_compare, $equal_value, $equal_type);
            if ($status) {
              Log::notice(''.$promotions['promotions_template_name'].' categoryId '.$order_item->product_id.' is on condition');
            }else{
              Log::notice(''.$promotions['promotions_template_name'].' categoryId '.$order_item->product_id.' is not on condition');
              
              //Remove Order Item ID From Promotions
              if(($key = array_search($order_item->order_item_id, $order_item_applied)) !== false) {
                unset($order_item_applied[$key]);
              }
            }
          }
          
          if (empty($order_item_applied)) {
            Log::notice(''.$promotions['promotions_template_name'].' categoryId error. All products is not on condition');
            return false;
          }else{
            session()->put('order_item_applied', $order_item_applied);
            return true;
          }
        }
        
        //Customer
        $cart = Cart::content();
        $category_cart = [];
        foreach ($cart as $row) {
            $front_end_type = $row->options->front_end_type;
            $category_array = explode(",", $front_end_type);
            $category_cart  = array_unique(array_merge($category_array,$category_cart), SORT_REGULAR);
        }
        $category_id_in_cart    = $category_cart;

        //Promotions
        $brand_id_equal             = $equal_value;
        $brand_id_no_tag_equal      = str_replace("^", "", $brand_id_equal);
        $equal_value                = explode(",", $brand_id_no_tag_equal);

        $status     = self::conditionEqual($category_id_in_cart, $equal_value, $equal_type);
        if (! $status) {
            Log::notice(''.$promotions['promotions_template_name'].' categoryId error');
        }

        return $status;
    }

    public static function selectedDay(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in selectedDay');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        
        $selected_day  = $get_order_session['selected_day'];
        
        $selected_day_equal         = $equal_value;
        $selected_day_no_tag_equal  = str_replace("^", "", $selected_day_equal);
        $equal_value                = explode(",", $selected_day_no_tag_equal);
        
        $status             = self::conditionEqual($selected_day, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' selectedDay error');
        }

        return $status;
    }

    public static function gender(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in gender');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        //Check product gender on order item
        $fetch_order_item   = OrderItem::fetchOrderItem();
        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        if(empty($fetch_order_item)){
          Log::notice(''.$promotions['promotions_template_name'].' gender Order Item is missing/empty or all products is not on condition');
          return false;
        }else{
          $order_item_applied = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          
          foreach ($fetch_order_item as $order_item) {
            //Get Gender
            $data_product                 = array();
            $data_product['where']['id']  = $order_item->product_id;;
            $fetch_product                = Product::fetchProduct($data_product); //fetch data product from DB
            $product_gender               = $fetch_product[0]->product_gender;
            //End Get Gender
            
            $status = self::conditionEqual($product_gender, $equal_value, $equal_type);
            if ($status) {
              Log::notice(''.$promotions['promotions_template_name'].' gender '.$order_item->product_id.' is on condition');
            }else{
              Log::notice(''.$promotions['promotions_template_name'].' gender '.$order_item->product_id.' is not on condition');
              
              //Remove Order Item ID From Promotions
              if(($key = array_search($order_item->order_item_id, $order_item_applied)) !== false) {
                unset($order_item_applied[$key]);
              }
            }
          }
          
          if (empty($order_item_applied)) {
            Log::notice(''.$promotions['promotions_template_name'].' gender error. All products is not on condition');
            return false;
          }else{
            session()->put('order_item_applied', $order_item_applied);
            return true;
          }
        }

        return $status;
    }

    public static function maximumUsage(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in maximumUsage');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        $attributes['promotions_template_id'] = $promotions['promotions_template_id'];
        $attributes['voucher_code']           = isset($promotions['voucher_code']) ? $promotions['voucher_code'] : NULL ;
        $attributes['get_total_order']        = true;
        $get_total_usage                      = Promotion::getOrderDiscount($attributes);
        $total_usage                          = (!empty($get_total_usage)) ? count($get_total_usage) : 0 ;
        $status                               = self::conditionEqual($total_usage + 1, $equal_value, $equal_type);
        
        if (! $status) {
          \Session::put('temp_err_msg', 'Promo ini sudah melebihi batas pemakaian kuota.');
          Log::notice(''.$promotions['promotions_template_name'].' maximumUsage error');
        }
        
        return $status;
    }

    public static function customerIdEmail(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in customerIdEmail');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $customer_status  = Auth::user()->customer_status;
        $status           = self::conditionEqual($customer_status, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' customerIdEmail error');
        }

        return $status;
    }

    public static function promoPage(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in promoPage');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        //Order Item
        $fetch_order_item   = OrderItem::fetchOrderItem();
        $reshape_order_item = PromotionHelper::reshapeOrderItem($promotions, $fetch_order_item);
        $fetch_order_item   = $reshape_order_item['fetch_order_item'];
        
        if(empty($fetch_order_item)){
          Log::notice(''.$promotions['promotions_template_name'].' promoPage Order Item is missing/empty or all products is not on condition');
          return false;
        }else{
          $promo_id_compare       = [];
          $order_item_applied     = (session('order_item_applied')) ? session('order_item_applied') : array() ;
          
          $promo_id_equal         = $equal_value;
          $promo_id_no_tag_equal  = str_replace("^", "", $promo_id_equal);
          $equal_value            = explode(",", $promo_id_no_tag_equal);
          
          //Check per order_item and store the order_item_id is condition is met
          foreach ($fetch_order_item as $order_item) {
            $promo_id_compare[] = $order_item->promo_id;
            $status               = self::conditionEqual($promo_id_compare, $equal_value, $equal_type);
            if ($status) {
              Log::notice(''.$promotions['promotions_template_name'].' promoPage '.$order_item->product_id.' is on condition');
            }else{
              Log::notice(''.$promotions['promotions_template_name'].' promoPage '.$order_item->product_id.' is not on condition');
              
              //Remove Order Item ID From Promotions
              if(($key = array_search($order_item->order_item_id, $order_item_applied)) !== false) {
                unset($order_item_applied[$key]);
              }
            }
            $promo_id_compare = [];
          }
          
          if (empty($order_item_applied)) {
            Log::notice(''.$promotions['promotions_template_name'].' promoPage error. All products is not on condition');
            return false;
          }else{
            session()->put('order_item_applied', $order_item_applied);
            return true;
          }
        }
    }

    public static function extPromoPage(array $promotions, array $customer)
    {
        var_dump('i was in extPromoPage');
        return true;
    }

    public static function brandPage(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in brandPage');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        $brand_page         = $get_order_session['brand_page'];
        $brand_page_equal   = $equal_value;
        $equal_value        = str_replace("^", "", $brand_page_equal);
        $status             = self::conditionEqual($brand_page, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' brandPage error');
        }

        return $status;
    }

    public static function utmSource(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in utmSource');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        $utm_source         = $get_order_session['utm_source'];
        $status             = self::conditionEqual($utm_source, $equal_value, $equal_type);
        
        if (!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' utmSource error');
        }

        return $status;
    }

    public static function shippingCity(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in shippingCity');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        $shipping_id        = array();
        $shipping_id[]      = $get_order_session['shipping_id'];
        
        $shipping_id_equal          = $equal_value;
        $shipping_id_no_tag_equal   = str_replace("^", "", $shipping_id_equal);
        $equal_value                = explode(",", $shipping_id_no_tag_equal);
        
        $status             = self::conditionEqual($shipping_id, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' shippingCity error');
        }

        return $status;
    }

    public static function platformDomain(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in platformDomain');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        $equal_type     = ($equal_type == 1) ? 9 : $equal_type ;
        
        if (strpos($equal_value, ',') !== false) {
          $equal_value  = explode(",", $equal_value);
        }else{
          $equal_value  = [$equal_value];
        }
        
        $get_order_session  = Order::getOrderSession();
        $platform_domain    = $get_order_session['platform_domain'];
        $platform_domain    = [$platform_domain];
        
        $status             = self::conditionEqual($platform_domain, $equal_value, $equal_type);
        
        if (!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' platformDomain error');
        }

        return $status;
    }

    public static function platformDevice(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in platformDevice');
        $equal_value    = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type     = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;

        $get_order_session  = Order::getOrderSession();
        $platform_device    = $get_order_session['platform_device'];
        $status             = self::conditionEqual($platform_device, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' platformDevice error');
        }

        return $status;
    }
    
    public static function emailDomain(array $promotions, array $customer)
    {
        Log::notice(''.$promotions['promotions_template_name'].' was in emailDomain');
        $equal_value  = isset($promotions['promotions_type_equal_value']) ? $promotions['promotions_type_equal_value'] : NULL;
        $equal_type   = isset($promotions['promotions_type_equal_type']) ? $promotions['promotions_type_equal_type'] : NULL;
        
        //Check Authentication
        if (!Auth::check()) {
          Log::notice(''.$promotions['promotions_template_name'].' emailDomain account is not logged in');
          return false;
        }
        //End Check Authentication
        
        $customer_email = explode("@", Auth::user()->customer_email);
        
        $customer_email_param   = [];
        $customer_email_param[] = "@" . $customer_email[1];
        
        $customer_email_equal         = $equal_value;
        $customer_email_no_tag_equal  = str_replace("^", "", $customer_email_equal);
        $customer_email_replace_space = str_replace(" ", "", $customer_email_no_tag_equal);
        $equal_value                  = explode(",", $customer_email_replace_space);
        
        $status = self::conditionEqual($customer_email_param, $equal_value, $equal_type);
        
        if(!$status) {
          Log::notice(''.$promotions['promotions_template_name'].' emailDomain error');
        }

        return $status;
    }

    //not finish all condition
    public static function conditionEqual($needle, $haystack, $condition_type)
    {
        //1.is, 2.is not, 3. equals or greater than, 4. equals or less than, 5. greater than,
        //6. less than, 7.contains, 8. is not contain, 9.is one of, 10. is not one of, 11. equals or below
        //change var type for logging
        if (is_array($needle)) {
            $needle_string = implode(',', $needle);
            $haystack_string = implode(',', $haystack);
        } else {
            $needle_string = $needle;
            $haystack_string = $haystack;
        }

        if ($condition_type == 1) {
            Log::notice('condition '.$needle_string.' is[==] '.$haystack_string.'');
            return ($needle == $haystack) ? true : false;
        }
        if ($condition_type == 2) {
            Log::notice('condition '.$needle_string.' is not[!=] '.$haystack_string.'');
            return ($needle != $haystack) ? true : false;
        }
        if ($condition_type == 3) {
            Log::notice('condition '.$needle_string.' equals or greater than[>=] '.$haystack_string.'');
            return ($needle >= $haystack) ? true : false;
        }
        if ($condition_type == 4) {
            Log::notice('condition '.$needle_string.' equals or less than[<=] '.$haystack_string.'');
            return ($needle <= $haystack) ? true : false;
        }
        if ($condition_type == 5) {
            Log::notice('condition '.$needle_string.' greater than[>] '.$haystack_string.'');
            return ($needle > $haystack) ? true : false;
        }
        if ($condition_type == 6) {
            Log::notice('condition '.$needle_string.' less than[<] '.$haystack_string.'');
            return ($needle < $haystack) ? true : false;
        }
        // if ($condition_type == 7) {
        //     Log::notice('condition '.$needle_string.' contains[array_diff] '.$haystack_string.'');
        //     return ($result == $needle) ? true : false;
        // }
        if ($condition_type == 8) {
            $result = array_diff($needle, $haystack);
            Log::notice('condition '.$needle_string.' is not contain[array_diff] '.$haystack_string.'');
            return ($result == $needle) ? true : false;
        }
        if ($condition_type == 9) {
            //if result null means same array thats match
            //if result not the same means is one of thats match
            $result = array_diff($needle, $haystack);

            Log::notice('condition '.$needle_string.' is one of[array_diff] '.$haystack_string.'');
            //if true then not is one of
            return ($result != $needle) ? true : false;
        }
        if ($condition_type == 10) {
            $result = array_diff($needle, $haystack);

            Log::notice('condition '.$needle_string.' is one of[array_diff] '.$haystack_string.'');
            //if true then not is one of
            return ($result == $needle) ? true : false;
        }
        if ($condition_type == 11) {
            Log::notice('condition '.$needle_string.' is[<=] '.$haystack_string.'');
            return ($needle <= $haystack) ? true : false;
        }
        Log::notice('Invalid argument type');
        return false;
    }

    public static function filterAccounts($accounts, $accountType)
    {
        return array_filter($accounts, function ($account) use ($accountType) {
            //return $account->isOfType($accountType);
            return ($account['type'] == $accountType && $account['status'] === 'active');
        });
    }

    public static function arrayDiffEmulation($arrayFrom, $arrayAgainst)
    {
        $arrayAgainst = array_flip($arrayAgainst);

        foreach ($arrayFrom as $key => $value) {
            if(isset($arrayAgainst[$value])) {
                unset($arrayFrom[$key]);
            }
        }

        return $arrayFrom;
    }

    public static function isHomogenous($find, $arr)
    {
        $firstValue = current($arr);
        foreach ($arr as $val) {
            if ($firstValue !== $val) {
                return false;
            }
        }
        return true;
    }
    
    public static function setOrderItem()
    {
      
    }

}
