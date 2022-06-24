<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

class UserCs extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users'; //Define your table name

	protected $primaryKey = 'user_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['user_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }

}
