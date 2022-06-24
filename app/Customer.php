<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use DB;
// use Mail;
use App\Libraries\Mail;
 
class Customer extends Authenticatable
{
  protected $table = 'customer';
  protected $primaryKey = 'customer_id';
  protected $guarded = ['customer_id'];
  public $timestamps = false;
  

  public function getAuthPassword()
  {
    	return $this->customer_password;
	}

	public function getRememberToken()
	{
		return null; // not supported //boan test
	}

	public function setRememberToken($value)
	{
		// not supported
	}

	public function getRememberTokenName()
	{
		return null; // not supported
  }

	/**
	* Overrides the method to ignore the remember token.
	*/
	public function setAttribute($key, $value)
	{
		$isRememberTokenAttribute = $key == $this->getRememberTokenName();

		if (!$isRememberTokenAttribute)
		{
			parent::setAttribute($key, $value);
		}
	}

  public static function getCustomerGender(){
    if(empty(Auth::user())){
      return array();
    }

    $customer_id = Auth::user()->customer_id;

    $get_customer_gender = DB::table('customer')
      ->select(DB::connection('read_mysql')->raw('customer_gender'))
      ->where('customer_id', '=', $customer_id)
      ->take(1)
      ->value('customer_gender');

    return $get_customer_gender;
  }

  public static function getCustomerAddress($params){
    if(empty(Auth::user())){
      return array();
    }

    $customer_id = Auth::user()->customer_id;
    $address_id = (isset($params['address_id'])) ? $params['address_id'] : FALSE ;
    $get_primary = (isset($params['get_primary'])) ? $params['get_primary'] : FALSE ; //Check wether the request wants primary or not
    $address_type = (isset($params['address_type'])) ? $params['address_type'] : FALSE ; //Address Type 1: shipping | 2: billing

    $get_customer_address = DB::table('customer_address')
      ->select(DB::connection('read_mysql')->raw('*'))
      ->where('status', '=', 1)
      ->where('customer_id', '=', $customer_id);

    if($address_id){
      $get_customer_address->where('address_id', '=', $address_id);
    }

    if($get_primary){
      $get_customer_address->where('is_primary', '=', 1);
    }

    if($address_type){
      $get_customer_address->where('address_type', '=', $address_type);
    }

    return $get_customer_address->get();
  }

  public static function setPrimaryAddress($params)
  {
    if(empty(Auth::user())){
      return false;
    }

    $customer_id = Auth::user()->customer_id;
    $address_id = (isset($params['address_id'])) ? $params['address_id'] : NULL ;
    $address_type = (isset($params['address_type'])) ? $params['address_type'] : NULL ; //Address Type 1: shipping | 2: billing
    
    $param_validate['customer_id']  = $customer_id;
    $param_validate['address_id']   = $address_id;
    if(count(Customer::getCustomerAddress($param_validate)) > 0){
      if($address_id !== NULL && $address_type !== NULL){
        DB::table('customer_address')
          ->where('customer_id', $customer_id)
          ->where('address_type', $address_type)
          ->update([
            'is_primary' => 0
          ]);

        DB::table('customer_address')
          ->where('address_id', $address_id)
          ->where('address_type', $address_type)
          ->update([
            'is_primary' => 1
          ]);

        return true;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  public static function newCustomerAddress($params)
  {
    if(empty(Auth::user())){
      return false;
    }

    $customer_id = Auth::user()->customer_id;

    //Shipping
    $shipping_street = (isset($params['shipping_street'])) ? $params['shipping_street'] : FALSE ;
    $shipping_province = (isset($params['shipping_province'])) ? $params['shipping_province'] : FALSE ;
    $shipping_city = (isset($params['shipping_city'])) ? $params['shipping_city'] : FALSE ;
    $shipping_postcode = (isset($params['shipping_postcode'])) ? $params['shipping_postcode'] : FALSE ;
    $shipping_phone = (isset($params['shipping_phone'])) ? $params['shipping_phone'] : FALSE ;

    //Billing
    $billing_street = (isset($params['billing_street'])) ? $params['billing_street'] : FALSE ;
    $billing_province = (isset($params['billing_province'])) ? $params['billing_province'] : FALSE ;
    $billing_city = (isset($params['billing_city'])) ? $params['billing_city'] : FALSE ;
    $billing_postcode = (isset($params['billing_postcode'])) ? $params['billing_postcode'] : FALSE ;
    $billing_phone = (isset($params['billing_phone'])) ? $params['billing_phone'] : FALSE ;

    $result = true;
    $result_message = '';

    if(!$shipping_street){
      $result = false;
      $result_message .= 'Alamat Pengiriman harus diisi. <br /> <br/>';
    }
    if(!$shipping_province){
      $result = false;
      $result_message .= 'Provinsi Pengiriman harus dipilih. <br /> <br/>';
    }
    if(!$shipping_city){
      $result = false;
      $result_message .= 'Kota Pengiriman harus dipilih. <br /> <br/>';
    }
    if(!$shipping_postcode){
      $result = false;
      $result_message .= 'Kode Pos Pengiriman harus diisi. <br /> <br/>';
    }else if(!is_numeric($shipping_postcode)){
      $result = false;
      $result_message .= 'Kode Pos Pengiriman harus berupa angka. <br /> <br/>';
    }else if(strlen($shipping_postcode) != 5){
      $result = false;
      $result_message .= 'Kode Pos tidak boleh lebih atau kurang dari 5 karakter. <br /> <br/>';
    }
    if(!$shipping_phone){
      $result = false;
      $result_message .= 'Telpon Pengiriman harus diisi. <br /> <br/>';
    }else if(!is_numeric($shipping_phone)){
      $result = false;
      $result_message .= 'Telpon Pengiriman harus berupa angka. <br /> <br/>';
    }

    if(!$billing_street){
      $result = false;
      $result_message .= 'Alamat Penagihan harus diisi. <br /> <br/>';
    }
    if(!$billing_province){
      $result = false;
      $result_message .= 'Provinsi Penagihan harus dipilih. <br /> <br/>';
    }
    if(!$billing_city){
      $result = false;
      $result_message .= 'Kota Penagihan harus dipilih. <br /> <br/>';
    }
    if(!$billing_postcode){
      $result = false;
      $result_message .= 'Kode Pos Penagihan harus diisi. <br /> <br/>';
    }else if(!is_numeric($billing_postcode)){
      $result = false;
      $result_message .= 'Kode Pos Penagihan harus berupa angka. <br /> <br/>';
    }else if(strlen($billing_postcode) != 5){
      $result = false;
      $result_message .= 'Kode Pos tidak boleh lebih atau kurang dari 5 karakter. <br /> <br/>';
    }
    if(!$billing_phone){
      $result = false;
      $result_message .= 'Telpon Penagihan harus diisi. <br /> <br/>';
    }else if(!is_numeric($billing_phone)){
      $result = false;
      $result_message .= 'Telpon Penagihan harus berupa angka. <br /> <br/>';
    }

    if($result){
      DB::table('customer_address')
        ->insert([
          'customer_id' => $customer_id,
          'address_type' => 1,
          'address_street' => $shipping_street,
          'address_province' => $shipping_province,
          'address_city' => $shipping_city,
          'address_postcode' => $shipping_postcode,
          'address_phone' => $shipping_phone,
          'is_primary' => 1
        ]);
      DB::table('customer_address')
        ->insert([
          'customer_id' => $customer_id,
          'address_type' => 2,
          'address_street' => $billing_street,
          'address_province' => $billing_province,
          'address_city' => $billing_city,
          'address_postcode' => $billing_postcode,
          'address_phone' => $billing_phone,
          'is_primary' => 1
        ]);
    }

    $data['result'] = $result;
    $data['result_message'] = $result_message;

    return $data;
  }

  public static function addCustomerAddress($params)
  {
    if(empty(Auth::user())){
      return false;
    }

    $customer_id = Auth::user()->customer_id;
    $address_type = (isset($params['address_type'])) ? $params['address_type'] : FALSE ; //1: Shipping | 2: Billing
    $address_street = (isset($params['address_street'])) ? $params['address_street'] : FALSE ;
    $address_province = (isset($params['address_province'])) ? $params['address_province'] : FALSE ;
    $address_city = (isset($params['address_city'])) ? $params['address_city'] : FALSE ;
    $address_postcode = (isset($params['address_postcode'])) ? $params['address_postcode'] : FALSE ;
    $address_phone = (isset($params['address_phone'])) ? $params['address_phone'] : FALSE ;
    $result = true;
    $result_message = '';

    if(!$address_street){
      $result = false;
      $result_message .= 'Alamat harus diisi. <br /> <br/>';
    }
    if(!$address_province){
      $result = false;
      $result_message .= 'Provinsi harus dipilih. <br /> <br/>';
    }
    if(!$address_city){
      $result = false;
      $result_message .= 'Kota harus dipilih. <br /> <br/>';
    }
    if(!$address_postcode){
      $result = false;
      $result_message .= 'Kode Pos harus diisi. <br /> <br/>';
    }else if(!is_numeric($address_postcode)){
      $result = false;
      $result_message .= 'Kode Pos harus berupa angka. <br /> <br/>';
    }else if(strlen($address_postcode) != 5){
      $result = false;
      $result_message .= 'Kode Pos tidak boleh lebih atau kurang dari 5 karakter. <br /> <br/>';
    }
    if(!$address_phone){
      $result = false;
      $result_message .= 'Telpon harus diisi. <br /> <br/>';
    }else if(!is_numeric($address_phone)){
      $result = false;
      $result_message .= 'Telpon harus berupa angka. <br /> <br/>';
    }

    if($result){
      $id = DB::table('customer_address')
        ->insertGetId([
          'customer_id' => $customer_id,
          'address_type' => $address_type,
          'address_street' => $address_street,
          'address_province' => $address_province,
          'address_city' => $address_city,
          'address_postcode' => $address_postcode,
          'address_phone' => $address_phone,
        ]);
    }

    $data['id'] = (isset($id)) ? $id : NULL ;
    $data['result'] = $result;
    $data['result_message'] = $result_message;

    return $data;
  }

  public static function editCustomerAddress($params)
  {
    if(empty(Auth::user())){
      return false;
    }

    $customer_id = Auth::user()->customer_id;
    $address_id = (isset($params['address_id'])) ? $params['address_id'] : FALSE ;
    $address_street = (isset($params['address_street'])) ? $params['address_street'] : FALSE ;
    $address_province = (isset($params['address_province'])) ? $params['address_province'] : FALSE ;
    $address_city = (isset($params['address_city'])) ? $params['address_city'] : FALSE ;
    $address_postcode = (isset($params['address_postcode'])) ? $params['address_postcode'] : FALSE ;
    $address_phone = (isset($params['address_phone'])) ? $params['address_phone'] : FALSE ;
    $result = true;
    $result_message = '';

    //Validate customer id & address id
    $param_validate['customer_id'] = $customer_id;
    $param_validate['address_id'] = $address_id;
    if(count(Customer::getCustomerAddress($param_validate)) > 0){
      if(!$address_street){
        $result = false;
        $result_message .= 'Alamat harus diisi. <br /> <br/>';
      }
      if(!$address_province){
        $result = false;
        $result_message .= 'Provinsi harus dipilih. <br /> <br/>';
      }
      if(!$address_city){
        $result = false;
        $result_message .= 'Kota harus dipilih. <br /> <br/>';
      }
      if(!$address_postcode){
        $result = false;
        $result_message .= 'Kode Pos harus diisi. <br /> <br/>';
      }else if(!is_numeric($address_postcode)){
        $result = false;
        $result_message .= 'Kode Pos harus berupa angka. <br /> <br/>';
      }else if(strlen($address_postcode) != 5){
        $result = false;
        $result_message .= 'Kode Pos tidak boleh lebih atau kurang dari 5 karakter. <br /> <br/>';
      }
      if(!$address_phone){
        $result = false;
        $result_message .= 'Telpon harus diisi. <br /> <br/>';
      }else if(!is_numeric($address_phone)){
        $result = false;
        $result_message .= 'Telpon harus berupa angka. <br /> <br/>';
      }

      if($result){
        DB::table('customer_address')
          ->where('address_id', $address_id)
          ->update([
            'address_street' => $address_street,
            'address_province' => $address_province,
            'address_city' => $address_city,
            'address_postcode' => $address_postcode,
            'address_phone' => $address_phone,
          ]);
      }
    }else{
      $result = false;
      $result_message = 'Address tidak ditemukan untuk akun ini.';
    }

    $data['result'] = $result;
    $data['result_message'] = $result_message;

    return $data;
  }

  public static function validateAccessToken()
  {
    if (!\Auth::check()) {
      return false;
    }
    
    $get_domain = get_domain();
    $domain_id 	= $get_domain['domain_id'];
    
    $user         = \Auth::user();
    $object_user  = Customer::where('customer_id','=',$user->customer_id)->first(); 
    
    $object_token = $object_user->bb_access_token;
    if($domain_id == 2){
      $object_token = $object_user->hb_access_token;
    }else if($domain_id == 3){
      $object_token = $object_user->sd_access_token;
    }
    
    $session_token  = \Session::get('bb_access_token');
    if($domain_id == 2){
      $session_token = \Session::get('hb_access_token');
    }else if($domain_id == 3){
      $session_token = \Session::get('sd_access_token');
    }
    
    if($object_token != $session_token){
      return false;
    }

    return true;
  }

  public static function validateCustomerStatus()
  {
    if (!\Auth::check()) {
      return false;
    }

    $get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
    
    $user         = \Auth::user();
    $object_user  = Customer::where('customer_id','=',$user->customer_id)->first();

    if ($object_user->customer_status != 1) {
      return false;
    }

    return true;
  }

	public static function send_mail_cs($params = array()) {

		$message = NULL;

		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
                $domain_name    = $get_domain['domain_name'];

		$params['domain'] = $domain;

		/*

		Mail::send('mailtemplates.mailtemplates', ['params' => $params], function ($message) use ($params) {
			$message->from('cs@berrybenka.com', strtoupper($params['domain_name']));
			$message->replyTo('cs@berrybenka.com', strtoupper($params['domain_name']));
			$message->to($params['email']);
			$message->subject($params['mail_subject']);
		});
		*/
		// $mail_headers = "MIME-Version: 1.0" . "\r\n";
		// $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// $mail_headers .= 'From: '.strtoupper($params['domain_name']) .' <cs@berrybenka.com>' . "\r\n";
		// $mail_headers .= 'Cc: cs@berrybenka.com' . "\r\n";
		$message 	= response()->view('mailtemplates.'.$domain_name.'.mailtemplates', $params)->content();

    $body = array(
      "personalizations"=>array(
        array("recipient"=>$params['email'])
      ),
      "from"=>array(
        "fromEmail"=>"cs@berrybenka.com",
        "fromName"=>strtoupper($params['domain_name'])
      ),
      "subject"=>$params['mail_subject'],
      "content"=>$message
    );

    $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
    $Mail->SendMail($body);

		// $sendmail 	= mail($params['email'],$params['mail_subject'],$message,$mail_headers);
	}

	public static function send_mail($params = array()) {
		$message = NULL;

		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
                $domain_name    = $get_domain['domain_name'];

		$params['domain'] = $domain;

		/*
		Mail::send('mailtemplates.mailtemplates', ['params' => $params], function ($message) use ($params) {
			$message->from('cs@berrybenka.com', strtoupper($params['domain_name']));
			$message->to($params['email']);
			$message->subject($params['mail_subject']);
		});

		*/
		// $mail_headers = "MIME-Version: 1.0" . "\r\n";
		// $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		// $mail_headers .= 'From: '.strtoupper($params['domain_name']) .' <cs@berrybenka.com>' . "\r\n";
		$message 	= response()->view('mailtemplates.'.$domain_name.'.mailtemplates', $params)->content();

		// $sendmail 	= mail($params['email'],$params['mail_subject'],$message,$mail_headers,"-f cs@berrybenka.com");

    $body = array(
      "personalizations"=>array(
        array("recipient"=>$params['email'])
      ),
      "from"=>array(
        "fromEmail"=>"cs@berrybenka.com",
        "fromName"=>strtoupper($params['domain_name'])
      ),
      "subject"=>$params['mail_subject'],
      "content"=>$message
    );

    $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
    $Mail->SendMail($body);
	}

	public static function MN_send_forgot_product_default($params) {

		$params['mail_message'] = "
			Dear <b>Admin</b>,<br/>
			<br/>
			We have received a error request because the default product is not set.<br/>
			<br/>
			Mind to resolve this error from link:
			<b>" . $params['url'] . "</b><br/>
			<br/>
			Referer: " . $params['referer'] . "
			<br/>
			Thank you.
			";

		$params['mail_subject'] = "Error Request Default Product Is Not Set";

		$msg = Self::send_mail($params);

	}

	public static function MN_send_forgot_password($params) {
		$params['mail_message'] = "
			Dear <b>" . ucfirst($params['customer_fname']) . " " . ucfirst($params['customer_lname']) . "</b>,<br/>
			<br/>
			We have received a request to reset your password.<br/>
			<br/>
			To reset your password, please click on the link below or copy and paste the URL into your browser:<br/>
			<b>" . $params['reset_url'] . "</b><br/>
			<br/>";

		$params['mail_subject'] = "Password Reminder ".ucfirst($params['domain_name'])." ";
		$msg = Self::send_mail($params);
	}

  public static function MN_send_subscribe_success($params) {
    $get_domain = get_domain();
    $channel    = $get_domain['channel'];
    $domain     = ucwords($get_domain['domain']);
    $domain_id  = $get_domain['domain_id'];
    $params['domain_name'] = $get_domain['domain_name'];

    $params['mail_message'] = "
	          Pelanggan yang terhormat,<br>
	            <br>
	            Terima kasih telah mendaftarkan email Anda pada ".$domain." newsletter. <br>
	            <br>Sebagai wujud terima kasih, Anda akan mendapatkan voucher promo senilai ".$params['promo_value']." yang berlaku sampai dengan " . date("j F Y", $params['promo_expiry']) . " dengan minimum pembelian sebesar Rp. ". number_format($params['promo_minvalue'],0,".",".") .",-<br>
	            <br>
	            Untuk menggunakan promo ini, kunjungi <a href=\"http://".$get_domain['domain']."/\">".$domain."</a> dan masukan kode voucher : " . $params['promocode'] . " pada halaman pembayaran di website kami.<br>
	            <br>  
	            Selamat berbelanja!<br>
	            <br>
	            <br>
	            Salam,<br>
	            <a href=\"http://".$get_domain['domain']."/\">".$domain."</a><br>
	            <br>";
	  $params['mail_subject'] = "Terima kasih telah berlangganan ".$domain." newsletter";
    
    $msg = Self::send_mail($params);
    
    return $msg;
  }

  public static function check_benka_stamp($id,$date){
    $get_customer_benka = DB::table('benka_stamp_history')
      ->select('id')
      ->where('customer_id', '=', $id)
      ->where('history_create_date', '>' , $date)
      ->get();

    return $get_customer_benka;
  }


}
