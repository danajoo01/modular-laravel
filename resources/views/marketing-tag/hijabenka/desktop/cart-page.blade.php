<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<script>
 var grand_total = $('#raw-grandtotal-value').val();

 dataLayer.push({
   'dynx_itemid'   : {!! $cart_data !!},
   'dynx_pagetype'  : 'cart',
   'dynx_totalvalue' : grand_total
 });
</script>

<!-- datalayer for GTM -->
<script type="text/javascript">    
    dataLayer.push( {
        'PageType': 'Basketpage', 
        'HashedEmail': '{{ $user_email }}', 
        'BasketProducts': cart336CC993E54E.cart
    }
);
</script>

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
  _fbq.push(['addPixelId', '256929811160719']);
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&amp;ev=NoScript" /></noscript>


<script>
(function() {
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
window._fbq.push(['track', '6026259379992', {'value':'0.01','currency':'USD'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6026259379992&amp;cd[value]=0.01&amp;cd[currency]=USD&amp;noscript=1" /></noscript>
<script>
 /* Set AddToCart pixel event */
 window._fbq = window._fbq || [];
    window._fbq.push(['track', 'AddToCart', {
   content_ids: {!! $cart_data !!},
   content_type: 'product'
 }]);
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

     fbq('track', 'AddToCart');

</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1031174190232735&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->
