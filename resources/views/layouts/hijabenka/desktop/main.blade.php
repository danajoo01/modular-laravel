<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://ogp.me/ns/fb#"
      itemscope itemtype="http://schema.org/Product">
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
    <meta name="author" content="hijabenka.com">
<!--    <meta name="google-site-verification" content="Ea_eO8eyxg_9GhRxWqQaGoNBHg0rZFRT84ovScDXR48" />-->
    <meta name="google-site-verification" content="rU4lsaddGLgVurmcBOrP4SVgWwBFE77Z51Ci41wcXsw" />
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
            window.location.href="{{ getEnvMobile() }}"  + window.location.pathname;
    } else if (isNon) {
            
    }; 

    </script>
    
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/core.css?t=').date('YmdHis') }}"><!-- ?t=').date('YmdHis') -->
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/search.css?t=').date('YmdHis') }}">    
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/jqueryui.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/script/scrollable/jquery.scrollable.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/flexslider/flexslider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('hijabenka/desktop/script/fancybox/jquery.fancybox.css') }}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ asset('hijabenka/desktop/css/jquery.fancybox.css') }}" media="screen" />
    <link href="https://fonts.googleapis.com/css?family=Didact+Gothic|Open+Sans:300,400,700" rel="stylesheet">
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/catalog.css">-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('hijabenka/desktop/img/favicon.png') }}" />        
    
    @yield('css')
    @yield('marketing-tag-header','')
    <link rel="manifest" href="https://hijabenka.api.useinsider.com/views/push/manifest/" />
</head>
<body>
    @yield('marketing-tag-body','')
    @include('layouts.hijabenka.desktop.header')

    @yield('content')
        
    <!-- /#page-wrapper   -->
    @include('layouts.hijabenka.desktop.footer')    
    
    <input type="hidden" id="tracking-server" class="tracking-server" value="{{ get_server_address() }}" />
    <input type="hidden" id="dir" value="{{ isset($dir) ? $dir : "DIR not found" }}" />
</body>
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<!-- <script src="{{ asset('hijabenka/theme/script/sidebar-fixed.js') }}"></script> -->
<?php if (getAppEnv() == 'development' && getMarketingEnv() == true) : ?>
<script type="text/javascript" src="{{ asset('hijabenka/desktop/script/jslog.js') }}"></script>
<?php endif; ?>
<!-- BB JS -->
<script type="text/javascript" src="{{ asset('hijabenka/desktop/script/sticky-side.js') }}"></script>
<script type="text/javascript" src="{{ asset('hijabenka/desktop/script/sticky.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/autocomplete.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/scrollable/jquery.scrollable.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/fancybox/jquery.fancybox.pack.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/core.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/lazyload.js') }}"></script>
<script src="{{ asset('js/desktop/app_bb.js?t=').date('YmdHis') }}"></script>
<!-- <script src="{{ asset('berrybenka/desktop/script/global.js') }}"></script> -->

<script type="text/javascript" src="{{ asset('hijabenka/desktop/flexslider/flexslider.js') }}"></script>
<script type="text/javascript">
    $('.flexslider').flexslider({
    animation: "slide",
        after: function(slider){
            var curSlide = slider.find("li.flex-active-slide");
            // get all the classes on the <li> element
            var bg_class = $(curSlide).attr('class');
            // remove the flex-active-slide class name
            bg_class = bg_class.replace("flex-active-slide", "");
            // remove all the classes
            $('body').removeClass('is-light');
            $('body').removeClass('is-dark');
            // add the active-slide
            $('body').addClass(bg_class);
        }
    });

    if ($(".home-sale").length > 0) {
        $('header').toggleClass('header-down');
        $("body").addClass("sales-banner");
    }

    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 50) {
            $(".header-down").addClass("scroll-header");
        } else {
            $(".header-down").removeClass("scroll-header");
        }

    });
    
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();
        if (scroll >= 50) {
            $("header").addClass("active-header");
            $("body").removeClass("sales-banner");
        } else {
            $("header").removeClass("active-header");
            $("body").addClass("sales-banner");
        }
    });
</script>

<script type="text/javascript">
  $(function(){
    $('#subscribe_women').click(function(){
      $('#subscriber_gender').val('1');
    });

    $('#subscribe_men').click(function(){
      $('#subscriber_gender').val('2');
    });
  })
</script>
<script type="text/javascript">var dataLayer = [];</script>


@include('marketing-tag.global-data', ['catalogs' => !empty($catalog) ? $catalog : [], 'ref' => !empty($ref) ? $ref : ''])

@if(getMarketingEnv() == true)
    <script src="{{ asset('js/marketing-js/global.js?t=').date('YmdHis') }}"></script>
    @include('marketing-tag.hijabenka.desktop.all-pages')
    {{--@include('olark.olark-bb')--}}
    @include('prism.prism-bb')
@endif


@if (isset($_GET['substa']))
    @if ($_GET['substa'] == 'exist' || $_GET['substa'] == 'invalid')
        <script type="text/javascript">
            alert('Error!! Email Anda gagal disimpan oleh sistem kami. Silakan dicoba kembali.');
        </script>    
    @elseif ($_GET['substa'] == 'success')
        <script type="text/javascript">
            alert('Success!! Email Anda berhasil disimpan oleh sistem kami.');
        </script>
    @endif
@endif

@yield('js')

@yield('marketing-tag')

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.gtm')
@endif
</html>

