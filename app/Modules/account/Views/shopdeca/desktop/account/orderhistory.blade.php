@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/user.css?t=').date('YmdHis') }}">
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/catalog-list.css') }}">
@endsection

@section('content')

<div class="user-wrapper clearfix">
    <div class="wrapper">
        @if(!empty(Session::get('error_message')))
            <span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i>{{ Session::get('error_message') }}</span>
        @endif
    	<div class="user-wrap">
        	{!! get_view('account', 'account.leftmenu', array('page'=>'order','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>Order Anda
                        <div class="right last-login"><b>Terakhir Login : </b>{{ indonesian_date(strtotime($user->last_login_date),'l, j F Y H:i:s') }}</div>
                    </h1>
                    <div class="order-content">
                    	<ul class="tabs">
                        	<li><a href="#d" class="active"><i class="fa fa-check-square"></i>Daftar Konfimasi</a></li>
                                <!--<li><a href="/user/return_form"><i class="fa fa-archive"></i>Form Retur</a></li>-->
                        </ul>
                    </div>
                    <div id="status-pesan" class="status-pesan-list">
                    	<h1 class="list-alamat-title">Order Status Tracking<i class="right fa fa-times close-edit-alamat" aria-hidden="true"></i></h1>
                        <div class="status-pesan-wrapper">
                        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <thead></thead>
                                <tbody>
                                    <!-- Loading div -->
                                    <span id="load_ship_city" style="width: 100%;z-index: 9999;padding: 10px;text-align: center;display: none">
                                        <i class="fa fa-refresh fa-spin"></i> Loading...
                                    </span>
                                    <!-- ******** -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-content">
                    	<div class="daftar-konfirmasi" id="daftar-konfirmasi">
                            <table width="100%" border="1px" cellspacing="0" cellpadding="0" class="confirm-table">
                            	<thead>
                                	<tr>
                                        <td>#</td>
                                        <td>Tanggal Pembayaran</td>
                                        <td>Deskripsi</td>
                                        <td>Total</td>
                                        <td>Tindakan</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data)
                                        <?php $i = $all->firstItem(); ?>
                                        @foreach ($data as $row)
                                            <tr>
                                                <td>{{ $i }}</td>
                                                <td>{{ $row['purchase_date'] }}<strong>{{ $row['purchase_code'] }}</strong></td>
                                                <td>
                                                    @foreach ($row['product'] as $rows)
                                                        {{ $rows->quantity }}x<br> {{ $rows->brand_name }}<br /> {{ $rows->product_name }}<br />
                                                    @endforeach
                                                </td>
                                                <td>IDR <?php echo number_format($row['grand_total'],0,".",".");?></td>
                                                <td class="order-status">
                                                    <?php if($row['payment_type_transfer'] == 1 && is_null($row['confirm_transfer_by']) && $row['status'] == 0):?>
                                                        <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Konfirmasi</a>
                                                    <?php elseif($row['payment_type_transfer'] == 1 && !is_null(['$row->confirm_transfer_by']) && $row['status'] == 1):?>
                                                        <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Telah Dikonfirmasi</a>
                                                    <?php elseif($row['status'] == 2):?>
                                                        <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Dibatalkan</a>
                                                    <?php else:?>
                                                        <a href="/user/order_history_detail/<?php echo $row['purchase_code'];?>" class="#">Detail</a>
                                                    <?php endif;?>
                                                    <a href="#status-pesan" class="status-pesan fancybox" code="<?php echo $row['purchase_code'];?>">Status</button>
                                                </td>
                                            </tr>
                                        <?php $i++ ?>
                                        @endforeach
                                    @else 
                                        <tr>
                                            <td colspan="5">Order Anda Kosong</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination right">
                                @if ($data)
                                    {!! $all->render() !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
  $(document).ready(function () {

    $(".order-status a.status-pesan").click(function(){
        /* Get Purchase Code Value*/
        var code = $(this).attr("code");

        /* Delete All Table Row After First */
        $(".status-pesan-wrapper table thead").html('');
        $(".status-pesan-wrapper table tbody").html('');

        <?php echo 'var url = "' . url('/') . '/user/order_status";'; ?>
        var current_purchase_code   = '';
        var show_purchase_code      = '';
        var rowspan_pc              = 1;
        var td_pc                   = '';
        var current_sku             = '';
        var show_sku                = '';
        var rowspan_sku             = 1;
        var td_sku                  = '';
        var current_pn              = '';
        var show_pn                 = '';
        var rowspan_pn              = 1;
        var td_pn                   = '';
        var current_ac              = '';
        var show_ac                 = '';
        var rowspan_ac              = 1;
        var td_ac                   = '';
        var current_sh              = '';
        var current_oid             = '';
        var show_sh                 = '';
        var rowspan_sh              = 1;
        var td_sh                   = '';
        var url_ship                = '';
        
        $.ajax({
            type : "POST",
            url: url, 
            data: { purchase_code : code }, 
            datatype : "json",
            beforeSend : function () {
                $(".status-pesan-wrapper #load_ship_city").css('display', 'block');
            },
            success: function(result){

                $(".status-pesan-wrapper table thead").append(' <tr>'+
                                                                    '<td>Purchase Code</td>'+
                                                                    '<td>SKU</td>'+
                                                                    '<td>Product Name</td>'+
                                                                    '<td>Date</td>'+
                                                                    '<td>Status</td>'+
                                                                '</tr>');

                /* Push Result Into Table Rows */
                $.each(result, function(i, row) {
                    if(row.purchase_code != current_purchase_code){
                        current_purchase_code = row.purchase_code;
                        show_purchase_code = current_purchase_code;
                        td_pc = '<td id="pc" rowspan="'+rowspan_pc+'">' + show_purchase_code + '</td>';
                    }else{
                        show_purchase_code = '';
                        rowspan_pc = rowspan_pc + 1;
                        td_pc = '';
                        $('#pc').attr('rowspan',rowspan_pc);
                    }
                    
                    if(row.SKU != current_sku){
                        current_sku = row.SKU;
                        show_sku = current_sku;
                        rowspan_sku = 1;
                        td_sku = '<td id="'+current_sku+'" rowspan="'+rowspan_sku+'">' + show_sku + '</td>';
                    }else{
                        show_sku = '';
                        rowspan_sku = rowspan_sku + 1;
                        td_sku = '';
                        $('#'+current_sku).attr('rowspan',rowspan_sku);
                    }
                    
                    if(row.product_id != current_pn){
                        current_pn = row.product_id;
                        show_pn = row.product_name;
                        rowspan_pn = 1;
                        td_pn = '<td id="'+current_pn+'" rowspan="'+rowspan_pn+'">' + show_pn + '</td>';
                    }else{
                        show_pn = '';
                        rowspan_pn = rowspan_pn + 1;
                        td_pn = '';
                        $('#'+current_pn).attr('rowspan',rowspan_pn);
                    }
                    
                    if(row.date != current_ac){
                        current_ac = row.date+row.SKU;
                        show_ac = row.approve_change;
                        rowspan_ac = 1;
                        td_ac = '<td id="'+current_ac+'" rowspan="'+rowspan_ac+'">' + show_ac + '</td>';
                    }else{
                        show_ac = '';
                        rowspan_ac = rowspan_ac + 1;
                        td_ac = '';
                        $('#'+current_ac).attr('rowspan',rowspan_ac);
                    }
                    
                    if(row.status_id != current_sh || row.order_item_id != current_oid){
                        current_sh  = row.status_id;
                        current_oid = row.order_item_id;
                        show_sh     = row.status_history;
                        rowspan_sh  = 1;
                        td_sh       = '<td id="'+current_sh+'" rowspan="'+rowspan_sh+'">' + show_sh + '</td>';
                    }else{
                        show_sh     = '';
                        rowspan_sh  = rowspan_sh + 1;
                        td_sh       = '';
                        $('#'+current_sh).attr('rowspan',rowspan_sh);
                    }
                    
                    $(".status-pesan-wrapper table tbody").append(
                      '<tr>' +
                        td_pc +
                        td_sku +
                        td_pn +
                        td_ac +
                        td_sh +
                      '</tr>'
                    );
                    
                    if(row.status_history == 'Shipped' && typeof row.number != "undefined"){
                        show_purchase_code = '';
                        rowspan_pc = rowspan_pc + 1;
                        td_pc = '';
                        $('#pc').attr('rowspan',rowspan_pc);
                        
                        show_sku = '';
                        rowspan_sku = rowspan_sku + 1;
                        td_sku = '';
                        $('#'+current_sku).attr('rowspan',rowspan_sku);
                        
                        show_pn = '';
                        rowspan_pn = rowspan_pn + 1;
                        td_pn = '';
                        $('#'+current_pn).attr('rowspan',rowspan_pn);
                        
                        //show_ac = '';
                        //rowspan_ac = rowspan_ac + 1;
                        //td_ac = '';
                        //$('#'+current_ac).attr('rowspan',rowspan_ac);
                        
                        if(row.method == 68){
                            url_ship = '<a href="http://www.jne.co.id/" target="_blank" style="color:blue">JNE</a>';
                        }else if(row.method == 69){
                            url_ship = '<a href="https://tracking.acommerce.asia/" target="_blank" style="color:blue">Acommerce</a>';
                        }else if(row.method == 70){
                            url_ship = '<a href="http://rpx.co.id/" target="_blank" style="color:blue">RPX</a>';
                        }else if(row.method == 106){
                            url_ship = '<a href="http://www.firstlogistics.co.id/" target="_blank" style="color:blue">First Logistics</a>';
                        }else if(row.method == 130){
                            url_ship = '<a href="http://www.sap-express.com" target="_blank" style="color:blue">sap-express</a>';
                        }
                        
                        td_ac = '<td id="'+current_ac+'" rowspan="'+rowspan_ac+'"> - </td>';
                        td_sh = '<td id="'+current_sh+'" rowspan="'+rowspan_sh+'"> Check '+url_ship+' and input AWB number [<font color="blue">'+ row.number +'</font>] to track your order deliver status</td>';
                        
                        $(".status-pesan-wrapper table tbody").append(
                          '<tr>' +
                            td_pc +
                            td_sku +
                            td_pn +
                            td_ac +
                            td_sh +
                          '</tr>'
                        );
                    }
                });

                $('.status-pesan-wrapper #load_ship_city').css('display', 'none');                
            }
        });
    });
  });

</script>
@endsection