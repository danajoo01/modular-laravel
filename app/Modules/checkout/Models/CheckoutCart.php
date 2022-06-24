<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Product\Models\Product;
use Auth;
use Cart;
use DB;

class CheckoutCart extends Model {

	/**
	 * The database table used by the model .
	 *
	 * @var string
	 */
	protected $table = 'table name'; //Define your table name

	protected $primaryKey = 'key_id'; //Define your primarykey

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

  public static function fetchBankPromo()
  {
    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    $today      = date('Y-m-d H:i:s');

    DB::enableQueryLog();
    $bank_promo = DB::table('promotions_template')
      ->leftJoin('promotions_condition', 'promotions_template.promotions_template_id', '=', 'promotions_condition.promotions_template_id')
      ->leftJoin('bank', 'promotions_condition.promotions_type_equal_value', '=', 'bank.bank_id')
      ->select(\DB::raw('
        promotions_template.`promotions_template_id`,
        promotions_template.`promotions_template_name`,
        promotions_template.`promotions_template_mode`,
        promotions_template.`promotions_template_mode_value`,
        (
          SELECT pc1.promotions_type_equal_value
          FROM promotions_condition pc1
          WHERE pc1.promotions_template_id = promotions_template.`promotions_template_id`
          AND pc1.promotions_type_condition = 4
          LIMIT 0,1
        ) AS min_purchase,
        (
          SELECT pc2.promotions_type_equal_value
          FROM promotions_condition pc2
          WHERE pc2.promotions_template_id = promotions_template.`promotions_template_id`
          AND pc2.promotions_type_condition = 30
          LIMIT 0,1
        ) AS domain_id,
        bank.`name`
      '))
      ->where('promotions_template.start_date', '<', $today)
      ->where('promotions_template.end_date', '>', $today)
      ->where('promotions_template.enabled', '=', 1)
      ->where('promotions_condition.promotions_type_condition', '=', 7)
      ->orderBy('promotions_template.promotions_template_id', 'ASC')
    ->get();
    
    $final_promo_bank = [];
    $temp = 0;
    if($bank_promo){
      foreach($bank_promo as $promo){
        if($promo->domain_id == NULL || $promo->domain_id == $domain_id){
          $bank_name    = $promo->name;
          $min_purchase = ($promo->min_purchase != NULL) ? "Min. Purchase IDR " . substr($promo->min_purchase, 0, 3) . "K" : "No Min. Purchase";

          $promo_value  = 'IDR ' . number_format($promo->promotions_template_mode_value);
          if($promo->promotions_template_mode == 2){
            $promo_value = $promo->promotions_template_mode_value . '% OFF';
          }
          
          $final_promo_bank[$temp]['bank_name']     = $bank_name;
          $final_promo_bank[$temp]['min_purchase']  = $min_purchase;
          $final_promo_bank[$temp]['promo_value']   = $promo_value;
          $final_promo_bank[$temp]['highlight']     = FALSE;
          
          $temp++;
        }
      }
    }
    
    //Request Sorting Bank to be first
    $sorted_bank  = 'ANZ';
    $temp_sort    = 0;
    
    foreach($final_promo_bank as $key => $promo_bank){
      $bank_name = (isset($final_promo_bank[$key]['bank_name'])) ? $final_promo_bank[$key]['bank_name'] : '' ;
      
      if($bank_name == $sorted_bank){
        $final_promo_bank[$key]['highlight'] = TRUE;
        
        //Swap Array Key
        $temp_promo                     = $final_promo_bank[$temp_sort];
        $final_promo_bank[$temp_sort]   = $final_promo_bank[$key];
        $final_promo_bank[$key]         = $temp_promo;
        
        $temp_sort++;
      }
    }

    return $final_promo_bank;
  }

  public static function checkInventory($params)
  {
    $SKU      = isset($params['SKU']) ? str_replace('/', 'or', $params['SKU']) : NULL;
    $quantity = isset($params['quantity']) ? $params['quantity'] : 0;
    
    $inventory = DB::table('inventory')->where('SKU', '=',  $SKU)->value('quantity');

    $data['result'] = ($quantity > $inventory) ? FALSE : TRUE ;
    $data['inventory'] = $inventory;

    return $data;
  }
  
  public static function updateCartPrice()
  {
    $cart = Cart::content();
    
    if(Cart::count() > 0){
      foreach ($cart as $row){
        //Update Price
        $fetch_products = Product::where('product_id', '=', $row->options->product_id)->first();
        if($fetch_products){
          $price = set_price($fetch_products->product_price, $fetch_products->product_sale_price);
          
          Cart::update($row->rowid, array('price' => isset($price) ? $price : 0));
        }else{
          Cart::remove($row->rowid);
        }
        //End Update Price
      }
    }
  }

  public static function fetchCart()
  {
    Self::updateCartPrice();
    $cart = Cart::content();  
    $cart_content = array();
    $temp = 0;
    
    if(Cart::count() > 0){
      foreach ($cart as $row){
        $cart_content[$temp]['product_id']      = $row->options->product_id;
        $cart_content[$temp]['SKU']             = $row->id;
        $cart_content[$temp]['image']           = $row->options->image;
        $cart_content[$temp]['brand_id']        = $row->options->brand_id;
        $cart_content[$temp]['brand_name']      = $row->options->brand_name;
        $cart_content[$temp]['front_end_type']  = $row->options->front_end_type;        
        $cart_content[$temp]['size']            = $row->options->size;
        $cart_content[$temp]['name']            = $row->name;
        $cart_content[$temp]['color_name']      = $row->options->color_name;
        $cart_content[$temp]['type_id']         = $row->options->type_id;
        $cart_content[$temp]['type_id_real']    = $row->options->type_id_real;
        $cart_content[$temp]['price']           = $row->price;
        $cart_content[$temp]['qty']             = $row->qty;
        $cart_content[$temp]['subtotal']        = $row->subtotal;
        $cart_content[$temp]['type_url']        = $row->options->type_url;
        
        //URL
        $url_arr = explode(',', $row->options->type_url);
        $parent  = isset($url_arr[0]) ? $url_arr[0] : $row->options->type_url;                
        $child   = isset($url_arr[1]) ? $url_arr[1] : $row->options->type_url;
        $cart_content[$temp]['url'] = url(''.$parent.'/'.$child.'/'.$row->options->product_id.'/'.str_slug($row->name, '-').'');
        //End URL

        //Check Inventory
        $param_check['SKU']       = $row->id;
        $param_check['quantity']  = $row->qty;
        $check_inventory = CheckoutCart::checkInventory($param_check);
        $cart_content[$temp]['inv_status'] = ($check_inventory['result']) ? 1 : 2 ; //Inventory Status [1: OK | 2: Min-Stock]
        $cart_content[$temp]['inv_qty'] = $check_inventory['inventory'];
        //End Check Inventory

        $temp++;
      }
    }

    return $cart_content;
  }

  public static function addCart($params)
  {
    $status = FALSE;

    $SKU = isset($params['SKU']) ? str_replace('/', 'or', $params['SKU']) : NULL;
    $quantity = isset($params['quantity']) ? $params['quantity'] : NULL;
    $brand_id = isset($params['brand_id']) ? $params['brand_id'] : NULL; //
    $product_id = isset($params['product_id']) ? $params['product_id'] : NULL;
    $product_price = isset($params['product_price']) ? $params['product_price'] : NULL;
    $product_ori_price = isset($params['product_ori_price']) ? $params['product_ori_price'] : NULL;
    $product_sale_price = isset($params['product_sale_price']) ? $params['product_sale_price'] : NULL;
    $product_special_price = isset($params['product_special_price']) ? $params['product_special_price'] : NULL;
    $product_name = isset($params['product_name']) ? $params['product_name'] : NULL; //
    $product_weight = isset($params['product_weight']) ? $params['product_weight'] : NULL; //

    $color_category = isset($params['color_category']) ? $params['color_category'] : NULL; //
    $size_category = isset($params['size_category']) ? str_replace('/', 'or', $params['size_category']) : NULL; //
    $image_name = isset($params['image_name']) ? $params['image_name'] : NULL; //
    $brand_name = isset($params['brand_name']) ? $params['brand_name'] : NULL; //
    $variant_color_name = isset($params['variant_color_name']) ? $params['variant_color_name'] : NULL;
    $promo_id = isset($params['promo_id']) ? $params['promo_id'] : NULL;
    $promo_name = isset($params['promo_name']) ? $params['promo_name'] : NULL;

    $parent_track_sale = isset($params['parent_track_sale']) ? $params['parent_track_sale'] : NULL;
    $child_track_sale = isset($params['child_track_sale']) ? $params['child_track_sale'] : NULL;
    $product_front_end_type = isset($params['product_front_end_type']) ? $params['product_front_end_type'] : NULL;
    $product_type_url = isset($params['product_type_url']) ? $params['product_type_url'] : NULL;

    if (\Cookie::get('_ga_utmz')) {
      $cookData = json_decode(\Cookie::get('_ga_utmz'));
      $utm_source = isset($cookData[0]->utm_source) ? $cookData[0]->utm_source : '';
      $utm_medium = isset($cookData[0]->utm_medium) ? $cookData[0]->utm_medium : '';
      $utm_campaign = isset($cookData[0]->utm_campaign) ? $cookData[0]->utm_campaign : '';
    } elseif (\Session::get('utm_source') != FALSE || \Session::get('utm_medium') != FALSE || \Session::get('utm_campaign') != FALSE) {
      $utm_source = \Session::get('utm_source');
      $utm_medium = \Session::get('utm_medium');
      $utm_campaign = \Session::get('utm_campaign');
    } else {
      $utm_source = "";
      $utm_medium = "";
      $utm_campaign = "";
    }

    $rowid = Cart::search(array('id' => $SKU));
    $cart = Cart::get($rowid[0]);

    if ($cart) {
      $rowId = $cart->rowid;
      $qty = $quantity + $cart->qty;

      Cart::update($rowId, array('qty' => $qty));
      $status = TRUE;
    } else {
      Cart::add(array(
        'id' => $SKU,
        'name' => $product_name,
        'qty' => $quantity,
        'price' => $product_price,
        'options' => array(
          'brand_id' => $brand_id,
          'brand_name' => $brand_name,
          'front_end_type' => $product_front_end_type,
          'type_url' => $product_type_url,
          'product_id' => $product_id,
          'color_id' => $color_category,
          'color_name' => $variant_color_name,
          'size' => $size_category,
          'image' => $image_name,
          'weight' => $product_weight,
          'price' => $product_ori_price,
          'sale_price' => $product_sale_price,
          'special_price' => $product_special_price,
          'promo_id' => $promo_id,
          'promo_name' => $promo_name,
          'utm_source' => $utm_source,
          'utm_medium' => $utm_medium,
          'utm_campaign' => $utm_campaign,
          'parent_track_sale' => $parent_track_sale, /** For tracking sale * */
          'child_track_sale' => $child_track_sale /** For tracking sale * */
        )
      ));
      $status = TRUE;
    }

    return $status;
  }

  public static function updateCart($params) {
    //Data
    $is_delete  = isset($params['is_delete']) ? $params['is_delete'] : 0;
		$SKU        = isset($params['SKU']) ? str_replace('/', 'or', $params['SKU']) : NULL;
    $quantity   = isset($params['quantity']) ? $params['quantity'] : NULL;
    //End Data

    if($SKU == NULL){
      //SKU is empty
      return false;
    }

		$get_row_id = Cart::search(array('id' => $SKU));
		$cart       = Cart::get($get_row_id[0]);

		if ($cart) {
			$row_id = $cart->rowid;

      //Is Delete
      if($is_delete == 1){
        Cart::remove($row_id);
        return true;
      }
      //End Is Delete

      if(CheckoutCart::checkInventory($params)){
        //Inventory is sufficient
        Cart::update($row_id, array('qty' => $quantity));
        return true;
      }else{
        //Inventory is not sufficient
        return false;
      }
		} else {
      //Cart not found
			return false;
		}
	}

  public static function getMarketingCart() {
    $carts = CheckoutCart::fetchCart();
    $arr_cart = [];

    $index = 0;
    foreach($carts as $cart) {
        $arr_cart[$index]['id'] = $cart['product_id'];
        $arr_cart[$index]['price'] = $cart['price'];
        $arr_cart[$index]['quantity'] = $cart['qty'];
        $arr_cart[$index]['quantity'] = $cart['qty'];
        $arr_cart[$index]['brand'] = $cart['brand_id'];
        $arr_cart[$index]['brand-name'] = $cart['brand_name'];        
        $arr_cart[$index]['category']  = $cart['type_id_real'];
        $arr_cart[$index]['category-name']  = $cart['type_id'];       
      
        $index++;
    }

    return json_encode($arr_cart);
  }

  public static function getMarketingProductCart() {
    $carts = CheckoutCart::fetchCart();
    $arr_cart = [];

    $index = 0;
    foreach($carts as $cart) {
      $arr_cart[$index]['id'] = $cart['product_id'];
      $arr_cart[$index]['price'] = $cart['price'];
      $arr_cart[$index]['quantity'] = $cart['qty'];

      $index++;
    }

    return json_encode($arr_cart);
  }

}
