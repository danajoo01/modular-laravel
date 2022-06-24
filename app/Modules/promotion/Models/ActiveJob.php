<?php namespace App\Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveJob extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'active_jobs'; //Define your table name

	// protected $primaryKey = 'key_id'; //Define your primarykey

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
	// 	return $this->belongsTo('App\Modules\Promotion\Models\Model_name');
	// }

}
