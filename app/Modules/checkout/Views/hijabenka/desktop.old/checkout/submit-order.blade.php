@extends('layouts.hijabenka.desktop.main')
 
@section('css')
  <link rel="stylesheet" href="{{ asset('hijabenka/desktop/css/cart.css') }}">
  <style>
    #loading_gif{
      width: 100%;
      height: 100%;
      top: 0px;
      left: 0px;
      position: fixed;
      display: block;
      background: rgba(255,255,255,.8);
      z-index: 9999;
      text-align: center;
    }
  </style>
@endsection

@section('content')

<div class="content">
  <!-- Loading div -->
  <div id="loading_gif" style="z-index: 800">
    <div class="load-icon">
      <img src="{{ asset('hijabenka/desktop/img/loading.gif') }}">
    </div>
  </div>
  <!-- ******** -->
  
  <input id="ajax_url" type="hidden" value="{{ url('/') }}" />
  
  <div class="cart-wrapper">
  	<div class="wrapper">
    	<div class="cart-help">
        <ul class="need-help">
          <li>
              <span class="icon"> <i class="fa fa-info-circle"></i></span>
              Butuh Bantuan? Hubungi Customer Service Kami<br><strong>Jam Kerja, Senin - Jumat (9.00 - 18.00)<br>Sabtu - Minggu (8.00 - 17.00)</strong>
          </li>
          <?php /*
          <li>
          	<span class="icon"> <i class="fa fa-phone"></i>
          	</span> Kontak Kami <br> <strong>021 2520555</strong>
          </li>
          */?>
          <li>
          	<span class="icon"> <i class="fa fa-envelope"></i>
         		</span> Email Kami <br> <strong><a href="mailto:cs@berrybenka.com">cs@berrybenka.com</a></strong>
          </li>
          <li>
          	<span class="icon"> <i class="fa fa-comments"></i>
          	</span> SMS Kami <br> <strong>0812 8880 9992</strong>
          </li>
        </ul>
      </div>
    </div>
  </div>
  
  <div class="cart-list-wrapper">
    @if($err_msg !== NULL)
      <span class="error-msg-login" style="width: 1079px; margin: 10px auto;">
        <i aria-hidden="true" class="fa fa-bell"></i>
        <i aria-hidden="true" class="fa fa-times"></i>
        <span>{!! $err_msg !!}</span>
      </span>
    @endif
    <span id="msg-container" class="error-msg-login" style="display:none; width: 1079px; margin: 10px auto;">
      <i aria-hidden="true" class="fa fa-bell"></i>
      <i aria-hidden="true" class="fa fa-times"></i>
      <span id="msg-content"></span>
    </span>
    <div class="wrapper">
      <div class="checkout-list-wrapper-left left">
        <div class="cart-address">
          <h1><i class="fa fa-book" aria-hidden="true"></i>Buku Alamat</h1>

          <!--CUSTOMER NOT HAVE ADDRESS-->
          <div id='new-address-container' class="add-address" style="display: none; border-bottom: 1px solid #dedede;margin-bottom: 15px;">
            <label for="alamat-other" class="tambah-alamat-label"><p>Tambahkan Alamat Pengiriman Baru</p></label><br>
            <div class="form-tambah-alamat">
              <p>
                <label>Nama</label>
                <label>{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</label>
              </p>
              <p>
                <label>Alamat</label>
                <span>
                  <textarea id="new-shipping-street" name="new-shipping-street"></textarea>
                  <select id="new-shipping-province" name="new-shipping-province" class="tambah-alamat-propinsi left">
                    <option selected="selected" disabled="disabled" value="">Propinsi</option>
                    @foreach($list_province as $province)
                      <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
                    @endforeach
                  </select>
                  <select id="new-shipping-city" name="new-shipping-city" class="tambah-alamat-kota left">
                    <option selected="selected" disabled="disabled" value="">Kota</option>
                    <option value="1">1</option>
                  </select>
                  <input id="new-shipping-postcode" name="new-shipping-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="tambah-alamat-postal left">
                </span>
              </p>
                <p>
                  <label>Telp</label>
                  <input id="new-shipping-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" name="new-shipping-phone" type="text">
                </p>
                <p>
                	<span>
                  	<input type="checkbox" id="tambah-alamat-checkbox" checked="">
                    <label for="tambah-alamat-checkbox">Atur Sebagai Alamat Penagihan</label>
                  </span>
                </p>
                <div class="add-address tambah-alamat-penagihan mt40" style="display: none;">
                  <label for="alamat-other" class="tambah-alamat-label"><p>Alamat Penagihan</p></label>
                  <div class="form-tambah-alamat">
                    <p>
                      <label>Alamat</label>
                      <span>
                          <textarea id="new-billing-street" name="new-billing-street"></textarea>
                          <select id="new-billing-province" name="new-billing-province" class="tambah-alamat-propinsi left">
                              <option selected="selected" disabled="disabled" value="">Propinsi</option>
                              @foreach($list_province as $province)
                                <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
                              @endforeach
                          </select>
                          <select id="new-billing-city" name="new-billing-city" class="tambah-alamat-kota left">
                              <option selected="selected" disabled="disabled" value="">Kota</option>
                              <option value="1">1</option>
                          </select>
                          <input id="new-billing-postcode" name="new-billing-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="tambah-alamat-postal left">
                      </span>
                    </p>
                    <p>
                      <label>Telp</label>
                      <input id="new-billing-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" name="new-billing-phone" type="text">
                    </p>
                  </div>
                </div>
                <span id="new-address-alert" class="error-address error-msg-login" role="alert" style="display: none;"><i class="fa fa-bell" aria-hidden="true"></i> Terdapat kesalahan: <br/> <br/></span>
                <input id="btn-new-address" type="submit" class="add-alamat" value="Simpan Alamat">
            </div>
          </div>
          <!--END CUSTOMER NOT HAVE ADDRESS-->

          <!--CUSTOMER HAVE ADDRESS-->
          <ul id="address-container" style="display:none;">
            <li class="clearfix">
              <h2>Alamat Pengiriman</h2>
              <div class="radio-address">
                <div class="address">
                  <label for="alamat2">
                    <div class="alamat-detail">
                      <p class="ch-head">{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</p>
                      <p id="txt-primary-shipping-street">Loading ...</p>
                      <p id="txt-primary-shipping-city">Loading ...</p>
                      <p id="txt-primary-shipping-phone">Loading ...</p>
                      <a id="btn-list-shipping-address" style="margin:10px 0 0 0;"><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Alamat</a>
                    </div>
                  </label>
                </div>
              </div>
            </li>
            <li class="clearfix">
              <h2>Alamat Penagihan</h2>
              <div class="radio-address">
                <div class="address">
                  <label for="alamat2">
                    <div class="alamat-detail">
                      <p class="ch-head">{{ Auth::user()->customer_fname }} {{ Auth::user()->customer_lname }}</p>
                      <p id="txt-primary-billing-street">Loading ...</p>
                      <p id="txt-primary-billing-city">Loading ...</p>
                      <p id="txt-primary-billing-phone">Loading ...</p>
                      <a id="btn-list-billing-address" style="margin:10px 0 0 0;"><i class="fa fa-pencil" aria-hidden="true"></i> Ubah Alamat</a>
                    </div>
                  </label>
                </div>
              </div>
            </li>
          </ul>
          <!--END CUSTOMER HAVE ADDRESS-->

          <div class="list-alamat" id="list-alamat" style="display: none;">
            <h1 class="list-alamat-title">Daftar Alamat<i class="right fa fa-times close-list-alamat" aria-hidden="true"></i><i class="right fa fa-times back-list-alamat" aria-hidden="true"></i></h1>
            <div class="ubah-list-alamat">
              <div class="error-wrapper">
                <span id="address-success" class="success-msg" style="display: none;">
                  <i class="fa fa-bell" aria-hidden="true"></i>
                  <i class="fa fa-times" aria-hidden="true"></i>
                  <span id="alert-description"></span>
                </span>
              </div>
              <ul id="list-address-container" style="display: block;">
                <!--GENERATED LIST ADDRESS HERE-->
              </ul>
              <div class="add-address" id="tambah-alamat-baru" style="display: none;">
                <div class="form-tambah-alamat">
                  <p>
                    <label>Alamat</label>
                    <span>
                      <input id="add-address-type" type="hidden" />
                      <textarea id="add-address-street"></textarea>
                      <select id="add-address-province" name="Propinsi" class="tambah-alamat-propinsi left">
                        <option selected="selected" disabled="disabled" value="">Propinsi</option>
                        @foreach($list_province as $province)
                          <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
                        @endforeach
                      </select>
                      <select id="add-address-city" name="kota" class="tambah-alamat-kota left">
                        <option selected="selected" disabled="disabled" value="">Kota</option>
                      </select>
                      <input id="add-address-postcode" type="text" placeholder="Kode Pos (5 Angka)" class="tambah-alamat-postal left">
                    </span>
                  </p>
                  <p>
                    <label>Telp</label>
                    <input id="add-address-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" type="text">
                  </p>
                  <span id="add-address-alert" class="error-address error-msg-login" role="alert"><i class="fa fa-bell" aria-hidden="true"></i> </span>
                  <input id="btn-add-address" type="submit" class="add-alamat" value="Simpan Alamat">
                  <input type="button" class="cancel-add-address" value="Batal">
                </div>
              </div>
              <div class="tambah-alamat-baru">Tambah Alamat Baru</div>
            </div>
            <div class="edit-alamat-detail">
              <span>
                <p>Alamat</p>
                <div class="edit-alamat-input clearfix">
                  <input id="edit-address-id" type="hidden" />
                  <input id="edit-address-type" type="hidden" />
                  <textarea id="edit-address-street"></textarea><br>
                  <select id="edit-address-province" class="tambah-alamat-propinsi left" name="Propinsi">
                    <option value="" disabled="disabled" selected="selected">Provinsi</option>
                    @foreach($list_province as $province)
                      <option value="{{ $province->shipping_area }}">{{ $province->shipping_area }}</option>
                    @endforeach
                  </select>
                  <select id="edit-address-city" class="tambah-alamat-kota left" name="kota">
                    <option value="" disabled="disabled" selected="selected">Kota</option>
                  </select>
                  <input id="edit-address-postcode" type="text" class="tambah-alamat-postal left" placeholder="Kode Pos (5 Angka)">
                </div>
              </span>
              <span>
                <p>Telpon</p>
                <div class="edit-alamat-input clearfix">
                  <input id="edit-address-phone" placeholder="02xxxxxxxx/08xxxxxxxxxx" type="text">
                </div>
              </span>
              <span id="edit-address-alert" class="error-address error-msg-login" role="alert"><i class="fa fa-bell" aria-hidden="true"></i> </span>
              <input id="btn-edit-address" type="submit" value="Simpan Alamat">
            </div>
          </div>
        </div>

        <div class="payment-method-wrapper clearfix">
          <div class="payment-left left">
            <span>Metode<br>Pembayaran</span>
          </div>
          <div class="payment-right right">
            <ul>
              @if(collect($list_payment_method_virtual_account)->count() > 0)
                <h1>Transfer via Virtual Account (Disetujui Otomatis)</h1>
              @endif 
                @foreach($list_payment_method_virtual_account as $virtual_account)
                  @if($virtual_account->master_payment_id == 28)
                    <!--BCA Virtual Account-->
                    <li>
                      <label>
                        <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $virtual_account->master_payment_id }}" id="RadioGroup2_0"  >
                        <span><img style="height:20px" src="{{ asset('hijabenka/desktop/img/bca-va.png') }}">{{ $virtual_account->master_payment_name }} <i class="alert">New</i></span>
                        <div class="payment-info">
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
                    <!--Permata Virtual Account-->
                    <li>
                      <label>
                        <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $virtual_account->master_payment_id }}" id="RadioGroup2_0"  >
                        <span><img style="height:20px" src="{{ asset('hijabenka/desktop/img/permata_va.jpg') }}">{{ $virtual_account->master_payment_name }} <i class="alert">New</i></span>
                        <div class="payment-info">
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
              
              @if(collect($list_payment_method_bank_transfer)->count() > 0)
                <h1>Transfer Reguler (Disetujui Manual)</h1>
              @endif 
              @foreach($list_payment_method_bank_transfer as $bank_transfer)
                @if($bank_transfer->master_payment_id == 1 || $bank_transfer->master_payment_id == 2 || $bank_transfer->master_payment_id == 29 || $bank_transfer->master_payment_id == 30)
                  <!--Transfer Method-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $bank_transfer->master_payment_id }}" id="RadioGroup2_0"	>
                      <span><img src="{{ asset('hijabenka/desktop/img/'.$bank_transfer->master_payment_image) }}">{{ $bank_transfer->master_payment_name }}</span>
                      <div class="payment-info">
                        <i class="fa fa-bell"></i>
                        <strong>PANDUAN PEMBAYARAN:</strong><br>
                        <ul>
                          <li>Mohon membayar dalam 2x24 jam, jika tidak maka transaksi dibatalkan.</li>
                          <li>Mohon transfer tanpa pembulatan, sesuai angka yang tertera di tagihan.</li>
                          <li>Mohon cantumkan Kode Pembelian pada keterangan berita transfer.</li>
                          <li>Pembayaran untuk Kode Pembelian berbeda harus dilakukan secara terpisah.</li>
                          <li>Mohon lakukan konfirmasi setelah melakukan pembayaran.</li>
                        </ul>
                        <p class="mb10">
                          {!! $bank_transfer->master_payment_description !!}
                        </p>
                      </div>
                    </label>
                  </li>
                  @endif
                @endforeach
              
              @if(collect($list_payment_method_kartu_kredit)->count() > 0)
                <h1>Kartu Kredit/Debit</h1>
              @endif 
              @foreach($list_payment_method_kartu_kredit as $kartu_kredit)
                @if($kartu_kredit->master_payment_id == 20)
                  <!--Mandiri Debit-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $kartu_kredit->master_payment_id }}" id="RadioGroup2_2">
                      <span><img src="{{ asset('hijabenka/desktop/img/bank-mandiri-logo.jpg') }}">{{ $kartu_kredit->master_payment_name }}</span>
                      <div class="payment-info">
                        <i class="fa fa-bell"></i>
                        <strong>PANDUAN PEMBAYARAN:</strong><br>
                        <ul>
                          <li>Pastikan nomor handphone Anda sudah terdaftar di sistem Bank Mandiri, agar Anda dapat melakukan pembayaran menggunakan debit Bank Mandiri berlogo Visa</li>
                          <li>Minimum transaksi adalah Rp 5.000, dan maksimum transaksi adalah Rp 20.000.000</li>
                        </ul>
                        <label>Nomor Kartu Debit Mandiri</label>
                        <input id="bin-number-mandiri" class="input-cc" type="text" maxlength="16" required>
                        <label>Nama Lengkap sesuai kartu</label>
                        <input id="bin-name-mandiri" class="input-cc" type="text" required>
                        <div class="card-number clearfix">
                          <div class="card-left left">
                            <label>Masa Berlaku</label>
                            <select id="bin-month-mandiri" name="month_exp" class="input-cc">
                              <option value="" disabled="disabled" selected="selected">Bulan<!-- Month --></option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                              <option value="6">6</option>
                              <option value="7">7</option>
                              <option value="8">8</option>
                              <option value="9">9</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                            </select>
                            /
                            <select id="bin-year-mandiri" name="year_exp" class="input-cc">
                              <option value="" disabled="disabled" selected="selected">Tahun <!-- Year --></option>
                              <option value="2016">2016</option>
                              <option value="2017">2017</option>
                              <option value="2018">2018</option>
                              <option value="2019">2019</option>
                              <option value="2020">2020</option>
                              <option value="2021">2021</option>
                              <option value="2022">2022</option>
                              <option value="2023">2023</option>
                              <option value="2024">2024</option>
                              <option value="2025">2025</option>
                              <option value="2026">2026</option>
                              <option value="2027">2027</option>
                              <option value="2028">2028</option>
                            </select>
                          </div>
                          <div class="cvv clearfix">
                            <label>CVV</label>
                            <input id="bin-cvv-mandiri" class="input-cc" type="password" required="required" maxlength="3" name="cvv" id="cvv_md" value="">
                          </div>
                        </div>
                        <div class="clear"></div>
                        <!--<div class="logo-mandiri-debit"><img src="{{ asset('hijabenka/desktop/img/bank-mandiri-logo.jpg') }}"></div>-->
                      </div>
                    </label>
                  </li>                
                @elseif($kartu_kredit->master_payment_id == 5)
                  <!--Visa/MasterCard-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $kartu_kredit->master_payment_id }}" id="RadioGroup2_0"  >
                      <span><img src="{{ asset('hijabenka/desktop/img/'.$kartu_kredit->master_payment_image) }}">{{ $kartu_kredit->master_payment_name }}</span>
                      <div class="payment-info">                        
                          <i class="fa fa-bell"></i>
                          <strong>PANDUAN PEMBAYARAN:</strong><br>
                        <label>Nomor Kartu Kredit</label>
                        <input id="bin-number" class="input-cc" type="text" maxlength="16" required>
                        <label>Nama Lengkap sesuai kartu</label>
                        <input id="bin-name" class="input-cc" type="text" required>
                        <div class="card-number clearfix">
                          <div class="card-left left">
                            <label>Masa Berlaku</label>
                            <select id="bin-month" name="month_exp" class="input-cc">
                              <option value="" disabled="disabled" selected="selected">Bulan<!-- Month --></option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                              <option value="6">6</option>
                              <option value="7">7</option>
                              <option value="8">8</option>
                              <option value="9">9</option>
                              <option value="10">10</option>
                              <option value="11">11</option>
                              <option value="12">12</option>
                            </select>
                            /
                            <select id="bin-year" name="year_exp" class="input-cc">
                              <option value="" disabled="disabled" selected="selected">Tahun <!-- Year --></option>
                              <option value="2016">2016</option>
                              <option value="2017">2017</option>
                              <option value="2018">2018</option>
                              <option value="2019">2019</option>
                              <option value="2020">2020</option>
                              <option value="2021">2021</option>
                              <option value="2022">2022</option>
                              <option value="2023">2023</option>
                              <option value="2024">2024</option>
                              <option value="2025">2025</option>
                              <option value="2026">2026</option>
                              <option value="2027">2027</option>
                              <option value="2028">2028</option>
                            </select>
                          </div>
                          <div class="cvv clearfix">
                            <label>CVV</label>
                            <input id="bin-cvv" class="input-cc" type="password" required="required" maxlength="3" name="cvv" value="">
                          </div>
                        </div>
                        <div class="clear"></div>
                      </div>
                    </label>
                  </li>
                @endif
              @endforeach
              
              @if(collect($list_payment_method_internet_banking)->count() > 0)
                <h1>Internet Banking</h1>
              @endif 
              @foreach($list_payment_method_internet_banking as $internet_banking)
                @if($internet_banking->master_payment_id == 3)
                  <!--Klik BCA-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $internet_banking->master_payment_id }}" id="RadioGroup2_0"  >
                      <span><img src="{{ asset('hijabenka/desktop/img/'.$internet_banking->master_payment_image) }}">{{ $internet_banking->master_payment_name }}</span>
                      <div class="payment-info">
                        <i class="fa fa-bell"></i>
                        <strong>PANDUAN PEMBAYARAN:</strong><br>
                        <ul>
                          <li>Mohon Login ke KlikBCA dengan UserID yang sama</li>
                          <li>Pembayaran maksimal 2 jam setelah pemesanan, minimum transaksi adalah Rp 10.000</li>
                          <li>Pilih "Pembayaran E-commerce" > Kategori "Baju / Aksesoris" > Nama Perusahaan "BERRYBENKA" > pilih Lanjut</li>
                          <li>Pilih transaksi yang ingin dibayarkan > pilih Lanjut</li>
                          <li>Pembayaran akan dikonfirmasi dan pesanan akan diproses secepatnya</li>
                        </ul>
                        <p class="mb10">  Masukkan User ID KlikBCA Anda.
                          <br>
                          <input type="text" id="txt-klikbca-user-id" name="klikbcauserid" maxlength="12" placeholder="Enter your KlikBCA User ID" class="mv10">
                        </p>
                      </div>
                    </label>
                  </li>
                @elseif($internet_banking->master_payment_id == 4)
                  <!--KlikPay-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $internet_banking->master_payment_id }}" id="RadioGroup2_0"  >
                      <span><img style="height:20px" src="{{ asset('hijabenka/desktop/img/'.$internet_banking->master_payment_image) }}">{{ $internet_banking->master_payment_name }}</span>
                      <div class="payment-info">
                        <i class="fa fa-bell"></i>
                        <strong>PANDUAN PEMBAYARAN:</strong><br>
                        <ul>
                          <li>Pastikan alamat domain adalah https://klikpay.klikbca.com</li>
                          <li>Pembayaran maksimal 2 jam setelah pemesanan, minimum transaksi adalah Rp 10.000</li>
                          <li>Masukkan email dan password > pastikan informasi transaksi sudah benar > pilih jenis pembayaran KlikBCA > pilih Kirim</li>
                          <li>Pilih Kirim OTP > masukkan kode OTP yang dikirimkan ke handphone Anda > pilih Bayar</li>
                          <li>Pembayaran akan dikonfirmasi dan pesanan akan diproses secepatnya</li>
                        </ul>
                        <!-- <p class="mb10">  Bayar menggunakan BCA KlikPay yang disediakan oleh Bank BCA. </p> -->
                      </div>
                    </label>
                  </li>                
                @endif
              @endforeach
              
              @if(collect($list_payment_method_others)->where('master_payment_id', 19)->count() > 0)
                <h1>Bayar di Tempat</h1>
              @endif 
              @foreach($list_payment_method_others as $cod)
                @if($cod->master_payment_id == 19)
                  <!--COD-->
                  <li id="cod-container" style="display:none;">
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $cod->master_payment_id }}" id="RadioGroup2_0"	>
                      <span><i class="fa fa-truck cod-icon"></i> {{ $cod->master_payment_name }} <i class="alert">New</i>
                      </span>
                      <div class="payment-info">                        
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
                  <li id="popup-store-{{ $cod->master_payment_id }}" class='popup-store-container' style="display: none;">
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $cod->master_payment_id }}" id="RadioGroup2_0"	>
                      <span><i class="fa fa-truck cod-icon"></i> {{ $cod->master_payment_name }} <i class="alert">New</i>
                      </span>
                      <div class="payment-info">                        
                          <i class="fa fa-bell"></i>
                          <strong>PANDUAN PEMBAYARAN:</strong><br>                        
                        <div>  {{ $cod->master_payment_description }}  </div>
                      </div>
                    </label>
                  </li>
                @endif
              @endforeach
              
              @if(collect($list_payment_method_others)->count() > 0)
                <h1>Lainnya</h1>
              @endif 
              @foreach($list_payment_method_others as $others)
                @if($others->master_payment_id == 99)
                    <!--  KREDIVO -->
                    <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" id="RadioGroup2_0" >
                      <span><img style="height:20px" src="{{ asset('berrybenka/desktop/img/kredivo.png') }}">{{ $others->master_payment_name }} <i class="alert">New</i></span>
                      <div class="payment-info">
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
                            <div id="select-kredivo" style="margin-top:10px;"></div>     
                            <div id="kredivo-note"></div>
                      </div>
                    </label>
                  </li>
                  @elseif($others->master_payment_id == 135)
                  <!--T-Cash-->
                  <li>
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" id="RadioGroup2_0"  >
                      <span><img style="height:20px" src="{{ asset('berrybenka/desktop/img/t-cash.png') }}">{{ $others->master_payment_name }} <i class="alert">New</i></span>
                      <div class="payment-info">
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
                    <label>
                      <input type="radio" class="rad-payment-method" name="rad-payment-method" value="{{ $others->master_payment_id }}" id="RadioGroup2_0"  >
                      <span><img src="{{ asset('hijabenka/desktop/img/indomaret.jpg') }}">{{ $others->master_payment_name }} </span>
                      <div class="payment-info">
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
                  @endif
                @endforeach
            </ul>
          </div>
        </div>

        <div id="div-shipping-method-container" style="display: none;" class="cart-shiping clearfix">
          <div class="cart-ship-left left">
            <span>Metode<br>Pengiriman</span>
          </div>
          <div id="shipping-method-container" class="cart-ship-right right">
            <!--Shipping Method generated here-->
          </div>
          <!--div class="cart-ship-right right">
            <a href="{{ url('/') }}/home/shipping_handling" style="background:#ececec;display:block;box-sizing:border-box;padding:10px;letter-spacing:1px;line-height:1.3;margin:10px 0;border-radius:2px;border:1px solid #ccc;">Informasi pengiriman pesanan selama Lebaran 2017 dapat mengacu ke ketentuan berikut: <span style="text-decoration:underline;">shipping & handling</span></a>
          </div-->
        </div>

      </div>
      <div class="cart-list-wrapper-right clearfix right scroll" id="sticky-cart">
        <div class="sticky-wrapper">
          <div class="cart-list-small">
            <ul>
              @foreach($fetch_cart as $cart)
                <li class="clearfix">
                  <div class="cart-list-small-img left"><img src="{{IMAGE_PRODUCTS_CATALOG_UPLOAD_PATH.$cart['image']}}"></div>
                  <div class="cart-list-small-detail right">
                    <h1>{{ $cart['name'] }}</h1>
                    <h2>{{ $cart['brand_name'] }}</h2>
                    <p>Warna<span>: {{ $cart['color_name'] }}</span></p>
                    <p>Ukuran<span>: {{ $cart['size'] }}</span></p>
                    <p>JUMLAH<span>: {{ $cart['qty'] }} @ {{ number_format($cart['price']) }}</span></p>
                    <p>IDR {{ number_format($cart['subtotal']) }}</p>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>
          <div class="cart-detail">
            <div class="table-purchase">
              <span class="purchase-label">TOTAL PEMBELIAN</span>
              <span id="txt-subtotal" class="purchase-value">Loading ...</span>
            </div>
            <div class="table-purchase">
              <span class="purchase-label">KODE PEMBAYARAN</span>
              <span id="txt-paycode" class="purchase-value">(+) IDR 0</span>
            </div>
            <div id="freegift-auto-container">
              <!--Freegift Information Generated Here-->
            </div>
            <div id="voucher-container">
              <!--Voucher Information Generated Here-->
            </div>
            <div id="freegift-container">
              <!--Freegift Information Generated Here-->
            </div>
            <div id="benka-point-container">
              <!--Benka Point Information Generated Here-->
            </div>
            <span class="purchase-line"></span>
            <div class="table-purchase">
              <span class="purchase-label">PENGIRIMAN</span>
              <span id="txt-shipping-cost" class="purchase-value">Loading ...</span>
            </div>
            <span class="purchase-line"></span>
            <div class="table-purchase">
              <span class="purchase-label"><strong>GRAND TOTAL</strong></span>
              <span id="txt-grandtotal" class="purchase-value">Loading ...</span>
            </div>
            <span class="purchase-line line-freegift-notif" style="display: none;"></span>
            <div id="freegift-notif" class="freegift-notif" style="display: none;">
              <!--Freegift Notif Generated Here-->
            </div>
            <span class="purchase-line"></span>
            <div class="kupon-wrapper">
              <label class="input-voucher">Masukan Kupon Anda</label>
              <div class="input-group input-voucher">
                <input type="text" class="form-control" id="voucher_code" name="voucher_code">
                <a id="btn-apply-voucher" class="input-group-addon" href="#">GUNAKAN</a>
              </div>
              @if($customer_credit > 0)
                <div id="benka-point-form" class="benka-point-form">
                  <label>Benka Point</label>
                  <p>Masukan Jumlah Benka Poin yang ingin Anda gunakan sebagai diskon pada saat checkout.</p>
                  <p>Anda Memiliki <span>{{number_format($customer_credit)}} Benka Poin</span></p>
                  <div class="input-group">
                    <input type="text" class="form-control" id="benka_point" name="benka_point">
                    <a id="btn-benka-point" class="input-group-addon" href="#">GUNAKAN</a>
<!--                    <div class="info-benka-point">
                      <p><i class="fa fa-info-circle" aria-hidden="true"></i> Informasi Lebih Lanjut</p>
                      <ul>
                        <li>Setiap kali Anda berbelanja senilai IDR 50.000, Anda akan mendapatkan 1 (satu) Benka Poin, Berlaku kelipatannya</li>
                        <li>1 Benka Poin setara dengan IDR 1</li>
                      </ul>
                    </div>-->
                  </div>
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
              <input id="token-id" name="token-id" type="hidden" />
              <input id="cc-holder" name="cc-holder" type="hidden" />
              <input id="final-grand-total" name="final-grand-total" type="hidden" />
              <!-- kredivo payment type-->
              <input id="kredivo-payment-type" name="kredivo-payment-type" type="hidden" value="30_days"/>
              <!-- end kredivo payment type-->
              
              <div class="submit-checkout"><input id="btn-submit-order" type="submit" value="PESAN"></div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('js')
<script src="{{ asset('js/veritrans.js') }}"></script>
<script src="{{ asset('js/ajaxq.js') }}"></script>
<script src="{{ asset('js/desktop/submit-order.js?t=').date('YmdHis') }}"></script>
@endsection

@section('marketing-tag')
<script type="text/javascript">
<?php
$user = \Auth::user();

$product_json = $marketing_data;
?>
var detail_product336CC993E54E = {
    customer_id : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
    customer_fname : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
    customer_lname : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
    customer_email : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif
  }    
var marketing336CC993E54E = {!! $marketing_data !!};   
</script>

@if(getMarketingEnv() == true)
    @include('marketing-tag.hijabenka.desktop.submit-order-page', ['product_ids' => $product_json])
@endif

@endsection
