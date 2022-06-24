<?php namespace App\Modules\Landingpage\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;
use \App\Modules\Landingpage\Models\Bulletin;

use Input;
use Validator;
use Session;

use Illuminate\Http\Request;

class BulletinController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request){
        $get_domain     = get_domain();
        $domain_id  = $get_domain['domain_id'];
        
        if($domain_id == 1){
            return get_view('Landingpage','bulletin.index');    
        }else{
            abort(404); 
        }        
    }
}