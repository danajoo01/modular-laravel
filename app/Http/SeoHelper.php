<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * @author Effendy
 * @copyright  Copyright (c) 2016 PT.Berrybenka (http://www.berrybenka.com)
 */
use \App\Modules\Product\Models\Product;
use \App\Modules\Seo\Models\Seo;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;

if (! function_exists('DefineSeo')) {
  function DefineSeo()
  {
    // Start of code
    $time = microtime(true); // Gets microseconds    
    //Generate get uri  
      
      
    $generate_uri_segment   = generate_uri_segment();//bb_debug($generate_uri_segment);//die;
    \Log::info('check_type_front_end_type : '.json_encode($generate_uri_segment).'');
    
    $gender                 = $generate_uri_segment['gender'] ? $generate_uri_segment['gender'] : NULL;
    $parent_type_url        = $generate_uri_segment['parent_type_url'] ? $generate_uri_segment['parent_type_url'] : NULL;
    $child_type_url         = $generate_uri_segment['child_type_url'] ? $generate_uri_segment['child_type_url'] : NULL;
    $sub_child_type_url     = $generate_uri_segment['sub_child_type_url'] ? $generate_uri_segment['sub_child_type_url'] : NULL;
            
    $get_domain     = get_domain();
    $domain_id      = $get_domain['domain_id'];
    $domain_name    = $get_domain['domain_name'];
    
    if(Route::getCurrentRoute() != NULL){
      $currentAction = Route::getCurrentRoute()->getActionName();
      list($controller, $method) = explode('@', $currentAction);    
      $controller = preg_replace('/.*\\\/', '', $controller); 
    }else{
      $controller = '';
      $method = '';      
    }
    
    // Validate title by Gender
    if ($gender) {
      switch($gender) {
        case "women"  : 
          $title_default  = 'Jual Fashion Wanita, Pakaian, dan Aksesoris.';
          $Gender         = "wanita";
          break;
        case "men"    : 
          $title_default  = 'Jual Fashion Pria, Pakaian, dan Sepatu.';
          $Gender         = "pria";
          break;				
      }	
    } else {
      // SET DEFAULT VALUE
      $Gender             = " wanita, pria";
      
      if($domain_id === 3){
        $title_default 	  = 'Discover your new lifestyle! | Shopdeca.com';  
      }else{
        $title_default 	  = 'Toko Fashion Wanita dan Pria Online.';    
      }
      
    }   
    
    // Validate Domain
    switch($domain_id){
        case 1 :
            $title_default        = $title_default;
            $domain_text          = 'Berrybenka';       
            $keywords_default     = 'indonesia, jual, pakaian, '.$Gender.', fashion, butik, dress, pakaian';
            $desc_default         = 'Merupakan butik online, toko baju online terlengkap di Indonesia.Toko baju online, butik online, pakaian batik, baju batik, baju wanita, baju tidur, pakaian pesta, baju anak, baju cewek, baju lingerie, kacamata, tas impor, mainan anak, sandal, aksesoris, sepatu dan sebagainya. Harga sangat terjangkau, dan koleksinya sangat lengkap. One Shop Many Brands'; 
            $footer_text_default  = '<p>situs belanja online fesyen dan kecantikan ternama di Indonesia. Onedeca menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label.Onedeca menawarkan kombinasi produk fesyen dan kecantikan terkini untuk setiap gaya personal yang beragam.</p><p>Kami menyediakan produk berkualitas terbaik untuk wanita dan pria, bervariasi dari pakaian, aksesori, sepatu, tas, produk olahraga dan kecantikan. Komitmen kami adalah memberikan pengalaman belanja online yang menyenangkan, mudah, dan terpercaya untuk memuaskan pelanggan dengan koleksi baru dan penawaran spesial setiap harinya, serta beragam keuntungan seperti kemudahan pengembalian produk hingga 14 hari setelah barang diterima, layanan bayar di tempat dan pengiriman gratis. </p>';
            break;
        case 2 :
            $title_default        = 'Jual Baju dan Busana Muslim Modern | Hijabenka';
            $domain_text          = 'Hijabenka';  
            $keywords_default     = 'indonesia, jual, pakaian, muslim, fashion, butik, hijab';
            $desc_default         = 'Toko Online yang Menjual berbagai macam busana muslim modern, berkualitas, terbaru dan trendi dengan harga murah.';      
            $footer_text_default  = ' <p>Hijabenka.com adalah situs belanja fesyen muslimah terkemuka di Indonesia. Hijabenka menjual lebih dari 200 merek lokal dan desainer muslim ternama untuk setiap gaya personal hijabers yang beragam. Kami menyediakan produk fesyen berkualitas dengan harga terjangkau yang bervariasi dari pakaian muslimah, gamis/abaya, jilbab, basic, aksesori, sepatu, dan tas. Komitmen kami adalah memberikan pengalaman belanja online yang menyenangkan, mudah, dan terpercaya melalui koleksi baru dan penawaran spesial setiap harinya. Situs Hijabenka.com juga menawarkan beragam keuntungan seperti kemudahan pengembalian produk hingga 14 hari setelah barang diterima, pengiriman gratis, dan layanan bayar di tempat. </p>'; 
            break;
        case 3 :
            $title_default        = $title_default;
            $domain_text          = 'Shopdeca';       
            $keywords_default     = 'indonesia, jual, pakaian, '.$Gender.', fashion, butik, dress, pakaian';
            $desc_default         = 'Shopdeca.com is your online one-stop lifestyle destination in Indonesia. Shopdeca sold more various local and international brand. Shopdeca.com offers a combination of curated lifestyle products and the latest fashion products for every variety style.We provide curated products for women and men, ranging from clothing, accessories, shoes, bags, sports, beauty and lifestyle products. Our commitment is to provide an online shopping experience that is fun, easy, and trusted to satisfy our customers with new collections and special daily offers, and many various advantages such as ease of returning products until 7 days after the item is received.'; 
            $footer_text_default  = '<p>Shopdeca.com is your online one-stop lifestyle destination in Indonesia. Shopdeca sold more various local and international brand. Shopdeca.com offers a combination of curated lifestyle products and the latest fashion products for every variety style.We provide curated products for women and men, ranging from clothing, accessories, shoes, bags, sports, beauty and lifestyle products. Our commitment is to provide an online shopping experience that is fun, easy, and trusted to satisfy our customers with new collections and special daily offers, and many various advantages such as ease of returning products until 7 days after the item is received. </p>';
            break;
        default :
            $title_default        = $title_default;
            $domain_text          = 'Onedeca';       
            $keywords_default     = 'indonesia, jual, pakaian, fashion, butik, dress, pakaian';
            $desc_default         = 'Merupakan butik online, toko baju online terlengkap di Indonesia.Toko baju online, butik online, pakaian batik, baju batik, baju wanita, baju tidur, pakaian pesta, baju anak, baju cewek, baju lingerie, kacamata, tas impor, mainan anak, sandal, aksesoris, sepatu dan sebagainya. Harga sangat terjangkau, dan koleksinya sangat lengkap. One Shop Many Brands'; 
            $footer_text_default  = '<p>situs belanja online fesyen dan kecantikan ternama di Indonesia. Onedeca menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label.Onedeca menawarkan kombinasi produk fesyen dan kecantikan terkini untuk setiap gaya personal yang beragam.</p><p>Kami menyediakan produk berkualitas terbaik untuk wanita dan pria, bervariasi dari pakaian, aksesori, sepatu, tas, produk olahraga dan kecantikan. Komitmen kami adalah memberikan pengalaman belanja online yang menyenangkan, mudah, dan terpercaya untuk memuaskan pelanggan dengan koleksi baru dan penawaran spesial setiap harinya, serta beragam keuntungan seperti kemudahan pengembalian produk hingga 14 hari setelah barang diterima, layanan bayar di tempat dan pengiriman gratis. </p>';
            break;
    }

    //get segment       
    $segment_1          = (Request::segment(1)) ? Request::segment(1) : "NULL";          
    $segment_2          = (Request::segment(2)) ? Request::segment(2) : "NULL";
    $segment_3          = (Request::segment(3)) ? Request::segment(3) : "NULL";
    $footer_text        = $footer_text_default;
    
    if($controller == 'ProductDetailController'){ // PRODUCT DETAIL 
        $product_id = $segment_3;       

        switch($domain_id){
            case 1 :
                $fields['default']        = 'default_bb';
                $fields['own']            = 'own_bb';
                $fields['default_promo']  = 'default_promo_bb';
                $fields['own_promo']      = 'own_promo_bb';
                break;
            case 2 :
                $fields['default']        = 'default_hb';
                $fields['own']            = 'own_hb';
                $fields['default_promo']  = 'default_promo_hb';
                $fields['own_promo']      = 'own_promo_hb';
                break;
            case 3 :
                $fields['default']        = 'default_sd';
                $fields['own']            = 'own_sd';
                $fields['default_promo']  = 'default_promo_sd';
                $fields['own_promo']      = 'own_promo_sd';
                break;
            default :
                $fields['default']        = 'default_bb';
                $fields['own']            = 'own_bb';
                $fields['default_promo']  = 'default_promo_bb';
                $fields['own_promo']      = 'own_promo_bb';
        }
      
      //***** FOR CHECK PRODUCT STATUS AVAILABLE ****//
      //$check_product = Product::checkProductAvailable($product_id);
      //***** END FOR CHECK PRODUCT STATUS AVAILABLE ****//
      try{
        $get_product = Product::fetch_product_detail($product_id , $fields);
        //var_dump($get_product);
        //exit;
        if($get_product){
          $product_type         = (isset($get_product['fetch_product']->type_id)) ? ucfirst($get_product['fetch_product']->type_id) : 'Type';
          $product_name         = (isset($get_product['fetch_product']->product_name)) ? $get_product['fetch_product']->product_name :  $domain_text . ' Product'; 
          $product_description  = (isset($get_product['fetch_product']->product_description)) ? $get_product['fetch_product']->product_description :  $domain_text . ' Product Description';

          $title                = 'Sell '.$product_name." ".$product_type." | ". $domain_text .".com";
          $keywords             = $product_type." ".$product_name;
          $desc                 = $product_name." ".strip_tags($product_description); 
        } 
      } catch (Exception $ex) { 
         $title = $title_default;
         $keywords = $keywords_default;
         $desc = $desc_default;
      }
                             
    }else{ // product category and other page (home,promo,lookbook)
        $segment_1            = (substr($segment_1, -4) == '_mob') ? substr($segment_1, 0, -4) : $segment_1; // mobile handler
        $type                 = ($controller == 'ProductController' || $controller == 'ProductDetailController') ? 'product' : 'others';
                
//        if ($domain_id == 1) { 
//            $cacheSeo       = 'seo-key-'.$segment_1.'-'.$segment_2.'-'.$segment_3.'-bb';    
//        }elseif($domain_id == 2) {
//            $cacheSeo       = 'seo-key-'.$segment_1.'-'.$segment_2.'-'.$segment_3.'-hb';    
//        }elseif($domain_id == 4) {
//            $cacheSeo       = 'seo-key-'.$segment_1.'-'.$segment_2.'-'.$segment_3.'-homepage';    
//        }else{
//            $cacheSeo       = 'seo-key-'.$segment_1.'-'.$segment_2.'-'.$segment_3.'-all';   
//        }        
        
//        $expiresAt      = Carbon::now()->addMinutes(60);
//        $getSeo         = Cache::remember($cacheSeo, $expiresAt, function() use($segment_1, $segment_2, $segment_3, $type){                            
//            $data = Seo::getSeo($segment_1, $segment_2, $segment_3, $type);
//            //\Log::notice('hasil store cache = '. json_encode($data));
//            return $data;
//        });      
        $getSeo         = Seo::getSeo($segment_1, $segment_2, $segment_3, $type);

        $title          = isset($getSeo->title) ? $getSeo->title : NULL ;
        $keywords       = isset($getSeo->meta_keywords) ? $getSeo->meta_keywords : NULL;
        $desc           = isset($getSeo->meta_description) ? $getSeo->meta_description : NULL;
        //$footer_text    = isset($getSeo->footer_text) ? $getSeo->footer_text : NULL;
    	$footer_text    = "";
    }    
    $data['title']          = (isset($title)         && $title != "")        ? $title        : $title_default;
    $data['keywords']       = (isset($keywords)      && $keywords != "")     ? $keywords     : $keywords_default;
    $data['description']    = (isset($desc)          && $desc != "")         ? $desc         : $desc_default;
    $data['footer_text']    = (isset($footer_text)   && $footer_text != "")  ? $footer_text  : $footer_text_default;
    
    //echo Route::getCurrentRoute()->getActionName();
    //exit; 
    
    $canonical = DefineCanonical();
    $data['canonical'] = (!is_null($canonical)) ? $canonical : '';
    
    $alternate = DefineAlternate();
    $data['alternate'] = (!is_null($alternate)) ? $alternate : '';        
    return $data;
  }
}

if ( ! function_exists('DefineCanonical')) // SEMUA HALAMAN MOBILE REFER KE HALAMAN DESKTOP NYA
{
  function DefineCanonical()
  {
    $host = Request::server('SERVER_NAME');
    $canonical = NULL;
    if(Route::getCurrentRoute() != NULL){
      $currentAction = Route::getCurrentRoute()->getActionName();
      list($controller, $method) = explode('@', $currentAction);    
      $controller = preg_replace('/.*\\\/', '', $controller); 
    }else{
      $controller = '';
      $method = '';      
    }
    
    if(substr($host, 0, 1) == 'm') {
      $url = Request::fullUrl();
      $desktop_host = substr($host, 0, 2);
      $current_url = str_replace(array($desktop_host, '_mob'), array('',''), $url);
      
      if($controller == 'HomeController' || $controller == 'ProductController' || $controller == 'AccountController') { // IF URL NOT EXIST
        $canonical = $current_url;
      }      
    }
    
    return $canonical;
  }
}

if(! function_exists('DefineAlternate'))
{ // SEMUA HALAMAN DESKTOP REFER KE HALAMAN MOBILE NYA
  function DefineAlternate()
  {
    $host = Request::server('SERVER_NAME');
    $alternate = NULL;
    if(Route::getCurrentRoute() != NULL){
      $currentAction = Route::getCurrentRoute()->getActionName();
      list($controller, $method) = explode('@', $currentAction);    
      $controller = preg_replace('/.*\\\/', '', $controller); 
    }else{
      $controller = '';
      $method = '';      
    }
    $url = Request::fullUrl();
    $httpnya = "http" . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s" : "") . "://";
    $mobile_url = explode($httpnya, str_replace($controller, $controller."_mob", $url));
    $mobile_url = 'm.'.$mobile_url[1];
    $mobile_url = $httpnya.$mobile_url;
    
    if($controller == 'HomeController' || $controller == 'ProductController' || $controller == 'AccountController') { // IF URL NOT EXIST
      $alternate = $mobile_url;
    } 
    
    return $alternate;
  }
}


/**
 * implementation involve-asia ref CI seo_helper
 *
 * @created : Effendy / 01-09-2016.
 */
if ( ! function_exists('utmInvolveAsia'))
{
  function utmInvolveAsia()
  {
    
    //get  
    $utm_source         = Input::get('utm_source');
    $utm_medium         = Input::get('utm_medium');
    $utm_campaign       = Input::get('utm_campaign');
    $session_id         = Input::get('session_id');
 
    if (isset($utm_source)) {
      if (!is_array($utm_source)) {
        $utm_source = strtolower($utm_source);
      }
    } else {
      $utm_source = "";
    }
    // $utm_source         = isset($utm_source) ? strtolower($utm_source) : '';
    $utm_medium         = isset($utm_medium) ? $utm_medium : '';
    $utm_campaign       = isset($utm_campaign) ? $utm_campaign : '';
    $session_id         = isset($session_id) ? $session_id : '';
    
    
    $utm = array(
      'utm_source'      => $utm_source,
      'utm_medium'      => $utm_medium,
      'utm_campaign'    => $utm_campaign
    );
    
    if((session('utm_source')==false && $utm_source != '') || (session('utm_source')!=$utm_source  && $utm_source!='')){
      session()->put('utm', $utm);
    }
    
    //********* implementation involve-asia ***********//
    if($utm_source != '' && $utm_source == 'shopstylers' && $session_id != ''){      
      $expire_time = time() + 60 * 60 * 24 * 30;      

      $setcookie = setcookie('iasia_utmz', "{'utm_source':$utm_source,'session_id':$session_id}",$expire_time, '/', $_SERVER['HTTP_HOST']);       
    }
    //********* implementation involve-asia ***********//       
  }
}

/**
 * implementation hasoffers ref CI seo_helper
 *
 * @created : Effendy / 01-09-2016.
 */
if ( ! function_exists('utmHasoffers'))
{
  function utmHasoffers()
  {
    
    //get  
    $utm_source         = Input::get('utm_source');
    $utm_medium         = Input::get('utm_medium');
    $utm_campaign       = Input::get('utm_campaign');
    $transaction_id     = Input::get('transaction_id');
 
    if (isset($utm_source)) {
      if (!is_array($utm_source)) {
        $utm_source = strtolower($utm_source);
      }
    } else {
      $utm_source = "";
    }
    // $utm_source         = isset($utm_source) ? strtolower($utm_source) : '';
    $utm_medium         = isset($utm_medium) ? $utm_medium : '';
    $utm_campaign       = isset($utm_campaign) ? $utm_campaign : '';
    $transaction_id     = isset($transaction_id) ? $transaction_id : '';
    
    
    $utm = array(
      'utm_source'      => $utm_source,
      'utm_medium'      => $utm_medium,
      'utm_campaign'    => $utm_campaign
    );
    
    if((session('utm_source')==false && $utm_source != '') || (session('utm_source')!=$utm_source  && $utm_source!='')){
      session()->put('utm', $utm);
    }
    
    //********* implementation involve-asia ***********//
    if($utm_source != '' && $utm_source == 'hasoffers' && $transaction_id != ''){      
      $expire_time = time() + 60 * 60 * 24 * 30;      
      
      $setcookie = setcookie('hasoffers_utmz', "{'utm_source':$utm_source,'transaction_id':$transaction_id}",$expire_time, '/', $_SERVER['HTTP_HOST']);       
    }
    //********* implementation involve-asia ***********//       
  }
}
