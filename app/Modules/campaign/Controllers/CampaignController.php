<?php namespace App\Modules\Campaign\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Campaign\Models\Campaign;
use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Account\Models\Subscriber;
use \App\ReferralGrabber;
use \App\Mailchimp;
use \App\Customer;

use Input;
use Validatoor;

use Illuminate\Http\Request;

class CampaignController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
		$where['url_name'] = \Request::segment(3);
        $where['status'] = 1;
		$data["data"] = Campaign::fetch_campaign_page($where);
		
		if(empty($data["data"]))
        {
        	abort(404);
        }
		
		$data["domain_id"] = $domain_id;
		return get_view('campaign', 'campaign.index', $data);;
	}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function subscribe(Request $request)
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];

        $get_utm_source 		= $request->input('utm_source') ? $request->input('utm_source') : '';        
        $get_utm_medium 		= $request->input('utm_medium') ? $request->input('utm_medium') : '';        
        $get_utm_campaign 		= $request->input('utm_campaign') ? $request->input('utm_campaign') : '';    
        $get_utm_campaign_bb 	= $request->input('utm_campaign_bb') ? $request->input('utm_campaign') : '';        
        $get_referer 			= $request->input('referrer') ? $request->input('referrer') : NULL;        
        $is_get_voucher 		= $request->input('is_get_voucher') ? $request->input('is_get_voucher') : TRUE;        
        $redirect_page 			= $request->input('redirect_page') ? $request->input('redirect_page') : FALSE;

        if (isset($_COOKIE['__utmz']))
        {
            $get_utm = ReferralGrabber::parseGoogleCookie($_COOKIE['__utmz']);
            
            if ($get_utm && is_array($get_utm))
            {
                if (empty($get_utm_source) || $get_utm_source == '')
                {
                    $get_utm_source = $get_utm['source'];
                }
                
                if (empty($get_utm_medium) || $get_utm_medium == '')
                {
                    $get_utm_medium = $get_utm['medium'];
                }
                
                if (empty($get_utm_campaign) || $get_utm_campaign == '')
                {
                    $get_utm_campaign = $get_utm['campaign'];
                }
            }
        }

        $referrer 			= $get_referer;
        $host_name 			= $request->input('host_name') ? $request->input('host_name') : NULL;
        $utm_source 		= $get_utm_source;
        $utm_medium 		= $get_utm_medium;
        $utm_campaign 		= $get_utm_campaign;
        $utm_campaign_bb	= $get_utm_campaign_bb;
        $form_location 		= $request->input('form_location') ? $request->input('form_location') : NULL;
        $subscriber_email 	= $request->input('subscriber_email') ? $request->input('subscriber_email') : NULL;
        $subscriber_telp 	= $request->input('subscriber_telp') ? $request->input('subscriber_telp') : NULL;
        $first_name 		= $request->input('subscriber_first_name') ? $request->input('subscriber_first_name') : NULL;
        $email_subject 		= $request->input('email_subject') ? $request->input('email_subject') : NULL;
        $email_content 		= $request->input('email_content') ? $request->input('email_content') : NULL;
        $subscriber_type 	= $request->input('subscriber_type') ? $request->input('subscriber_type') : NULL;
        $subscriber_gender 	= $request->input('subscriber_gender') ? $request->input('subscriber_gender') : NULL;
        $subscriber_city 	= $request->input('subscriber_city') ? $request->input('subscriber_city') : NULL;
        
        //Subscribe date
        date_default_timezone_set('Asia/Jakarta');
        $curSubsTime 	= time();
        $subscribe_date = date("Y-m-d");
        
        $error 	= '';
        $status = false;
        //Check Email Format
        if (filter_var($subscriber_email, FILTER_VALIDATE_EMAIL))
        {
            //Check Email Exist
            if (!is_null($subscriber_email))
            {
                $check_subscriber = Subscriber::check_new_campaign_subscriber($subscriber_email);
                
                $gender = ($subscriber_gender == 'women') ? 1 : 2;
                
                if (empty($check_subscriber))
                {
                	$campaign_page = implode(',', array($referrer));
                	
                    $data = array(
                            'subscriber_email' 	=> $subscriber_email,
                            'utm_source' 		=> $utm_source, 
                            'utm_medium' 		=> $utm_medium, 
                            'utm_campaign' 		=> $utm_campaign, 
                            'utm_campaign_bb' 	=> $utm_campaign_bb,
                            'first_name' 		=> $first_name,
                            'subscriber_telp' 	=> $subscriber_telp,
                            'campaign_gender' 	=> $gender,
                            'campaign_page' 	=> $campaign_page
                    );
                    // var_dump($data);exit;
                    //Success - Insert new_subscriber
                    $add_new_subscriber = Subscriber::create_new_subscriber($data);

                    /*if ($add_new_subscriber == TRUE)
                    {
                        if ($referrer == 'Popup OM')
                        {
                            
                            // kalo misalnya dia popup OM ga perlu bikin
                            // vocuher dan email yg dikirim berdasarkan email contain popupnya
                            Self::send_email_popup_om($subscriber_email, $email_subject, $email_content);
                            $error = 'success_om';
                            
                            // nama variable error (sebenarnya bukan error) krn mengikuti yg dibawah, supaya
                            // ga banyak perubahan
                            
                            
                        }
                        else
                        {
                        	if ($is_get_voucher)
	                        {
	                            //Create Voucher
	                            $promoexpiry 	= $curSubsTime + 2629743;
	                            $promoname 		= 'Subscriber Voucher Rp.50.000 Off';
	                            $promovalue 	= 50000;
	                            $promominvalue 	= 300000;

	                            $subscriber_voucher = Self::create_subscriber_voucher($curSubsTime, $subscribe_date, $domain_id, $subscriber_email, $subscriber_type);
	                            
	                            if (!empty($subscriber_voucher))
	                            {
	                                $params['email'] 			= isset($subscriber_email) ? $subscriber_email : NULL;
	                                $params['form_location'] 	= isset($form_location) ? $form_location : NULL;
	                                $params['promoname'] 		= isset($promoname) ? $promoname : NULL;
	                                $params['promo_expiry'] 	= isset($promoexpiry) ? $promoexpiry : NULL;
	                                $params['promocode'] 		= isset($subscriber_voucher) ? $subscriber_voucher : NULL;
	                                $params['menwomen'] 		= $subscriber_gender;
	                                $params['promo_value'] 		= isset($promovalue) ? $promovalue : NULL;
	                                $params['promo_minvalue'] 	= isset($promominvalue) ? $promominvalue : NULL;

	                                // SEND EMAIL SUBSCRIBE
	                                //$sendmail_subscribe = Customer::MN_send_subscribe_success($params);
	                                //***********************

	                                // MAILCHIMP
	                                $subscriber_gender = ($subscriber_gender == "women") ? 1 : 2;
	                                $send_mailchimp = Mailchimp::subscribe($subscriber_email, $subscriber_gender, $subscriber_voucher);
	                                //*****************************
	                            }
	                        }
	                    }
                    }*/
                }
                else
                {
                	$referrers = explode(',',$check_subscriber["campaign_page"]);
                	
                	if(in_array($referrer, $referrers))
                	{
                    	$error = 'exist';
                    }
                    else
                    {
                    	array_push($referrers, $referrer);
                    	
                    	$campaign_page = implode(',', $referrers);
                    	
                    	$data_sub 	= array('campaign_page'=>$campaign_page, 'campaign_gender'=>$gender);
                    	$where_sub 	= array('subscriber_email' => $subscriber_email);
						
						$update_subscriber = Subscriber::update_subscriber($data_sub, $where_sub);
					}
                }
            }
            else
            {
                $error = 'invalid';
            }
        }
        else
        {
			// Special promo from indosat
            if ($utm_source == "indosat" && !is_null($subscriber_telp))
            {
                $data = array(	
                				'subscriber_email' 	=> "promo-indosat@berrybenka.com", 
                				'subscriber_telp' 	=> $subscriber_telp, 
                				'subscribe_date' 	=> $subscribe_date, 
                				'utm_source' 		=> $utm_source, 
                				'utm_medium' 		=> $utm_medium, 
                				'utm_campaign' 		=> $utm_campaign, 
                				'referrer' 			=> $referrer, 
                				'first_name' 		=> $first_name, 
                				'host_name' 		=> $host_name, 
                				'has_subscribe' 	=> 0, 
                				'subs_status' 		=> 0
                			);
                
                //Success - Insert new_subscriber
                $add_new_subscriber = Subscriber::create_new_subscriber($data);
            }
            else
            {
                $error = 'invalid';
            }
		}

        $http_referer = explode('?', @$_SERVER['HTTP_REFERER']);
    
        $home = $http_referer[0];
        
        if(\Request::ajax())
        {
        	if (!empty($error))
        	{
                $output = array(
                    "success" => 0, 
                    "result"  => $error
                );
            }
            else
            {
                 $output = array(
                    "success" => 1, 
                    "result"  => "success",
                );
            }
            
            die(json_encode($output));
        }
        else
        {
	        if ($redirect_page != FALSE)
	        {
	            //echo 'test';
	            return redirect($redirect_page);
	        }
	        else
	        {
	            if (!empty($error))
	            {
	                //echo 'test-error';
	                return redirect("$home?substa=$error");
	            }
	            else
	            {
	                //echo 'test-success';
	                if ($utm_source == "indosat" && !is_null($subscriber_telp))
	                {
                        return redirect("$home?substa=success&indosat");
                    }
                    else
                    {
                        return redirect("$home?substa=success");
                    }
	            }
	        }
	    }         
    }

    private function create_subscriber_voucher($curSubsTime, $subscribe_date,$domain_id, $customer_email, $gender) {
        //Select Last ID voucher
        $last_id_voucher = Promotion::last_id_promotion_code();
        
        if(empty($last_id_voucher) || $last_id_voucher==FALSE)
        {
            $txt = 0;
        }
        else
        {
            $txt = $last_id_voucher;
        }
        
        $genCode = ($gender == "women") ? $txt.substr(md5($txt), rand(0, (strlen(md5($txt)) - 5)), 5) : substr(md5($txt), rand(0, (strlen(md5($txt)) - 5)), 6);

        if ($domain_id == 1)
        {
        	$pre_voucher = ($gender == "women") ? "SCB" : "SCBM";
        	$data_voucher['promotion_template_id'] = 3; 
          $promocode = $pre_voucher . $genCode;
        }
        elseif ($domain_id == 2)
        {
        	$data_voucher['promotion_template_id'] = 169; 
          $promocode = 'SCB' . $genCode;
        }
        else
        {
        	$pre_voucher = ($gender == "women") ? "SCB" : "SCBM";
        	$data_voucher['promotion_template_id'] = 3; 
          $promocode = $pre_voucher . $genCode;
        }

        $data_voucher['promotion_code_number'] = $promocode;
        $data_voucher['customer_email'] = $customer_email;
        $data_voucher['status'] = 1;
        $data_voucher['created_by'] = 0;
        $data_voucher['createddate'] = date('Y-m-d H:i:s');
        $data_voucher['duration'] = 30;
        $create_voucher = Promotion::create_promotion_code($data_voucher);

        if (!empty($create_voucher)) {
            return $promocode;    
        } else {
            return false;
        }        
    }
    
    function send_email_popup_om($email, $email_subject, $email_content)
    {
    	$get_domain = get_domain();
	    $channel    = $get_domain['channel'];
	    $domain     = ucwords($get_domain['domain']);
	    $domain_id  = $get_domain['domain_id'];
	    
	    $send['domain_name'] 	= $get_domain['domain_name'];
        $send['mail_subject'] 	= $email_subject;
        $send['mail_message'] 	= $email_content;
        $send['email'] 			= $email;
        
        $send_email = Customer::send_mail($send);
    }

}
