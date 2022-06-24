<?php namespace App\Modules\Campaign\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'campaign_page'; //Define your table name

	protected $primaryKey = 'campaign_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['campaign_id']; //Define your guarded columns

	public static function fetch_campaign_page($where = NULL)
	{
		$campaign = Self::where('url_name','=',$where['url_name'])
							->where('status','=',$where['status'])
							->first();
	
		return $campaign;
	}

}
