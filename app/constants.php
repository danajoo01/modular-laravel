<?php  
use Illuminate\Http\Request;

//define('DS', '/');
if (!defined('DS')) define('DS', '/');

//define('SITE_ROOT', \Request::server('DOCUMENT_ROOT') . DS );
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS );
//http://192.168.2.117:81/

//define('SITE_PATH', 'http://' .  $_SERVER['HTTP_HOST'] . DS );
define('SITE_PATH', 'http://im.onedeca.com' . DS );

//define('SITE_PATH', 'http://' . $_SERVER['SERVER_NAME'] . DS . 'berrybenka_core' . DS );
define('ASSETS_ROOT', 			SITE_ROOT . 'assets' . DS );
define('ASSETS_PATH', 			SITE_PATH . 'assets' . DS );

define('IMAGE_PRODUCTS_CACHE_ROOT', ASSETS_ROOT . 'cache' . DS );
define('IMAGE_PRODUCTS_CACHE_PATH', ASSETS_PATH . 'cache' . DS );

//define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS );
//define('SITE_PATH', 'http://' . $_SERVER['SERVER_NAME'] . DS );
//define('SITE_FRONT_END_ROOT', \Request::server('DOCUMENT_ROOT') . DS );
define('SITE_FRONT_END_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS );
//define('SITE_FRONT_END_PATH', 'http://' . Request::server('SERVER_NAME') . DS );
//define('SITE_FRONT_END_PATH', 'http://' . $_SERVER['SERVER_NAME'] . DS );
define('SITE_MEDIA_ROOT', $_SERVER['DOCUMENT_ROOT'] . DS . 'media' . DS );
//define('SITE_MEDIA_PATH', 'http://' . $_SERVER['SERVER_NAME'] . DS . 'media' . DS );

//define('ASSETS_ROOT', 			SITE_ROOT . 'assets' . DS );
//kjdefine('ASSETS_PATH', 			SITE_PATH . 'assets' . DS );
define('ASSETS_FRONT_END_ROOT', SITE_FRONT_END_ROOT . 'assets' . DS );
//define('ASSETS_FRONT_END_PATH', SITE_FRONT_END_PATH . 'assets' . DS );

define('UPLOAD_ROOT', 			ASSETS_ROOT . 'upload' . DS );
define('UPLOAD_PATH', 			ASSETS_PATH . 'upload' . DS );
define('UPLOAD_FRONT_END_ROOT', ASSETS_ROOT . 'upload' . DS );
define('UPLOAD_FRONT_END_PATH', ASSETS_PATH . 'upload' . DS );
define('UPLOAD_MEDIA_ROOT', ASSETS_FRONT_END_ROOT . 'upload' . DS );
//define('UPLOAD_MEDIA_PATH', ASSETS_FRONT_END_PATH . 'upload' . DS );

define('IMAGE_PRODUCTS_UPLOAD_ROOT', UPLOAD_ROOT . 'product' . DS );
define('IMAGE_PRODUCTS_UPLOAD_PATH', UPLOAD_PATH . 'product' . DS );

define('IMAGE_SPECIAL_PAGE_UPLOAD_ROOT', UPLOAD_ROOT . 'special-page' . DS );
define('IMAGE_SPECIAL_PAGE_UPLOAD_PATH', UPLOAD_PATH . 'special-page' . DS );
define('IMAGE_SLIDER_UPLOAD_ROOT', UPLOAD_ROOT . 'slider' . DS );
define('IMAGE_SLIDER_UPLOAD_PATH', UPLOAD_PATH . 'slider' . DS );
define('IMAGE_FEATURED_UPLOAD_ROOT', UPLOAD_ROOT . 'featured' . DS );
define('IMAGE_FEATURED_UPLOAD_PATH', UPLOAD_PATH . 'featured' . DS );
define('IMAGE_BANNER_UPLOAD_ROOT', UPLOAD_ROOT . 'banner' . DS );
define('IMAGE_BANNER_UPLOAD_PATH', UPLOAD_PATH . 'banner' . DS );
define('IMAGE_BOTTOM_BANNER_UPLOAD_ROOT', IMAGE_BANNER_UPLOAD_ROOT . 'bottom' . DS );
define('IMAGE_BOTTOM_BANNER_UPLOAD_PATH', IMAGE_BANNER_UPLOAD_PATH . 'bottom' . DS );
define('IMAGE_TOP_BANNER_UPLOAD_ROOT', IMAGE_BANNER_UPLOAD_ROOT . 'top' . DS );
define('IMAGE_TOP_BANNER_UPLOAD_PATH', IMAGE_BANNER_UPLOAD_PATH . 'top' . DS );
define('IMAGE_CATEGORY_BANNER_UPLOAD_ROOT', IMAGE_BANNER_UPLOAD_ROOT . 'category' . DS );
define('IMAGE_CATEGORY_BANNER_UPLOAD_PATH', IMAGE_BANNER_UPLOAD_PATH . 'category' . DS );
define('IMAGE_FLOATING_BANNER_UPLOAD_ROOT', IMAGE_BANNER_UPLOAD_ROOT . 'floating' . DS );
define('IMAGE_FLOATING_BANNER_UPLOAD_PATH', IMAGE_BANNER_UPLOAD_PATH . 'floating' . DS );

define('IMAGE_PRODUCT_BANNER_ROOT', UPLOAD_ROOT . 'product_banner' . DS );
define('IMAGE_PRODUCT_BANNER_PATH', UPLOAD_PATH . 'product_banner' . DS );

define('IMAGE_DEALS_UPLOAD_ROOT', UPLOAD_ROOT . 'deals' . DS );
define('IMAGE_DEALS_UPLOAD_PATH', UPLOAD_PATH . 'deals' . DS );

define('IMAGE_PRODUCTS_ORIGINAL_UPLOAD_ROOT', 	IMAGE_PRODUCTS_UPLOAD_ROOT . 'original' . DS );
define('IMAGE_PRODUCTS_ORIGINAL_UPLOAD_PATH', 	IMAGE_PRODUCTS_UPLOAD_PATH . 'original' . DS );
define('IMAGE_PRODUCTS_ZOOM_UPLOAD_ROOT', 		IMAGE_PRODUCTS_UPLOAD_ROOT . 'zoom' . DS );
define('IMAGE_PRODUCTS_ZOOM_UPLOAD_PATH', 		IMAGE_PRODUCTS_UPLOAD_PATH . 'zoom' . DS );
define('IMAGE_PRODUCTS_DETAIL_UPLOAD_ROOT', 	IMAGE_PRODUCTS_UPLOAD_ROOT . 'details' . DS );
define('IMAGE_PRODUCTS_DETAIL_UPLOAD_PATH', 	IMAGE_PRODUCTS_UPLOAD_PATH . 'details' . DS );
define('IMAGE_PRODUCTS_CATALOG_UPLOAD_ROOT', 	IMAGE_PRODUCTS_UPLOAD_ROOT . 'catalogs' . DS );
define('IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH', 	IMAGE_PRODUCTS_UPLOAD_PATH . 'catalogs' . DS );
define('IMAGE_PRODUCTS_THUMBNAIL_UPLOAD_ROOT', 	IMAGE_PRODUCTS_UPLOAD_ROOT . 'thumbnails' . DS );
define('IMAGE_PRODUCTS_THUMBNAIL_UPLOAD_PATH', 	IMAGE_PRODUCTS_UPLOAD_PATH . 'thumbnails' . DS );
define('IMAGE_BANNER_MOBILE_UPLOAD_PATH', IMAGE_BANNER_UPLOAD_PATH . 'mobile' . DS );
define('IMAGE_BANNER_MOBILE_UPLOAD_ROOT', IMAGE_BANNER_UPLOAD_ROOT . 'mobile' . DS );

define('IMAGE_SPECIAL_PAGE_COVER_UPLOAD_ROOT', 	IMAGE_SPECIAL_PAGE_UPLOAD_ROOT . 'cover' . DS );
define('IMAGE_SPECIAL_PAGE_COVER_UPLOAD_PATH', 	IMAGE_SPECIAL_PAGE_UPLOAD_PATH . 'cover' . DS );
define('IMAGE_SPECIAL_PAGE_SLIDER_UPLOAD_ROOT', 	IMAGE_SPECIAL_PAGE_UPLOAD_ROOT . 'slider' . DS );
define('IMAGE_SPECIAL_PAGE_SLIDER_UPLOAD_PATH', 	IMAGE_SPECIAL_PAGE_UPLOAD_PATH . 'slider' . DS );
define('IMAGE_SPECIAL_PAGE_BANNER_UPLOAD_ROOT', 	IMAGE_SPECIAL_PAGE_UPLOAD_ROOT . 'banner' . DS );
define('IMAGE_SPECIAL_PAGE_BANNER_UPLOAD_PATH', 	IMAGE_SPECIAL_PAGE_UPLOAD_PATH . 'banner' . DS );

define('IMAGE_NEWSLETTER_UPLOAD_PATH', ASSETS_PATH . 'newsletter' . DS );
define('IMAGE_NEWSLETTER_UPLOAD_ROOT', ASSETS_ROOT . 'newsletter' . DS );
define('IMAGE_BRAND_UPLOAD_PATH', ASSETS_PATH . 'brand' . DS );
define('IMAGE_BRAND_UPLOAD_ROOT', ASSETS_ROOT . 'brand' . DS );
define('IMAGE_BANNER_PROMO_UPLOAD_ROOT', UPLOAD_ROOT . 'promo' . DS );
define('IMAGE_BANNER_PROMO_UPLOAD_PATH', UPLOAD_PATH . 'promo' . DS );
define('IMAGE_TICKET_UPLOAD_ROOT', UPLOAD_ROOT . 'ticket' . DS );
define('IMAGE_TICKET_UPLOAD_PATH', UPLOAD_PATH . 'ticket' . DS );
define('IMAGE_KTP_ROOT', ASSETS_ROOT . 'ktp' . DS );
define('IMAGE_KTP_PATH', ASSETS_PATH . 'ktp' . DS );
define('IMAGE_PROMO_PAGE_PATH', ASSETS_PATH . 'promo_page' . DS);
define('IMAGE_PROMO_PAGE_ROOT', ASSETS_ROOT . 'promo_page' . DS);
define('IMAGE_PROMO_PAGE_TMP_PATH', IMAGE_PROMO_PAGE_PATH . 'tmp' . DS);
define('IMAGE_PROMO_PAGE_TMP_ROOT', IMAGE_PROMO_PAGE_ROOT . 'tmp' . DS);
define('JSON_ROOT', ASSETS_ROOT . 'json_dev' . DS );
define('JSON_PATH', ASSETS_PATH . 'json_dev' . DS );

define('IMAGE_ADS_ROOT', UPLOAD_ROOT . 'ads' . DS );
define('IMAGE_ADS_PATH', UPLOAD_PATH . 'ads' . DS );

// REPORT --------------------------------------
define('CSV_ROOT', ASSETS_ROOT . 'csv' . DS );
define('CSV_PATH', ASSETS_PATH . 'csv' . DS );
define('PRODUCT_TO_LAUNCH_CSV', CSV_ROOT . DS . 'product_to_launch' . DS );
define('ZERO_STOCK_CSV', CSV_ROOT . DS . 'zero_stock' . DS );
define('ZERO_STOCK_CSV_PATH', CSV_PATH . DS . 'zero_stock' . DS );

define('APPROVAL_DATE_REPORT_CSV_ROOT', CSV_ROOT .  'approval_date_report' . DS );
define('APPROVAL_DATE_REPORT_CSV_PATH', CSV_PATH .  'approval_date_report' . DS );
define('FINANCE_CSV_ROOT', CSV_ROOT . DS . 'finance' . DS );
define('FINANCE_CSV_PATH', CSV_PATH . DS . 'finance' . DS );
define('AVERAGE_PURCHASE_CSV_ROOT', CSV_ROOT . 'average_purchase' . DS );
define('AVERAGE_PURCHASE_CSV_PATH', CSV_PATH . 'average_purchase' . DS );
define('USER_REPORT_CSV_ROOT', CSV_ROOT . 'user_report' . DS );
define('USER_REPORT_CSV_PATH', CSV_PATH . 'user_report' . DS );

define('CICILAN_REPORT_CSV', CSV_ROOT . 'cicilan_report' . DS );
define('CICILAN_REPORT_CSV_PATH', CSV_PATH . 'cicilan_report' . DS );
define('FINANCE_REPORT_BY_PURCHASE_CODE_CSV', CSV_ROOT . 'finance_report_by_purchase_code' . DS );
define('FINANCE_REPORT_BY_PURCHASE_CODE_CSV_PATH', CSV_PATH . 'finance_report_by_purchase_code' . DS );
define('MANDIRI_TRANSFER_REPORT_FINANCE_CSV', CSV_ROOT . 'mandiri_transfer_report_finance' . DS );
define('MANDIRI_TRANSFER_REPORT_FINANCE_CSV_PATH', CSV_PATH . 'mandiri_transfer_report_finance' . DS );

define('BCA_TRANSFER_REPORT_FINANCE_CSV_ROOT', CSV_ROOT . 'bca_transfer_report_finance' . DS );
define('BCA_TRANSFER_REPORT_FINANCE_CSV_PATH', CSV_PATH . 'bca_transfer_report_finance' . DS );

define('BCA_REPORT_FINANCE_CSV_ROOT', CSV_ROOT . 'bca_report_finance' . DS );
define('BCA_REPORT_FINANCE_CSV_PATH', CSV_PATH . 'bca_report_finance' . DS );

define('INVENTORY_SEARCH_CSV_ROOT', CSV_ROOT . 'inventory_search' . DS );
define('INVENTORY_SEARCH_CSV_PATH', CSV_PATH . 'inventory_search' . DS );

define('VERITRANS_REPORT_FINANCE_CSV', CSV_ROOT . 'veritrans_report' . DS );
define('VERITRANS_REPORT_FINANCE_CSV_PATH', CSV_PATH . 'veritrans_report' . DS );

define('KUDO_REPORT_FINANCE_CSV', CSV_ROOT . 'kudo_report' . DS );
define('KUDO_REPORT_FINANCE_CSV_PATH', CSV_PATH . 'kudo_report' . DS );

define('CREDIT_HISTORY_CSV', CSV_ROOT . 'credit_history' . DS );
define('CREDIT_HISTORY_CSV_PATH', CSV_PATH . 'credit_history' . DS );

define('IMAGE_POPUP_BACKGROUND_ROOT', ASSETS_ROOT . 'popupimage' . DS );
define('IMAGE_POPUP_BACKGROUND_PATH', ASSETS_PATH . 'popupimage' . DS );

define('IMAGE_SHOP_STYLE_ROOT', ASSETS_ROOT . 'shop_style' . DS );
define('IMAGE_SHOP_STYLE_PATH', ASSETS_PATH . 'shop_style' . DS );

define('IMAGE_LANDING_PAGE_ROOT', ASSETS_ROOT . 'landing_page' . DS );
define('IMAGE_LANDING_PAGE_PATH', ASSETS_PATH . 'landing_page' . DS );

define('IMAGE_SPECIAL_BRAND_ROOT', ASSETS_ROOT . 'special_brand' . DS );
define('IMAGE_SPECIAL_BRAND_PATH', ASSETS_PATH . 'special_brand' . DS );

define('KOMISI_CSV_ROOT', CSV_ROOT  . 'komisi' . DS );
define('KOMISI_CSV_PATH', CSV_PATH  . 'komisi' . DS );

define('IMAGE_PO_UPLOAD_ROOT', UPLOAD_ROOT . 'purchase_order' . DS );
define('IMAGE_PO_UPLOAD_PATH', UPLOAD_PATH . 'purchase_order' . DS );

define('IMAGE_LOYALTY_PROMO_UPLOAD_ROOT', UPLOAD_ROOT . 'loyalty_promo' . DS );
define('IMAGE_LOYALTY_PROMO_UPLOAD_PATH', UPLOAD_PATH . 'loyalty_promo' . DS );

define('IMAGE_SPECIAL_DEAL_UPLOAD_ROOT', UPLOAD_ROOT . 'special_deal' . DS );
define('IMAGE_SPECIAL_DEAL_UPLOAD_PATH', UPLOAD_PATH . 'special_deal' . DS );

// MAP CSV FOLDER
define('MAP_ITEM_MASTER_CSV_ROOT', CSV_ROOT . 'MAP' . DS . 'ItemMaster' . DS );
define('MAP_ITEM_MASTER_CSV_PATH', CSV_PATH . 'MAP' . DS . 'ItemMaster' . DS );

define('MAP_INVENTORY_AVAILABLE_CSV_ROOT', CSV_ROOT . 'MAP' . DS . 'InventoryAvailable' . DS );
define('MAP_INVENTORY_AVAILABLE_CSV_PATH', CSV_PATH . 'MAP' . DS . 'InventoryAvailable' . DS );

define('MAP_INVENTORY_RESERVED_CSV_ROOT', CSV_ROOT . 'MAP' . DS . 'InventoryReserved' . DS );
define('MAP_INVENTORY_RESERVED_CSV_PATH', CSV_PATH . 'MAP' . DS . 'InventoryReserved' . DS );

define('MAP_SALES_ORDERS_CSV_ROOT', CSV_ROOT . 'MAP' . DS . 'SalesOrders' . DS );
define('MAP_SALES_ORDERS_CSV_PATH', CSV_PATH . 'MAP' . DS . 'SalesOrders' . DS );

define('MAP_SHIPPING_STATUS_CSV_ROOT', CSV_ROOT . 'MAP' . DS . 'ShippingStatus' . DS );
define('MAP_SHIPPING_STATUS_CSV_PATH', CSV_PATH . 'MAP' . DS . 'ShippingStatus' . DS );

define('MAP_MASS_UPDATE_ARTICLE_ROOT', CSV_ROOT . 'MAP' . DS . 'MassArticleUpdate' . DS );
define('MAP_MASS_UPDATE_ARTICLE_PATH', CSV_PATH . 'MAP' . DS . 'MassArticleUpdate' . DS );

define('MASS_INVENTORY_UPDATE_ROOT', CSV_ROOT . 'mass_inventory_update' . DS );
define('MASS_INVENTORY_UPDATE_PATH', CSV_PATH . 'mass_inventory_update' . DS );

define('MAP_STOCK_FILE_ROOT', CSV_ROOT . 'MAP' . DS . 'StockFile' . DS );
define('MAP_STOCK_FILE_PATH', CSV_PATH . 'MAP' . DS . 'StockFile' . DS );

define('IMAGE_MEGA_MENU_ROOT', UPLOAD_ROOT . 'mega_menu' . DS );
define('IMAGE_MEGA_MENU_PATH', UPLOAD_PATH . 'mega_menu' . DS );

//MEGA MENU FOLDER ------------
define('MEGA_MENU_ROOT', ASSETS_ROOT . 'mega_menu' . DS );
define('MEGA_MENU_PATH', ASSETS_PATH . 'mega_menu' . DS );


define('IMAGE_MEGA_MENU_LARAVEL_ROOT', UPLOAD_ROOT . 'mega_menu_laravel' . DS );
define('IMAGE_MEGA_MENU_LARAVEL_PATH', UPLOAD_PATH . 'mega_menu_laravel' . DS );

define('MEGA_MENU_LARAVEL_ROOT', UPLOAD_ROOT . 'mega_menu_laravel' . DS );
define('MEGA_MENU_LARAVEL_PATH', UPLOAD_PATH . 'mega_menu_laravel' . DS );

define('IMAGE_SPECIAL_PAGE_TY_PAGE_UPLOAD_ROOT', IMAGE_SPECIAL_PAGE_UPLOAD_ROOT . 'ty_page' . DS );
define('IMAGE_SPECIAL_PAGE_TY_PAGE_UPLOAD_PATH', IMAGE_SPECIAL_PAGE_UPLOAD_PATH . 'ty_page' . DS );

define('MASS_PRODUCT_BOOST_ROOT', CSV_ROOT . 'mass_inventory_update' . DS );
define('MASS_PRODUCT_BOOST_PATH', CSV_PATH . 'mass_inventory_update' . DS );

define('FRONT_END_URL', 'http://satrya-dev.berrybenka.biz' . DS );
/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');



/*
 * Detect AJAX Request for MY_Session
*/
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

// WMS API
define('WMS_API', 'http://wms.berrybenka.biz/');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
