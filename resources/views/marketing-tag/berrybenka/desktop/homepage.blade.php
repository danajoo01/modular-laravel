<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<script type="text/javascript">
    <!-- datalayer for GTM -->
    dataLayer.push({ 
        'PageType': 'Homepage', 
        'HashedEmail': '{{ $user_email }}', 
    });    
</script>


<!-- IMPRESSION PAGE -->
<script type="text/javascript">

  /*ga('create', 'UA-22337758-1', 'berrybenka.com');

  ga('require', 'ec');

  ga('ec:addImpression', {
    'id': '16291', // Product details are provided in an impressionFieldObject.
    'name': 'Amore',
    'category': 'ballerina-flats',
    'brand': 'Alive',
    'variant': '',
    'list': 'Homepage',
    'position': 1 // 'position' indicates the product position in the list.
  });
  ga('ec:addImpression', {
    'id': '17525', // Product details are provided in an impressionFieldObject.
    'name': 'Berre',
    'category': 'ballerina-flats',
    'brand': 'Alive',
    'variant': '',
    'list': 'Homepage',
    'position': 2 // 'position' indicates the product position in the list.
  });
  ga('ec:addImpression', {
    'id': '127260', // Product details are provided in an impressionFieldObject.
    'name': 'ESENE TWO WAY CAKE - CARAMEL',
    'category': 'skin-care',
    'brand': 'ESENE',
    'variant': '',
    'list': 'Homepage',
    'position': 3 // 'position' indicates the product position in the list.
  });
  ga('ec:addImpression', {
    'id': '126108', // Product details are provided in an impressionFieldObject.
    'name': 'Eye Pencil Black',
    'category': 'eye-makeup',
    'brand': 'JAFRA',
    'variant': '',
    'list': 'Homepage',
    'position': 4 // 'position' indicates the product position in the list.
  });
  ga('ec:addImpression', {
    'id': '126083', // Product details are provided in an impressionFieldObject.
    'name': 'Tje Fuk FOE-2 Whitening Cream Day  Night - 15 gr',
    'category': 'skin-care',
    'brand': 'Tje Fuk',
    'variant': '',
    'list': 'Homepage',
    'position': 5 // 'position' indicates the product position in the list.
  });
  ga('ec:addImpression', {
    'id': '104434', // Product details are provided in an impressionFieldObject.
    'name': 'La Palette LED Curing Lamp',
    'category': 'nails',
    'brand': 'La Palette',
    'variant': '',
    'list': 'Homepage',
    'position': 6 // 'position' indicates the product position in the list.
  });
   

  ga('send', 'pageview'); // Send product impressions with initial pageview.*/

  
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

<!-- RTB House Homepage --> 
<iframe  src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_home" width="1" height="1"  scrolling="no"  frameBorder="0"
style="display: none;"></iframe>
<!--– End of RTB House Homepage -->

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

