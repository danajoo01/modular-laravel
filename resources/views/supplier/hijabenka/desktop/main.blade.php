<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:fb="http://ogp.me/ns/fb#"
      itemscope itemtype="http://schema.org/Product">
<head>
    <?php 
    //SEO HELPER
    $seo = DefineSeo(); 
    $title_seo = stripslashes($seo["title"]);
    $keywords_seo = $seo["keywords"];
    $description_seo = $seo["description"];
    $alternate = '<link rel="alternate" href="'.$seo['alternate'].'" />';
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
    
    <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/core.css?t=').date('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/jqueryui.css') }}">
    <link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/font-awesome.min.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="http://berrybenka.com/assets/berrybenka/desktop/img/favicon.png" />

    @yield('css')

</head>
<body>

    @include('supplier.berrybenka.desktop.header')

    @yield('content')
        
    <!-- /#page-wrapper -->

    @include('supplier.berrybenka.desktop.footer')    
    
    <input type="hidden" id="tracking-server" class="tracking-server" value="{{ get_server_address() }}" />
</body>
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<!-- <script src="{{ asset('berrybenka/theme/script/sidebar-fixed.js') }}"></script> -->

<!-- BB JS -->
<script src="{{ asset('js/jquery-ui.js') }}"></script>

@yield('js')

@yield('marketing-tag')

</html>

