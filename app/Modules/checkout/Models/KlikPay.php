<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Checkout\Models\KlikPayKeys;
use Auth;
use Cart;
use DB;

class KlikPay extends Model {
	
	/**
	 * Processing Klikpay
	 *
	 * @access public
	 * @param array     	
	 * @return array
	 */
	public static function setKlikPay($data) 
	{	
		$klikPayCode = \Config::get('berrybenka.klikpay.klikpaycode_klikpay');
    $postUrl = \Config::get('berrybenka.klikpay.post_url_klikpay');
    $clearKey = \Config::get('berrybenka.klikpay.clearkey_klikpay');
    $transactionNo = $data['order_header']['purchase_code'];
    $transactionDate = date('d/m/Y H:i:s', strtotime("now"));
    $totalAmount = $data['total']['grand_total'];
    $currency = 'IDR';

    $klikpay['postUrl'] = $postUrl;
    $klikpay['klikPayCode'] = $klikPayCode;
    $klikpay['transactionNo'] = $transactionNo;
    $klikpay['totalAmount'] = $totalAmount;
    $klikpay['currency'] = $currency;
    $klikpay['payType'] = "01";
    $klikpay['callbackUrl'] = 'checkout/final_order';
    $klikpay['transactionDateTime'] = $transactionDate;
    $klikpay['signature'] = KlikPayKeys::signature($klikPayCode, $transactionNo, $currency, $clearKey, $transactionDate, $totalAmount);
    
    return $klikpay;
	}
}
