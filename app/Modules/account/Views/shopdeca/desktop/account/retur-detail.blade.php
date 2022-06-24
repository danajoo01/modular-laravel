@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/user.css?t=').date('YmdHis') }}">
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
                        <i class="fa fa-archive" aria-hidden="true"></i>Form Retur
                    </h1>
                    <div class="retur-content">
                        <form method="GET" name="account-form" id="return_form">
                            <div class="ubah-data content-show retur-form" id="ubah-data">
                                <ul>
                                    <li>
                                        <label>Nomor Pemesanan</label>
                                        <span>{{ $return_detail->purchase_code }}</span>
                                    </li>
                                    <li>
                                        <label>Deskripsi</label>
                                        <span>{{ $return_detail->product_name }}<br>{{ $return_detail->product_color }}<br>{{ $return_detail->product_size }}</span>
                                    </li>
                                    <li>
                                        <label>Jumlah</label>
                                        <span>{{ $return_detail->quantity }}</span>
                                    </li>
                                    <li>
                                        <label>Tujuan Pengembalian</label>
                                        <span class="tujuan-pengembalian">
                                            <select name="return_obj" id="return_obj">
                                                <option value="0">- Pilih Salah Satu -</option>
                                                <option value="1">Tukar dengan barang yang sama</option>
                                                <option value="2">Tukar dengan barang yang beda</option>
                                                <option value="3">Ganti Ukuran</option>
                                                <option value="4">Ganti Warna</option>
                                                <option value="5">Kredit Shopdeca</option>
                                                <option value="6">Pengembalian Pembayaran</option>
                                                <option value="7">Lainnya..</option>
                                            </select>
                                            <div class="half-ans-dropdown clearfix"></div>
                                            <div class="ans-dropdown clearfix"></div>
                                        </span>
                                    </li>
                                    <li>
                                        <label>Alasan Pengembalian</label>
                                        <span class="alasan-pengembalian">
                                            <select name="return_reason" id="return_reason">
                                                <option value="0">- Pilih Salah Satu -</option>
                                                <option value="1">Berbeda Dengan di Web</option>
                                                <option value="2">Kualitas Tidak Bagus</option>
                                                <option value="3">Ukuran Tidak Sesuai</option>
                                                <option value="5">Tidak Sesuai Pesanan</option>
                                                <option value="4">Rusak/Tumpah/Pecah</option>
                                                <option value="6">Lainnya..</option>
                                            </select>
                                            <div class="half-ans-dropdown clearfix"></div>
                                            <div class="ans-dropdown clearfix"></div>
                                        </span>
                                    </li>
                                </ul>
                                <input type="submit" value="Proses" class="submit-button ml205 submit-ubah-data" id="submit_return">
                            </div>
                            <div class="full-width text-center">
                                <input type="hidden" name="return_sku" value="{{ $return_detail->SKU }}" />
                                <input type="hidden" name="return_purchase_code" value="{{ $return_detail->purchase_code }}" />
                                <input type="hidden" name="return_customer_id" value="{{ $return_detail->customer_id }}" />
                                <input type="hidden" name="return_order_item_id" value="{{ $return_detail->order_item_id }}" />
                                <input type="hidden" name="submit" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    
    $(function(){
        var over = '<div id="loading""><div class="load-icon"><img src="{{ asset('shopdeca/desktop/img/bb-loading.gif') }}"></div></div>';
        $('#return_form').submit(function(){
                
        var obj     = $('#return_obj option:selected').val();
        var reason  = $('#return_reason option:selected').attr('id');

          if(obj==0 || reason==0) {
            alert('Please complete your return objective and return reason.');
            return false;
          }

          $.ajax({
                    method : 'post',
                    url : '{{ url('/user/insert_customer_return') }}',
                    data : $('#return_form').serialize(),
                    beforeSend : function() {
                        $('#submit_return').attr("disabled", "disabled");
                        $('#loading').css('display', 'block');
                    },
                    success : function(data) {
                        // HIT WMS RETURN FUNCTION
                        $.ajax({
                            method : 'post',
                            url : '{{ url('user/call_wms_return_process') }}',
                            data : $('#return_form').serialize(),
                            success : function (data_wms) {
                                $('#loading').css('display', 'none');
                                window.location.href = "{{ url('user/return_form') }}";
                            }
                        });
                    }
                });
                return false;
            });

        $('#return_obj').change(function(){
          var obj = $('#return_obj option:selected').val();

          /* Clear All Value */
          
          if(obj==7) {
            $( ".tujuan-pengembalian .half-ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).append( "<textarea name='return_obj_txtarea' placeholder='Reason..' style='width:400px;height:94px;border: 1px solid #ccc;box-sizing: border-box;'></textarea>" );
          }
          else if(obj==3) {
            $( ".tujuan-pengembalian .half-ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).append( '*<?php echo (empty($available_size)) ? "EMPTY<br/>" : "Available Size: " . $available_size ; ?><input type="text" name="return_obj_chg_size" placeholder="Available Size: <?php echo (empty($available_size)) ? "EMPTY" : $available_size ; ?>">' );
          }
          else if(obj==4) {
            $( ".tujuan-pengembalian .half-ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).append( '*<?php echo (empty($available_color)) ? "EMPTY<br/>" : "Available Color: " . $available_color ; ?><input type="text" name="return_obj_chg_color" placeholder="Available Color: <?php echo (empty($available_color)) ? "EMPTY" : $available_color ; ?>">' );
          }
          else if(obj==6) {
            $( ".tujuan-pengembalian .half-ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .ans-dropdown" ).append( '<input type="text" name="return_obj_refund_bank" placeholder="Bank Name">' + 
                                       '<input type="text" name="return_obj_refund_bank_acc" placeholder="Bank Account Name">' +
                                       '<input type="text" name="return_obj_refund_acc" placeholder="Account Number">' );
          }
          else if(obj==2) {
            $( ".tujuan-pengembalian .ans-dropdown" ).html('');
            $( ".tujuan-pengembalian .half-ans-dropdown" ).append( '<input type="text" name="return_obj_brand" placeholder="Brand">'
                                               + '<input type="text" name="return_obj_color" placeholder="Color">'
                                               + '<input type="text" name="return_obj_product_name" placeholder="Product Name">'
                                               + '<input type="text" name="return_obj_quantity" placeholder="Quantity">'
                                               + '<input type="text" name="return_obj_size" placeholder="Size">' );
          }
          else {
             $( ".tujuan-pengembalian .ans-dropdown" ).html('');
             $( ".tujuan-pengembalian .half-ans-dropdown" ).html('');
          }

        });

        $('#return_reason').change(function(){
          var reason = $('#return_reason option:selected').val();

          /* Clear All Value */
          
          if(reason==6) {
            $( ".alasan-pengembalian .ans-dropdown" ).html('');
            $( ".alasan-pengembalian .ans-dropdown" ).append( "<textarea name='return_reason_txtarea' placeholder='Reason..' style='width:400px;height:94px;border: 1px solid #ccc;box-sizing: border-box;'></textarea>" );
          }
          else {
            $( ".alasan-pengembalian .ans-dropdown" ).html('');
          }
        });

      });

</script>
@endsection