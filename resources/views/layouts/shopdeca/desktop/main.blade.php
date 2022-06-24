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
    <meta name="author" content="shopdeca.com">
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
            window.location.href="{{ getEnvMobile() }}" + window.location.pathname;
    } else if (isNon) {
            
    }; 

    </script>
    
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/core.css?t=').date('YmdHis') }}"><!-- ?t=').date('YmdHis') -->
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/search.css?t=').date('YmdHis') }}">    
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/jqueryui.css') }}">
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('shopdeca/desktop/script/scrollable/jquery.scrollable.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('shopdeca/desktop/script/fancybox/jquery.fancybox.css') }}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ asset('shopdeca/desktop/css/jquery.fancybox.css') }}" media="screen" />
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/catalog.css">-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('shopdeca/desktop/img/favicon.png') }}" />

    @yield('css')
    @yield('marketing-tag-header','')
</head>
<body>
    @yield('marketing-tag-body','')
    @include('layouts.shopdeca.desktop.header')

    @yield('content')
        
    <!-- /#page-wrapper   -->
    @include('layouts.shopdeca.desktop.footer')    
    
    <input type="hidden" id="tracking-server" class="tracking-server" value="{{ get_server_address() }}" />
    <input type="hidden" id="dir" value="{{ isset($dir) ? $dir : "DIR not found" }}" />
</body>
<script type="text/javascript">var dataLayer = [];</script>
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<!-- <script src="{{ asset('shopdeca/theme/script/sidebar-fixed.js') }}"></script> -->
<?php if (getAppEnv() == 'development' && getMarketingEnv() == true) : ?>
<script type="text/javascript" src="{{ asset('shopdeca/desktop/script/jslog.js') }}"></script>
<?php endif; ?>
<!-- BB JS -->
<script type="text/javascript" src="{{ asset('shopdeca/desktop/script/sticky-side.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/autocomplete.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/scrollable/jquery.scrollable.js') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/fancybox/jquery.fancybox.pack.js') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/core.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('shopdeca/desktop/script/lazyload.js') }}"></script>
<script src="{{ asset('js/desktop/app.js?t=').date('YmdHis') }}"></script>
<!-- <script src="{{ asset('shopdeca/desktop/script/global.js') }}"></script> -->

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

@include('marketing-tag.global-data', ['catalogs' => !empty($catalog) ? $catalog : [], 'ref' => !empty($ref) ? $ref : ''])

@if(getMarketingEnv() == true)
    @include('marketing-tag.shopdeca.desktop.all-pages')
    {{-- @include('olark.olark-bb')--}}
@endif



@if (isset($_GET['substa']))
    @if ($_GET['substa'] == 'exist' || $_GET['substa'] == 'invalid')
        <script type="text/javascript">
            alert('Mohon maaf email Anda sudah terdaftar di sistem Kami. Untuk pertanyaan lebih lanjut silahkan hubungi Customer Service team kami.');
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
    @include('marketing-tag.shopdeca.desktop.gtm')
@endif
</html>

