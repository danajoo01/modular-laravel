<?php namespace App\Modules\Checkout\Controllers;

use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\CheckoutCart;
use \App\Modules\Checkout\Models\Kredivo;
use Cart;

use Illuminate\Http\Request;

class KredivoController extends Controller {

  /**
   * Display a listing of the resource.
   *
   * @return Response
   */
    public function pushNotification(Request $request)
    {
        $data = $request->all();
        if ($data) {            
            $response['status'] = "OK";
            $response['message'] = "Message from merchant if any";
           
            Kredivo::pushNotification($data);            

        } else {
            $response['status'] = "ERROR";
            $response['message'] = "empty response from kredivo";
        }
        echo json_encode($response);

    }

}
