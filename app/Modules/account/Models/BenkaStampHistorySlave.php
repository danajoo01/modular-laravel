<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;
use Mail;
// use App\Jobs\BenkaStampEmailNotification;
use Request;
use \App\Modules\Checkout\Models\Order;


class BenkaStampHistorySlave extends Model {

	// use DispatchesJobs;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'benka_stamp_history_slave'; //Define your table name

	protected $primaryKey = 'id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }
	
	public function benkaStampActivation()
	{
		try{
			$credit_stamp = Self::select('*')
	            ->where('delivered_date', '<=', Carbon::now()->subDays(40))
	            ->where('type', '=', 'CR')
	            ->where('stamp_status', '=', 'pending')
	            ->get();
	        \Log::info('Process > 40 days pending stamp');

	        foreach ($credit_stamp as $credit) {
	            $debit_stamp = Self::select('*')
	                ->where('purchase_code', '=', $credit->purchase_code)
	                ->where('customer_id', '=', $credit->customer_id)
	                ->where('type', '=', 'DB')
	                ->where('stamp_status', '=', 0)
	                ->get();

	            $current_stamp = $credit->stamp_value;
	            if(!empty($debit_stamp)) {
	            	foreach ($debit_stamp as $debit) {
	            		$current_stamp = $current_stamp - $debit->stamp_value;
	            	}
	            }

	            // data utk insert ke stamp_history_master
	            $insert = array(
	            		'purchase_code'			=> $credit->purchase_code,
	            		'stamp_value'			=> $current_stamp,
	            		'current_config_val'	=> $credit->current_config_val,
	            		'delivered_date'		=> Carbon::now(),
	            		'customer_id'			=> $credit->customer_id,
	            		'customer_email'		=> $credit->customer_email,
	            		'type'					=> 'CR',
	            		'stamp_status'			=> '1',
	            		'description'			=> 'Konversi pending stamp jadi active stamp, credit active stamp - #'.$credit->purchase_code,
	            		'flag_pos'				=> 0
	            	);

	            $insert_debit = array(
	            		'purchase_code'			=> $credit->purchase_code,
	            		'stamp_value'			=> $current_stamp,
	            		'current_config_val'	=> $credit->current_config_val,
	            		'delivered_date'		=> Carbon::now(),
	            		'customer_id'			=> $credit->customer_id,
	            		'customer_email'		=> $credit->customer_email,
	            		'type'					=> 'DB',
	            		'stamp_status'			=> '0',
	            		'description'			=> 'Konversi pending stamp jadi active stamp, debit pending stamp - #'.$credit->purchase_code,
	            		'flag_pos'				=> 0
	            	);

	            $bs_email = array(
	            		'purchase_code'		=> $credit->purchase_code,
	            		'stamp_value'		=> $current_stamp,
	            		'customer_id'		=> $credit->customer_id,
	            		'customer_email'	=> $credit->customer_email
	            	);
  
	            // delete data yg udah diproses di stamp_history_slave
	            Self::where('purchase_code' ,'=', $credit->purchase_code)->delete();

	            // insert data ke stamp_history_master
	            DB::table('benka_stamp_history')->insert($insert_debit);
	            DB::table('benka_stamp_history')->insert($insert);

	            // insert ke table stamp_email
	            DB::table('benka_stamp_email_notification')->insert($bs_email);

	            // get active_stamp from table customer
	            $customer_stamp = DB::table('customer')->select('customer_id','stamp_active', 'stamp_pending')->where('customer_id', '=', $credit->customer_id)->get();
	            // add active_stamp and deduct pending_stamp from table customer
	            $total_stamp_active = $customer_stamp[0]->stamp_active + $current_stamp;
	            $total_stamp_pending = $customer_stamp[0]->stamp_pending - $current_stamp;
	            // update total stamp to customer
	            DB::table('customer')->where('customer_id', $customer_stamp[0]->customer_id)->update([
		            	'stamp_active' => $total_stamp_active,
			            'stamp_pending' => $total_stamp_pending		            
					]);
	        }

        } catch (Exception $e) {
        	\Log::info('Process benka stamp activation failed');
        }

        return "Benka stamp activation processed";        
	}

	public function benkaStampEmailNotif()
	{
		$domain                 = get_domain(); 

        $email_recipients = DB::table('benka_stamp_email_notification')
                    ->select('benka_stamp_email_notification.purchase_code', 'benka_stamp_email_notification.stamp_value', 'benka_stamp_email_notification.customer_id', 'benka_stamp_email_notification.customer_email', 'customer.customer_fname', 'customer.customer_lname', 'order_header.stamp_currency')
                    ->join('customer', 'customer.customer_id', '=', 'benka_stamp_email_notification.customer_id')
                    ->join('order_header', 'benka_stamp_email_notification.purchase_code', '=', 'order_header.purchase_code')
                    ->get();
         
        \Log::info('Fetch benka stamp email recipients');

        foreach ($email_recipients as $recipient) {   
        	if ($recipient->stamp_value > 0) {
        		$params = (array)$recipient;
				$params['from_cron'] = true;
				$params['stamp_estimate'] = $recipient->stamp_value;
				$params['stamp_type']	= 'ACTIVE';
				Order::sendMailBenkaStamp($params);
				// $mail_message = Order::setMailMessageStamp($params);
				// $recipient->sender_email = 'cs@berrybenka.com';
				// $recipient->sender_name	= strtoupper($domain['domain_name']);
	        	// Mail::queue('mailtemplates.mailtemplates_queue', ['mail_message' => $mail_message], function ($m) use ($recipient) {
		        //     $m->from($recipient->sender_email, $recipient->sender_name);
		        //     $m->to($recipient->customer_email, $recipient->customer_id)->subject('Loyalty Program - Active Benka Stamp from #'.$recipient->purchase_code);
		        // });
        	}		

	        DB::table('benka_stamp_email_notification')->where('purchase_code', '=', $recipient->purchase_code)->delete();

        }
	}
}
