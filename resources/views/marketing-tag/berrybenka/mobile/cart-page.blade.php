<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';

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
        'BasketProducts': marketing336CC993E54E
    }
);
</script>


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
    window._fbq.push(['track', '6022000585007', {
        'value': '0.00',
        'currency': 'USD'
    }]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6022000585007&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1" />
</noscript>

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
<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_basketstatus_{{ $var_cart_id }}" width="1" height="1" scrolling="no"  frameBorder="0"  style="display: none;"></iframe>
