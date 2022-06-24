<?php 

namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;

class Gopay extends Model {

	public static function checkOrder($po_number, $user){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$data = DB::table('order_item')
						->leftjoin('order_payment', 'order_item.purchase_code', '=', 'order_payment.purchase_code')
						->leftjoin('order_header', 'order_item.purchase_code', '=', 'order_header.purchase_code')
						->select(DB::raw('order_item.purchase_code'))
						->where('order_item.purchase_code', '=', $po_number)
						->where('order_header.customer_id', '=', $user)
						->where('order_payment.status', '=', 0)
						->where('order_item.purchase_status', '=', 1)
						->where('order_item.order_status_item', '=', 0)
						->where('order_payment.master_payment_id', '=', 343)
						->get();

		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return (count($data) > 0 ? true : false);

    } 

    public static function checkOrderFinish($po_number, $user){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$data = DB::table('order_item')
						->leftjoin('order_payment', 'order_item.purchase_code', '=', 'order_payment.purchase_code')
						->leftjoin('order_header', 'order_item.purchase_code', '=', 'order_header.purchase_code')
						->select(DB::raw('order_item.purchase_code'))
						->where('order_item.purchase_code', '=', $po_number)
						->where('order_header.customer_id', '=', $user)
						->where('order_payment.status', '=', 1)
						->where('order_item.purchase_status', '=', 3)
						->where('order_item.order_status_item', '=', 2)
						->where('order_payment.master_payment_id', '=', 343)
						->get();

		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return (count($data) > 0 ? true : false);

    } 

    public static function getNotif($po_number){
    	DB::setFetchMode(\PDO::FETCH_ASSOC);
	    DB::enableQueryLog();

		$notif = DB::table('veritrans_notifications')
						->select(DB::raw('veritrans_post'))
						->where('mStatus', '=', 'pending')
						->where('orderId', '=', $po_number)
						->first();
		DB::setFetchMode(\PDO::FETCH_CLASS);
	    \Log::notice(DB::getQueryLog());

	    return $notif;
    }

}