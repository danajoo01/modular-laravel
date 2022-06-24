<?php namespace App\Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\VoucherCodeMigrate;
use \App\Modules\Promotion\Models\ActiveJob;

class Promotion extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'promotions_code'; //Define your table name

	protected $primaryKey = 'promotions_code_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['promotions_code_id']; //Define your guarded columns
  

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Promotion\Models\Model_name');
	// }
}
