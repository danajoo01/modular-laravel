<?php 
namespace App\Modules\Account\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Account\Models\CustomerCreditHistory;
use \App\Modules\Account\Models\OrderHeader;
use \App\Modules\Account\Models\User;
use \App\Modules\Product\Models\Wishlist;
use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Checkout\Models\Tcash;
use \App\Customer;
use \App\Mailchimp;
use \App\Frontier;
use \App\ReferralGrabber;
use \App\Modules\Account\Models\Subscriber;
use \App\Modules\Account\Models\BenkaStamp;
use \App\Modules\Checkout\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use Validator;
use Carbon\Carbon;
use Log;

use Auth;
use Session;

use \App\Modules\Account\Models\BenkaStampHistorySlave;

class AccountController extends Controller {
    /**
     * Display a listing of the Benka Point.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($domain_id == 3 && $channel == 5){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
            return redirect('/login');
        } 
        
        $paginate                       = 10;                               
        $data['user']                   = $user;        
        $data['stamp_history']          = BenkaStamp::StampHistory($user->customer_id, $paginate);                  
        $data['limit']                  = $paginate;
        $data['page']                   = $request->get('page') != null ? $request->get('page') : 1;
                        
        return get_view('account', 'account.index', $data);
    }
    
    public function benkaPoin()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($domain_id == 3 && $channel == 5){ 
          abort(404);
        }
        
        Session::forget('notif_benka_stamp');
        Session::forget('notif_benka_stamp_last_login');

        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        } 
        //bb_debug($user);
        $where['customer_id'] = $user->customer_id;
        $limit = 10;
        $fetch_credits_history = CustomerCreditHistory::fetchCustomerCreditHistory($where,$limit);

        //bb_debug($fetch_credits_history);
        $data['user']                   = $user;
        $data['limit']                  = $limit;
        $data['credits_history']        = $fetch_credits_history;
        return get_view('account', 'account.benkapoin', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function referral()
    {
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/referral_program'));
        }

        $get_domain = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        } 

        $data['user'] = $user;

        return get_view('account', 'account.referral', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function textMeApp()
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
        $user = Auth::user();
        
        if (empty($user)) {
            return redirect('/login');
        } 
        
        $data['user'] = $user;
        
        return get_view('account', 'account.sms-referral', $data);
    }
	
    /**
     * Display a listing of the wishlist.
     *
     * @return Response
     */
    public function wishlist()
    {
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/wishlist'));
        }

        $get_domain = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        $domain_alias   = $get_domain['domain_alias'];

        //Generate get uri
        $where["data_http_get"] = generate_get_uri();
		
        $user               = Auth::user();
        $user_email         = $user->customer_email;
        $user_customerid    = $user->customer_id;
        if (empty($user)) {
            return redirect('/login');
        } 
        
        $where['customer_id']   = $user->customer_id;
        $where['domain_id']     = $domain_id;
        $limit                  = 8;        
        $page_num               = isset($where["data_http_get"]['page']) ? $where["data_http_get"]['page'] : '0';
        
        $cacheWishlist      = 'wishlist'.$domain_alias.'-' . $user_customerid . '-' . $user_email . '-page:' . $page_num;  
        $expiresAt          = Carbon::now()->addSeconds(60);
        $fetch_wishlist     = Cache::remember($cacheWishlist, $expiresAt, function() use($where,$limit){                            
            return Wishlist::fetchWishlist($where,$limit);
        });
         
        //$fetch_wishlist = Cache::get($cacheWishlist); 

        $data['user']           = $user;
        $data['wishlist']       = isset($fetch_wishlist['wishlist']) ? $fetch_wishlist['wishlist'] : array();
        $data['total']          = isset($fetch_wishlist['total']) ? $fetch_wishlist['total'] : 0;
        $data['page_num']       = $page_num;
        $data['start_catalog']  = isset($fetch_wishlist['start']) ? $fetch_wishlist['start'] : 0;
		
        if($channel == 2 || $channel == 4 || $channel == 6) abort(404);
        
        return get_view('account', 'account.wishlist', $data);
    }
	
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function orderHistory(Request $request)
    {
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/order_history'));
        }

        $get_domain = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        $domain_alias 	= isset($get_domain['domain_alias']) ? $get_domain['domain_alias'] : 'bb';

        $user               = Auth::user();
        $user_email         = $user->customer_email;
        $user_customerid    = $user->customer_id;

        if (empty($user)) {
                return redirect('/login');
        } 
		
        //Fetch user
        $user_id 	= $user->customer_id;
        $limit 		= 5;
        

        $where['customer_id']   = $user_id;
        $where['domain_id']     = $domain_id;
        
        $request                = $request->all();
        $HistoryPage            = isset($request['page']) && is_numeric($request['page']) ? $request['page'] : 1;
        $cacheName              = "OrderHistory".$domain_alias."-" . $user_customerid . "-" . $user_email . "-page:" . $HistoryPage;  
        $expiresAt              = Carbon::now()->addSeconds(60);
               
        $invoice_headers        = Cache::remember($cacheName, $expiresAt, function() use($where, $limit){                            
            return OrderHeader::invoice_header($where, $limit, 'order_id');
        });  
        
        //$invoice_headers        = Cache::get($cacheName); 
        
        $data = array(	'data'	 			=> $invoice_headers['oh'], 
                        'user' 				=> $user, 
                        'start_catalog'                 => ($invoice_headers['data']->currentPage() == 1) ? 0 : $invoice_headers['data']->currentPage(),
                        'total_catalog'                 => $invoice_headers['total'],
                        'all' 				=> $invoice_headers['data']
		);

		
	   return get_view('account', 'account.orderhistory', $data);
    }
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function setting(Request $request)
	{
            if (!Auth::check()) {
                return redirect('login/?continue='.urlencode('/user/setting'));
            }

            $get_domain = get_domain();
            $channel = $get_domain['channel'];
            if($channel == 2 || $channel == 4 || $channel == 6){
                return redirect('/user/account_dashboard');
            }

            $user = Auth::user();

            if (empty($user)) {
                return redirect('/login');
            }

            $address_type = ($request->get('address_type') != '') ? $request->get('address_type') : 1;

            //Fetch user
            try {
                $user_id 	= $user->customer_id;

                $get_customer_address = User::get_customer_address($user_id, $address_type);

                //Fetch Shipping by shipping area
                $where_shipping_area['enabled']         = 1;
                $where_shipping_area['shipping_type'] 	= 1;
                $order = 'shipping_area';
                $group = 'shipping_area';
                $fetch_shipping_area = User::fetch_shipping($where_shipping_area, $limit = NULL, $offset = NULL, $order, $group);

                if (!empty($fetch_shipping_area)) {
                    foreach ($fetch_shipping_area as $shipping_area) {
                        $options_area[$shipping_area->shipping_area] = $shipping_area->shipping_area;
                    }
                }

                //Fetch shipping by shipping name
                $where_shipping_name['enabled']         = 1;
                $where_shipping_name['shipping_type'] 	= 1;
                $order = 'shipping_name';
                $fetch_shipping_name = User::fetch_shipping($where_shipping_name, $limit = NULL, $offset = NULL, $order, $group = NULL);

                if (!empty($fetch_shipping_name)) {
                    foreach ($fetch_shipping_name as $shipping_name) {
                        $options_name[$shipping_name->shipping_name] = $shipping_name->shipping_name;
                    }
                }

                $data = [
                    'address_type' => isset($address_type) ? $address_type : NULL,
                    'shipping_area' => isset($fetch_shipping_area) ? $fetch_shipping_area : NULL,
                    'shipping_name' => isset($fetch_shipping_name) ? $fetch_shipping_name : NULL,
                    'options_area' => isset($options_area) ? $options_area : NULL,
                    'options_name' => isset($options_name) ? $options_name : NULL,
                    'customer_address' => $get_customer_address,
                    'user' => isset($user) ? $user : NULL
                ];           

                return get_view('account', 'account.setting', $data);    
            } catch (Exception $ex) {
                \Log::error('User setting problem with user email = ' . isset($user->customer_email) ? $user->customer_email : '-');
            }            
	}
  
    public function addAddress(Request $request)
    {
        $params['address_type']     = (\Request::segment(3) == "shipping") ? 1 : 2;
        $params['address_street']   = $request->get('address');
        $params['address_province'] = $request->get('shipping_area');
        $params['address_city']     = $request->get('shipping_name');
        $params['address_postcode'] = $request->get('postcode');
        $params['address_phone']    = $request->get('phone');
        
        //Check Address Validity
        $check_params['type']           = 1;
        $check_params['shipping_type']  = 1;
        $check_params['shipping_area']  = $request->get('shipping_area');
        $check_params['shipping_name']  = $request->get('shipping_name');

        $check_shipping = Shipping::getShippingList($check_params);

        if (count($check_shipping) <= 0) {
          return redirect('/user/setting?address_type='.$params['address_type'])->with('err_msg', 'Data alamat anda salah')->withInput();
        }
        //End Check Address Validity

        $add_customer_address = Customer::addCustomerAddress($params);

        if($add_customer_address["result"] == true){
            return redirect('/user/setting?address_type='.$params['address_type']);
        }else{
            return redirect('/user/setting?address_type='.$params['address_type'])->with('err_msg', $add_customer_address["result_message"])->withInput();
        }
    }
  
    public function editAddress(Request $request)
    {
        $params['address_id']       = \Request::segment(3);
        $params['address_street']   = $request->get('address');
        $params['address_province'] = $request->get('shipping_area');
        $params['address_city']     = $request->get('shipping_name');
        $params['address_postcode'] = $request->get('postcode');
        $params['address_phone']    = $request->get('phone');
        $params['address_type']     = $request->get('address_type');
        
        //Check Address Validity
        $check_params['type']           = 1;
        $check_params['shipping_type']  = 1;
        $check_params['shipping_area']  = $request->get('shipping_area');
        $check_params['shipping_name']  = $request->get('shipping_name');

        $check_shipping = Shipping::getShippingList($check_params);

        if (count($check_shipping) <= 0) {
          return redirect('/user/setting?address_type='.$params['address_type'])->with('err_msg', 'Data alamat anda salah')->withInput();
        }
        //End Check Address Validity

        $edit_customer_address = Customer::editCustomerAddress($params);

        if($edit_customer_address["result"] == true){
            return redirect('/user/setting?address_type='.$params['address_type']);
        }else{
            return redirect('/user/setting?address_type='.$params['address_type'])->with('err_msg', $edit_customer_address["result_message"])->withInput();
        }
    }
  
    public function setPrimaryAddress()
    {
        $params['address_id']   = \Request::segment(4);
        $params['address_type'] = \Request::segment(3);

        $set_primary_address = Customer::setPrimaryAddress($params);

        if($set_primary_address == true){
            return redirect('/user/setting?address_type='.$params['address_type'])->with('success', 'Alamat pertama telah diperbaharui');
        }else{
            return redirect('/user/setting?address_type='.$params['address_type'])->with('err_msg', 'Pengubahan alamat pertama gagal')->withInput();
        }
    }
  
    public function deleteAddress()
    {
        if(!empty(\Auth::user())){
            $customer_id  = \Auth::user()->customer_id;            
            $address_id   = \Request::segment(3);
            $address_type   = \Request::get('address_type');
            $delete_address = User::deleteAddress($address_id);

            if($delete_address == true){
              //Log when customer emptied his primary shipping address
              $get_primary_address = Customer::getCustomerAddress(['get_primary' => TRUE]);

              if(count($get_primary_address)  <= 0){                                
                    $customer     = Customer::where('customer_id', '=', $customer_id)->first();
                    \Log::alert($customer->customer_email . ' has emptied his primary shipping address.');                                    
              }
              //End Log
            }                              
        }
        return redirect('/user/setting?address_type='.$address_type);
    }
    
    public function getShippingCityNew(Request $request) {
        // post variable
        $shiping_area   = $request->get('shipping_area');
        $shipping_type  = $request->get('shipping_type');
        
        $where['shipping_area'] = $shiping_area;
        $where['shipping_type'] = $shipping_type;
        $group = 'shipping_name';
        $fetch_all_shipping_name = User::fetch_shipping($where, $limit = NULL, $offset = NULL, $group, $group);
        
        $city = NULL;
        
        $city .='<option value="">Kota</option>';

        foreach ($fetch_all_shipping_name as $row_city){
            $city.= '<option value="' . $row_city->shipping_name . '" >' . $row_city->shipping_name . '</option>';
        }
        
        echo $city;
    }
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function returnForm()
	{
		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
		
		$user = Auth::user();
		
		if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/return_form'));
        }
		
		//Fetch user
        $user_id    = $user->customer_id;

        $limit      = 20;
        
        $get_delivered_list = OrderHeader::get_delivered_list($user_id, $limit);
        $get_returned_list 	= OrderHeader::get_returned_list($user_id);

		$data = array(	'user' 				=> $user, 
						'total_delivered' 	=> $get_delivered_list->total(), 
						'total_returned' 	=> $get_returned_list->total(), 
						'delivered_list' 	=> $get_delivered_list, 
						'returned_list' 	=> $get_returned_list
					);
		
		return get_view('account', 'account.returnform', $data);
	}
    
    public function returnPurchase() {
        // DEFINE DOMAIN
        $get_domain = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        
        if($domain_id == 1) {
            $own = 'own_bb';
        } elseif($domain_id == 2) {
            $own = 'own_hb';
        } else {
            $own = 'own_sd';
        }

        $user = Auth::user();
        
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/return_purchase'));
        }
    
        //Fetch user
        $data = array('user' => isset($user) ? $user : NULL,);

        $order_item_id = \Request::segment(3);
        
        $return_detail = OrderHeader::get_order_item_detail($order_item_id);
        
        $data['return_detail'] = $return_detail;
        
        /*Available Size & Color */
        $where['product_id'] 	= $return_detail->product_id;
        $where['domain_id'] 	= $domain_id;
        $where['status'] 		= 1;
        $where[$own] 			= 1;

        /* Temp Variable */
        $temp = $temp2 = array();

        /* Size */
        $data['available_size'] = OrderHeader::get_size_variant($where);
        
        foreach ($data['available_size'] as $k => $v) {
            $temp[] = $v->product_size;
        }
        $data['available_size'] = strtoupper( implode( ', ', $temp ) );

        /* Color */
        $data['available_color'] = OrderHeader::get_color_variant($where);

        foreach ($data['available_color'] as $k => $v) {
            $temp2[] = $v->variant_color_name;
        }
        
        $data['available_color'] = strtoupper( implode( ', ', $temp2 ) );

        
        return get_view('account', 'account.retur-detail', $data);
    }

    public function insertCustomerReturn (Request $request) {
        $order_item_id 	= $request->input('return_order_item_id');
        $sku 			= $request->input('return_sku');
        $purchase_code 	= $request->input('return_purchase_code');
        $customer_id 	= $request->input('return_customer_id');
        $date_created 	= date('Y-m-d H:i:s');

        /* Param Return Objective & Reason */
        $reason = $request->input('return_reason');
        $obj 	= $request->input('return_obj');

        /* If Return Reason Others */
        $txt_reason = ucwords($request->input('return_reason_txtarea'));
        if($reason=='Others') {
            $reason = !empty($txt_reason) ? $txt_reason : $reason;
        }

        /* Validate Return Objective */
        if($obj=='1') {
            $reason = $reason . ' - Tukar dengan barang yang sama';
        } else if($obj=='2') {
            $diff_product 	= $request->input('return_obj_brand') . ',' . $request->input('return_obj_color') . ',' . $request->input('return_obj_product_name') . ',' . $request->input('return_obj_quantity') . ',' . $request->input('return_obj_size');
            $reason 		= $reason . ' - Tukar dengan barang yang beda (' . $diff_product . ')';
        } else if($obj=='3') {
            $size 	= $request->input('return_obj_chg_size');
            $reason = $reason . ' - Ganti Ukuran (' . $size . ')';
        } else if($obj=='4') {
            $color 	= $request->input('return_obj_chg_color');
            $reason = $reason . ' - Ganti Warna (' . $color . ')';
        } else if($obj=='5') {
            $size 	= $request->input('return_obj');
            $reason = $reason . ' - Kredit Berrybenka';
        } else if($obj=='6') {
            $bank_name 			= $request->input('return_obj_refund_bank');
            $bank_account_name 	= $request->input('return_obj_refund_bank_acc');
            $bank_account 		= $request->input('return_obj_refund_acc');
            $reason 			= $reason . ' - Pengembalian Pembayaran ' . $bank_name . ' ' . $bank_account . ' A/N ' . $bank_account_name;
        } else {
            $txt_obj 	= $request->input('return_obj_txtarea');
            $reason 	= $reason . ' - Lainnya.. (' . $txt_obj . ')';
        }

        $insert = array(
                 'purchase_code'    => $purchase_code,
                 'customer_id'      => $customer_id,
                 'order_item_id'    => $order_item_id,
                 'SKU'              => $sku,
                 'date_created'     => $date_created,
                 'reason'           => $reason
        );

        $insert = OrderHeader::insert_customer_return($insert);
        
        echo ($insert) ? 1 : 0;
        
    }
    
    public function callWmsReturnProcess (Request $request) {
        $order_item_id 	= $request->input('return_order_item_id') ? $request->input('return_order_item_id') : NULL;
        $customer_id 	= $request->input('return_customer_id');
        $sku 			= $request->input('return_sku') ? $request->input('return_sku') : NULL;
        $url 			= WMS_API . 'api/eksternal/ajax_process_return_frontend';
        
        /* Param Return Objective & Reason */
        $reason = $request->input('return_reason');
        $obj 	= $request->input('return_obj');

        /* If Return Reason Others */
        $txt_reason = ucwords($request->input('return_reason_txtarea'));
        if($reason=='Others') {
            $reason = !empty($txt_reason) ? $txt_reason : $reason;
        }

        /* Validate Return Objective */
        if($obj=='1') {
            $reason = $reason . ' - Tukar dengan barang yang sama';
        } else if($obj=='2') {
            $diff_product 	= $request->input('return_obj_brand') . ',' . $request->input('return_obj_color') . ',' . $request->input('return_obj_product_name') . ',' . $request->input('return_obj_quantity') . ',' . $request->input('return_obj_size');
            $reason 		= $reason . ' - Tukar dengan barang yang beda (' . $diff_product . ')';
        } else if($obj=='3') {
            $size 	= $request->input('return_obj_chg_size');
            $reason = $reason . ' - Ganti Ukuran (' . $size . ')';
        } else if($obj=='4') {
            $color 	= $request->input('return_obj_chg_color');
            $reason = $reason . ' - Ganti Warna (' . $color . ')';
        } else if($obj=='5') {
            $size 	= $request->input('return_obj');
            $reason = $reason . ' - Kredit Berrybenka';
        } else if($obj=='6') {
            $bank_name 			= $request->input('return_obj_refund_bank');
            $bank_account_name 	= $request->input('return_obj_refund_bank_acc');
            $bank_account 		= $request->input('return_obj_refund_acc');
            $reason 			= $reason . ' - Pengembalian Pembayaran ' . $bank_name . ' ' . $bank_account . ' A/N ' . $bank_account_name;
        } else {
            $txt_obj 	= $request->input('return_obj_txtarea');
            $reason 	= $reason . ' - Lainnya.. (' . $txt_obj . ')';
        }

        $param['return_order_item_id'] 	= $order_item_id;
        $param['return_sku'] 			= $sku;
        $param['return_customer_id'] 	= $customer_id;
        $param['return_note'] 			= $reason;

        $result = bb_curl($url,$param);
        
        echo $result;
    }
    
    public function cancelReturn () {
        /* Get Customer Id */
        $customer_id 	= (!is_null( session('customer_id') ) ) ? session('customer_id') : 0 ;
        $order_item_id 	= \Request::segment(3);

        $param['order_item_id'] = $order_item_id;
        $param['customer_id'] 	= $customer_id;

        $cancel_return = OrderHeader::cancel_return($order_item_id);
        
        $url = WMS_API . 'api/eksternal/cancel_return';
        $result = json_decode(bb_curl($url,$param));

        if(isset($result->success)) {
            return redirect('/user/return_form')->with('sukses','Success to cancel return process');
        } else {
            return redirect('/user/return_form')->with('gagal','Failed to cancel return process');
        }
        
        
    }
                
    /* Get Order Status Tracking */
    function order_status_tracking() {
        return get_view('account', 'account.order_status_tracking', array());
    }

    /* Get Order Status Tracking */
    public static function ajax_order_status() {
        /* Get Purchase Code & user ID */
        $purchase_code = (Input::get('purchase_code')) ? Input::get('purchase_code') : NULL;
        $type = (Input::get('type')) ? Input::get('type') : NULL;

        if($type == NULL)
        {
            $user = Auth::user();
    		
    		if (empty($user)) {
    			return redirect('/login');
    		} 
    		
    		//Fetch user
            $user_id = $user->customer_id;
            $where['customer_id']   = $user_id;
        }

        /* Get Order Status */
        $where['purchase_code'] = $purchase_code;
        
        $order_status = OrderHeader::fetch_order_tracking($where);

        foreach ($order_status as $k => $v) {

            if(is_null($v->approve_change)) {
                $order_status[$k]->approve_change = '-';
            } 
            else {
                $order_status[$k]->approve_change = date('d F Y H:i:s',strtotime($v->approve_change));
                $order_status[$k]->date = strtotime($v->approve_change);
            }

            /* Get Status Modifier */
            $status_name = array(
                                    0 => 'New',
                                    1 => 'Verified',
                                    2 => 'Approved',
                                    3 => 'Picklisted',
                                    4 => 'Picked',
                                    5 => 'Packed',
                                    6 => 'Shipped',
                                    7 => 'Delivered',
                                    8 => 'PO Created',
                                    9 => 'Received',
                                    10 => 'Put Away',
                                    11 => 'OOS',
                                    12 => 'Refunded',
                                    13 => 'Return In Progress',
                                    14 => 'Returned',
                                    //15 => 'Return OK',
                                    15 => 'Product Quality Checked',
                                    //16 => 'Return Denied',
                                    16 => 'Product Quality Rejected',
                                    17 => 'Cancelled',
                                    18 => 'Closed',
                                    19 => 'Cancel Before Delivery',
                                    20 => 'Fail Delivered'
                                );
            /* Check Order Item History Null or Not */
            /* IF NULL Get Value From Order Item */
            if( is_null($v->status_history) ) $v->status_history = $v->status_final;

             $order_status[$k]->status_history = $status_name[ $v->status_history ];
             $order_status[$k]->status_id = $v->status_history;
             
            /* Get Product Name */
            if(strpos($v->product_name, '-') !== FALSE) {
                $name = explode(' - ',$v->product_name);
                $order_status[$k]->product_name = $name[0];
            }
            
            if(!is_null($order_status[$k]->TRACKING_NUMBER)){
                $order_status[$k]->number = $v->TRACKING_NUMBER;
                $order_status[$k]->method = $v->SHIPPING_METHOD;
            }
        }

        return response()->json($order_status);
    }

    public function orderHistoryDetail() {

        // DEFINE DOMAIN
        $get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
		
		$user = Auth::user();
        
        $purchase_code = \Request::segment(3);
        
        if (empty($user)) {
            return redirect('login/?continue='.urlencode('/user/order_history_detail/'.$purchase_code));
        } 
        
        //Fetch user
        $user_id 	= $user->customer_id;

        //Fetch Order where
        $where['customer_id'] 	= $user_id;
        $where['purchase_code'] = $purchase_code;
        $data = OrderHeader::fetch_invoice_detail($where);


        if(empty($data['invoice_detail']) || empty($data['order_product']))
        {
            \Session::flash('error_message', 'Nomor Pemesanan '.$purchase_code.' tidak ditemukan');
            return redirect('user/order_history');
        }

        if($data['invoice_detail']->master_payment_id == 135 && $data['invoice_detail']->status == 0){

            $get_signature = Tcash::getTcashSignature($purchase_code);

            $get_url = url('/') . "/checkout/tcash_success?refNum=" . $get_signature['tcash_refnum'];
            $visit = url('/') . "/checkout/tcash_redirect?trxId=" . $purchase_code;

            $data['tcash_detail'] = array(
                'tcash_redirect' => $visit,
                'tcash_confirmation' => $get_url
            );
        }

        if($data['invoice_detail']->master_payment_id == 343 && $data['invoice_detail']->status == 0){
            $data['gopay_url'] = url('/') . '/checkout/gopay/qrcode?po=' . $purchase_code;
        }
        
        $data = array('data' => $data, 'user' => $user);
        \Log::info('Account Order History Detail: ' . json_encode($data));

        return get_view('account', 'account.order-history-detail', $data);
    }

    public function validateAccount(Request $request) {
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/order_history'));
        }
        $user               = Auth::user();
        $user_email         = $user->customer_email;
        $user_customerid    = $user->customer_id;

        $get_domain     = get_domain();
        $channel 	= $get_domain['channel'];
        $domain 	= $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        $domain_alias 	= isset($get_domain['domain_alias']) ? $get_domain['domain_alias'] : 'bb';
        
        //Setup form validation
        $rules = array(
                'account-name' 	=> 'Required',
                'amount'     	=> 'Required'
        );

        $validate = Validator::make($request->all(), $rules);

        $purchase_code = $request->input('purchase_code');

        // Run form validation
        if ($validate->passes()) {
            
            // Update user
            $update['confirm_transfer_by'] 		= trim($request->input('account-name'));
            $update['confirm_transfer_amount'] 	= $request->input('amount');
            
            $where['purchase_code'] = $request->input('purchase_code');
            
            //update order item
            $action             = OrderHeader::update_confirm_transfer($where, $update);
            $CacheOrderHistory  = "OrderHistory".$domain_alias."-" . $user_customerid . "-" . $user_email . "-page:"; 
            clearRedisContains($CacheOrderHistory); 
            
            //back to user index page
            return redirect('/user/order_history_detail/' . $purchase_code);
        }else{
        	return redirect('/user/order_history_detail/'.$purchase_code)->withErrors($validate->errors())->withInput();
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function editPersonalDetail(Request $request)
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
        $user = Auth::user();
        
        if (empty($user)) {
            return redirect('/login');
        }
        
        //Fetch user
        $user_id    = $user->customer_id;
        
        $data = array(  'user' => $user);
        
        return get_view('account', 'account.ubah-data', $data);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function savePersonalDetail(Request $request)
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];
        
        if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/edit_personal_detail'));
        }

        //Setup form validation
        $rules = array(
                'customer_fname'            => 'Required|regex:/^[\pL\s\-]+$/u|min:2|max:24',
                'customer_lname'            => 'Required|regex:/^[\pL\s\-]+$/u|min:2|max:24',
                'customer_phone'            => 'Required|numeric',
                'customer_gender'           => 'Required',
                'how_did_you_know_us'       => 'Required',
        );

        $validate = Validator::make($request->all(), $rules);

        $user = Auth::user();
        $customer_id = $user->customer_id;

        // Run form validation
        if ($validate->passes()) {
            
            // Update user
            $dob = $request->input('yy').'-'.$request->input('mm').'-'.$request->input('dd');
            
            $update['customer_fname']           = trim($request->input('customer_fname'));
            $update['customer_lname']           = trim($request->input('customer_lname'));
            $update['customer_date_of_birth']   = $dob;
            $update['customer_phone']           = $request->input('customer_phone');
            $update['customer_gender']          = $request->input('customer_gender');
            $update['how_did_you_know_us']      = $request->input('how_did_you_know_us');
            
            $where['customer_id'] = $customer_id;
            
            //update order item
            $action = User::update_user_data($where, $update);

            if ($action) {
                // S MAILCHIMP  
                $object_user = Customer::where('customer_id','=',$customer_id)->first();                           
                //$set_mailchimp = Mailchimp::update_member($object_user);
                // -- Update by Boan, Request by SAL
                //$set_mailchimp = Mailchimp::UpdateMemberV3($object_user);
                // E MAILCHIMP
            }
            
            //back to user index page
            return redirect('/user/edit_personal_detail/');
        }else{
            return redirect('/user/edit_personal_detail/')->withErrors($validate->messages())->withInput();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function newsubcriber(Request $request)
    {
        $get_domain = get_domain();
        $channel    = $get_domain['channel'];
        $domain     = $get_domain['domain'];
        $domain_id  = $get_domain['domain_id'];

        $get_utm_source = $request->input('utm_source') ? $request->input('utm_source') : '';        
        $get_utm_medium = $request->input('utm_medium') ? $request->input('utm_medium') : '';        
        $get_utm_campaign = $request->input('utm_campaign') ? $request->input('utm_campaign') : '';        
        $get_referer = $request->input('referrer') ? $request->input('referrer') : NULL;        
        $is_get_voucher = $request->input('is_get_voucher') ? $request->input('is_get_voucher') : TRUE;        
        $redirect_page = $request->input('redirect_page') ? $request->input('redirect_page') : FALSE;

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

        $referrer = $get_referer;
        $host_name = $request->input('host_name') ? $request->input('host_name') : NULL;
        $utm_source = $get_utm_source;
        $utm_medium = $get_utm_medium;
        $utm_campaign = $get_utm_campaign;
        $form_location = $request->input('form_location') ? $request->input('form_location') : NULL;
        $subscriber_email = $request->input('subscriber_email') ? $request->input('subscriber_email') : NULL;
        $subscriber_telp = $request->input('subscriber_telp') ? $request->input('subscriber_telp') : NULL;
        $first_name = $request->input('subscriber_first_name') ? $request->input('subscriber_first_name') : NULL;
        $email_subject = $request->input('email_subject') ? $request->input('email_subject') : NULL;
        $email_content = $request->input('email_content') ? $request->input('email_content') : NULL;
        $subscriber_type = $request->input('subscriber_type') ? $request->input('subscriber_type') : NULL;
        $subscriber_gender = $request->input('subscriber_gender') ? $request->input('subscriber_gender') : NULL;
        
        //Subscribe date
        date_default_timezone_set('Asia/Jakarta');
        $curSubsTime = time();
        $subscribe_date = date("Y-m-d");
        
        $error = '';
        $status = false;
        //Check Email Format
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
                    // var_dump($data);exit;
                    //Success - Insert new_subscriber
                    $add_new_subscriber = Subscriber::create_new_subscriber($data);

                    if ($add_new_subscriber == TRUE){
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
                                $params['form_location'] = isset($form_location) ? $form_location : NULL;
                                $params['promoname'] = isset($promoname) ? $promoname : NULL;
                                $params['promo_expiry'] = isset($promoexpiry) ? $promoexpiry : NULL;
                                $params['promocode'] = isset($subscriber_voucher) ? $subscriber_voucher : NULL;
                                $params['menwomen'] = ($subscriber_gender==2) ? "men" : "women";
                                $params['promo_value'] = isset($promovalue) ? $promovalue : NULL;
                                $params['promo_minvalue'] = isset($promominvalue) ? $promominvalue : NULL;

                                // SEND EMAIL SUBSCRIBE
                                //$sendmail_subscribe = Customer::MN_send_subscribe_success($params);
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
                                if ($domain_id == 1) {
                                    $frontier_data = array(
                                        'EMAIL'        => $subscriber_email,
                                        'GENDER'            => $genderText,                                        
                                        'SUBSCRIPTIONDATE'  => date("Y-m-d H:i:s")
                                    );
                                } else {
                                    $frontier_data = array(
                                        'EMAIL'        => $subscriber_email,                                      
                                        'SUBSCRIPTIONDATE'  => date("Y-m-d H:i:s")
                                    );
                                }
                                
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
                    }
                    elseif($domain_id == 2){
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
                                    // -- Request by Boan, Update by SAL
                                    // $send_mailchimp = Mailchimp::SubscribeV3($subscriber_email, $subscriber_voucher, $subscriber_gender);
                                    if ($domain_id == 1) {
                                    $frontier_data = array(
                                        'PRIMARYKEY'        => $subscriber_email,
                                        'GENDER'            => $subscriber_gender,                                        
                                        'SUBSCRIPTIONDATE'  => Carbon::now('Asia/Jakarta')
                                        );
                                    } else {
                                        $frontier_data = array(
                                            'PRIMARYKEY'        => $subscriber_email,                                      
                                            'SUBSCRIPTIONDATE'  => Carbon::now('Asia/Jakarta')
                                        );
                                    }
                                    
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

        $http_referer = explode('?', @$_SERVER['HTTP_REFERER']);
    
        $home = $http_referer[0];
        if ($redirect_page != FALSE) {
            //echo 'test';
            return redirect($redirect_page);
        } else {
            if (!empty($error)) {
                //echo 'test-error';
                return redirect("$home?substa=$error");
            } else {
                //echo 'test-success';
                return redirect("$home?substa=success");
            }
        }            
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


    public function benkaStampActivation(BenkaStampHistorySlave $benkaStampHistorySlave)
    {
        $benkaStampHistorySlave->benkaStampActivation();
    }

    public function benkaStampEmailNotif(BenkaStampHistorySlave $benkaStampHistorySlave)
    {
        $benkaStampHistorySlave->benkaStampEmailNotif();
    }
    
    public function stampDeals(){
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id      = $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($domain_id == 3 && $channel == 5){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }         
        $limit = 6;

        $data['deals']  = BenkaStamp::StampDeals($limit); 
        $data['user']   = $user;
        $data['limit']  = $limit;
        return get_view('account', 'account.stampdeals', $data);    
    }

    public function stampDealsDetail($id = NULL){
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id      = $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($channel == 1 && $channel == 3){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }         
        $limit = 1;
        $deals = BenkaStamp::StampDealsDetail($id);

        $data['deals_id'] = $id;
        $data['deals_name']  = $deals->deals_name; 
        $data['deals_description'] = $deals->deals_description;
        $data['deals_image'] = $deals->deals_image;
        $data['user']   = $user;
        $data['limit']  = $limit;
        return get_view('account', 'account.stampdetail', $data);    
    }

    public function checkAJAX()
    {
        if(!Auth::check() || (\Session::token() != \Request::header('X-CSRF-Token'))) { 
          return false;
        }
        
        if(!Customer::validateAccessToken()){
          return false;
        }
        
        return true;
    }

    public function stampDealsRedeem(Request $request){
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id      = $get_domain['domain_id'];

       if(!Self::checkAJAX()) {
            $json['result']       = false;
            $json['need_refresh'] = true;
            return json_encode($json);
        }
        
        $user = Auth::user();

        //Redirect if domain shopdeca
        if($channel == 2 && $channel == 4){ 
          abort(404);
        }

        if (empty($user)) {
                return redirect('/login');
        }

        $result = FALSE;
        $message = '';

        $id = $request->get('deals_id');

        $customer_stamp_active = BenkaStamp::GetStampActive($user->customer_id);

        $deals = BenkaStamp::StampDealsDetail($id);

        $date = date('Y-m-d H:i:s');
        
        if($customer_stamp_active->stamp_active < $deals->stamp_price){
            $json['result'] = FALSE;
            $json['result_message'] = 'Stamp Anda Tidak Cukup';
        }elseif($customer_stamp_active->stamp_expiry_date != NULL && $customer_stamp_active->stamp_expiry_date < $date){
            $json['result'] = FALSE;
            $json['result_message'] = 'Stamp Anda Sudah Expired';
        }else{

            // BenkaStamp::beginTransaction();

            $stamp_left['stamp_active'] = $customer_stamp_active->stamp_active - $deals->stamp_price;

            $update = BenkaStamp::UpdateCustomer($customer_stamp_active->customer_id,$stamp_left);

            // if($update == false){
            //     BenkaStamp::rollbackTransaction();
            //     $json['result'] = FALSE;
            //     $json['result_message'] = 'Proses Redeem gagal, coba kembali beberapa saat lagi.';

            //     return json_encode($json);
            // }

            $redeem['deals_id'] = $deals->id;
            $redeem['customer_id']      = $customer_stamp_active->customer_id;
            $redeem['customer_email']   = $customer_stamp_active->customer_email;
            $redeem['first_name']       = $customer_stamp_active->customer_fname;
            $redeem['last_name']        = $customer_stamp_active->customer_lname;
            $redeem['redeem_status']    = 0;
            $redeem['stamp_used']       = $deals->stamp_price;

            Log::notice('########## Insert benka Redeem Log ########## | ' . json_encode($redeem));

            $insert_redeem = BenkaStamp::InsertRedeem($redeem);

            // if($insert_redeem == false){
            //     BenkaStamp::rollbackTransaction();
            //     $json['result'] = FALSE;
            //     $json['result_message'] = 'Proses Redeem gagal, coba kembali beberapa saat lagi.';

            //     return json_encode($json);
            // }

            // $log['purchase_code']       = 0;
            // $log['stamp_value']         = $deals->stamp_price;
            // $log['current_config_val']  = 0;
            // $log['delivered_date']      = $date;
            // $log['customer_id']         = $customer_stamp_active->customer_id;
            // $log['customer_email']      = $customer_stamp_active->customer_email;
            // $log['type']                = 'DB';
            // $log['stamp_status']        = 1;
            // $log['description']         = 'Redeem Deals '.$deals->deals_name;
            // $log['flag_pos']            = 0;

            // Log::notice('########## Insert benka history Log ########## | ' . json_encode($log));

            // $insert_log = BenkaStamp::InsertLogHistory($log);

            // if($insert_log == false){
            //     BenkaStamp::rollbackTransaction();
            //     $json['result'] = FALSE;
            //     $json['result_message'] = 'Proses Redeem gagal, coba kembali beberapa saat lagi.';

            //     return json_encode($json);
            // }

            // BenkaStamp::commitTransaction();

            $json['result'] = TRUE;
            $json['result_message'] = 'Proses Redeem Anda Akan Segera Kami Proses';
        }

        return json_encode($json);
    }

    public function stampDealsRedeemMobile($id = NULL){
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id      = $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($channel == 1 && $channel == 3){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }         

        $customer_stamp_active = BenkaStamp::GetStampActive($user->customer_id);

        $deals = BenkaStamp::StampDealsDetail($id);

        $date = date('Y-m-d H:i:s');
        $result_message = '';

        if($customer_stamp_active->stamp_active < $deals->stamp_price){
            $result_message = 'Stamp Anda Tidak Cukup';
        }elseif($customer_stamp_active->stamp_expiry_date != NULL && $customer_stamp_active->stamp_expiry_date < $date){
            $result_message = 'Stamp Anda Sudah Expired';
        }else{
            $stamp_left['stamp_active'] = $customer_stamp_active->stamp_active - $deals->stamp_price;

            $update = BenkaStamp::UpdateCustomer($customer_stamp_active->customer_id,$stamp_left); 
 
            $redeem['deals_id'] = $deals->id;
            $redeem['customer_id']      = $customer_stamp_active->customer_id;
            $redeem['customer_email']   = $customer_stamp_active->customer_email;
            $redeem['first_name']       = $customer_stamp_active->customer_fname;
            $redeem['last_name']        = $customer_stamp_active->customer_lname;
            $redeem['redeem_status']    = 0;
            $redeem['stamp_used']       = $deals->stamp_price;

            $insert_redeem = BenkaStamp::InsertRedeem($redeem);

            $log['purchase_code']       = 0;
            $log['stamp_value']         = $deals->stamp_price;
            $log['current_config_val']  = 0;
            $log['delivered_date']      = $date;
            $log['customer_id']         = $customer_stamp_active->customer_id;
            $log['customer_email']      = $customer_stamp_active->customer_email;
            $log['type']                = 'DB';
            $log['stamp_status']        = 1;
            $log['description']         = 'Redeem Deals '.$deals->deals_name;
            $log['flag_pos']            = 0;

            $insert_log = BenkaStamp::InsertLogHistory($log);

            $result_message = 'Proses Redeem Anda Akan Segera Kami Proses';
        }

        return redirect('/user/stamp/deals/'.$id)->with('message',$result_message); 
    }
    
    public function stampTerms()
    {
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($domain_id == 3 && $channel == 5){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }         
        $limit = 10;

        $terms              = Self::jsonBenkaStampTerms();
        $data['terms']      = json_decode($terms->getContent());
        $data['user']       = $user;
        $data['limit']      = $limit;
        return get_view('account', 'account.stampterms', $data);    
    }
    
    public function stampFaq()
    {
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        
        //Redirect if domain shopdeca
        if($domain_id == 3 && $channel == 5){ 
          abort(404);
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }   

        $faq                = Self::jsonBenkaStampFaq();
        $data['faq']        = json_decode($faq->getContent());
        $data['user']       = $user;
        return get_view('account', 'account.stampfaq', $data);    
    }
    
    public function stampHistory(Request $request){
        $get_domain     = get_domain();
        $channel        = $get_domain['channel'];
        $domain         = $get_domain['domain'];
        $domain_id 	= $get_domain['domain_id'];
        
        //Redirect 404 if domain shopdeca
        if($domain_id == 3 || ($channel == 1 || $channel == 3 || $channel == 5)){ 
            abort(404);   
        }
        
        if (!Auth::check()) {
          return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

        $user = Auth::user();

        if (empty($user)) {
                return redirect('/login');
        }                 
        $paginate                       = 5;    
        $data['user']                   = $user;
        $data['stamp_history']          = BenkaStamp::StampHistory($user->customer_id, $paginate);                  
        $data['limit']                  = $paginate;
        $data['page']                   = $request->get('page') != null ? $request->get('page') : 1;
        return get_view('account', 'account.stamphistory', $data);    
    }
    
    public function jsonBenkaStampTerms()
    {
        $get_domain     = get_domain();                
        $domain_id 	= $get_domain['domain_id'];
        $terms          = [];
        
        switch($domain_id){
            case 1 : $terms = [
                    0 => "Anda berhak mendapatkan Benka Stamp untuk setiap pembelanjaan yang sudah terkonfirmasi senilai Rp 250,000 (setelah diskon dan ongkos kirim) dan berlaku kelipatan",
                    1 => "Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan pelanggan, setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund. Untuk pembelanjaan di Pop Up Store Berrybenka, Benka Stamp akan langsung ditambahkan ke akun Anda.",
                    2 => "Perolehan Benka Stamp berlaku untuk semua pembelian di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store (dengan menggunakan alamat email yang sama)",
                    3 => "Tukarkan Benka Stamp Anda dengan berbagai produk menarik yang akan terus kami update secara berkala",
                    4 => "Hadiah yang sudah ditukar tidak dapat dikembalikan dengan alasan apapun, kecuali telah ditemukan kelalaian atau kesalahan dari pihak Berrybenka"
                ];
            break;
            case 2 : $terms = [
                    0 => "Anda berhak mendapatkan Benka Stamp untuk setiap pembelanjaan yang sudah terkonfirmasi senilai Rp 250,000 (setelah diskon dan ongkos kirim) dan berlaku kelipatan",
                    1 => "Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan pelanggan, setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund. Untuk pembelanjaan di Pop Up Store Berrybenka, Benka Stamp akan langsung ditambahkan ke akun Anda.",
                    2 => "Perolehan Benka Stamp berlaku untuk semua pembelian di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store (dengan menggunakan alamat email yang sama)",
                    3 => "Tukarkan Benka Stamp Anda dengan berbagai produk menarik yang akan terus kami update secara berkala",
                    4 => "Hadiah yang sudah ditukar tidak dapat dikembalikan dengan alasan apapun, kecuali telah ditemukan kelalaian atau kesalahan dari pihak Berrybenka"
                ];
            break;  
            default : $terms = [
                    0 => "Anda berhak mendapatkan Benka Stamp untuk setiap pembelanjaan yang sudah terkonfirmasi senilai Rp 250,000 (setelah diskon dan ongkos kirim) dan berlaku kelipatan",
                    1 => "Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan pelanggan, setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund. Untuk pembelanjaan di Pop Up Store Berrybenka, Benka Stamp akan langsung ditambahkan ke akun Anda.",
                    2 => "Perolehan Benka Stamp berlaku untuk semua pembelian di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store (dengan menggunakan alamat email yang sama)",
                    3 => "Tukarkan Benka Stamp Anda dengan berbagai produk menarik yang akan terus kami update secara berkala",
                    4 => "Hadiah yang sudah ditukar tidak dapat dikembalikan dengan alasan apapun, kecuali telah ditemukan kelalaian atau kesalahan dari pihak Berrybenka"
                ];               
        }
        return response()->json($terms);
    }
    
    public function jsonBenkaStampFaq(){
        $get_domain     = get_domain();                
        $domain_id 	= $get_domain['domain_id'];
        $faq            = [];
        
        switch($domain_id){
            case 1 : $faq = [
                        'Apakah itu Benka Stamp?' => [
                            0 => 'Benka Stamp merupakan loyalty program dari Berrybenka yang dipersembahkan khusus untuk pelanggan setia Berrybenka, Hijabenka dan Berrybenka Pop Up Store dengan berbagai hadiah menarik yang kami update terus secara berkala.'
                        ],
                        'Bagaimana cara saya mendapatkan Benka Stamp?' => [
                            0 => 'Anda akan mendapatkan Benka Stamp untuk setiap pembelian senilai Rp 250,000 (setelah diskon dan ongkos kirim) berlaku kelipatan. Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan customer , setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund.'
                        ],
                        'Apakah perolehan Benka Stamp hanya untuk pembelian produk atau merk tertentu saja?' => [
                            0 => 'Tidak, perolehan Benka Stamp berlaku untuk semua produk atau merk yang tersedia di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store.'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat hangus?' => [
                            0 => 'Benka Stamp akan berlaku hingga 365 hari setelah tanggal pembelian terakhir. Jika tidak ada pembelian di hari ke-366 maka seluruh Benka Stamp di akun tersebut akan hangus, namun Anda tidak perlu khawatir, cukup lakukan pembelian apapun dengan tanpa minimum pembelian maka masa berlaku Benka Stamp Anda akan otomatis diperpanjang.'
                        ],
                        'Apakah hadiah yang saya tukarkan dapat ditukar atau dikembalikan?' => [
                            0 => 'Hadiah yang sudah ditukarkan dengan Benka Stamp tidak dapat dikembalikan atau ditukarkan dengan alasan apapun kecuali telah ditemukan kelalaian atau kesalahan oleh Berrybenka'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat ditransfer, atau dipindahkan ke akun lain?' => [
                            0 => 'Untuk saat ini Benka Stamp yang diperoleh tidak dapat ditransfer atau dipindahkan ke akun lain'
                        ],
                        'Jika terdapat kesalahan atau ketidakcocokan dalam pemesanan saya (salah ukuran, warna, dll) yang mengakibatkan saya harus retur atau refund, apakah Benka Stamp saya akan tetap didapatkan?' => [
                            0 => 'Berrybenka selalu berkomitmen memberikan pelayanan terbaik dalam setiap pemesanan yang dilakukan oleh pelanggan setia kami. Untuk pemesanan yang direfund, Benka Stamp akan dibatalkan, namun untuk pemesanan yang diretur dengan produk lain yang sama nilainya, Benka Stamp akan tetap ditambahkan ke akun Anda'
                        ],                
                    ];
            break;
            case 2 :$faq = [
                        'Apakah itu Benka Stamp?' => [
                            0 => 'Benka Stamp merupakan loyalty program dari Berrybenka yang dipersembahkan khusus untuk pelanggan setia Berrybenka, Hijabenka dan Berrybenka Pop Up Store dengan berbagai hadiah menarik yang kami update terus secara berkala.'
                        ],
                        'Bagaimana cara saya mendapatkan Benka Stamp?' => [
                            0 => 'Anda akan mendapatkan Benka Stamp untuk setiap pembelian senilai Rp 250,000 (setelah diskon dan ongkos kirim) berlaku kelipatan. Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan customer , setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund.'
                        ],
                        'Apakah perolehan Benka Stamp hanya untuk pembelian produk atau merk tertentu saja?' => [
                            0 => 'Tidak, perolehan Benka Stamp berlaku untuk semua produk atau merk yang tersedia di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store.'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat hangus?' => [
                            0 => 'Benka Stamp akan berlaku hingga 365 hari setelah tanggal pembelian terakhir. Jika tidak ada pembelian di hari ke-366 maka seluruh Benka Stamp di akun tersebut akan hangus, namun Anda tidak perlu khawatir, cukup lakukan pembelian apapun dengan tanpa minimum pembelian maka masa berlaku Benka Stamp Anda akan otomatis diperpanjang.'
                        ],
                        'Apakah hadiah yang saya tukarkan dapat ditukar atau dikembalikan?' => [
                            0 => 'Hadiah yang sudah ditukarkan dengan Benka Stamp tidak dapat dikembalikan atau ditukarkan dengan alasan apapun kecuali telah ditemukan kelalaian atau kesalahan oleh Berrybenka'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat ditransfer, atau dipindahkan ke akun lain?' => [
                            0 => 'Untuk saat ini Benka Stamp yang diperoleh tidak dapat ditransfer atau dipindahkan ke akun lain'
                        ],
                        'Jika terdapat kesalahan atau ketidakcocokan dalam pemesanan saya (salah ukuran, warna, dll) yang mengakibatkan saya harus retur atau refund, apakah Benka Stamp saya akan tetap didapatkan?' => [
                            0 => 'Berrybenka selalu berkomitmen memberikan pelayanan terbaik dalam setiap pemesanan yang dilakukan oleh pelanggan setia kami. Untuk pemesanan yang direfund, Benka Stamp akan dibatalkan, namun untuk pemesanan yang diretur dengan produk lain yang sama nilainya, Benka Stamp akan tetap ditambahkan ke akun Anda'
                        ],                
                    ];
            break;  
            default : $faq = [
                        'Apakah itu Benka Stamp?' => [
                            0 => 'Benka Stamp merupakan loyalty program dari Berrybenka yang dipersembahkan khusus untuk pelanggan setia Berrybenka, Hijabenka dan Berrybenka Pop Up Store dengan berbagai hadiah menarik yang kami update terus secara berkala.'
                        ],
                        'Bagaimana cara saya mendapatkan Benka Stamp?' => [
                             0 => 'Anda akan mendapatkan Benka Stamp untuk setiap pembelian senilai Rp 250,000 (setelah diskon dan ongkos kirim) berlaku kelipatan. Benka Stamp akan ditambahkan ke akun Anda paling lambat 40 hari setelah barang sampai di tangan customer , setelah terkonfirmasi bahwa pembelian tersebut tidak diretur atau direfund.'
                        ],
                        'Apakah perolehan Benka Stamp hanya untuk pembelian produk atau merk tertentu saja?' => [
                            0 => 'Tidak, perolehan Benka Stamp berlaku untuk semua produk atau merk yang tersedia di berrybenka.com, hijabenka.com maupun Berrybenka Pop Up Store.'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat hangus?' => [
                            0 => 'Benka Stamp akan berlaku hingga 365 hari setelah tanggal pembelian terakhir. Jika tidak ada pembelian di hari ke-366 maka seluruh Benka Stamp di akun tersebut akan hangus, namun Anda tidak perlu khawatir, cukup lakukan pembelian apapun dengan tanpa minimum pembelian maka masa berlaku Benka Stamp Anda akan otomatis diperpanjang.'
                        ],
                        'Apakah hadiah yang saya tukarkan dapat ditukar atau dikembalikan?' => [
                            0 => 'Hadiah yang sudah ditukarkan dengan Benka Stamp tidak dapat dikembalikan atau ditukarkan dengan alasan apapun kecuali telah ditemukan kelalaian atau kesalahan oleh Berrybenka'
                        ],
                        'Apakah Benka Stamp yang saya peroleh dapat ditransfer, atau dipindahkan ke akun lain?' => [
                            0 => 'Untuk saat ini Benka Stamp yang diperoleh tidak dapat ditransfer atau dipindahkan ke akun lain'
                        ],
                        'Jika terdapat kesalahan atau ketidakcocokan dalam pemesanan saya (salah ukuran, warna, dll) yang mengakibatkan saya harus retur atau refund, apakah Benka Stamp saya akan tetap didapatkan?' => [
                            0 => 'Berrybenka selalu berkomitmen memberikan pelayanan terbaik dalam setiap pemesanan yang dilakukan oleh pelanggan setia kami. Untuk pemesanan yang direfund, Benka Stamp akan dibatalkan, namun untuk pemesanan yang diretur dengan produk lain yang sama nilainya, Benka Stamp akan tetap ditambahkan ke akun Anda'
                        ],                
                    ];              
        }
        return response()->json($faq);

    }

    public function confirmEmail() {
		date_default_timezone_set('Asia/Jakarta');		
		// if (!Auth::check()) {
        //  return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        // }
		
		$userid = \Request::get('id');
		$token = \Request::get('token');
		$now = date('Y-m-d H:i:s');
		
		$account = Customer::where('customer_id','=',$userid)->first();
		if ($account) {
            $confirm_email_config = config('berrybenka.confirm_email');
			// Check if reset password has expired
			if ($now < (strtotime($account->resetsenton) + $confirm_email_config["confirm_email_expiration"])) {
				// Check if token is valid
				if ($token == sha1($account->customer_id . $account->resetsenton . $confirm_email_config["confirm_email_secret"])) {
					// Remove reset sent on datetime
					$account->resetsenton = NULL;
                    $account->email_status = 1;
                    $account->save();
                    return redirect('/login')->with('success_message', "Email anda telah berhasil dikonfirmasi."); 
				} else {
					$notice = "Token tidak valid.";
				}
			} else {
				$notice = "Maaf, halaman konfirmasi email anda telah kadaluarsa.";
			}
		} else {
			$notice = "Maaf, akun tidak terdaftar.";
		}
		
		// Insert your code for showing an error message here
		return redirect('/login')->with('error_message', $notice);
	}
}
