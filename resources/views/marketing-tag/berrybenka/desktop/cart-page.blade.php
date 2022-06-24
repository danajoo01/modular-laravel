<?php 
$user           = \Auth::user();
$user_email     = !empty($user->customer_email) ? $user->customer_email : '';

//get marketing value
$jsonmarketing  = json_decode($marketing_data, TRUE);
$var_cart_id    = implode(',', array_map(function ($entry) {
  return $entry['id'];
}, isset($jsonmarketing) ? $jsonmarketing : null));
$string_id_rtb  = isset($var_cart_id) ? $var_cart_id : 0;

?>
<!-- datalayer for GTM -->
<script type="text/javascript">    
    dataLayer.push( {
        'PageType': 'Basketpage', 
        'HashedEmail': '{{ $user_email }}', 
        'BasketProducts': cart336CC993E54E.cart
    }
);
</script>
<!-- datalayer push cart -->

<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "elSoCPLxggoQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=1.000000&amp;label=elSoCPLxggoQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<script>
 $('[name="google_conversion_frame"]').attr("style","position:absolute");
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
fbq('track', 'AddToCart');
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->

<!-- RTB House Cart --> 
<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_basketstatus_{{ $string_id_rtb }}" width="1" height="1"
scrolling="no"  frameBorder="0"  style="display: none;"></iframe>