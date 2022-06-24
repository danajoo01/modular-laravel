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

<script>
//  dataLayer.push({
//    'ecommerce': {
//      'checkout': {
//        'actionField': {'step': 1},
//        'products':      }
//   }
//  });
</script>

<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_startorder" width="1" height="1"  scrolling="no"  frameBorder="0" style="display: none;"></iframe>


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
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript> <!-- End Facebook Pixel Code -->

<!-- RTB House Start Order --> 
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_startorder" width="1" height="1" scrolling="no" frameBorder="0"
style="display: none;"></iframe>