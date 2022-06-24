<?php 
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';
?>
<!-- datalayer for GTM -->
<script type="text/javascript">    
    dataLayer.push( {
        'PageType': 'Submitorderpage', 
        'HashedEmail': '{{ $user_email }}', 
        'BasketProducts': marketing336CC993E54E
    }
);
</script>

<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "i8BXCML8hwoQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=1.000000&amp;label=i8BXCML8hwoQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<script>
 $('[name="google_conversion_frame"]').attr("style","position:absolute");
</script>

<!-- Facebook Conversion Code for Conversion Pixel - Submit Order -->
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
window._fbq.push(['track', '6022000643207', {'value':'0.00','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6022000643207&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

<!-- start: Remarketing Facebook -->

<script>
function reportPurchase() {
window._fbq = window._fbq || [];
window._fbq.push(['track', 'Purchase', {
  content_ids: detail_product336CC993E54E.product_ids,
  content_type: 'product'
}]);
}
</script>

<!-- end: Remarketing Facebook -->

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '256929811160719');
fbq('track', 'PageView');


    fbq('track', 'InitiateCheckout');

</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->


<!-- RTB House Start Order --> 
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_startorder" width="1" height="1" scrolling="no" frameBorder="0"
style="display: none;"></iframe>
