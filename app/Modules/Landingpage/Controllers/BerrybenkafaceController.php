<?php namespace App\Modules\Landingpage\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Landingpage\Models\Berrybenkaface;

use Input;
use Validator;
use Session;

use Illuminate\Http\Request;

class BerrybenkafaceController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {   
        $get_domain              = get_domain();
        if($get_domain['domain_id'] != 1){
            abort(404);
        }
        return get_view('Landingpage','berrybenkaface.index');
    }
    
    public function postBerrybenkaFace(Request $request)
    {
        $berrybenkaface = new Berrybenkaface;    
        $berrybenkaface->fullname           = $request->get('bbf_name');
        $berrybenkaface->id_instagram       = $request->get('bbf_idig');
        $berrybenkaface->total_followers    = $request->get('bbf_numfollowers');
        $berrybenkaface->email              = $request->get('bbf_email');
        
        $validation = $this->validator($request->all());
        
        if($validation->passes()){
            $create = $berrybenkaface->save();      
            if($create){
                Session::flash('bbf_success', 'Registrasi Sukses!');
                return redirect('/berrybenkaface#form-regis')->withInput();        
            }else{
                \Log::alert('[Berrybenkaface] Failed to save data');
                return redirect('/berrybenkaface#form-regis')->withInput();        
            }         
        }else{
            return redirect('/berrybenkaface#form-regis')->withError($validation->errors())->withInput();
        }
        
        
    }

    protected function validator(array $data)
    {
        $messages = [
            'bbf_name.required'                             => 'Nama lengkap harus diisi',
            'bbf_idig.required'                             => 'ID Instagram harus diisi',
            //'bbf_idig.regex'                                => 'ID Instagram kamu salah',
            'bbf_numfollowers.required'                     => 'Jumlah Follower harus diisi',
            'bbf_numfollowers.integer'                      => 'Jumlah Follower harus dalam numerik',
            'bbf_email.required'                            => 'Email harus diisi',
            'bbf_email.unique'                              => 'Email kamu sudah pernah mendaftar',            
            'bbf_email.email'                               => 'Email kamu salah', 
        ];
        return Validator::make($data, [
            'bbf_name'          => 'required|max:255',
            //'bbf_idig'          => 'required|max:255|regex:/^(\@)?([a-z0-9_.-]{1,255})$/i',
            'bbf_idig'          => 'required|max:255',
            'bbf_idig'          => 'required|max:255',
            'bbf_numfollowers'  => 'required|integer',
            'bbf_email'         => 'required|unique:duta_berrybenka,email|email|max:255',
        ],$messages);
    }
}
