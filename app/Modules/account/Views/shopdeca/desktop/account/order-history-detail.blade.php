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
                        <i class="fa fa-info-circle" aria-hidden="true"></i>Rincian Pemesanan
                    </h1>
                    <div class="detail-shop-wraper">
                        <div class="detail-shop-header clearfix">                            
                            <div class="left"><i class="fa fa-barcode" aria-hidden="true"></i> No Pemesanan<p>{{ $data["invoice_detail"]->purchase_code }}</p></div>
                            <div class="right"><i class="fa fa-calendar" aria-hidden="true"></i> Tanggal Pemesanan<p> {{ $data["invoice_detail"]->purchase_date }} </p></div>
                        </div>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <td colspan="2">PRODUK</td>
                                    <td>HARGA</td>
                                    <td>JUMLAH</td>
                                    <td>TOTAL</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $subtotal = 0;?>
                                @foreach ($data["order_product"] as $row)
                                <tr>
                                    <td>
                                        <div class="cart-list-img left"><img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $row['image_name'] }}"></div>
                                    </td>
                                    <td class="clearfix">
                                        <div class="product-name">
                                            <h1>{{ $row["product_name"] }}</h1>
                                            <h2>{{ $row["brand_name"] }}</h2>
                                            <p>color : {{ $row["color"] }}</p>
                                            <p>Size : {{ $row["product_size"] }}</p>
                                        </div>
                                    </td>
                                    <?php if ($row["special_price"] > 0){?>
                                      <td width="200">IDR <?php echo number_format($row["special_price"],0,".",".");?></td>
                                    <?php }elseif($row["discount_price"] == 0){?>
                                        <td width="200">IDR <?php echo number_format($row["each_price"],0,".",".");?></td>
                                    <?php }else{?>
                                        <td width="200">IDR <?php echo number_format($row["discount_price"],0,".",".");?></td>
                                    <?php }?>
                                    <?php
                                        if ($row["total_special_price"] > 0){
                                            $total_price = $row["total_special_price"];
                                        }elseif($row["total_discount_price"] == 0){
                                            $total_price = $row["total_price"];
                                        }else{
                                            $total_price = $row["total_discount_price"];
                                        }

                                        $subtotal = $subtotal + $total_price;
                                    ?>
                                    <td>{{ $row["quantity"] }}</td>
                                    <td>IDR <?php echo number_format($total_price,0,".",".");?></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="rincian-pembayaran">
                            <ul>
                                <li>
                                    <span>jumlah pembayaran</span>
                                    <span>IDR <?php echo number_format($subtotal,0,".",".");?></span>
                                </li>
                                <li>
                                    <span>kode unik pembayaran</span>
                                    <span>IDR {{ $data["invoice_detail"]->paycode }}</span>
                                </li>
                                <li>
                                    <span>biaya pengiriman</span>
                                    <span>IDR <?php echo number_format($data["invoice_detail"]->shipping_finance,0,".",".");?></span>
                                </li>
                                <?php foreach($data["discount"] as $discount){?>
                                <li>
                                    <span>
                                        <?php echo $discount->discount_nfc_or_discount;?>
                                    </span>
                                    <span colspan="2" class="text-right valign-middle">-IDR <?php echo number_format($discount->discount_value,0,".",".");?></span>
                                </li>
                                <?php }?>
                                <li>
                                    <span>penggunaan kredit anda</span>
                                    <span>IDR <?php echo number_format($data["invoice_detail"]->credit_use,0,".",".");?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="total-history-pembayaran">
                            <span>Total Yang harus dibayar</span>
                            <span><?php echo number_format($data["invoice_detail"]->grand_total,0,".",".");?>,-</span>
                        </div>
                        <div class="history-alamat-pengiriman-pembayaran clearfix">
                            <ul>
                                <li>
                                    <h2><i class="fa fa-truck" aria-hidden="true"></i> Alamat Pengiriman</h2>
                                    <div>
                                        <span>Nama</span>
                                        <span>:</span>
                                        <span>{{ $user->customer_fname }} {{ $user->customer_lname }}</span>
                                    </div>   
                                    <div>
                                        <span>Email</span>
                                        <span>:</span>
                                        <span>{{ $user->customer_email }}</span>
                                    </div> 
                                    <div>
                                        <span>No Telp</span>
                                        <span>:</span>
                                        <span>{{ $user->customer_phone }}</span>
                                    </div>
                                    <div>
                                        <span>Alamat</span>
                                        <span>:</span>
                                        <span>{{ $data["invoice_detail"]->shipping_address }}</span>
                                    </div>
                                    <div>
                                        <span>Provinsi</span>
                                        <span>:</span>
                                        <span>{{ $data["invoice_detail"]->shipping_area }}</span>
                                    </div>
                                    <div>
                                        <span>Kota</span>
                                        <span>:</span>
                                        <span>{{ $data["invoice_detail"]->shipping_name }}</span>
                                    </div>
                                </li>
                                <li>
                                    <?php                                                                         
                                    if(is_null($data["invoice_detail"]->confirm_transfer_by) && (in_array($data["invoice_detail"]->master_payment_id,array('1','2','29','30'))) && $data["invoice_detail"]->status == 0){
                                        if( in_array($data["invoice_detail"]->purchase_status, array('0', '1') ) ){
                                    ?>
                                            <h2><i class="fa fa-money" aria-hidden="true"></i> METODE PEMBAYARAN</h2>
                                            <p>

                                                @if(!empty(Session::get('errors')))
                                                    {!! error_message(Session::get('errors')) !!}
                                                @endif
                                                <form action="{{ url('/user/check_account') }}" method="POST" id="confirm-form">
                                                    {!! csrf_field() !!}
                                                    <div>
                                                        <input type="text" name="account-name" placeholder="Bank Account Name" pattern="[a-zA-Z ]+" title="Hanya huruf yang diperbolehkan" required="required">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="amount" placeholder="Amount Transfered" pattern="[0-9]+" required="required">
                                                    </div>
                                                    <div>
                                                        <input type="hidden" name="purchase_code" value="{{ $data['invoice_detail']->purchase_code }}">
                                                        <input type="submit" value="KONFIRMASI" class="confirm-button">
                                                    </div>
                                                </form>
                                            </p>
                                    <?php } ?>
                                    <?php 
                                    }elseif($data["invoice_detail"]->status == 1){?>
                                        <h2><i class="fa fa-money" aria-hidden="true"></i> RINCIAN PEMBAYARAN</h2>
                                        <p>PEMBAYARAN DITERIMA</p>
                                    <?php 
                                    }elseif($data["invoice_detail"]->status == 2){?>
                                        <h2><i class="fa fa-money" aria-hidden="true"></i> RINCIAN PEMBAYARAN</h2>
                                        <p>DIBATALKAN</p>
                                    <?php 
                                    }else{?>
                                        <h2><i class="fa fa-money" aria-hidden="true"></i> RINCIAN PEMBAYARAN</h2>
                                        <p>
                                            <?php if($data["invoice_detail"]->master_payment_id == 1){?>
                                                    <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                                            Silahkan melakukan pembayaran ke:<br/>
                                                    <?php }else{?>
                                                            Anda telah melakukan konfirmasi:<br/>
                                                    <?php }?>
                                                    
                                                    Bank BCA<br/>
                                                    a/n PT. Berrybenka<br/>
                                                    No. Rekening : 546 032 7077
                                                
                                            <?php }elseif($data["invoice_detail"]->master_payment_id == 2){?>
                                            
                                                <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                                    Silahkan melakukan pembayaran ke:<br/>
                                                <?php }else{?>
                                                    Anda telah melakukan konfirmasi:<br/>
                                                <?php }?>
                                                Bank Mandiri<br/>
                                                a/n PT. Berrybenka<br/>
                                                No. Rekening : 165 000 042 7964
                                            <?php }elseif($data["invoice_detail"]->master_payment_id == 29){?>
                                            
                                                <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                                    Silahkan melakukan pembayaran ke:<br/>
                                                <?php }else{?>
                                                    Anda telah melakukan konfirmasi:<br/>
                                                <?php }?>
                                                Bank BNI
                                                <br>
                                                a/n PT Berrybenka
                                                <br>
                                                No Rekening :
                                                <strong>290 222 0008</strong>   
                                            <?php }elseif($data["invoice_detail"]->master_payment_id == 30){?>
                                            
                                                <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                                    Silahkan melakukan pembayaran ke:<br/>
                                                <?php }else{?>
                                                    Anda telah melakukan konfirmasi:<br/>
                                                <?php }?>
                                                Bank BRI
                                                <br>
                                                a/n PT Berrybenka
                                                <br>
                                                No Rekening :
                                                <strong>0505 01 000 151302</strong>   
                                            <?php }elseif($data["invoice_detail"]->master_payment_id == 19){?>
                                                Bayar di Tempat
                                            <?php }else{?>                                                                                                
                                                <?php                                                                                                                                                 
                                                if($data["invoice_detail"]->status == '2'){
                                                    $pay_stat = "DIBATALKAN";
                                                }elseif($data["invoice_detail"]->status == '1'){
                                                    $pay_stat = "PEMBAYARAN DITERIMA";
                                                }elseif($data["invoice_detail"]->status == '3'){
                                                    $pay_stat = "PEMBAYARAN DITANGGUHKAN";
                                                }else{
                                                    $pay_stat = 'BELUM TERBAYARKAN';
                                                }
                                                
                                                $payment_method = 'Non-Transfer';
                                                if(isset($data["invoice_detail"]->master_payment_name)){
                                                    $payment_method .= ' [ '.$data["invoice_detail"]->master_payment_name.' ]';
                                                }
                                                
                                                ?>
                                                <?php if ($data["invoice_detail"]->master_payment_id == 99 && $data["invoice_detail"]->status == '0') {
                                                ?>
                                                Kamu belum menyelesaikan pembayaran. Klik tombol "LANJUT KE KREDIVO.COM" di bawah ini untuk melanjutkan pembayaran<br /><br />
                                                <a href="{{ $data['invoice_detail']->kredivo_redirect_uri }}" class="confirm-button" style="background:#47BBE4;color:#fff;">LANJUT KE KREDIVO.COM</a>
                                                </div>
                                                <?php } else { ?>
                                                METODE PEMBAYARAN : {{ $payment_method }} <br /> STATUS : {{ $pay_stat }} 
                                                <?php } ?>
                                            <?php }?>
                                        </p>
                                    <?php 
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection