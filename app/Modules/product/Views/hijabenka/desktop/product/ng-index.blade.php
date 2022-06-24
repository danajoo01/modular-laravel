@extends('layouts.hijabenka.main')

@section('css')

@endsection

@section('content')

<div ng-app="productApp">
	<div ng-view></div>
</div>

@endsection

@section('js')
<!-- Modules -->
<script src="{{ asset('hijabenka/ng-app/product/js/app.js') }}"></script>

<!-- Controllers -->
<script src="{{ asset('hijabenka/ng-app/product/js/controller/ProductController.js') }}"></script>

<!-- Services -->
<script src="{{ asset('hijabenka/ng-app/product/js/services/products.js') }}"></script>

<script>
function urlSegment() {
	var newURL = window.location.protocol + "://" + window.location.host + "/" + window.location.pathname;
	var pathArray = window.location.pathname.split( '/' );
	var segment = pathArray

	return segment;
}

function ChangeUrl(elm) {
	var uri = $(elm).prev().val();
	var segment = urlSegment();
	var gender = segment[1];

    if (typeof (history.pushState) != "undefined") {
        var obj = { Title: "cek", Url: '/'+gender+'/'+uri };
        history.pushState(obj, obj.Title, obj.Url);
    } else {
        alert("Browser does not support HTML5.");
    }
}
</script>

@endsection