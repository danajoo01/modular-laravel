<?php namespace App\Modules\SupplierBrand\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierBrand extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'supplier_brand'; //Define your table name

	protected $primaryKey = 'id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id']; //Define your guarded columns
	
	/** Fetch Brand List Page 
	*** Get Data Brand
	*** @return data array.
	**/
	public static function fetchBrandList($where) {
		
		$brand_list = Self::select('B.brand_id', 'B.brand_name', 'B.brand_url')
							->join('brand AS B', 'supplier_brand.brand_id', '=' ,'B.brand_id')
							->where('supplier_brand.id_supplier','=',$where["customer_id"])
							->orderBy('supplier_brand.brand_id', 'asc')
							->paginate($where["limit"]);
		
		return $brand_list;
	}
	
	/** Fetch Inventory Page 
	*** Get Data Inventory
	*** @return data array.
	**/
	public static function getBrandName($where) {
		
		$brand_list = Self::select('B.brand_name')
							->join('brand AS B', 'supplier_brand.brand_id', '=' ,'B.brand_id')
							->where('supplier_brand.id_supplier','=',$where["customer_id"])
							->where('supplier_brand.brand_id','=',$where["brand_id"])
							->limit(1)
							->first();
		
		return $brand_list;
	}
	
	/** Fetch Inventory Page 
	*** Get Data Inventory
	*** @return data array.
	**/
	public static function listInventory($where) {

		if($where["domain_id"] == 1){
			$launch = 'launch_date_bb';
		}elseif($where["domain_id"] == 2){
			$launch = 'launch_date_hb';
		}else{
			$launch = 'launch_date_sd';
		}
		
		$list_inventory = \DB::table('products')
							->select(	'product_variant.SKU', 
										'products.product_id', 
										'products.product_name', 
										'product_variant.product_size', 
										'product_variant.variant_color_name', 
										'product_variant.variant_color_name_custom', 
										'inventory.quantity', 
										'inventory.quantity_warehouse', 
										\DB::raw("IFNULL(date_format(products.$launch, '%Y-%m-%d'), '-') product_launch_date"),
										\DB::raw("CASE products.product_status
													    WHEN 0 then 'New Product (disable)'
													    WHEN 1 then 'Enabled'
													    WHEN 2 then 'Out Of Stock'
													    WHEN 3 then 'Disabled'
													    WHEN 4 then 'Incoming'
													    WHEN 5 then 'Draft Incoming'
												   END product_status")
									)
							->leftJoin('product_variant', 'product_variant.product_id', '=' ,'products.product_id')
							->leftJoin('inventory', 'inventory.SKU', '=' ,'product_variant.SKU')
							->where('products.product_brand','=',$where["brand_id"])
							->orderBy('products.product_id', 'DESC')
							->groupBy('product_variant.SKU', 'products.product_id');

		if(isset($where['status']) && !empty($where['status']) && $where['status'] != 'ALL')
		{
			switch ($where['status'])
			{
				case 'NEW_PRODUCT':
					$list_inventory = $list_inventory->where('products.product_status',0);
					break;
				
				case 'ENABLED':
					$list_inventory = $list_inventory->where('products.product_status',1);
					break;
				
				case 'OUT_OF_STOCK':
					$list_inventory = $list_inventory->where('products.product_status',2);
					break;
				
				case 'DISABLED':
					$list_inventory = $list_inventory->where('products.product_status',3);
					break;
				case 'INCOMING':
					$list_inventory = $list_inventory->where('products.product_status',4);
					break;
			}
		}

		if(isset($where['product_name']) && !empty($where['product_name']))
		{
			$list_inventory = $list_inventory->where('products.product_name', 'like', '%'.$where['product_name'].'%');
		}
		
		if(isset($where['limit']))
		{
			$list_inventory = $list_inventory->paginate($where["limit"]);
		}
		else
		{
			$list_inventory = $list_inventory->get();
		}
		
		return $list_inventory;
	}
	
	public static function getCsvData($data = NULL)
	{
		$HeaderTitle = array();
		
		if(!empty($data))
		{
			$HeaderTitle[] = array("PRODUCT NAME","SIZE","COLOR","INVENTORY","LAUNCH DATE","STATUS");
			
			foreach($data as $inventory)
			{
				$color = ($inventory->variant_color_name_custom <> '' && $inventory->variant_color_name_custom <> NULL) ? $inventory->variant_color_name_custom : $inventory->variant_color_name;
				
				$HeaderTitle[] = array($inventory->product_name,$inventory->product_size,$color,$inventory->quantity_warehouse,$inventory->product_launch_date,$inventory->product_status);
			}
		}
		
		return $HeaderTitle;
	}
}
