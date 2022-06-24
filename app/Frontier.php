<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Frontier extends Model
{        
    private static function getListId(){     
        $listId                 = '';
        $frontier_config       = \Config::get('berrybenka.frontier');                
        $frontierList          = $frontier_config['listID_LIVE'];
        
        if(env('APP_ENV', 'development') == 'development'){
            $frontierList      = $frontier_config['listID_DEV'];
        }
        
        $get_domain             = get_domain();            
        $domain_alias           = strtolower($get_domain['domain_alias']);        
        if($domain_alias){
            $listId             = $frontierList['listID_' . $domain_alias];            
        }else{
            $listId             = $frontierList['listID_bb'];            
        }
        
        return $listId;
    } 
    
    
    public static function call_frontier($frontier_data, $type = 'contact', $activity = 'add'){
        $get_domain     = get_domain();
        $alias          = $get_domain['domain_alias'];

        $frontier_config = \Config::get('berrybenka.frontier');
        $listId = Self::getListId();
        
        $url = $frontier_config['api_url'].'?type='.$type.'&activity='.$activity.'&listid='.$listId.'&apikey='.$frontier_config['apikey'].'&data='.json_encode($frontier_data);   
        \Log::alert('REQUEST TO FRONTIER : '.$url);
        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $frontier_config['apikey']);            
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);            
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $args);        
       
        $result = curl_exec($ch);
        \Log::alert('FRONTIER RESPONSE : '.$result);    
        curl_close($ch);
        return $result ? json_decode($result, true) : false;
    } 
           
}
