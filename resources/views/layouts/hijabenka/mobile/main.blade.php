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
    $footer_seo         = isset($seo["footer_text"]) ? $seo["footer_text"] : '';
    ?>
    <title>{!! $title_seo !!}</title>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <meta name="keywords" content="{!! $keywords_seo !!}">
    <meta name="description" content="{!! $description_seo !!}">
    <meta name="revisit-after" content="7 days" />
    <meta name="author" content="berrybenka.com">
<!--    <meta name="google-site-verification" content="Ea_eO8eyxg_9GhRxWqQaGoNBHg0rZFRT84ovScDXR48" />-->
    <meta name="google-site-verification" content="4Fq561OX9lQQNh7nQDLhTyoFxTBGSjkFypt34p6vML4" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
    {!! $alternate !!}
    
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('hijabenka/mobile/img/favicon.png') }}" />
    <meta name="_token" content="{!! csrf_token() !!}"/>
    
    <link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/core.css?t=').date('YmdHis') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/search.css?t=').date('YmdHis') }}"> 
    <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/mobile/script/accordion-nav/accordion.css') }}">
    <link rel="stylesheet" href="{{ asset('hijabenka/mobile/script/flexslider/flexslider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('berrybenka/mobile/script/fancybox/jquery.fancybox.css') }}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{{ asset('berrybenka/mobile/css/jquery.fancybox.css') }}" media="screen" />

    @yield('css')
    @yield('marketing-tag-header','')
    <link rel="manifest" href="https://hijabenka.api.useinsider.com/views/push/manifest/" />
</head>
<body>
    <script type="text/javascript">var dataLayer = [];</script>
    @yield('marketing-tag-body','')
    @yield('filter')

    @include('layouts.hijabenka.mobile.header')

    @yield('content')

    <!-- /#page-wrapper -->

    </div>
    @include('layouts.hijabenka.mobile.footer')
    <input type="hidden" id="tracking-server" class="tracking-server" value="{{ get_server_address() }}" />
</body>
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<!-- <script src="{{ asset('hijabenka/theme/script/sidebar-fixed.js') }}"></script> -->
<?php if (getAppEnv() == 'development' && getMarketingEnv() == true) : ?>
<script type="text/javascript" src="{{ asset('hijabenka/mobile/script/jslog.js') }}"></script>
<?php endif; ?>
<!-- BB JS -->
<script src="{{ asset('js/jquery-ui.js') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/accordion-nav/accordion.js') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/tabs.js') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/flexslider/flexslider.js') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/fancybox/jquery.fancybox.js') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/jquery.cookie.js') }}"></script>
<script src="{{ asset('js/mobile/app.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('hijabenka/mobile/script/core.js?t=').date('YmdHis') }}"></script>
<script src="{{ asset('berrybenka/desktop/script/clipboard/clipboard.min.js') }}"></script>
<script src="{{ asset('berrybenka/mobile/script/autocomplete.js?t=').date('YmdHis') }}"></script>

@yield('js')

<!-- Variable for marketing tag data -->
<script type="text/javascript">
<?php $user = \Auth::user(); ?>
  var mydata336CC993E54D = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
  }
</script>
@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.mobile.all-pages')
    @include('prism.prism-hb')
@endif

@yield('marketing-tag')

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.mobile.gtm')
@endif
</html>

