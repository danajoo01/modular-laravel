@extends('layouts.berrybenka.desktop.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/desktop/css/user.css?t=').date('YmdHis') }}">
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
                    </h1>
                    <div class="order-content">
                    	<ul class="tabs">
                        	<li><a href="/user/setting"><i class="fa fa-map-marker" aria-hidden="true"></i>Ubah Alamat</a></li>
                            <li><a href="{{ URL::to('/user/change_password') }}"><i class="fa fa-lock" aria-hidden="true"></i>Ubah Password</a></li>
                            <li><a href="#u" class="active"><i class="fa fa-user" aria-hidden="true"></i>Ubah Data</a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div id="ubah-data" class="ubah-data">
                            <form name="ubah-data" method="POST" action="/user/save_personal_detail">
                                {!! csrf_field() !!}
                            	<ul>
                                    <li>
                                        @if(!empty(Session::get('errors')))
                                            <?php 
                                            if ($errors->has('customer_fname')) {
                                                echo $errors->first('customer_fname', ' <span class="error-msg-login">
                                                                                            <i aria-hidden="true" class="fa fa-bell"></i>
                                                                                            <i aria-hidden="true" class="fa fa-times"></i>
                                                                                            Nama depan tidak boleh kosong dan hanya boleh diisi dengan huruf.
                                                                                        </span>'); 
                                            }

                                            if ($errors->has('customer_lname')) {
                                                echo $errors->first('customer_lname', ' <span class="error-msg-login">
                                                                                            <i aria-hidden="true" class="fa fa-bell"></i>
                                                                                            <i aria-hidden="true" class="fa fa-times"></i>
                                                                                            Nama belakang tidak boleh kosong dan hanya boleh diisi dengan huruf.
                                                                                        </span>'); 
                                            }

                                            if ($errors->has('customer_phone')) {
                                                echo $errors->first('customer_phone', ' <span class="error-msg-login">
                                                                                            <i aria-hidden="true" class="fa fa-bell"></i>
                                                                                            <i aria-hidden="true" class="fa fa-times"></i>
                                                                                            No telepon tidak boleh kosong dan hanya boleh diisi dengan angka.
                                                                                        </span>'); 
                                            }

                                            if ($errors->has('customer_gender')) {
                                                echo $errors->first('customer_gender', '<span class="error-msg-login">
                                                                                            <i aria-hidden="true" class="fa fa-bell"></i>
                                                                                            <i aria-hidden="true" class="fa fa-times"></i>
                                                                                            Jenis kelamin tidak boleh kosong.
                                                                                        </span>'); 
                                            }

                                            if ($errors->has('how_did_you_know_us')) {
                                                echo $errors->first('how_did_you_know_us', '<span class="error-msg-login">
                                                                                                <i aria-hidden="true" class="fa fa-bell"></i>
                                                                                                <i aria-hidden="true" class="fa fa-times"></i>
                                                                                                Silahkan pilih Bagaimana Anda Mengetahui Kami.
                                                                                            </span>'); 
                                            }
                                            ?>
                                        @endif
                                    </li>
                                	<li>
                                    	<label>Nama Depan*</label>
                                        <input type="text" name="customer_fname" value="{{ $user->customer_fname }}">
                                    </li>
                                    <li>
                                    	<label>Nama Belakang</label>
                                        <input type="text" name="customer_lname" value="{{ $user->customer_lname }}">
                                    </li>
                                    <li>
                                    	<label>Tanggal Lahir</label>
                                        <?php if(!empty($user->customer_date_of_birth)){ ?>
                                            <?php $dob = explode("-",$user->customer_date_of_birth); ?>
                                            <select name="dd" required="required">
                                            	<option>Tanggal</option>
                                                <?php 
                                                for($i=1;$i<=31;$i++) {                     
                                                ?>
                                                <option <?php echo ($dob[2] == $i) ? 'selected="selected"' : ''; ?>><?php echo $i;?></option>
                                                <?php } ?>
                                            </select>
                                             <select name="mm" required="required">
                                            	<option>Bulan</option>
                                                <option <?php echo ($dob[1] == '1') ? 'selected="selected"' : ''; ?> value="1">Januari</option>
                                                <option <?php echo ($dob[1] == '2') ? 'selected="selected"' : ''; ?> value="2">Februari</option>
                                                <option <?php echo ($dob[1] == '3') ? 'selected="selected"' : ''; ?> value="3">Maret</option>
                                                <option <?php echo ($dob[1] == '4') ? 'selected="selected"' : ''; ?> value="4">April</option>
                                                <option <?php echo ($dob[1] == '5') ? 'selected="selected"' : ''; ?> value="5">Mei</option>
                                                <option <?php echo ($dob[1] == '6') ? 'selected="selected"' : ''; ?> value="6">Juni</option>
                                                <option <?php echo ($dob[1] == '7') ? 'selected="selected"' : ''; ?> value="7">Juli</option>
                                                <option <?php echo ($dob[1] == '8') ? 'selected="selected"' : ''; ?> value="8">Agustus</option>
                                                <option <?php echo ($dob[1] == '9') ? 'selected="selected"' : ''; ?> value="9">September</option>
                                                <option <?php echo ($dob[1] == '10') ? 'selected="selected"' : ''; ?> value="10">Oktober</option>
                                                <option <?php echo ($dob[1] == '11') ? 'selected="selected"' : ''; ?> value="11">November</option>
                                                <option <?php echo ($dob[1] == '12') ? 'selected="selected"' : ''; ?> value="12">Desember</option>
                                            </select>
                                            <select name="yy" required="required">
                                            	<option>Tahun</option>
                                                <?php 
                                                for($i=1964;$i<=(date('Y')-10);$i++) {                      
                                                ?>
                                                <option <?php echo ($dob[0] == $i) ? 'selected="selected"' : ''; ?>><?php echo $i;?></option>
                                                <?php } ?>
                                            </select>
                                        <?php }else{ ?>
                                                <select required="required" name="dd">
                                                    <option disabled="disabled" selected="selected">Tanggal</option>
                                                    <?php 
                                                    for($i=1;$i<=31;$i++) {                     
                                                    ?>
                                                        <option><?php echo $i;?></option>
                                                    <?php } ?>
                                                </select>
                                                
                                                <select name="mm">
                                                    <option disabled="disabled" selected="selected">Bulan</option>
                                                    <option value="1">Januari</option>
                                                    <option value="2">Februari</option>
                                                    <option value="3">Maret</option>
                                                    <option value="4">April</option>
                                                    <option value="5">Mei</option>
                                                    <option value="6">Juni</option>
                                                    <option value="7">Juli</option>
                                                    <option value="8">Agustus</option>
                                                    <option value="9">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>
                                                
                                                <select name="yy">
                                                    <option disabled="disabled" selected="selected">Tahun</option>
                                                    <?php 
                                                    for($i=1964;$i<=(date('Y')-10);$i++) {                      
                                                    ?>
                                                        <option><?php echo $i;?></option>
                                                    <?php } ?>
                                                </select>
                                        <?php } ?>
                                    </li>
                                    <li>
                                    	<label>Email</label>
                                        <label><?php echo isset($user->customer_email) ? $user->customer_email : ''; ?></label>
                                    </li>
                                    <li>
                                    	<label>No Telpon*</label>
                                        <input type="text" name="customer_phone" value="<?php echo isset($user->customer_phone) ? $user->customer_phone : ''; ?>">
                                    </li>
                                    <li>
                                    	<label>Jenis Kelamin*</label>
                                        <p>
                                            <label>
                                                <input type="radio" name="customer_gender" value="1" id="setting-gender_0" <?php echo ($user->customer_gender==1) ? 'checked' : ''; ?>>Wanita
                                            </label>
                                            <label>
                                                <input type="radio" name="customer_gender" value="2" id="setting-gender_1" <?php echo ($user->customer_gender==2) ? 'checked' : ''; ?>>Pria
                                            </label>
                                        </p>
                                    </li>
                                    <li>
                                    	<label>Bagaimana Anda Mengetahui Kami</label>
                                        <select name="how_did_you_know_us">
                                        	<option value="">Silahkan Pilih</option>
                                            <option value="Friends" <?php echo ($user->how_did_you_know_us=="Friends") ? 'selected' : ''; ?>>Teman</option>
                                            <option value="Magazines" <?php echo ($user->how_did_you_know_us=="Magazines") ? 'selected' : ''; ?>>Majalah</option>
                                            <option value="Blogs" <?php echo ($user->how_did_you_know_us=="Blogs") ? 'selected' : ''; ?>>Blogs</option>
                                            <option value="Facebook" <?php echo ($user->how_did_you_know_us=="Facebook") ? 'selected' : ''; ?>>Facebook</option>
                                            <option value="Twitter" <?php echo ($user->how_did_you_know_us=="Twitter") ? 'selected' : ''; ?>>Twitter</option>
                                        </select>
                                    </li>
                                </ul>
                                <input type="submit" class="submit-button ml205 submit-ubah-data" value="Ubah Profil">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
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
</script>

@endsection