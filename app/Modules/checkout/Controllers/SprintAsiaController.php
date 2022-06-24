<?php namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\Order;
use \App\Modules\Checkout\Models\Sprintasiaz;

use Cart;

use Illuminate\Http\Request;

class SprintAsiaController extends Controller {
        
    
    /**
     * Checkout Klikpay - Communication with sprint and redirect to sprint klikpay
     * - Transfer Checkout in final order
     *
     * @access public
     * @return array
     */
    public function checkout_klikpay($new_checkout = NULL) {
        $define_domain = define_domain();
        $domain_id = $define_domain['domain_id'];
      
        // instance of the Auth class
        $auth_library = new Auth();
        $user_id = $auth_library->get_user_id();
        
        // instance of the CI_Cart class
        $cart = new CI_Cart();
        
        // instance of the User_service class
        $user_service = new User_service();
        $user_object = array_shift($user_service->user_get_by_id($user_id));
        $customer_email = $user_object->customer_email;
        $customer_fname = $user_object->customer_fname;
        $customer_lname = $user_object->customer_lname;
        
        // Set Payment method to get the right id case installment
        $session = new CI_Session();
        $address = $session->userdata('customer_shipping_address');
        $area = $session->userdata('customer_province');
        $name = $session->userdata('customer_city');
        $customer_postcode = $session -> userdata('customer_postcode');
        $customer_phone = $session -> userdata('customer_phone');
        $bill_address = $session->userdata('customer_shipping_address_bill');
        $total_final = $this->input->post('final_total');
        $payment_method = $session->userdata('payment_method');
        
        if ($payment_method == '0'){
            $pm = $this->session->userdata('payment_type_cicilan');
        }else{
            $pm = $session->userdata('payment_method');
        }
        $payment_method = $pm;
        
        // instance of the Order_service class
        $order_service = new Order_service();
        $create_purchase_code = $order_service->create_purchase_code($user_id, $channel = 1);
        
        // Checking purchase code in Order Process
        $check_purchase_exist = $order_service->check_purchase_exist($create_purchase_code);
        
        if ($check_purchase_exist == TRUE){
            echo "Something went wrong with system...<br/><br/><a href=" . site_url('checkout/cart') . ">Return to Berrybenka.com</a><br/>";
            return false;
        }
        
        // instance of the Payment_service class
        $payment_service = new Payment_service();
        $get_grand_total = ($new_checkout == NULL) ? $payment_service->get_grand_total() : $payment_service->get_grand_total_new();
        
        //Calculate Grand Total
        $promo_service = new Promo_service();
        $subtotal = $get_grand_total['total_before_shipping'];
        
        //Shipping
        $shipping_service = new Shipping_service();
        $shipping_weight = $shipping_service->get_shipping_weight();
        $shipping_method = $shipping_service->fetch_shipping_order_new($area, $name, $shipping_weight, $with_text = TRUE);
        asort($shipping_method);
        $shipping_cost = (isset($shipping_method[1]['cost'])) ? $shipping_method[1]['cost'] : NULL;
        $shipping_cost_fg = $session->userdata('shipping_cost_fg');
        
        $freeshipping = status_freeshipping($domain_id, $get_grand_total['total_before_user_credit']);
        if(!$freeshipping){
          $freeshipping = ($shipping_cost_fg == 1) ? 0 : $shipping_cost;
        }else{
          $freeshipping = 0;
        }
        $shipping_method = $session->userdata('shipping_method');
        
        $paycode = $session -> userdata('paycode');
        $cart_service = new Cart_service();
        $fetch_cart = $cart_service->draft_order_to_cart($user_id);
        $my_cart = new Cart_service();
        $my_subtotal = $my_cart -> subtotal_draft_order($fetch_cart);
        $subtotal = $my_subtotal;
        
        //Voucher
        $real_voucher_value = 0;
        if($session -> userdata('voucher_code') != ''):
          $voucher_code = $session -> userdata('voucher_code');
          $cek_voucher = $promo_service->check_voucher_v2($voucher_code, $get_grand_total['grand_total'], $fetch_cart,1);
          if ($cek_voucher ['result_error'] == '') :
            $real_voucher_value = $cek_voucher['result_data']['TOTAL_DISCOUNT'];
            $order_item_id_apply_voucher = $cek_voucher['result_data']['ORDER_ITEM_ID_APPLY'];
          endif;
        endif;
        
        if ($session -> userdata('voucher_mode') == 1 || $session -> userdata('voucher_mode') == 2 || $session -> userdata('voucher_mode') == 3 || $session -> userdata('voucher_mode') == 4 || $session -> userdata('voucher_mode') == 5 || $session -> userdata('voucher_mode') == 6 || $session -> userdata('voucher_mode') == 7 || $session -> userdata('voucher_mode') == 8 || $session -> userdata('voucher_mode') == 9) {
          $real_voucher_value = ( $real_voucher_value <> 0 ) ? $real_voucher_value : $session->userdata('real_voucher_value');
          $order_item_id_apply = ($order_item_id_apply_voucher == NULL) ? $session -> userdata('voucher_order_item_id_apply') : $order_item_id_apply_voucher;
          if (!empty($fetch_cart)) {
            foreach ($fetch_cart as $key => $items) {
              if (count($order_item_id_apply) == 0 || in_array($key, $order_item_id_apply)) {
                if ($session -> userdata('voucher_mode') == 4){
                  $real_voucher_value = $shipping_cost;
                }
              }
            }
          }
        }
        
        $total_value = 0;
        $my_price_subtotal = 0;
        if (!empty($fetch_cart)){
          foreach ($fetch_cart as $key => $items) {
            $my_price_subtotal = $my_price_subtotal + $items['subtotal_before_voucher'];
          }
        }
        
        $discount_service = new Discount_service();
        $array_method = array(
          'payment_method' => $session->userdata('payment_method'),
          'shipping_method' => $session->userdata('shipping_method'),
          'bin_number' => $session->userdata('bin_number')
        );
        $new_cart_promo = $discount_service->get_discount('freegift', $array_method);
        $hold_total_disc_mc = 0;
        if($session->userdata('eksklusif_voucher') != FALSE){
          if($session->userdata('eksklusif_voucher') == 1 || $session->userdata('eksklusif_voucher') == 3){
            $new_cart_promo['LIST_DISCOUNT'] = NULL;
          }
        }
        if (!empty($new_cart_promo['LIST_DISCOUNT'])) {
          $hold_total_disc_mc = $new_cart_promo['TOTAL_DISCOUNT'];
        }
        
        //Get Customer Credit from DB
        $benka_credit = $this->input->post('benka_credit');
        $hold_user_credit = $this->input->post('hold_user_credit');
        $customer_credit = abs($user_object->customer_credit);

        //Validate Customer Credit
        $temp_customer_credit = $hold_user_credit+$benka_credit;
        if($temp_customer_credit != $customer_credit){
          $notice = 'Transaction can not be processed, there was a mismatch data on your benka point.';
          $this->notice_inventory($notice);
        }
        //End Validate Customer Credit
        
        if ($subtotal != 0 || $total_value != 0 || $real_voucher_value != 0 || $hold_total_disc_mc != 0){
          if ($my_price_subtotal == 0){
            $my_price_subtotal = $subtotal;
          }
          $data_uoh['purchase_price'] = $my_price_subtotal;
          $data_uoh['discount'] = round($total_value + round($real_voucher_value)+ round($hold_total_disc_mc));
          $grand_total = $data_uoh['purchase_price'] - $data_uoh['discount'] - $hold_user_credit;

          if($grand_total < 0){
            $grand_total = 0;
          }

          $grand_total = $grand_total + $freeshipping + $paycode;
        }
        //End Calculate Grand Total
        
        // Data Pass to view sent POST data to sprint
        $clearKey = $this->config->item('clearkey_klikpay');
        $klikPayCode = $this->config->item('klikpaycode_klikpay');
        $postUrl = $this->config->item('post_url_klikpay');
        
        date_default_timezone_set('Asia/Jakarta');
        
        $currency = "IDR";
        $payType = "01";
        $klikPayCallback = "" . site_url() . "checkout/final_order";
        $transactionNo = $create_purchase_code;
        $totalAmount = $grand_total;
        $callbackUrl = $klikPayCallback;
        $transactionDateTime = date('d/m/Y H:i:s', strtotime("now"));
        $klikpay_TransactionDateTime = date('Y-m-d H:i:s', strtotime("now"));
        
        // instance of the Order_service class
        $order_service = new Order_service();
        $add_draft_order = $order_service->add_draft_order();
        
        // Setup form validation
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger" style="margin-top:5px;margin-bottom:5px;"><icon class="icon-bell"></icon> ', '</div>');
        $this->form_validation->set_rules(array(array('field' => 'signature', 'label' => 'Signature', 'rules' => 'required')));
        
        // Run form validation
        if ($this->form_validation->run() === TRUE) {
            $order_service = new Order_service();
            $get_inventory_notice = $order_service->get_inventory_notice();
            
            if (count($get_inventory_notice) != 0){
                $all_pname = NULL;
                
                foreach ($get_inventory_notice as $pname){
                    $all_pname.= '' . $pname . ',';
                }
                $rest = substr($all_pname, 0, -1);
                $notice = 'Transaction can not be processed, the quantity for ' . $rest . ' need to be lesser.';
                $this->notice_inventory($notice);
            }
        }
        
        // Communication with sprint/klikpay
        $payment_service = new Payment_service();
        $process_klikpays = $payment_service->processing_klikpays($create_purchase_code, $new_checkout);
        
        $signature = $process_klikpays['signature'];
        
        // data to view and POST DATA
        $data = array(	'postUrl' => $postUrl, 
        				'klikPayCode' => $klikPayCode, 
        				'transactionNo' => $transactionNo, 
        				'totalAmount' => $totalAmount, 
        				'currency' => $currency, 
        				'payType' => $payType, 
        				'callbackUrl' => $callbackUrl, 
        				'transactionDateTime' => $transactionDateTime, 
        				'signature' => $signature
        			);
        
        // signature and process submit order
        $session = new CI_Session();
        $x = NULL;
        $x = $x . $user_id . $customer_email;
        $add = SHARED_KEY;
        $signature = sign_data($x, $add);
        $signature_post = $this->input->post('signature');
        
        $requirement_submit_order = array(
            'shipping_address' => $address,
            'customer_shipping_address' => $address,
            'customer_province' => $area,
            'customer_city' => $name,
            'customer_postcode' => $customer_postcode,
            'customer_phone' => $customer_phone,
            'billing_address' => $bill_address,
            'shipping_method' => $shipping_method,
            'shipping_cost' => $shipping_cost,
            'shipping_cost_fg' => $shipping_cost_fg,
            'payment_method' => $payment_method,
            'hold_user_credit' => $hold_user_credit, // add from new_submit_order, author : irfan
            'total_final' => $total_final, // add from new_submit_order, author : irfan
            'benka_credit' => $benka_credit, // add from new_submit_order, author : irfan
            'payment_type_cicilan'=> $this->input->post('payment_cicilan_or_not') ? $this->input->post('payment_cicilan_or_not') : NULL
        );
        
        if ($signature_post == $signature) {
            $order_service = new Order_service();
            $session_id = $this->session->userdata('session_id');
            $op['bin_number'] = $this->session->userdata('cc_digit');
            $op['authKey'] = $process_klikpays['authKey'];
            $op['transactionDate'] = $klikpay_TransactionDateTime;
            $op['currency'] = $currency;
            $op['totalAmount'] = $totalAmount;
            //$process_submit_order = $order_service->new_process_submit_order($channel = 1, $create_purchase_code, $op);
            $process_submit_order = $order_service->process_submit_order_v2($channel = 1, $create_purchase_code, $op, $requirement_submit_order);
            // set session
            $session->set_userdata('order_id', $process_submit_order);
            $this->load->view('berrybenka/desktop/checkout_klikpay.php', $data);
        }
    }


    // --------------------------------------------------------------------
    
    
    
    /**
     * Payment Invocation - Klikpay POST data to sent notification
     *
     * @access public
     * @return array
     */
    public function payment_notification_klikpay() {
        $clearKey = $this->config->item('clearkey_klikpay');
        $klikPayCode = $this->config->item('klikpaycode_klikpay');
        $auth_library = new Auth();
        $user_id = $auth_library->get_user_id();
        
        if($this->input->post('klikPayCode')){
          $klikPayCode = ($this->input->post('klikPayCode')) ? $this->input->post('klikPayCode') : NULL ;
          $transactionDate = ($this->input->post('transactionDate')) ? $this->input->post('transactionDate') : NULL ;
          $transactionNo = ($this->input->post('transactionNo')) ? $this->input->post('transactionNo') : NULL ;
          $currency = ($this->input->post('currency')) ? $this->input->post('currency') : NULL ;
          $totalAmount = ($this->input->post('totalAmount')) ? $this->input->post('totalAmount') : NULL ;
          $payType = ($this->input->post('payType')) ? $this->input->post('payType') : NULL ;
          $authKey = ($this->input->post('authKey')) ? $this->input->post('authKey') : NULL ;
          $approvalCode = ($this->input->post('approvalCode')) ? $this->input->post('approvalCode') : NULL ;
          if($approvalCode !== NULL){
            $approvalCode_fullTransaction = (!empty($approvalCode['fullTransaction'])) ? $approvalCode['fullTransaction'] : NULL ;
            $approvalCode_installmentTransaction = (!empty($approvalCode['installmentTransaction'])) ? $approvalCode['installmentTransaction'] : NULL ;
          }
          
          // fetch data order payment
          $order_service = new Order_service();
          $where_op['purchase_code'] = $transactionNo;
          $fetch_order_payment = $order_service->fetch_order_payment($where_op);
          $trxDate = $fetch_order_payment->transactionDate;
          $klikpay_authKey = $fetch_order_payment->authKey;
          $klikpay_currency = $fetch_order_payment->currency;
          $klikpay_totalAmount = $fetch_order_payment->totalAmount;
          
          if (($klikpay_currency != $currency) || (floatval($klikpay_totalAmount) != floatval($totalAmount))) {
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction cannot be processed.", "Transaksi Anda tidak dapat diproses.");
          }

          // fetch order payment with that purchase order
          $where_ops['purchase_code'] = $transactionNo;
          $where_ops['cc_holder'] = 'auth_tested';
          $fetch_order_payments = $order_service->fetch_order_payments($where_ops);
          
          if (count($fetch_order_payments) < 1) {
            $this->db_write->where('purchase_code', $transactionNo);
            $this->db_write->update('order_payment', array('cc_holder' => 'auth_tested'));
            $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction cannot be processed.", "Transaksi Anda tidak dapat diproses.");
          }else{
            // to check counter
            $where_ops['purchase_code'] = $transactionNo;
            $where_ops['status'] = 1;
            $fetch_order_payments = $order_service->fetch_order_payments($where_ops);
            if (count($fetch_order_payments) > 0) {
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_payment', array('cc_holder' => NULL));
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction has been paid.", "Transaksi Anda telah dibayar.");
            }else{
              // update order payment payment_status with $approvalCode_fullTransaction
              // Mark order as paid
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_payment', array('status' => 1, 'payment_status' => 'success'));

              // order item
              $approval_date = date("Y-m-d H:i:s");
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_item', array('purchase_status' => 3, 'order_status_item' => 2, 'item_warehouse_status' => 0, 'approval_date' => $approval_date));

              // insert ke tabel order_item_history
              $where_oih["purchase_code"] = $transactionNo;

              $fetch_order_item = $order_service->fetch_order_items($where_oih);

              foreach ($fetch_order_item as $key => $value) {
                  $data_insert['order_item_id']     = $value->order_item_id;
                  $data_insert['SKU']               = $value->SKU;
                  $data_insert['purchase_code']     = $transactionNo;
                  $data_insert['order_status_item'] = 2;
                  $data_insert['created_by']        = $user_id;
                  $data_insert['created_date']      = date('Y-m-d H:i:s');

                  $this->db_write->insert('order_item_history', $data_insert);
              }
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "00", "Success", "Sukses");
            }
          }

          $bca = explode(" ", $transactionDate);
          $bca1 = explode("/", $bca[0]);
          $bca1 = $bca1[2] . "-" . $bca1[1] . "-" . $bca1[0];
          $bca = $bca1 . " " . $bca[1];
          $order_model = new Order_model();
          $time_trx = $order_model->time_trx($trxDate);

          // fetch time diff
          if (floatval($time_trx->bedajam / 3600) > floatval(2.000)) {
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction has been expired.", "Transaksi anda telah kedaluwarsa.");
          }
        }else{
          echo "ERR";
        }
    }


    // --------------------------------------------------------------------
    
    
    
    /**
     * Generate XML for Klikpay
     *
     * @access public
     * @return array
     */
    public function generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, $status, $reason_english, $reason_indonesian) 
    {
        header("Content-Type:text/xml");  
        //output menyesuakian dengan update dari sprintasia
        $output = ""
        . "<OutputPaymentIPAY>
            <status>".$status."</status>
            <reason>
              <english>".$reason_english."</english>
              <indonesian>".$reason_indonesian."</indonesian>
            </reason>
            <additionalData>Additional Data if any</additionalData>
          </OutputPaymentIPAY>
        ";
        
        echo $output;
        
        // Populate klikpay notification
        $data = array('klikPayCode'       => $klikPayCode, 
        				'transactionDate'         => $transactionDate, 
        				'transactionNo'           => $transactionNo, 
        				'currency'                => $currency, 
        				'totalAmount'             => $totalAmount, 
        				'payType'                 => $payType, 
        				'fullTransaction'         => $approvalCode_fullTransaction,
        				'installmentTransaction'  => $approvalCode_installmentTransaction, 
        				'status'                  => $status, 
        				'indonesian'              => $reason_indonesian, 
        				'english'                 => $reason_english, 
        				'additionalData'          => $output
        			);
        // log the notification
        $create_veritrans_notification = Order::createKlikpayNotifications($data);
        exit();
    }



	//----------------------------------------------------------------------------

	/**
   * Inquiry List of Transactions (Payment Inquiry) 
   * http://berrybenka.biz/checkout/paygate_sprint?userid=123456789&adddata=
   *
   * @access public
   * @return array
   */
  public function paymentInquiry(Request $request) 
  {

  	$userid 	= $request->get('userid');
  	$adddata 	= $request->get('adddata');

  	$data_xml = NULL;

  	if (! is_null($userid)) {
          
      $data_xml.= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
      $data_xml.= "<OutputListTransactionPGW>\n";
      $data_xml.= "<userID>" . $userid . "</userID>\n";
      $data_xml.= "<additionalData>" . $adddata . "</additionalData>\n";

  		$where_order_payment[] = ['klikbcaUserId', $userid];
  		$where_order_payment[] = ['master_payment_id', 3];
  		$where_order_payment[] = ['payment_status', 'new'];
  		$where_order_payment[] = ['status', 0];
      $fetch_order_payment = Order::fetchOrderPayment($where_order_payment);
      foreach ($fetch_order_payment as $row) {
          $bcaDate 		= NULL;
          $time_trx 		= Order::time_trx($row->transactionDate);
          $time_trx		= array_shift($time_trx);
          //var_dump(floatval($time_trx->bedajam / 3600) <= floatval(2.000));
          if (floatval($time_trx->bedajam / 3600) <= floatval(2.000)) {
              $data_xml.= "<OutputDetailPayment>\n";
              $data_xml.= "<transactionNo>" . $row->purchase_code . "</transactionNo>\n";
              
              $date = explode(" ", $row->transactionDate);
              $bcaDate = $date[1];
              $date = $date[0];
              $date = explode("-", $date);
              $bcaDate = $date[2] . "/" . $date[1] . "/" . $date[0] . " " . $bcaDate;
              $data_xml.= "<transactionDate>" . $bcaDate . "</transactionDate>\n";
              $data_xml.= "<amount>IDR" . $row->totalAmount . ".00</amount>\n";
              $data_xml.= "<description>Order ID " . $row->purchase_code . "</description>\n";
              $data_xml.= "</OutputDetailPayment>\n";
          }
      }
  	}

  	$data_xml.= "</OutputListTransactionPGW>\n";
    header("Content-Type:text/xml");
    echo $data_xml;

    // log notification
    $data = array('userid' => $userid, 'system_process_type' => 'payment_inquiry', 'adddata' => $adddata, 'bb_response' => $data_xml);
    $create_klikbca_notification = Order::createKlikbcaNotifications($data);
  }

  //----------------------------------------------------------------------------

	/**
   * Payment Flag Invocations to Merchants (Payment Confirmation)
   * http://berrybenka.biz/checkout/paygate_flag_sprint?userid=Andreawa2910&transno=4799061109600&transdate=17/06/2016%2011:20:30&amount=IDR228000.00&amount=IDR228000.00&type=N&adddata=
   *
   * @access public
   * @return array
   */
  public function paymentConfirmation(Request $request) 
  {

  	$userid 	= $request->get('userid');
  	$adddata 	= $request->get('adddata');
  	$transno 	= $request->get('transno');
  	$transdate 	= $request->get('transdate');
  	$amount 	= $request->get('amount');
  	$type 		= $request->get('type');

  	$data_xml = NULL;

  	if (! is_null($userid)) {

     	$where_order_payment[] = ['purchase_code', $transno];
      $fetch_order_payment = Order::fetchOrderPayment($where_order_payment);
      $fetch_order_payment = array_shift($fetch_order_payment);
      $time_trx 			 = Order::time_trx($fetch_order_payment->transactionDate);
      $time_trx			 = array_shift($time_trx);

      if ($fetch_order_payment->order_payment_id != NULL) {

        $amountTotal = "IDR" . $fetch_order_payment->totalAmount . ".00";

        $date 		= explode(" ", $fetch_order_payment->transactionDate);
        $bcaDate 	= $date[1];
        $date 		= $date[0];
        $date 		= explode("-", $date);
        $bcaDate 	= $date[2] . "/" . $date[1] . "/" . $date[0] . " " . $bcaDate;
        //var_dump($transdate, $bcaDate);
        if (floatval($time_trx->bedajam / 3600) > floatval(2.000)) {
            $status = "01";
            $reason = "Your transaction has been expired.";
        } elseif ($amountTotal != $amount) {
            $status = "01";
            $reason = "Transaction cannot be processed, amount not equal '.$amountTotal.' != '.$amount.'.";
        } elseif (strtoupper($fetch_order_payment->klikbcaUserId) != strtoupper($userid)) {
            $status = "01";
            $reason = "Transaction cannot be processed, user id not match.";
        } elseif ($transdate != $bcaDate) {
            $status = "01";
            $reason = "Transaction cannot be processed, date not match.";
        } elseif ($fetch_order_payment->payment_status != 'new') {
            $status = "01";
            $reason = "Transaction has been paid.";
        } else {
            $status = "00";
            $reason = "Success.";
          
          $auto_approve_klikbca = Order::autoApproveKlikbca($transno, $userid);
        }

        // insert klikbca notification
        $data = NULL;
        
        header("Content-Type:text/xml");
        $data.= "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $data.= "<OutputPaymentPGW>\n";
        $data.= "<userID>" . $userid . "</userID>\n";
        $data.= "<transactionNo>" . $transno . "</transactionNo>\n";
        $data.= "<transactionDate>" . $transdate . "</transactionDate>\n";
        $data.= "<status>" . $status . "</status>\n";
        $data.= "<reason>" . $reason . "</reason>\n";
        $data.= "<additionalData>" . $adddata . "</additionalData>\n";
        $data.= "</OutputPaymentPGW>\n";
        
        echo $data;
      }
  	}
      
    // log notification
    $data = array('userid' => $userid, 'type' => $type, 'adddata' => $adddata, 'bb_response' => $data_xml, 'system_process_type' => 'payment_confirmation');
    $create_klikbca_notification = Order::createKlikbcaNotifications($data);
  	
  } 

    public function generateUrlPaymentInquiry()
    {
  		$where_order_payment[] = ['master_payment_id', 3];
  		$where_order_payment[] = ['payment_status', 'new'];
  		$where_order_payment[] = ['status', 0];
      $fetch_order_payment = Order::fetchOrderPayment($where_order_payment);
      foreach ($fetch_order_payment as $row) {
      	$userid = $row->klikbcaUserId;
      	echo 'http://berrybenka.biz/checkout/paygate_sprint?userid='.$userid.'&adddata=';echo '<br />';
      }
    }

    public function generateUrlPaymentConfirmation($transno)
    {
    	$where_order_payment[] 	= ['purchase_code', $transno];
      $fetch_order_payment 	= Order::fetchOrderPayment($where_order_payment);
      $fetch_order_payment 	= array_shift($fetch_order_payment);
      if ($fetch_order_payment->order_payment_id != NULL) {
      	$amountTotal = $fetch_order_payment->totalAmount;
      	$userid 	 = $row->klikbcaUserId;
      	$date 		 = explode(" ", $fetch_order_payment->transactionDate);
          $bcaDate 	 = $date[1];
          $date 		 = $date[0];
          $date 		 = explode("-", $date);
          $bcaDate 	 = $date[2] . "/" . $date[1] . "/" . $date[0] . " " . $bcaDate;

          echo 'http://berrybenka.biz/checkout/paygate_flag_sprint?userid='.$userid.'&transno='.$transno.'&transdate='.$bcaDate.'&amount=IDR'.$amountTotal.'.00&type=N&adddata=';
      }
    }

    public function redirectForwardBcaKlikPay()
    {
        // Data Pass to view sent POST data to sprint
        $clearKey     = env('CLEARKEY_KLIKPAY');
        $klikPayCode  = env('KLIKPAYCODE_KLIKPAY');
        $postUrl      = env('POST_URL_KLIKPAY');
        
        $currency                     = "IDR";
        $payType                      = "01";
        $klikPayCallback              = URL::to('/checkout/final_order');
        $transactionNo                = $create_purchase_code;
        $totalAmount                  = $grand_total;
        $callbackUrl                  = $klikPayCallback;
        $transactionDateTime          = date('d/m/Y H:i:s', strtotime("now"));
        $klikpay_TransactionDateTime  = date('Y-m-d H:i:s', strtotime("now"));

        // Communication with sprint/klikpay
        $process_klikpays = Sprintasia::processing_klikpays($create_purchase_code, $new_checkout);
        
        $signature = $process_klikpays['signature'];
        
        // Data to view and POST DATA
        $data = array(  'postUrl' => $postUrl, 
                'klikPayCode'     => $klikPayCode, 
                'transactionNo' => $transactionNo, 
                'totalAmount' => $totalAmount, 
                'currency' => $currency, 
                'payType' => $payType, 
                'callbackUrl' => $callbackUrl, 
                'transactionDateTime' => $transactionDateTime, 
                'signature' => $signature
              );

        return get_view('checkout', 'checkout.redirect-forward-klikpay', $data);
    }

    public function paymentAcknowledgeBcaKlikPay()
    {
      $clearKey = $this->config->item('clearkey_klikpay');
        $klikPayCode = $this->config->item('klikpaycode_klikpay');
        $auth_library = new Auth();
        $user_id = $auth_library->get_user_id();
        
        if($this->input->post('klikPayCode')){
          $klikPayCode = ($this->input->post('klikPayCode')) ? $this->input->post('klikPayCode') : NULL ;
          $transactionDate = ($this->input->post('transactionDate')) ? $this->input->post('transactionDate') : NULL ;
          $transactionNo = ($this->input->post('transactionNo')) ? $this->input->post('transactionNo') : NULL ;
          $currency = ($this->input->post('currency')) ? $this->input->post('currency') : NULL ;
          $totalAmount = ($this->input->post('totalAmount')) ? $this->input->post('totalAmount') : NULL ;
          $payType = ($this->input->post('payType')) ? $this->input->post('payType') : NULL ;
          $authKey = ($this->input->post('authKey')) ? $this->input->post('authKey') : NULL ;
          $approvalCode = ($this->input->post('approvalCode')) ? $this->input->post('approvalCode') : NULL ;
          if($approvalCode !== NULL){
            $approvalCode_fullTransaction = (!empty($approvalCode['fullTransaction'])) ? $approvalCode['fullTransaction'] : NULL ;
            $approvalCode_installmentTransaction = (!empty($approvalCode['installmentTransaction'])) ? $approvalCode['installmentTransaction'] : NULL ;
          }
          
          // fetch data order payment
          $order_service = new Order_service();
          $where_op['purchase_code'] = $transactionNo;
          $fetch_order_payment = $order_service->fetch_order_payment($where_op);
          $trxDate = $fetch_order_payment->transactionDate;
          $klikpay_authKey = $fetch_order_payment->authKey;
          $klikpay_currency = $fetch_order_payment->currency;
          $klikpay_totalAmount = $fetch_order_payment->totalAmount;
          
          if (($klikpay_currency != $currency) || (floatval($klikpay_totalAmount) != floatval($totalAmount))) {
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction cannot be processed.", "Transaksi Anda tidak dapat diproses.");
          }

          $auto_approve_klikbca = Order::autoApproveKlikbca($transno, $userid);

          // fetch order payment with that purchase order
          $where_ops['purchase_code'] = $transactionNo;
          $where_ops['cc_holder'] = 'auth_tested';
          $fetch_order_payments = $order_service->fetch_order_payments($where_ops);
          
          if (count($fetch_order_payments) < 1) {
            $this->db_write->where('purchase_code', $transactionNo);
            $this->db_write->update('order_payment', array('cc_holder' => 'auth_tested'));
            $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction cannot be processed.", "Transaksi Anda tidak dapat diproses.");
          }else{
            // to check counter
            $where_ops['purchase_code'] = $transactionNo;
            $where_ops['status'] = 1;
            $fetch_order_payments = $order_service->fetch_order_payments($where_ops);
            if (count($fetch_order_payments) > 0) {
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_payment', array('cc_holder' => NULL));
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction has been paid.", "Transaksi Anda telah dibayar.");
            }else{
              // update order payment payment_status with $approvalCode_fullTransaction
              // Mark order as paid
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_payment', array('status' => 1, 'payment_status' => 'success'));

              // order item
              $approval_date = date("Y-m-d H:i:s");
              $this->db_write->where('purchase_code', $transactionNo);
              $this->db_write->update('order_item', array('purchase_status' => 3, 'order_status_item' => 2, 'item_warehouse_status' => 0, 'approval_date' => $approval_date));

              // insert ke tabel order_item_history
              $where_oih["purchase_code"] = $transactionNo;

              $fetch_order_item = $order_service->fetch_order_items($where_oih);

              foreach ($fetch_order_item as $key => $value) {
                  $data_insert['order_item_id']     = $value->order_item_id;
                  $data_insert['SKU']               = $value->SKU;
                  $data_insert['purchase_code']     = $transactionNo;
                  $data_insert['order_status_item'] = 2;
                  $data_insert['created_by']        = $user_id;
                  $data_insert['created_date']      = date('Y-m-d H:i:s');

                  $this->db_write->insert('order_item_history', $data_insert);
              }
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "00", "Success", "Sukses");
            }
          }

          $bca = explode(" ", $transactionDate);
          $bca1 = explode("/", $bca[0]);
          $bca1 = $bca1[2] . "-" . $bca1[1] . "-" . $bca1[0];
          $bca = $bca1 . " " . $bca[1];
          $order_model = new Order_model();
          $time_trx = $order_model->time_trx($trxDate);

          // fetch time diff
          if (floatval($time_trx->bedajam / 3600) > floatval(2.000)) {
              $this->generateXML($klikPayCode, $transactionNo, $transactionDate, $currency, $totalAmount, $payType, $approvalCode_fullTransaction, $approvalCode_installmentTransaction, "01", "Your transaction has been expired.", "Transaksi anda telah kedaluwarsa.");
          }
        }else{
          echo "ERR";
        }
    }
}