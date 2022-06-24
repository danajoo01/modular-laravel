<?php 

namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\Tcash;
use \App\Modules\Checkout\Models\OrderHeader;
use \App\Modules\Checkout\Models\OrderDiscount;
use \App\Modules\Checkout\Models\Order;

use Illuminate\Http\Request;

use Auth;

class TcashController extends Controller {

	/*
		status 0 = Param View error tidak ada PO Number
		status 1 = Param View Tcash pending
		status 2 = Param view tidak ada order tcash yang pending
		status 3 = Param view Thanks page pembayaran tcash sukses
	 */
	
	public function debug(Request $request){
		Tcash::check_transaction_status("berry18071817065109312");
	}
	
	public function redirect(Request $request) {

		if(!Auth::check()) {
	      return redirect('login/?continue='.urlencode('/checkout/cart'));
	    }

		$po_number = $request->get('trxId');

		if($po_number != ""){
			$check_status = Tcash::checkOrderStatus($po_number);

			if($check_status){
				$data["status"] = 1;
				$data["po_number"] = $po_number;
				// $get_signature = Tcash::getTcashSignature($po_number);
				$get_signature = Tcash::createTcashSignature($po_number);

				$data['tcash_signature'] = (isset($get_signature->pgpToken) ? $get_signature->pgpToken : '');
				$data['tcash_webcheckout'] = env('T-CASH_WEBCHECKOUT');

			}else{
				$data["status"] = 2;
			}

		}else{
			$data["status"] = 0;
		}

		return get_view('checkout', 'checkout.tcash-landing', $data);
	}

	public function success(Request $request){

		// if(!Auth::check()) {
	 //      return redirect('login/?continue='.urlencode('/checkout/cart'));
	 //    }

		$ref_number = $request->get('refNum');

		// Get PO number dari DB, karena kalo ambil dari url bakal ada security issue.
		$get_po_number = Tcash::getPoSignature($ref_number);

		if(!empty($get_po_number) > 0){

			// cek status order, jika purchase status =1, order_item_status = 0 == TRUE
			$po_number = $get_po_number['po_number'];
			$check_status = Tcash::checkOrderStatus($po_number);

			if($check_status){

				// cek ke endpoint tcash jika response SUCCESS_COMPLETED, return TRUE
				$check_transaction_status = Tcash::check_transaction_status($ref_number);

				if($check_transaction_status){
					
					// Update order status with commit - rollback
					$execute_update_payment = Tcash::execute_success_payment($po_number, $ref_number);

					if($execute_update_payment){

						$get_domain = get_domain();

						$data["status"] = 3;
						$data["po_number"] = $po_number;
						$data["domain_name"] = strtoupper($get_domain['domain_name']);

						$this->sendmail($po_number);
					}else{
						$data["status"] = 4;
					}
				}else{
					return redirect('checkout/tcash_redirect?trxId='.$po_number);
				}
				
			}else{
				$data["status"] = 2;
			}

		}else{
			$data["status"] = 2;
		}

		return get_view('checkout', 'checkout.tcash-landing', $data);
	}

	public function sendmail($purchase_code){

		//Fetch Order Header
	    $param_oh['purchase_code'] = $purchase_code;
	    $fetch_order_header = OrderHeader::fetchOrderHeader($param_oh);

	    //Fetch Discount
	    $param_discount['purchase_code'] = $purchase_code;
	    $get_discount = OrderDiscount::getDiscount($param_discount);

	    // Fetch Payment Methode
	    $payment = Tcash::get_payment_method($purchase_code);
	    $payment_method = $payment['master_payment_id'];

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
		$mail_data['veritrans_payment_code']  = NULL;
		$mail_data['veritrans_va_number']     = NULL;
		$mail_data['email_banner']            = "";
		$mail_data['kredivo_url']             = NULL;

		Order::sendMail($mail_data);  

	}

}