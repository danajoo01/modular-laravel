<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<script type="text/javascript">
//var dataLayer = dataLayer || []; 
dataLayer.push({ 
    'PageType': 'Homepage', 
    'HashedEmail': '{{ $user_email }}', 
});    
</script>


<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_home" width="1" height="1"  scrolling="no"  frameBorder="0" style="display: none;"></iframe>

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
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=256929811160719&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->

<!-- RTB House Homepage --> 
<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_home" width="1" height="1"  scrolling="no"  frameBorder="0"
style="display: none;"></iframe>
<!--– End of RTB House Homepage -->