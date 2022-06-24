<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use \App\Modules\Checkout\Models\Order;
use Log;
use \App\Modules\Checkout\Models\Veritrans;

class OrderProcess extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    /*
    //KlikBCA
    $data_submit_order['klikbca_user_id'] = $request->get('klikbca-user-id');
    
    //Veritrans
    $data_submit_order['token_id']  = $request->get('token-id');
    $data_submit_order['cc_holder'] = $request->get('cc-holder');
    //End Veritrans
     *      */
    protected $data_submit_order;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data_submit_order)
    {
      $this->data_submit_order = $data_submit_order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $customer_email = (isset($this->data_submit_order['customer_email'])) ? $this->data_submit_order['customer_email'] : NULL ;
      
      //Check whether order process still exist or not
      $data['customer_email'] = $customer_email;
      $check_order_process    = Order::fetchOrderProcess($data);
      //End Check
      
      if($check_order_process){
        $submit_order = Order::processSubmitOrder($this->data_submit_order);

        if(!$submit_order || !$submit_order['result']){
          
          if(isset($submit_order['false_qty']) && $submit_order['false_qty']){
            //Redirect to Cart if one of order item is out of stock
            $data = array();
            $data['status']         = 3;
            $data['status_message'] = $submit_order['err_msg'];
          }else{
            $data = array();
            $data['status']         = 2;
            $data['status_message'] = $submit_order['err_msg'];
          }
          
        }else{
          $data = array();
          $data['status']                 = 1;
          $data['purchase_order']         = $submit_order['create_order_header']['purchase_code'];
          $data['payment_method']         = $submit_order['payment_method'];
          $data['veritrans_payment_code'] = (isset($submit_order['veritrans']['payment_code'])) ? $submit_order['veritrans']['payment_code'] : NULL ;
          $data['veritrans_redirect_url'] = (isset($submit_order['veritrans']['redirect_url'])) ? $submit_order['veritrans']['redirect_url'] : NULL ;
        }
        
        //Veritrans Process
        $veritrans              = isset($submit_order['veritrans']) ? $submit_order['veritrans'] : NULL ;
        $veritrans_result_code  = isset($veritrans['code']) ? $veritrans['code'] : NULL ;
        $log_vn                 = isset($veritrans['log_vn']) ? $veritrans['log_vn'] : array() ;
        
        if($veritrans != NULL){
          $veritrans_order_id = (isset($veritrans['order_id'])) ? $veritrans['order_id'] : NULL;
          
          if($veritrans_result_code == 400){ //Change bank acquire if result 400
            Payment::updateBankAcquire($this->data_submit_order);
            if($veritrans_order_id != NULL){
              Log::notice('Process Cancel Veritrans caused by 400');
              
              $veritrans_data['domain_id']    = isset($data['domain_id']) ? $data['domain_id'] : NULL ;
              $veritrans_data['veritrans_id'] = $veritrans_order_id;
              Veritrans::cancelVeritrans($veritrans_data);
            }
          }else if($veritrans_result_code != 200 && $veritrans_result_code != 201 && $veritrans_result_code != 202){
            if($veritrans_order_id != NULL){
              Log::notice('Process Cancel Veritrans caused by else 200 / 201');
              
              $veritrans_data['domain_id']    = isset($data['domain_id']) ? $data['domain_id'] : NULL ;
              $veritrans_data['veritrans_id'] = $veritrans_order_id;
              Veritrans::cancelVeritrans($veritrans_data);
            }
          }

          if(!empty($log_vn)){ //Insert veritrans notification
            Order::createVeritransNotifications($log_vn);
          }
        }
        //End Veritrans Process

        Order::setOrderProcess($customer_email, $data);
      }
    }
}
