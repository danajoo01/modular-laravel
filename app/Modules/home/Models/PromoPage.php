<?php namespace App\Modules\Home\Models;

use Illuminate\Database\Eloquent\Model;

class PromoPage extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'landing_page'; //Define your table name

	protected $primaryKey = 'landing_page_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['landing_page_id']; //Define your guarded columns

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
	public static function fetch_landing_page($where)
	{		
            $landing_page = Self::where('landing_page_url','=',$where['url'])
                                ->where('landing_page_status','=',1)
                                ->where('landing_page_domain_id','=',$where['domain_id'])
                                ->first();

            return $landing_page;
	}

	public static function get_top_banner_mini ($domain_id)
	{
            $landing_page = \DB::table('banner_topmini')
                                ->where('status','=',1)
                                ->where('domain_id','=',$domain_id)
                                ->orderBy('id', 'DESC')
                                ->first();

            return $landing_page;
	}

	public static function promo_special_deals_category ($domain_id)
        {
               //$category = ($domain_id == 1) ? "category_bb" : "category_hb";

               switch ($domain_id) {
                   case 1 : $category = "category_bb";
                       break;
                   case 2 : $category = "category_hb";
                       break;
                   case 3 : $category = "category_sd";
                       break;
                   default : $category = "category_bb";
               }

               $category_deals = \DB::table('special_deal_category')
                       ->where($category, '=', 1)
                       ->where('category_status', '=', 1)
                       ->orderBy('category_id', 'ASC')
                       ->get();

               $category_id = array();

               foreach ($category_deals as $data) {
                   array_push($category_id, $data->category_id);
               }

               $category_deal["data"] = $category_deals;
               $category_deal["id"] = $category_id;

               return $category_deal;
           }

           public static function promo_special_deals ($domain_id = NULL, $category_id = NULL)
	{
		if($domain_id == 1){
			$special_deal = 'special_deal_bb';
			$category 		= 'category_bb';
		}elseif($domain_id == 2){
			$special_deal = 'special_deal_hb';
			$category 		= 'category_hb';
		}else{
			$special_deal = 'special_deal_sd';
			$category 		= 'category_sd';
		}

		$special_deals = \DB::table('special_deal as A')
      ->leftJoin("special_deal_category as B", "A.special_deal_category", "=", "B.category_id")
      ->where($special_deal,'=',1)
      ->where($category,'=',1)
      ->where('special_deal_status','=',1)
      ->orderBy('special_deal_id', 'DESC');

		if(count($category_id) > 0){
			$special_deals->whereIn('special_deal_category', $category_id);
		}

		return $special_deals->get();
	}
}
