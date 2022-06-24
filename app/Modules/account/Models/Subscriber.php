<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'new_subscriber'; //Define your table name

	protected $primaryKey = 'subscriber_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['subscriber_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }

	/**
	 * Check New_Subscriber exist
	 *
	 * @access public
	 * @param string $subscriber_email
	 * @return array of object(brand object)
	 */
	public static function check_new_subscriber($subscriber_email) {
		$get_domain = get_domain();
    $channel    = $get_domain['channel'];
    $domain     = $get_domain['domain'];
    $domain_id  = $get_domain['domain_id'];

    $subcriber = Self::where('subscriber_email','=',$subscriber_email);

    if($domain_id == 1)	{
      $subcriber = $subcriber->where('has_subcribe_bb','=', 1);
    } elseif($domain_id == 2) {
      $subcriber = $subcriber->where('has_subcribe_hb','=', 1);
    } else {
      $subcriber = $subcriber->where('has_subcribe_sd','=', 1);
    }

    $subcriber = $subcriber->first();

    return $subcriber;
	}

	/**
	 * Create New_Subscriber
	 *
	 * @access public
	 * @param array $insert
	 * @return int $insert_id
	 */
	public static function create_new_subscriber($insert) {
		$get_domain = get_domain();
    $channel    = $get_domain['channel'];
    $domain     = $get_domain['domain'];
    $domain_id  = $get_domain['domain_id'];

    if($domain_id == 1) {
			$subs     = 'subscribe_date_bb';
			$source   = 'utm_source_bb';
			$medium   = 'utm_medium_bb';
			$campaign = 'utm_campaign_bb';
			$referrer = 'referrer_bb';
			$status   = 'subs_status_bb';
			$has      = 'has_subcribe_bb';
		} elseif($domain_id == 2) {
			$subs     = 'subscribe_date_hb';
			$source   = 'utm_source_hb';
			$medium   = 'utm_medium_hb';
			$campaign = 'utm_campaign_hb';
			$referrer = 'referrer_hb';
			$status   = 'subs_status_hb';
			$has      = 'has_subcribe_hb';
		} else {
			$subs     = 'subscribe_date_sd';
			$source   = 'utm_source_sd';
			$medium   = 'utm_medium_sd';
			$campaign = 'utm_campaign_sd';
			$referrer = 'referrer_sd';
			$status   = 'subs_status_sd';
			$has      = 'has_subcribe_sd';
		}

		$insert_id = NULL;

		$subcriber 						= new Subscriber();
		$subcriber->subscriber_email 	= isset($insert['subscriber_email']) ? $insert['subscriber_email'] : NULL;
		$subcriber->subscriber_telp		= isset($insert['subscriber_telp']) ? $insert['subscriber_telp'] : NULL;
		$subcriber->subscribe_date  	= isset($insert['subscribe_date']) ? $insert['subscribe_date'] : NULL;
		$subcriber->$subs 				= isset($insert['subscribe_date']) ? $insert['subscribe_date'] : NULL;
		$subcriber->utm_source_hb 		= isset($insert['utm_source']) ? $insert['utm_source'] : NULL;
		$subcriber->$source 			= isset($insert['utm_source']) ? $insert['utm_source'] : NULL;
		$subcriber->utm_medium_hb		= isset($insert['utm_medium']) ? $insert['utm_medium'] : NULL;
		$subcriber->$medium 			= isset($insert['utm_medium']) ? $insert['utm_medium'] : NULL;
		$subcriber->utm_campaign 		= isset($insert['utm_campaign']) ? $insert['utm_campaign'] : NULL;
		$subcriber->$campaign 			= isset($insert['utm_campaign']) ? $insert['utm_campaign'] : NULL;
		$subcriber->referrer 			= isset($insert['referrer']) ? $insert['referrer'] : NULL;
		$subcriber->$referrer 			= isset($insert['referrer']) ? $insert['referrer'] : NULL;
		$subcriber->host_name	 		= isset($insert['host_name']) ? $insert['host_name'] : NULL;
		$subcriber->first_name	 		= isset($insert['first_name']) ? $insert['first_name'] : NULL;
		$subcriber->last_name	 		= isset($insert['last_name']) ? $insert['last_name'] : NULL;
		$subcriber->subs_status 		= isset($insert['subs_status']) ? $insert['subs_status'] : NULL;
		$subcriber->$status 			= isset($insert['subs_status']) ? $insert['subs_status'] : NULL;
		$subcriber->registration_date	= isset($insert['registration_date']) ? $insert['registration_date'] : NULL;
		$subcriber->count_buy			= isset($insert['count_buy']) ? $insert['count_buy'] : NULL;
		$subcriber->has_subcribe		= isset($insert['has_subscribe']) ? $insert['has_subscribe'] : NULL;
		$subcriber->$has 				= isset($insert['has_subscribe']) ? $insert['has_subscribe'] : NULL;
		$subcriber->has_survey			= isset($insert['has_survey']) ? $insert['has_survey'] : NULL;
		$subcriber->subscriber_gender	= isset($insert['subscriber_gender']) ? $insert['subscriber_gender'] : NULL;
		$subcriber->city				= isset($insert['subscriber_city']) ? $insert['subscriber_city'] : NULL;
		$subcriber->campaign_page		= isset($insert['campaign_page']) ? $insert['campaign_page'] : NULL;
		$subcriber->campaign_gender		= isset($insert['campaign_gender']) ? $insert['campaign_gender'] : NULL;
        if ($subcriber->save()) {
        	$insert_id = $subcriber->subscriber_id;
        }

        return $insert_id;

	}

	/**
	 * Check New_Subscriber exist for campaign
	 *
	 * @access public
	 * @param string $subscriber_email
	 * @return array of data
	 */
	public static function check_new_campaign_subscriber($subscriber_email) {
		$get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];

        $subcriber = Self::where('subscriber_email','=',$subscriber_email);
       
        $subcriber = $subcriber->first();

        return $subcriber;
	}

	/**
	 * Update exist Subscriber
	 *
	 * @access public
	 * @param array $data
	 * @param array $where
	 * @return boolean true or false
	 */
	public static function update_subscriber($data, $where)
	{
		$update_subcriber = Self::where('subscriber_email', '=', $where['subscriber_email'])
						          ->update($data);

        return $update_subcriber;
	}

}
