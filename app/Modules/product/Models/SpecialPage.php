<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class SpecialPage extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'special_page'; //Define your table name

	protected $primaryKey = 'special_page_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['special_page_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Product\Models\Model_name');
	// }

	public static function checkSpecialPageActive($special_page_id) {
		//Define Domain and Channel
        $get_domain = get_domain();
    	$domain_id = $get_domain['domain_id'];

		$special_page = Self::where('special_page_id','=',$special_page_id)
							->where('domain_id','=',$domain_id)
							->where('enabled','=',1)
							->first();

		if (empty($special_page)) {
			\Log::info('Promo Page Disable / Expired');
            abort(404); //*** Promo Page Disable / Expired error page... ***//
        }

    	return $special_page;
	}

	public static function fetch_special_page_image($special_page_id) {
		//Define Domain and Channel
        $get_domain = get_domain();
    	$domain_id = $get_domain['domain_id'];

		$special_page = \DB::table('special_page_slider_image')
								->where('special_page_id','=',$special_page_id)
								->where('enabled','=',1);

		if($domain_id == 1) {
			$special_page = $special_page->where('own_bb','=', 1);
		} elseif($domain_id == 2) {
			$special_page = $special_page->where('own_hb','=', 1);
		} else {
			$special_page = $special_page->where('own_sd','=', 1);
		}

		$special_page = $special_page->get();

    	return $special_page;
	}

}
