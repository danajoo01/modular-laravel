<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";

$string_id_rtb      = '';

$ChildId_gtm     = []; 
$ChildName_gtm   = [];
$brandID_gtm     = [];
$brandName_gtm   = [];

if(isset($catalog)){
    foreach($catalog as $row){
        if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
            continue;
        }
        $arr_catalog[]  =   $row->pid;
        
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
    if(isset($arr_catalog)){
        $list_search_id = implode(',',$arr_catalog);
        $string_id_rtb  = isset($list_search_id) ? $list_search_id : '';   
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

<!-- RTB House Search 5/12/2016 {{ date('d m y H:i:s') }} -->
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_listing_{{ $string_id_rtb }}" width="1"
height="1" scrolling="no" frameBorder="0"style="display: none;"></iframe>