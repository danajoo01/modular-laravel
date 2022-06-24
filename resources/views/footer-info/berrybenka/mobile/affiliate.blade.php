<?php $time = microtime(true); 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.mobile.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/mobile/css/home.css") }}">
@endsection

@section('content')

<!--s::affiliate-->
<style>
.clearit::after{clear:both;display:block;content:"";}
.affiliate-container{max-width:960px;margin:0px auto;color:#555 !important;}
.affiliate-header{margin:20px 0 !important;}
.affiliate-header img{width:100%;display:block;height:auto;}
.affiliate-header p{margin: 10px auto;text-align: center;width: 80%;}
.affiliate-header:after{clear:both;content:"";display:block;}
.affiliate-content{border-top:1px solid #ccc;padding-top:20px;margin-top:20px;}
.affiliate-content h1 {font-size: 30px;text-align: center;}
.affiliate-content h1:before{display:block;position:absolute;top:0;background:#000;height:2px;width:100%;}
.affiliate-content ul li{display:block;float:left;width:25%;box-sizing:border-box;padding:10px;text-align:center;}
.affiliate-content ul li p{line-height:18px;letter-spacing:1px;}
.affiliate-content ul li img{width:50%;margin:0 auto;}
.half-page{width:100%;padding:10px;box-sizing:border-box;float:left;border-top:1px solid #ccc;padding:40px 0;}
.affiliate-content::after,.half-page-wrapper::after{display:block;content:"";clear:both;}
.half-page-left{width:50%;float:left;box-sizing:border-box;pading:10px;}
.half-page h1{margin:0 0 20px 0;font-size:20px;line-height:1;}
.half-page p{letter-spacing:1px;line-height:18px;}
.half-page img{float:left;margin:0 20px 20px 20px;width:100px;}
.mt30{marin-top:30px;}
.w50{width:50% !important;}
div.nama {position: relative;margin: 20px 0;display:block;width:100%;float:none;}
.nama label {display:none;color: #999;font-size: 18px;font-weight: normal;position: absolute;pointer-events: none;left: 5px;top: 10px;transition: 0.2s ease all;-moz-transition: 0.2s ease all;-webkit-transition: 0.2s ease all;}
.affiliate-form input[type="submit"] {margin-top: 20px;border:none;border-bottom: 3px solid #999;border-radius: 4px;cursor: pointer;text-transform: uppercase;font-size: 16px;background:#ddd !important;color:#222 !important;width:100%;padding:7px 0;}
.mv40{margin:20px 0;}
.form-wrapper{display:block;box-sizing:border-box;padding:0 20px 20px 20px;box-sizing:border-box;}
.affiliate-content2 ul li {width:20%;}
.affiliate-content2 ul li img{width:75px;max-height:75px;}
.affiliate-content2 ul li{letter-spacing:0px;font-weight:normal;line-height:18px;}
.affiliate-content2 strong{font-size:22px;}
.login-container{display: inline-flex;position: absolute; margin: -3.2% 0 0 3.2%;}
.sign-log{display:block;width:300px;margin:0 auto;border:1px solid #ccc;text-transform:uppercase;text-align:center;padding:10px 0;background-color:#000;color:#fff;}
.sign-log2{display:block;width:100px;margin:0 auto;border:1px solid #ccc;text-transform:uppercase;text-align:center;padding:5px 0;background-color:#000;color:#fff;}
.sign-log:hover{color:#fff;}
.sign-log2:hover{color:#fff;}

.reg-text{text-align:center;margin:20px auto 0 auto;}
.keterangan{text-align:center;margin-bottom: 50px;}
.keterangan ul li{display:inline-block;font-weight:bold;}
.affiliate-form{padding:0 0 0 20px;border-left:1px solid #ccc;width:47%;margin-left:0%;float:left;}
.affiliate-form input,.affiliate-form textarea{border:1px solid #999;box-sizing:border-box;padding:10px;width:100%;padding:10px;background:none;}
@-webkit-keyframes "inputHighlighter" {
    from {
        background: #5264AE;
    }
    to {
        width: 0;
        background: transparent;
    }
}
@-moz-keyframes "inputHighlighter" {
    from {
        background: #5264AE;
    }
    to {
        width: 0;
        background: transparent;
    }
}
@keyframes "inputHighlighter" {
    from {
        background: #5264AE;
    }
    to {
        width: 0;
        background: transparent;
    }
}
@media only screen and (max-width:699px){
.affiliate-content ul li{width:50%;}
.half-page div{width:100%;text-align:center;margin:10px auto;float:none;}
div .half-page-left{width:90%;}
/*.half-page img{margin:0;float:none;}*/
.half-page img{margin:10px auto;float:none;}
.affiliate-form{width:100%;border:none;padding:0;margin:0;}
.affiliate-content2 h1{font-size:18px;line-height:1.3;}
.affiliate-content ul li{width:100%;}
.keterangan ul li{margin:5px 0;}
.keterangan ul li:nth-child(2),.keterangan ul li:nth-child(4){display:none;}
.login-container{display: inline-flex;position: absolute; margin: -5.5% 0 0 3.2%;}
.sign-log2{display: block;width: 50px;margin: 0 auto;border: 1px solid #ccc;text-transform: uppercase;text-align: center;background-color: #000;color: #fff;font-size: 9px;padding: 0px;}
.sign-log2:hover{color:#fff;}
}
.login-sign{display:block;margin:20px 0;}
.login-sign::after{clear:both;display:block;content:"";}
.login-sign a{display:block;float:left;width:49%;text-align:center;background:#cecece;border-radius:2px;padding:10px 0;border:1px solid #aaa;}
.login-sign a:last-child{float:right;}
@media only screen and (min-width:700px){
.half-page img{margin-bottom:40px;}
}
</style>

<div class="content-home">
<div class="affiliate-container  mb30">
    <div class="row" style="margin: 0 !important;">
                
                <div class="sixteen columns affiliate-header">
            <div class="img-tulisan" style="position:relative;margin-top:35px;">
                <a href="https://berrybenka.hasoffers.com/login" target="_blank">
                    <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/banner.jpg">
                </a>
            </div>
            <p>BERRYBENKA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner BERRYBENKA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.</p>
            <div class="login-sign">
                <a href="https://berrybenka.hasoffers.com/login">LOGIN</a>
                <a href="http://berrybenka.hasoffers.com/signup">SIGN UP</a>
            </div>
        </div>
        <div class="clear"></div>
        <div class="affiliate-content">
            <h1>HOW IT WORKS</h1>
            <ul>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/list1.gif"><p>Daftar menjadi partner affiliate BERRYBENKA</p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/list2.gif"><p>Partner menampilkan banner/link berrybenka.com</p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/list3.gif"><p>Pengunjung website/blog partner meng-klik banner/link berrybenka.com</p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/list4.gif"><p>Partner mendapat komisi  (IDR) dari transaksi pengunjung</p></li>
            </ul>
        </div>
        <div class="half-page-wrapper">
            <div class="half-page clearit">
                <div class="half-page-left">
                    <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/commission-icon.gif">
                    <h1>COMMISSION</h1>
                    <p>Kami memberikan komisi setiap bulannya, dengan besar komisi 10% untuk setiap transaksi yang direferensikan dari link afiliasi BERRYBENKA.com yang ada pada website atau blog Anda.</p><br>
                    <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/report.gif">
                    <h1>REPORT</h1>
                    <p>Anda dapat dengan mudah mengakses laporan pendapatan, besar komisi yang sudah Anda dapatkan, dan laporan lainnya dalam panel affiliate yang kami sediakan.</p><br>
                    <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/support.gif">
                    <h1>SUPPORT</h1>
                    <p>Anda dapat menghubungi kami  untuk pertanyaan, kritik, saran, dan berbagai informasi  tentang BERRYBENKA.com affiliate program, melalui form disamping ini. </p><br>
					<img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/networking-group.png">
					<h1>REFERRAL</h1>
                    <p>Mau dapat komisi lebih banyak dari affiliate Berrybenka?</p>
                    <p>Ajak orang-orang terdekat Anda untuk mendaftar jadi partner affiliate Berrybenka, dan dapatkan tambahan komisi 10%. Tahu lebih banyak, <a href="http://m.berrybenka.com/affiliate/referral"><strong><u>klik disini</u></strong></a></p>
				</div>
                <div class="affiliate-form">
                    <span class="form-wrapper">
                        <p>Jika ada pertanyaan silahkan hubungi kami melalui email ke <a href="emailto:affiliate@berrybenka.com"><strong>affiliate@berrybenka.com</strong></a> atau silahkan isi form dibawah ini.</p>
                        <form action="" method="post">
                            <div class="nama">
                                <input type="text" name="name" pattern="[a-zA-Z ]+" title="Hanya huruf yang diperbolehkan" required="" placeholder="Nama" value="">
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label>Nama</label>
                            </div>
                            <div class="nama">
                                <input type="email" name="guest_email" required="" placeholder="email" value="">
                                <span class="highlight"></span>
                                <span class="bar"></span>
                                <label>Email</label>
                            </div>
                            <textarea name="message" style="resize:vertical;height:170px;" required placeholder="Pesan" value=""></textarea>
                            <div class="nama">
                                <div class="g-recaptcha" data-sitekey="6Lf-shwTAAAAAIXgGA2LKTrMMKNM64flCSkWzzLj"><div style="width: 304px; height: 78px;"><div><iframe src="https://www.google.com/recaptcha/api2/anchor?k=6Lf-shwTAAAAAIXgGA2LKTrMMKNM64flCSkWzzLj&amp;co=aHR0cDovL20tZmVlZC5iZXJyeWJlbmthLmNvbTo4MA..&amp;hl=en&amp;v=r20170503135251&amp;size=normal&amp;cb=yhrkxfd9rd0f" title="recaptcha widget" width="304" height="78" frameborder="0" scrolling="no" name="undefined"></iframe></div><textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;  display: none; "></textarea></div></div>
                            </div>
                            <input type="submit" value="Kirim">
                        </form>
                    </span>
                </div>
            </div>
        </div>
        <div class="affiliate-content affiliate-content2" style="margin:0;">
            <h1>MENGAPA MEMILIH AFFILIATE BERRYBENKA ?</h1>
            <ul>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/free.gif"><p>GRATIS<br>dan siapapun bisa bergabung</p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/sale.gif"><p>BERRYBENKA <br>menjual lebih dari 1000 brand lokal dan internasional</p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/list44.gif"><p>Komisi <br><strong>10%</strong><br> Untuk setiap transaksi</p></li>
				<li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/networking-group.png"><p style="font-size:14px;">Tambahan komisi dengan<br><strong style="font-size:16px;">Affiliate Referral</strong></p></li>
                <li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/timsupport.gif"><p><strong style="font-size:14px;line-height:1.2;">Realtime Report &amp; Tim Support</strong><br> yang siap membantu</p></li>
			</ul>
        </div>
        <a href="https://berrybenka.hasoffers.com/signup" class="sign-log">daftar sekarang</a>
        <p class="reg-text">Atau belum menjadi member BERRYBENKA.com, registrasi <a href="https://berrybenka.com/customer/account/auth"><strong>disini</strong></a> dan dapatkan voucher belanja</p>
        <div class="keterangan">
            <ul>
                <li><a href="https://blogaffiliateberrybenka.wordpress.com/">Blog</a></li>
                <li>|</li>
                <li><a href="http://m.berrybenka.com/affiliate/faq">FAQ</a></li>
                <li>|</li>
                <li><a href="http://m.berrybenka.com/affiliate/illustration">Ilustrasi Kerja</a></li>
            </ul>
        </div>
    </div>
</div>
</div>

<!--e::affiliate-->

@endsection



