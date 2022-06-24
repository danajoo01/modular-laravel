<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mailchimp extends Model
{        
    private static function getListId(){     
        $listId                 = '';
        $mailchimp_config       = \Config::get('berrybenka.mailchimp');                
        $mailchimpList          = $mailchimp_config['listID_LIVE'];
        
        if(env('APP_ENV', 'development') == 'development'){
            $mailchimpList      = $mailchimp_config['listID_DEV'];
        }
        
        $get_domain             = get_domain();            
        $domain_alias           = strtolower($get_domain['domain_alias']);        
        if($domain_alias){
            $listId             = $mailchimpList['listID_' . $domain_alias];            
        }else{
            $listId             = $mailchimpList['listID_bb'];            
        }
        
        return $listId;
    } 
    
    
    public static function put($method, $args=NULL){
        $get_domain     = get_domain();
        $alias          = $get_domain['domain_alias'];

        $mailchimp_config = \Config::get('berrybenka.mailchimp');
        //\Log::info('API KEY PUT: '.$mailchimp_config['apikey'."_".$alias] .' - '.$mailchimp_config['api_url_'.$alias]); 
        //$args['apikey'] = $mailchimp_config['apikey'];
        //$url = $mailchimp_config['api_url'].'/'.$method.'.json';
        //$url = $mailchimp_config['api_url'].$method;
        $url = $mailchimp_config['api_url_'.$alias].$method;   
               
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $mailchimp_config['apikey'."_".$alias]);            
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);            
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        //\Log::alert('MAILCHIMP REQUEST CALLV3 : '.$args);
        //\Log::alert('MAILCHIMP API KEY : ' . json_encode($mailchimp_config['apikey'."_".$alias]));
        $result = curl_exec($ch);
        //\Log::alert('MAILCHIMP RESPONSE CALLV3: '.$result);    
        curl_close($ch);
        return $result ? json_decode($result, true) : false;
    } 
    
    public static function order_add($params) {
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id      = $get_domain['domain_id'];   
            
        if($domain_id == 1){
          $groups         = 'Berrybenka';
          $lastPurchase   = 'BBLASTPURC';
        } elseif($domain_id == 2) {
          $groups         = 'Hijabenka';
          $lastPurchase   = 'HBLASTPURC';
        } else {
          $groups         = 'Shopdeca';
          $lastPurchase   = 'BBLASTPURC';
        }
        
        $listId             = Self::getListId();
        $subscriber_email   = $params['customer_email'];
        $mailchimp_config   = \Config::get('berrybenka.mailchimp');         
        
        $memberId           = md5(strtolower($subscriber_email));
        $method             = 'lists/' . $listId . '/members/' . $memberId;

        $data_mailchimp = json_encode([
            'email_address' => $subscriber_email,
            'status_if_new' => "subscribed", // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields' => [
                $lastPurchase => $params['chimp_purchase_date']
            ]
        ]);


        //\Log::info('LIST ID - INTEREST : '.$listId .' - '.$interest);

        $result_mailchimp = Self::put($method,$data_mailchimp);
        //\Log::info('Result Mailchimp ORDER ADD : '.json_encode($result_mailchimp));

        if($result_mailchimp){
            return $result_mailchimp;
        }else{
            return false;
        }
    }
    
    public static function RegisterV3($firstName = '', $lastName = '', $subscriber_email, $promocode){
        $get_domain         = get_domain();
        $channel            = $get_domain['channel'];
        $domain             = $get_domain['domain'];
        $domain_id          = $get_domain['domain_id'];   
        
        $listId             = Self::getListId();         
        $subDate            = 'BBSUBDATE';
        if($domain_id == 1){      
            $subDate        = 'BBSUBDATE';
        } elseif($domain_id == 2) {            
            $subDate        = 'HBSUBDATE';
        } elseif($domain_id == 3) {            
            $subDate        = 'BBSUBDATE';
        }       
        
        $mailchimp_config   = \Config::get('berrybenka.mailchimp');         
        
        $memberId           = md5(strtolower($subscriber_email));
        $method             = 'lists/' . $listId . '/members/' . $memberId;
        
        $data_mailchimp     = json_encode([
            'email_address'     => $subscriber_email,
            'status_if_new'     => "subscribed", // "subscribed","unsubscribed","cleaned","pending"
            'merge_fields'      => [
                'GENDER'        => '',
                'FNAME'         => $firstName,
                'LNAME'         => $lastName,
                'VOUCHER'       => $promocode,
                $subDate        => date('d-m-Y')
            ]
        ]); 
                
        //\Log::info('LIST ID - INTEREST : '.$listId .' - '.$interest);

        $result_mailchimp = Self::put($method,$data_mailchimp);
        //\Log::info('Result Mailchimp RegisterV3 : '.json_encode($result_mailchimp));  

        if($result_mailchimp){
            return $result_mailchimp;
        }else{
            return false;
        }
    }

    public static function SubscribeV3($subscriber_email, $subscriber_voucher = NULL, $subscriber_gender = 1){
        $get_domain     = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];            
        
        $listId         = Self::getListId();                         
        $memberId       = md5(strtolower($subscriber_email));
        $method         = 'lists/' . $listId . '/members/' . $memberId;
        
        
        if($domain_id == 3){
            if($subscriber_gender == 1){
                $gender_mail = "Female";
            } elseif($subscriber_gender == 2) {
                $gender_mail = "Male";
            } else {
                $gender_mail = "";
            }
        }else{
            if($subscriber_gender == 1){
                $gender_mail = "Women";
            } elseif($subscriber_gender == 2) {
                $gender_mail = "Men";
            } else {
                $gender_mail = "";
            }
        }
        
        
        if($domain_id == 3){
           $data_mailchimp      = json_encode([
                'email_address'     => $subscriber_email,
                'status'            => "subscribed", // "subscribed","unsubscribed","cleaned","pending"
                'merge_fields'      => [
                    'FNAME'         => '',
                    'LNAME'         => '',
                    'NEWVOUCHER'    => $subscriber_voucher,
                    'GENDER'        => $gender_mail
                ]
            ]); 
        }else{
            $data_mailchimp     = json_encode([
                'email_address'     => $subscriber_email,
                'status'            => "subscribed", // "subscribed","unsubscribed","cleaned","pending"
                'merge_fields'      => [
                    'FNAME'         => '',
                    'LNAME'         => '',
                    'NEWVOUCHER'    => $subscriber_voucher,
                    'GENDER'        => $gender_mail
                ]
            ]);
        }
        
        //\Log::info('LIST ID - INTEREST : '.$listId .' - '.$interest);
        
        $result_mailchimp = Self::put($method,$data_mailchimp);  
        //\Log::info('Result Mailchimp : '.json_encode($result_mailchimp));

        if($result_mailchimp){
            return $result_mailchimp;
        }else{
            return false;
        }
    }
    
    public static function UpdateMemberV3($object){    
        $get_domain     = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];            
        
        $gender_mail        = "";

        if($domain_id == 3){
            if($object->customer_gender == 2){
                $gender_mail    = "Male";
            }else{
                $gender_mail    = "Female";
            }
        }else{
            if($object->customer_gender == 2){
                $gender_mail    = "Men";
            }else{
                $gender_mail    = "Women";
            }
        }

        //\Log::alert('mailchimp - update member object: '.$object);
        $customer_email       = isset($object->customer_email) ? $object->customer_email : '';
        $customer_firstname   = isset($object->customer_fname) ? $object->customer_fname : '';
        $customer_lastname    = isset($object->customer_lname) ? $object->customer_lname : '';
        $customer_birthday    = isset($object->customer_date_of_birth) ? str_replace('-','/',$object->customer_date_of_birth) : '';
        
        $listId             = Self::getListId();         
        $mailchimp_config   = \Config::get('berrybenka.mailchimp');         
        
        $memberId           = md5(strtolower($customer_email));
        $method             = 'lists/' . $listId . '/members/' . $memberId;
        
        $data_mailchimp = json_encode([
            'email_address' => $customer_email,
            'status_if_new' => "subscribed",
            'merge_fields' => [
                'GENDER' => $gender_mail,
                'FNAME' => $customer_firstname,
                'LNAME' => $customer_lastname,
                'BIRTHDAY' => $customer_birthday
            ]
        ]);
        
        $result_mailchimp = Self::put($method,$data_mailchimp);
        //\Log::info('Result Mailchimp UpdateMemberV3: '.json_encode($result_mailchimp));

        if($result_mailchimp){
            return $result_mailchimp;
        }else{
            return false;
        }
    }            
}
