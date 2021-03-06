@extends('layouts.berrybenka.mobile.main')

@section('css')
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/cart.css') }}">
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/lebaran-notice.css') }}">
<style>
  .sub-btn{
      padding: 15px 0 !important;
  }
</style>
@endsection
<input id="ajax_url" type="hidden" value="{{ url('/') }}" />
<div id="loading_gif" class="loading-icon" style="z-index: 800">
  <div class="load-icon"><img src="{{ asset('berrybenka/mobile/img/loading.gif') }}"></div>
</div>
<div class="alamat-pengiriman list-alamat" id="list-alamat">
  <h1><i aria-hidden="true" class="fa fa-book"></i>Buku Alamat<i aria-hidden="true" class="fa fa-times"></i></h1>
  <div class="list-alamat-wrapper">
    <ul id="address-container">
      <!--Customer Address List Generated Here-->
    </ul>
    <!--Form New Customer-->
    <div id="new-address-container" class="editing-address">
      <!--Shipping Address-->
      <h1>Alamat Pengiriman Baru</h1>
      <textarea id="new-shipping-street" placeholder="Alamat" class="form-address"></textarea>
      <select id="new-shipping-province" class="form-address">
        <option selected="selected" disabled="disabled" value="">Propinsi</option>
        @foreach($list_province as $province)
          <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
        @endforeach
      </select>
      <select id="new-shipping-city" class="form-address">
        <option selected="selected" disabled="disabled" value="">Kota</option>
      </select>
      <input id="new-shipping-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="postal form-address">
      <input id="new-shipping-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" type="text" placeholder="No Telp" class="form-address">
      <!--###############-->
      
      <!--Billing Address-->
      <h1 class="form-billing" style="display:none;">Alamat Penagihan</h1>
      <textarea id="new-billing-street" placeholder="Alamat" class="form-address form-billing" style="display:none;"></textarea>
      <select id="new-billing-province" class="form-address form-billing" style="display:none;">
        <option selected="selected" disabled="disabled" value="">Propinsi</option>
        @foreach($list_province as $province)
          <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
        @endforeach
      </select>
      <select id="new-billing-city" class="form-address form-billing" style="display:none;">
        <option selected="selected" disabled="disabled" value="">Kota</option>
      </select>
      <input id="new-billing-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="postal form-address form-billing" style="display:none;">
      <input id="new-billing-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" type="text" placeholder="No Telp" class="form-address form-billing" style="display:none;">
      <!--###############-->
      
      <div class="set-as-deliver clear">
        <input type="checkbox" id="set-as-address" checked>
        <label for="set-as-address">Atur Juga Sebagai Alamat Penagihan</label>
      </div>
      <span id="new-shipping-alert" class="error-address error-msg-login" role="alert" style="float:left;width:100%;display:none;"> </span>
      <input id="new-submit-address" type="submit" value="Simpan" class="sub-btn mt40" mode="">
      <input type="submit" value="Batal" class="back-btn new-close-button">
    </div>
    <!--End Form New Customer-->
 
    <!--Form Existing Customer-->
    <div id="address-detail-container" class="editing-address">
      <!-- <input type="hidden" id="address-id" class="form-address" />
      <input type="hidden" id="address-type" class="form-address" />
      <input type="hidden" id="add-address-type" class="form-address" />
      <textarea id="address-street" placeholder="Alamat" class="form-address"></textarea>
      <select id="address-province" class="form-address">
        <option selected="selected" disabled="disabled" value="">Propinsi</option>
        @foreach($list_province as $province)
          <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
        @endforeach
      </select>
      <select id="address-city" class="form-address">
        <option selected="selected" disabled="disabled" value="">Kota</option>
      </select>
      <input id="address-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="postal form-address">
      <input id="address-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" type="text" placeholder="No Telp" class="form-address">
      <span id="address-alert" class="error-address error-msg-login" role="alert" style="float:left;width:100%;display:none;"> </span>
      <input id="submit-address" type="submit" value="Simpan" class="sub-btn mt40" mode="">
      <input type="submit" value="Batal" class="back-btn"> -->
    </div>
    <!--End Form Existing Customer-->
  </div>
</div>

@section('filter')
<div class="content-detail">
  <div class="cart-wraper">
    <div class="cart-header">
      <h1>Tas Belanja Saya</h1>
      <a href="#">Kembali Berbelanja</a>
    </div>
    
    <span id="msg-container" class="error-msg-login" style="display:none; float: left; width: 100%;">
      <i aria-hidden="true" class="fa fa-bell"></i>
      <i aria-hidden="true" class="fa fa-times"></i>
      <span id="msg-content">{{$err_msg}}</span>
    </span>
    
    @if($err_msg != NULL)
      <span class="error-msg-login" style="float: left; width: 100%;">
        <i aria-hidden="true" class="fa fa-bell"></i>
        <i aria-hidden="true" class="fa fa-times"></i>
        <span>{!! $err_msg !!}</span>
      </span>
    @endif
    
    @if(!empty($voucher))
      <span class="success-msg-login" style="float: left; width: 100%;">
        <i aria-hidden="true" class="fa fa-bell"></i>
        <i aria-hidden="true" class="fa fa-times"></i>
        <span>{{ $voucher['promotions_name_for_customer'] . ". " .  $voucher['promotions_notice'] }}</span>
      </span>
    @endif

    <div class="address-list">
      @if(empty($get_customer_address))
        <h1 style="margin-top:0px;">Alamat</h1>
        <div class="add-new"><a class="edit-address new-address"><i class="fa fa-plus" aria-hidden="true"></i>Tambah Alamat Baru</a></div>
      @else
      <?php $alamat_pengiriman = false; $alamat_penagihan = false; ?>
        @foreach($get_customer_address as $customer_address)
          @if($customer_address->address_type == 1 && $alamat_pengiriman == false)
            <h1 style="margin-top:0px;">Alamat Pengiriman</h1>
            <div class="alamat-pengiriman">
              <p>{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</p>
              <p>{{$customer_address->address_street}} - {{$customer_address->address_postcode}}</p>
              <p>{{$customer_address->address_city}} - {{$customer_address->address_province}}</p>
              <p>Nomor Handphone: {{$customer_address->address_phone}}</p>
              <a class="edit-address edit-shipping-address" href="javascript:void(0)"><i class="fa fa-pencil" aria-hidden="true"></i>Ubah Alamat</a>
            </div>
            <?php $alamat_pengiriman = true; ?>
          @elseif($customer_address->address_type == 2 && $alamat_penagihan == false)
            <h1>Alamat Penagihan</h1>
            <div class="alamat-pengiriman">
              <p>{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</p>
              <p>{{$customer_address->address_street}} - {{$customer_address->address_postcode}}</p>
              <p>{{$customer_address->address_city}} - {{$customer_address->address_province}}</p>
              <p>Nomor Handphone: {{$customer_address->address_phone}}</p>
              <a class="edit-address edit-billing-address" href="javascript:void(0)"><i class="fa fa-pencil" aria-hidden="true"></i>Ubah Alamat</a>
            </div>
            <?php $alamat_penagihan = true; ?>
          @endif
        @endforeach
      @endif
    </div>
    
    <div class="payment-method" {!! (empty($get_customer_address)) ? "style='display:none;'" : '' !!}>
      <h1>Metode Pembayaran</h1>
      
      @if(collect($list_payment_method_virtual_account)->count() > 0)
      <div class="payment-method-list">
        <ul>
          <h2>Transfer via Virtual Account (Disetujui Otomatis)</h2>
          @foreach($list_payment_method_virtual_account as $virtual_account)
             @if($virtual_account->master_payment_id == 28)
              <!--BCA Virtual Acount-->
              <li>
                <label class="clear">
                  <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $virtual_account->master_payment_id }}" {{(session('payment_method') == $virtual_account->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                  <span class="clear"><p>{{ $virtual_account->master_payment_name }}</p></span>
                  <div class="payment-detail">
                    <i class="fa fa-bell"></i>
                    <strong>PANDUAN PEMBAYARAN:</strong><br>
                    <ul>
                      <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan</li>
                      <li>Pembayaran dapat dilakukan lewat ATM BCA, KlikBCA, atau m-BCA</li>
                      <li>Setelah klik Pesan, Anda akan mendapatkan nomor BCA Virtual Account beserta langkah-langkah pembayaran</li>
                    </ul>
                  </div>
                </label>
              </li>
              @elseif($virtual_account->master_payment_id == 98)
              <!--Permata Virtual Acount-->
              <li>
                <label class="clear">
                  <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $virtual_account->master_payment_id }}" {{(session('payment_method') == $virtual_account->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                  <span class="clear"><p>{{ $virtual_account->master_payment_name }}</p></span>
                  <div class="payment-detail">
                    <i class="fa fa-bell"></i>
                    <strong>PANDUAN PEMBAYARAN:</strong><br>
                    <ul>
                      <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan</li>
                      <li>Pembayaran dapat dilakukan ATM Bersama, ATM Alto, ATM Prima, ATM Mandiri, atau ATM Permata</li>
                      <li>Setelah klik Pesan, Anda akan mendapatkan nomor Virtual Account beserta langkah-langkah pembayaran</li>
                    </ul>
                  </div>
                </label>
              </li>
            @endif
          @endforeach
        </ul>
      </div>
      @endif

      @if(collect($list_payment_method_bank_transfer)->count() > 0)  
      <div class="payment-method-list">
        <ul>
          <h2>Transfer Reguler (Disetujui Manual)</h2>
          @foreach($list_payment_method_bank_transfer as $bank_transfer)
            @if($bank_transfer->master_payment_id == 1 || $bank_transfer->master_payment_id == 2 || $bank_transfer->master_payment_id == 29 || $bank_transfer->master_payment_id == 30)
            <!--Transfer Method-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_0" value="{{ $bank_transfer->master_payment_id }}" {{(session('bank_transfer') == $bank_transfer->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $bank_transfer->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <p><i class="fa fa-bell"></i> <strong>PANDUAN PEMBAYARAN:</strong></p>                  
                  <ul>
                    <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan.</li>
                    <li>Mohon transfer tanpa pembulatan, sesuai angka yang tertera di tagihan.</li>
                    <li>Mohon cantumkan Kode Pembelian pada keterangan berita transfer.</li>
                    <li>Pembayaran untuk Kode Pembelian berbeda harus dilakukan secara terpisah.</li>
                    <li>Mohon lakukan konfirmasi setelah melakukan pembayaran.</li>
                  </ul>
                  <hr>
                  {!! $bank_transfer->master_payment_description !!}
                </div>
              </label>
            </li>
            @endif
          @endforeach
        </ul>
      </div>
      @endif

      @if(collect($list_payment_method_kartu_kredit)->count() > 0)  
      <div class="payment-method-list">
        <ul>
          <h2>Kartu Kredit/Debit</h2>
          @foreach($list_payment_method_kartu_kredit as $kartu_kredit)
            @if($kartu_kredit->master_payment_id == 20)
            <!--Mandiri Debit-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $kartu_kredit->master_payment_id }}" {{(session('payment_method') == $kartu_kredit->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $kartu_kredit->master_payment_name }}</p></span>
                <div class="payment-detail">
                  {!! Form::open(['id' => 'form-mandiri-debit', 'url' => 'checkout/apply_bank_promo']) !!}
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>
                  <ul>
                    <li>Pastikan nomor handphone Anda sudah terdaftar di sistem Bank Mandiri, agar Anda dapat melakukan pembayaran menggunakan debit Bank Mandiri berlogo Visa</li>
                    <li>Minimum transaksi adalah Rp 5.000, dan maksimum transaksi adalah Rp 20.000.000</li>
                  </ul>
                  <label>Nomor Kartu Debit Mandiri</label>
                  <input id="bin-number-mandiri" name="bin_number" type="text" maxlength="16" value="{{($bin_number_mandiri_raw != NULL) ? $bin_number_mandiri_raw : "" }}" required>
                  <label>Nama Lengkap sesuai kartu</label>
                  <input id="bin-name-mandiri" name="bin_name" type="text" value="{{($bin_name_mandiri != NULL) ? $bin_name_mandiri : "" }}" required>
                  <div class="card-number clear">
                    <div class="card left">
                      <label>Masa Berlaku</label>
                      <select id="bin-month-mandiri" name="month_exp" class="">
                        <option value="" disabled="disabled" {{($bin_month_mandiri == NULL) ? "selected='selected'" : "" }}>Bulan<!-- Month --></option>
                        <option value="1" {{($bin_month_mandiri == 1) ? "selected='selected'" : "" }}>1</option>
                        <option value="2" {{($bin_month_mandiri == 2) ? "selected='selected'" : "" }}>2</option>
                        <option value="3" {{($bin_month_mandiri == 3) ? "selected='selected'" : "" }}>3</option>
                        <option value="4" {{($bin_month_mandiri == 4) ? "selected='selected'" : "" }}>4</option>
                        <option value="5" {{($bin_month_mandiri == 5) ? "selected='selected'" : "" }}>5</option>
                        <option value="6" {{($bin_month_mandiri == 6) ? "selected='selected'" : "" }}>6</option>
                        <option value="7" {{($bin_month_mandiri == 7) ? "selected='selected'" : "" }}>7</option>
                        <option value="8" {{($bin_month_mandiri == 8) ? "selected='selected'" : "" }}>8</option>
                        <option value="9" {{($bin_month_mandiri == 9) ? "selected='selected'" : "" }}>9</option>
                        <option value="10" {{($bin_month_mandiri == 10) ? "selected='selected'" : "" }}>10</option>
                        <option value="11" {{($bin_month_mandiri == 11) ? "selected='selected'" : "" }}>11</option>
                        <option value="12" {{($bin_month_mandiri == 12) ? "selected='selected'" : "" }}>12</option>
                      </select>
                      /
                      <select id="bin-year-mandiri" name="year_exp" class="">
                        <option value="" disabled="disabled" {{($bin_year_mandiri == NULL) ? "selected='selected'" : "" }}>Tahun <!-- Year --></option>
                        <option value="2016" {{($bin_year_mandiri == '2016') ? "selected='selected'" : "" }}>2016</option>
                        <option value="2017" {{($bin_year_mandiri == '2017') ? "selected='selected'" : "" }}>2017</option>
                        <option value="2018" {{($bin_year_mandiri == '2018') ? "selected='selected'" : "" }}>2018</option>
                        <option value="2019" {{($bin_year_mandiri == '2019') ? "selected='selected'" : "" }}>2019</option>
                        <option value="2020" {{($bin_year_mandiri == '2020') ? "selected='selected'" : "" }}>2020</option>
                        <option value="2021" {{($bin_year_mandiri == '2021') ? "selected='selected'" : "" }}>2021</option>
                        <option value="2022" {{($bin_year_mandiri == '2022') ? "selected='selected'" : "" }}>2022</option>
                        <option value="2023" {{($bin_year_mandiri == '2023') ? "selected='selected'" : "" }}>2023</option>
                        <option value="2024" {{($bin_year_mandiri == '2024') ? "selected='selected'" : "" }}>2024</option>
                        <option value="2025" {{($bin_year_mandiri == '2025') ? "selected='selected'" : "" }}>2025</option>
                        <option value="2026" {{($bin_year_mandiri == '2026') ? "selected='selected'" : "" }}>2026</option>
                        <option value="2027" {{($bin_year_mandiri == '2027') ? "selected='selected'" : "" }}>2027</option>
                        <option value="2028" {{($bin_year_mandiri == '2028') ? "selected='selected'" : "" }}>2028</option>
                      </select>
                    </div>
                    <div class="cvv card left">
                      <label>CVV</label>
                      <input id="bin-cvv-mandiri" type="password" required="required" maxlength="3" name="cvv" value="{{($bin_cvv_mandiri != NULL) ? $bin_cvv_mandiri : "" }}">
                    </div>
                    <a id="btn-submit-mandiri-debit" class="input-group-addon" href="#" style="width:100%">GUNAKAN</a>
                  </div>
                  <div class="clear"></div>
                  {!! Form::close() !!}
                </div>
              </label>
            </li>
            @elseif($kartu_kredit->master_payment_id == 5)
            <!--Visa/MasterCard-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $kartu_kredit->master_payment_id }}" {{(session('payment_method') == $kartu_kredit->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $kartu_kredit->master_payment_name }}</p></span>
                <div class="payment-detail">
                  {!! Form::open(['id' => 'form-bank-promo', 'url' => 'checkout/apply_bank_promo']) !!}
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>
                  <label>Nomor Kartu Kredit</label>
                  <input id="bin-number" name="bin_number" type="text" maxlength="16" value="{{($bin_number_raw != NULL) ? $bin_number_raw : "" }}" required>
                  <label>Nama Lengkap sesuai kartu</label>
                  <input id="bin-name" name="bin_name" value="{{($bin_name != NULL) ? $bin_name : "" }}" type="text" required>
                  <div class="card-number clear">
                    <div class="card left">
                      <label>Masa Berlaku</label>
                      <select id="bin-month" name="month_exp" class="">
                        <option value="" disabled="disabled" {{($bin_month == NULL) ? "selected='selected'" : "" }}>Bulan<!-- Month --></option>
                        <option value="1" {{($bin_month == 1) ? "selected='selected'" : "" }}>1</option>
                        <option value="2" {{($bin_month == 2) ? "selected='selected'" : "" }}>2</option>
                        <option value="3" {{($bin_month == 3) ? "selected='selected'" : "" }}>3</option>
                        <option value="4" {{($bin_month == 4) ? "selected='selected'" : "" }}>4</option>
                        <option value="5" {{($bin_month == 5) ? "selected='selected'" : "" }}>5</option>
                        <option value="6" {{($bin_month == 6) ? "selected='selected'" : "" }}>6</option>
                        <option value="7" {{($bin_month == 7) ? "selected='selected'" : "" }}>7</option>
                        <option value="8" {{($bin_month == 8) ? "selected='selected'" : "" }}>8</option>
                        <option value="9" {{($bin_month == 9) ? "selected='selected'" : "" }}>9</option>
                        <option value="10" {{($bin_month == 10) ? "selected='selected'" : "" }}>10</option>
                        <option value="11" {{($bin_month == 11) ? "selected='selected'" : "" }}>11</option>
                        <option value="12" {{($bin_month == 12) ? "selected='selected'" : "" }}>12</option>
                      </select>
                      /
                      <select id="bin-year" name="year_exp" class="">
                        <option value="" disabled="disabled" {{($bin_year == NULL) ? "selected='selected'" : "" }}>Tahun <!-- Year --></option>
                        <option value="2016" {{($bin_year == '2016') ? "selected='selected'" : "" }}>2016</option>
                        <option value="2017" {{($bin_year == '2017') ? "selected='selected'" : "" }}>2017</option>
                        <option value="2018" {{($bin_year == '2018') ? "selected='selected'" : "" }}>2018</option>
                        <option value="2019" {{($bin_year == '2019') ? "selected='selected'" : "" }}>2019</option>
                        <option value="2020" {{($bin_year == '2020') ? "selected='selected'" : "" }}>2020</option>
                        <option value="2021" {{($bin_year == '2021') ? "selected='selected'" : "" }}>2021</option>
                        <option value="2022" {{($bin_year == '2022') ? "selected='selected'" : "" }}>2022</option>
                        <option value="2023" {{($bin_year == '2023') ? "selected='selected'" : "" }}>2023</option>
                        <option value="2024" {{($bin_year == '2024') ? "selected='selected'" : "" }}>2024</option>
                        <option value="2025" {{($bin_year == '2025') ? "selected='selected'" : "" }}>2025</option>
                        <option value="2026" {{($bin_year == '2026') ? "selected='selected'" : "" }}>2026</option>
                        <option value="2027" {{($bin_year == '2027') ? "selected='selected'" : "" }}>2027</option>
                        <option value="2028" {{($bin_year == '2028') ? "selected='selected'" : "" }}>2028</option>
                      </select>
                    </div>
                    <div class="cvv card left">
                      <label>CVV</label>
                      <input type="password" required="required" maxlength="3" name="cvv" id="bin-cvv" value="{{($bin_cvv != NULL) ? $bin_cvv : "" }}">
                    </div>
                    <a id="btn-submit-bank-promo" class="input-group-addon" href="#" style="width:100%">GUNAKAN</a>
                  </div>
                  <div class="clear"></div>
                  {!! Form::close() !!}
                </div>
              </label>
            </li>
            @endif
          @endforeach
        </ul>
      </div>
      @endif

      @if(collect($list_payment_method_internet_banking)->count() > 0) 
      <div class="payment-method-list">
        <ul>
          <h2>Internet Banking</h2>
          @foreach($list_payment_method_internet_banking as $internet_banking)
            @if($internet_banking->master_payment_id == 3)
            <!--Klik BCA-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $internet_banking->master_payment_id }}" {{(session('payment_method') == $internet_banking->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $internet_banking->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>
                  <ul>
                    <li>Mohon Login ke KlikBCA dengan UserID yang sama</li>
                    <li>Pembayaran maksimal 2 jam setelah pemesanan, minimum transaksi adalah Rp 10.000</li>
                    <li>Pilih "Pembayaran E-commerce" > Kategori "Baju / Aksesoris" > Nama Perusahaan "BERRYBENKA" > pilih Lanjut</li>
                    <li>Pilih transaksi yang ingin dibayarkan > pilih Lanjut</li>
                    <li>Pembayaran akan dikonfirmasi dan pesanan akan diproses secepatnya</li>
                  </ul>
                  <label>Masukkan User ID KlikBCA Anda.</label>
                  <input id="txt-klikbca-user-id" name="klikbcauserid" maxlength="12" type="text" placeholder="Enter your KlikBCA User ID" required>
                  <div class="clear"></div>
                </div>
              </label>
            </li>
            @elseif($internet_banking->master_payment_id == 4)
            <!--KlikPay-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $internet_banking->master_payment_id }}" {{(session('payment_method') == $internet_banking->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $internet_banking->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>
                  <ul>
                    <li>Pastikan alamat domain adalah https://klikpay.klikbca.com</li>
                    <li>Pembayaran maksimal 2 jam setelah pemesanan, minimum transaksi adalah Rp 10.000</li>
                    <li>Masukkan email dan password > pastikan informasi transaksi sudah benar > pilih jenis pembayaran KlikBCA > pilih Kirim</li>
                    <li>Pilih Kirim OTP > masukkan kode OTP yang dikirimkan ke handphone Anda > pilih Bayar</li>
                    <li>Pembayaran akan dikonfirmasi dan pesanan akan diproses secepatnya</li>
                  </ul>                  
                </div>
              </label>
            </li>            
            @endif
          @endforeach
        </ul>
      </div>
      @endif

      @if(collect($list_payment_method_others)->where('master_payment_id', 19)->count() > 0)
      <div class="payment-method-list">
        <ul>
          <h2>Bayar di Tempat</h2>            
          @foreach($list_payment_method_others as $cod)
            @if($cod->master_payment_id == 19 && $is_cod_available)
            <!--COD-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $cod->master_payment_id }}" {{(session('payment_method') == $cod->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $cod->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>
                  <ul>
                    <li>Maksimum transaksi adalah Rp 1.000.000</li>
                    <li>Transaksi COD yang sudah dikirimkan tidak dapat dibatalkan</li>
                  </ul> 
                </div>
              </label>
            </li>                       
            @elseif($cod->master_payment_type_transfer == 4)
            <!--Popup Store-->
              @if(!empty($is_popup_store_available))
                @foreach($is_popup_store_available as $key => $popup_store)
                  <li id="popup-store-{{ $cod->master_payment_id }}" {!!($cod->master_payment_id == $is_popup_store_available[$key]->master_payment_id) ? '' : 'style="display: none;"' !!}>
                    <label class="clear">
                      <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $cod->master_payment_id }}" {{(session('payment_method') == $cod->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                      <span class="clear"><p>{{ $cod->master_payment_name }}</p></span>
                      <div class="payment-detail">
                        <i class="fa fa-bell"></i>
                        <strong>PANDUAN PEMBAYARAN:</strong><br>                                                
                        <label>{{ $cod->master_payment_description }}</label>
                      </div>
                    </label>
                  </li>
                @endforeach
              @endif
            @endif
          @endforeach
        </ul>
      </div>
      @endif
            
      @if(collect($list_payment_method_others)->count() > 0)
          <div class="payment-method-list">
            <ul>
            <h2>Lainnya</h2>
            @foreach($list_payment_method_others as $others)
              @if($others->master_payment_id == 99)
              <!--  KREDIVO -->
              <li>
              <label>
                <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" {{(session('payment_method') == $others->master_payment_id) ? "checked" : "" }} id="RadioGroup2_0" >    
                <span class="clear"><p>{{ $others->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong>
                  <br>
                    <ul>
                        <li>Pilih Pesan > diarahkan ke website Kredivo > silakan Login menggunakan akun Kredivo atau <a href="https://app.kredivo.com/#/login" target="_blank"><strong>Daftar</strong></a> jika belum memiliki akun Kredivo</li>
                        <li>Saat ini metode pembayaran Kredivo hanya berlaku untuk pemilik akun Kredivo di Jabodetabek, Bandung, Semarang, Surabaya, Denpasar, Palembang dan Medan</li>
                        <li>Untuk transaksi menggunakan metode pembayaran Kredivo tidak berlaku partial retur (sehingga retur harus dilakukan atas keseluruhan barang, atau akan ditolak) </li>
                        <li>Untuk transaksi Bayar dalam 30 Hari akan dikenakan bunga 0%.</li>
                        <li>Untuk transaksi Cicilan akan dikenakan bunga 2.95%, berlaku untuk 1 item produk dalam 1 pesanan, dengan harga antara Rp 1.500.000-Rp 20.000.000. Anda diwajibkan membayar down payment 20% dari total transaksi.</li>
                        <li>Syarat dan ketentuan lengkap bisa lihat <a href="{{ url('/') }}/kredivo" target="_blank"><strong>disini</strong></a></li>
                    </ul>
                  <br />                            
                  <p class="mb10">  Bayar menggunakan Kredivo. </p>
                  <div id="select-kredivo" style="margin-top:10px;" class="card-number"></div>
                  <div id="kredivo-note"></div>
                </div>                            
              </label>
            </li>
            @elseif($others->master_payment_id == 135)
            <li>
              <label class="clear">
                 <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" {{(session('payment_method') == $others->master_payment_id) ? "checked" : "" }} id="RadioGroup2_0" >    
                <span class="clear"><p>{{ $others->master_payment_name }}</p></span>
                <div class="payment-detail">                  
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>                  
                  <ul>
                   <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan</li>
                   <li>Mohon tidak menutup halaman browser anda setelah selesai melakukan pembayaran T-Cash sebelum dialihkan kembali kehalaman {{ucfirst(get_domain()['domain_name'])}}.</li>
                  </ul>
                </div>
              </label>
            </li>
            @elseif($others->master_payment_id == 24)
            <!--Indomaret-->
            <li>
              <label class="clear">
                <input class="rad-payment-method" name="rad-payment-method" type="radio" id="RadioGroup2_2" value="{{ $others->master_payment_id }}" {{(session('payment_method') == $others->master_payment_id) ? "checked" : "" }} name="RadioGroup2">
                <span class="clear"><p>{{ $others->master_payment_name }}</p></span>
                <div class="payment-detail">                  
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>                  
                  <ul>
                   <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan</li> 
                   <li>Anda akan mendapatkan Kode Pembayaran setelah klik Pesan</li>
                   <li>Beritahukan Kode Pembayaran kepada kasir Indomaret, lalu bayar di kasir</li>
                  </ul>
                </div>
              </label>
            </li>
            <?php /*
            @elseif($others->master_payment_id == 343)
            <!--Go-Pay-->
            <li>
              <label class="clear">
                <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" id="RadioGroup2_0"  >
                <span><p>{{ $others->master_payment_name }}</p></span>
                <div class="payment-detail">
                  <i class="fa fa-bell"></i>
                  <strong>PANDUAN PEMBAYARAN:</strong><br>                       
                  <ul>
                   <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan</li>
                  </ul>
                </div>
              </label>
            </li>
            */ ?>
            @endif
          @endforeach
        </ul>
      </div>
      @endif
    </div>

    <div class="delivery-method" {!! (empty($get_customer_address)) ? "style='display:none;'" : '' !!}>
      <h1>Metode Pengiriman</h1>
      <!--div class="lebaran-notice">
        <p>Dalam rangka libur Idul Fitri 2017, maka untuk memastikan agar pesanan kamu sampai sebelum Idul Fitri, harap melakukan pesanan sebelum tanggal di bawah ini:</p>
          <ul>
            <li> Jabodetabek: 21 Juni 2017</li>
              <li>Jawa dan Sumatera: 19 Juni 2017</li>
              <li>Luar Jawa dan Sumatera: 15 Juni 2017</li>
          </ul>
          <p>Pengiriman pesanan setelah tanggal di atas akan kembali normal per tanggal 3 Juli 2017.</p>

      </div-->
      <ul id="shipping-method-container">
        @if(!empty($get_customer_address))
          @foreach($list_shipping_method as $shipping_method)
          <li>
            <label class="clear">
              <input type="radio" class="rad-shipping-method" name="rad-shipping-method" value="{{$shipping_method['shipping_type']}}" {!! ($shipping_method['shipping_type'] == 1) ? 'checked' : '' !!} {!! ($shipping_method['is_available'] == TRUE) ? '' : 'disabled' !!} id="RadioGroup1_0">
              <span><p>{!!$shipping_method['text']!!}</p></span>
            </label>
          </li>
          @endforeach
        @endif
      </ul>
    </div>

    <div class="cart-list chck">
      <h1 class="cart-title">Daftar belanja Anda <span>({{count($fetch_cart)}} Item)</span><i class="fa fa-angle-up" aria-hidden="true"></i></h1>
      <ul>
        @foreach($fetch_cart as $cart)
          <li>
            <div class="cart-img left"><a href="#"><img alt="" src="{{IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH.$cart['image']}}"></a></div>
            <div class="cart-detail left">
              <h1>{{ $cart['name'] }}</h1>
              <?php /*<h2>{{ $cart['brand_name'] }}</h2>*/?>
              <p>Warna <span>: {{ $cart['color_name'] }}</span></p>
              <p>Ukuran <span>: {{ $cart['size'] }}</span></p>
              <p>JUMLAH <span>: {{ $cart['qty'] }} </span></p>
              <p>IDR {{ number_format($cart['subtotal']) }}</p>
            </div>
          </li>
        @endforeach
      </ul>
    </div>
    <div class="cart-detail-payment">
      <div class="table-purchase">
        <span class="purchase-label">TOTAL PEMBELIAN</span>
        <span id="txt-subtotal" class="purchase-value">IDR {{number_format($total['base_subtotal'])}}</span>
      </div>
      <div class="table-purchase">
        <span class="purchase-label">KODE PEMBAYARAN</span>
        <span id="txt-paycode" class="purchase-value">(+) IDR {{number_format($total['paycode'])}}</span>
      </div>
      
      @if(session('freegift_auto'))
        <span class="purchase-line freegift-auto-content"></span>
        @foreach($freegift_auto as $key => $value)
          @if($freegift_auto[$key]['promotions_value'] > 0)
            <div class="table-purchase freegift-auto-content">
              <span class="purchase-label">{{ $freegift_auto[$key]['promotions_name'] }}</span>
              <span id="txt-promo" class="purchase-value">(-) IDR {{ number_format($freegift_auto[$key]['promotions_value']) }}</span>
            </div>
          @elseif($freegift_auto[$key]['promotions_mode'] == 4 || $freegift_auto[$key]['promotions_mode'] == 5)
            <div class="table-purchase freegift-auto-content">
              <span class="purchase-label">{{ $freegift_auto[$key]['promotions_name'] }}</span>
            </div>
          @endif
        @endforeach
      @endif
      
      @if(!empty($voucher))
        @if($voucher['promotions_value'] > 0)
          <span class="purchase-line"></span>
          <div class="table-purchase">
            <span class="purchase-label">{{ $voucher['promotions_name'] }}</span>
            <span id="txt-promo" class="purchase-value">(-) IDR {{number_format($voucher['promotions_value'])}}</span>
          </div>
        @elseif($voucher['promotions_mode'] == 4 || $voucher['promotions_mode'] == 5)
          <div class="table-purchase freegift-auto-content">
            <span class="purchase-label">{{ $voucher['promotions_name'] }}</span>
          </div>
        @endif
      @endif
      
      @if(session('freegift'))
        <span class="purchase-line freegift-content"></span>
        @foreach($freegift as $key => $value)
          @if($freegift[$key]['promotions_value'] > 0)
            <div class="table-purchase freegift-content">
              <span class="purchase-label">{{ $freegift[$key]['promotions_name'] }}</span>
              <span id="txt-promo" class="purchase-value">(-) IDR {{ number_format($freegift[$key]['promotions_value']) }}</span>
            </div>
          @elseif($freegift[$key]['promotions_mode'] == 4 || $freegift[$key]['promotions_mode'] == 5)
            <div class="table-purchase freegift-auto-content">
              <span class="purchase-label">{{ $freegift[$key]['promotions_name'] }}</span>
            </div>
          @endif
        @endforeach
      @endif
      
      @if(session('benka_point'))
        <span class="purchase-line"></span>
        <div class="table-purchase">
          <span class="purchase-label">Benka Point</span>
          <span id="txt-promo" class="purchase-value">(-) IDR {{number_format(session('benka_point'))}}</span>
        </div>
      @endif
      
      <span class="purchase-line"></span>
      <div class="table-purchase">
        <span class="purchase-label">PENGIRIMAN</span>
        <span id="txt-shipping-cost" class="purchase-value">{{($total['is_freeshipping'] || $total['is_freeshipping_promotions']) ? 'FREE' : '(+) IDR '.number_format($total['shipping_cost'])}}</span>
      </div>
      <span class="purchase-line"></span>
      <div class="table-purchase">
        <span class="purchase-label"><strong>GRAND TOTAL</strong></span>
        <span id="txt-grandtotal" class="purchase-value">IDR {{number_format($total['grand_total'])}}</span>
      </div>
      <span class="purchase-line"></span>
      
      @if($promotions_eksklusif == 0 || $promotions_eksklusif == 2)
      <div class="kupon-wrapper">
        {!! Form::open(['id' => 'form-voucher', 'url' => 'checkout/apply_voucher']) !!}
        <label>Masukan Kupon Anda</label>
        <div class="input-group">
          <input type="text" class="form-control" id="voucher_code" name="voucher_code" value="{{(session('voucher_code')) ? session('voucher_code') : '' }}">
          <a id="btn-submit-voucher" class="input-group-addon" href="#">GUNAKAN</a>
        </div>
        {!! Form::close() !!}
      </div>
      @endif
      
      @if(session('freegift'))
        @foreach($freegift as $key => $value)
          @if($freegift[$key]['promotions_notice'] != '')
            <div id="freegift-notif" class="freegift-notif">
              {{ $freegift[$key]['promotions_notice'] }}
            </div>
          @endif
        @endforeach
      @endif
      
      @if($customer_credit > 0 && $allow_benka_point == 1)
      <div class="kupon-wrapper benka-point-input">
        {!! Form::open(['id' => 'form-benka-point', 'url' => 'checkout/apply_benka_point']) !!}
        <label>Benka Poin</label>
        <div class="benka-point-input-wrapper">
          <p>Masukan Jumlah Benka Poin yang ingin Anda gunakan sebagai diskon pada saat checkout</p>
          <p>Anda memiliki {{number_format($customer_credit)}} Benka Poin.</p>
          <div class="input-group">
            <input type="text" class="form-control benka-form" id="benka_point" name="benka_point">
            <a id="btn-submit-benka-point" class="input-group-addon" href="#">GUNAKAN</a>
          </div>
<!--          <div class="info-benka-point">
            <p><i aria-hidden="true" class="fa fa-info-circle"></i> Informasi Lebih Lanjut</p>
            <ul>
              <li>Setiap kali Anda berbelanja senilai IDR 50.000, Anda akan mendapatkan 1 (satu) Benka Poin, Berlaku kelipatannya</li>
              <li>1 Benka Poin setara dengan IDR 1</li>
            </ul>
          </div>-->
        </div>
        {!! Form::close() !!}
      </div>
      @endif
    </div>
    {!! Form::open(['id' => 'form-submit-order', 'url' => 'checkout/insert_order_process']) !!}
        <input id="client-key" type="hidden" value="{{$veritrans['client_key']}}" />
        <input id="veritrans-api" type="hidden" value="{{$veritrans['js']}}" />
        <input id="transaction-queuing" type="hidden" value="{{$transaction_queuing}}" />
        <input id="queuing-periodic-time" type="hidden" value="{{$queuing_periodic_time}}" />
        <input id="max-queuing-trying" type="hidden" value="{{$max_queuing_trying}}" />
        <input id="klikbca-user-id" name="klikbca-user-id" type="hidden" />
        <input id="acquiring-bank" type="hidden" />
        <input id="token-id" name="token-id" type="hidden" />
        <input id="cc-holder" name="cc-holder" type="hidden" />
        <input id="final-grand-total" name="final-grand-total" type="hidden" value="{{$total['grand_total']}}" />
        <!-- kredivo payment type-->
        <input id="kredivo-payment-type" name="kredivo-payment-type" type="hidden" value="{{ session('kredivo_type') ? session('kredivo_type') : '30_days' }}"/>
        <!-- end kredivo payment type-->
        <input id="btn-submit-order" type="submit" value="Proses Pembayaran" class="sub-btn" {!!(empty($get_customer_address)) ? 'disabled' : ''!!} >                
    {!! Form::close() !!}
    <a href="/checkout/cart" class="back-btn">Kembali</a>
  </div>
</div>
@endsection

@section('js')
<script src="{{ asset('js/veritrans.js') }}"></script>
<script src="{{ asset('js/ajaxq.js') }}"></script>
<script src="{{ asset('js/mobile/submit-order.js?t=').date('YmdHis') }}"></script>
@endsection


@section('marketing-tag')
<script type="text/javascript">
<?php $user = \Auth::user(); ?>
var detail_product336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif
  }    
var marketing336CC993E54E = {!! $marketing_data !!}; 
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.berrybenka.mobile.submit-order')
@endif

@endsection
