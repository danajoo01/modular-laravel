<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Checkout\Models\Order;
use Auth;
use Cart;
use Carbon\Carbon;
use DB;
use Log;

class Kredivo extends Model {	
	/**
	 * Processing Kredivo
	 *
	 * @access public
	 * @param string $create_purchase_code        	
	 * @return string
	 */
    
    private static function BaseUrl(){
        $appenv     = env('APP_ENV', 'development');
        if($appenv == 'development'){
            return \Config::get('berrybenka.KREDIVO.SANDBOX.BASEURL_REDIRECT');
        }else{
            return \Config::get('berrybenka.KREDIVO.PRODUCTION.BASEURL_REDIRECT');
        }        
    }
    
    private static function ServerKey(){
        $appenv         = env('APP_ENV', 'development');
        $get_domain     = get_domain(); 
        $domain_name    = strtoupper($get_domain['domain_name']);
        if($appenv == 'development'){
            return \Config::get('berrybenka.KREDIVO.SANDBOX.'. $domain_name .'_SERVER_KEY');
        }else{
            return \Config::get('berrybenka.KREDIVO.PRODUCTION.'. $domain_name .'_SERVER_KEY');   
        }       
    }
    
    /*
     * Eff
     * make request kredivo     
     */    
    public static function Call($requestData = [], $PathUrl = '', $method = 'POST'){                                              
        $config['url']  = Self::BaseUrl();                        
        $data           = isset($requestData) ? json_encode($requestData) : [];
        $kredivoURL     = $config['url'] . (isset($PathUrl) ? $PathUrl : '');
        
        Log::debug('Berrybenka requests : ' . $data);
        //curl process
        $curl           = curl_init();
        
        if($method == 'POST'){
            curl_setopt($curl, CURLOPT_POST, 1);    
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data),
                'Accept: application/json')
            );
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        }                
        
        curl_setopt($curl, CURLOPT_URL,$kredivoURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                
        $result = curl_exec($curl);        
        curl_close($curl);        
        
        //Log::emergency('Kredivo  API : ' . json_encode($kredivoURL));
        //Log::emergency('Kredivo  responses : ' . json_encode($result));
        
        return $result;
    } 
    
    /*
     * Eff
     * Fetch Kredivo Payment Type List
     */
    public static function PaymentType($RequestParams = []){   
        $result         = false;        
        $PathUrl        = '/v2/payments';
        $method         = 'POST';
        $purchasedItems = $RequestParams['items'];
        if(!empty($purchasedItems)){
            $purchasedItems = array_map(function($item) {
                return array(
                    'id' => $item['SKU'],
                    'name' => $item['name'],
                    'price' => (int)$item['price'],
                    'url' => $item['url'],
                    'type' => $item['type_url'],
                    'quantity' => (int)$item['qty']
                );
            }, $purchasedItems);
        }
               
        // SET REQUEST DATA
        $requestData['server_key'] = Self::ServerKey();        
        $requestData['amount']     = isset($RequestParams['total']) ? $RequestParams['total'] : 0;
        $requestData['items']      = $purchasedItems; 
        
        // REQUEST TO KREDIVO
        //Log::emergency('########## Process Kredivo Started [PaymentType] ##########'); 
        $KredivoResponse = Self::Call($requestData, $PathUrl, $method);
        //Log::emergency('########## Process Kredivo Finished [PaymentType] ##########');
        
        if($KredivoResponse){
            $result = $KredivoResponse;
        }
        
        return $result;
    }
    
    /*
     * Eff
     * Charge Checkout Kredivo
     */
    public static function Charge($RequestParams = []){
        $result         = false;
        $PathUrl        = '/v2/checkout_url';
        $ItemList       = [];
        $method         = 'POST';
        $server_name    = \Request::server('SERVER_NAME');
        $appenv         = env('APP_ENV', 'development');
        //FETCH PARAM 
        /*
        $orderItems     = $RequestParams['fetch_order_item'];              
        if(!empty($orderItems)){
            $ItemList = array_map(function($item) {
                return array(
                    'id' => $item->SKU,
                    'name' => $item->product_name,
                    'price' => $item->total_discount_price > 0 ? $item->total_discount_price : $item->total_price,                    
                    'type' => ($item->category != null) ? $item->category : '',
                    'quantity' => $item->quantity
                );
            }, $orderItems);
        }          
         */       
        $fetchCart      = $RequestParams['fetch_cart'];           
        if(!empty($fetchCart)){
            foreach($fetchCart as $item){
                $setPrice   = ceil($item['price']); 
                $ItemList[] = [
                    'id' => $item['SKU'],
                    'name' => $item['name'],
                    'price' => (int)$setPrice,
                    'type' => isset($item['type_id']) ? ucfirst($item['type_id']) : '',
                    'quantity' => (int)$item['qty']
                ];
            }
        }
        
        if($RequestParams['order_header']['shipping_finance'] > 0){
            $shipping   = [
                'id' => 'shippingfee',
                'name' => 'Shipping Fee',
                'price' => $RequestParams['order_header']['shipping_finance'],
                'quantity' => 1            
            ];
            $ItemList[] = $shipping;    
        }
        
        
        $discount                       = [
            'id' => 'discount',
            'name' => 'Discount',
            'price' => $RequestParams['order_header']['discount'],
            'quantity' => 1            
        ];                
        $ItemList[] = $discount;
        
        //transaction        
        $transaction['order_id']        = $RequestParams['order_header']['purchase_code'];
        $transaction['amount']          = $RequestParams['total']['grand_total'];
        $transaction['items']           = $ItemList;
        
        //customer
        $customer['first_name']         = $RequestParams['customer_fname'];
        $customer['last_name']          = $RequestParams['customer_lname'];
        $customer['email']              = $RequestParams['customer_email'];
        $customer['phone']              = '';
        
        //billing address
        $billing['first_name']          = $RequestParams['customer_fname'];
        $billing['last_name']           = $RequestParams['customer_lname'];
        $billing['address']             = $RequestParams['order_header']['billing_address'];
        $billing['city']                = $RequestParams['customer_address']['billing']['address_city'];
        $billing['postal_code']         = $RequestParams['customer_address']['billing']['address_postcode'];
        $billing['phone']               = $RequestParams['customer_address']['billing']['address_phone'];
        
        //shipping address
        $shippingAddr['first_name']     = $RequestParams['customer_fname'];
        $shippingAddr['last_name']      = $RequestParams['customer_lname'];
        $shippingAddr['address']        = $RequestParams['order_header']['shipping_address'];
        $shippingAddr['city']           = $RequestParams['customer_address']['shipping']['address_city'];
        $shippingAddr['postal_code']    = $RequestParams['customer_address']['shipping']['address_postcode'];
        $shippingAddr['phone']          = $RequestParams['customer_address']['shipping']['address_phone'];        
        
        // SET REQUEST DATA
        $requestData['server_key']          = Self::ServerKey();
        $requestData['payment_type']        = isset($RequestParams['kredivo_payment_type']) ? $RequestParams['kredivo_payment_type']  : '30_days';
        $requestData['transaction_details'] = $transaction;
        $requestData['customer_details']    = $customer;  
        $requestData['billing_address']     = $billing;
        $requestData['shipping_address']    = $shippingAddr;
        $requestData['expiration_time']     = Carbon::now()->addDays(2)->timestamp; 
        
        //url manage                
        //$requestData['push_uri']            = 'http://berrybenka.biz/checkout/kredivo_push_notif';        
        $requestData['push_uri']            = ($appenv == 'development') ? 'http://'. $server_name .'/checkout/kredivo_push_notif' : 'https://'. $server_name .'/checkout/kredivo_push_notif';        
        
        //backUriName -> ke link nya si API
        $backUriName                        = ($appenv == 'development') ? 'api-dev.berrybenka.biz/v1' : 'api.berrybenka.com/v1';
        $requestData['back_to_store_uri']   = ($appenv == 'development') ? 'http://'. $backUriName .'/checkout/final_order?domain=http://'.urlencode($server_name) : 'https://'. $backUriName .'/checkout/final_order?domain='.urlencode($server_name);
        
        // REQUEST TO KREDIVO
        //Log::emergency('########## Process Kredivo Started [Charge] ##########'); 
        $KredivoResponse = Self::Call($requestData, $PathUrl, $method);
        //Log::emergency('########## Process Kredivo Finished [Charge] ##########');
        
        if($KredivoResponse){
            $result['requests']     = [
                'purchase_code' => $transaction['order_id'],
                'json_data' => json_encode($requestData),
                'payment_type' => $requestData['payment_type']
            ];
            
            $result['responses']    = [
                'purchase_code' => $transaction['order_id'],
                'json_data' => $KredivoResponse,
                'payment_type' => $requestData['payment_type']
            ];    
            $result['purchase_code'] = $transaction['order_id'];
            $result['result'] = $KredivoResponse;
        }
        
        return $result;
    }
    
    public static function Status($params = []){
        $result             = false;        
        $transactionId      = isset($params['transaction_id']) ? $params['transaction_id'] : NULL;
        $signatureKey       = isset($params['signature_key']) ? $params['signature_key'] : NULL;
        //Log::emergency('KREDIVO TRANS ID = ' . $transactionId);
        //Log::emergency('KREDIVO SIGN KEY = ' . $signatureKey);
        $method             = 'GET';
        $requestData        = [];
        $PathUrl            = '/v2/update?transaction_id='. $transactionId .'&signature_key='. $signatureKey;
        
        // REQUEST TO KREDIVO
        //Log::emergency('########## Process Kredivo Started [Status] ##########'); 
        $KredivoResponse = Self::Call($requestData, $PathUrl, $method);
        //Log::emergency('########## Process Kredivo Finished [Status] ##########');
        
        if($KredivoResponse){
            $result = $KredivoResponse;
        }
        
        return $result;
    }
    
    public static function StatusByPO($purchase_code = NULL){       
        $result     = false;
        $response   = DB::table('kredivo_responses')
                ->select('json_data')
                ->where('purchase_code', '=', $purchase_code)
                ->where('response_type', '=', 3)
                ->first();
        
        if($response){
            $result = $response;
        }
        
        return $result;
    }

    /** kredivo confirm purchase
    *	Okto
    *	push notif from kredivo
    */

    public static function pushNotification($data) {	
        $json_data = json_encode($data);
        Log::info('CURL to kredivo: ' . $json_data);
        DB::table('kredivo_responses')
            ->insert([
            'response_date' =>  Carbon::now(),
            'purchase_code' => $data['order_id'],
            'json_data' => $json_data,
            'payment_type' => $data['payment_type'],
            'response_type' => 2
        ]);

        $PathUrl = "/v2/update?transaction_id=".$data['transaction_id']."&signature_key=".$data['signature_key'];
              
        try {
            $return     = Self::Call(array(),$PathUrl, "GET");       	 	
        } catch (Exception $e) {
            Log::error('KREDIVO RESPONSE ERROR');
        }        

        $confirm_order = json_decode($return);

        if (isset($confirm_order->transaction_status) && strcasecmp($confirm_order->transaction_status, "SETTLEMENT") === 0) {
            Log::info('Update order status');
            $update_payment = array('status' => 1, 'payment_status' => 'success');
            OrderPayment::updateOrderPayment($confirm_order->order_id, $update_payment);
            //End Update Order Payment
            //Update Order Item
            $filter_oi = array();
            $filter_oi['with'] = 'purchase_code';
            $filter_oi['value'] = $confirm_order->order_id;

            $update_oi = array();
            $update_oi['purchase_status'] = 3;
            $update_oi['order_status_item'] = 2;
            $approval_date = date("Y-m-d H:i:s");

            $update_item = array('purchase_status' => 3, 'order_status_item' => 2, 'item_warehouse_status' => 0, 'approval_date' => $approval_date);
            OrderItem::updateOrderItem($filter_oi, $update_item);

            $item_history = DB::table('order_item')->select(\DB::raw('*'))->where('purchase_code', '=', $confirm_order->order_id)->get();
            foreach ($item_history as $item) {
                $data_insert['order_item_id'] = $item->order_item_id;
                $data_insert['SKU'] = $item->SKU;
                $data_insert['purchase_code'] = $confirm_order->order_id;
                $data_insert['order_status_item'] = 2;
                $data_insert['created_by'] = $item->customer_id;
                $data_insert['created_date'] = date('Y-m-d H:i:s');

                DB::table('order_item_history')->insert($data_insert);
            }

            try {
                DB::table('kredivo_responses')
                        ->insert([
                            'response_date' =>  Carbon::now(),
                            'purchase_code' => $confirm_order->order_id,
                            'json_data' => $return,
                            'payment_type' => $confirm_order->payment_type,
                            'response_type' => 3
                ]);
            } catch (Exception $e) {
                Log::error('Kredivo response insert fail:' . $e->getMessage);
            }


            $param_oh['purchase_code'] = $confirm_order->order_id;
            $fetch_order_header = OrderHeader::fetchOrderHeader($param_oh);
            $get_discount = OrderDiscount::getDiscount($param_oh);

            $get_domain = get_domain();
            $domain_id = $get_domain['domain_id'];
            $channel = $get_domain['channel'];
            $domain_name = $get_domain['domain_name'];

            $mail_data = array();
            $mail_data['payment_method'] = 99;
            $mail_data['purchase_code'] = $fetch_order_header->purchase_code;
            $mail_data['customer_fname'] = $fetch_order_header->customer_fname;
            $mail_data['customer_lname'] = $fetch_order_header->customer_lname;
            $mail_data['purchase_date'] = $fetch_order_header->purchase_date;
            $mail_data['grand_total'] = $fetch_order_header->grand_total;
            $mail_data['purchase_price'] = $fetch_order_header->purchase_price;
            $mail_data['paycode'] = $fetch_order_header->paycode;
            $mail_data['shipping_finance'] = $fetch_order_header->shipping_finance;
            $mail_data['credit_use'] = $fetch_order_header->credit_use;
            $mail_data['order_shipping_address'] = $fetch_order_header->order_shipping_address;
            $mail_data['order_city'] = $fetch_order_header->order_city;
            $mail_data['order_province'] = $fetch_order_header->order_province;
            $mail_data['order_postcode'] = $fetch_order_header->order_postcode;
            $mail_data['order_phone'] = $fetch_order_header->order_phone;
            $mail_data['get_discount'] = $get_discount;
            $mail_data['veritrans_payment_code'] = NULL;
            $mail_data['veritrans_va_number'] = NULL;
            $mail_data['kredivo_payment_type'] = $confirm_order->payment_type;

            Order::sendMail($mail_data, $fetch_order_header->customer_email);            
        }
    }
}

