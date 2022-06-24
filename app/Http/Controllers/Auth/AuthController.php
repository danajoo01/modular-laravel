<?php

namespace App\Http\Controllers\Auth;

// use App\User;
use Auth;
use URL;
use Socialite;
use Cart;
use Illuminate\Http\Request;
use App\Customer;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Modules\Checkout\Models\Promotion;
use \App\Modules\Account\Models\UserCs;
use \App\Modules\Account\Models\Subscriber;
use \App\Mailchimp;
use \App\Frontier;
use \App\ReferralGrabber;
use Session;
use \App\Modules\Account\Models\User;
use Carbon\Carbon;
use \App\Modules\Account\Models\CustomerCreditHistory;
use \App\VerifyEmail;
use App\Libraries\Mail;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        \Config::set('services.facebook.redirect', 'http://'. \Request::server('SERVER_NAME') .'/auth/facebook/callback');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'customer_fname' => 'required|max:255',
            'customer_email' => 'required|email|max:255|unique:customer',
            'password'       => 'required|confirmed|min:8',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)                    
    {
        return Customer::create([
            'customer_fname' => $data['customer_fname'],
            'customer_lname' => $data['customer_lname'],
            'customer_email' => $data['customer_email'],
            'customer_status' => 1,
            'customer_password' => bcrypt($data['password']),
            'customer_registration_date' => Carbon::now('Asia/Jakarta'),
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    public function register(Request $request)
    {
        $get_domain = get_domain();
        $domain_id = $get_domain['domain_id'];    
        $domain_name = $get_domain['domain_name']; 
        $validation = $this->validator($request->all());

        if($validation->passes())
        {
            $create = $this->create($request->all());
            $continue = $request->get('continue');
            
            if($create)
            {
                $this->login($request);

                // S MAILCHIMP  
                $user = Auth::user();
                $object_user = Customer::where('customer_id','=',$user->customer_id)->first();    
                date_default_timezone_set('Asia/Jakarta');
                $curSubsTime = time();
                $subscribe_date = date("Y-m-d");
                
                $registerdata['customer_email'] = $object_user->customer_email;
                $registerdata['customer_gender'] = $object_user->customer_gender;
                $subscriber_email   = $registerdata['customer_email'];
                $subscriber_gender  = $registerdata['customer_gender'];
                $subscriber_fname   = $object_user->customer_fname;
                $subscriber_lname   = $object_user->customer_lname;
                
                //create promotion code

//                $last_id_voucher = Promotion::last_id_promotion_code();
//                if ($domain_id == 1) {
//                    $promocode = 'SCBMV3D' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
//                } else {
//                    $promocode = 'SCBMV3DHB' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
//                }
//                
//                $data_voucher['promotion_code_number'] = $promocode;
//                $data_voucher['customer_email']        = $registerdata['customer_email'];
//                $data_voucher['status']                = 1;
//                $data_voucher['created_by']            = 0;
//                $data_voucher['createddate']           = date('Y-m-d H:i:s');
//                $data_voucher['promotion_template_id'] = 3; 
//                $data_voucher['duration']              = 30;
//                $create_voucher = Promotion::create_promotion_code($data_voucher);
//
                if (!empty($create_voucher)) {
                    $subscriber_voucher = $promocode;    
                } else {
                    $subscriber_voucher = "";
                }     
                
                //end create promotion code                                               
                                
                //--old $set_mailchimp = Mailchimp::update_member($object_user);
                
                // MAILCHIMP
                //$send_mailchimp = Mailchimp::subscribe($subscriber_email, $subscriber_gender, $subscriber_voucher);
                // - Update By Boan, Request By SAL
                //$send_mailchimp = Mailchimp::RegisterV3($subscriber_fname, $subscriber_lname, $subscriber_email, $subscriber_voucher);
                //*****************************
                // E MAILCHIMP

                //send to customer email [disabled 29/8/2017 - use mailchimp template]
                //
                if($domain_id != 1){
                    Self::signupMail($object_user, $subscriber_voucher);
                }
                
                if($domain_name == 'berrybenka') {
                    Self::signupVoucherMail($object_user, $subscriber_voucher);
                } 
                 
                
                //add to subscribe
//                $get_utm_source = $request->input('utm_source') ? $request->input('utm_source') : '';        
//                $get_utm_medium = $request->input('utm_medium') ? $request->input('utm_medium') : '';        
//                $get_utm_campaign = $request->input('utm_campaign') ? $request->input('utm_campaign') : '';        
//                $get_referer = $request->input('referrer') ? $request->input('referrer') : NULL;        
//                $is_get_voucher = $request->input('is_get_voucher') ? $request->input('is_get_voucher') : TRUE;
//                $host_name = $request->input('host_name') ? $request->input('host_name') : 'berrybenka.com';
                
                                
                try{
                    $arrayAutoSubscribe = [
                        'get_utm_source' => '',
                        'get_utm_medium' => '',
                        'get_utm_campaign' => '',
                        'get_referer' => NULL,
                        'is_get_voucher' => TRUE,
                        'redirect_page' => FALSE,
                        'host_name' => $domain_name . '.com',
                        'subscriber_email' => $subscriber_email,
                        'subscriber_first_name' => $subscriber_fname,
                        'subscriber_last_name' => $subscriber_lname,
                        'subscriber_gender' => $subscriber_gender
                    ];
                    $this->AutoSubscribe($arrayAutoSubscribe); 
                } catch (Exception $ex) {
                    \Log::critical('Auto Subscribe after register fail' . json_encode($ex));
                }

                $redirectTo = '/new-arrival';
                if($continue){
                    $redirectTo = $continue;    
                }
                return redirect($redirectTo);
            }
        }
        else
        {
            if($get_domain['channel'] == 2 || $get_domain['channel'] == 4 || $get_domain['channel'] == 6) {
                setcookie("left", ".mid-login-left", time()+3600, "/");
                setcookie("right", ".mid-login-right", time()+3600, "/");
            }

            return redirect('/login')->withError($validation->errors())->withInput();
        }
    }
    
    protected function AutoSubscribe($subscribeData = []){
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];

        $get_utm_source = $subscribeData['get_utm_source'] ? $subscribeData['get_utm_source'] : '';        
        $get_utm_medium = $subscribeData['get_utm_medium'] ? $subscribeData['get_utm_medium'] : '';        
        $get_utm_campaign = $subscribeData['get_utm_campaign'] ? $subscribeData['get_utm_campaign'] : '';        
        $get_referer = $subscribeData['get_referer'] ? $subscribeData['get_referer'] : NULL;        
        $is_get_voucher = $subscribeData['is_get_voucher'] ? $subscribeData['is_get_voucher'] : TRUE;        
        $redirect_page = $subscribeData['redirect_page'] ? $subscribeData['redirect_page'] : FALSE;        
        
        if (isset($_COOKIE['__utmz'])) {
            $get_utm = ReferralGrabber::parseGoogleCookie($_COOKIE['__utmz']);
            if ($get_utm && is_array($get_utm)) {
                if (empty($get_utm_source) || $get_utm_source == '') {
                    $get_utm_source = $get_utm['source'];
                }
                
                if (empty($get_utm_medium) || $get_utm_medium == '') {
                    $get_utm_medium = $get_utm['medium'];
                }
                
                if (empty($get_utm_campaign) || $get_utm_campaign == '') {
                    $get_utm_campaign = $get_utm['campaign'];
                }
            }
        }       

        //customer data
        date_default_timezone_set('Asia/Jakarta');
        $curSubsTime = time();
        $subscribe_date = date("Y-m-d");
        
        $host_name = $subscribeData['host_name'] ? $subscribeData['host_name'] : '';
        $referrer = $get_referer;
        $utm_source = $get_utm_source;
        $utm_medium = $get_utm_medium;
        $utm_campaign = $get_utm_campaign;
        $subscriber_email = $subscribeData['subscriber_email'] ? $subscribeData['subscriber_email'] : NULL;        
        $first_name = $subscribeData['subscriber_first_name'] ? $subscribeData['subscriber_first_name'] : NULL;
        $subscriber_gender = $subscribeData['subscriber_gender'] ? $subscribeData['subscriber_gender'] : NULL;  
        
        
        
        $error = '';
        $status = false;
        
        if (filter_var($subscriber_email, FILTER_VALIDATE_EMAIL)) {
            //Check Email Exist
            if (!is_null($subscriber_email)) {
                $check_subscriber = Subscriber::check_new_campaign_subscriber($subscriber_email);
                if (empty($check_subscriber)) {
                    $data = array(
                            'subscriber_email' => $subscriber_email, 
                            'subscribe_date' => $subscribe_date, 
                            'utm_source' => $utm_source, 
                            'utm_medium' => $utm_medium, 
                            'utm_campaign' => $utm_campaign, 
                            'referrer' => $referrer, 
                            'first_name' => $first_name, 
                            'host_name' => $host_name, 
                            'has_subscribe' => 1, 
                            'subs_status' => 1,
                            'subscriber_gender' => $subscriber_gender
                    );                    
                    //Success - Insert new_subscriber                    

                    if (Subscriber::create_new_subscriber($data)){
                        if ($is_get_voucher) {
                            if($domain_id != 3){
                                //Create Voucher
                                $promoexpiry = $curSubsTime + 2629743;
                                $promoname = 'Subscriber Voucher Rp.50.000 Off';
                                $promovalue = 'Rp.50000';
                                $promominvalue = 300000;    
                            }else{
                                //Create Voucher
                                $promoexpiry = $curSubsTime + 2629743;
                                $promoname = 'Subscriber Voucher 10%';
                                $promovalue = '10% dari total belanja anda, ';
                                $promominvalue = 300000;
                            }

                            $subscriber_voucher = $this->create_subscriber_voucher($curSubsTime, $subscribe_date, $domain_id, $subscriber_email);
                            
                            if (!empty($subscriber_voucher)) {
                                $params['email'] = isset($subscriber_email) ? $subscriber_email : NULL;
                                $params['form_location'] = 'MAIN REGISTER';
                                $params['promoname'] = isset($promoname) ? $promoname : NULL;
                                $params['promo_expiry'] = isset($promoexpiry) ? $promoexpiry : NULL;
                                $params['promocode'] = isset($subscriber_voucher) ? $subscriber_voucher : NULL;
                                $params['menwomen'] = ($subscriber_gender==2) ? "men" : "women";
                                $params['promo_value'] = isset($promovalue) ? $promovalue : NULL;
                                $params['promo_minvalue'] = isset($promominvalue) ? $promominvalue : NULL;

                                // SEND EMAIL SUBSCRIBE [disabled, 29/8/2017 - use mailchimp template]
                                
                                /*
                                 * $sendmail_subscribe = Customer::MN_send_subscribe_success($params);
                                 */
                                //***********************

                                // MAILCHIMP
                                //$send_mailchimp = Mailchimp::subscribe($subscriber_email, $subscriber_gender, $subscriber_voucher);
                                //\Log::critical('send to Mailchimp : '.$subscriber_email);
                                $genderText = 'Women';
                                if($subscriber_gender == 2){
                                    $genderText = 'Men';
                                }
                                // -- Update by Boan, Request by SAL
                                // $send_mailchimp = Mailchimp::SubscribeV3($subscriber_email, $subscriber_voucher, $genderText);
                                $frontier_data = array(
                                        'EMAIL'        => $subscriber_email,
                                        'FIRSTNAME'         => isset($subscribeData['subscriber_first_name']) ? $subscribeData['subscriber_first_name'] : NULL,
                                        'LASTNAME'          => isset($subscribeData['subscriber_last_name']) ? $subscribeData['subscriber_last_name'] : NULL,
                                        'REGISTRATIONDATE'  => date("Y-m-d H:i:s")
                                    );
                                $send_frontier = Frontier::call_frontier($frontier_data);
                                //*****************************
                            }
                        }
                        
                        /*if($domain_id == 3){
                          // MAILCHIMP
                          //$send_mailchimp = Mailchimp::subscribe($subscriber_email, $subscriber_gender, $subscriber_voucher);
                          $send_mailchimp = Mailchimp::SubscribeV3($subscriber_email);
                          //*****************************
                        }*/
                    }
                } else {

                    if($domain_id == 1){
                            $has_subscribe      = $check_subscriber["has_subcribe_bb"];
                            $has_subscribe_col 	= "has_subcribe_bb";
                            $status             = 'subs_status_bb';
                            $referrer_col       = 'referrer_bb';
                            $campaign_col       = 'utm_campaign_bb';
                            $subs               = 'subscribe_date_bb';
                    }elseif($domain_id == 2){
                            $has_subscribe      = $check_subscriber["has_subcribe_hb"];
                            $has_subscribe_col 	= "has_subcribe_hb";
                            $status             = 'subs_status_hb';
                            $referrer_col       = 'referrer_hb';
                            $campaign_col       = 'utm_campaign_hb';
                            $subs               = 'subscribe_date_hb';
                    }else{
                            $has_subscribe      = $check_subscriber["has_subcribe_sd"];
                            $has_subscribe_col 	= "has_subcribe_sd";
                            $status             = 'subs_status_sd';
                            $referrer_col       = 'referrer_sd';
                            $campaign_col       = 'utm_campaign_sd';
                            $subs               = 'subscribe_date_sd';
                    }
	
                    if ($has_subscribe != 1) {
                        $data_sub = array(
                            'subscribe_date' => $subscribe_date,
                            $subs => $subscribe_date,
                            'utm_source' => $utm_source,
                            'utm_medium' => $utm_medium,
                            'utm_campaign' => $utm_campaign,
                            $campaign_col => $utm_campaign,
                            'referrer' => $referrer,
                            $referrer_col => $referrer,
                            'host_name' => $host_name,
                            $has_subscribe_col => 1,
                            $status => 1,
                            'subscriber_gender' => $subscriber_gender
                        );

                        $where_sub = array('subscriber_email' => $subscriber_email);

                        $update_subscriber = Subscriber::update_subscriber($data_sub, $where_sub);

                        if ($update_subscriber == TRUE) {
                            if ($is_get_voucher) {
                                if ($domain_id != 3) {
                                    //Create Voucher
                                    $promoexpiry = $curSubsTime + 2629743;
                                    $promoname = 'Subscriber Voucher Rp.50.000 Off';
                                    $promovalue = 'Rp.50000';
                                    $promominvalue = 300000;
                                } else {
                                    //Create Voucher
                                    $promoexpiry = $curSubsTime + 2629743;
                                    $promoname = 'Subscriber Voucher 10%';
                                    $promovalue = '10% dari total belanja anda, ';
                                    $promominvalue = 300000;
                                }

                                $subscriber_voucher = $this->create_subscriber_voucher($curSubsTime, $subscribe_date, $domain_id, $subscriber_email);

                                if (!empty($subscriber_voucher)) {
                                    $params['email'] = isset($subscriber_email) ? $subscriber_email : NULL;
                                    $params['form_location'] = isset($form_location) ? $form_location : NULL;
                                    $params['promoname'] = isset($promoname) ? $promoname : NULL;
                                    $params['promo_expiry'] = isset($promoexpiry) ? $promoexpiry : NULL;
                                    $params['promocode'] = isset($subscriber_voucher) ? $subscriber_voucher : NULL;
                                    $params['menwomen'] = ($subscriber_gender == 2) ? "men" : "women";
                                    $params['promo_value'] = isset($promovalue) ? $promovalue : NULL;
                                    $params['promo_minvalue'] = isset($promominvalue) ? $promominvalue : NULL;

                                    // SEND EMAIL SUBSCRIBE
                                    //$sendmail_subscribe = Customer::MN_send_subscribe_success($params);
                                    //***********************
                                    // MAILCHIMP
                                    //$send_mailchimp = Mailchimp::subscribe($subscriber_email, $subscriber_gender, $subscriber_voucher);
                                    // -- Update by Boan, Request by SAL
                                    // $send_mailchimp = Mailchimp::SubscribeV3($subscriber_email, $subscriber_voucher, $subscriber_gender);
                                    $frontier_data = array(
                                        'EMAIL'        => $subscriber_email,
                                        'FIRSTNAME'         => isset($subscribeData['subscriber_first_name']) ? $subscribeData['subscriber_first_name'] : NULL,
                                        'LASTNAME'          => isset($subscribeData['subscriber_last_name']) ? $subscribeData['subscriber_last_name'] : NULL,
                                        'REGISTRATIONDATE'  => date("Y-m-d H:i:s")
                                    );
                                $send_frontier = Frontier::call_frontier($frontier_data);
                                    //*****************************
                                }
                            }
                        }
                    }else {
                        $error = 'exist';
                    }
                }
            } else {
                $error = 'invalid';
            }
        }
        
        return $status;
    }

    public function showLoginForm(Request $request) {
        $get_domain = get_domain();

        $view = property_exists($this, 'loginView')
                    ? $this->loginView : 'auth.authenticate';

        if (view()->exists($view)) {
            return view($view);
        }

        if(!empty($request->input('continue'))) {
            $data['continue'] = $request->input('continue');
        }

        switch($get_domain['channel']) {
            case 1:
                return view('auth.login', (!empty($data)) ? $data : []);
            break;
            case 2: 
                $data['logo_path']  = asset('berrybenka/mobile/img/circleb.gif');
                $data['title_path'] = asset('berrybenka/mobile/img/bb-logo.gif');
                $data['title']      = 'Onedeca';

                return view('auth.login-mobile', (!empty($data)) ? $data : []);
            break;
            case 3:
                return view('auth.login-hijabenka', (!empty($data)) ? $data : []);
            break;
            case 4:
                $data['logo_path']  = asset('hijabenka/mobile/img/circleh.gif');
                $data['title_path'] = asset('hijabenka/mobile/img/logo.gif');
                $data['title']      = 'Hijabenka';

                return view('auth.login-mobile', (!empty($data)) ? $data : []); 
            break;
            case 5:
                return view('auth.login-shopdeca', (!empty($data)) ? $data : []);
            break;
            case 6:
                $data['logo_path']  = asset('shopdeca/mobile/img/shopdeca-logo.png');
                $data['title_path'] = asset('shopdeca/mobile/img/shopdeca.png');
                $data['title']      = 'Shopdeca';

                return view('auth.login-mobile', (!empty($data)) ? $data : []); 
            break;
        }
    }

    public function login(Request $request)
    {        
        Session::forget('auth_cs');
        
        $get_domain = get_domain();
        $domain_id  = $get_domain['domain_id'];
      
        $email    = $request->get('customer_email');
        $password = $request->get('password');
        $remember = $request->has('remember') ? $request->get('remember') : null;
        
        $customer = Customer::where('customer_email', $email)->first();        
        if(!$customer){
          if(!empty($request->input('continue'))) {
              return redirect('/login?continue='. urlencode($request->input('continue')))->with('login_error', 'Wrong email or password')->withInput();
          } else {
              return redirect('/login')->with('login_error', 'Wrong email or password')->withInput();
          }
        }
        
        if($domain_id != 3 && $customer->customer_status != 1){          
          if(!empty($request->input('continue'))) {
              return redirect('/login?continue='. urlencode($request->input('continue')))->with('login_error', 'Akun anda tidak aktif')->withInput();
          } else {
              return redirect('/login')->with('login_error', 'Akun anda tidak aktif')->withInput();
          }
        }
        
        //Check when Shopdeca, update password if password NULL
        if($domain_id == 3 && $customer->customer_password == NULL){ 
          //Update inputted password to customer
          $update_customer = Customer::where('customer_email', $email)
            ->update(['customer_password' => bcrypt($password)]);

          if(!$update_customer){
            return redirect('/');
          }
        }

        switch($get_domain['domain_id']){
            case 1 :
              $oldlastlogin = $customer->last_login_date;
            break;
            case 2 :
              $oldlastlogin = $customer->hb_last_login_date;
            break;
            case 3 :
              $oldlastlogin = $customer->sd_last_login_date;
            break;
            default : 
              $oldlastlogin = $customer->last_login_date;
        }
       
        //Notif Benka Stamp
        if(Session::has('notif_benka_stamp_last_login')){
            $oldlastlogin = Session::get('notif_benka_stamp_last_login');
        }
        
        //Get benka stamp history
        if(!is_null($oldlastlogin)){
            $benka_stamp = Customer::check_benka_stamp($customer->customer_id,$oldlastlogin);

             //session for check notif benka stamp
            if(!empty($benka_stamp)){
                Session::put('notif_benka_stamp', count($benka_stamp));
                Session::put('notif_benka_stamp_last_login', $oldlastlogin);
            }
        }
        
        $credential = ['customer_email' => $email, 'password' => $password];
                
        if (Auth::check()) {          
          return redirect('/new-arrival');
        }else if (Auth::attempt($credential, $remember)){
          //Sync Order Item to Cart
          OrderItem::syncOrderItem();

          //update last login
          $get_domain     = get_domain();
          $datetime       = Carbon::now('Asia/Jakarta');

          switch($get_domain['domain_id']){
            case 1 : 
              $lastlogin = array(
                'last_login_date' => $datetime
              );
              break;
            case 2 :
              $lastlogin = array(
                'hb_last_login_date' => $datetime
              );  
              break;
            case 3 :
              $lastlogin = array(
                'sd_last_login_date' => $datetime
              );  
              break;
            default : 
              $lastlogin = array(
                'last_login_date' => $datetime
              );
          }        

          $user = Auth::user();

          if($user){
            $string = $user->customer_email.Date('Ymd His').$get_domain['domain_name'];
            $token  = \Hash::make($string);

            if($get_domain['domain_name'] == 'berrybenka') {
                $lastlogin['bb_access_token'] = $token;
                Session::put('bb_access_token', $token);
            } elseif($get_domain['domain_name'] == 'hijabenka') {
                $lastlogin['hb_access_token'] = $token;
                Session::put('hb_access_token', $token);
            } elseif($get_domain['domain_name'] == 'shopdeca') {
                $lastlogin['sd_access_token'] = $token;
                Session::put('sd_access_token', $token);
            } else{
                $lastlogin['bb_access_token'] = $token;
                Session::put('bb_access_token', $token); 
            }

            $where = [];
            $where['customer_id'] = $user->customer_id;

            $updateLastLogin      = User::update_user_data($where,$lastlogin);
          }

          if(!empty($request->input('continue'))) {
              return redirect($request->input('continue'));
          } else {
              return redirect('/new-arrival');
          }
        }else{
          if(!empty($request->input('continue'))) {
              return redirect('/login?continue='. urlencode($request->input('continue')))->with('login_error', 'Wrong email or password')->withInput();
          } else {
              return redirect('/login')->with('login_error', 'Wrong email or password')->withInput();
          }
        }
    }

    /**
     * show auth_cs form.
     *
     * @return auth_cs view
     */
    public function getAuthCs() {
        $get_domain = get_domain();

        switch($get_domain['channel']) {
            case 1:
                return view('auth.cs.berrybenka.desktop.first-login');
                break;
            case 2:
                $data['logo_path']  = asset('berrybenka/mobile/img/circleb.gif');
                $data['title_path'] = asset('berrybenka/mobile/img/bb-logo.gif');
                $data['title']      = 'Berrybenka';

                return view('auth.cs.berrybenka.mobile.first-login', $data);
                break;
            case 3:
                return view('auth.cs.hijabenka.desktop.first-login');
                break;
            case 4:
                $data['logo_path']  = asset('hijabenka/mobile/img/circleh.gif');
                $data['title_path'] = asset('hijabenka/mobile/img/logo.gif');
                $data['title']      = 'Hijabenka';

                return view('auth.cs.hijabenka.mobile.first-login', $data);
                break;
            case 5:
                return view('auth.cs.shopdeca.desktop.first-login');
                break;
            case 6:
                $data['logo_path']  = asset('shopdeca/mobile/img/circleh.gif');
                $data['title_path'] = asset('shopdeca/mobile/img/logo.gif');
                $data['title']      = 'Shopdeca';

                return view('auth.cs.shopdeca.mobile.first-login', $data);
                break;
        }
    }

    /**
     * Execute auth_cs login.
     *
     * @return redirect to user dashboard
     */
    public function postAuthCs(Request $request) {
        $email      = $request->get('customer_email');
        $password   = $request->get('password');

        $authUser = UserCs::where('user_email', $email)
                            ->where('user_password', $password)
                            ->first();

        if (!$authUser) return redirect('/user/auth_cs')->with('login_error', 'Wrong email')->withInput();

        Session::put('email_auth_cs', $email);
        
        Session::put('auth_cs', 1);

        return redirect('/user/customer_auth');
    }

    /**
     * show customer_cs form.
     *
     * @return auth_cs view
     */
    public function getCustomerAuth() {
        $get_domain = get_domain();

        switch($get_domain['channel']) {
            case 1:
                return view('auth.cs.berrybenka.desktop.cs-login');
                break;
            case 2:
                $data['logo_path']  = asset('berrybenka/mobile/img/circleb.gif');
                $data['title_path'] = asset('berrybenka/mobile/img/bb-logo.gif');
                $data['title']      = 'Berrybenka';

                return view('auth.cs.berrybenka.mobile.cs-login', $data);
                break;
            case 3:
                return view('auth.cs.hijabenka.desktop.cs-login');
                break;
            case 4:
                $data['logo_path']  = asset('hijabenka/mobile/img/circleh.gif');
                $data['title_path'] = asset('hijabenka/mobile/img/logo.gif');
                $data['title']      = 'Hijabenka';

                return view('auth.cs.hijabenka.mobile.cs-login', $data);
                break;
            case 5:
                return view('auth.cs.shopdeca.desktop.cs-login');
                break;
            case 6:
                $data['logo_path']  = asset('shopdeca/mobile/img/circleh.gif');
                $data['title_path'] = asset('shopdeca/mobile/img/logo.gif');
                $data['title']      = 'Shopdeca';

                return view('auth.cs.shopdeca.mobile.cs-login', $data);
                break;
        }
    }

    /**
     * Execute customer_cs login.
     *
     * @return redirect to user dashboard
     */
    public function postCustomerAuth(Request $request) {
        $get_domain = get_domain();
        
        $email = $request->get('customer_email');
        
        $credentials = [];
        $credentials['customer_email']  = $email;
        $credentials['customer_status'] = 1;

        $authUser = Customer::where($credentials)->first();

        if (!$authUser) return redirect('/user/customer_auth')->with('login_error', 'Email salah atau akun tidak aktif')->withInput();

        Auth::login($authUser, true);
        
        //Sync Order Item to Cart
        OrderItem::syncOrderItem();
        //End Sync Order Item to Cart

        Session::put('auth_cs', 1);
        
        $string = $email.Date('Ymd His').$get_domain['domain_name'];
        $token  = \Hash::make($string);

        if($get_domain['domain_name'] == 'berrybenka') {
            $lastlogin['bb_access_token'] = $token;
            Session::put('bb_access_token', $token);
        } elseif($get_domain['domain_name'] == 'hijabenka') {
            $lastlogin['hb_access_token'] = $token;
            Session::put('hb_access_token', $token);
        } elseif($get_domain['domain_name'] == 'shopdeca') {
            $lastlogin['sd_access_token'] = $token;
            Session::put('sd_access_token', $token);
        } else{
            $lastlogin['bb_access_token'] = $token;
            Session::put('bb_access_token', $token);
        }

        $where = [];
        $where['customer_id'] = $authUser->customer_id;

        $updateLastLogin      = User::update_user_data($where,$lastlogin);

        return redirect('/user/account_dashboard');
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return Response
     */

    // public function redirectToProvider()
    // {
    //     return Socialite::driver('facebook')->redirectUrl(url('/') . '/auth/facebook/callback')->redirect();
    // }
    

    // public function handleProviderCallback(Request $request)
    // {
    //     if (!$request->has('code') || $request->has('denied')) {
    //         return redirect('/');
    //     }

    //     try {
    //         $user = Socialite::driver('facebook')->user();
    //     } catch (Exception $e) {
    //         return redirect('auth/facebook');
    //     }

    //     $data = [
    //         'field'          => 'customer_email',
    //         'value'          => $user->email,
    //         'customer_email' => $user->email,
    //         'customer_fname' => $user->name,
    //         'source'         => 'facebook'
    //     ];

    //     session()->put('state', $request->input('state'));
    //     $authUser = $this->findOrCreateUser($data);

    //     $auth = Auth::login($authUser, true);
 
    //     return redirect('/user/account_dashboard');
    // }

    /**
     * Obtain the user information from Line.
     *
     * @return Response
     */
    public function handleLineCallback(Request $request) {
        $line_code = $request->input('code');
        $data = [];

        $get_domain     = get_domain();

        $url = 'https://api.line.me/v1/oauth/accessToken';
        $request_data = 'grant_type=authorization_code&code='. $request->input('code') .'&client_id=1478974875&client_secret=d24e8cb654aaf4c65f1a76a2d3fbdc28&redirect_uri='. urlencode('http://irfan.berrybenka.biz/line_auth');

        $access = social_curl($url, $request_data);

        if(!isset($access['error'])) {
            $profile_url  = 'https://api.line.me/v1/profile';
            $header       = 'Authorization: Bearer '. $access['access_token'];
            $profile_data = social_curl($profile_url, $header, 'GET');

            $split_name = explode(' ',$profile_data['displayName']);

            $data = [
                'field'          => 'social_id',
                'value'          => $profile_data['mid'],
                'customer_fname' => !empty($split_name[0]) ? $split_name[0] : '',
                'customer_lname' => !empty($split_name[1]) ? $split_name[1] : '',
                'source'         => 'Line',
                'social_id'      => $profile_data['mid']
            ];
            
        } else {
            return redirect('/login')->with('login_error', 'Token Expired')->withInput();
        }

        $authUser = $this->findOrCreateUser($data);

        $auth = Auth::login($authUser, true);

        return redirect('/user/account_dashboard');
    }
 
    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $facebookUser
     * @return User
     */
    private function findOrCreateUser($param = [])
    {
        $authUser       = Customer::where($param['field'], $param['value'])->first();
        $get_domain     = get_domain();
        $domain_id      = $get_domain['domain_id']; 
        if ($authUser){            
            $string = $authUser->customer_email.Date('Ymd His').$get_domain['domain_name'];
            $token  = \Hash::make($string);

            $lastlogin = [];

            if($get_domain['domain_name'] == 'berrybenka') {
                $lastlogin['bb_access_token'] = $token;
                Session::put('bb_access_token', $token);
            } elseif($get_domain['domain_name'] == 'hijabenka') {
                $lastlogin['hb_access_token'] = $token;
                Session::put('hb_access_token', $token);
            } elseif($get_domain['domain_name'] == 'shopdeca') {
                $lastlogin['sd_access_token'] = $token;
                Session::put('sd_access_token', $token);
            } else{
                $lastlogin['bb_access_token'] = $token;
                Session::put('bb_access_token', $token);
            }

            $where = [];
            $where['customer_id'] = $authUser->customer_id;

            User::update_user_data($where,$lastlogin);

            return $authUser;
        }

        $result = Customer::create([
            'customer_fname' => !empty($param['customer_fname']) ? $param['customer_fname'] : '',
            'customer_lname' => !empty($param['customer_lname']) ? $param['customer_lname'] : '',
            'customer_email' => !empty($param['customer_email']) ? $param['customer_email'] : '',
            'customer_status'   => 1,            
            'source' => !empty($param['source']) ? $param['source'] : '',
            'social_id' => !empty($param['social_id']) ? $param['social_id'] : '',
        ]);
        
        //create promotion code
        
        $subscriber_fname = !empty($param['customer_fname']) ? $param['customer_fname'] : '';
        $subscriber_lname = !empty($param['customer_lname']) ? $param['customer_lname'] : '';
        $subscriber_email = !empty($param['customer_email']) ? $param['customer_email'] : '';           

//        $last_id_voucher = Promotion::last_id_promotion_code();
//        if ($domain_id == 1) {
//            $promocode = 'SCBMV3D' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
//        } else {
//            $promocode = 'SCBMV3DHB' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
//        }
//                
//        
//        $data_voucher['promotion_code_number'] = $promocode;
//        $data_voucher['customer_email']        = $subscriber_email;
//        $data_voucher['status']                = 1;
//        $data_voucher['created_by']            = 0;
//        $data_voucher['createddate']           = date('Y-m-d H:i:s');
//        $data_voucher['promotion_template_id'] = 3; 
//        $data_voucher['duration']              = 30;
//        $create_voucher = Promotion::create_promotion_code($data_voucher);                        
        //end create promotion code  
        
        if (!empty($create_voucher)) {
            $subscriber_voucher = $promocode;    
        } else {
            $subscriber_voucher = "";
        }  
        
        // MAILCHIMP
        // - Update by Boan, Request by SAL        
        //$send_mailchimp = Mailchimp::RegisterV3($subscriber_fname, $subscriber_lname, $subscriber_email, $subscriber_voucher);        
        // E MAILCHIMP
        
        //send to customer email
        
        if($subscriber_email != ''){
            $object_user = new \stdClass;
            $object_user->customer_fname    = $subscriber_fname;
            $object_user->customer_lname    = $subscriber_lname;
            $object_user->customer_email    = $subscriber_email;
            Self::signupMail($object_user, $subscriber_voucher);        
        }

        return $result;
    }

    public function logout()
    {
        Cart::destroy(); 
        Auth::logout();
        
        Session::forget('auth_cs');
        Session::forget('email_auth_cs');
        Session::forget('notif_benka_stamp');
        
        return redirect('/login');
    }
  
  public static function signupMail($params = array(), $voucher) 
  { 
    $get_domain     = get_domain();
    $domain_name    = $get_domain['domain_name'];
    $domain_id      = $get_domain['domain_id'];
    
    // $mail_headers = "MIME-Version: 1.0" . "\r\n";
    // $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // $mail_headers .= 'From: '.strtoupper($domain_name) .' <cs@berrybenka.com>' . "\r\n";
    
    if($domain_id == 3){
        $param['mail_message'] = "
          Dear <b>" . ucfirst($params->customer_fname) . " " . ucfirst($params->customer_lname) . "</b>,<br/>
          <br/>
           Terima kasih telah bergabung di Shopdeca [dot] com. Akun Anda terdaftar dengan email: " . $params->customer_email . " dan sudah siap digunakan untuk mulai berbelanja di Shopdeca.com<br/>
          <br/>
          <br/>
           Jika Anda lupa kata sandi, silahkan klik  <a href=\"http://shopdeca.com/forgot_password\" target=\"_blank\">disini</a> <br/>
          <br/>";

        $mail_subject   = "Pembuatan Akun Shopdeca berhasil!";    
    }elseif($domain_id == 2){
        $param['mail_message'] = "
          Dear <b>" . ucfirst($params->customer_fname) . " " . ucfirst($params->customer_lname) . "</b>,<br/>
          <br/>
           Terima kasih telah bergabung di Hijabenka [dot] com. Akun Anda terdaftar dengan email: " . $params->customer_email . " dan sudah siap digunakan untuk mulai berbelanja di Hijabenka.com<br/>
          <br/>
          <br/>
           Jika Anda lupa kata sandi, silahkan klik  <a href=\"http://hijabenka.com/forgot_password\" target=\"_blank\">disini</a> <br/>
          <br/>";

        $mail_subject   = "Pembuatan Akun HIJABENKA berhasil!";   
    }else{
        $param['mail_message'] = "
          Dear <b>" . ucfirst($params->customer_fname) . " " . ucfirst($params->customer_lname) . "</b>,<br/>
          <br/>
           Terima kasih telah bergabung di Berrybenka [dot] com. Akun Anda terdaftar dengan email: " . $params->customer_email . " dan sudah siap digunakan untuk mulai berbelanja di Berrybenka.com<br/>
          <br/>
          <br/>
           Jika Anda lupa kata sandi, silahkan klik  <a href=\"http://berrybenka.com/forgot_password\" target=\"_blank\">disini</a> <br/>
          <br/>";

        $mail_subject   = "Pembuatan Akun BERRYBENKA berhasil!";    
    }
    
    $message = response()->view('mailtemplates.'.strtolower($domain_name).'.mailtemplates_signup_voucher', $param)->content();
    $body = array(
        "personalizations"=>array(
          array("recipient"=>$params->customer_email)
        ),
        "from"=>array(
          "fromEmail"=>"cs@berrybenka.com",
          "fromName"=>strtoupper($domain_name)
        ),
        "subject"=>$mail_subject,
        "content"=>$message
    );
  
    $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
    $Mail->SendMail($body);
    
    $message  = response()->view('mailtemplates.'.strtolower($domain_name).'.mailtemplates_signup', $param)->content();
    // $sendmail = mail($params->customer_email, $mail_subject, $message, $mail_headers);
  }

  public function signupVoucherMail($params = array(), $voucher)
  {
    $get_domain     = get_domain();
    $domain_name    = $get_domain['domain_name'];
    $domain_id      = $get_domain['domain_id'];

    // $mail_headers = "MIME-Version: 1.0" . "\r\n";
    // $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // $mail_headers .= 'From: '.strtoupper($domain_name) .' <cs@berrybenka.com>' . "\r\n";

    $param['mail_message'] = "";
    $mail_subject = "Selamat Datang di Berrybenka!";

    $message = response()->view('mailtemplates.'.strtolower($domain_name).'.mailtemplates_signup_voucher', $param)->content();
    $body = array(
        "personalizations"=>array(
          array("recipient"=>$params->customer_email)
        ),
        "from"=>array(
          "fromEmail"=>"cs@berrybenka.com",
          "fromName"=>strtoupper($domain_name)
        ),
        "subject"=>$mail_subject,
        "content"=>$message
    );
  
    $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
    $Mail->SendMail($body);
    // $sendmail = mail($params->customer_email, $mail_subject, $message, $mail_headers);
  }
    
    private function create_subscriber_voucher($curSubsTime, $subscribe_date,$domain_id, $customer_email) {
        //Select Last ID voucher
        $last_id_voucher = Promotion::last_id_promotion_code();

        if ($domain_id == 1) {
            $promocode = 'SCBMV3D' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
            $template_id = 3;
        } elseif ($domain_id == 2) {
            $promocode = 'SCBMV3DHB' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
            $template_id = 3;
        } else {
            $promocode = 'SCBSD' . $last_id_voucher . substr(md5($last_id_voucher), rand(0, (strlen(md5($last_id_voucher)) - 4)), 5). generate_random_string(5);
            $template_id = 11478;
        }

        $data_voucher['promotion_code_number'] = $promocode;
        $data_voucher['customer_email'] = $customer_email;
        $data_voucher['status'] = 1;
        $data_voucher['created_by'] = 0;
        $data_voucher['createddate'] = date('Y-m-d H:i:s');
        $data_voucher['promotion_template_id'] = $template_id; 
        $data_voucher['duration'] = 30;
        $create_voucher = Promotion::create_promotion_code($data_voucher);

        if (!empty($create_voucher)) {
            return $promocode;    
        } else {
            return false;
        }        
    }


    public function redirectToProvider()
    {
        $get_domain = get_domain();

        if($get_domain['domain_name'] == 'berrybenka') {
            $fb = new \Facebook\Facebook([
              'app_id' => '228899163974178',
              'app_secret' => '71f37e31f78195c220f47b4261f75093',
              'default_graph_version' => 'v2.8',
            ]);
        } elseif($get_domain['domain_name'] == 'hijabenka') {
            $fb = new \Facebook\Facebook([
              'app_id' => '1743757482525710',
              'app_secret' => '6eb5e0d47b3f33c075e1bd54ab70437b',
              'default_graph_version' => 'v2.8',
            ]);
        }else{
            $fb = new \Facebook\Facebook([
              'app_id' => '228899163974178',
              'app_secret' => '71f37e31f78195c220f47b4261f75093',
              'default_graph_version' => 'v2.8',
            ]);
        }

        $helper = $fb->getRedirectLoginHelper();

        $loginUrl = $helper->getLoginUrl(url('/') . '/auth/facebook/callback');

        // $loginUrl = $helper->getLoginUrl('https://berrybenka.local/auth/facebook/callback');

        return redirect($loginUrl);
    }
 
    /**
     * Obtain the user information from Facebook.
     *
     * @return Response
     */

    public function handleProviderCallback(Request $request)
    {

        if (!$request->has('code') || $request->has('denied')) {
            return redirect('/');
        }

        $get_domain = get_domain();

        if($get_domain['domain_name'] == 'berrybenka') {
            $fb = new \Facebook\Facebook([
              'app_id' => '228899163974178',
              'app_secret' => '71f37e31f78195c220f47b4261f75093',
              'default_graph_version' => 'v2.8',
            ]);
        } elseif($get_domain['domain_name'] == 'hijabenka') {
            $fb = new \Facebook\Facebook([
              'app_id' => '1743757482525710',
              'app_secret' => '6eb5e0d47b3f33c075e1bd54ab70437b',
              'default_graph_version' => 'v2.8',
            ]);
        }else{
            $fb = new \Facebook\Facebook([
              'app_id' => '228899163974178',
              'app_secret' => '71f37e31f78195c220f47b4261f75093',
              'default_graph_version' => 'v2.8',
            ]);
        }

        $helper = $fb->getRedirectLoginHelper();

        if ($request->input('state')) {
            $helper->getPersistentDataHandler()->set('state', $request->input('state'));
        }

        try {
          $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          return redirect('auth/facebook');
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          return redirect('auth/facebook');
        }

        if (isset($accessToken)) {
            $response = $fb->get('/me?fields=email,name', $accessToken);

            $userData = $response->getGraphUser();

            $data = [
                'field'          => 'customer_email',
                'value'          => $userData['email'],
                'customer_email' => $userData['email'],
                'customer_fname' => $userData['name'],
                'source'         => 'facebook'
            ];

            session()->put('state', $request->input('state'));
            $authUser = $this->findOrCreateUser($data);

            $auth = Auth::login($authUser, true);
     
            return redirect('/user/account_dashboard');
        }else{
            return redirect('auth/facebook');
        }
    }

}
