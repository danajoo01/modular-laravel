<?php 
namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Checkout\Models\OrderPayment;
use \App\Modules\Checkout\Models\OrderItem;
use DB;
use Log;

class Tcash extends Model {

	public static function build_credential(){
		return [
			'terminal_id' => env('T-CASH_TERMINALID'),
			'signature' => env('T-CASH_SIGNATURE'),
			'key' => env('T-CASH_KEY'), 
			'password' => env('T-CASH_PASSSWORD'), 
			'url' =>  env('T-CASH_ENDPOINT')
		];
	}

	public static function build_domain_url($domain, $chanel){
		$domain_id = $domain;
		$channel_id = $chanel;
		$domain_url = '';

		if($domain_id == 1 && $channel_id == 1){
			$domain_url = env('BERRYBENKA');
		}else if($domain_id == 1 && $channel_id == 2){
			$domain_url = env('BERRYBENKA_MOBILE');
		}else if($domain_id == 2 && $channel_id == 1){
			$domain_url = env('HIJABENKA');
		}else if($domain_id == 2 && $channel_id == 2){
			$domain_url = env('HIJABENKA_MOBILE');
		}

		return $domain_url;
	}

	public static function generate_token($data_item, $data_header){

		$config = Self::build_credential(); 

		$path = $config['url'] . "payment";

		// $domain_url = Self::build_domain_url($data_header['create_order_header']['domain_id'], $data_header['create_order_header']['channel']); 

		$domain_url = ($data_header['create_order_header']['domain_id'] == 1 ? env('BERRYBENKA') : env('HIJABENKA'));

		// populate item
		$item = "";

		foreach ($data_item['fetch_order_item'] as $di) {
			$item .= '["' . $di->product_name . '", "' . ceil($di->item_paid_price) . '", "' . $di->quantity . '"],';
		}

		$item = "[" . rtrim($item,',') . "]";

		$grandtotal = $data_header['create_order_header']['grand_total'];
		$purchase_code = $data_header['create_order_header']['purchase_code'];

		$data = array(
			'trxId' => $purchase_code,
			'terminalId' => $config['terminal_id'],
			'userKey' => $config['key'],
			'password' => $config['password'],
			'signature' => $config['signature'],
			'total' => $grandtotal,
			'successUrl' => "https://" . $domain_url . "/checkout/tcash_success",
			'failedUrl' => "https://" . $domain_url . "/checkout/tcash_redirect",
			'items' => $item
		);

		$response = Self::Call($data, $path);

		if($response != false){
			$return = json_decode($response);
			if (strlen($return->pgpToken) > 50){
				$return->po_number = $purchase_code;
			}else{
				$return = false;
			}
		}else{
			$return = false;
		}

		return $return;
	}

	public static function getTcashSignature($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		// $signature = DB::table('tcash_signature')
		// 				->select(DB::raw('tcash_signature, tcash_refnum'))
		// 				->where('po_number', '=', $po_number)
		// 				->where('status_payment', '=', 0)
		// 				->first();
		$signature = DB::table('tcash_signature')
						->select(DB::raw('tcash_signature, tcash_refnum'))
						->where('po_number', '=', $po_number)
						->where('status_payment', '=', 0)
						->orderBy('id_tcash', 'desc')
						->first();

		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $signature;
    } 

    public static function createTcashSignature($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$order_header = array(
			'create_order_header' => self::get_data_order_header($po_number)
		);

		$order_item = array(
			'fetch_order_item' => self::get_data_order_item($po_number)
		);

		$token = self::generate_token($order_item, $order_header);

		self::insertTcashSignature($token);

		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $token;
    }

    public static function insertTcashSignature ($data = []){
      $field = array(
        'po_number' => $data->po_number,
        'tcash_signature' => $data->pgpToken,
        'tcash_refnum' => $data->refNum
      );

      $insert_signature = DB::table('tcash_signature')->insertGetId($field);
      return $insert_signature;        
  	}

    public static function get_data_order_header($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();
	    $data = DB::table('order_header')
						->select(DB::raw('purchase_code, grand_total, channel, domain_id'))
						->where('purchase_code', '=', $po_number)
						->first();
	    DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $data;
    }

    public static function get_data_order_item($po_number){
    	// DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();
	    $data = DB::table('order_item')
						->select(DB::raw('products.product_name, order_item.quantity, order_item.item_paid_price'))
						->leftJoin('products', 'order_item.product_id', '=', 'products.product_id')
						->where('order_item.purchase_code', '=', $po_number)
						->get();
	    // DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $data;
    }

	public static function check_transaction_status($ref_num){
		$config = Self::build_credential();
		$url = $config['url'] . "check/customer/transaction";

		$data = array(
			'refNum' => $ref_num,
			'terminalId' => $config['terminal_id'],
			'userKey' => $config['key'],
			'passKey' => $config['password'],
			'signKey' => $config['signature']
		);

		$response = Self::Call($data, $url);

		$data = json_decode($response);

		if(isset($data->status) && $data->status == "SUCCESS_COMPLETED"){
			return true;
		} else if (isset($data->status) && $data->status == "FAILED"){
			return false;
		}else{
			return false;
		}

	}

	/*
		@scoop = generate_token, check_transaction
	 */
	public static function Call($data, $url){                                              
        
		$endpoint = $url;
        
        Log::debug('Berrybenka requests T-Cash Request : ' . json_encode($data));

        //curl process
        try{
        	$curl           = curl_init();
	        curl_setopt($curl, CURLOPT_POST, 1);    
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
	    	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
	        curl_setopt($curl, CURLOPT_URL,$endpoint);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	                
	        $result = curl_exec($curl);        
        	curl_close($curl);
        } catch (Exception $e) {
        	$result = false;
        }       
        
        return $result;
    }

    public static function checkOrderStatus($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$data = DB::table('order_item')
						->leftjoin('order_payment', 'order_item.purchase_code', '=', 'order_payment.purchase_code')
						->select(DB::raw('order_item.purchase_code'))
						->where('order_item.purchase_code', '=', $po_number)
						->where('order_payment.status', '=', 0)
						->where('order_item.purchase_status', '=', 1)
						->where('order_item.order_status_item', '=', 0)
						->where('order_payment.master_payment_id', '=', 135)
						->get();

		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return (count($data) > 0 ? true : false);

    } 

    public static function getPoSignature($ref_num){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$signature = DB::table('tcash_signature')
						->select(DB::raw('po_number'))
						->where('tcash_refnum', '=', $ref_num)
						->where('status_payment', '=', 0)
						->first();
		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $signature;
    }

    public static function execute_success_payment($po_number, $ref_number){

    	// start transaction
		DB::beginTransaction();

		// Update status order di order payment menjadi paid.
		$update_op = array();
		$update_op['status'] = 1;
		$update_payment = OrderPayment::updateOrderPayment($po_number, $update_op);

		if(!$update_payment){
			DB::rollBack();
			$time_executed  = microtime(true) - $time_start;
  			Log::notice('########## process_verify_tcash : Failed ########## | Failed on update order_payment | Total Executed Time: '.$time_executed);

  			return false;
		}

		//Update Order Item
		$filter_oi = array();
		$filter_oi['with']  = 'purchase_code';
		$filter_oi['value'] = $po_number;

		$update_oi = array();
		$update_oi['purchase_status']   = 3;
		$update_oi['order_status_item'] = 2;
		$update_oi['approval_date'] = date("Y-m-d H:i:s");

		$update_order_item = OrderItem::updateOrderItem($filter_oi, $update_oi);		

		if(!$update_order_item){
			DB::rollBack();
			$time_executed  = microtime(true) - $time_start;
  			Log::notice('########## process_verify_tcash : Failed ########## | Failed on update order_item | Total Executed Time: '.$time_executed);

  			return false;
		}

		// update status paid
    	$update_tcash['status_payment'] = 1;
    	$update_tcash_status = Self::update_tcash_payment_status($po_number, $ref_number, $update_tcash);

    	if(!$update_tcash_status){
			DB::rollBack();
			$time_executed  = microtime(true) - $time_start;
  			Log::notice('########## process_verify_tcash : Failed ########## | Failed on update tcash_signature | Total Executed Time: '.$time_executed);

  			return false;
		}

		DB::commit();

		return true;
    }

    public static function update_tcash_payment_status($purchase_code, $ref_number, $data){
    	$update_tcash_payment_status = DB::table('tcash_signature')
		->where('po_number', $purchase_code)
		->where('tcash_refnum', $ref_number)
		->update($data);

		return $update_tcash_payment_status;
    }

    public static function get_payment_method($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$payment = DB::table('order_payment')
						->select(DB::raw('master_payment_id'))
						->where('purchase_code', '=', $po_number)
						->first();
		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $payment;
    }
}