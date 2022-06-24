<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<!-- datalayer for GTM -->
<script type="text/javascript"> 
if(typeof criteo_data0192e3 == 'undefined') {
        var criteo_data0192e3 = [];
}
if(typeof childCatIdGTMList_data0192e3 == 'undefined') {
        var childCatIdGTMList_data0192e3 = [];
}
if(typeof childCatNameGTMList_data0192e3 == 'undefined') {
        var childCatNameGTMList_data0192e3 = [];
}
if(typeof brandIdGTMList_data0192e3 == 'undefined') {
        var brandIdGTMList_data0192e3 = [];
}
if(typeof brandNameGTMList_data0192e3 == 'undefined') {
        var brandNameGTMList_data0192e3 = [];
}
if(typeof catalog_data930ad9 == 'undefined') {
        var catalog_data930ad9 = [];
}

dataLayer.push( {
    'PageType': 'Categorypage', 
    'HashedEmail': '{{ $user_email }}', 
    'ProductIDList': criteo_data0192e3,
    'CategoryIDList': childCatIdGTMList_data0192e3,
    'CategoryNameList': childCatNameGTMList_data0192e3,
    'BrandIDList': brandIdGTMList_data0192e3,
    'BrandNameList' : brandNameGTMList_data0192e3
}); 
</script>

<script>
  $(document).ready(function(){  
    window._fbq = window._fbq || [];
    window._fbq.push(['track', 'ViewCatalog', {
      content_ids: criteo_data0192e3,
      content_type: 'product'
    }]);
  });
</script>

<!-- IMPRESSION PAGE -->
<script type="text/javascript">
  ga('create', 'UA-49302532-1', 'hijabenka.com');
  ga('require', 'ec');

  $.each(catalog_data930ad9, function(index, value) {
      ga('ec:addImpression', {
          'id'      : value.pid, // Product details are provided in an impressionFieldObject.
          'name'    : value.name,
          'category': value.category,
          'brand'   : value.brand,
          'variant' : '',
          'list'    : value.ref,
          'position': value.index // 'position' indicates the product position in the list.
      });
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
        //document.location = url;
      }
    });
  }      

</script>
<!-- END OF IMPRESSION PAGE -->

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
    _fbq.push(['addPixelId', '1031174190232735']);
})();
</script>

<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1031174190232735&amp;ev=PixelInitialized" /></noscript>
<!-- e: FACEBOOK REMARKETING -->