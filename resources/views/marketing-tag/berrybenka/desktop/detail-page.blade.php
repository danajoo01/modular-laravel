<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';
?>
<!-- s: FACEBOOK REMARKETING -->
<script>
(function() {
    var _fbq = window._fbq || (window._fbq = []);
    if (!_fbq.loaded) 
    {
        var fbds = document.createElement('script');
        fbds.async = true;
        fbds.src = '//connect.facebook.net/en_US/fbds.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(fbds, s);
        _fbq.loaded = true;
    }
    _fbq.push(['addPixelId', '256929811160719']);
})();
</script>

<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&amp;ev=PixelInitialized" /></noscript>
<!-- e: FACEBOOK REMARKETING -->

<!-- IMPRESSION PAGE -->
<script type="text/javascript">
  //ga('create', 'UA-22337758-1', 'berrybenka.com');
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
   

  ga('send', 'pageview'); // Send product impressions with initial pageview.

  
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

<!--  GA Product tracking ---->
<script>
dataLayer.push({
  'ecommerce': {
    'detail': {
      'actionField': {'list': 'clothing'},    
      'products': [{
        'name': 'Driska Dress Khaki',
        'id': '131264',
        'brand': 'Stratto'
       }]
     }
   }
});
</script>
<!-- e: GA Product tracking ---->

<!-- start: Remarketing Facebook -->
<script>
  $(document).ready(function(){  
      window._fbq = window._fbq || [];
      window._fbq.push(['track', 'ViewContent', {
        content_ids: [detail_product336CC993E54E.product_id],
        content_type: 'product'
      }]);
  });
</script>
<!-- end: Remarketing Facebook -->  

<!--  GA Product tracking ---->
<script>
dataLayer.push({
  'ecommerce': {
    'detail': {
      'actionField': {'list': 'clothing'},    
      'products': [{
        'name': detail_product336CC993E54E.product_name,
        'id': detail_product336CC993E54E.product_id,
        'brand': detail_product336CC993E54E.brand_name
       }]
     }
   }
});
</script>
<!-- e: GA Product tracking ---->

<!-- start: Remarketing Facebook -->
<script>
  $(document).ready(function(){  
      window._fbq = window._fbq || [];
      window._fbq.push(['track', 'ViewContent', {
        content_ids: [detail_product336CC993E54E.product_id],
        content_type: 'product'
      }]);
  });
</script>
<!-- end: Remarketing Facebook -->  