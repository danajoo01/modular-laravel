<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerCreditHistory extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'customer_credit_history'; //Define your table name

	protected $primaryKey = 'credithistory_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['credithistory_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }
	
	/** Fetch Credit History 
	*** Get Data Credit History
	*** @return data object.
	**/
	public static function fetchCustomerCreditHistory($where,$limit=20) {
		
		$credit_history = Self::where('customer_id','=',$where['customer_id'])
								->orderBy('credithistory_date','desc')
								->paginate($limit);
		
		return ($credit_history->total()>0)?$credit_history:NULL;
	}
}
