<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';

$var_ordered_id = implode(',', array_map(function ($entry) {
  return $entry['id'];
}, isset($tag_products) ? $tag_products : null));

$string_id_rtb = isset($var_ordered_id) ? $var_ordered_id : 0;

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

<script type="text/javascript">
    ga('require', 'ecommerce', 'ecommerce.js');

    ga('ecommerce:addTransaction', {
       id          : '{{ $marketing_data['purchase_code'] }}', // Transaction ID
       affiliation : 'Berrybenka', // Affiliation or store name
       revenue     : {{ $marketing_data['grand_total'] }}, // Grand Total
       shipping    : {{ $marketing_data['shipping'] }} , // Shipping cost
       tax         : {{ $marketing_data['tax'] }}, // Tax.
       city        : '{{ $marketing_data['city'] }}', // City
       province    : '{{ $marketing_data['province'] }}', // state or province
       country     : 'INDONESIA'
    }); // country

    @foreach($carts as $row)
    ga('ecommerce:addItem', {
       id       : '{{ $marketing_data['purchase_code'] }}', // Transaction ID.
       sku      : '{{ $row['SKU'] }}', // SKU/code.
       name     : '{{ $row['name'] }}', // Product name.
       category : '', // Category or variation.
       price    : '{{ $row['price'] }}', // Unit price.
       quantity : '{{ $row['qty'] }}'}
    ); // Quantity.
    @endforeach

    ga('ecommerce:send');
  </script>

  <script type="text/javascript">
    var fb_param = {};
    fb_param.pixel_id = '6009647096207';
    fb_param.value = '0.00';
    fb_param.currency = 'USD';
    (function() {
        var fpw = document.createElement('script');
        fpw.async = true;
        fpw.src = '//connect.facebook.net/en_US/fp.js';
        var ref = document.getElementsByTagName('script')[0];
        ref.parentNode.insertBefore(fpw, ref);
    })();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6009647096207&amp;value=0&amp;currency=USD" />
</noscript>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 993328875;
    var google_conversion_language = "en";
    var google_conversion_format = "2";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "0AxMCK2NjAQQ6_3T2QM";
    var google_conversion_value = 0;
    /* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/993328875/?value=0&amp;label=0AxMCK2NjAQQ6_3T2QM&amp;guid=ON&amp;script=0" />
    </div>
</noscript>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 984782654;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "ATWcCNKzywgQvq7K1QM";
    var google_conversion_value = 0;
    var google_remarketing_only = false;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/984782654/?value=0&amp;label=ATWcCNKzywgQvq7K1QM&amp;guid=ON&amp;script=0" />
    </div>
</noscript>

<script>
dataLayer.push({
  'ecommerce': {
    'purchase': {
      'actionField': {
        'id': '{{ $marketing_data['purchase_code'] }}',// Transaction ID. Required for purchases and refunds.
        'affiliation': 'Berrybenka',
        'revenue': '{{ $marketing_data['grand_total'] }}',// Total transaction value (incl. tax and shipping)
        'tax':'{{ $marketing_data['tax'] }}',
        'shipping': '{{ $marketing_data['shipping'] }}',
        'coupon': ''
      },
      'products': {"name":"Edvia Top Cream","id":"124441","price":"129000","brand":"Berrybenka Label","category":null,"variant":"Cream","quantity":"1","coupon":false}        }
  }
});
</script>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 958968193;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "K6e9CMCtl2MQgeOiyQM";
    var google_remarketing_only = false;
    /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.
        com/pagead/conversion/958968193/?label=K6e9CMCtl2MQgeOiyQM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 927344594;
    var google_conversion_language = "en";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "nO89CIHuwmUQ0s-YugM";
    var google_remarketing_only = false;
    /* ]]> */
    </script>
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/927344594/?label=nO89CIHuwmUQ0s-YugM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>

<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_orderstatus2_129045_2276063718442_124441&amp;cd=default" width="1" height="1"  scrolling="no"  frameBorder="0"  style="display: none;"></iframe>

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
    fbq('track', 'Purchase', {value: '{{ $marketing_data['grand_total'] }}', currency: 'IDR'});
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->


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
<!-- 1. Offer Conversion: BB1: Berrybenka Affiliate Program GENERAL -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=1&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 2. Offer Conversion: BBW1: Berrybenka Affiliate Weekly Campaign 1 | 15% No Min -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=98&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 3. Offer Conversion: BBW2: Berrybenka Affiliate Weekly Campaign 2 | Disc 75Ribu min pembelian 350Ribu -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=90&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 4. Offer Conversion: BBW3: Berrybenka Affiliate Weekly Campaign 1 | #BFF Promo 3 days -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=88&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 5. Offer Conversion: BBM1: Berrybenka Affiliate Monthly Campaign 1 | October Feast 10% no minimum payment -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=94&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 6. Offer Conversion: BBW3: Berrybenka Affiliate Weekly Campaign 3 -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=92&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!-- 7. Offer Conversion: BBM3: Berrybenka Affiliate Monthly Campaign 2 | Big Sale up to 90% -->
<img src="http://berrybenka.go2cloud.org/aff_l?offer_id=96&adv_sub={{ $marketing_data['purchase_code'] }}&adv_sub2={{ $hasoffer_data['id'] }}&adv_sub3={{ $hasoffer_data['brand'] }}&adv_sub4={{ $hasoffer_data['parent_category'] }}&amount={{ $marketing_data['grand_total'] }}" width="1" height="1" />

<!--end hasoffers tag-->

<!-- RTB thank you page -->
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_orderstatus2_{{ $marketing_data['grand_total'] }}_{{ $marketing_data['purchase_code'] }}_{{ $string_id_rtb }}&amp;cd=default"
width="1" height="1" scrolling="no" frameBorder="0" style="display: none;"></iframe>