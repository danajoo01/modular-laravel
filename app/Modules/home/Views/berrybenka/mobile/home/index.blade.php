@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/home.css') }}">
@endsection

@section('content')

    {!! $home !!}

<!-- BEGIN GCR Badge Code -->
<script src="https://apis.google.com/js/platform.js?onload=renderBadge"
  async defer>
</script>

<script>
  window.renderBadge = function() {
    var ratingBadgeContainer = document.createElement("div");
      document.body.appendChild(ratingBadgeContainer);
      window.gapi.load('ratingbadge', function() {
        window.gapi.ratingbadge.render(
          ratingBadgeContainer, {
            // REQUIRED
            "merchant_id": {{ GetMerchantIdGcr() }},
            // OPTIONAL
            "position": "BOTTOM_LEFT"
          });           
     });
  }
</script>
<!-- END GCR Badge Code -->
<!-- BEGIN GCR Language Code -->
<script>
  window.___gcfg = {
    lang: 'id'
  };
</script>
<!-- END GCR Language Code -->

@endsection

@section('js')
  @if(getSnowflakeEnv() == true)
    <script src="{{ asset('berrybenka/desktop/script/snowfall.min.js') }}"></script>
    <style>
    .snowflakes_body{opacity:0.3;}
    </style>
    <script>
    var sf = new Snowflakes({
          container: document.body, // Default: document.body
          count: 100, // 100 snowflakes. Default: 50
          speed: 1, // The property affects the speed of falling. Default: 1
          useRotate: true, // Default: true
          useScale: true, // Default: true
      });
    </script>
  @endif
@endsection

@section('marketing-tag')
	@include('marketing-tag.berrybenka.mobile.homepage')
@endsection