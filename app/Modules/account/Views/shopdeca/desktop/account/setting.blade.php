@extends('layouts.shopdeca.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('shopdeca/desktop/css/user.css?t=').date('YmdHis') }}">
@endsection

@section('content')
<div id="fb-root"></div>
<div class="user-wrapper clearfix">
    <div class="wrapper">
    	<div class="user-wrap">
        	{!! get_view('account', 'account.leftmenu', array('page'=>'setting','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-cog" aria-hidden="true"></i>Pengaturan
                        <div class="right last-login"><b>Terakhir Login : </b>{{ indonesian_date(strtotime($user->last_login_date),'l, j F Y H:i:s') }}</div>
                    </h1>
                    <div class="order-content">
                    	<ul class="tabs">
                        	<li><a href="#u" class="active"><i class="fa fa-map-marker" aria-hidden="true"></i>Ubah Alamat</a></li>
                            <li><a href="{{ URL::to('/user/change_password') }}"><i class="fa fa-lock" aria-hidden="true"></i>Ubah Password</a></li>
                            <li><a href="/user/edit_personal_detail"><i class="fa fa-user" aria-hidden="true"></i>Ubah Data</a></li>
                        </ul>
                    </div>

                    <div class="tab-content">
                        @if(!empty(Session::get('err_msg')))
                          <span class="error-msg-login" style="margin: 10px auto;">
                            <i aria-hidden="true" class="fa fa-bell"></i>
                            <i aria-hidden="true" class="fa fa-times"></i>
                            <span>{!! Session::get('err_msg') !!}</span>
                          </span>
                        @endif
                      
                        <?php //EDIT ALAMAT PENGIRIMAN ?>
                        @foreach ($customer_address as $edit_address)
                        <?php if($edit_address->address_type == 1) { ?>
                                	<div class="ubah-alamat-edit" id="ubah-alamat-edit-{{ $edit_address->address_id }}">
                                    	 <h1 class="list-alamat-title">Mengubah Alamat<i aria-hidden="true" class="right fa fa-times close-edit-alamat" style="display: inline;"></i></h1>
                                    	<div class="edit-alamat-detail">
                                            <form method="post" id="account" action="/user/edit_address/{{ $edit_address->address_id }}">
                                                {!! csrf_field() !!}
                                                <span>
                                                    <p>Alamat</p>
                                                    <div class="edit-alamat-input clearfix">
                                                        <input type="hidden" name="address_type" value="{{ $address_type }}" />
                                                        <textarea name="address" required="required">{{ $edit_address->address_street }}</textarea><br>
                                                        <select name="shipping_area" class="tambah-alamat-propinsi left" onChange="requestCityNew(this.value,'ubah-alamat-edit-{{ $edit_address->address_id }}');" required>
                                                            <option selected="selected" disabled="disabled" value="">Propinsi</option>
                                                            @foreach ($options_area as $area)
                                                                <option value="{{ $area }}" <?php echo $edit_address->address_province==$area ? 'selected' : ''?>>{{ $area }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="load_ship_city" class="tambah-alamat-kota left" style="width: 30%;display: none">
                                                            <i class="fa fa-refresh fa-spin"></i> Loading
                                                        </span>
                                                        <select name="shipping_name" id="shipping_name" class="tambah-alamat-kota left" required>
                                                            <option selected="selected" disabled="disabled" value="">Kota</option>
                                                            @foreach ($options_name as $name)
                                                                <option value="{{ $name }}" <?php echo $edit_address->address_city==$name ? 'selected' : ''?>>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="text" name="postcode" placeholder="Kode Pos" class="tambah-alamat-postal left" value="{{ $edit_address->address_postcode }}" required="required">
                                                    </div>
                                                </span>
                                                <span>
                                                    <p>Telpon</p>
                                                    <div class="edit-alamat-input clearfix">
                                                        <input type="text" name="phone" onkeypress="return validatePhoneNum(event);" value="{{ $edit_address->address_phone }}" required="required">
                                                    </div>
                                                </span>
                                                <input type="submit" value="Simpan Alamat">
                                            </form>
                                        </div>
                                    </div>
                        <?php } ?>
                        @endforeach

                        <?php //EDIT ALAMAT PEMBAYARAN ?>
                        @foreach ($customer_address as $edit_address)
                        <?php if($edit_address->address_type == 2) { ?>
                                    <div class="ubah-alamat-edit" id="ubah-alamat-edit-{{ $edit_address->address_id }}">
                                         <h1 class="list-alamat-title">Mengubah Alamat<i aria-hidden="true" class="right fa fa-times close-edit-alamat" style="display: inline;"></i></h1>
                                        <div class="edit-alamat-detail">
                                            <form method="post" id="account" action="/user/edit_address/{{ $edit_address->address_id }}">
                                                {!! csrf_field() !!}
                                                <span>
                                                    <p>Alamat</p>
                                                    <div class="edit-alamat-input clearfix">
                                                        <input type="hidden" name="address_type" value="{{ $address_type }}" />
                                                        <textarea name="address" required="required">{{ $edit_address->address_street }}</textarea><br>
                                                        <select name="shipping_area" class="tambah-alamat-propinsi left" onChange="requestCityNew(this.value,'ubah-alamat-edit-{{ $edit_address->address_id }}');" required>
                                                            <option selected="selected" disabled="disabled" value="">Propinsi</option>
                                                            @foreach ($options_area as $area)
                                                                <option value="{{ $area }}" <?php echo $edit_address->address_province==$area ? 'selected' : ''?>>{{ $area }}</option>
                                                            @endforeach
                                                        </select>
                                                        <span id="load_ship_city" class="tambah-alamat-kota left" style="width: 30%;display: none">
                                                            <i class="fa fa-refresh fa-spin"></i> Loading
                                                        </span>
                                                        <select name="shipping_name" id="shipping_name" class="tambah-alamat-kota left" required>
                                                            <option selected="selected" disabled="disabled" value="">Kota</option>
                                                            @foreach ($options_name as $name)
                                                                <option value="{{ $name }}" <?php echo $edit_address->address_city==$name ? 'selected' : ''?>>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <input type="text" name="postcode" placeholder="Kode Pos" class="tambah-alamat-postal left" value="{{ $edit_address->address_postcode }}" required="required">
                                                    </div>
                                                </span>
                                                <span>
                                                    <p>Telpon</p>
                                                    <div class="edit-alamat-input clearfix">
                                                        <input type="text" name="phone" onkeypress="return validatePhoneNum(event);" value="{{ $edit_address->address_phone }}" required="required">
                                                    </div>
                                                </span>
                                                <input type="submit" value="Simpan Alamat">
                                            </form>
                                        </div>
                                    </div>
                        <?php } ?>
                        @endforeach

                        <?php //TAMBAH ALAMAT PENGIRIMAN ?>
                        <div class="ubah-alamat-edit" id="tambah-alamat-setting-1">
                        	<h1 class="list-alamat-title">Menambah Alamat<i aria-hidden="true" class="right fa fa-times close-edit-alamat" style="display: inline;"></i></h1>
                        	<div class="edit-alamat-detail">
                                <form method="post" id="account" action="/user/add_address/shipping">
                                    {!! csrf_field() !!}
                                    <span>
                                        <p>Alamat</p>
                                        <div class="edit-alamat-input clearfix">
                                            <textarea name="address" required="required"></textarea><br>
                                            <select name="shipping_area" class="tambah-alamat-propinsi left" onChange="requestCityNew(this.value,'tambah-alamat-setting-1');" required>
                                                <option selected="selected" disabled="disabled" value="">Propinsi</option>
                                                @foreach ($options_area as $area)
                                                    <option value="{{ $area }}">{{ $area }}</option>
                                                @endforeach
                                            </select>
                                            <span id="load_ship_city" class="tambah-alamat-kota left" style="width: 30%;display: none">
                                                <i class="fa fa-refresh fa-spin"></i> Loading...
                                            </span>
                                            <select name="shipping_name" id="shipping_name" class="tambah-alamat-kota left">
                                                <option selected="selected" disabled="disabled" value="">Kota</option>
                                                @foreach ($options_name as $name)
                                                    <option value="{{ $name }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="postcode" placeholder="Kode Pos" class="tambah-alamat-postal left" required="required">
                                        </div>
                                    </span>
                                    <span>
                                        <p>Telpon</p>
                                        <div class="edit-alamat-input clearfix">
                                            <input type="text" name="phone" placeholder="Silahkan masukan nomor telepon" onkeypress="return validatePhoneNum(event);" required="required">
                                        </div>
                                    </span>
                                    <input type="submit" value="Simpan Alamat">
                                    <input type="hidden" name="shipping_type" value="1">
                                </form>
                            </div>
                        </div>

                        <?php //TAMBAH ALAMAT PEMBAYARAN ?>
                        <div class="ubah-alamat-edit" id="tambah-alamat-setting-2">
                            <h1 class="list-alamat-title">Menambah Alamat<i aria-hidden="true" class="right fa fa-times close-edit-alamat" style="display: inline;"></i></h1>
                            <div class="edit-alamat-detail">
                                <form method="post" id="account" action="/user/add_address/billing">
                                    {!! csrf_field() !!}
                                    <span>
                                        <p>Alamat</p>
                                        <div class="edit-alamat-input clearfix">
                                            <textarea name="address" required="required"></textarea><br>
                                            <select name="shipping_area" class="tambah-alamat-propinsi left" onChange="requestCityNew(this.value,'tambah-alamat-setting-2');" required>
                                                <option selected="selected" disabled="disabled" value="">Propinsi</option>
                                                @foreach ($options_area as $area)
                                                    <option value="{{ $area }}">{{ $area }}</option>
                                                @endforeach
                                            </select>
                                            <span id="load_ship_city" class="tambah-alamat-kota left" style="width: 30%;display: none">
                                                <i class="fa fa-refresh fa-spin"></i> Loading...
                                            </span>
                                            <select name="shipping_name" id="shipping_name" class="tambah-alamat-kota left">
                                                <option selected="selected" disabled="disabled" value="">Kota</option>
                                                @foreach ($options_name as $name)
                                                    <option value="{{ $name }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="postcode" placeholder="Kode Pos" class="tambah-alamat-postal left" required="required">
                                        </div>
                                    </span>
                                    <span>
                                        <p>Telpon</p>
                                        <div class="edit-alamat-input clearfix">
                                            <input type="text" name="phone" placeholder="Silahkan masukan nomor telepon" onkeypress="return validatePhoneNum(event);" required="required">
                                        </div>
                                    </span>
                                    <input type="submit" value="Simpan Alamat">
                                    <input type="hidden" name="shipping_type" value="1">
                                </form>
                            </div>
                        </div>

                    	<div id="ubah-alamat">

                            <?php //LIST ALAMAT PENGIRIMAN ?>
                            <div class="ubah-alamat">
                                <h2>Ubah Alamat <select class="tambah-alamat-propinsi" style="background-color:white;" onchange="ubah_alamat(this.options[this.selectedIndex].value);"><option value="1" <?php if($address_type == 1) echo 'selected'; ?>>Alamat Pengiriman</option><option value="2" <?php if($address_type == 2) echo 'selected'; ?>>Alamat Pembayaran</option></select> <a href="#tambah-alamat-setting-<?php echo $address_type; ?>" class="fancybox"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Alamat</a></h2>
                                <div class="address-setting">

                                    @if(!empty(Session::get('success')))
                                        <div style="margin-left:17px; margin-right:17px; margin-bottom:-17px">
                                            {!! show_message(Session::get('success')) !!}
                                        </div>
                                    @endif
                                    
                                    <ul>
                                        @foreach ($customer_address as $address)
                                            
                                            <li>
                                                <p>{{ $address->address_street }}</p>
                                                <p>{{ $address->address_city }} - {{ $address->address_province }}</p>
                                                <p>Indonesia - {{ $address->address_postcode }}</p>
                                                <p>Nomor Handphone: {{ $address->address_phone }}</p>
                                                <?php if($address->is_primary != 1){ ?>
                                                    <a href="/user/set_primary_address/{{ $address_type }}/{{ $address->address_id }}"><i class="fa fa-check-square" aria-hidden="true"></i> Jadikan Alamat Utama</a>
                                                <?php } ?>
                                                <a href="#ubah-alamat-edit-{{ $address->address_id }}" class="fancybox"><i aria-hidden="true" class="fa fa-pencil"></i> Ubah Alamat</a>
                                                <a href="/user/delete_address/{{ $address->address_id }}?address_type={{ $address_type }}" class="erase-address-setting"><i class="fa fa-times" aria-hidden="true"></i> Hapus Alamat</a>
                                            </li>
                                            
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- <?php //LIST ALAMAT PEMBAYARAN ?>
                            <div class="ubah-alamat mt40">
                                <h2>Alamat Pembayaran <a href="#tambah-alamat-setting-2" class="fancybox"><i class="fa fa-plus" aria-hidden="true"></i> Tambah Alamat</a></h2>
                                <div class="address-setting">
                                	<ul>
                                    	@foreach ($customer_address as $pembayaran)
                                        <?php if($pembayaran->address_type == 2) { ?>
                                                <li>
                                                    <!-- <h3>{{ $user->customer_fname }} {{ $user->customer_lname }}</h3>
                                                    <p>{{ $pembayaran->address_street }}</p>
                                                    <p>{{ $pembayaran->address_city }} - {{ $pembayaran->address_province }}</p>
                                                    <p>Indonesia - {{ $pembayaran->address_postcode }}</p>
                                                    <p>Nomor Handphone: {{ $pembayaran->address_phone }}</p>
                                                    <?php if($pembayaran->is_primary != 1){ ?>
                                                        <a href="/user/set_primary_address/2/{{ $pembayaran->address_id }}"><i class="fa fa-check-square" aria-hidden="true"></i> Jadikan Alamat Utama</a>
                                                    <?php } ?>
                                                    <a href="#ubah-alamat-edit-{{ $pembayaran->address_id }}" class="fancybox"><i aria-hidden="true" class="fa fa-pencil"></i> Ubah Alamat</a>
                                                    <a href="/user/delete_address/{{ $address->address_id }}" class="erase-address-setting"><i class="fa fa-times" aria-hidden="true"></i> Hapus Alamat</a>
                                                </li>
                                        <?php } ?>
                                        @endforeach
                                    </ul>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    @if ($customer_address)
                    <div class="pagination right">
                        {!! $customer_address->appends(['address_type' => $address_type])->links() !!}                             
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
function requestCityNew(elem,div) {
    if(typeof(div)=='undefined'){
        var divs="tambah-alamat-setting";
    }else{
        var divs = div;
    }
    
    var weight = document.getElementById("weight");
    
    $.ajax({
        url : '{{ url('user/get_shipping_city_new') }}',
        type : 'post',
        data : $("#"+divs+" #account").serialize(),
        beforeSend : function () {
            $("#"+divs+" #shipping_name").hide();
            $("#"+divs+" #load_ship_city").show();
        },
        success : function (data) {
            $("#"+divs+" #load_ship_city").hide();
            $("#"+divs+" #shipping_name").show().html(data);
            
        }
    });
}

function validatePhoneNum(event) {
    var theEvent = event || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /^[0-9\b]+$/;
    
    if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }
}

function ubah_alamat(alamat) {
    var url = location.protocol + '//' + location.host + '/user/setting?address_type=' + alamat;
    window.location = url;
}

</script>

@endsection