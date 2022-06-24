<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\KlikPayKeys;
use Auth;
use Cart;
use DB;

class Sprintasia extends Model {
	
	/**
	 * Processing Klikpay
	 *
	 * @access public
	 * @param string $create_purchase_code        	
	 * @return string
	 */
	public static function processing_klikpays($codePurchase, $new_checkout=NULL) 
	{	
		$clearKey     = env('CLEARKEY_KLIKPAY');
        $klikPayCode  = env('KLIKPAYCODE_KLIKPAY');
        $postUrl      = env('POST_URL_KLIKPAY');
		 
		$klikPayCallback = "" . site_url () . "checkout/final_order";
		
		$transactionNo = $codePurchase;
		$totalAmount = $get_grand_total ['total'];
		$currency = "IDR";
		$payType = "01";
		$callbackUrl = $klikPayCallback;
		$transactionDateTime = date ( 'd/m/Y H:i:s', strtotime ( "now" ) );
		$klikpay_TransactionDateTime = date ( 'Y-m-d H:i:s', strtotime ( "now" ) );
		
		$klikpay = new KlikPayKeys ();
		$signature = $klikpay->signature ( $klikPayCode, $transactionNo, $currency, $clearKey, $transactionDateTime, $totalAmount );
		
		if (strstr ( $signature, "." )) {
			$signature = explode ( ".", $signature );
			$signature = $signature [0];
		}
		$authKey = $klikpay->authkey ( $klikPayCode, $transactionNo, $currency, $transactionDateTime, $clearKey );
		
		$data = array (
				'signature' => $signature,
				'authKey' => $authKey 
		);
		
		return $data;
	}
}
