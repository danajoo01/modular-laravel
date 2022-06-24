<?php namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\CheckoutCart;

use \Illuminate\Support\Facades\Cache;

use Cart;

use Auth;

use Illuminate\Http\Request;

class CartController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
    if (!Auth::check()) {
      return redirect('login/?continue='.urlencode('/checkout/cart'));
    }
       
    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    $channel    = $get_domain['channel'];
    $source     = ($channel == 1 || $channel == 3 || $channel == 5) ? 'desktop' : 'mobile' ;
    
    //Clear Session     
    session()->forget('voucher');
    session()->forget('voucher_code');
    session()->forget('freegift_auto');
    session()->forget('freegift');
    session()->forget('payment_method');
    session()->forget('benka_point');
    
    session()->forget('bin_number_raw');
    session()->forget('bin_name');
    session()->forget('bin_month');
    session()->forget('bin_year');
    session()->forget('bin_cvv');
    
    session()->forget('bin_number_mandiri_raw');
    session()->forget('bin_name_mandiri');
    session()->forget('bin_month_mandiri');
    session()->forget('bin_year_mandiri');
    session()->forget('bin_cvv_mandiri');
    //End Clear Session
    
    $marketing_data = CheckoutCart::getMarketingCart();
    $bank_promo = Cache::remember('cache-bank-promo-'.$domain_id, 60, function() {
      return CheckoutCart::fetchBankPromo();
    });

    $data['title']          = "Cart";
    $data['bank_promo']     = $bank_promo;
    $data['fetch_cart']     = ($source == 'desktop') ? '' : CheckoutCart::fetchCart(); //If desktop fetch cart using AJAX.
    $data['marketing_data'] = $marketing_data;
    $data['grand_total']    = Cart::total();
        
    return get_view('checkout', 'checkout.cart', $data);
	}

  //Function
  public function updateCart(Request $request)
  {
    $params['SKU']        = str_replace('or', '/', $request->get('SKU'));
    $params['is_delete']  = $request->get('is_delete');
    $params['quantity']   = $request->get('quantity');
    $update_cart = CheckoutCart::updateCart($params);

    if(!$update_cart){
      \Session::flash('cart_error_message', 'Maaf stock barang dengan SKU '.$params['SKU'].' tidak mencukupi');
    }

    return redirect('checkout/cart');
  }
  //End Function

  //JSON Function
  public function jsonLoadCart()
  {
    $cart_content       = CheckoutCart::fetchCart();

    $json['cart']       = $cart_content;
    $json['total_cart'] = Cart::count();

    return json_encode($json);
  }

  public function jsonUpdateCart(Request $request)
  {
    $sku        = str_replace('or', '/', $request->get('SKU'));
    $quantity   = $request->get('quantity');
    $is_delete  = $request->get('is_delete');
        
    $params['SKU']        = $sku;
    $params['is_delete']  = $is_delete;
    $params['quantity']   = ($quantity >= 1) ? $quantity : 1 ;
    $update_cart = CheckoutCart::updateCart($params);

    $json['result'] = $update_cart;

    return json_encode($json);
	}
  //End JSON Function
}
