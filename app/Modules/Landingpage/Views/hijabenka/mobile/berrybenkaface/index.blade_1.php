@extends('layouts.berrybenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('berrybenka/mobile/css/bbface.css') }}">
@endsection

@section('content')
<div class="content" style="background:#F3F3F3;">
    <div class="about-wrapper affi-wrapper">
        <div class="sixteen columns affiliate-header" style="margin-top: 55px;">
                <a class="affi-head-img" target="_blank" href="">
                    <img src="/berrybenka/desktop/img/bbface/IB-BB-FACE-1080x550-R1.jpg">
                </a>
                <p>Berrybenka Face adalah pencarian duta Berrybenka, yang akan menjadi model untuk media sosial Berrybenka. Pemenang akan dipilih oleh pihak internal Berrybenka dan mendapatkan berbagai hadiah dan pengalaman istimewa.</p>

            </div>
            <div class="bbface-howto">
                <img src="/berrybenka/desktop/img/bbface/R4_15112016_Affiliate_ButtonBBFaceSteps.png" alt="">
            </div>
            <div class="cara-gabung">
                    <h1>CARA BERGABUNG</h1>
                <ol>
                    <li>Daftar melalui website <a href="http://www.berrybenka.com/berrybenkaface" target="_blank">www.berrybenka.com/berrybenkaface</a></li>
                    <li>Posting Foto OOTD terbaik kamu di Instagram, mention dan tag <strong>@Berrybenka</strong> dan cantumkan hashtag <strong>#berrybenkaface2017 #BerrybenkaLook #BBFace</strong></li>
                    <li>Periode pendaftaran mulai tanggal 1 – 30 Desember 2016 </li>
                    <li>Posting foto sebanyak-banyaknya, konten terbaik akan menjadi SEMI FINALIS Berrybenka Face yang akan diumumkan pada 6 Januari 2017</li>
                    <li>Pengumuman pemenang di tanggal 20 Januari 2017</li>
                    <li>Berdomisili di daerah <strong>Jabodetabek</strong>, berusia 18 - 25 tahun</li>
                </ol>
            </div>
            <div class="two-collumn">
                    <div class="l-col">
                    <h1>HADIAH</h1>
                    <ul>
                            <li>
                            <div class="hadiah-image"><img src="/berrybenka/desktop/img/bbface/21112016_Icon_ButtonBBFaceSteps_01DutaBBFace.png" alt=""></div>
                            <div class="hadiah-wording">
                                    <h1>Menjadi duta Berrybenka Face selama 3 bulan</h1>
                                <p>Kamu bisa mendapatkan fasilitas eksklusif dan kesempatan istimewa dari Berrybenka.</p>
                            </div>
                        </li>
                        <li>
                            <div class="hadiah-image"><img src="/berrybenka/desktop/img/bbface/21112016_Icon_ButtonBBFaceSteps_02FreeShopping.png" alt=""></div>
                            <div class="hadiah-wording">
                                    <h1>Gratis belanja di Berrybenka selama 3 bulan</h1>
                                <p>Kamu bisa pilih barang yang kamu suka dan mendapatkannya secara gratis selama 3 bulan.</p>
                            </div>
                        </li>
                        <li>
                            <div class="hadiah-image"><img src="/berrybenka/desktop/img/bbface/21112016_Icon_ButtonBBFaceSteps_03SpecialCommission.png" alt=""></div>
                            <div class="hadiah-wording">
                                    <h1>Komisi spesial dari program Afiliasi</h1>
                                <p>Kamu bisa langsung terdaftar sebagai anggota afiliasi Berrybenka dan mendapatkan komisi setiap pembelian.</p>
                            </div>
                        </li>
                        <li>
                            <div class="hadiah-image"><img src="/berrybenka/desktop/img/bbface/21112016_Icon_ButtonBBFaceSteps_04InternshipChance.png" alt=""></div>
                            <div class="hadiah-wording">
                                    <h1>Kesempatan magang di kantor pusat Berrybenka</h1>
                                <p>Kamu langsung dapat golden ticket untuk kempatan magang selama 3 bulan di kantor pusat Berrybenka.</p>
                            </div>
                        </li>
                        <li>
                            <div class="hadiah-image"><img src="/berrybenka/desktop/img/bbface/21112016_Icon_ButtonBBFaceSteps_05ModelChance.png" alt=""></div>
                            <div class="hadiah-wording">
                                    <h1>Menjadi model media sosial Berrybenka selama 3 bulan</h1>
                                <p>Kamu berkesempatan untuk lebih terkenal dengan menjadi model di akun media sosial Berrybenka selama 3 bulan.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="r-col" id="form-regis">
                    <h1>REGISTRASI</h1>
                    <span>Jika kamu tertarik untuk bergabung, segera daftarkan dirimu dengan mengisi form di bawah ini:</span>
                    <div class="bbf-form">      
                        @if(!empty(Session::get('error')))                        
                            {!! error_message(Session::get('error')) !!}
                        @endif
                        
                        @if(!empty(Session::get('bbf_success')))
                            
                            <div class="success-msg-login">
                                <i aria-hidden='true' class='fa fa-bell' style="color: #027C0E !important;"></i>
                                <i aria-hidden='true' class='fa fa-times' style="color: #027C0E !important;"></i>
                                {{ Session::get('bbf_success') }}
                            </div>
                        @endif
                        
                        <form name="berrybenkaface" method="POST" action="/berrybenkaface/register">
                            {!! csrf_field() !!}
                            <input name="bbf_name" type="text" placeholder="Nama Lengkap" value="{{ old('bbf_name') }}" required>
                            <input name="bbf_idig" type="text" placeholder="ID Instagram" value="{{ old('bbf_idig') }}" required>
                            <input name="bbf_numfollowers" type="text" placeholder="Jumlah Followers" value="{{ old('bbf_numfollowers') }}" required>
                            <input name="bbf_email" type="text" placeholder="Alamat Email" value="{{ old('bbf_email') }}" required>
                            <input type="submit" value="KIRIM">
                        </form>
                    </div>
                </div>
            </div>
            <div class="bbf-info">
                <h1>Info lebih lanjut:</h1>
                <p>berrybenkaface@berrybenka.com</p>
                <p>Jl. KH. Mas Mansyur no. 19 </p>
                <p>RT 09 / RW 06, Tanah Abang, Jakarta Pusat 10250, Indonesia </p>
                <p>(021) – 29022067 ext. 124 </p>
            </div>
    </div>
</div>
@endsection

@section('js')
<!-- JS here -->
<script type="text/javascript">
$(document).ready(function(){
    $('.success-msg-login .fa-times').click(function(){
        $(this).parent().hide().addClass('disabled');
    });
});   
</script>    
@endsection