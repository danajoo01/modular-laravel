<?php 
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
        <div class="about-wrapper">
            <h1>ABOUT SHOPDECA</h1>
            <p>Shopdeca.com was launched in July 2013 in Jakarta, Indonesia with vision of bringing design to the people. Our online platform is composed of assorted high quality and uniquely designed products from South East Asian designers and international brands. We differentiate ourselves by curating the best items from all over the world. This is your one stop shop for international indie fashion labels, emerging local designers, practical yet distinct home living products, original gift ideas and quirky tech gadgets. We aim to be the online lifestyle destination in Southeast Asia.</p>
        </div>
    </div>
    </div>
</div>

@endsection



