<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Log;

class SolrSync extends Model {

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

	/****
  	** Update default variant in SOLR
	** @return response.
	***/
	public static function updateSolr(array $param)
	{
		Log::notice('Process Update SOLR: Started');

		if($param["sku"] == NULL){
		  Log::notice('Process Update SOLR: SKU is NULL');
		  return false;
		}

		$get_domain                   = get_domain();
    $core_selector                = getCoreSelector("products");
    $core_product_detail          = getCoreSelector("product_detail");
    $core_product_images          = getCoreSelector("product_images");
    $core_product_special_variant = getCoreSelector("product_special_variant");
    $core_products_special        = getCoreSelector("products_special");
    $core_special_page            = getCoreSelector("special_page");
    
		if($get_domain["domain_id"] == 1){
		  $default  = "default_bb";
		}elseif($get_domain["domain_id"] == 2){
		  $default  = "default_hb";
		}else{
		  $default  = "default_sd";
		}

		//update core product_detail
		$param["url"]                     = solr_site().$core_product_detail."/update?wt=json";
		$response["core_product_detail"]  = Self::solr_update($core_product_detail, $param);

		if(!isset($param[$default]))
		{
			//update core products
			$param["url"]                 = solr_site().$core_products."/update?wt=json";
			$response["update_products"]  = Self::solr_update($core_products, $param);

			//update core product_images
			$param["url"]                     = solr_site().$core_product_images."/update?wt=json";
			$response["core_product_images"]  = Self::solr_update($core_product_images, $param);

			//update core product_special_variant
			/*$param["url"]                               = solr_site().$core_product_special_variant."/update?wt=json";
			$response["core_product_special_variant"]   = Self::solr_update($core_product_special_variant, $param);

			//update core product_special
			$param["url"]                       = solr_site().$core_products_special."/update?wt=json";
			$response["core_products_special"]  = Self::solr_update($core_products_special, $param);

			//update core special_page
			$param["url"]                   = solr_site().$core_special_page."/update?wt=json";
			$response["core_special_page"]  = Self::solr_update($core_special_page, $param);*/
		}

		Log::notice('Process Update SOLR: Success.');
		  
		return $response;
	}

	public static function solr_update ($core,$params = array()) {
        Log::notice('Process Update SOLR Core '.$core.': Started');

        if($params["sku"] == NULL){
          Log::notice('Process Update SOLR Core '.$core.': SKU is NULL');
          return false;
        }

        $get_domain = get_domain();

        switch ($get_domain['domain_id']) {
          case '1':
                     $default       = "default_bb";
                    break;
          case '2':
                     $default       = "default_hb";
                    break;
          case '3':
                     $default       = "default_sd";
                    break;
        }

        if($core != 'product_images' && $core != 'product_images_hb')
        {
            $fq_arr['eksklusif_in_promo'] = 0;
        }
        
        $fq_arr['product_status']     = 1;

        if($core == 'product_detail' || $core == 'product_detail_hb')
        {
            $fq_arr['product_sku']    = $params["sku"];
        }
        else
        {
            $fq_arr['pid']            = $params["pid"];
        }

        $update = get_active_solr($core, null, $fq_arr, null, null, null, null)->docs;

        $docs = array();

        if(!empty($update))
        {
          $update = object_to_array($update);
          
          foreach ($update as $key => $value)
          {
            $update_field = $value;

            if(isset($params['quantity']))
            {
                $update_field['inventory'] = $update_field['inventory'] - $params['quantity'];

                if($update_field['inventory'] <= 0)
                {
                  $update_field['product_status'] = 2;
                }
            }
            else
            {
                $update_field[$default] = $params[$default];
            }
            
            unset($update_field['_version_']);
            //replace current data
            $docs = $update_field;
          }
        }

        $data = array(
            "add" => array( 
                "doc" => $docs,
                "boost" => 1.0,
                "overwrite"     => true,
                "commitWithin"  => 1000
            ),
        );

        $json_data = json_encode($data);

        Log::notice('Update SOLR URL '.$params['url'].': Data.'.$json_data);

        $ch = curl_init($params['url']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        $response = curl_exec($ch);
        curl_close($ch);

        Log::notice('Process Update SOLR Core '.$core.': Success.'.$response);

        return $response;
    }

}
