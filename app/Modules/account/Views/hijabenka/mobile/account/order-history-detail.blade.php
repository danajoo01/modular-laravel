@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/cart.css') }}">
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/account.css') }}">
@endsection

@section('content')
<div class="content-detail">
    <div class="cart-wraper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        <div class="cart-list">
            <ul>
                <?php $subtotal = 0;?>
                @foreach ($data["order_product"] as $row)
                    <li>
                        <div class="cart-img left"><a href="#"><img src="{{ IMAGE_PRODUCTS_CACHE_PATH }}238x358/product/zoom/{{ $row['image_name'] }}" alt="{{ $row['image_name'] }}"></a></div>
                        <div class="cart-detail left">
                            <h1>{{ $row["product_name"] }}</h1>
                            {{-- <h2>{{ $row["brand_name"] }}</h2> --}}
                            <p>SKU <span>: {{ $row["sku"] }}</span></p>
                            <p>Warna <span>: {{ $row["color"] }}</span></p>
                            <p>Ukuran <span>: {{ $row["product_size"] }}</span></p>
                            <p>Jumlah <span>: {{ $row["quantity"] }}</span></p>
                            <p>
                                @if ($row["special_price"] > 0)
                                        IDR {{ number_format($row["special_price"],0,".",".") }}
                                @elseif($row["discount_price"] == 0)
                                        {{ number_format($row["each_price"],0,".",".") }}
                                @else
                                        {{ number_format($row["discount_price"],0,".",".") }}
                                @endif
                            </p>
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
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="cart-detail-payment">
            <div class="table-purchase">
                <span class="purchase-label">SUBTOTAL</span> 
                <span class="purchase-value">IDR {{ number_format($subtotal,0,".",".") }}</span>
            </div>
            <div class="table-purchase">
                <span class="purchase-label">BIAYA PENGIRIMAN</span> 
                <span class="purchase-value">IDR {{ number_format($data["invoice_detail"]->shipping_finance,0,".",".") }}</span>
            </div>
            <div class="table-purchase">
                <span class="purchase-label">KODE UNIK PEMBAYARAN</span> 
                <span class="purchase-value">IDR {{ $data["invoice_detail"]->paycode }}</span>
            </div>
            @foreach($data["discount"] as $discount)
            <span class="purchase-line"></span>
            <div class="table-purchase">
                <span class="purchase-label">
                    {{ $discount->discount_nfc_or_discount }}
                </span>
                <span class="purchase-value">-IDR {{ number_format($discount->discount_value,0,".",".") }}</span>
            </div>
            @endforeach
            <span class="purchase-line"></span>
            <div class="table-purchase">
                <span class="purchase-label">PENGGUNAAN KREDIT ANDA</span>                  
                <span class="purchase-value" id="txt-shipping-cost">IDR {{ number_format($data["invoice_detail"]->credit_use,0,".",".") }}</span>
            </div>
            <span class="purchase-line"></span>
            <div class="table-purchase">
                <span class="purchase-label"><strong>GRAND TOTAL</strong></span> 
                <span class="purchase-value" id="txt-grandtotal">IDR {{ number_format($data["invoice_detail"]->grand_total,0,".",".") }}</span>
            </div>
            <span class="purchase-line"></span>
        </div>
        <div class="address-list nomargin-noborder">
            <h1 class="">Alamat Pengiriman</h1>
            <div class="alamat-pengiriman">
                <p>{{ $user->customer_fname }} {{ $user->customer_lname }}</p>
                <p>{{ $data["invoice_detail"]->shipping_address }}</p>
                <p>{{ $data["invoice_detail"]->shipping_area }} - {{ $data["invoice_detail"]->shipping_name }}</p>
                <p>Nomor Handphone: {{ $user->customer_phone }}</p>
            </div>
            <h1 class="">Status Transaksi</h1>
            <div class="alamat-pengiriman">
                <?php 
                if(is_null($data["invoice_detail"]->confirm_transfer_by) && (in_array($data["invoice_detail"]->master_payment_id,array('1','2','29','30'))) && $data["invoice_detail"]->status == 0){
                    if( in_array($data["invoice_detail"]->purchase_status, array('0', '1') ) ){
                ?>
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
                                    <input type="text" name="amount" placeholder="Amount Transfered" pattern="[0-9]+" title="Hanya angka yang diperbolehkan" required="required">
                                </div>
                                <div>
                                    <input type="hidden" name="purchase_code" value="{{ $data['invoice_detail']->purchase_code }}">
                                    <input type="submit" value="KONFIRMASI" class="confirm-button">
                                </div>
                            </form>
                        </p>
                <?php }
                }elseif($data["invoice_detail"]->status == 1){?>
                    <p>PEMBAYARAN DITERIMA</p>
                <?php 
                }elseif($data["invoice_detail"]->status == 2){?>
                    <p>DIBATALKAN</p>
                <?php 
                }else{?>
                    <p>
                        <?php if($data["invoice_detail"]->master_payment_id == 1){?>
                                <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                        Silahkan melakukan pembayaran ke:<br/>
                                <?php }else{?>
                                        Anda telah melakukan konfirmasi:<br/>
                                <?php }?>
                                
                                Bank BCA<br/>
                                a/n PT. hijabenka<br/>
                                No. Rekening : 546 032 7077
                            
                        <?php }elseif($data["invoice_detail"]->master_payment_id == 2){?>
                        
                            <?php if(is_null($data["invoice_detail"]->confirm_transfer_by)){?>
                                Silahkan melakukan pembayaran ke:<br/>
                            <?php }else{?>
                                Anda telah melakukan konfirmasi:<br/>
                            <?php }?>
                            Bank Mandiri<br/>
                            a/n PT. hijabenka<br/>
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
                                    $pay_stat = NULL;
                                }
                                ?>
                            <?php if ($data["invoice_detail"]->master_payment_id == 99 && $data["invoice_detail"]->status == '0') {
                            ?>
                            Kamu belum menyelesaikan pembayaran. Klik tombol "LANJUT KE KREDIVO.COM" di bawah ini untuk melanjutkan pembayaran<br /><br />
                            <a href="{{ $data['invoice_detail']->kredivo_redirect_uri }}" style="background:#47BBE4;color:#fff;font-family: 'gotham',arial;text-align: center;">LANJUT KE KREDIVO.COM</a>
                            </div>
                            <?php } else if ($data["invoice_detail"]->master_payment_id == 135 && $data["invoice_detail"]->status == '0') {
                            ?>
                            METODE PEMBAYARAN : Non-Transfer <br /> STATUS : {{ $pay_stat }} <br /><br />
                            Kamu belum menyelesaikan pembayaran. Klik tombol "LANJUT KE T-CASH" di bawah ini untuk melanjutkan pembayaran<br />
                            <a href="{{ $data['tcash_detail']['tcash_redirect'] }}" style='background:#ED1B24;color:#fff;padding: 8px 15px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font: 16px "futura", sans-serif;display: inline-block;'>LANJUT KE T-CASH</a>
                            <br>
                            <br>
                            <p style="font-size: 15px;font-weight: bold;margin: 0 0 5px;">Jika sudah melakukan pembayaran tapi status masih "BELUM TERBAYARKAN", silakan lakukan konfirmasi manual. </p>
                            <a href="{{ $data['tcash_detail']['tcash_confirmation'] }}" style='background:#47BBE4;color:#fff;padding: 8px 15px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font: 16px "futura", sans-serif;display: inline-block;'>Konfirmasi Manual</a>
                            </div>
                            <?php } else if ($data["invoice_detail"]->master_payment_id == 343 && $data["invoice_detail"]->status == '0') { ?>
                            METODE PEMBAYARAN : Non-Transfer [GO-PAY] <br /> STATUS : Belum Terbayarkan <br /><br />
                            Untuk melanjutkan pembayaran, silakan buka menggunakan Desktop browser.
                            <?php /*
                            Kamu belum menyelesaikan pembayaran. Klik tombol "BAYAR DENGAN GO-PAY" di bawah ini untuk melanjutkan pembayaran<br /><br>
                            <a href="{{ $data['gopay_url'] }}" target="_blank" style="background:#295DB3;color:#fff;padding: 8px 15px;letter-spacing: 2px;text-transform: uppercase;border-radius: 5px;cursor: pointer;font: 16px 'futura', sans-serif;display: inline-block;">BAYAR DENGAN GO-PAY</a>
                            */ ?>
                            <?php } else { ?>
                            METODE PEMBAYARAN : Non-Transfer <br /> STATUS : Belum Terbayarkan
                            <?php } ?>
                        <?php }?>
                    </p>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
</div>
@endsection