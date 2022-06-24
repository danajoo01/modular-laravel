<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>

@if(isOthersPage() == true)
    <!-- datalayer for GTM -->
    <script type="text/javascript">
    dataLayer.push({ 
        'PageType': 'Otherpage', 
        'HashedEmail': '{{ $user_email }}', 
    });    
    </script>
@endif

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-22337758-1', 'auto');
  ga('send', 'pageview');

  googletag.cmd.push(function() {
      googletag.defineSlot('/13255522/Homepage_LookbookBanner_306x543', [306, 543], 'div-gpt-ad-1397113120835-0').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_MiniBanner_Left_Bottom_306x206', [306, 206], 'div-gpt-ad-1397113120835-1').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_MiniBanner_Left_Top_306x206', [306, 206], 'div-gpt-ad-1397113120835-2').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_MiniBanner_Right_306x360', [306, 360], 'div-gpt-ad-1397113120835-3').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_SaleBanner_Right_306x173', [306, 173], 'div-gpt-ad-1397113120835-4').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_SpecialPromo_Left_465x80', [465, 80], 'div-gpt-ad-1397113120835-5').addService(googletag.pubads());
      googletag.defineSlot('/13255522/Homepage_SpecialPromo_Right_465x80', [465, 80], 'div-gpt-ad-1397113120835-6').addService(googletag.pubads());
      googletag.pubads().enableSingleRequest();
      googletag.enableServices();
  });

</script>

<!-- GCM AND MOENGAGE -->
<script src="https://www.gstatic.com/firebasejs/3.4.1/firebase.js"></script>
<script>
// Initialize Firebase
var config = {
	apiKey: "AIzaSyA0uic8L1A9vuFr9lDmz9ky1Mx4E98T45c",
	authDomain: "berrybenka-3452b.firebaseapp.com",
	databaseURL: "https://berrybenka-3452b.firebaseio.com",
	storageBucket: "berrybenka-3452b.appspot.com",
	messagingSenderId: "628937040060"
};
firebase.initializeApp(config);
</script>


<?php /*
<script type="text/javascript" src="https://cdn.moengage.com/webpush/moe_webSdk.min.latest.js"></script>
<script type="text/javascript">
isChrome = function() {
    return Boolean(window.chrome);
}
// chrome only
if(isChrome){
    Moengage = moe({
            app_id: "5EJ223KSODB2TMXIN1VFC8IO",
            debug_logs: 0
    });    
}
</script>
<iframe id="moengage_iframe" src="https://berrybenka.moengage.com/" style="display:none;"></iframe>
 */?>

<!-- GCM AND MOENGAGE -->

<!-- s: FACEBOOK REMARKETING -->
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&amp;ev=PixelInitialized" /></noscript>
<!-- e: FACEBOOK REMARKETING -->

<!-- Google Code for Remarketing List Berrybenka -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 984782654;
var google_conversion_label = "p1McCKqDzAgQvq7K1QM";
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>

<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=0&amp;label=p1McCKqDzAgQvq7K1QM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<!-- Deqwas -->

 <!-- Google Code for Remarketing Tag -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 993328875;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>

<noscript>
<div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/993328875/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>