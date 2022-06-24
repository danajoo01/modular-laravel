@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        {!! get_view('account', 'account.stampmenu', array('page'=>'deals')) !!}
        <div class="loyalty-deal">
            @foreach ($deals as $row)
            <div id="bb-stamp-detail" class="bb-stamp-detail">
                <div class="deals-wrapper">
                    <img src="{{ IMAGE_DEALS_UPLOAD_PATH }}{{ $row->deals_image }}" alt="">
                    <div class="deals-wording">
                        <h6>{{ $row->deals_name}}</h6>
                        <p>{{ $row->deals_description}}</p>
                        
                        <input type="submit" value="Redeem Stamp" class="redeem-stamp">
                    </div>
                </div>
            </div>
            @endforeach
            <h1>Benka Stamp Deals</h1>
            <ul>
                @foreach ($deals as $row)
                <li>
                    <a href="/user/stamp/deals/{{ $row->id}}">
                        <img src="{{ IMAGE_DEALS_UPLOAD_PATH }}{{ $row->deals_image }}" alt="">
                        <div class="deals-detail">
                            <h5>{{ $row->deals_name}}</h5>
                            <p><span><img src="/berrybenka/mobile/img/bb-stamp/bb-stamp-large.png" alt=""></span>{{ $row->stamp_price}} Berrybenka Stamp</p>
                        </div>
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="pagination clear deals-pagination">
            @include('pagination.mobile.paginationbb', ['paginator' => $deals, 'anchor' => 'stamp-deals'])
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection