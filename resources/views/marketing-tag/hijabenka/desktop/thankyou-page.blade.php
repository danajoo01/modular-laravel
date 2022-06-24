<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<!-- datalayer for GTM -->
<script type="text/javascript">    
    dataLayer.push( {
        'PageType': 'Thankyoupage', 
        'HashedEmail': '{{ $user_email }}',  
        'TransProducts': finalorder336CC993E54E.item, 
        'TransGrandTotal': finalorder336CC993E54E.grand_total,
        'TransactionID': finalorder336CC993E54E.purchase_code        
    });
</script>

<script>
   (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
   (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
   m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
   })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
 
    ga('create', 'UA-49302532-1', 'hijabenka.com');
    ga('require', 'displayfeatures'); 
    ga('send', 'pageview');
    ga('require', 'ecommerce', 'ecommerce.js');
                                                                         
    ga('ecommerce:addTransaction', {
      id          : '{{ $marketing_data['purchase_code'] }}', // Transaction ID
      affiliation : 'Hijabenka', // Affiliation or store name
      revenue     : {{ $marketing_data['grand_total'] }}, // Grand Total
      shipping    : {{ $marketing_data['shipping'] }} , // Shipping cost
      tax         : {{ $marketing_data['tax'] }}, // Tax.
      city        : '{{ $marketing_data['city'] }}', // City
      province    : '{{ $marketing_data['province'] }}', // state or province
      country     : 'INDONESIA'
    }); // country

   <?php $items = [];?>
   @foreach($carts as $row)
   ga('ecommerce:addItem', {
       id: '{{ $marketing_data['purchase_code'] }}', // Transaction ID.
       sku: '{{ $row['SKU'] }}', // SKU/code.
       name: '{{ $row['name'] }}', // Product name.
       category: '', // Category or variation.
       price: '{{ $row['price'] }}', // Unit price.
       quantity: '{{ $row['qty'] }}'}
   ); // Quantity.
   <?php $items[] =  $row['product_id']; ?>
   @endforeach

   <?php $json_item =  json_encode($items); ?>


   ga('ecommerce:send');
</script>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "R6i6CNqhgwoQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=1.000000&amp;label=R6i6CNqhgwoQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>



<script>
 dataLayer.push({
   'dynx_itemid'   : {!! $json_item !!},
   'dynx_pagetype'  : 'thankyou',
   'dynx_totalvalue' : '{{ $marketing_data['grand_total'] }}'
 });
</script>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "IilACMLArQkQvq7K1QM";
var google_conversion_value = 33;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/984782654/?label=IilACMLArQkQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 973989159;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "tYXcCIn84gkQp8q30AM";
var google_conversion_value = 33;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/973989159/?label=tYXcCIn84gkQp8q30AM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 952049652;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "_aQRCNbjpFkQ9L_8xQM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/952049652/?label=_aQRCNbjpFkQ9L_8xQM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "R6i6CNqhgwoQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=1.000000&amp;label=R6i6CNqhgwoQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script>(function() {
var _fbq = window._fbq || (window._fbq = []);
if (!_fbq.loaded) {
var fbds = document.createElement('script');
fbds.async = true;
fbds.src = '//connect.facebook.net/en_US/fbds.js';
var s = document.getElementsByTagName('script')[0];
s.parentNode.insertBefore(fbds, s);
_fbq.loaded = true;
}
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', '6025380820192', {'value':'0.01','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6025380820192&amp;cd[value" /></noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 928189419;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "YEvnCJ7Cx2UQ65fMugM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/928189419/?label=YEvnCJ7Cx2UQ65fMugM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

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

<!-- 1. Offer Conversion: HB1: Hijabenka Affiliate Program GENERAL -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=10&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 2. Offer Conversion: HBM1: Hijabenka Affiliate Monthly Campaign 1 | November Special 10% no minimum payment -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=106&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 3. Offer Conversion: HBM2: Hijabenka Affiliate Monthly Campaign 2 | September Treats 15% with minimum payment Rp 200.000 -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=108&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 4. Offer Conversion: HBM3: Hijabenka Affiliate Monthly Campaign 3 -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=110&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 5. Offer Conversion: HBW1: Hijabenka Affiliate Weekly Campaign 1 |  Discount 75Ribu min pembelian 350Ribu -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=100&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 6. Offer Conversion: HBW2: Hijabenka Affiliate Weekly Campaign 2 | Big Sale Up To 90% -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=104&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 7. Offer Conversion: HBW3: Hijabenka Affiliate Weekly Campaign 3 | Discount 15% no minimum -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=102&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!--end hasoffers tag-->


<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1031174190232735');
fbq('track', 'PageView');

     fbq('track', 'Purchase', {value: '{{ $marketing_data['grand_total'] }}', currency: 'IDR'});

</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1031174190232735&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->