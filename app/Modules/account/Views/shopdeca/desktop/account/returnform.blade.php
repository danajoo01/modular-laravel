@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/catalog-list.css') }}">
@endsection

@section('content')
<!-- Loading div -->
<div id="loading" style="display:none;">
    <div class="load-icon">
        <img src="{{ asset('shopdeca/desktop/img/bb-loading.gif') }}">
    </div>
</div>
<!-- ******** -->
<div class="user-wrapper clearfix">
    <div class="wrapper">
    	<div class="user-wrap">
        	{!! get_view('account', 'account.leftmenu', array('page'=>'order','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>Order Anda
                    </h1>
                    <div class="order-content">
                    	<ul class="tabs">
                        	<li><a href="/user/order_history"><i class="fa fa-check-square"></i>Daftar Konfimasi</a></li>
                            <li><a href="#f" class="active"><i class="fa fa-archive"></i>Form Retur</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        @if(!empty(Session::get('gagal')))
                            {!! show_message(Session::get('gagal'),1) !!}
                        @elseif(!empty(Session::get('sukses')))
                            {!! show_message(Session::get('sukses')) !!}
                        @endif
                    	<div class="form-retur" id="form-retur">
                        	<h2>Daftar Terkirim</h2>
                            <table width="100%" border="1px" cellspacing="0" cellpadding="0" class="return-table">
                            	<thead>
                                	<tr>
                                        <td>#</td>
                                        <td>Nomor Pemesanan</td>
                                        <td>Tanggal Pemesanan</td>
                                        <td>Deskripsi</td>
                                        <td>Tanggal Penerimaan</td>
                                        <td>Tindakan</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($delivered_list->total() > 0)
                                        <?php $i = $delivered_list->firstItem(); ?>
                                        @foreach ($delivered_list as $delivered)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $delivered->purchase_code }}</td>
                                                <td>{{ $delivered->purchase_date }}</td>
                                                <td>{{ $delivered->product_name }}<br>Color : {{ $delivered->variant_color_name_custom }}<br>Size : {{ $delivered->product_size }}</td>
                                                <td>{{ $delivered->delivered_date }}</td>
                                                <td>
                                                    <a href="/user/return_purchase/{{ $delivered->order_item_id }}" class="class="retur-button"">Retur</a>
                                                </td>
                                            </tr>
                                        <?php $i++ ?>
                                        @endforeach
                                    @else 
                                        <tr>
                                            <td colspan="6" align="center">Daftar Terkirim Anda Kosong</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination right">
                                {!! $delivered_list->render() !!}
                            </div>
                            <br>
                            <br>
                            <br>
                            <h2>Daftar Dikembalikan</h2>
                            <table width="100%" border="1px" cellspacing="0" cellpadding="0" class="return-table">
                            	<thead>
                                	<tr>
                                        <td>#</td>
                                        <td>Nomor Pemesanan</td>
                                        <td>Tanggal Pemesanan</td>
                                        <td>Deskripsi</td>
                                        <td>Status</td>
                                        <td>Tindakan</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($returned_list->total() > 0)
                                        <?php $i = $returned_list->firstItem(); ?>
                                        @foreach ($returned_list as $returned)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $returned->purchase_code }}</td>
                                                <td>{{ $returned->purchase_date }}</td>
                                                <td>{{ $returned->product_name }}<br>Color : {{ $returned->variant_color_name_custom }}<br>Size : {{ $returned->product_size }}</td>
                                                <td>{{ $returned->return_status }}</td>
                                                <td>
                                                    <a onclick="return confirm('Are you sure to cancel this return transaction?')" <?php echo ($returned->return_status == 'CLOSED') ? 'style="display:none"' : ''; ?> href="/user/cancel_return/{{ $returned->order_item_id }}" class="btn btn-danger btn-block">Batalkan</a>
                                                </td>
                                            </tr>
                                        <?php $i++ ?>
                                        @endforeach
                                    @else 
                                        <tr>
                                            <td colspan="6" align="center">Daftar Dikembalikan Anda Kosong</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination right">
                                {!! $returned_list->render() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection