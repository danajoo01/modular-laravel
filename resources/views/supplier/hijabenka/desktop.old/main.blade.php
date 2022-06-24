<!doctype html>
<html>
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
    <meta charset="utf-8">
    <meta name="keywords" content="{!! $keywords_seo !!}">
    <meta name="description" content="{!! $description_seo !!}">
    <meta name="revisit-after" content="7 days" />
    <meta name="author" content="hijabenka.com">
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
    
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/core.css?t=').date('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/search.css?t=').date('YmdHis') }}">    
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/jqueryui.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/script/scrollable/jquery.scrollable.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('hijabenka/desktop/script/fancybox/jquery.fancybox.css') }}" media="screen" />
    <!--<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="assets/css/catalog.css">-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('hijabenka/desktop/img/favicon.png') }}" />

    @yield('css')

</head>
<body>
    @include('layouts.hijabenka.desktop.header')

    @yield('content')
        
    <!-- /#page-wrapper -->

    @include('layouts.hijabenka.desktop.footer')    
    
    <input type="hidden" id="tracking-server" class="tracking-server" value="{{ get_server_address() }}" />
</body>
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<!-- <script src="{{ asset('hijabenka/theme/script/sidebar-fixed.js') }}"></script> -->

<!-- BB JS -->
<script type="text/javascript" src="{{ asset('hijabenka/desktop/script/sticky-side.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/autocomplete.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/scrollable/jquery.scrollable.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/fancybox/jquery.fancybox.pack.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/core.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('hijabenka/desktop/script/lazyload.js') }}"></script>
<script src="{{ asset('js/desktop/app.js?t=').date('YmdHis') }}"></script>
<!-- <script src="{{ asset('hijabenka/desktop/script/global.js') }}"></script> -->

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

<script type="text/javascript">
<?php $user = \Auth::user(); ?>
  var mydata336CC993E54D = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
  }
</script>

@include('marketing-tag.global-data', ['catalogs' => !empty($catalog) ? $catalog : [], 'ref' => !empty($ref) ? $ref : ''])

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.all-pages')
    @include('olark.olark-hb')
@endif

@yield('marketing-tag')
</html>

