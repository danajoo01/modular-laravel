<?php namespace App\Modules\Account\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Account\Models\Account;
use \App\Modules\Checkout\Models\OrderItem;
use \App\Customer;

use Input;
use Validator;
use Auth;

use Illuminate\Http\Request;

use App\Libraries\Mail;

class AccountPasswordController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function resetPassword() {
		date_default_timezone_set('Asia/Jakarta');		

		// if (!Auth::check()) {
  //           return redirect('login/?continue='.urlencode('/user/account_dashboard'));
  //       }
		
		$userid = \Request::get('id');
		$token = \Request::get('token');
		$now = date('Y-m-d H:i:s');
		
		$account = Customer::where('customer_id','=',$userid)->first();
		
		if ($account) {
      $resetpassword_config = config('berrybenka.reset_password');
      
			// Check if reset password has expired
			if ($now < (strtotime($account->resetsenton) + $resetpassword_config["password_reset_expiration"])) {
				// Check if token is valid
				if ($token == sha1($account->customer_id . $account->resetsenton . $resetpassword_config["password_reset_secret"])) {
					// Remove reset sent on datetime
					$account->resetsenton = NULL;
					$account->save();
					
					if (Auth::loginUsingId($userid)) 
					{
						//Sync Order Item to Cart
						OrderItem::syncOrderItem();
						//End Sync Order Item to Cart
						return redirect('/user/change_password');
					} 
				} else {
					$notice = "Token tidak valid.";
				}
			} else {
				$notice = "Maaf, halaman penggantian password anda telah kadaluarsa";
			}
		} else {
			$notice = "Maaf, account tidak terdaftar.";
		}
		
		//Insert your code for showing an error message here        
        \Session::flash('error_message', $notice);
		return redirect('/forgot_password');
	}
	
	/**
	 * Change Password
	 *
	 * @return Response
	 */
	public function changePassword() {
		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
		
		$user = Auth::user();
		
		if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/change_password'));
        }
		
		$data['user'] = $user;
		
		return get_view('account', 'account.changepassword', $data);
	}
	
	/**
     * Set Session Check Email and redirect - Submit Order
     *
     * @access public
     * @return void
    */
    private function errorForgotPassword($notice) {
		echo 'notice';
		\Session::flash('error_message', $notice);
		return redirect('/forgot_password');
    }
	
	
	/** Forgot Password 
	
	**/
	public function forgotPassword() {
            $get_domain = get_domain();
            $channel 	= $get_domain['channel'];
            $domain 	= $get_domain['domain'];
            $domain_id 	= $get_domain['domain_id'];

            $user = Auth::user();

            if ($user) {
                    return redirect('login/?continue='.urlencode('/user/change_password'));
            } 

            $data['user'] = $user;
            $data['domain_name'] = $get_domain['domain_name'];

            switch ($get_domain['channel']) {
                case 2:
                    $data['logo_path'] = asset('berrybenka/mobile/img/circleb.gif');
                    $data['title_path'] = asset('berrybenka/mobile/img/bb-logo.gif');
                    break;
                case 4:
                    $data['logo_path'] = asset('hijabenka/mobile/img/circleh.gif');
                    $data['title_path'] = asset('hijabenka/mobile/img/logo.gif');
                    break;
                case 6:
                    $data['logo_path'] = asset('shopdeca/mobile/img/shopdeca-logo.png');
                    $data['title_path'] = asset('shopdeca/mobile/img/shopdeca.png');
                    break;
            }

        return get_view('account', 'account.forgotpassword', $data);
	}
	
	/**
     * Get a validator for Password.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function passwordValidator(array $data)
    {
        return Validator::make($data, [
            'password' => 'required|confirmed|min:6',
        ]);
    }
	
	/** Update Password
	** @param request $_POST
	** @return response.
	**/
  public function updatePassword(Request $request) {
    // 
    if (!Auth::check()) {
      return redirect('login/?continue=' . urlencode('/user/change_password'));
    }
    $user = Auth::user();
    $validation = $this->passwordValidator($request->all());

    if ($validation->passes()) {
      $newpassword = $request->get('password');

      $update = Customer::where('customer_id', '=', $user->customer_id)->first();
      $update->update(['customer_password' => bcrypt($newpassword)]);

      if ($update == TRUE) {
        //Send Email to On-Site Team if auth_cs change the password
        $onsite_emails = array(
          "bismar@berrybenka.com"
        );
        
        $get_domain     = get_domain();
        $domain_name    = $get_domain['domain_name'];
        $email_auth_cs  = (session()->has('email_auth_cs')) ? session('email_auth_cs') : NULL;

        if($email_auth_cs != NULL){
          $mail_headers = "MIME-Version: 1.0" . "\r\n";
          $mail_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
          $mail_headers .= 'From: '.strtoupper($domain_name) .' <cs@berrybenka.com>' . "\r\n";
          
          $mail_subject = '[Auth CS] Changed Email Activity';
          
          $message      = 'Dear Onsite Team, <br> <br> Customer Service with account <strong>' . $email_auth_cs . '</strong> has changed <strong>' . $user->customer_email . '</strong> password on ' . date('Y-m-d H:i:s') . ' ';
          
          \Log::alert('[Auth CS] ' . $message);
          
          foreach ($onsite_emails as $email) {
            $body = array(
              "personalizations"=>array(
                array("recipient"=>$email)
              ),
              "from"=>array(
                "fromEmail"=>"bismar@berrybenka.com",
                "fromName"=>strtoupper($domain['domain_name'])
              ),
              "subject"=>$mail_subject,
              "content"=>$message
            );
        
            $Mail = new Mail("https://api.pepipost.com/v2/sendEmail", env('PEPIPOST_KEY'));
            $Mail->SendMail($body);
            // $sendmail 	= mail($email, $mail_subject, $message, $mail_headers);
          }
        }
        /*End Send*/
        
        
        $request->session()->flash('message', 'Update password sukses');
        return redirect('/user/change_password');
      }
    } else {
      return redirect('/user/change_password')->withError($validation->errors())->withInput();
    }
  }

    /**
     * Get a validator for Email.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function emailValidator(array $data)
    {
        return Validator::make($data, [
            'customer_email' => 'required|email|max:255',
        ]);
    }
	
	/** Send Confirmation Forgot Password
	** @param request $_POST['customer_email']
	** @return response.
	**/
	public function forgotPasswordPost(Request $request) {
		date_default_timezone_set('Asia/Jakarta');
		
		$get_domain = get_domain();
    $domain_id  = $get_domain['domain_id'];
		
		$user = Auth::user();		
    
		if ($user) {
			return redirect('/user/account_dashboard');
		}
		
		$validation = $this->emailValidator($request->all());
    if($validation->passes()){
			$email                = $request->get('customer_email');
			$resetpassword_config = config('berrybenka.reset_password');
			$customer             = Customer::where('customer_email','=',$email)->first();
			
			if ($customer) {	
        if($domain_id != 3 && $customer->customer_status != 1){          
          $request->session()->flash('message', 'Akun anda tidak aktif');
					return redirect('/forgot_password');
        }
        
				$customer->resetsenton = date('Y-m-d H:i:s');
				if ($customer->save()) {
					// Generate reset password url  
					$password_reset_url = \App::make('url')->to('user/reset_password?id=' . $customer->customer_id . '&token=' . sha1($customer->customer_id . $customer->resetsenton . $resetpassword_config["password_reset_secret"]));
                
					$params['email']              = $email;
          $params['reset_url']          = $password_reset_url;
          $params['domain_name']        = $get_domain['domain_name'];
          $params['customer_fname']     = $customer->customer_fname;
          $params['customer_email']     = $customer->customer_email;
          $params['customer_lname']     = $customer->customer_lname;
          $params['customer_password']  = $customer->customer_password;
          
					$sendMail = Customer::MN_send_forgot_password($params);
					
					$request->session()->flash('message', 'Silahkan periksa email anda untuk mengganti password');
					return redirect('/forgot_password');
				}        
			} else {
				$request->session()->flash('error_message', 'Email tidak terdaftar');
				return redirect('/forgot_password');
			}
    }else{  
			$request->session()->flash('error_message', 'Email must be a valid email address.');
      return redirect('/forgot_password');
    }
	}

}
