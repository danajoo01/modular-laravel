<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVisit extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'product_visit'; //Define your table name

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

	public static function insertVisit($data = []) {

        $insert_visit = new ProductVisit();
        $insert_visit->product_id    = $data['product_id'];
        $insert_visit->domain_id     = $data['domain_id'];
        $insert_visit->channel       = $data['channel'];
        $insert_visit->page_referrer = $data['page_referrer'];
        $insert_visit->date          = date('Y-m-d H:i:s');
        $insert_visit->save();

        return $insert_visit;
    }
}
