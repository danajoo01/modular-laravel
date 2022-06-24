<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\TestBean;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Log; 
use App\Modules\Checkout\Models\Shipping;
use Carbon\Carbon;
use QrCode;

class HomeController extends Controller
{
    /** 
     * Create a new controller instance.
     *
     * @return void 
     */
    public function __construct()
    {
        //$this->middleware('auth'); 
    }

    /** 
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {                
        return view('testqrcode');
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');        
        return view("footer-info.".$folders[$channel].".about");
    }

    /**
     * Show the Affiliate page.
     *
     * @return \Illuminate\Http\Response
     */
    public function affiliate($page = NULL)
    {       
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];        
        $domain_id  = $get_domain['domain_id'];        
        
        
        //if($domain_id == 3){
            abort(404);
        //}
        $folders    = \Config::get('berrybenka.folders');
        
        switch ($page) {
          case 'faq' : 
            return view("footer-info.".$folders[$channel].".affiliate-faq");
            break;
          case 'illustration' : 
            return view("footer-info.".$folders[$channel].".affiliate-illustration");
            break;
          case 'referral' : 
            return view("footer-info.".$folders[$channel].".affiliate-referral");
            break;
          default : return view("footer-info.".$folders[$channel].".affiliate");
        }        
    }

    /**
     * Show the Brand list page.
     *
     * @return \Illuminate\Http\Response
     */
    public function brand()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        $folders    = \Config::get('berrybenka.folders');

        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
      
        return view('footer-info.'.$folders[$channel].'.brand-list');
    }

    /**
     * Show the FAQ page.
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders = \Config::get('berrybenka.folders');
        $data['page']   = 'faq';
        return view("footer-info.".$folders[$channel].".faq",$data);
    }
    
    /**
     * Show the Contact page.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders        = \Config::get('berrybenka.folders');
        $data['page']   = 'contact';
        return view("footer-info.".$folders[$channel].".contact",$data);
    }

    /**
     * Show the COD page.
     *
     * @return \Illuminate\Http\Response
     */
    public function cod()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders        = \Config::get('berrybenka.folders');
        $data['page']   = 'cod';
        return view("footer-info.".$folders[$channel].".cod",$data);
    }

    /**
     * Show the featured-brand page.
     *
     * @return \Illuminate\Http\Response
     */
    public function featuredBrand()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');
        return view("footer-info.".$folders[$channel].".featured-brand");
    }

    /**
     * Show the help return page.
     *
     * @return \Illuminate\Http\Response
     */
    public function helpReturn()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');
        
        //Get Province
        $params_province['shipping_type'] = 1;
        $params_province['type']          = 2;
         
        $cacheName      = 'list-province';
        $expiresAt      = Carbon::now()->addMinutes(60);
        $list_province  = Cache::remember($cacheName, $expiresAt, function() use($params_province){                                        
            return Shipping::getShippingList($params_province);    
        });
        
        $data['list_province']            = (isset($list_province) && !empty($list_province)) ? $list_province : array() ;
        //End Get Province                
        
        $data['page']                     = 'help_return';
        return view("footer-info.".$folders[$channel].".help-return",$data);
    }

    /**
     * Show the help return watch page.
     *
     * @return \Illuminate\Http\Response
     */
    public function helpReturnWatch()
    {
        $get_domain   = get_domain();
        $channel      = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders      = \Config::get('berrybenka.folders');
        $data['page'] = 'help_return_watch';
        return view("footer-info.".$folders[$channel].".help-return-watch",$data);        
    }
    
    /**
     * Show the help return watch page.
     *
     * @return \Illuminate\Http\Response
     */
    public function kredivo()
    {
        $get_domain   = get_domain();
        $channel      = $get_domain['channel'];
        
        $folders      = \Config::get('berrybenka.folders');
        $data['page'] = 'kredivo';
        return view("footer-info.".$folders[$channel].".kredivo",$data);        
    }

    /**
     * Show the help return watch page.
     *
     * @return \Illuminate\Http\Response
     */
    public function howToOrder()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');
        
        $data['page']   = 'how_to_order';
        return view("footer-info.".$folders[$channel].".how-to-order",$data);
    }

    /**
     * Show the referal page.
     *
     * @return \Illuminate\Http\Response
     */
    public function referal()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        return view('footer-info.referal');
    }

    /**
     * Show the same day page.
     *
     * @return \Illuminate\Http\Response
     */
    public function sameDay()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');
        $data['page']   = 'same-day';
        return view("footer-info.".$folders[$channel].".same-day",$data);
    }

    /**
     * Show the shipping handling page.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipping()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        
        if($channel == 2 || $channel == 4 || $channel == 6){
          abort(404);
        }
        
        $folders    = \Config::get('berrybenka.folders');
        
        //Get Province
        $params_province['shipping_type'] = 1;
        $params_province['type']          = 2;
        $orderby                          = "shipping_area";
        
        $cacheName      = 'list-province';
        $expiresAt      = Carbon::now()->addMinutes(60);
        $list_province  = Cache::remember($cacheName, $expiresAt, function() use($params_province){                                        
            return Shipping::getShippingList($params_province);    
        });
        
        $data['page']                     = 'shipping_handling';
        $data['list_province']            = (isset($list_province) && !empty($list_province)) ? $list_province : array() ;        
        //End Get Province 
        
        return view("footer-info.".$folders[$channel].".shipping-handling",$data);
    }
    
    /**
     * download pdf.
     *
     * @return \Illuminate\Http\Response
     */
    public function DownloadPDF(){
        $get_domain     = get_domain();
        $domain_id      = $get_domain['domain_id'];
        $protocol = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
        $server = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        
        if($domain_id == 2){
          $FileName = 'form-retur.pdf';
          // $FileURL  = $protocol.$server.'/pdf/new-form-retur.pdf';
          $FileURL  = $protocol.$server.'/pdf/'.$FileName;
        }elseif($domain_id == 3){
          $FileName = 'Shopdeca-Return-Form.pdf';
          $FileURL  = 'http://www.berrybenka.com/pdf/shopdeca-return-form.pdf';
        }else{
          $FileName = 'form-retur.pdf';
          // $FileURL  = $protocol.$server.'/pdf/new-form-retur.pdf';
          $FileURL  = $protocol.$server.'/pdf/'.$FileName;
        }

        return \Response::make(file_get_contents($FileURL), 200, [  
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$FileName.'"'
        ]);
    }
    

    /**
     * Show the shipping handling page.
     *
     * @return \Illuminate\Http\Response
     */
    public function termCondition()
    {
        $get_domain     = get_domain();
        $channel 	= $get_domain['channel'];
        $folders        = \Config::get('berrybenka.folders');
        return view("footer-info.".$folders[$channel].".term-condition");
    }
    
    public function privacy()
    {
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $folders        = \Config::get('berrybenka.folders');
        return view("footer-info.".$folders[$channel].".privacy");
    }


    public function error() {
        return view('errors.503');
    }

    public function queue() {
        $job = (new TestBean());
        $this->dispatch($job);
    }

    public function log() {
        $monolog = Log::getMonolog();
        var_dump($monolog);
    }

    public function mysql_conn()
    {
        $link = mysqli_connect(env('DB_HOST'), env('DB_USERNAME'), env('DB_PASSWORD'));
        if (!$link) die('Could not connect: ' . mysqli_error());
        print "MySQL server version: " . mysqli_get_server_info($link);
        mysqli_close($link);
    }

    public function solr_conn()
    {
        $url      = solr_site().'/admin/info/system'; 
        $homepage = file_get_contents($url);
        $xml = simplexml_load_string($homepage);

        foreach($xml->children() as $child) {
            
                $role = $child->attributes();

                foreach($child as $key => $value) {

                    $child_role = $value->attributes();
                    
                    if($child_role == "QTime") {
                        echo("SOLR ResponseHeader QTime ".$value . "<br />");
                    } elseif ($child_role == "solr-spec-version") {
                        echo("SOLR Server Version ".$value . "<br />");
                    }
                }
        }
    }

    public function redis_conn()
    {
        $redis = \Redis::connection();
        $host = $redis->getConnection()->getParameters()->host;
        $port = $redis->getConnection()->getParameters()->port;
        $fp = @fsockopen($host, $port);

        if ( ! $fp) die('Redis server not responding at *' . $host . ':' . $port . '*');

        echo "Redis Host : " . $host . " Redis Port : " . $port;
    }
    
    public function logs_size()
    {
    	$file_size = 0;
    	
    	foreach( \File::allFiles(storage_path('logs')) as $file)
    	{
    		$file_size += $file->getSize();
    	}
    	echo "Folder Logs Size : " . number_format($file_size / 1048576,2) . "MB";
    }

    public function status()
    {
        $laravel = app();
        $version = $laravel::VERSION;
        echo 'Laravel Version : '.$version.'';
        echo '<br />';
        echo "PHP Current version : " . phpversion();
        echo '<br />';
        $this->mysql_conn();
        //echo '<br />';
        //$this->redis_conn();
        echo '<br />';
        $this->solr_conn();
        echo '<br />';
        $this->logs_size();
        // echo '<br />';
        // if(getAppEnv() == 'production') {
        // 	echo "Beanstalkd Console : <a href='http://beanstalkd.berrybenka.com/'>Beanstalk Console URL</a>";
        // 	echo '<br />';
        // } else {
        // 	echo "Beanstalkd Console : <a href='http://beanstalk.berrybenka.biz/'>Beanstalk Console URL</a>";
        // 	echo '<br />';
        // }

        try
        {
            $filename = public_path()."/lb.txt";
            $contents = \File::get($filename);var_dump($contents);die;
            echo $contents;
        }
        catch (Illuminate\Filesystem\FileNotFoundException $exception)
        {
            die("The file doesn't exist");
        }
    }

    public function boanversion()
    {
        phpinfo( );
    }

    public function js_log(Request $request) {
      $jsErrorLog = array();
      $jsErrorLog['errorMsg']         = $request->get('errorMsg');
      $jsErrorLog['errorLine']        = $request->get('errorLine');
      $jsErrorLog['column']           = $request->get('column');
      $jsErrorLog['errObject']        = $request->get('errObject');
      $jsErrorLog['queryString']      = $request->get('queryString');
      $jsErrorLog['url']              = $request->get('URL');
      $jsErrorLog['referrer']         = $request->get('referrer');
      $jsErrorLog['userAgent']        = $request->get('userAgent');

      $errMsg = 'Javascript error: '.$jsErrorLog['errorMsg'].'; Error line:'.$jsErrorLog['errorLine'].'; Column No: '.$jsErrorLog['column'].'; Error Object: '.$jsErrorLog['errObject'].'; Query String: '.$jsErrorLog['queryString'].'; URL: '.$jsErrorLog['url'].' ; Referrer: '.$jsErrorLog['referrer'].'; User Agent: '.$jsErrorLog['userAgent'];

      if(getSlackEnv() == 1 && getAppEnv() == 'development') {
        \Log::error($errMsg);

        define('SLACK_WEBHOOK', 'https://hooks.slack.com/services/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');
        // Make your message
        $message = array('payload' => json_encode(array('text' => $errMsg)));
        // Use curl to send your message
        $c = curl_init(SLACK_WEBHOOK);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $message);
        curl_exec($c);
        curl_close($c);
      }
    }
   
}
