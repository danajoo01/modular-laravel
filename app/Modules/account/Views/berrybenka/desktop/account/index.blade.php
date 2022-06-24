@extends('layouts.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/catalog-list.css') }}">
@endsection

@section('content')

<div class="user-wrapper clearfix">
		<div class="wrapper">
			<div class="user-wrap">
			{!! get_view('account', 'account.leftmenu', array('page'=>'index','user'=>$user)) !!}
                                <div class="user-right right">
                                    <div class="user-dashboard">
                                        <h1 class="clearfix">
                                            <i class="fa fa-dashboard"></i>Halaman Akun
                                            <div class="right last-login"><b>Terakhir Login : </b>{{ indonesian_date(strtotime($user->last_login_date),'l, j F Y H:i:s') }}</div>
                                        </h1>

                                        <div class="user-dashboard-content">
<?php /*
                                            <div class="stamp-wrapper" id="stamp-history">
                                                <div class="stamp-menu">
                                                    {!! get_view('account', 'account.stampmenu', array('page'=>'index')) !!}                                                    
                                                </div>
                                                <div class="stamp-dash">
                                                    <div class="stamp-head">
                                                        <ul>
                                                            <li>
                                                               <?php /* <img src="/berrybenka/desktop/img/bb-stamp/bb-stamp-large.png" alt="">*/?>
<?php /*                                                                <div class="stamp-head-info">
                                                                    <h2>Active Benka Stamp</h2>                                                                    
                                                                    <p>Kamu Memiliki {{ isset($user->stamp_active) ? $user->stamp_active : 0}} active Benka Stamp</p>
                                                                </div>
                                                            </li>
                                                            <li>
<?php /*
                                                                <img src="/berrybenka/desktop/img/bb-stamp/pending-stamp.png" alt="">*/?>
      <?php /*                                                          <div class="stamp-head-info">
                                                                    <h2>Pending Benka Stamp</h2>
                                                                    <p>Kamu Memiliki {{ isset($user->stamp_pending) ? $user->stamp_pending : 0}} pending Benka Stamp</p>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <div class="stamp-history">
                                                        <h2>Benka Stamp History</h2>
                                                        <div class="stamp-history-wrapper">
                                                            <div class="stamp-history-head">
                                                                <ul>
                                                                    <li>#</li>
                                                                    <li>Tanggal</li>
                                                                    <li style='width:10%;'>Tipe</li>
                                                                    <li style='width:50%;'>Deskripsi</li>
                                                                    <li style='width:15%;'>Tipe Stamp</li>
                                                                    <li>Total</li>
                                                                </ul>
                                                            </div>
                                                            <div class="stamp-history-body">
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
                                                                        <li>{{ ($key + ($limit * ($page - 1))) + 1 }}</li>
                                                                        <li>{{ isset($row->history_create_date) ? $row->history_create_date : '-' }}</li>
                                                                        <li style='width:10%;'>{{ isset($row->type) && $row->type == 'CR' ? 'CR' : 'DB' }}</li>
                                                                        <li style='width:50%;'>{{ $stamp_description }}</li>
                                                                        <li style='width:15%;'>{{ (isset($row->stamp_status) &&  $row->stamp_status == 1) ? 'Active' : 'Pending' }}</li>
                                                                        <li>{{ isset($row->stamp_value) ? $row->stamp_value : '-' }}</li>
                                                                    </ul>    
                                                                    @endforeach
                                                                @else                                                                
                                                                <ul>
                                                                    <li style="width: 100%;text-align: center;">No Data</li>
                                                                </ul>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="pagination deals-paging">
                                                @include('pagination.desktop.paginationbb', ['paginator' => $stamp_history, 'anchor' => 'stamp-history'])
                                            </div>
                                        </div>

                                    </div>*/?>
                                </div>
			</div>
		</div>
</div>

@endsection

@section('js')
<!-- JS here -->
@endsection
