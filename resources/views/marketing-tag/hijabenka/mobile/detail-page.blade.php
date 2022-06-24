<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>

<!-- datalayer for GTM -->
<script type="text/javascript">
    dataLayer.push({ 
        'PageType': 'Productpage', 
        'HashedEmail': '{{ $user_email }}', 
        'ProductID': detail_product336CC993E54E.product_id,
        'CategoryID': detail_product336CC993E54E.product_frontendtypeID,
        'CategoryName': detail_product336CC993E54E.product_frontendtypeName,
        'BrandID': detail_product336CC993E54E.brand_id,
        'BrandName': detail_product336CC993E54E.brand_name
    });     
</script>


<!--  Product page tag ---->

<script>
  $(document).ready(function(){  
    window._fbq = window._fbq || [];
    window._fbq.push(['track', 'ViewContent', {
		content_ids: [detail_product336CC993E54E.product_id],
		content_type: 'product'
    }]);
  });
</script>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1031174190232735');
fbq('track', 'PageView');

     fbq('track', 'ViewContent');

</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1031174190232735&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->
