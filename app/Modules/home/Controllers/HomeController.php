<?php 

namespace App\Modules\Home\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;
use \App\Modules\Home\Models\Home;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Auth;

class HomeController extends Controller {

    /**
     * Display a listing of the Benka Point.
     *
     * @return Response
     */
    public function index()
    {
        $get_domain = get_domain();
	$channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];

        //fetch home page
        $where["default"]      = 1;
        $where["domain_key"]   = $channel;     
        
        $cacheName      = 'homepage-' . $domain;         
        //$expiresAt      = Carbon::now()->addMinutes(1);


        //if($domain_id == 3) {
        //  $expiresAt      = Carbon::now()->addMinutes(1);
        //}
        
        //retrieve homepage from the cache or, if they don't exist, retrieve them from the database and add them to the cache
        //Cache::remember($cacheName, $expiresAt, function() use($where){                            
        //    return Home::fetch_home_page($where);
        //});        	
        
        //$home_page         = Cache::get($cacheName); 
        $home_page     = Home::fetch_home_page($where);
 //additional snowflakes for desktop bb
        $snowflakes_start = strtotime(date('Y-m-d H:i:s'));
        $snowflakes_end   = strtotime(date('2016-12-25 23:59:59'));
        $data['show_snowflakes'] = $snowflakes_start < $snowflakes_end;
    
        if(count($home_page) > 0){
          $home_page_html = $home_page->homepage_html;
          $server_name = \Request::server('SERVER_NAME');
          if(strpos($server_name, 'beta') !== false || strpos($server_name, 'm-beta') !== false){
            $home_page_html = str_replace("http://berrybenka.com", "http://beta.berrybenka.com", $home_page_html);
            $home_page_html = str_replace("http://m.berrybenka.com", "http://m-beta.berrybenka.com", $home_page_html);

            $home_page_html = str_replace("http://hijabenka.com", "http://beta.hijabenka.com", $home_page_html);
            $home_page_html = str_replace("http://m.hijabenka.com", "http://m-beta.hijabenka.com", $home_page_html);
          }else{
            $home_page_html = str_replace("http://beta.berrybenka.com", "http://front.onedeca.com", $home_page_html);
            $home_page_html = str_replace("http://m-beta.berrybenka.com", "http://m-front.onedeca.com", $home_page_html);

            $home_page_html = str_replace("http://beta.hijabenka.com", "http://hijabenka.com", $home_page_html);
            $home_page_html = str_replace("http://m-beta.hijabenka.com", "http://m.hijabenka.com", $home_page_html);
          }
        }
    
        $data['home'] = (isset($home_page_html)) ? $home_page_html : '';

        return get_view('home', 'home.index', $data);
    }
}
