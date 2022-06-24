<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrderHeader extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'order_header'; //Define your table name

	protected $primaryKey = 'order_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['order_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }

	public static function invoice_header($where = NULL, $limit = NULL, $order = NULL)
	{
		$order_history = DB::connection('read_mysql')->table('order_header AS A')
									->select('A.purchase_date', 'A.purchase_code', 'A.grand_total', 'B.payment_type_transfer', 'B.confirm_transfer_by', 'B.status')
									->join('order_payment AS B', 'A.purchase_code', '=' ,'B.purchase_code')
									->where('A.customer_id','=',$where['customer_id'])
									->where('A.domain_id','=',$where['domain_id']);

		if(isset($where['flag_purchase'])) {
			$order_history = $order_history->where('A.flag_purchase !=', $where['flag_purchase']);
		}

		if($order){
			$order_history = $order_history->orderBy($order, 'desc');
		}

		$order_history = $order_history->paginate($limit);//dd($order_history);

		//get products
		$oh = array();
		$total = $order_history->total();

		foreach ($order_history as $value) {
			$product_history = DB::connection('read_mysql')->table('order_header AS A')
										->select('D.product_name', 'E.brand_name', DB::raw('SUM(C.quantity) AS quantity'))
										->join('order_item AS C', 'A.purchase_code', '=' ,'C.purchase_code')
										->join('products AS D', 'C.product_id', '=' ,'D.product_id')
										->join('brand AS E', 'D.product_brand', '=' ,'E.brand_id')
										->where('A.customer_id','=',$where['customer_id'])
										->where('A.domain_id','=',$where['domain_id'])
										->where('A.purchase_code','=',$value->purchase_code)
										->groupBy('C.product_id');

			$product_history = $product_history->get();

			$oh[$value->purchase_code]['purchase_date'] 			= $value->purchase_date;
			$oh[$value->purchase_code]['purchase_code'] 			= $value->purchase_code;
			$oh[$value->purchase_code]['grand_total'] 				= $value->grand_total;
			$oh[$value->purchase_code]['payment_type_transfer'] 	= $value->payment_type_transfer;
			$oh[$value->purchase_code]['confirm_transfer_by'] 		= $value->confirm_transfer_by;
			$oh[$value->purchase_code]['status'] 					= $value->status;
			$oh[$value->purchase_code]['product'] 					= $product_history;
		}//dd($oh);
		$data['oh'] 	= $oh;
		$data['total'] 	= $total;
		$data['data'] 	= $order_history;

		return $data;

	}

	public static function get_delivered_list ($customer_id = NULL, $limit = NULL) {
		$delivered_list = DB::table('order_item AS oi')
									->select(DB::connection('read_mysql')->raw("	oi.purchase_code, 
														oi.SKU, 
														oi.order_item_id, 
														oh.purchase_date, 
														pv.variant_color_name_custom, 
														pv.variant_color_name, 
														p.product_name, 
														pv.product_size, 
														(select 
																created_date 
														from 
																order_item_history 
														where 
																order_status_item = 7 and 
																order_item_id = oi.order_item_id 
														LIMIT 1 
														ORDER BY 
																created_date desc
														) as delivered_date"))
									->join('products AS p', 'p.product_id', '=', 'oi.product_id')
									->join('product_variant AS pv', 'pv.SKU', '=', 'oi.SKU')
									->join('order_item_history AS oih', 'oih.order_item_id', '=', 'oi.order_item_id')
									->join('order_header AS oh', 'oh.purchase_code', '=', 'oi.purchase_code')
									->having(DB::raw('(	select 
															created_date 
														from 
															order_item_history 
														where 
															order_status_item = 7 and 
															order_item_id = oi.order_item_id 
														LIMIT 1 
														ORDER BY created_date desc)'), ' > ', date('Y-m-d H:i:s', strtotime("-10 days")))
									->where('oh.customer_id', $customer_id)
									->where('oi.order_status_item', 7)
									->orderBy('delivered_date', 'desc')
									->groupBy('oi.order_item_id');
		
		$delivered_list = $delivered_list->paginate($limit);//dd($delivered_list);
		
		return isset($delivered_list) ? $delivered_list : NULL;
	}
	
	public static function get_returned_list ($customer_id = NULL) {
		$returned_list = DB::table('order_item AS oi')
									->select(DB::connection('read_mysql')->raw("	oi.purchase_code, 
														oi.SKU, 
														oi.order_item_id, 
														oh.purchase_date, 
														pv.variant_color_name_custom, 
														pv.variant_color_name, 
														p.product_name, 
														pv.product_size,
														(select 
																created_date 
														from 
																order_item_history 
														where 
																order_status_item = oi.order_status_item and 
																order_item_id = oi.order_item_id 
														GROUP BY order_item_id 
														ORDER BY created_date DESC) as date_processed,
														CASE oi.order_status_item
															WHEN 13 THEN 'OPEN'
															ELSE 'CLOSED' 
														END as return_status"))
									->join('products AS p', 'p.product_id', '=', 'oi.product_id')
									->join('product_variant AS pv', 'pv.SKU', '=', 'oi.SKU')
									->join('order_item_history AS oih', 'oih.order_item_id', '=', 'oi.order_item_id')
									->join('order_header AS oh', 'oh.purchase_code', '=', 'oi.purchase_code')
									->where('oh.customer_id', $customer_id)
									->whereIn('oi.order_status_item', [13,14,15,16])
									->orderBy('oi.order_item_id', 'desc')
									->groupBy('oi.order_item_id');
		
		$returned_list = $returned_list->paginate(5);//dd($returned_list);

		return isset($returned_list) ? $returned_list : NULL;
	}

	/* Order Tracking Status */
	public static function fetch_order_tracking($where = NULL, $order = NULL) {
		$order_track = DB::table('order_item AS a')
								->select(DB::connection('read_mysql')->raw("a.order_item_id,
				  								  a.purchase_code,
				  								  a.product_id,
				  								  c.product_name,
				  								  a.SKU,
											  	  a.customer_id,
											 	  a.customer_email,
											 	  a.purchase_status,
											 	  a.order_status_item AS status_final,
											 	  b.order_status_item AS status_history,
											 	  a.approval_date AS approve,
											 	  b.created_date AS approve_change,
												  d.SHIPPING_METHOD,
												  d.TRACKING_NUMBER"))
								->leftJoin('order_item_history AS b', 'a.order_item_id', '=', 'b.order_item_id')
								->leftJoin('berrybenka_wms.delivery AS d', 'a.purchase_code', '=', 'd.purchase_code')
								->join('products AS c', 'a.product_id', '=', 'c.product_id')
								->where('a.purchase_code',  $where['purchase_code'])
								->orderBy('a.product_id', 'ASC')
								->orderBy('b.created_date', 'ASC');
		
		$order_track = $order_track->get();//dd($order_track);

		if(count($order_track) > 0) {
			return $order_track;
		}

		return array();
	}
	
	public static function fetch_invoice_detail($where = NULL)
	{
		//fetch invoice detail
		$invoice_detail = DB::table('order_header AS A')
									->select(DB::connection('read_mysql')->raw("	A.purchase_code,
														A.purchase_date,
														A.paycode,
														A.shipping_finance,
														A.credit_use,
														A.grand_total,
														A.domain_id,
														A.channel,
														A.shipping_address,
														C.shipping_area,
														C.shipping_name,
														B.confirm_transfer_by,
														B.master_payment_id,
														B.kredivo_redirect_uri,
														B.status,
                                                                                                                E.master_payment_name,
														D.purchase_status"))
									->join('order_payment AS B', 'A.purchase_code', '=', 'B.purchase_code')
									->join('shipping AS C', 'A.shipping_id', '=', 'C.shipping_id')
									->join('order_item AS D', 'A.purchase_code', '=', 'D.purchase_code')
                                                                        ->leftJoin('master_payment AS E', 'E.master_payment_id', '=', 'B.master_payment_id')
									->where('A.customer_id', $where['customer_id'])
									->where('A.purchase_code', $where['purchase_code']);
	
		$invoice_detail = $invoice_detail->first();//dd($invoice_detail);

		if(isset($invoice_detail->purchase_code))
		{
			$invoice_detail->purchase_code = isset($invoice_detail->purchase_code) ? $invoice_detail->purchase_code : '';
			$invoice_detail->purchase_date = isset($invoice_detail->purchase_date) ? $invoice_detail->purchase_date : '';
		}
		
		//fetch discount
		$fetch_discount = DB::table('order_discount')
									->select(DB::connection('read_mysql')->raw("	discount_nfc_or_discount,
														SUM(discount_value) AS discount_value"))
									->where('customer_id', $where['customer_id'])
									->where('purchase_code', $where['purchase_code'])
									->groupBy('purchase_code','discount_id');

		$fetch_discount = $fetch_discount->get();//dd($fetch_discount);

		$order_product = DB::table('products AS A')
									->select(DB::connection('read_mysql')->raw("	A.product_name,
														B.special_price,
														B.each_price,
														B.discount_price,
														B.product_id,
														B.SKU,
														C.brand_name,
														COUNT(B.quantity) AS quantity,
														(COUNT(B.quantity) * B.discount_price) AS total_discount_price,
														(COUNT(B.quantity) * B.each_price) AS total_price,
														(COUNT(B.quantity) * B.special_price) AS total_special_price"))
									->join('order_item AS B', 'A.product_id', '=', 'B.product_id')
									->join('brand AS C', 'C.brand_id', '=', 'A.product_brand')  
									->where('B.customer_id', $where['customer_id'])
									->where('B.purchase_code', $where['purchase_code'])
									->groupBy('B.product_id');
	
		$order_product = $order_product->get();//dd($order_product);

		$product_order = array();

		foreach ($order_product as $product) {
			$product_order[$product->product_id]['product_name'] 			= $product->product_name;
			$product_order[$product->product_id]['special_price'] 			= $product->special_price;
			$product_order[$product->product_id]['each_price'] 				= $product->each_price;
			$product_order[$product->product_id]['discount_price'] 			= $product->discount_price;
			$product_order[$product->product_id]['brand_name'] 				= $product->brand_name;
			$product_order[$product->product_id]['quantity'] 				= $product->quantity;
			$product_order[$product->product_id]['total_discount_price'] 	= $product->total_discount_price;
			$product_order[$product->product_id]['total_price'] 			= $product->total_price;
			$product_order[$product->product_id]['total_special_price'] 	= $product->total_special_price;
			$product_order[$product->product_id]['sku'] 					= $product->SKU;
			
			$get_variant_image = DB::table('product_image AS A')
										->select(DB::connection('read_mysql')->raw("	IF (
															    B.variant_color_name_custom IS NOT NULL,
															    B.variant_color_name_custom,
															    B.variant_color_name
														  	) AS color,
														  	B.product_size,
														  	A.image_name"))
										->join('product_variant AS B', 'A.product_id', '=', 'B.product_id')
										->join('order_item AS C', 'A.product_id', '=', 'C.product_id')
										->where('C.customer_id', $where['customer_id'])
										->where('C.purchase_code', $where['purchase_code'])
										->where('C.product_id', $product->product_id)
                                                                                ->where('B.SKU', $product->SKU) 
										->groupBy('B.product_id');
                        //dd($get_variant_image);
			$get_variant_image = $get_variant_image->first();

			$product_order[$product->product_id]['color'] 			= isset($get_variant_image->color) ? $get_variant_image->color : '';
			$product_order[$product->product_id]['product_size'] 	= isset($get_variant_image->product_size) ? $get_variant_image->product_size : '';
			$product_order[$product->product_id]['image_name'] 		= isset($get_variant_image->image_name) ? $get_variant_image->image_name : '';
		}
		
		$data = array("invoice_detail" => $invoice_detail, "order_product" => $product_order, "discount" => $fetch_discount);

		return $data;
	}

	/* update order payment and purchase status */
	public static function update_confirm_transfer($where = NULL, $data = NULL) {
		$update = DB::table('order_payment')
							->where('purchase_code',  $where['purchase_code'])
							->update($data);

		//cek purchase status first
		$cek_ps = DB::connection('read_mysql')->table('order_item')
							->select("purchase_status")
							->where('purchase_code', $where['purchase_code']);
	
		$cek_ps = $cek_ps->first();//dd($order_product);
		
		if (count($cek_ps) <> 0 && $cek_ps->purchase_status <> 3)
		{
			//update order item
			$order_item = array('purchase_status' => 2);
			$update = DB::table('order_item')
								->where('purchase_code',  $where['purchase_code'])
								->update($order_item);
		}

		return $update;
	}
	
	public static function get_order_item_detail ($order_item_id = NULL) {
		$returned_detail = DB::table('order_item AS oi')
									->select(DB::connection('read_mysql')->raw("	oi.order_item_id, 
														oi.SKU, 
														p.product_name, 
														oi.product_id, 
														oi.customer_id, 
														oi.purchase_code, 
														oi.quantity, 
														IFNULL(pv.variant_color_name_custom, pv.variant_color_name) product_color, 
														pv.product_size"))
									->join('products AS p', 'p.product_id', '=', 'oi.product_id')
									->join('product_variant AS pv', 'pv.SKU', '=', 'oi.SKU')
									->where('oi.order_item_id', $order_item_id);
		
		$returned_detail = $returned_detail->first();//dd($returned_detail);

		return isset($returned_detail) ? $returned_detail : NULL;
	}
	
	public static function get_size_variant ($where = NULL) {
		$size_variant = DB::connection('read_mysql')->table('product_variant')
									->select("product_size")
									->where('product_id', $where['product_id'])
									->where('status', $where['status']);
    
		if(isset($where['domain_id']) && $where['domain_id'] == 2) {
			$size_variant = $size_variant->where('own_hb', 1);
		}elseif(isset($where['domain_id']) && $where['domain_id'] == 3){
			$size_variant = $size_variant->where('own_sd');
    }else{
      $size_variant = $size_variant->where('own_bb');
    }

		$size_variant = $size_variant->groupBy('product_size');

		$size_variant = $size_variant->get();//dd($size_variant);

		return isset($size_variant) ? $size_variant : NULL;
	}
	
	public static function get_color_variant ($where = NULL) {
		$color_variant = DB::table('product_variant AS A')
									->select(DB::connection('read_mysql')->raw("B.color_name AS variant_color_name"))
									->leftJoin('general_colors AS B', 'A.general_color_id', '=', 'B.general_color_id')
									->where('A.product_id', $where['product_id'])
									->where('A.status', $where['status']);
    
		if(isset($where['domain_id']) && $where['domain_id'] == 2) {
			$color_variant = $color_variant->where('A.own_hb', 1);
		}elseif(isset($where['domain_id']) && $where['domain_id'] == 3){
			$color_variant = $color_variant->where('A.own_sd', 1);
    }else{
      $color_variant = $color_variant->where('A.own_bb', 1);
    }

		$color_variant = $color_variant->groupBy('A.general_color_id');

		$color_variant = $color_variant->get();//dd($color_variant);

		return isset($color_variant) ? $color_variant : NULL;
	}

	/* insert return data */
	public static function insert_customer_return($data) {
		$insert = DB::table('customer_return')->insert($data);

		return $insert;
	}

	/* cancel return */
	public static function cancel_return($order_item_id = NULL) {
		$update = DB::table('order_item')
							->where('order_item_id',  $order_item_id)
							->update(array('order_status_item' => 7));
		$return = FALSE;

		if($update){
			$delete = DB::table('order_item_history')
								->where('order_item_id',  $order_item_id)
								->where('order_status_item',  13)
								->delete();

			if($delete){
				$delete_cr = DB::table('customer_return')
										->where('order_item_id',  $order_item_id)
										->delete();
				$return = $delete_cr;
			}
		}

		return $return;
	}
}
