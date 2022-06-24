<?php namespace App\Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class User extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'customer_address'; //Define your table name

	protected $primaryKey = 'address_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['address_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Account\Models\Model_name');
	// }

	public static function get_customer_address($customer_id = NULL, $address_type = NULL, $limit = 10)
	{
		$customer_address = DB::table('customer_address')
									->where('customer_id', $customer_id)
									->where('address_type', $address_type)
									->where('status', 1)
									->orderBy('is_primary', 'desc')
									->orderBy('address_id', 'asc')
									->paginate($limit);

		// $customer_address = $customer_address->get();//dd($customer_address);

		return (count($customer_address) > 0) ? $customer_address : array();

	}

	public static function fetch_shipping($where = NULL, $limit = NULL, $offset = NULL, $order = NULL, $group = NULL)
	{
		$shipping = DB::table('shipping')
									->where('shipping_type', 1)
									->where('enabled', 1);

		if(isset($where['shipping_area'])){
			$shipping = $shipping->where('shipping_area', '=', $where['shipping_area']);
		}

		if($order){
			$shipping = $shipping->orderBy($order, 'asc');
		}
		
		if($group != NULL){
			$shipping = $shipping->groupBy($group);
		}

		$shipping = $shipping->get();//dd($shipping);

		return (count($shipping) > 0) ? $shipping : array();

	}

	/* delete address */
	public static function deleteAddress($address_id = NULL) {
		$update = DB::table('customer_address')
							->where('address_id',  $address_id)
							->update(array('status' => 0));
		$return = true;

		return $return;
	}

	/* update user data */
	public static function update_user_data($where = NULL, $data) {
		$update = DB::table('customer')
							->where('customer_id',  $where['customer_id'])
							->update($data);
		$return = true;

		return $return;
	}
}
