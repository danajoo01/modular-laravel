<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'wishlist'; //Define your table name

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
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Product\Models\Model_name');
	// }
	
	/**
	** Get Count Wishlist Function
	** To count wishlist by customer_id and domain_id (berrybenka or Hijabenka)
	** @param customer_id, domain_id, product_id, product_variant_color_id
	** @result value
	** DS @ 02/2016
	*/	
	public static function get_count_wishlist($customer_id = NULL, $domain_id = NULL, $product_id = NULL,  $product_variant_color_id = NULL) {
		if ($customer_id == NULL) {
			return 0;
		}
		
		// $result = Self::select( \DB::raw("count(id) as count"))
		$result = Self::select('id')
			->where('customer_id','=', $customer_id)
			->where('domain_id','=', $domain_id);
                
		if ($product_id <> NULL) {
			$result = $result->where('product_id','=', $product_id);
		}
		
		if ($product_variant_color_id <> NULL) {
			$result = $result->where('product_variant_color_id', '=', $product_variant_color_id);
		}
		
		$result = $result->first();
		
		//bb_debug($result->count);
		
		// return $result->count;		
		return $result;		
	}
	
	/** Wishlist Query 
	*** generate wishlist query
	*** @return object.
	**/
	public static function wishListQuery($select) {
		//\DB::enableQueryLog();		
		$query = \DB::table('wishlist as W')->select(\DB::connection('read_mysql')->raw($select))
						->leftJoin("products as P", "P.product_id", "=", "W.product_id")
						->leftJoin("product_variant as PV", "P.product_id", "=", "PV.product_id")
						->leftJoin('product_image as PI', function ($join) {
									$join->on("P.product_id", "=", "PI.product_id")
										 ->on("PV.variant_color_id", "=", "PI.variant_color_id");
							})
						->leftJoin("brand as B", "P.product_brand", "=", "B.brand_id");
						
		return $query;
	}
	
	/** Fetch Wishlist 
	*** Get Data Wishlist
	*** @return data object.
	**/
  public static function fetchWishlist($where, $limit = 8) {
    $offset = 0;

    if (isset($where["data_http_get"]) && is_array($where["data_http_get"])) {
      if (isset($where["data_http_get"]['page']) && !is_null($where["data_http_get"]['page'])) {
        $offset = $where["data_http_get"]['page'];
      }
    }
    
    $core_selector  = getCoreSelector("product_detail");
    
    switch ($where['domain_id']) {
      case '1' : 
        $fq["default_bb"] = 1;
        break;

      case '2' : 
        $fq["default_hb"] = 1;
        break;
      
      case '3' : 
        $fq["default_sd"] = 1;
        break;
    }

    $raw_query = \DB::connection('read_mysql')->table('wishlist')
      ->select("wishlist.product_id")
      ->leftJoin('products', 'products.product_id', '=', 'wishlist.product_id')
      ->where('wishlist.customer_id', '=', $where['customer_id'])
      ->where('wishlist.domain_id', '=', $where['domain_id'])
      ->where('products.product_status', '=', 1)
      ->orderBy('wishlist.created_date', 'desc');
    
    $total_query  = $raw_query->count();
    $query        = $raw_query
      ->skip($offset)->take($limit)
      ->get();

    $data = array();

    if (!empty($query)) {
      foreach ($query as $pid) {
        $product_id[] = $pid->product_id;
      }

      $wishlist = [];
      $pid      = [];

      if (isset($product_id) && !empty($product_id)) {
        $pid = implode("+OR+", $product_id);
        $fq["pid"] = '(' . $pid . ')';

        $total = get_active_solr($core_selector, $query = null, $fq, 1000, 0, $order = null, $group = "pid");

        $pid = array();

        if (isset($total->numFound) && $total->numFound > 0) {
          foreach ($total->docs as $data_pid) {
            if (in_array($data_pid->pid, $product_id)) {
              $key            = array_search($data_pid->pid, $product_id);
              $wishlist[$key] = $data_pid;
              $pid[$key]      = $data_pid->pid;
            }
          }
        }
      }

      ksort($wishlist);

      $data["wishlist"] 	= $wishlist;
      $data["pid_data"]   = isset($pid) ? $pid : array();
      $data["start"]      = $offset;
      $data["total"]      = isset($total_query) ? $total_query : 0;
    }

    return $data;

    // bb_debug($wishlist);
    // bb_debug(\DB::getQueryLog());
    // exit;		
  }

      /** Get Wishlist Product ID
	*** Get Data Wishlist
	*** @return data object.
	**/
	public static function getProductIdWishlist($customer_id,$domain_id) {
		$result = array();
		$where['customer_id'] 	= $customer_id;
		$where['domain_id'] 	= $domain_id;
		$get_data = Self::fetchWishlistID($where);
		if ($get_data)
		{
			foreach($get_data as $row)
			{
				array_push($result, (int) $row->product_id);
			}
		}
		
		return $result;
		
	}

	/** Fetch Wishlist ID
	*** Get Data Wishlist minimal data
	*** @return data object.
	**/
	public static function fetchWishlistID($where,$limit=NULL) {
		$array_status = array(1,2);

		$wishlist = \DB::table('wishlist as W')->select(\DB::connection('read_mysql')->raw("P.product_id, P.product_name"))
      ->leftJoin("products as P", "P.product_id", "=", "W.product_id");
		
		switch ($where['domain_id']) {
			case '1'	: 	
        $default  = 'default_bb';
        $own      = 'own_bb';	
        $wishlist = $wishlist->leftJoin("front_end_type as FT", "P.front_end_type", "like", \DB::raw("CONCAT('%,', FT.type_id, ',%')"))
          ->where('FT.type_owner','=',1);							
        break;
			case '2'	: 	
        $default  = 'default_hb';
        $own      = 'own_hb';
        $wishlist = $wishlist->leftJoin("type as T", "P.product_type_hb", "=", "T.type_id");												
        break;
      case '3'	: 	
        $default  = 'default_sd';
        $own      = 'own_sd';
        $wishlist = $wishlist->leftJoin("front_end_type as FT", "P.front_end_type", "like", \DB::raw("CONCAT('%,', FT.type_id, ',%')"))
          ->where('FT.type_owner','=',4);													
        break;
		}
		
		$wishlist = $wishlist->where('W.customer_id','=',$where['customer_id'])
      ->where('P.'.$own,'=',1)							
      ->whereIn('P.product_status',$array_status)
      ->groupBy('W.product_id')
      ->orderBy('W.created_date', 'desc');
		
		if ($limit == NULL) {
			$wishlist = $wishlist->get();
		} else {
			$wishlist = $wishlist->paginate($limit);
		}
		
		return $wishlist;
	}
}
