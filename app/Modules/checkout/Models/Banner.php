<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
use DB;

class Banner extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'special_ty_page'; //Define your table name

	protected $primaryKey = 'ty_page_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['primarykey']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Checkout\Models\Model_name');
	// }

 

  public static function ThankYouBanner($wheres = NULL , $type = '')
  {
   //var_dump($wheres);
   // exit;
    
     //parameters
    $datenow = date('Y-m-d H:i:s');
    $get_domain = get_domain();
    $domain_id = $get_domain['domain_id'];
    
    
    //query banner
    //DB::enableQueryLog();
    $queryBanner = DB::connection('read_mysql')->table('special_ty_page')
            ->select('ty_page_id', 'ty_page_img_web', 'ty_page_img_mob', 'ty_page_url')
            ->distinct()
            ->leftJoin('special_ty_ship', 'ty_page_id', '=', 'special_ty_ship.special_ty_ship_id_page')
            ->where('end_date','>=',$datenow)
            ->where('domain_id',$domain_id)
            //->where('domain_id',3)
            ->where('deleted_flag',1)
            ->where('status',1); 
    
    //conditions
    if(isset($wheres["ty_shipping_name"]) || isset($wheres["ty_shipping_area"])){      
      
     $tyshipname = $wheres["ty_shipping_name"];
     $tyshiparea = $wheres["ty_shipping_area"];
      
      $queryBanner->where(function ($queryParam) use ($tyshipname) {
        $queryParam->where('special_ty_ship_name_shipping', $tyshipname)
              ->orWhereNull('special_ty_ship_name_shipping');
      });
      
      $queryBanner->where(function ($queryParam) use ($tyshiparea) {
        $queryParam->where('special_ty_ship_area_shipping', $tyshiparea)
              ->orWhereNull('special_ty_ship_area_shipping');
      });      
    }
    
    
    
    //result
    $result = $queryBanner
              ->orderBy('start_date', 'asc')
              ->take(3)
              ->get();
         
    //dd(DB::getQueryLog());
    
    //var_dump($result);
    //exit;
    return $result;
  }

  

}
