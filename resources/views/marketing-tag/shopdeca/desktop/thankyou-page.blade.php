<!--start hasoffers tag-->
<?php 
if($tag_products){
    $hasoffer_prodid = implode(',', array_unique(array_map(function ($entry) {
        return $entry['id'];
    }, $tag_products)));
    
    $hasoffer_brand = implode(',', array_unique(array_map(function ($entry) {
        return str_replace("'", "\\", isset($entry['brand-name']) ? $entry['brand-name'] : $entry['brand']);
    }, $tag_products)));
     
    $hasoffer_category = implode(',', array_unique(array_map(function ($entry) {
        $url_arr            = explode(',', $entry['type_url']);
        $parent_category    = isset($url_arr[0]) ? $url_arr[0] : NULL;
        return $parent_category;
    }, $tag_products)));   
}
$hasoffer_data                      = array();
$hasoffer_data['id']                = isset($hasoffer_prodid)   ? $hasoffer_prodid      : 'NULL';
$hasoffer_data['brand']             = isset($hasoffer_brand)    ? $hasoffer_brand       : 'NULL';
$hasoffer_data['parent_category']   = isset($hasoffer_category) ? $hasoffer_category    : 'NULL';
?>

<!-- Offer Conversion: SD1: Shopdeca Affiliate Program GENERAL -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=122&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- Offer Conversion: SDW1: Shopdeca Affiliate Weekly Campaign 1 |  -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=124&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- Offer Conversion: SDW2: Shopdeca Affiliate Monthly Campaign 2 |  -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=126&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />


<!--end hasoffers tag-->