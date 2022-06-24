@extends('layouts.hijabenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/order-stat-track.css') }}">
<link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/about.css') }}">
@endsection

@section('content')
<div class="order-track">
    <!-- Loading div -->
    <div id="loading">
        <div class="load-icon">
            <img src="{{ asset('hijabenka/desktop/img/bb-loading.gif') }}">
        </div>
    </div>
    <div class="wrapper">
        <div class="category-head">
            <h4>Order Status Tracking</h4>
        </div>
        <table class="table table-bordered" id="tracking_table">
            <thead>
                <tr></tr>
            </thead>
            <tbody> 
                <tr></tr>
            </tbody>
        </table>
        <p>
         Jika Anda membutuhkan bantuan, silahkan email ke cs@berrybenka.com atau telepon ke 021- 2520555. Kami akan senang membantu Anda. <br>Terima kasih. 
        </p>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    $(document).ready(function () {
        var urlSplit    = document.location.pathname.split("/");
        var code        = urlSplit[3];
        var email       = urlSplit[4];

        /* Delete All Table Row After First */
        $(".order-track .wrapper table thead").html('');
        $(".order-track .wrapper table tbody").html('');

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
            data: { purchase_code : code, type : 'order_tracking' }, 
            datatype : "json",
            success: function(result){
                $(".order-track .wrapper table thead").append(' <tr>'+
                                                                    '<td>Purchase Code</td>'+
                                                                    '<td>SKU</td>'+
                                                                    '<td>Product Name</td>'+
                                                                    '<td>Date</td>'+
                                                                    '<td>Status</td>'+
                                                                '</tr>');

                if(result.length == 0)
                {
                    $(".order-track .wrapper table thead").append(' <tr>'+
                                                                        '<td colspan="5" style="text-align:center;">Data tidak ditemukan</td>'+
                                                                    '</tr>');
                }
                else
                {
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
                        
                        $(".order-track .wrapper table tbody").append(
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
                            
                            $(".order-track .wrapper table tbody").append(
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
                }

                $('.order-track #loading').css('display', 'none');
            }
        });
    });

</script>
@endsection
