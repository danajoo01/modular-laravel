@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/account.css') }}">
@endsection

@section('content')
<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        <div class="account-body">
            <div class="benka-wrapper">
                <ul>
                    <li>
                        <h1 class="border-bot b-gratis">
                            <a href="#">Daftar Pemesanan</a>
                            <i aria-hidden="true" class="fa fa-angle-down"></i>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/account_dashboard">Akun Saya</a>
                            </h1>
<!--                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/referral_program">Belanja Gratis hijabenka</a>
                            </h1>-->
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/change_password">Ubah Password</a>
                            </h1>
                        </h1>
                    </li>
                </ul>
                <div class="order-list">
                    <ul>
                        @if ($data)
                            @foreach ($data as $row)
                                <li class="clear">
                                    <p><span>Tanggal</span><span>{{ $row['purchase_date'] }}</span></p>
                                    <p><span>No. Order</span><span>{{ $row['purchase_code'] }}</span></p>
                                    <p>
                                        <span>Detail</span>
                                        <span>
                                            @foreach ($row['product'] as $rows)
                                                {{ $rows->quantity }}x<br> {{ $rows->brand_name }}<br /> {{ $rows->product_name }}<br />
                                            @endforeach
                                        </span>
                                    </p>
                                    <p>
                                        <span>
                                            <?php if($row['payment_type_transfer'] == 1 && is_null($row['confirm_transfer_by']) && $row['status'] == 0):?>
                                                    <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Konfirmasi</a>
                                            <?php elseif($row['payment_type_transfer'] == 1 && !is_null(['$row->confirm_transfer_by']) && $row['status'] == 1):?>
                                                    <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Telah Dikonfirmasi</a>
                                            <?php elseif($row['status'] == 2):?>
                                                    <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Dibatalkan</a>
                                            <?php else:?>
                                                    <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Detail</a>
                                            <?php endif;?>
                                        </span>
                                    </p>
                                </li>
                            @endforeach
                        @else
                            <li class="clear">
                                <p>Order Anda Kosong</p>
                            </li>
                        @endif
                    </ul>
                </div>
                <div class="pagination mt40 clear">
                    @if ($data)
                        {!! $all->render() !!}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

@endsection