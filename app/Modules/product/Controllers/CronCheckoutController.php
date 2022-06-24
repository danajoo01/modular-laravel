<?php namespace App\Modules\Product\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Checkout\Models\Shipping;
use \App\Modules\Home\Models\Home;

use Input;
use Validatoor;

use Illuminate\Http\Request;

//use Illuminate\Http\Request;
// use Redis;
use Illuminate\Support\Facades\Redis;

class CronCheckoutController extends Controller {

    
     /**
     * Automate Location Store with Master Payment
     *
     * @return Response
     */
    public function run_offline_store()
    {
        $automate_offline_store_payment = Shipping::automate_location_store_master_payment();

        echo $automate_offline_store_payment;
    }

     // --------------------------------------------------------------------

    /**
     * Automate Homepage
     *
     * $channel => 1/2/3/4 
     * $domain => berrybenka.com/m.berrybenka.com/hijabenka.com/m.hijabenka.com 
     *
     * @return Response
     */
    public function run_homepage_cache($channel = null)
    {
        $automate_homepage_cache = Home::automate_homepage_cache($channel);

        echo $automate_homepage_cache;
    }

    // --------------------------------------------------------------------

}
