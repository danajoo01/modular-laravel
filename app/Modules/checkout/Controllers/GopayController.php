<?php 

namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \App\Modules\Checkout\Models\Gopay;

use Auth;

class GopayController extends Controller 
{

	public function qrcode(Request $request){
		
		$po_number = $request->get('po');

		if(!Auth::check()) {
			if(isset($po_number) || $po_number != ""){
				$redirect = 'login/?continue='.urlencode('/checkout/gopay/qrcode?po='.$po_number);
			}else{
				$redirect = 'login';
			}

	      	return redirect($redirect);
	    }

	    // set default value
	    $status = 0;
	    $qr_code = '';

		if(isset($po_number) || $po_number != ""){
			
			// Pastikan order tersebut hanya milik user session
			// order_payment->status = 0 (New)
			// order_item->purchase_status =  1 (request)
			// order_item->order_status_item = 0 (new)
			// order_payment->master_payment_id = 343 (gopay)
			
			$auth = Auth::user();
			$check_order = Gopay::checkOrder($po_number, $auth->customer_id);
			
			if($check_order){
				// Get QR URL di veritrans notification
				$notifData = Gopay::getNotif($po_number);
				
				if(isset($notifData['veritrans_post'])){
					$status = 1;
					$qr_code = self::cleanGopay($notifData['veritrans_post']);
				}
			}
		}
		
		$check_finish_order = Gopay::checkOrderFinish($po_number, $auth->customer_id);
		
		if($check_finish_order){
			$status = 2;
		}
		
		$data = array(
			"status" => $status,
			"qr_url" => $qr_code,
			"po_number" => $po_number
		);
		
		return get_view('checkout', 'checkout.gopay-qrcode', $data);
	}

	public static function cleanGopay($param = ''){
		$json = explode("|", $param);
		$json = $json[0];
		$json = json_decode($json);
		$json = json_decode($json);

		foreach ($json->actions as $v) {
			if($v->name == 'generate-qr-code'){
				return $v->url;
			}
		}
	}

}