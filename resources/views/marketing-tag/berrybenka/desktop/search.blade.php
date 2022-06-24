<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";

$string_id_rtb      = '';
if(isset($catalog)){
    foreach($catalog as $row){
        $arr_catalog[]  =   $row->pid;
    }    
    if(isset($arr_catalog)){
        $list_search_id = implode(',',$arr_catalog);
        $string_id_rtb  = isset($list_search_id) ? $list_search_id : '';   
    }     
}
?>
<!-- datalayer for GTM -->
<script type="text/javascript"> 
dataLayer.push( {
    'PageType': 'Categorypage', 
    'HashedEmail': '{{ $user_email }}', 
    'ProductIDList': criteo_data0192e3,
    'CategoryIDList': childCatIdGTMList_data0192e3,
    'CategoryNameList': childCatNameGTMList_data0192e3,
    'BrandIDList': brandIdGTMList_data0192e3,
    'BrandNameList' : brandNameGTMList_data0192e3
}); 
</script>

<!-- RTB House Search 5/12/2016 {{  date('d m y H:i:s') }}-->
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_listing_{{ $string_id_rtb }}" width="1"
height="1" scrolling="no" frameBorder="0"style="display: none;"></iframe>