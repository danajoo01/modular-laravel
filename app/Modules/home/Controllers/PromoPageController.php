<?php namespace App\Modules\Home\Controllers;

use \App\Http\Controllers\Requests;
use \App\Http\Controllers\Controller;

use \App\Modules\Home\Models\PromoPage;

use Auth;

class PromoPageController extends Controller {

	/**
	 * Display a listing of the Benka Point.
	 *
	 * @return Response
	 */
	public function index($page_url)
	{
            $get_domain = get_domain();
            $domain_id = $get_domain['domain_id'];

            //fetch home page
            $where["url"] = $page_url;
            $where["domain_id"] = $domain_id;
            $landing_page = PromoPage::fetch_landing_page($where);

            /* s: PROSES TOP BANNER */
            $top_banner_mini = PromoPage::get_top_banner_mini($domain_id);
            /* e: PROSES TOP BANNER */

            if(count($landing_page) <= 0){
              abort(404);
            }

            $data['landing_page']     = (count($landing_page) > 0) ? $landing_page->landing_page_html : '';
            $data['top_banner_mini']  = (count($top_banner_mini) > 0) ? $top_banner_mini->content : '';

            return get_view('home', 'promo_page.index', $data);
	}

	public function specialDeals()
	{
		$get_domain = get_domain();
		$domain_id 	= $get_domain['domain_id'];

        $get_promo_category = PromoPage::promo_special_deals_category($domain_id);

		$get_promo = PromoPage::promo_special_deals($domain_id, $get_promo_category["id"]);
		
		$data = array(	'special_deals' => isset($get_promo) ? $get_promo : NULL, 
						'category' 		=> isset($get_promo_category["data"]) ? $get_promo_category["data"] : NULL
					);
		
		return get_view('home', 'special_deals.index', $data);
	}
}
