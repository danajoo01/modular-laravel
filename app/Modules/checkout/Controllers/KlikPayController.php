<?php namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\CheckoutCart;

use Cart;

use Illuminate\Http\Request;

class KlikPayController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    $channel = $get_domain['channel'];
    $source = ($channel == 1 || $channel == 3 || $channel == 5) ? 'desktop' : 'mobile' ;

		$data['title'] = "KlikPay";
    $data['bank_promo'] = CheckoutCart::fetchBankPromo();
    $data['fetch_cart'] = ($source == 'desktop') ? '' : CheckoutCart::fetchCart() ; //If desktop fetch cart using AJAX.
    $data['grand_total'] = Cart::total();

    return get_view('checkout', 'checkout.cart', $data);
	}
}
