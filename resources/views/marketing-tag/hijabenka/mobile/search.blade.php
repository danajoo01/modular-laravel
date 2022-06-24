<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";

$ChildId_gtm     = []; 
$ChildName_gtm   = [];
$brandID_gtm     = [];
$brandName_gtm   = [];

if(isset($catalog)){
    foreach($catalog as $row){  
        if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
            continue;
        }
        
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
    }
}
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
    'ProductIDList': mydata336CC993E54E.product_data,
    'CategoryIDList': {!! $ChildIdList_gtm !!},
    'CategoryNameList': {!! $ChildNameList_gtm !!},
    'BrandIDList': {!! $brandIDList_gtm !!},
    'BrandNameList' : {!! $brandNameList_gtm !!} 
}); 
</script>