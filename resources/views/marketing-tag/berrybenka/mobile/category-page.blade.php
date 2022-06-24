<?php
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

$product_data_json = json_encode($product_data);
$product_ids_json  = json_encode($product_ids);

//GTM
$ChildIdList_gtm = json_encode($ChildId_gtm);
$ChildNameList_gtm = json_encode($ChildName_gtm);
$brandIDList_gtm = json_encode($brandID_gtm);
$brandNameList_gtm = json_encode($brandName_gtm);

$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';

$generate_uri_segment   = generate_uri_segment();//bb_debug($generate_uri_segment);//die;
$parent_type_url        = $generate_uri_segment['parent_type_url'] ? $generate_uri_segment['parent_type_url'] : '';
$child_type_url         = $generate_uri_segment['child_type_url'] ? $generate_uri_segment['child_type_url'] : '';
$sub_child_type_url     = $generate_uri_segment['sub_child_type_url'] ? $generate_uri_segment['sub_child_type_url'] : '';

$segment_1              = (\Request::segment(1)) ? \Request::segment(1) : NULL;  
if(isset($segment_1) && ($segment_1 == 'new-arrival' || $segment_1 == 'sale')){    
    $CatIDforRTBHouse   = $segment_1;
}else{
    $CatIDforRTBHouse   = $parent_type_url;   
}
if($child_type_url != ''){
    $CatIDforRTBHouse   = $child_type_url;
    if($sub_child_type_url != ''){
        $CatIDforRTBHouse   = $sub_child_type_url;    
    }    
}
?>
<script type="text/javascript"> 
<!-- datalayer for GTM -->
if(typeof mydata336CC993E54E == 'undefined') {
        var mydata336CC993E54E = [];
}
dataLayer.push({
    'PageType': 'Categorypage', 
    'HashedEmail': '{{ $user_email }}', 
    'ProductIDList': mydata336CC993E54E.product_data,
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

<script>
dataLayer.push({
  'ecommerce': {
    'currencyCode': 'IDR',                       
    'impressions': {!! $product_data_json !!}
    }
});
</script>

<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_category2_1" width="1" height="1"  scrolling="no"  frameBorder="0" style="display: none;"></iframe>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '256929811160719', {
em: '{!! $user_email !!}',
});
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->

<!-- RTB House Category Page --> 
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_category2_{{ $CatIDforRTBHouse }}" width="1" height="1" scrolling="no" frameBorder="0"
style="display: none;"></iframe>