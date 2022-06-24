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

<!-- IMPRESSION PAGE -->
<script type="text/javascript">
// Called when a link to a product is clicked.
  function onProductClick(id, name, category, brand, variant, position, ref, url) {

    // console.log(id);
    // console.log(name);
    // console.log(category);
    // console.log(brand);
    // console.log(variant);
    // console.log(position);
    // console.log(ref);
    // console.log(url);
    // return false;
    
    ga('ec:addProduct', {
      'id': id,
      'name': name,
      'category': category,
      'brand': brand,
      'variant': variant,
      'position': position
    });

    // Set Action
    ga('ec:setAction', 'click', {list: ref});

    // Send click with an event.
    ga('send', 'event', 'UX', 'click', 'Results', {
      hitCallback: function() {
        document.location = url;
      }
    });
  }      

  </script>
  <!-- END OF IMPRESSION PAGE -->

  <!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1031174190232735', {
em: '{!! $user_email !!}',
});
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none;" src="https://www.facebook.com/tr?id=1031174190232735&ev=PageView&noscript=1" /></noscript>
<!-- End Facebook Pixel Code -->