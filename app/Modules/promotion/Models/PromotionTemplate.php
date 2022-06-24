<?php namespace App\Modules\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\VoucherMigrate;
use \App\Modules\Promotion\Models\ActiveJob;

class PromotionTemplate extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'promotions_template'; //Define your table name

	protected $primaryKey = 'promotions_template_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['promotions_template_id']; //Define your guarded columns
  

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Promotion\Models\Model_name');
	// }
}
