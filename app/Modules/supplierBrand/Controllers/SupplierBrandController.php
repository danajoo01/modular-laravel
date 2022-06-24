<?php namespace App\Modules\SupplierBrand\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\SupplierBrand\Models\SupplierBrand;

use Input;
use Validatoor;

use Illuminate\Http\Request;

class SupplierBrandController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		/*if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];
		
		$user = Auth::user();
		
		if (empty($user)) {
			return redirect('/login');
		}*/

		$customer_id = \Request::segment(4);

		$data["customer_id"] 	= $customer_id;
		$data["limit"] 			= 20;

		$data['data'] 	= SupplierBrand::fetchBrandList($data);
		$data['title'] 	= 'Your List Brand, Americanino / Per Favore / Elvi';
		
		return get_view('supplierBrand', 'supplierBrand.index', $data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function inventory()
	{
		/*if (!Auth::check()) {
            return redirect('login/?continue='.urlencode('/user/account_dashboard'));
        }

		
		
		$user = Auth::user();
		
		if (empty($user)) {
			return redirect('/login');
		}*/

		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];

		$customer_id 	= \Request::segment(4);
		$brand_id 		= \Request::segment(6);

		$data["customer_id"] 	= $customer_id;
		$data["brand_id"] 		= $brand_id;
		$data["limit"] 			= 20;

		$data["fetch_brand_name"] 	= SupplierBrand::getBrandName($data);
		
		if (!$data["fetch_brand_name"]) redirect('brand_report/index/id/' . $customer_id);
		
		$data["domain_id"] 		= $domain_id;
		$data["status"]			= request()->status;
		$data["product_name"]	= request()->product_name;

		$data["data"] 	= SupplierBrand::listInventory($data);

		$data['title'] 	= 'Review Inventory Brand '.$data["fetch_brand_name"]["brand_name"];
		
		return get_view('supplierBrand', 'supplierBrand.inventory', $data);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getCsvInventory()
	{
		$get_domain = get_domain();
		$channel 	= $get_domain['channel'];
		$domain 	= $get_domain['domain'];
		$domain_id 	= $get_domain['domain_id'];

		$customer_id 	= \Request::segment(3);
		$brand_id 		= \Request::segment(4);
		$status 		= \Request::segment(5);

		$data["customer_id"] 	= $customer_id;
		$data["brand_id"] 		= $brand_id;
		$data["domain_id"] 		= $domain_id;
		$data["status"]			= $status;

		$fetch_brand_name 	= SupplierBrand::getBrandName($data);
		
		if (!$fetch_brand_name) redirect('brand_report/index/id/' . $customer_id);

		$inventory = SupplierBrand::listInventory($data);
		
		$csv_data = SupplierBrand::getCsvData($inventory);
		
		$rand_num = mt_rand(0, 100);
		
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"REPORT_INVENTORY_" . $fetch_brand_name["brand_name"] . '_' . date("Y-m-d") . "_".$rand_num.".csv\"");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		return outputCSV($csv_data);
	}

}
