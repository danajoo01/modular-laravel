<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
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
