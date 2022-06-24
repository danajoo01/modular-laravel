@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/account.css') }}">
@endsection

@section('content')

<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        {!! get_view('account', 'account.stampmenu', array('page'=>'history')) !!}
        <div class="loyalty-history" id='stamp-history'>
        	<h1>Benka Stamp History</h1>            
                @if(!empty($stamp_history) && count($stamp_history) > 0)
                    @foreach($stamp_history as $key => $row)
                        <?php 
                            if(isset($row->description)) {
                                $stamp_description = $row->description;
                            } else {
                                if (isset($row->type) && $row->type == 'CR' && isset($row->stamp_status) &&  $row->stamp_status != 1) {
                                    $stamp_value = isset($row->stamp_value) ? $row->stamp_value : '-';
                                    $stamp_description = 'Anda berkemungkinan mendapatkan '.$stamp_value.' stamp - #'.$row->purchase_code;
                                } elseif (isset($row->type) && $row->type == 'CR' && isset($row->stamp_status) &&  $row->stamp_status == 1) {
                                    $stamp_value = isset($row->stamp_value) ? $row->stamp_value : '-';
                                    $stamp_description = 'Anda mendapatkan '.$stamp_value.' stamp - #'.$row->purchase_code;
                                } elseif(isset($row->type) && $row->type == 'DB' && isset($row->stamp_status) &&  $row->stamp_status != 1) {
                                    $stamp_value = isset($row->stamp_value) ? $row->stamp_value : '-';
                                    $stamp_description = 'Pengurangan '.$stamp_value.' pending stamp karena retur/refund - #'.$row->purchase_code;
                                } else {
                                    $stamp_description = '-';
                                }                                                                                
                            }
                        ?>
                    <ul>
                        <li>
                            <h1>{{ isset($row->stamp_value) ? $row->stamp_value : '0' }} {{ (isset($row->stamp_status) &&  $row->stamp_status == 1) ? 'Active Stamp' : 'Pending Stamp' }}</h1>
                            <p>{{ isset($row->type) && $row->type == 'CR' ? 'CREDIT' : 'DEBIT' }} | {{ $stamp_description }}</p>
                        </li>
                    </ul>    
                    @endforeach
                @else                                                                
                <ul>
                    <li style="width: 100%;text-align: center;">No Data</li>
                </ul>
                @endif
        </div>
        <div class="pagination mt40 clear">
            @include('pagination.mobile.paginationbb', ['paginator' => $stamp_history, 'anchor' => 'stamp-history'])
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection