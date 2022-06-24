<?php namespace App\Modules\Home\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Home extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'homepage_laravel'; //Define your table name

	protected $primaryKey = 'homepage_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['homepage_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }
	
	/** Fetch Homepage 
	*** Get Data Homepage
	*** @return data object.
	**/
	public static function fetch_home_page($where) {
		
		$homepage = Self::where('default','=',$where['default'])
							->where('domain_key','=',$where['domain_key'])
							->first();
		
		return $homepage;
	}

	public static function automate_homepage_cache($channel) {

		 //fetch home page
        $where["default"]      = 1;
        $where["domain_key"]   = $channel;

        $domain_lists 	= \Config::get('berrybenka.domains');
        $domain	 		= $domain_lists[$channel]; 
        
        $cacheName      = 'homepage-' . $domain;         
        $expiresAt      = Carbon::now()->addMinutes(60);

		// Cache::remember($cacheName, $expiresAt, function() use($where){                            
  //           return self::fetch_home_page($where);
  //       });

        $cacheValue = self::fetch_home_page($where);

        Cache::put($cacheName, $cacheValue, $expiresAt);

        $home_page      = Cache::get($cacheName);

        return $home_page;
	}
}
