<?php namespace App\Modules\Checkout\Models;

use Illuminate\Database\Eloquent\Model;
use \App\Modules\Checkout\Models\Order;
use Auth;
use Cart;
use DB;
use Log;
use \App\Modules\Checkout\Models\Payment;
use Veritrans_Config;
use Veritrans_VtDirect;
use Veritrans_Transaction;

class Veritrans extends Model {

  /*
  $cc_data = [
        'cc_holder' => ucwords(strtolower($this->input->post('card_holder'))),
        'cc_address' => $this->input->post('cc_address'),
        'cc_city' => ucwords(strtolower($this->input->post('cc_city'))),
        'cc_country' => ucwords(strtolower($this->input->post('cc_country'))),
        'cc_zipcode' => $this->input->post('cc_zipcode'),
        'cc_phone' => $this->input->post('cc_phone'),
        'cc_billing_address' => $this->input->post('cc_billing_address'),
        'cc_email' => $this->input->post('cc_email')
      ];
  /*

  /*
  $sent = [
        'token_id' => $this->input->post('token_id'),
        'cc_data' => $cc_data,
        'grand_total_vt' => $grand_total_vt,
        'voucher_name' => $this->input->post('voucher_name'),
        'quantity' => $quantity,
        'installment_term' => $installment_term,
        'acquiring_bank' => $acquiring_bank,
        'discount_set' => $discount_set,
        'freeshipping_set' => $freeshipping_set,
        'paycode_set' => $paycode_set,
      ];
  */
  
  /**
   * Veritrans Integration CC
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function chargeVeritrans(array $data)
  {
    Log::notice('Process chargeVeritrans: Started');
    
    $domain_id      = (isset($data['domain_id'])) ? $data['domain_id'] : NULL ;
    $customer_email = (isset($data['customer_email'])) ? $data['customer_email'] : NULL ;
    $customer_fname = (isset($data['customer_fname'])) ? $data['customer_fname'] : NULL ;
    $customer_lname = (isset($data['customer_lname'])) ? $data['customer_lname'] : NULL ;
    $payment_method = $data['payment_method'];
    $fetch_cart     = (isset($data['fetch_cart'])) ? $data['fetch_cart'] : array();
    
    if($domain_id == NULL){
      Log::notice('Process chargeVeritrans: Domain ID is missing.');
      return false;
    }
    
    if($payment_method != 5 && $payment_method != 24 && $payment_method != 20 && $payment_method != 4 && $payment_method != 3 && $payment_method != 28 && $payment_method != 98 && $payment_method != 343){
      Log::notice('Process chargeVeritrans: Transaction is not using veritrans.');
      return false;
    }
    
    $customer_address = ($data['customer_address'] != NULL || !empty($data['customer_address'])) ? $data['customer_address'] : array() ;

    $cc_data = [
      'cc_holder'           => ucwords(strtolower($data['cc_holder'])),
      'cc_address'          => $customer_address['shipping']['address_street'],
      'cc_city'             => ucwords(strtolower($customer_address['shipping']['address_city'])),
      'cc_country'          => ucwords('Indonesia'),
      'cc_zipcode'          => $customer_address['shipping']['address_postcode'],
      'cc_phone'            => $customer_address['shipping']['address_phone'],
      'cc_billing_address'  => $customer_address['billing']['address_street'],
      'cc_email'            => $customer_email
    ];

    $sent = [
      'domain_id'         => $domain_id,
      'customer_email'    => $customer_email,
      'customer_fname'    => $customer_fname,
      'customer_lname'    => $customer_lname,
      'token_id'          => $data['token_id'],
      'cc_data'           => $cc_data,
      'fetch_cart'        => $fetch_cart,
      'grand_total_vt'    => $data['total']['grand_total'],
      'quantity'          => count($fetch_cart),
      'acquiring_bank'    => Payment::getBankAcquire(),
      'discount_set'      => $data['total']['total_freegift_auto_value'] + $data['total']['total_voucher_value'] + $data['total']['total_freegift_value'] + $data['total']['benka_point'],
      'freeshipping_set'  => ($data['total']['is_freeshipping'] || $data['total']['is_freeshipping_promotions']) && $data['total']['shipping_type'] == 1 ? 0 : $data['total']['shipping_cost'],
      'paycode_set'       => $data['total']['paycode'],
      'klikbca_user_id'   => $data['klikbca_user_id']
    ];
    
    $purchase_code = $data['order_header']['purchase_code'];
    
    $integration = 'integrationCreditCard';
    if($payment_method == 24){
      $integration = 'integrationIndomaret';
    }else if($payment_method == 99){ //Not Yet
      $integration = 'integrationInstallment';
    }else if($payment_method == 20){
      $integration = 'integrationMandiriDebit';
    }else if($payment_method == 4){
      $integration = 'integrationKlikpay';
    }else if($payment_method == 3){
      $integration = 'integrationKlikBCA';
    }else if($payment_method == 28){
      $integration = 'integrationBCAVirtualAccount';
    }else if($payment_method == 98){        
      $integration = 'integrationPermataVirtualAccount';    
    }else if($payment_method == 343){        
      $integration = 'integrationGopay';    
    }
    
    return Self::$integration($sent, $purchase_code);
  }
  
  public static function populateItem(array $sent)
  {
    $fetch_cart = $sent['fetch_cart'];
    $items      = NULL;
    
    // Populate items
    if (!empty($fetch_cart)) {
      foreach ($fetch_cart as $item) {

        $set_price = ceil($item['price']); 

        if ($item['name'] !== NULL) {
          $items[] = array(
            'id' => veritransFilter($item['SKU'], 50),
            'price' => $set_price,
            'quantity' => veritransFilter($item['qty'], 50),
            'name' => veritransFilter($item['name'],50),
          );
        }
      }
    }

    if($sent['discount_set'] > 0){
      $items[] = array(
        'id' => 'Discount',
        'price' => '-'.$sent['discount_set'],
        'quantity' => '1',
        'name' => 'Discount'
      );  
    }

    if ($sent['freeshipping_set'] > 0){
      $items[] = array(
        'id' => 'Shipping Cost',
        'price' => $sent['freeshipping_set'],
        'quantity' => '1',
        'name' => 'Shipping Cost'
      );
    }

    if ($sent['paycode_set'] > 0){
      $items[] = array(
        'id' => 'Paycode',
        'price' => $sent['paycode_set'],
        'quantity' => '1',
        'name' => 'Paycode'
      );
    }
    
    return $items;
  }
  
  public static function populateCustomerDetail(array $sent)
  {
    //Get from db or session
    $first_name_db   = isset($sent['customer_fname']) ? $sent['customer_fname'] : '';
    $last_name_db    = isset($sent['customer_lname']) ? $sent['customer_lname'] : '';
    $email_db        = isset($sent['customer_email']) ? $sent['customer_email'] : '';
    $phone_db        = !empty($sent['cc_data']['cc_phone']) ? $sent['cc_data']['cc_phone'] : '081322311801';
    
    // Populate customer's billing address
    $billing_address = array(
      'first_name'   => $first_name_db,
      'last_name'    => $last_name_db,
      'address'      => ! empty($sent['cc_data']['cc_address']) ? $sent['cc_data']['cc_address'] : 'Karet Belakang 15A, Setiabudi.',
      'city'         => ! empty($sent['cc_data']['cc_city']) ? $sent['cc_data']['cc_city'] : 'Jakarta',
      'postal_code'  => ! empty($sent['cc_data']['cc_zipcode']) ? $sent['cc_data']['cc_zipcode'] : '51161',
      'phone'        => ! empty($sent['cc_data']['cc_phone']) ? $sent['cc_data']['cc_phone'] : '081322311801',
      'country_code' => 'IDN'
    );
    
    // Populate customer's shipping address
    $shipping_address = array(
      'first_name'   => $first_name_db,
      'last_name'    => $last_name_db,
      'address'      => ! empty($sent['cc_data']['cc_billing_address']) ? $sent['cc_data']['cc_billing_address'] : 'Bakerstreet 221B.',
      'city'         => ! empty($sent['cc_data']['cc_city']) ? $sent['cc_data']['cc_city'] : 'Jakarta',
      'postal_code'  => ! empty($sent['cc_data']['cc_zipcode']) ? $sent['cc_data']['cc_zipcode'] : '51162',
      'phone'        => ! empty($sent['cc_data']['cc_phone']) ? $sent['cc_data']['cc_phone'] : '081322311801',
      'country_code' => 'IDN'
    );
    
    $customer_details = array(
      'first_name'       => $first_name_db,
      'last_name'        => $last_name_db,
      'email'            => $email_db,
      'phone'            => $phone_db,
      'billing_address'  => $billing_address,
      'shipping_address' => $shipping_address
    );
    
    return $customer_details;
  }
  
  public static function setVeritransStatusMessage($status_code)
  {
    $status_msg = 'Transaksi berhasil/settlement';
    
    if($status_code == 202){
      $status_msg = 'Request berhasil tapi transaksi ditolak penyedia pembayaran.';
    }else if($status_code == 400){
      $status_msg = 'Validasi error. Anda data yang salah; validasi error, kesalahan tipe transaksi, kesalahan format kartu kredit, dll.';
    }else if($status_code == 401){
      $status_msg = 'Akses transaksi ditolak.';
    }else if($status_code == 402){
      $status_msg = 'Akses transaksi metode pembayaran ditolak.';
    }else if($status_code == 403){
      $status_msg = 'Adanya kesalahan HTTP request.';
    }else if($status_code == 404){
      $status_msg = 'Sumber tidak ditemukan.';
    }else if($status_code == 405){
      $status_msg = 'Metode HTTP tidak diizinkan.';
    }else if($status_code == 406){
      $status_msg = 'Adanya duplikasi nomor pembayaran.';
    }else if($status_code == 407){
      $status_msg = 'Transaksi telah lewat dari masa berlaku.';
    }else if($status_code == 408){
      $status_msg = 'Adanya kesalahan tipe data.';
    }else if($status_code == 409){
      $status_msg = 'Terlalu banyak transaksi dengan nomor kartu yang sama.';
    }else if($status_code == 410){
      $status_msg = 'Akun sudah dinonaktifkan.';
    }else if($status_code == 411){
      $status_msg = 'Token ID sudah expired.';
    }else if($status_code == 412){
      $status_msg = 'Tidak bisa memodifikasi status transaksi.';
    }else if($status_code == 413){
      $status_msg = 'Ada kesalahan syntax body.';
    }else if($status_code == 500){
      $status_msg = 'Server Internal Error.';
    }else if($status_code == 501){
      $status_msg = 'Fitur akan segera tersedia.';
    }else if($status_code == 502){
      $status_msg = '	Server Internal Error: Masalah Koneksi Bank.';
    }else if($status_code == 503){
      $status_msg = 'Server Internal Error.';
    }else if($status_code == 504){
      $status_msg = 'Server Internal Error: Deteksi Fraud tidak tersedia.';
    }
    
    return $status_msg;
  }
  
  public static function setVeritransResult($result)
  {
    $status['code']         = $result->status_code;
    $status['status_msg']   = Self::setVeritransStatusMessage($result->status_code);
    $status['masked_card']  = (isset($result->masked_card)) ? $result->masked_card : NULL ;
    $status['order_id']     = $result->order_id;
    $status['payment_code'] = (isset($result->payment_code)) ? $result->payment_code : NULL ;
    $status['redirect_url'] = (isset($result->redirect_url)) ? $result->redirect_url : NULL ;
    $status['va_numbers']   = (isset($result->va_numbers)) ? $result->va_numbers : array() ;
    $status['fraud_status'] = (isset($result->fraud_status)) ? $result->fraud_status : NULL ;
    
    if ($result->status_code == "200") {
        $status['msg'] = "Transaction status for order id " . $result->order_id . ": " . $result->transaction_status;
        $status['flag'] = 1;
        $status['transaction_msg'] = "Transaksi berhasil";
    } else if ($result->status_code == "202") {
        $status['msg'] = "Transaction status for order id " . $result->order_id . ": " . $result->transaction_status;
        $status['flag'] = 2;
        $status['transaction_msg'] = "Transaksi ditolak";
    } else if ($result->status_code == "201") {
        $fraudInfo      = '';
        $message        = 'Transaksi challenge';
        if(isset($status['fraud_status'])){
            $fraudInfo      = ' | Fraud Status : ' . $status['fraud_status'];
            $message        = $status['fraud_status'];
        }

        $status['msg'] = "Transaction status for order id " . $result->order_id . ": " . $result->transaction_status . $fraudInfo;
        $status['flag'] = 2;
        $status['transaction_msg'] = $message;
    } else {
        $status['msg'] = "Error occurred on the sent transaction data";
        $status['flag'] = 2;
        $status['transaction_msg'] = "Transaksi error";
    }

        return $status;
  }
  
  public static function setVeritransConfig($data)
  {
    $domain_id = isset($data['domain_id']) ? $data['domain_id'] : NULL ;
    
    Veritrans_Config::$isProduction = \Config::get('berrybenka.veritrans.VERITRANS_IS_PRODUCTION');
    Veritrans_Config::$serverKey    = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BB');
    
    if($domain_id == 2){
      Veritrans_Config::$serverKey    = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_HB');
    }elseif($domain_id == 3){
      Veritrans_Config::$serverKey    = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_SD');
    }
    
    return true;
  }
  
  public static function statusVeritrans($data)
  {
    Log::notice('Process statusVeritrans: Started');
    
    $order_id = isset($data['veritrans_id']) ? $data['veritrans_id'] : NULL;
    $status   = array();
    
    if($order_id !== NULL){
      Self::setVeritransConfig($data);
      
      try{
        $veritrans_status = Veritrans_Transaction::status($order_id);

        $status['code']               = (isset($veritrans_status->status_code)) ? $veritrans_status->status_code : NULL ;
        $status['message']            = (isset($veritrans_status->status_message)) ? $veritrans_status->status_message : NULL ;
        $status['transaction_status'] = (isset($veritrans_status->transaction_status)) ? $veritrans_status->transaction_status : NULL ;
      } catch (\Exception $e) {

        $status['code']               = 400;
        $status['message']            = $e->getMessage() ;
        $status['transaction_status'] = NULL;
      }
    }
    
    Log::notice('Process statusVeritrans: Status Code: '.(isset($status['code'])) ? $status['code'] : 400);
    Log::notice('Process statusVeritrans: Status Message: '.(isset($status['message'])) ? $status['message'] : NULL);
    Log::notice('Process statusVeritrans: Transaction Status: '.(isset($status['transaction_status'])) ? $status['transaction_status'] : NULL);
    
    return $status;
  }
  
  public static function cancelVeritrans($data)
  {
    Log::notice('Process cancelVeritrans: Started');
    
    $order_id = isset($data['veritrans_id']) ? $data['veritrans_id'] : NULL;
    $status   = array();
    
    if($order_id !== NULL){
      Self::setVeritransConfig($data);
    
      try{
        $cancel = Veritrans_Transaction::cancel($order_id);

        $status['code']     = (isset($cancel->status_code)) ? $cancel->status_code : NULL ;
        $status['message']  = (isset($cancel->status_message)) ? $cancel->status_message : NULL ;
      } catch (\Exception $e) {

        $status['code']     = 400;
        $status['message']  = $e->getMessage() ;
      }
    }
    
    Log::notice('Process cancelVeritrans: Status Code: '.(isset($status['code'])) ? $status['code'] : 400 );
    Log::notice('Process cancelVeritrans: Status Message: '.(isset($status['message'])) ? $status['message'] : NULL);
    
    return $status;
  }
  
  /**
   * Manual charge veritrans not using vendor
   *
   * @access public
   * @param array $transaction_data
   * @return array $response
  */
  public static function chargeVTBCA($sent, $transaction_data, $endpoint = NULL)
  {
    $domain_id = isset($sent['domain_id']) ? $sent['domain_id'] : 1;
    
    $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_BB');
    if($domain_id == 2){
      $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_HB');
    }elseif($domain_id == 3){
      $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_SD');
    }
    
    
		$endpoint  = ($endpoint != NULL) ? $endpoint : \Config::get('berrybenka.veritrans.VERITRANS_ENDPOINT');

		// Mengirimkan request dengan menggunakan CURL
		$auth = sprintf('Authorization: Basic %s', base64_encode($server_key.':'));
		$ch = curl_init();
    
		//1.Payment API GET/POST
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(
      $ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        $auth
      )
		);
    
		//2.Payment API BLANK/NOT BLANK - $json_transaction_data
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction_data));
    
		//SSL Settings
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
    
		//3.Payment API Endpoint/2/3/4
		curl_setopt($ch, CURLOPT_URL, $endpoint);

		$data = curl_exec($ch);
    $info = curl_getinfo($ch);
    
		curl_close($ch);
		$response = json_decode($data);                
		return $response;
  }
  
  /**
   * Manual charge veritrans not using vendor
   *
   * @access public
   * @param array $transaction_data
   * @return array $response
  */
  public static function status(array $data, $endpoint = NULL)
  {
    $domain_id = isset($data['domain_id']) ? $data['domain_id'] : 1;
    
    $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_BB');
    if($domain_id == 2){
      $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_HB');
    }elseif($domain_id == 3){
      $server_key = \Config::get('berrybenka.veritrans.VERITRANS_SERVER_KEY_BCA_SD');
    }
    
    $order_id     = isset($data['purchase_code']) ? $data['purchase_code'] : NULL ;
    $veritrans_id = isset($data['veritrans_id']) ? $data['veritrans_id'] : NULL ;
    
    $endpoint  = ($endpoint != NULL) ? $endpoint.$veritrans_id.'/status' : \Config::get('berrybenka.veritrans.VERITRANS_API').$veritrans_id.'/status';

		// Mengirimkan request dengan menggunakan CURL
		$auth = sprintf('Authorization: Basic %s', base64_encode($server_key.':'));
		$ch = curl_init();
    
		//1.Payment API GET/POST
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt(
      $ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        $auth
      )
		);
    
		//2.Payment API BLANK/NOT BLANK - $json_transaction_data
		//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transaction_data));
    
		//SSL Settings
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
    
		//3.Payment API Endpoint/2/3/4
		curl_setopt($ch, CURLOPT_URL, $endpoint);

		$data = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($data);

		return $response;
  }

  /**
   * Veritrans Integration CC
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
  */
  public static function integrationCreditCard($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationCreditCard: Started');
    
    if(empty($sent['token_id'])){
      $status['code'] = 404;
      $status['msg']  = "Missing token_id";
      
      Log::notice('Process integrationCreditCard: Token ID is missing!');
      return $status;
    } 
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    // token_id represents the credit card that will be charged.
    // token_id can be obtained from the request through veritrans.min.js
    $token_id = $sent['token_id'];
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    $secureDefault          = false;
    $get_domain             = get_domain();
    $domain_id              = $get_domain['domain_id'];    
    
    
    $arrayCC['token_id']    = $token_id;
    $arrayCC['bank']        = $sent['acquiring_bank'];
    if($domain_id == 3){
        $secureDefault = true;        
        unset($arrayCC['bank']);
    }
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
        'payment_type'          => 'credit_card',
        'secure'                => $secureDefault,
        'credit_card'           => $arrayCC,
        'transaction_details'   => $transaction_details,
        'item_details'          => Self::populateItem($sent),
        'customer_details'      => Self::populateCustomerDetail($sent)
    );    
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = $token_id;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notifications
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notifications
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'credit_card';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationCreditCard: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration CC
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationInstallment($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationInstallment: Started');
    
    if(empty($sent['token_id'])){
      $status['code'] = 404;
      $status['msg']  = "Missing token_id";
      
      return $status;
    } 
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    // token_id represents the credit card that will be charged.
    // token_id can be obtained from the request through veritrans.min.js
    $token_id = $sent['token_id'];
    $installment_term = $sent['installment_term'];
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
      'payment_type'          => 'credit_card',
      'credit_card'           => array(
        'installment_term'    => $installment_term,
        'token_id'            => $token_id,
        'bank'                => $sent['acquiring_bank']
      ),
      'transaction_details'   => $transaction_details,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent)
    );
    
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = $token_id;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }

    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'credit_card';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationInstallment: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration CC
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationMandiriDebit($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationMandiriDebit: Started');
    
    if(empty($sent['token_id'])){
      $status['code'] = 404;
      $status['msg']  = "Missing token_id";
      
      Log::notice('Process integrationMandiriDebit: Token ID is missing!');
      return $status;
    }
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    // token_id represents the credit card that will be charged.
    // token_id can be obtained from the request through veritrans.min.js
    $token_id = $sent['token_id'];
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
      'payment_type'          => 'credit_card',
      'secure'                 => true,
      'credit_card'           => array(
        'token_id'            => $token_id,
        'bank'                => 'mandiri'
      ),
      'transaction_details'   => $transaction_details,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent)
    );
    
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = $token_id;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'credit_card';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationMandiriDebit: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration CC
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationIndomaret($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationIndomaret: Started');
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    // token_id represents the credit card that will be charged.
    // token_id can be obtained from the request through veritrans.min.js
    //$token_id = $sent['token_id'];
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    // Data that will be sent for charge transaction request with Indomaret.
    $customer_details = Self::populateCustomerDetail($sent);
    $transaction_data = array(
      "custom_expiry" => array(
        "order_time"      => date('Y-m-d H:i:s') . ' +0700',
        "expiry_duration" => 2,
        "unit"            => "day"
       ),
      'payment_type'      => 'cstore',      
      'cstore'            => array(
        'store'           => "indomaret",
        'message'         => $customer_details['first_name'].' '.$customer_details['last_name'].':'.$invoice_code
      ),
     'transaction_details' => $transaction_details,
     'item_details'        => Self::populateItem($sent),
     'customer_details'    => $customer_details
    );
    
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }

    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'cstore';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationIndomaret: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration Gopay
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationGopay($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationGopay: Started');  
          
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );

    $custom_expiry  = array(
      'order_time' => date('Y-m-d H:i:s') . ' +0700',
      'expiry_duration' => 48,
      'unit' => 'hour'
    );
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
      'payment_type'          => 'gopay',
      'transaction_details'   => $transaction_details,
      'custom_expiry'         => $custom_expiry,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent)
    );
    
    try {
      //$result = Veritrans_VtDirect::charge($transaction_data);
      $result = Self::chargeVTBCA($sent, $transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);

    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());      
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;      
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'gopay';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationGopay: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration Klikpay BCA
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationKlikpay($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationKlikpay: Started');  
          
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
      'payment_type'          => 'bca_klikpay',
      'bca_klikpay'           => array(
        'type'            => 1,
        'description'     => "Pembelian Barang"
      ),
      'transaction_details'   => $transaction_details,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent)
    );
    
    try {
      //$result = Veritrans_VtDirect::charge($transaction_data);
      $result = Self::chargeVTBCA($sent, $transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());      
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;      
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'bca_klikpay';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationKlikpay: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }

  /**
   * Veritrans Integration KlikBCA
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationKlikBca($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationKlikBca: Started');  
          
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with credit card.
    $transaction_data = array(
      'payment_type'          => 'bca_klikbca',
      'bca_klikbca'           => array(
        'description'            => $invoice_code,
        'user_id'                => $sent['klikbca_user_id']
      ),
      'transaction_details'   => $transaction_details,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent)
    );
    
    try {
      //$result = Veritrans_VtDirect::charge($transaction_data);
      $result = Self::chargeVTBCA($sent, $transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'bca_klikbca';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationKlikbca: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }
  
  /**
   * Veritrans Integration VA
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationBCAVirtualAccount($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationBCAVirtualAccount: Started');
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with veritrans virtual account.
    $transaction_data = array(
      "custom_expiry" => array(
        "order_time"      => date('Y-m-d H:i:s') . ' +0700',
        "expiry_duration" => 2,
        "unit"            => "day"
       ),
      'payment_type'          => 'bank_transfer',
      'transaction_details'   => $transaction_details,
      'item_details'          => Self::populateItem($sent),
      'customer_details'      => Self::populateCustomerDetail($sent),
      'bank_transfer'         => array(
        'bank'                  => "bca",
        'va_number'             => "111111"
      )
    );
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $log_vn['va_number']        = isset($result->va_numbers[0]->va_number) ? $result->va_numbers[0]->va_number : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'bank_transfer';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationBCAVirtualAccount: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }
  
  /**
   * Veritrans Integration VA Permata
   *
   * @access public
   * @param array $sent
   * @return string $invoice_code
   */
  public static function integrationPermataVirtualAccount($sent, $invoice_code)
  {
    $time_start = microtime(true);
    Log::notice('Process integrationPermataVirtualAccount: Started');
    
    // TODO: change with your actual server_key that can be found on Merchant Administration Portal (MAP)
    Self::setVeritransConfig($sent);
    
    $transaction_details = array(
      'order_id'      => $invoice_code,
      'gross_amount'  => $sent['grand_total_vt']
    );
    
    //Data that will be sent to request charge transaction with veritrans virtual account.
    $transaction_data = array(
      "custom_expiry" => array(
        "order_time"      => date('Y-m-d H:i:s') . ' +0700',
        "expiry_duration" => 2,
        "unit"            => "day"
       ),
      'payment_type'          => 'bank_transfer',      
      'bank_transfer'         => array(
        'bank'                  => "permata"
      ),
      'customer_details'      => Self::populateCustomerDetail($sent),
      'transaction_details'   => $transaction_details
    );
    try {
      $result = Veritrans_VtDirect::charge($transaction_data);
      $status = Self::setVeritransResult($result);      
      $transaction_msg  = $status['transaction_msg'];

      $transaction_status = isset($result->transaction_status) ? $result->transaction_status : NULL;
      $transaction_bin    = isset($result->masked_card) ? $result->masked_card : NULL;
      $transaction_id     = isset($result->order_id) ? $result->order_id : NULL;
      $status_code        = isset($result->status_code) ? $result->status_code : NULL;
      $status_message     = isset($result->status_message) ? $result->status_message : NULL;
      $charge_result      = json_encode($result);
    } catch(\Exception $e) {
      //error
      $status['code']         = 400;
      $status['msg']          = "Payment gateway validation error, " . str_replace('Veritrans', '', $e->getMessage());
      $status['status_msg']   = "Payment gateway validation error";
      $status['flag']         = 2;
      $transaction_msg        = "Transaksi error";
      //error

      $transaction_status = 'System Error';
      $transaction_bin    = NULL;
      $transaction_id     = $invoice_code;
      $status_code        = 400;
      $status_message     = $e->getMessage();
      $charge_result      = $e->getMessage();
    }
    
    //Veritrans Notification    
    $log_vn['orderId']          = $transaction_id;
    $log_vn['mStatus']          = $transaction_status;
    $log_vn['maskedCardNumber'] = $transaction_bin;
    $log_vn['mErrMsg']          = $transaction_msg;
    $log_vn['vResultCode']      = $status_code;
    $log_vn['params']           = $transaction_msg;
    $log_vn['veritrans_post']   = json_encode($charge_result) . ' | Server Key : ' . json_encode(Veritrans_Config::$serverKey) . ' | ' . (Veritrans_Config::$isProduction ? 'Production' : 'Development' );
    $log_vn['redirect_url']     = isset($result->redirect_url) ? $result->redirect_url : NULL;
    $log_vn['payment_code']     = isset($result->payment_code) ? $result->payment_code : NULL;
    $log_vn['veritrans_id']     = isset($result->transaction_id) ? $result->transaction_id : NULL;
    $log_vn['va_number']        = isset($result->permata_va_number) ? $result->permata_va_number : NULL;
    $status['log_vn']           = $log_vn;
    //End Veritrans Notification
    
    //Veritrans Verification
    $log_vv['request_date']   = date('Y-m-d H:i:s');
    $log_vv['purchase_code']  = $transaction_id;
    $log_vv['json_data']      = json_encode($transaction_data);
    $log_vv['payment_type']   = 'bank_transfer';
    $status['log_vv']         = $log_vv;
    //End Veritrans Verification
    
    $time_executed  = microtime(true) - $time_start;
    Log::notice('Process integrationPermataVirtualAccount: '.$transaction_msg.' Code: '.$status_code.'. Executed Time: '. $time_executed);
    return $status;
  }
}
