<?php namespace App\Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Model;

class GiftCondition extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'gift_condition'; //Define your table name

	protected $primaryKey = 'gift_condition_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['gift_condition_id']; //Define your guarded columns
  

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Promotion\Models\Model_name');
	// }

}
