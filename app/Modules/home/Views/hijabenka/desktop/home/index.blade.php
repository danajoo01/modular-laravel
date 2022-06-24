@extends('layouts.hijabenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/home.css') }}">
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
<!-- JS here -->
@endsection

@section('marketing-tag')
	@include('marketing-tag.hijabenka.desktop.homepage')
@endsection