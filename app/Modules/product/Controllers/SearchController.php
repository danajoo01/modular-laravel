<?php 
namespace App\Modules\Product\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Product\Models\Product;
use \App\Modules\Product\Models\Search;

use Input;
use Validatoor;

use Illuminate\Http\Request;

class SearchController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view("product::product.index");
	}

	 /**
     * Show the search result.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxSearch(Request $request)
    {
    	$get_domain        = get_domain();
        $request["domain"] = $get_domain;

    	$result = Search::getAjaxSearch($request);

        echo $result;
    }

	 /**
     * Show the search result.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
    	//Define Domain and Channel
        $get_domain        = get_domain();
        $request["domain"] = $get_domain;
		
		//Generate get uri
		$generate_uri_httpget    = generate_get_uri();

        $result = Search::search($request);
        
        if(isset($result['url_terms']))
        {
        	return redirect($result['url_terms']);
        }
        
        // Data call into view
        $data['catalog']        = isset($result["products"]) ? $result["products"] : [];
        $data['total_catalog']  = isset($result["total_products"]) ? $result["total_products"] : null;
        $data['start_catalog']  = isset($result["start_catalog"]) ? $result["start_catalog"] : null;
        $data['status']         = isset($status) ? $status : null;
        $data['title']          = isset($result["title"]) ? $result["title"] : null;
        $data['skeyword']       = isset($result["skeyword"]) ? $result["skeyword"] : null;
        $data['ga_list'] 		= isset($result["ga_list"]) ? $result["ga_list"] : null;

        switch($get_domain['channel']) {
            case 1:
                if(\Request::ajax()) {
                    return response()->json($data);
                } else {
                    return view('product::berrybenka.desktop.search.index', $data);
                }
            break;
            case 2: 
                return view('product::berrybenka.mobile.search.index', $data);
            break;
            case 3: 
                if(\Request::ajax()) {
                    return response()->json($data);
                } else {
                    return view('product::hijabenka.desktop.search.index', $data);
                }
            break;
            case 4: 
                return view('product::hijabenka.mobile.search.index', $data);
            break;
            case 5: 
                if(\Request::ajax()) {
                    return response()->json($data);
                } else {
                    return view('product::shopdeca.desktop.search.index', $data);
                }
            break;
            case 6: 
                return view('product::shopdeca.mobile.search.index', $data);
            break;
        }
    }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
