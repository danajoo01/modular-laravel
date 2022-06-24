<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : '';
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
  ga('create', 'UA-49302532-1', 'auto');
  ga('send', 'pageview');
</script>
<!-- GCM AND MOENGAGE -->
<script src="https://www.gstatic.com/firebasejs/3.4.1/firebase.js"></script>
<script>
// Initialize Firebase
var config = {
	apiKey: "AIzaSyC1xX3C3mrwh8wPZZyQTv5wWfLcHq4YVr8",
	authDomain: "hijabenka-146104.firebaseapp.com",
	databaseURL: "https://hijabenka-146104.firebaseio.com",
	storageBucket: "hijabenka-146104.appspot.com",
	messagingSenderId: "1011226584062"
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
            app_id: "1EOOB34KZ70VIA8S729C3ENG",
            debug_logs: 0
    });
}
</script>
<iframe id="moengage_iframe" src="https://hijabenka.moengage.com/" style="display:none;"></iframe>
*/?>

<!-- GCM AND MOENGAGE -->  

<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 973989159;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/973989159/?value=0&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

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
    _fbq.push(['addPixelId', '1031174190232735']);
  })();
  window._fbq = window._fbq || [];
  window._fbq.push(['track', 'PixelInitialized', {}]);
  </script>
  <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1031174190232735&amp;ev=PixelInitialized" /></noscript>

  <script type="text/javascript">
  /* <![CDATA[ */
  var google_conversion_id = 952049652;
  var google_conversion_label = "g1dWCICvoVkQ9L_8xQM";
  var google_custom_params = window.google_tag_params;
  var google_remarketing_only = true;
  /* ]]> */
  </script>
  <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
  </script>
  <noscript>
  <div style="display:inline;">
  <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/952049652/?value=1.00&amp;currency_code=USD&amp;label=g1dWCICvoVkQ9L_8xQM&amp;guid=ON&amp;script=0"/>
  </div>
  </noscript>

