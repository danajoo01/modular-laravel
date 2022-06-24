<?php namespace App\Modules\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cache;

class Seo extends Model {

	/**
	 * Get data from SEO [solr]
	 *
	 * @var string
	 */	
        
        //model getSeo
        public static function getSeo($segment1 = '', $segment2 = '', $segment3 = '', $type = '', $domain_id = 1)
        {
            $define_domain    = get_domain();
            $domain_id        = $define_domain['domain_id'];
            $fq_arr           = array();
            $query            = null;                              

            if(!empty($segment1)){
              $fq_arr['segment_1'] = $segment1;    
            }
            if(!empty($segment2)){
              $fq_arr['segment_2'] = $segment2;    
            }
            if(!empty($segment3)){
              $fq_arr['segment_3'] = $segment3;    
            }

            $fq_arr['domain_id'] = urlencode("(5 ".$domain_id.")"); // 5 = multidomain, if domain id 5 found in solr then fetch data directly

            $solr_param['core_selector']  = getCoreSelector('seo');
            $solr_param['query']          = isset($query) ? $query : null;
            $solr_param['where']          = isset($fq_arr) ? $fq_arr : null;          
            $solr_param['limit']          = 1;
            $solr_param['offset']         = 0;
            $solr_param['order']          = NULL;
            $solr_param['group']          = NULL;
            $solr_param['field_list']     = NULL;
          
            $seo_detail     = get_active_solr($solr_param['core_selector'],$solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);               
            
            $result    = NULL;
            if(isset($seo_detail->docs[0]) && !empty($seo_detail->docs[0])){
                $result = $seo_detail->docs[0];
            }
            
            // with database
//            $querySeo = DB::connection('read_mysql')->table('seo')
//                    -> select('title', 'meta_keywords', 'meta_description')
//                    -> where('domain_id',$domain_id);                     
//          
//            //Conditions
//            $querySeo 
//                -> where('segment_1' , ($segment1) ? $segment1 : NULL)
//                -> where('segment_2' , ($segment2) ? $segment2 : NULL)
//                -> where('segment_3' , ($segment3) ? $segment3 : NULL);
//
//            //Result
//            $result = $querySeo->first();
            // end with database
          
            return $result;
        }

}
