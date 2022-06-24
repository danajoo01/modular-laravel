@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        {!! get_view('account', 'account.stampmenu', array('page'=>'deals')) !!}
        <div class="deals-detail-page">
            <img src="{{ IMAGE_DEALS_UPLOAD_PATH }}{{ $deals_image }}" alt="">
            <div class="deals-wording">
                <h1>{{ $deals_name}}</h1>
                <p>{{ $deals_description}}</p>
                <a href="/user/stamp/deals/redeem/{{ $deals_id }}"><input type="submit" value="Redeem"></a>
                @if(!empty(Session::get('message')))
                    <span class="redeem-note">{!! Session::get('message') !!}</span>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection