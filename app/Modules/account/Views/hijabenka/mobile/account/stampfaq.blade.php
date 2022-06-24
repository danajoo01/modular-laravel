@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        {!! get_view('account', 'account.stampmenu', array('page'=>'faq')) !!}
        <div class="loyalty-sk">
            <h1>Benka Stamp FAQ</h1>
            <ul>
                @foreach($faq as $key => $val)
                <li>
                    <p>{{ $key }}</p>
                    {{ $val[0] }}
                </li>                
                @endforeach
            </ul>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection