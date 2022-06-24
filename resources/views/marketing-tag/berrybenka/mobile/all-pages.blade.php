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

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 993328875;
    var google_conversion_label = "3UhhCLWMjAQQ6_3T2QM";
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/993328875/?value=0&amp;label=3UhhCLWMjAQQ6_3T2QM&amp;guid=ON&amp;script=0" />
    </div>
</noscript>

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 984782654;
    var google_conversion_label = "p1McCKqDzAgQvq7K1QM";
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/984782654/?value=0&amp;label=p1McCKqDzAgQvq7K1QM&amp;guid=ON&amp;script=0" />
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
        _fbq.push(['addPixelId', '256929811160719']);
    })();
    window._fbq = window._fbq || [];
    window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=256929811160719&amp;ev=PixelInitialized" />
</noscript>

