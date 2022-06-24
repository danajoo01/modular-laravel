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


<script>
 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
         m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

 ga('create', 'UA-22337758-1', 'berrybenka.com');
 ga('require', 'displayfeatures');
 ga('send', 'pageview');
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
  id: '{{ $marketing_data['purchase_code'] }}', // Transaction ID.
  sku: '{{ $row['SKU'] }}', // SKU/code.
  name: '{{ $row['name'] }}', // Product name.
  category: '', // Category or variation.
  price: '{{ $row['price'] }}', // Unit price.
  quantity: '{{ $row['qty'] }}'}
 ); // Quantity.
 @endforeach


 ga('ecommerce:send');
</script>

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
fbq('track', 'Purchase', {value: '{{ $total_data['grand_total'] }}', currency: 'IDR'});
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->

<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "YHnwCNr0ggoQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=1.000000&amp;label=YHnwCNr0ggoQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<script>
 $('[name="google_conversion_frame"]').attr("style","position:absolute");
</script>

<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-22337758-1']);
_gaq.push(['_setDomainName', 'berrybenka.com']);
_gaq.push(['_trackPageview']);
</script>

<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 951096533;
var google_conversion_label = "plQeCNvx-QQQ1anCxQM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript"
 src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
 <div style="display: inline;">
  <img height="1" width="1" style="border-style: none;" alt=""
   src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/951096533/?value=0&amp;label=plQeCNvx-QQQ1anCxQM&amp;guid=ON&amp;script=0" />
 </div>
</noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "ATWcCNKzywgQvq7K1QM";
var google_conversion_value = 19;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/984782654/?value=1.000000&amp;label=ATWcCNKzywgQvq7K1QM&amp;guid=ON&amp;script=0"/>
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
window._fbq.push(['track', '6009647096207', {'value':'19','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6009647096207&amp;cd[value]=239236&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 993328875;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "0AxMCK2NjAQQ6_3T2QM";
var google_conversion_value = 19;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/993328875/?value=1.000000&amp;label=0AxMCK2NjAQQ6_3T2QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-22337758-1']);
_gaq.push(['_setDomainName', 'berrybenka.com']);
_gaq.push(['_trackPageview']);
</script>

<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 951096533;
var google_conversion_label = "plQeCNvx-QQQ1anCxQM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript"
 src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
 <div style="display: inline;">
  <img height="1" width="1" style="border-style: none;" alt=""
   src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/951096533/?value=0&amp;label=plQeCNvx-QQQ1anCxQM&amp;guid=ON&amp;script=0" />
 </div>
</noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "ATWcCNKzywgQvq7K1QM";
var google_conversion_value = 19;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/984782654/?value=1.000000&amp;label=ATWcCNKzywgQvq7K1QM&amp;guid=ON&amp;script=0"/>
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
window._fbq.push(['track', '6009647096207', {'value':'19','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6009647096207&amp;cd[value]=239236&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 993328875;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "0AxMCK2NjAQQ6_3T2QM";
var google_conversion_value = 19;
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/993328875/?value=1.000000&amp;label=0AxMCK2NjAQQ6_3T2QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<script>
dataLayer.push({
  'ecommerce': {
    'purchase': {
      'actionField': {
        'id': finalorder336CC993E54E.purchase_code,// Transaction ID. Required for purchases and refunds.
        'affiliation': 'Berrybenka',
        'revenue': '{{ $total_data['grand_total'] }}',// Total transaction value (incl. tax and shipping)
        'tax':'{{ $total_data['tax'] }}',
        'shipping': '{{ $total_data['shipping'] }}',
        'coupon': ''
      },
      'products': [
              @foreach($tag_products as $row)
              {
                <?php                  
                  $brand_name   = str_replace("'", "\\", $row['brand']);
                  $product_name = str_replace("'", "\\", $row['name']);
                ?>
                   "name":"{{ $product_name }}",
                   "id":"{{ $row['id'] }}",
                   "price":"{{ $row['price'] }}",
                   "brand":"{{ $brand_name }}",
                   "category":"{{ $row['category'] }}",
                   "variant":"{{ $row['variant'] }}",
                   "quantity":"{{ $row['quantity'] }}",
                  "coupon":false
              },
              @endforeach
      ]
    }
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
