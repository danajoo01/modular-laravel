<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

/**
* Custom debug 
* @author Irfan Fauzan
*/
if (! function_exists('bb_debug')) {
    function bb_debug($data)
    {
        echo'<pre>';
        print_r($data);
        echo'</pre>';
    }
}

/**
* get selected price
* @author Rian Eka Cahya
*/
if (! function_exists('get_selected_price')) {
    function get_selected_price($range)
    {
        if(isset($_GET['sprice'])){
            $url = $_GET['sprice'];
        
            $url = explode('|', $url);

            if(in_array($range, $url)){
                return true;
            }else{
                return false;
            }
        }
    }
}

/**
* get_domain
* Menampilkan list domain sesuai dengan servernamen-nya
* @author Irfan Fauzan
*/
if (! function_exists('get_domain')) {
    function get_domain()
    {
      $server_name  = \Request::server('SERVER_NAME'); //localhost
      // $server_name  = "berrybenka.local"; //localhost
      $domain_lists = \Config::get('berrybenka.domains');

      $key = array_search($server_name, $domain_lists);
      //$key = 1;
      if (empty($key)) {
        //return "Domain is not defined!";
        $data['domain_name']  = 'berrybenka';
        $data['domain_alias'] = 'bb';
        $data['domain_id']    = 1;
        return $data;
      }

      $data = array();
      $data['channel'] = $key;
      $data['domain'] = $domain_lists[$key];

      if ($key == 1 || $key == 2) {
        // DOMAIN BERRYBENKA
        $data['domain_name']  = 'berrybenka';
        $data['domain_alias'] = 'bb';
        $data['domain_id']    = 1;
      } elseif ($key == 3 || $key == 4) {
        // DOMAIN HIJABENKA
        $data['domain_name']  = 'benka';
        $data['domain_alias'] = 'hb';
        $data['domain_id']    = 2;
      } else {
        // DOMAIN SHOPDECA
        $data['domain_name']  = 'shopdeca';
        $data['domain_alias'] = 'sd';
        $data['domain_id']    = 3;
      }

      return $data;
    }

  }

/**
* get_uri 
* Menampilkan uri
* @author Irfan Fauzan
*/
if (! function_exists('get_uri')) {
    function get_uri($full = false)
    {
        $server_name = \Request::server('SERVER_NAME');
        // $server_name = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if($full == true) {
            // $url = \Request::getUri();
            //$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $url = \Request::fullUrl();
            //$result = substr($url, strlen($server_name) + 7);
            if(\Request::secure()) {
                $result = substr($url, strlen($server_name) + 8);
            } else { 
                $result = substr($url, strlen($server_name) + 7);
            }
        } else {
            $url = \Request::url();
            if(\Request::secure()) {
                $result = substr($url, strlen($server_name) + 8);
            } else { 
                $result = substr($url, strlen($server_name) + 7);
            }
            //$result = substr($url, strlen($server_name) + 7);    
        }
        
        return $result;
    }
}

/**
 * Return HTTP GET variable
 *
 * array['price'] array Defines the value to be str 'asc' or 'desc' if exist in URL.
 * array['pn'] array Defines the value to be str 'asc' or 'desc' if exist in URL.
 * array['popular'] array Defines the value to be str 'asc' or 'desc' if exist in URL.
 * array['sprice'] array Defines the value to be str (0-4188) in K if exist in URL.
 * @return array
 */
if(! function_exists("generate_get_uri") ) {
    function generate_get_uri() 
    {
        $data_param = null;
        //Define URI Segment
        $get_uri = get_uri(true);                
        
        try {
            $url = array_filter(explode('?',$get_uri));//bb_debug($url);
                   
            if (is_array($url) && count($url) == 2) {
                $parse_url = explode('&', $url[1]);                                
                if(!empty($parse_url) && is_array($parse_url)){                   
                    foreach ($parse_url as $key => $value) {                             
                        if(strpos($value,'=') == TRUE){
                            $param = explode('=', $value);                         
                            if(!empty($param) && is_array($param) ){
                                $data_param[$param[0]] = $param[1];    
                            } 
                        }                                          
                    }                                        
                }

            }    
        } catch (\Exception $e) {
          \Log::error('func generate_get_uri error with URI : ' . \Request::fullUrl());
        }
        
        return $data_param;
    }
}

/**
* Get View
* $module (string) ex : 'product' (module name)
* $view (string) ex : 'product.index' (folder and file name)
* $data (array) ex : array('foo' => 'bar')
* Return view folder template
* @author Irfan Fauzan
*/
if (! function_exists('get_view')) {
    function get_view($module, $view, $data = array())
    {
        $domain = get_domain();
        $folders = \Config::get('berrybenka.folders');
        
        switch ($domain['channel']) {
            case 1 : 
                return view("$module::". $folders[1] .".$view", $data);
            break;
            case 2 :
                return view("$module::". $folders[2] .".$view", $data);
            break;
            case 3 :
                return view("$module::". $folders[3] .".$view", $data);
            break;
            case 4 :
                return view("$module::". $folders[4] .".$view", $data);
            break;
            case 5 :
                return view("$module::". $folders[5] .".$view", $data);
            break;
            case 6 :
                return view("$module::". $folders[6] .".$view", $data);
            break;
        }
    }
}

/**
* Get SOLR
* Get file content solr
*/
if(! function_exists("get_solr") ) {
    function get_solr($params) 
    {
        $request    = $params["core_selector"];
        $q          = (isset($params["query"]))?$params["query"]:"*:*"; 
        $fq         = (isset($params["filter_query"]))?$params["filter_query"]:""; 
        $start      = (isset($params["start"]))?"&start=".$params["start"]:""; 
        $fl         = (isset($params["field_list"]))?"&fl=".$params["field_list"]:""; 
        $rows       = (isset($params["rows"]))?"&rows=".$params["rows"]:""; 
        $sort       = (isset($params["sort"]))?"&sort=".$params["sort"]:""; 
        
        $url        = solr_site().$request.'/select?q='.$q.'&'.$fq.$start.$fl.$rows.$sort.'&wt=json'; 

        $return     = file_get_contents($url); 
        $cTojson    = json_decode($return);
        $data       = $cTojson->response->docs;

        return $data;
    }
}

/**
* Get SOLR url
*/
if(! function_exists("solr_site") ) {
    function solr_site() 
    {
        //Application Environment
        $app_env = env('APP_ENV', 'development');
        //\Log::info('Solr site : '.$app_env.'');
        switch ($app_env) {
            case 'local':
                //$solr_site = 'http://localhost:8080/solr/';
                $solr_site_value = env('SOLR_SITE_LOCAL', 'http://localhost:8080/solr/');
                $solr_site = $solr_site_value;
                break;
            case 'development':
                 //$solr_site = 'http://dev.berrybenka.biz:8080/solr/';
                 $solr_site_value = env('SOLR_SITE_DEV', 'http://dev.berrybenka.biz:8080/solr/');
                 $solr_site = $solr_site_value;
                break;
            case 'production':
                //$solr_site = 'http://54.151.164.95:8080/solr/';
                $solr_site_value = env('SOLR_SITE_LIVE', 'http://54.151.164.95:8080/solr/');
                $solr_site = $solr_site_value;
                break;
            default:
                 $solr_site = 'http://54.151.164.95:8080/solr/';
                //$solr_site = 'http://54.151.164.95:8080/solr/';
                break;
        }

        return $solr_site;
    }
}

/**
* Check Front End Type
*/
if(! function_exists("check_type") ) {
    function check_type($type) 
    {
        //$path = public_path() . "/upload/genfile/dd.ss.json"; // ie: /var/www/laravel/app/public/upload/genfile/json/filename.json
        $path = path_type();

        if (!File::exists($path)) {
            //throw new Exception("Invalid File");
            $path = path_type_public();
        }

        $status = false;
        $get_frontend_type = json_decode(@file_get_contents($path), TRUE);
        //Log::info('check_type_front_end_type : '.json_encode($get_frontend_type).'');
        if($get_frontend_type){
            foreach ($get_frontend_type as $key => $value) {
                if ($type == $value) {
                    $status = true;
                }           
            }            
        }        
        
//        var_dump($get_frontend_type);
//        exit;
        return $status;
    }
}

/**
* Check Front End Type
*
* Check URI Segment for Parent, Child type, New Arrival, Sale, Filter
*/
if(! function_exists("check_uri_segment") ) {
    function check_uri_segment($param = null, $gender = null) 
    {
        $data = null;
        $check_parent_type = false;
        //bb_debug($param);
        //Define URI Segment
        $get_uri = get_uri();
        $url = array_filter(explode('/',$get_uri)); 
        
        if ($param == 'type') {
            $hold_type_url = null;
            foreach ($url as $key => $value) {//clothing,dresses
                $check_type = check_type($value);
                if ($check_type == true) {
                    $hold_type_url[] = $value;
                }
            }
            $data = is_array($hold_type_url) ? end($hold_type_url) : null;
        } elseif ($param == 'sub_type') {
            $hold_type_url = null;
            foreach ($url as $key => $value) {//clothing,dresses
                $sub_type = isset($url[$key+1]) ? $url[$key+1] : "";
                $parent_type = isset($url[$key-1]) ? $url[$key-1] : "";
                $check_parent_type = check_type($parent_type);
                $check_sub_type = check_type($sub_type);
                $check_type = check_type($value);
                if ($check_type == true && $check_sub_type == true && $check_parent_type == true) {
                    $hold_type_url[] = $value;
                }
            }
            $data = is_array($hold_type_url) ? end($hold_type_url) : null;
        } elseif ($param == 'parent_type') {
            $hold_parent_url = null;
            foreach ($url as $key => $value) {
                $check_type = check_type($value);
                if ($check_type == true) {  
                    $check_parent_type = check_parent_type($value, $gender);
                    if ($check_parent_type == true) { 
                        $hold_parent_url[] = $value;
                        break;
                    }
                }
            }

            $data = is_array($hold_parent_url) ? end($hold_parent_url) : null;

        } elseif ($param == 'pagination') {
            $i = 0;//dd($url);
            foreach ($url as $key => $value) {
                if (is_numeric($value)) {
                    $check_uri_before_pagination = (isset($url[$key-1])) ? $url[$key-1] : null;
                    if ($check_uri_before_pagination != 'size' && $check_uri_before_pagination != 'special') {
                        //$data = $url[$key];
                        if(isset($url[$key])){
                            $data = $url[$key];   
                        } 
                    }
                }
                $i++;
            }
        } elseif ($param == 'special_name') {
            if (in_array('special', $url)) {
                $key = array_search ('special', $url);
                $key = $key+2;
                
                if(isset($url[$key])){
                    $data = $url[$key];   
                }                                
            }
        } else {
            if (in_array($param, $url)) {
                $key = array_search ($param, $url);
                $array_filter = ['brand','color','size','tag','special'];
                if (in_array($param, $array_filter)) {
                    $key = $key+1;
                }
                
                if(isset($url[$key])){
                    $data = $url[$key];   
                }
                
            }
        }

        return $data;
    }
}

/**
* Check Front End Type
*/
if(! function_exists("check_parent_type") ) {
    function check_parent_type($type, $gender) 
    {
        //Define Domain and Channel
        $get_domain = get_domain();

        if ($gender == 'women') {
            $file_gender = 'parent'.'-women'.'-'.$get_domain['domain_alias'];
        } elseif ($gender == 'men') {
            $file_gender = 'parent'.'-men'.'-'.$get_domain['domain_alias'];
        } else {
            $file_gender = 'parent'.'-women'.'-'.$get_domain['domain_alias'];
        }
        //echo $file_gender;
        $status = false;
        $value = Redis::get($file_gender);
        if (is_null($value)) {
            //$value = \Storage::get($file_gender.'.json'); 
            $value = \Storage::get($get_domain['domain_name'].'/catalog/'.$file_gender.'.json');
        }
        
        //bb_debug($value);
        $get_frontend_type = json_decode($value);                           //bb_debug($get_frontend_type);
        if (is_null($get_frontend_type)) {
            $status = false;
        } else {
            if (in_array($type, $get_frontend_type)) {
                $status = true;
            }
        }
        
        return $status;
    }
}

/**
* Check Front End Type
*/
if(! function_exists("get_active_solr") ) {
    function get_active_solr($core_selector, $query, $filter_query, $limit, $offset, $order, $group, $field_list = null) 
    {
        $q = null;
        if (! is_null($query)) {
            $q = $query;
        }

        $fq = null;
        if (is_array($filter_query)) {
            foreach ($filter_query as $key => $value) {
                if ($key == 'url_set' || $key == 'product_sku') {
                    $fq .= '&fq='.$key.':"'.$value.'"';
                } elseif ($key == 'sprice') {
                    $fq .= $value;
                } else {
                    $fq .= '&fq='.$key.':'.$value.'';
                }
            }
        }

        if (is_null($limit)) {
            $limit = 24;
        }

        if (is_null($offset)) {
            $offset = 0;
        }

        if (is_null($order)) {
            if ($core_selector == 'products' || $core_selector == 'products_hb' || $core_selector == 'products_sd') {
                $order = 'total_series_score+desc%2C+product_scoring+desc';
            } elseif ($core_selector == 'front_end_type' || $core_selector == 'front_end_type_hb' || $core_selector == 'front_end_type_sd') {
                $order = 'type_name+asc';
            }
        }

        $solr['query']          = $q;
        $solr['filter_query']   = $fq;
        $solr['core_selector']  = $core_selector;
        $solr['start']          = $offset;
        $solr['rows']           = $limit;
        $solr['field_list']     = $field_list;
        $solr['sort']           = $order;
        $solr['group']          = $group;
        $get_solr = query_solr($solr);

        return $get_solr;
    }
}

/**
* Get SOLR
* Get file content solr
*/
if(! function_exists("query_solr") ) {
    function query_solr($params) 
    {
        $cTojson = new stdClass();

        $request    = $params["core_selector"];
        $q          = (isset($params["query"]))?$params["query"]:"*:*"; 
        $fq         = (isset($params["filter_query"]))?$params["filter_query"]:""; 
        $start      = (isset($params["start"]))?"&start=".$params["start"]:""; 
        $fl         = (isset($params["field_list"]))?"&fl=".$params["field_list"]:""; 
        $rows       = (isset($params["rows"]))?"&rows=".$params["rows"]:""; 
        $sort       = (isset($params["sort"]))?"&sort=".$params["sort"]:""; 

        $group      = (isset($params["group"]))?"&group=true&group.field=".$params["group"]."&group.main=true":""; 

        $url        = solr_site().$request.'/select?q='.$q.$fq.$start.$fl.$rows.$sort.'&wt=json&indent=true'.$group;
        Log::info('query_solr : '.$url.'');
        // $opts       = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        // $context    = stream_context_create($opts);
        // $return     = file_get_contents($url, false, $context);

        $output = curl_get($url);
        
        $cTojson = json_decode($output);
        
        try {
          $cTojson->response->url = $url;
        } catch (\Exception $e) {
          //Log::error('query_solr : '.$url.' with URI : ' . \Request::fullUrl());
        }
        
        $data = $cTojson->response;

        return $data;
    }
}

/**
     * Display a param catalog from URL
     *
     * array['gender'] array Defines the value to be str 'men' or 'women' if exist in URL.
     * array['new'] array Defines the value to be str 'new-arrival' if exist in URL.
     * array['sale'] array Defines the value to be str 'sale' if exist in URL.
     * array['child_type_url'] array Defines the value to be str {type_url} if exist in URL.
     * array['brand_url'] array Defines the value to be str {brand_url} if exist in URL.
     * array['color_name'] array Defines the value to be str {color_name} if exist in URL.
     * array['product_size_url'] array Defines the value to be str {product_size_url} if exist in URL.
     * array['pagination'] array Defines the value to be int (24,48,72) if exist in URL.
     * @return array
     */
if(! function_exists("generate_uri_segment") ) {
    function generate_uri_segment()
    {
        $women              = check_uri_segment('women');
        $men                = check_uri_segment('men');
        $new                = check_uri_segment('new-arrival');
        $sale               = check_uri_segment('sale');

        $data['gender']     = ( ! is_null($women) ) ? $women : $men; 

        $parent_type_url    = check_uri_segment('parent_type', $data['gender']);
        $child_type_url     = check_uri_segment('type');
        $sub_child_type_url = check_uri_segment('sub_type');
        $brand_url          = check_uri_segment('brand');
        $color_name         = check_uri_segment('color');
        $product_size_url   = check_uri_segment('size');
        $pagination         = check_uri_segment('pagination');
        $special            = check_uri_segment('special');
        $special_name       = check_uri_segment('special_name');
        $tag                = check_uri_segment('tag');
        
        $get_domain = get_domain();         
        if (is_null($parent_type_url)) {
            $where['type_url'] = '"'.$child_type_url.'"';
            $core_selector = getCoreSelector("front_end_type");
            
            try {
              $solr_front_end_type = get_active_solr($core_selector, $query = null, $where, $limit = 1, $offset = null, $order = null, $group = null)->docs;
              \Log::info('solr_front_end_type: ' . json_encode($solr_front_end_type));

              if (! empty($solr_front_end_type)) {
                      foreach ($solr_front_end_type as $key => $value) {
                          $parent = $value->parent;
                          $wheres['id'] = $parent;
                          $solr_front_end_type = get_active_solr($core_selector, $query = null, $wheres, $limit = 1, $offset = null, $order = null, $group = null)->docs;
                          if (! empty($solr_front_end_type)) {
                              foreach ($solr_front_end_type as $key => $value) {
                                  $parent_type_url = $value->type_url;
                              }
                          }
                      }
                  }
            } catch (\Exception $e) {
              \Log::error($e);
              \Log::error('func generate_uri_segment error with URI : ' . \Request::fullUrl());
            } 
        }

        $data['new']                = ( ! is_null($new) ) ? $new : null; 
        $data['sale']               = ( ! is_null($sale) ) ? $sale : null; 
        $data['parent_type_url']    = ( ! is_null($parent_type_url) ) ? (string) $parent_type_url : null; 
        $data['child_type_url']     = ( ! is_null($child_type_url) ) ? (string) $child_type_url : null;
        $data['sub_child_type_url'] = ( ! is_null($sub_child_type_url) ) ? (string) $sub_child_type_url : null;  
        $data['brand_url']          = ( ! is_null($brand_url ) ) ? $brand_url : null; 
        $data['color_name']         = ( ! is_null($color_name) ) ? $color_name : null; 
        $data['product_size_url']   = ( ! is_null($product_size_url) ) ? $product_size_url : null;
        $data['pagination']         = ( ! is_null($pagination) ) ? $pagination : null; 
        $data['special_name']       = ( ! is_null($special_name) ) ? $special_name : null;
        $data['special']            = ( ! is_null($special) ) ? $special : null; 
        $data['tag']                = ( ! is_null($tag) ) ? $tag : null; 
        
        return $data;
    }
}

/*
// Search In Array
// ** To select array object/values in array data by key and value parameter.
// ** @params array values/object, key_name, value which want to search
// ** @return array value/object.
// @created : DedyS. 01/02/2016
*/
if (! function_exists("search_in_array")) {
	function search_in_array($array, $key, $value)
	{
		$results = array();
		
		foreach ($array as $subarray) {
			if (isset($subarray->$key) && $subarray->$key == $value) {
				$results[] = $subarray;
			}
		}
		
		return $results;
	}
}

/*
// Search Not In Array
// ** To select array object/values in array data by key excluding value parameter.
// ** @params array values/object, key_name, value which want to exclude
// ** @return array value/object.
// @created : DedyS. 01/02/2016
*/
if (! function_exists("search_not_in_array")) {
	function search_not_in_array($array, $key, $value)
	{
		$results = array();
		
		foreach ($array as $subarray) {
			if (isset($subarray->$key) && $subarray->$key != $value) {
				$results[] = $subarray;
			}
		}
		
		return $results;
	}
}

/*
// Search Array Group
// @created : DedyS. 01/02/2016
*/
if (! function_exists("array_group")) {
	function array_group($array, $key)
	{
		$results = array();
		//$return = "value";
		$select = array();
		$val = "";
		
		$i=0;
		foreach ($array as $rows) {			
			if ($rows->$key <> $val) {
				$select[$i] = $rows->$key;
				$val = $rows->$key;
				$i++;
			}
		}
		
		//$select = array_selection($array,$key,$return);
		
		foreach ($select as $row) {
			$results[] = search_in_array($array,$key,$row);
		}
				
		return $results;
	}
}

/*
// Search Array By Selection
// @created : DedyS. 01/02/2016
*/
if (! function_exists("array_selection")) {
	function array_selection($array, $key, $return="array")
	{
		$returns = array();
		$val = "";
		
		$i=0;
		foreach ($array as $rows) {			
			if ($rows->$key <> $val) {
				if ($return = "value") {
					$returns[$i] = $rows->$key;
				} else {
					$returns[$i] = $rows;
				}
				$val = $rows->$key;
				$i++;
			}
		}
				
		return $returns;
	}
}

/*
// * Array to Object
// *** Convert array data values to array object 
// *** @return array object 
// @created : DedyS. 01/02/2016
*/
if (!function_exists('array_to_object')) {
	function array_to_object($array) {
		$obj = new stdClass;
		foreach($array as $k => $v) {
			if(strlen($k)) {
				if(is_array($v)) {
					$obj->{$k} = array_to_object($v); //RECURSION
				} else {
					$obj->{$k} = $v;
				}
			}
		}
		return $obj;
	} 
}

/*
// * Convert Object to Array
// *** Convert array object to array values 
// *** @return array values
// @created : DedyS. 01/02/2016
*/
if (!function_exists('object_to_array')) {
	function object_to_array($data)	{
		if (is_array($data) || is_object($data)) {
			$result = array();
			foreach ($data as $key => $value) {
				$result[$key] = object_to_array($value);
			}
			return $result;
		}
		return $data;
	}
}

/**
 * Create URL Title
 *
 * Takes a "title" string as input and creates a
 * human-friendly URL string with a "separator" string 
 * as the word separator.
 *
 * @access	public
 * @param	string	the string
 * @param	string	the separator
 * @return	string
 */
if ( ! function_exists('url_title'))
{
	function url_title($str, $separator = '-', $lowercase = FALSE)
	{
		if ($separator == 'dash') 
		{
		    $separator = '-';
		}
		else if ($separator == 'underscore')
		{
		    $separator = '_';
		}
		
		$q_separator = preg_quote($separator);

		$trans = array(
			'&.+?;'                 => '',
			'[^a-z0-9 _-]'          => '',
			'\s+'                   => $separator,
			'('.$q_separator.')+'   => $separator
		);

		$str = strip_tags($str);

		foreach ($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		if ($lowercase === TRUE)
		{
			$str = strtolower($str);
		}

		return trim($str, $separator);
	}
}

/**
 * Create URL Title
 *
 * Takes a "title" string as input and creates a
 * human-friendly URL string with a "separator" string 
 * as the word separator.
 *
 * @access  public
 * @param   string  the string
 * @param   string  the separator
 * @return  string
 */

 if( !function_exists('modul_path'))
 {
    function module_path($module_name)
    {
        $path = app_path()."/Modules/$module_name/Views/";

        return $path;
    }
 }

/**
 * Generate pagination for catalog
 *
 * @access  public
 * @param   Integer Current page
 * @param   Integer Total Page
 * @return  HTML
 */

if ( ! function_exists('paginate_page'))
{
    function paginate_page($page, $total_pages, $event = 'onclick="ChangeUrl(this)"', $page_limit = 48) {
        // $tmp = $total_page - $page;
        $get_domain     = get_domain();
        $current_page   = $page / $page_limit;
        $get_rest_page  = ($total_pages - $page) / $page_limit;
        $get_rest_page  = ceil($get_rest_page);
        $current_page   = ceil($current_page + 1);
        $rest_page      = ($get_rest_page <= 4) ? $get_rest_page : 4;
        
        $pagination = '';

        //arrow prev and next
        $prev_arrow = 'left';
        $next_arrow = 'right';

        if($get_domain['channel']==2 || $get_domain['channel']==4 || $get_domain['channel']==6){
            $prev_arrow = 'up';
            $next_arrow = 'down';
        }
        
        if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
            
            $right_links    = $current_page + $rest_page; 
            $previous       = $current_page - 1; //previous link 
            $next           = $current_page + 1; //next link
            $first_link     = true; //boolean var to decide our first link

            if($current_page > 1){
                $previous_link = ($previous==0)? 1: $previous;
                $pagination .= '<li class="prev left"><a data-page="'.$previous_link.'" href="javascript:void(0);" '.$event.'><i class="fa fa-angle-'.$prev_arrow.'"></i></a></li>'; //previous link

                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li><a data-page="'.$i.'" href="javascript:void(0);" '.$event.'>'.$i.'</a></li>';
                    }
                }

                $first_link = false; //set first link to false
            }
            
            
            if($current_page == $total_pages){ //if it's the last active link
                $pagination .= '<li class="last active">'.$current_page.'</li>';
            }else{ //regular current link
                $pagination .= '<li class="active">'.$current_page.'</li>';
            }
                    
            // bb_debug($right_links);
            for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
                if($i<=$total_pages){
                    $pagination .= '<li><a data-page="'.$i.'" href="javascript:void(0);" '.$event.'>'.$i.'</a></li>';
                }
            }
            if($current_page < $total_pages && $rest_page > 1){ 
                    $next_link = $current_page+1;
                    $pagination .= '<li class="next right"><a data-page="'.$next_link.'" href="javascript:void(0);" '.$event.'><i class="fa fa-angle-'.$next_arrow.'"></i></a></li>'; //next link

                    // $pagination .= '<li class="last"><a href="#" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link   
            }
            
        }

        return $pagination; //return pagination links    
    }
}


if( !function_exists('mega_menu'))
{
    function mega_menu($gender, $client = false)
    {
        $get_domain = get_domain();

        $html = "";
        //$file_path = MEGA_MENU_LARAVEL_PATH."mega_menu_laravel_nav_".$gender."_". $get_domain['domain_name'] .".php";
        $app_env = env('APP_ENV', 'production');
        
        \Log::info('APP_ENV: ' . json_encode($app_env));
        if($app_env == 'production'){
            //$file_path = MEGA_MENU_LARAVEL_PATH."mega_menu_laravel_nav_".$gender."_". $get_domain['domain_name'] ."_beta.php";

            //if($get_domain['domain'] == 'berrybenka.com' || $get_domain['domain'] == 'hijabenka.com') {
                $file_path = MEGA_MENU_LARAVEL_PATH."mega_menu_laravel_nav_".$gender."_". $get_domain['domain_name'] .".php?".date("dmYHis")."";
            //}

        }else{
            // $file_path = MEGA_MENU_LARAVEL_PATH."mega_menu_laravel_nav_".$gender."_". $get_domain['domain_name'] ."_dev.php?".date("dmYHis")."";
            $file_path = MEGA_MENU_LARAVEL_PATH."mega_menu_laravel_nav_".$gender."_". $get_domain['domain_name'] ."_dev.php?".date("dmYHis")."";
        }

        \Log::info('Mega Menu Path: ' . json_encode($file_path));
        //var_dump($file_path);
        //die;
        $file = curl_get($file_path);

        if ($client === 'mobile') {
            if (explode('.', $_SERVER['HTTP_HOST'])[0] === 'm-debug') {
                $file = str_replace('hijabenka.com', 'm-debug.hijabenka.com', $file);
            } else {
                $file = str_replace('hijabenka.com', 'm.hijabenka.com', $file);                
            }
            // $file = str_replace('hijabenka.com', 'm.hijabenka.com', $file);
            // $file = str_replace('debug.hijabenka.com', 'm-debug.hijabenka.com', $file);
            $file = str_replace('bismar.hijabenka.biz', 'm-bismar.hijabenka.biz', $file);
            $file = str_replace('hijabenka.local', 'm.hijabenka.local', $file);
        }

        if($file) {
            $html .= "<ul>$file</ul>";   
        }
                
        return $html;
    }
}

if ( ! function_exists('show_message'))
{
    function show_message($message = null, $type = null) {
        $get_domain = get_domain();
        $channel  = $get_domain["channel"];

        if($type == 1) {


            $html = "<span class='error-msg-login'>
                        <i aria-hidden='true' class='fa fa-bell'></i>
                        <i aria-hidden='true' class='fa fa-times'></i>
                        $message
                    </span>";
        } else {

            if($channel==2 || $channel==4 || $channel==6){
                $html = "<span class='success-msg-login'>
                            <i aria-hidden='true' class='fa fa-bell'></i>
                            <i aria-hidden='true' class='fa fa-times'></i>
                            $message
                        </span>";
            }else{
                $html = "<span class='success-msg'>
                            <i aria-hidden='true' class='fa fa-bell'></i>
                            <i aria-hidden='true' class='fa fa-times'></i>
                            $message
                        </span>";
            }
         }

        return $html;
    }
}

if ( ! function_exists('error_message'))
{
    function error_message($errors) {
        if (count($errors) > 0) {
            $html = '';

            foreach ($errors->all() as $error) {
                
                $html .= "<span class='error-msg-login'>
                            <i aria-hidden='true' class='fa fa-bell'></i>
                            <i aria-hidden='true' class='fa fa-times'></i>
                            $error
                        </span>";
            }
            
            return $html;
        }
    }
}

if(! function_exists("clean_teks_for_brand_url")){
    function clean_teks_for_brand_url( $teks, $separator = "" ) {
        $find = array( '|<(.*?)>|', '|</(.*?)>|', '|[_]{1,}|', '|[ ]{1,}|', '|[^a-zA-Z0-9\/\:\-.]|', '|[-]{2,}|', '|[,]|', '|:|', '|quot|', '|039|', '|[.]{2,}|', '|[.]{3,}|', '|[/]|', '|[.]|' );
        $replace = array( $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator, $separator );

        $newteks1 = preg_replace( "|[ ]|", "-", strtolower( $teks ) ); 
        $newteks2 = preg_replace( $find, $replace, $newteks1 );

        return $newteks2;
    }
}
// ------------------------------------------------------------------------


/* Make Cookie Function
** for create cookie 
** @params : $name, $value, $expire(in minutes), $path, $domain, $secure, $httpOnly
** @return true/false
*/
if ( ! function_exists('makeCookie'))
{
    function makeCookie($cookie) {
		$name   = $cookie['name'];
        $value  = $cookie['value'];
        $expire	= isset($cookie['expire'])?$cookie['expire']:NULL;
        $domain = isset($cookie['domain'])?$cookie['domain']:NULL;
        $path   = isset($cookie['path'])?$cookie['path']:NULL;
		$secure	= isset($cookie['secure'])?$cookie['secure']:false;
		$httpOnly = isset($cookie['httpOnly'])?$cookie['httpOnly']:true;
		
		$cookies = Cookie::get($name);
		if(empty($cookies)) {
			if ($expire) {
				//$cookies = Cookie::queue($name,$value,$expire,$path,$domain,$secure,$httpOnly);
				$cookies = Cookie::queue(Cookie::make($name,$value,$expire,$path,$domain,$secure,$httpOnly));
			} else {
				$cookies = Cookie::queue(Cookie::forever($name,$value,$path,$domain,$secure,$httpOnly));
			}		
		}
		
		if ($cookies) {
			return true;
		} else {
			return false;
		}
	}
}

// ------------------------------------------------------------------------


/* Make Session Function
** for create session 
** @params : $key, $value
** @return 
*/
if ( ! function_exists('putSession'))
{
    function putSession($params) {
		//bb_debug($params);
		foreach ($params as $key => $value) {
			Session::put($key, $value);
		}		
	}
}

// ------------------------------------------------------------------------


/* Header Menu Mobile
** Redis / Storage
** how to use           
** $data = array('gender' => 'men');
** $filter_type = menuMweb($data);
** @return array
*/
if ( ! function_exists('menuMweb'))
{
    function menuMweb($data) {

        $get_domain             = get_domain();
        $generate_uri_segment   = generate_uri_segment();
        $domain_alias           = $get_domain['domain_alias'];
        $gender                 = $data['gender'];
        
        $filter_type_redis_name = 'menu-'.$gender.'-'.$domain_alias.'';
        if ($generate_uri_segment['new'] == 'new-arrival' || $generate_uri_segment['sale'] == 'sale') {
            $filter_type_redis_name = 'menu-'.$gender.'-'.$domain_alias.'';
            if($generate_uri_segment['sale'] == 'sale'){
                $filter_type_redis_name = 'menu-sale-'.$gender.'-'.$domain_alias.'';    
            }
        }    

        if($get_domain['domain_id'] == 3){
            $filter_type_redis_name = 'menu-'.$domain_alias.'';
            if ($generate_uri_segment['new'] == 'new-arrival' || $generate_uri_segment['sale'] == 'sale') {
                $filter_type_redis_name = 'menu-'.$domain_alias.'';
                if($generate_uri_segment['sale'] == 'sale'){
                    $filter_type_redis_name = 'menu-sale-'.$domain_alias.'';    
                }
            }  
        }
        
        $filter_type            = Redis::get($filter_type_redis_name);

        $status = 1;

        if (is_null($filter_type)) {
            $file = storage_path().'/app/'.$get_domain['domain_name'].'/catalog/'.$filter_type_redis_name.'.json';

            if (\File::exists($file)) {
                $filter_type = \Storage::get($get_domain['domain_name'].'/catalog/'.$filter_type_redis_name.'.json');
            } else {
                $status = 0;
            }
        }

        $filter_type    = (array) json_decode($filter_type);

        return $filter_type;
    }
}

if ( ! function_exists('getEnvMobile'))
{
    function getEnvMobile() {
        $get_domain      = get_domain();
        $domain_name     = strtoupper($get_domain['domain_name']);
        $domain_redirect = env($domain_name.'_MOBILE');
        $http            = 'http://';

        if(\Request::secure()) {
            $http = 'https://';
        }

        $env_mobile = $http.$domain_redirect;

        return $env_mobile;
    }
}

if ( ! function_exists('getAppEnv'))
{
    function getAppEnv() {
        $app_env = env('APP_ENV');

        return $app_env;
    }
}

if ( ! function_exists('getSlackEnv'))
{
    function getSlackEnv() {
        $app_env = env('SLACK_LOG');

        return $app_env;
    }
}

if ( ! function_exists('getMarketingEnv'))
{
    function getMarketingEnv() {
        $app_env = env('APP_ENV');
        $marketing_env = env('MARKETING_TAG');

        if($marketing_env == "enable") {
            return true;
        } else {
            return false;
        }
    }
}

/** Indonesia Date Format
*** convert date time to Indonesian date time format. 
*** @return @datetime format.
**/
if (! function_exists("indonesian_date")){
	function indonesian_date ($timestamp = '', $date_format = 'l, j F Y | H:i', $suffix = 'WIB') {
		if (trim ($timestamp) == '')
		{
				$timestamp = time ();
		}
		elseif (!ctype_digit ($timestamp))
		{
			$timestamp = strtotime ($timestamp);
		}
		# remove S (st,nd,rd,th) there are no such things in indonesia :p
		$date_format = preg_replace ("/S/", "", $date_format);
		$pattern = array (
			'/Mon[^day]/','/Tue[^sday]/','/Wed[^nesday]/','/Thu[^rsday]/',
			'/Fri[^day]/','/Sat[^urday]/','/Sun[^day]/','/Monday/','/Tuesday/',
			'/Wednesday/','/Thursday/','/Friday/','/Saturday/','/Sunday/',
			'/Jan[^uary]/','/Feb[^ruary]/','/Mar[^ch]/','/Apr[^il]/','/May/',
			'/Jun[^e]/','/Jul[^y]/','/Aug[^ust]/','/Sep[^tember]/','/Oct[^ober]/',
			'/Nov[^ember]/','/Dec[^ember]/','/January/','/February/','/March/',
			'/April/','/June/','/July/','/August/','/September/','/October/',
			'/November/','/December/',
		);
		$replace = array ( 'Sen','Sel','Rab','Kam','Jum','Sab','Min',
			'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu',
			'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des',
			'Januari','Februari','Maret','April','Juni','Juli','Agustus','Sepember',
			'Oktober','November','Desember',
		);
		$date = date ($date_format, $timestamp);
		$date = preg_replace ($pattern, $replace, $date);
		//$date = "{$date} {$suffix}";
		$date = "{$date}";
		return $date;
	} 	
}

/** CURL formatted
*** convert date time to Indonesian date time format. 
*** @return string.
**/
if (! function_exists("bb_curl")){
    function bb_curl ($url,$params = array()) {
        $ch = curl_init();

        foreach ($params as $key => $value) {
            $params[] = urlencode($key).'='.urlencode($value);
        }

        $params = implode('&', $params);

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec ($ch);

        if ($errno = curl_errno($ch)) {
            return $errno;
        }

        curl_close ($ch);

        return $server_output;
    }
}


/** Get value by uri key
*** Return value of httpget key
*** @return Array.
**/
if(!function_exists('findUriKey')) {
    function findUriKey($word, $separator = '--') {
        $uri = Request::getQueryString();
        $arr_param = [];

        $exp_uri = explode('&' ,$uri);

        foreach($exp_uri as $key => $uri_key) {
            if(strpos($uri_key, $word) !== false) {
                $param = $uri_key;
                $exp_uri_key = explode('=', $uri_key);

                if(strpos($exp_uri_key[1], $separator) !== false) {
                    $exp_multi_filter = explode($separator, $exp_uri_key[1]);
                    $arr_param = $exp_multi_filter;
                } else {
                    $arr_param = [$exp_uri_key[1]];
                }
            }
        }

        return $arr_param;
    }

}

/** Get value by segment key
*** Return value of segment uri key
*** @return Array.
**/
if(!function_exists('findUriSegment')) {
    function findUriSegment($key) {
        $uri = Request::path();
        $arr_uri = explode('/', $uri);
        $return = [];               

        switch($key) {
            case 'cat_parent' :
                $return = (!empty(Request::segment(2))) ? [Request::segment(2)] : '';
                break;
            case 'cat_children' :
                $return = (!empty(Request::segment(3))) ? [Request::segment(3)] : '';
                break;
            case 'size' :
                $val = array_search($key, $arr_uri) != '' ? array_search($key, $arr_uri) + 2 : 0;
                $key_val = (!empty(Request::segment($val))) ? Request::segment($val) : '';
                $arr_key_val = explode('-', $key_val);
                $return = $arr_key_val;

                break;
            default:
                $val = array_search($key, $arr_uri) != '' ? array_search($key, $arr_uri) + 2 : 0;
                $key_val = (!empty(Request::segment($val))) ? Request::segment($val) : '';
                $arr_key_val = explode('--', $key_val);
                $return = $arr_key_val;

                break;
        }

        return $return;
    }
}

/*
* Implode array from generated uri segment with function findUriSegment or findUriKey
* @param array, string
* @return string
*/
if( !function_exists('implodeUri'))
{
    function implodeUri ($keyword, $val, $separator = '--') {

        $return = $val[0] != '' ? "/$keyword/". implode($separator, $val) : '';

        return $return;
    }
}

/*
* Implode array from generated uri segment with function findUriSegment or findUriKey
* @param array, string
* @return string
*/
if( !function_exists('categoryUrl'))
{
    function categoryUrl($gender) {
        $uri = Request::path();
        // $split_uri = explode('/', $uri);
        // $gender_pos = array_search($gender, $split_uri);

        if(strpos($uri, 'clothing') === false) {
            $parent = (!empty(Request::segment(1))) ? Request::segment(1) : '';
            $children = (!empty(Request::segment(2)) && Request::segment(2) != $gender && Request::segment(2) != 'brand' && Request::segment(2) != 'size' && Request::segment(2) != 'color') ? '/'.Request::segment(2) : '';

            $return = '/'.$parent.$children;
        } else {
            $parent = (!empty(Request::segment(2)) && Request::segment(2) != $gender && Request::segment(2) != 'brand' && Request::segment(2) != 'size' && Request::segment(2) != 'color') ? '/'. Request::segment(2) : '';
            $children = (!empty(Request::segment(3)) && Request::segment(3) != $gender && Request::segment(3) != 'brand' && Request::segment(3) != 'size' && Request::segment(3) != 'color') ? '/'.Request::segment(3) : '';

            $return = '/clothing'.$parent.$children;
        }

        return $return;
    }
}

if( ! function_exists('veritransFilter'))
{
    function veritransFilter ($param, $length) {
        $temp = str_replace(array("_", "\\", ",", ".", "@", "-"), "", $param);
        // my additional filters
        $temp = str_replace(array("\"", "'", "/", ":", "&"), "", $temp);
        $temp = str_replace(array("\r\n"), " ", $temp);
        $temp = preg_replace('/\s+/', ' ', trim($temp));
        // 26 length
        return  substr($temp, 0, $length-1);
    }
}

/** Get Path Type category
*** @return file path
**/
if (!function_exists('path_type')) {
    function path_type() {
        $get_domain = get_domain();
        $path = storage_path().'/app/'.$get_domain['domain_name'].'/genfile/all-type-'.$get_domain['domain_alias'].'.json';
        
        return $path;        
    }
}

/** Get Path Type category Public Backup
*** @return file path
**/
if (!function_exists('path_type_public')) {
    function path_type_public() {
        $get_domain = get_domain();
        $path = public_path().'/upload/genfile/all-type-'.$get_domain['domain_alias'].'.json';
        
        return $path;         
    }
}

/**
* set product price
* Return product normal price / special price / discount price
* @author Alief Nochtavio
*/
if (!function_exists('set_price')) {

    function set_price($each_price, $discount_price) {
        $price = $each_price;
        if($discount_price != NULL && $discount_price != 0){
          $price = $discount_price;
        }

        return $price;
    }
}
  

/** 
** Generate random string.
** for Generate random string.
**/
if(! function_exists("generate_random_string")){
    function generate_random_string($length = 10) {
        // $uniq_char = mt_rand();
        $uniq_char = strtoupper(md5(uniqid(rand(),true))); 
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'.$uniq_char;
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    } 
}

  /** 
  ** Generate random string.
  ** for Generate random string.
  **/
if(! function_exists("curl_get")){
    function curl_get($url = "") {
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

        $output=curl_exec($ch);

        if($output === false)
        {
            echo "Error Number:".curl_errno($ch)."<br>";
            echo "Error String:".curl_error($ch);
        }
        curl_close($ch);

        return $output;
    } 
}

if(! function_exists("social_curl")){
    function social_curl($url, $data, $method="POST") {
        // create curl resource
        $ch = curl_init();

        //set the url, number of POST vars, POST data

        if($method == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($data));
        }
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $json = json_decode($result, true);
        
        //close connection
        curl_close($ch);

        return $json;
    } 
}

/** 
** Check IP Internal.
**/
if(! function_exists("is_ip_internal")){
  function is_ip_internal() {
    $list_ip_internal = [
      '103.18.133.9'
    ];
    
    $ip = \Request::ip();
    
    return in_array($ip, $list_ip_internal);
  } 
}

/** 
** Return IP Server.
**/
if(! function_exists("get_server_address")){
  function get_server_address() {
    return is_ip_internal() ? \Request::server('SERVER_ADDR') : '' ;
  } 
}

if(! function_exists("outputCSV")){
	function outputCSV($data) {
	    $outstream = fopen("php://output", "w");
	    function __outputCSV(&$vals, $key, $filehandler) {
	        fputcsv($filehandler, $vals,","); // add parameters if you want
	    }
	    array_walk($data, "__outputCSV", $outstream);
	    fclose($outstream);
	}
}

/** 
** Clear Redis by contain string to value
**/
if(! function_exists("clearRedisContains")){
    function clearRedisContains($string = NULL){
        if($string != NULL){            
            $redis      = \Cache::getRedis();
            $keys       = $redis->keys("*".$string."*");
            foreach ($keys as $key) {                    
                $redis->del($key);
            } 
        }
        return TRUE;
    }
}

/** 
** Write to csv
**/
if(! function_exists("writeCSV")) {
  function writeCSV($data, $dir) {
    if (!@file_put_contents($dir, $data)) {
      \Log::error('Cant write to buffer file: "'.$dir.'"', E_USER_ERROR);
      return false;
    }
    
    return true;
  }
}

/** 
** Get Core Selector based on domain id
**/
if(! function_exists("getCoreSelector")) {
  function getCoreSelector($core) {
    $core_selector  = NULL;
    $whitelist_core = ["seo", "terms", "wishlist", "promotions_exclusive_products"];
    
    if(in_array($core, $whitelist_core)){
      return $core;
    }
    
    $get_domain = get_domain();
    switch ($get_domain['domain_id']) {
      case '2': //hijabenka
        $core_selector = $core . '_hb';
        break;
      case '3': //shopdeca
        $core_selector = $core . '_sd';
        break;
      default : //berrybenka
        $core_selector = $core;
        break;
    }
    
    return $core_selector;
  }
}

/**
* getBannerCatalog
* Menampilkan banner di catalog
* @author Efendy Salim
*/
if (! function_exists('getBannerCatalog')) {
    function getBannerCatalog($limit = 48, $offset = 0){                
        $segments           = getOriginSegment();
        #\Log::notice('origin segment =' . json_encode($segments) );
        $arrSegment         = array();
        $arrSegment[1]      = ($segments[1]) ? $segments[1] : "NULL";          
        $arrSegment[2]      = ($segments[2]) ? $segments[2] : "NULL"; 
        $arrSegment[3]      = ($segments[3]) ? $segments[3] : "NULL"; 
        
        $define_domain      = get_domain();
        $domain_id          = $define_domain['domain_id'];
        
        $fq_arr             = array();
        $query              = null;       
        $start_number       = $offset + 1;
        $range              = $limit  + $offset;
        //filter query
        if(!empty($arrSegment[1])){
            $fq_arr['segment_1']        = $arrSegment[1];    
        }
        
        if($arrSegment[2]  == "NULL"){
            $fq_arr['segment_2']        = urlencode('(women OR men OR NULL)');
        }else{
            if($arrSegment[2]  == "women" || $arrSegment[2]  == "men"){
                $fq_arr['segment_2']    = urlencode('('. $arrSegment[2] .' OR NULL)');    
            } else{
                $fq_arr['segment_2']    = $arrSegment[2];    
            }           
        }
        
        if($arrSegment[3]  == "NULL"){
            $fq_arr['segment_3'] = urlencode('(women OR men OR NULL)');
        }else{
            if($arrSegment[3]  == "women" || $arrSegment[3]  == "men"){
                $fq_arr['segment_3']    = urlencode('('. $arrSegment[3] .' OR NULL)');    
            } else{
                $fq_arr['segment_3']    = $arrSegment[3];    
            }   
        }
        
        $fq_arr['template_domain']      = urlencode("(4 ".$domain_id.")"); // 4 = multidomain
        
        //limit offset - display number
        $fq_arr['display_number']       = urlencode("[". $start_number ." TO ". $range. "]");        
        
        $solr_param                     = array();        
        $solr_param['core_selector']    = 'banner_catalog';
        $solr_param['query']            = isset($query) ? $query : null;
        $solr_param['where']            = isset($fq_arr) ? $fq_arr : null;          
        $solr_param['limit']            = 3;
        $solr_param['offset']           = 0;
        $solr_param['order']            = urlencode('display_number ASC, segment_2 DESC, segment_3 DESC');
        $solr_param['group']            = NULL;
        $solr_param['field_list']       = NULL;
        
        $fetchBannerCatalog             = get_active_solr($solr_param['core_selector'],$solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);               
        $result = array(); 
        
        try{
            if(isset($fetchBannerCatalog->docs)){
                foreach($fetchBannerCatalog->docs as $key => $value){
                    //kalau ada posisi yang sama tumburan, eg: 1 gender both, 1 gender women di posisi yg sama hapus
                    if($key > 0 && isset($fetchBannerCatalog->docs[$key-1])){
                        if($fetchBannerCatalog->docs[$key]->display_number == $fetchBannerCatalog->docs[$key-1]->display_number){
                            unset($fetchBannerCatalog->docs[$key]);
                        }
                    }

                    //kalau dari url nya NULL (both gender) namun dari response solr dia bukan untuk both gender hapus object nya
                    if(isset($fetchBannerCatalog->docs[$key])){
                        if($arrSegment[2] == 'NULL' && $fetchBannerCatalog->docs[$key]->segment_2 !='NULL'){
                            unset($fetchBannerCatalog->docs[$key]);
                        }elseif($arrSegment[3] == 'NULL' && $fetchBannerCatalog->docs[$key]->segment_3 !='NULL'){
                            unset($fetchBannerCatalog->docs[$key]);
                        }
                    }

                    //kalau response image tidak ada di skip
                    if(!isset($fetchBannerCatalog->docs[$key]->image) || $fetchBannerCatalog->docs[$key]->image == ''){
                        unset($fetchBannerCatalog->docs[$key]);
                    }
                }    
            }        


            $result['docs']         = NULL;        
            $result['perPage']      = 0;        
            $result['totalBanner']  = 0;

            //docs result
            if(isset($fetchBannerCatalog->docs) && !empty($fetchBannerCatalog->docs)){
                $result['docs']         = $fetchBannerCatalog->docs;
            }

            //count banner per page        
            if(isset($fetchBannerCatalog->numFound) && $fetchBannerCatalog->numFound > 0){
                $result['perPage']      = $fetchBannerCatalog->numFound;
            }

            //last data fetch       
            unset($solr_param['where']['display_number']);
            $solr_param['where']['display_number']   = urlencode("[0 TO ". $offset. "]");
            $LastCount                  = get_active_solr($solr_param['core_selector'],$solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);                                              
            if(isset($LastCount->numFound) && $LastCount->numFound > 0){
                $result['lastCount']    = $LastCount->numFound;
            }         

            //count total banner on category       
            unset($solr_param['where']['display_number']);
            $TotalBannerCatalog         = get_active_solr($solr_param['core_selector'],$solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);                                              
            if(isset($TotalBannerCatalog->numFound) && $TotalBannerCatalog->numFound > 0){
                $result['totalBanner']  = $TotalBannerCatalog->numFound;
            }            
        } catch (Exception $e){ 
            \Log::error('Problem helper getBannerCatalog with URL : ' . \Request::fullUrl());
        }                         
        
        return $result;
    }
}

/**
* generateLinkBanner
* Generate link untuk banner katalog
* @author Efendy Salim
*/
if (! function_exists('generateLinkBanner')) {
    function generateLinkBanner($paramLanding = array()){        
        $define_domain          = get_domain();
        $domain_id              = $define_domain['domain_id'];              
        $FullUrl                = '/';
        
        $segUrl                 = array();
        $segUrl['segment_1']    = '';
        $segUrl['segment_2']    = '';
        $segUrl['segment_3']    = '';        

        
        $arrLanding        = array();        
        if(isset($paramLanding) && !empty($paramLanding)){
            $arrLanding['landing_page_type']        = $paramLanding['landing_page_type'];
            $arrLanding['landing_page_id']          = $paramLanding['landing_page_id']; 
            $arrLanding['landing_page_url']         = $paramLanding['landing_page_url'];
            $arrLanding['landing_page_segment_1']   = $paramLanding['landing_page_segment_1'];
            $arrLanding['landing_page_segment_2']   = $paramLanding['landing_page_segment_2'];
            $arrLanding['landing_page_segment_3']   = $paramLanding['landing_page_segment_3'];
            
            switch($paramLanding['landing_gender']){
                case 1 :
                    $arrLanding['landing_gender']    = 'women'; 
                    break;
                case 2 :
                    $arrLanding['landing_gender']    = 'men'; 
                    break;
                default :
                    $arrLanding['landing_gender']    = NULL;                    
            }            
        } 
        
        if(!empty($arrLanding['landing_page_type']) && is_numeric($arrLanding['landing_page_type'])){
            $addgenderuri = '';
            switch ($arrLanding['landing_page_type']){
                case 1 : 
                    //special page
                    $segUrl['segment_1']         = $arrLanding['landing_page_url'];
                    $segUrl['segment_2']         = '';
                    $segUrl['segment_3']         = '';
                    if(isset($arrLanding['landing_gender'])){
                        $addgenderuri                = '?gender=' . $arrLanding['landing_gender'] ;    
                    }                   
                    break;
                case 2 :                
                    //get brand-url
                    $segUrl['segment_1']         = 'brand';
                    $segUrl['segment_2']         = '';
                    
                    if(is_numeric($arrLanding['landing_page_id'])){
                        $fq_arr          = array();
                        $query           = null;                     
                        $solr_param      = array();                
                        
                        $solr_param['core_selector'] = getCoreSelector("brand");

                        $fq_arr['id']                   = !empty($arrLanding['landing_page_id']) & is_numeric($arrLanding['landing_page_id']) ? $arrLanding['landing_page_id'] : 0;
                        $solr_param['query']            = isset($query) ? $query : null;
                        $solr_param['where']            = isset($fq_arr) ? $fq_arr : null;          
                        $solr_param['limit']            = 1;
                        $solr_param['offset']           = 0;
                        $solr_param['order']            = NULL;
                        $solr_param['group']            = NULL;
                        $solr_param['field_list']       = NULL;

                        $fetchBrandUrl                  = get_active_solr($solr_param['core_selector'],$solr_param['query'], $solr_param['where'], $solr_param['limit'], $solr_param['offset'], $solr_param['order'], $solr_param['group'], $solr_param['field_list']);                                   

                        if(isset($fetchBrandUrl->docs[0])){
                            $arrBrand                   = $fetchBrandUrl->docs[0];
                            $segUrl['segment_2']        = isset($arrBrand->brand_url) ? $arrBrand->brand_url : '';
                        }    
                    }
                    
                    //end get brand-url                    
                    $segUrl['segment_3']            = '';
                    
                    if(isset($arrLanding['landing_gender'])){
                        $addgenderuri                = '?gender=' . $arrLanding['landing_gender'] ;    
                    }  
                    break;
                case 3 :
                    //special price page
                    $segUrl['segment_1']         = $arrLanding['landing_page_url'];
                    $segUrl['segment_2']         = '';
                    $segUrl['segment_3']         = '';
                    
                    if(isset($arrLanding['landing_gender'])){
                        $addgenderuri                = '?gender=' . $arrLanding['landing_gender'] ;    
                    } 
                    break;
                case 4 : 
                    //front end type
                    $segUrl['segment_1']         = $arrLanding['landing_page_segment_1'];
                    $segUrl['segment_2']         = $arrLanding['landing_page_segment_2'];   
                    $segUrl['segment_3']         = $arrLanding['landing_page_segment_3'];
                    break;
                default :
                    $segUrl['segment_1']            = '';
                    $segUrl['segment_2']            = '';
                    $segUrl['segment_3']            = '';
            }            
        }    
        $url = array();
        $url['seg1']    = isset($segUrl['segment_1']) ? $segUrl['segment_1'] . '/' : '';
        $url['seg2']    = isset($segUrl['segment_2']) ? $segUrl['segment_2'] . '/' : '';
        $url['seg3']    = isset($segUrl['segment_3']) ? $segUrl['segment_3'] : '';
        
        $FullUrl        = $FullUrl . str_replace('//', '/', $url['seg1'] . $url['seg2'] . $url['seg3']) . $addgenderuri;   
        
        return $FullUrl;
    }
}

/**
* getOriginSegment
* Get origin segment
* @author Efendy Salim
*/
if (! function_exists('getOriginSegment')) {
    function getOriginSegment(){
        //get uri segment 
        $arrUriSegment  = generate_uri_segment();        
        
        //segment 
        $segments        = array();
        $segments[1]     = (\Request::segment(1)) ? \Request::segment(1) : 'NULL';
        $segments[2]     = (\Request::segment(2)) ? \Request::segment(2) : 'NULL';
        $segments[3]     = (\Request::segment(3)) ? \Request::segment(3) : 'NULL';
        
        $pagination = NULL;
        if(isset($arrUriSegment['pagination']) && is_numeric($arrUriSegment['pagination'])){
            $pagination = $arrUriSegment['pagination'];
            
            $key = array_search($pagination, $segments); // $key = 2;
            if($key){
                $segments[$key] = 'NULL';
            }
        }                                 
                        
        return $segments;
    }    
}
/** 
** Date in range
**/
if(! function_exists("check_in_range")) {
  function check_in_range($start_date, $end_date, $date_from_user)
    {
      // Convert to timestamp
      $start_ts = strtotime($start_date);
      $end_ts = strtotime($end_date);
      $user_ts = strtotime($date_from_user);

      // Check that user date is between start & end
      return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
}

/** 
** Get Client IP
**/
if(! function_exists("getIp")) {
  function getIp()
  {
    $ip = NULL;
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
  }
}

/**
 * check is Others page
 * Effendy Salim
 */

if(! function_exists("isOthersPage")){
    
    function isOthersPage(){ 
        $isOthersPage = false;    
        $currentAction = \Route::getCurrentRoute();
        
        if($currentAction !== NULL){
            list($path, $method) = explode('@', $currentAction->getActionName());            

            $arrPath = explode("\\",$path);

            if(isset($arrPath[1]) && $arrPath[1] == 'Modules'){    
                if(isset($arrPath[2])){ 
                    switch($arrPath[2]){
                        case 'Landingpage' :                         
                        case 'campaign' :
                            $isOthersPage = true;
                            break;      
                        case 'Account' :
                            $isOthersPage = true;
                            if(isset($method) && $method == 'wishlist'){
                                $isOthersPage = false;
                            }
                            break;
                        default :
                            $isOthersPage = false;
                    }
                }                
                
            }elseif(isset($arrPath[1]) && $arrPath[1] = 'Http' && isset($arrPath[2]) && $arrPath[2] == 'Controllers'){
               if(isset($method) && $method == 'index'){
                   $isOthersPage = false;
               }else{
                   $isOthersPage = true;
               }  
            }else{
                $isOthersPage = false;    
            }     
        }                                       
        
        return $isOthersPage;
    }
}

/**
 * merchant id live chat prism
 * Effendy Salim
 */
if(! function_exists("GetMerchantIdPrism")){
    function GetMerchantIdPrism(){       
        $suffix         = '_dev';
        $appenv         = env('APP_ENV', 'development');
        if ($appenv == 'production') {
            $suffix     = '_live';
        }
        $get_domain     = get_domain(); 
        $domainprism    = 'bb' . $suffix;
        if($get_domain['domain_alias']){
            $domainprism = $get_domain['domain_alias'] . $suffix;
        }

        $prismconfig    = \Config::get('berrybenka.prism_merchant_id');
        $merchantid     = $prismconfig[$domainprism];
        if($merchantid){
            return $merchantid;
        }else{
            return '077daa05-81d3-4d64-93d9-b24b30e6ed10';
        }                    
    }
}

/**
 * client id live chat prism
 * Effendy Salim
 */
if(! function_exists("GetClientKeyPrism")){
    function GetClientKeyPrism(){       
        $suffix         = '_dev';
        $appenv         = env('APP_ENV', 'development');
        if ($appenv == 'production') {
            $suffix     = '_live';
        }
        $get_domain     = get_domain(); 
        $domainprism    = 'bb' . $suffix;
        if($get_domain['domain_alias']){
            $domainprism = $get_domain['domain_alias'] . $suffix;
        }

        $prismconfig    = \Config::get('berrybenka.prism_client_key');
        $clientkey      = $prismconfig[$domainprism];
        if($clientkey){
            return $clientkey;
        }else{
            return '0334aa20fe0c6b9ed81d6703755d8e46037ca875be3576634340b2476075f204';
        }                    
    }    
}


/**
 * js url live chat prism
 * Effendy Salim
 */
if(! function_exists("GetJSUrlPrism")){
    function GetJSUrlPrism(){       
        $suffix         = '_dev';
        $appenv         = env('APP_ENV', 'development');
        if ($appenv == 'production') {
            $suffix     = '_live';
        }
        $get_domain     = get_domain(); 
        $domainprism    = 'bb' . $suffix;
        if($get_domain['domain_alias']){
            $domainprism = $get_domain['domain_alias'] . $suffix;
        }

        $prismconfig    = \Config::get('berrybenka.prism_js_url');
        $prism_js_url   = $prismconfig[$domainprism];
        if($prism_js_url){
            return $prism_js_url;
        }else{
            return 'https://prismapp-files.s3.amazonaws.com/widget/prism.js?';
        }                    
    }    
}

/**
 * check oos
 * Effendy Salim
 */
if(! function_exists("CheckOOS")){
    function CheckOOS(){
        $get_domain         = get_domain(); 
        $domain_name        = $get_domain['domain_name'];
        
        $cacheName          = 'oos-' . $domain_name; 
        $expiresAt          = Carbon::now()->addMinutes(5); 
        
        //where set
        $where['domain_id'] = $get_domain['domain_id'];
                
        Cache::remember($cacheName, $expiresAt, function() use($where){
            return \DB::connection('read_mysql')
                        ->table('set_display_oos')
                        ->select('set_display_oos_set' , 'set_display_oos_category')
                        ->where('set_display_oos_domain_id' , $where['domain_id'])                        
                        ->first();    
        });
        
        $setOos         = Cache::get($cacheName);
        $result         = false;
        if(isset($setOos)){        
            if($setOos->set_display_oos_set == 1){
                $parent     = isset(generate_uri_segment()['parent_type_url']) ? generate_uri_segment()['parent_type_url'] : NULL;
                $parentcat  = isset(generate_get_uri()['cat']) ? generate_get_uri()['cat'] : NULL;
                if(isset($parent)){                     
                    if(isset($setOos->set_display_oos_category)){
                        if(in_array($parent, array_filter(explode(',', $setOos->set_display_oos_category)))){
                            $result = true;
                        }      
                    }                           
                }else{
                    if(isset($setOos->set_display_oos_category)){
                        if(in_array($parentcat, array_filter(explode(',', $setOos->set_display_oos_category)))){
                            $result = true;
                        }  
                    }
                }                                
            }            
        }        
        return $result;
    }
}

/**
 * check oos promo
 * Effendy Salim
 */
if(! function_exists("CheckOOSPromo")){
    function CheckOOSPromo($IDspecialpage = NULL){
        $get_domain         = get_domain(); 
        $domain_name        = $get_domain['domain_name'];                                 
        
        $result         = false;
        if(isset($IDspecialpage)){
            
            //where set
            $where['domain_id']         = $get_domain['domain_id'];
            $where['special_page_id']   = $IDspecialpage;
            
            $cacheName = 'oosPromo['. $IDspecialpage .']-' . $domain_name;
            $expiresAt = Carbon::now()->addMinutes(5);


            Cache::remember($cacheName, $expiresAt, function() use($where) {
                return \DB::connection('read_mysql')
                                ->table('special_page')
                                ->select('special_page_oos')
                                ->where('domain_id', $where['domain_id'])
                                ->where('special_page_id', $where['special_page_id'])
                                ->first();
            });
            
            $setOosPromo  = Cache::get($cacheName);                                
            if(isset($setOosPromo->special_page_oos) && $setOosPromo->special_page_oos == 1){                
                $result = true;
            }    
                
        }
        return $result;        
    }
}

/*
 * redirect forbidRoute to new-arrival
 * Effendy Salim
 */
if(! function_exists("isforbidRoute")){
    function isforbidRoute(){  
        $result = false;     
        if(strpos(url()->full(), '.html') !== false){                
            $result = true;
        }                    

        return $result;
    }
}

/**
 * merchant id live GCR
 * Boan TP
 */
if(! function_exists("GetMerchantIdGcr")){
    function GetMerchantIdGcr(){       
        $suffix         = '_dev';
        $appenv         = env('APP_ENV', 'development');
        if ($appenv == 'production') {
            $suffix     = '_live';
        }
        $get_domain     = get_domain(); 
        $domaingcr    = 'bb' . $suffix;
        if($get_domain['domain_alias']){
            $domaingcr = $get_domain['domain_alias'] . $suffix;
        }

        $gcrconfig    = \Config::get('berrybenka.gcr_merchant_id');
        $merchantid     = $gcrconfig[$domaingcr];
        if($merchantid){
            return $merchantid;
        }else{
            return '100941103';
        }                    
    }
}

if ( ! function_exists('getSnowflakeEnv'))
{
    function getSnowflakeEnv() {
        $app_env = env('APP_ENV');
        $snowflake_env = env('SNOWFLAKES_TAG');

        if($snowflake_env == "enable") {
            return true;
        } else {
            return false;
        }
    }
}
/**
 * Generate image qrcode
 * Effendy
 * @string string to encode
 * @imgType type image eps/png/svg, default : png
 * @size size of qrcode, default : 100
 */

if(! function_exists("GenerateQRCodeIMG")){
    function GenerateQRCodeIMG($string = null, $size = 100, $imgType = 'png'){
        $result = '';        
        if($string){
            $result = 'data:image/png;base64, '. base64_encode(\QrCode::format($imgType)->margin(0)->size($size)->generate($string));
        }
        
        return $result;
    }
}

?>
