<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';

$generate_uri_segment   = generate_uri_segment();//bb_debug($generate_uri_segment);//die;
$parent_type_url        = $generate_uri_segment['parent_type_url'] ? $generate_uri_segment['parent_type_url'] : '';
$child_type_url         = $generate_uri_segment['child_type_url'] ? $generate_uri_segment['child_type_url'] : '';
$sub_child_type_url     = $generate_uri_segment['sub_child_type_url'] ? $generate_uri_segment['sub_child_type_url'] : '';

$segment_1              = (\Request::segment(1)) ? \Request::segment(1) : NULL;  
if(isset($segment_1) && ($segment_1 == 'new-arrival' || $segment_1 == 'sale')){    
    $CatIDforRTBHouse   = $segment_1;
}else{
    $CatIDforRTBHouse   = $parent_type_url;   
}
if($child_type_url != ''){
    $CatIDforRTBHouse   = $child_type_url;
    if($sub_child_type_url != ''){
        $CatIDforRTBHouse   = $sub_child_type_url;    
    }    
}

//get category last child as array

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


<script type='text/javascript'>
<!--  GA Product tracking -->
ga('create', 'UA-22337758-1', 'berrybenka.com');
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

/*     console.log(id);
     console.log(name);
     console.log(category);
     console.log(brand);
     console.log(variant);
     console.log(position);
     console.log(ref);
     console.log(url);
     return false;*/

    ga('ec:addProduct', {
        'id'      : id,
        'name'    : name,
        'category': category,
        'brand'   : brand,
        'variant' : variant,
        'position': position
    });

    // Set Action
    ga('ec:setAction', 'click', {list: ref});

    // Send click with an event.
    ga('send', 'event', 'UX', 'click', 'Results', {
        hitCallback: function() {
//            document.location = url;
        }
    });
}

<!-- banner catalog click -->
function onBannerCatalogClick(template_title,banner_name) {
    ga('send','event','Banner Catalog','click',template_title + '#' + banner_name,1);
}
<!-- end banner catalog click -->

<!-- IMPRESSION PAGE -->
if(typeof impression_data0192e3 == 'undefined') {
        var impression_data0192e3 = [];
}

dataLayer.push({
    'ecommerce': {
        'currencyCode': 'IDR',
        'impressions': impression_data0192e3
    }
});
<!-- END IMPRESSION PAGE -->
<!-- s: FACEBOOK REMARKETING -->
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

<!-- e: FACEBOOK REMARKETING -->

<!-- e: Listing tag page -->

</script>

<!-- s: FACEBOOK REMARKETING -->
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&amp;ev=PixelInitialized" /></noscript>
<!-- e: FACEBOOK REMARKETING -->

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


<!--  GA Product tracking ---->
<script>

</script>
<!-- e: GA Product tracking ---->

<!-- RTB House Category Page --> 
<iframe src="//asia.creativecdn.com/tags?id=pr_QVBoOhP0iAXuw71oerc0_category2_{{ $CatIDforRTBHouse }}" width="1" height="1" scrolling="no" frameBorder="0"
style="display: none;"></iframe>