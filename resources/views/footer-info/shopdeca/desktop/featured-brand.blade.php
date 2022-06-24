<?php $time = microtime(true); 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
@endsection

@section('content')

<div class="error-content thx-wrapper">
	<div class="error-overlay">
        <div class="error-inside">
            <div class="about-wrapper tnc-wrapper feature-brand">
                <h1>PARTNERS</h1>
                <p>Looking to sell with us? We'd be happy to help. It doesn't matter if you're a small brand just starting out, or an established name looking for a new market. Just contact us with your brand details and product information and our dedicated sales staff will quickly get in touch.</p>
                <p>Email: shopdeca@berrybenka.com</p>
            </div>
        </div>
    </div>
</div>

@endsection



