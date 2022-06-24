<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";

$product_data = [];
$product_ids = [];
$index = 0;

$ChildId_gtm     = []; 
$ChildName_gtm   = [];
$brandID_gtm     = [];
$brandName_gtm   = [];

foreach($catalog as $row) {
    if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
        continue;
    }
    $product_data[$index]['id']   = $row->pid;
    $product_data[$index]['name'] = $row->product_name;
    $product_data[$index]['list'] = 'clothing';

    $product_ids[] = $row->pid;
    
    //additional for gtm                
    $arrCat = array_filter(explode(',' , $row->front_end_type));
    if(isset($arrCat)){
        $ChildId_gtm[] = array_values(array_slice($arrCat, -1))[0];   
    }

    $arrCatName = array_filter(explode(',' , $row->url_set));
    if(isset($arrCatName)){
        $ChildName_gtm[] = array_values(array_slice($arrCatName, -1))[0];   
    }

    $brandID_gtm[] = $row->brand_id;
    $brandName_gtm[] = str_replace("'", "\\", $row->brand_name);

    $index++;
}

$product_ids_json  = json_encode($product_ids);

//GTM
$ChildIdList_gtm = json_encode($ChildId_gtm);
$ChildNameList_gtm = json_encode($ChildName_gtm);
$brandIDList_gtm = json_encode($brandID_gtm);
$brandNameList_gtm = json_encode($brandName_gtm);
?>
<script type="text/javascript"> 
<!-- datalayer for GTM -->
dataLayer.push({
    'PageType': 'Categorypage', 
    'HashedEmail': '{{ $user_email }}', 
    'ProductIDList': mydata336CC993E54D.product_data,
    'CategoryIDList': {!! $ChildIdList_gtm !!},
    'CategoryNameList': {!! $ChildNameList_gtm !!},
    'BrandIDList': {!! $brandIDList_gtm !!},
    'BrandNameList' : {!! $brandNameList_gtm !!} 
});

<!-- banner catalog click -->
function onBannerCatalogClick(template_title,banner_name) {
    ga('send','event','Banner Catalog','click',template_title + '#' + banner_name,1);
}
<!-- end banner catalog click -->
</script>
