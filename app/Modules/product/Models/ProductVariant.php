<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'product_variant'; //Define your table name

	protected $primaryKey = 'product_variant_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['product_variant_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Product\Models\Model_name');
	// }

}
