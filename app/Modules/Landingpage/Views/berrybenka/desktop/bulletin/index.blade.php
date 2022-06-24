<!doctype html>  
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<head>
    <?php 
    //SEO HELPER
    $seo                = DefineSeo(); 
    $title_seo          = stripslashes($seo["title"]);
    $keywords_seo       = $seo["keywords"];
    $description_seo    = $seo["description"];
    $footer_seo         = isset($seo["footer_text"]) ? $seo["footer_text"] : '';
    $alternate          = '<link rel="alternate" href="'.$seo['alternate'].'" />';    
    ?>
    <title>{!! $title_seo !!}</title> 
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="keywords" content="{!! $keywords_seo !!}">
    <meta name="description" content="{!! $description_seo !!}">
    <meta name="revisit-after" content="7 days" />
    <meta name="author" content="berrybenka.com">
    <meta name="google-site-verification" content="Ea_eO8eyxg_9GhRxWqQaGoNBHg0rZFRT84ovScDXR48" />
    {!! $alternate !!}
        
        
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="fragment" content="!">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="_token" content="{!! csrf_token() !!}"/>

    @yield('meta')
    
    <script>
    
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPod/i); 
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };

    var isNon = {
            iPad: function() {
            return navigator.userAgent.match(/iPad/i);
            }
    };
    
    if (isMobile.any()) {
            window.location.href="http://{{ getEnvMobile() }}";
    } else if (isNon) {
            
    }; 

    </script>
    
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('berrybenka/desktop/img/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/landingpage/bulletin.css') }}">    
    @yield('marketing-tag-header','')
</head>

<body class="cf">

  <!-- <header class="span1">
  </header>
 -->
  <div id="container" class="cf">  

  	<article class="span1">
      <section>
        <div class="center-column">
          <a href="https://berrybenka.onelink.me/3544722419?pid=blackberry&c=download_app&af_dp=berrybenka%3A%2F%2F&af_web_dp=http%3A%2F%2Fberrybenka.com%2F">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin1.jpg">
          </a>
        </div>
        <div class="left-column">
          <a href="http://berrybenka.com/special/4671/pakaian-wanita-mulai-dari-49ribu?utm_source=bbm&utm_campaign=bannerA_pakaian_mulai_49ribu&utm_medium=cpm">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin2.jpg">
          </a>
        </div>
        <div class="right-column">
          <a href="http://berrybenka.com/special/4673/pakaian-pria-mulai-dari-69ribu?utm_source=bbm&utm_campaign=bannerB_pakaian_pria_mulai_69ribu&utm_medium=cpm">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin3.jpg">
          </a>
        </div>
        <div class="left-column">
          <a href="http://berrybenka.com/special/4672/tas-dan-sepatu-mulai-dari-69ribu?utm_source=bbm&utm_campaign=bannerC_tas_sepatu_mulai_69ribu&utm_medium=cpm">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin4.jpg">
          </a>
        </div>
        <div class="right-column">
          <a href="http://hijabenka.com/special/4674/pakaian-muslim-dan-jilbab-mulai-dari-25ribu?utm_source=bbm&utm_campaign=bannerD_pakaian_muslim_jilbab_mulai_25ribu&utm_medium=cpm">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin5.jpg">
          </a>
        </div>
        <div class="center-column">
          <a href="http://berrybenka.com/special/4675/voucher-eksklusif-20-persen-untuk-pengguna-bbm?utm_source=bbm&utm_campaign=bannerE_voucher_eksklusif_diskon20&utm_medium=cpm">
            <img src="/berrybenka/desktop/img/bulletin-ads/BBMBulletin6.jpg">
          </a>
        </div>
      </section>
    </article>

  </div> <!--! end of #container -->

  <footer>
    
  </footer>

</body>
</html>

