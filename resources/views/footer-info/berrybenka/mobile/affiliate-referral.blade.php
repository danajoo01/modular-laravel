<?php 
$time = microtime(true); 
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
.content-home{background:#fff !important;}
.section-title {
    height: 40px;
    line-height: 40px;
    padding: 0 20px;
    text-align: center;
    display: block;
    margin: 0 auto;
    color: #111;
    background-color: #fff;
    font-weight: 300;
    font-size: 18px;
    font-family: Georgia,Times,serif;
    font-style: italic;
    z-index: 2;
	position:relative;
}
.section-title:before {
    left: 0 !important;
}
.aff-faq{padding:10px;}
.aff-faq ol li{margin:30px 0;letter-spacing:1px;line-height:1.2;}
.aff-faq ol li p{color:#000 !important;margin-bottom:5px;}
.section-title:before {
    content: "";
    width: 30px;
    height: 1px;
    position: absolute;
    top: 50%;
    left: -30px;
    border-top: 1px solid #111;
    z-index: -1;
}
.section-title:after {
    right: 0 !important;
}
.section-title:after {
    content: "";
    width: 30px;
    height: 1px;
    position: absolute;
    top: 50%;
    right: -30px;
    border-top: 1px solid #111;
    z-index: -1;
}
.clearit::after{clear:both;display:block;content:"";}
.affiliate-container{max-width:960px;margin:0px auto;color:#555 !important;font-family:'stempel',aria !important;color:#555 !important;}
.affiliate-container *{color:#555 !important;}
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
.aff-faq ol li{margin-bottom:30px;font-weight:bold;}
.aff-faq ol li p{margin-bottom:5px !important;}
.aff-faq ol li span{font-weight:normal;}
.ilustrasi ul li{width:16.6%;display:block;float:left;text-align:center;padding:5px;box-sizing:border-box;}
.ilustrasi ul li p{margin:10px 0;line-height:1.3;letter-spacing:1px;}
.ilustrasi-detail{border-top:1px dashed #999;padding:20px 10px;}
.ilustrasi-detail p:first-child{letter-spacing:1px;text-transform:uppercase;margin-bottom:20px;}
.simulasi-komisi *{letter-spacing:1px;line-height:1.3;}
.simulasi-komisi ol li{margin:20px 0;}
.simulasi-komisi span{display:inline-block;}
.simulasi-komisi ol li span:first-child{width:35%;}
@media only screen and (max-width:699px){
.ilustrasi ul li{width:33.3%;min-height:210px;}	
.ilustrasi ul li img{max-width:50%;display:block;margin:0 auto;}
}
@media only screen and (max-width:500px){
.ilustrasi ul li{width:50%;min-height:210px;}	
}
@media only screen and (max-width:350px){
	.simulasi-komisi ol li span:nth-child(2){margin-right:5px;display:none;}
	.simulasi-komisi ol li span:first-child{width:100%;font-weight:bold;margin-bottom:5px;}
}

/*referral*/
html, body{margin:0;padding:0;font-family:"Open Sans","HelveticaNeue","Helvetica Neue",Helvetica,Arial,sans-serif;}
.wrapper{max-width:960px;overflow:hidden;margin:0 auto;padding:0 15px;}
.header-banner{max-width:960px;margin:0 auto;}
img{max-width:100%;width:100%;height:auto;}
.tentang-affiliate{letter-spacing:1px;font-weight:normal;}
.tentang-affiliate h1{font-size:18px;border-bottom:1px solid #333;padding:10px 0;margin:0;}
.tentang-affiliate p{line-height:1.5;font:16px;}
.towork ul,.commision ul{padding:0;margin:30px 0;}
.towork:after,.commision:after{clear:both;display:block;content:"";}
.towork ul li,.commision ul li{list-style:none;display:block;float:left;text-align:center;box-sizing:border-box;}
.towork ul li:nth-child(odd){width:21.25%;}
.towork ul li:nth-child(even){width:5%;padding:2% 0;}
.commision ul li{float:left;width:50%;box-sizing:border-box;padding:10px 50px;text-align:left;}
.commision ul li:first-child{text-align:center;}
.commision img{width:250px;}
.about-affiliate,.towork,.commision,.assist{border:1px dashed #333;box-sizing:border-box;padding:10px;margin:20px 0;}
@media only screen and (max-width:700px){
	.towork ul li:nth-child(odd){width:25%;}
	.towork ul li:nth-child(even){display:none;}
	.commision ul li img{margin:0 auto;}
}
@media only screen and (max-width:500px){
	.towork ul li:nth-child(odd){width:100%;margin:10px 0;}
	.commision ul li{width:100%;padding:0;}
}
</style>
<div class="content-home">
<div class="header-banner">
    <a href="https://berrybenka.hasoffers.com/login" target="_blank">
        <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/banner-referral.jpg">
    </a>
</div>
<div class="wrapper">
    <div class="tentang-affiliate">
		<div>
		<br>
    	<p align="center">Referral affiliate Berrybenka adalah bagian dari program affiliate yang memberikan kesempatan kepada Anda sebagai partner affiliate untuk mendapatkan komisi tambahan dengan merekomendasikan orang lain untuk mendaftar sebagai partner affiliate Berrybenka  melalui URL referral unik (signup link) yang telah disediakan pada dashboard masing-masing akun affiliate.</p>
        </div>
		<div class="towork">
        	<h1>HOW TO WORK </h1>
            <ul>
            	<li>A Terdaftar sebagai Partner affiliate Berrybenka atau Hijabenka.</li>
                <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                <li>A membagikan link referral pendaftaran affiliate miliknya melalui media tertentu.</li>
                <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                <li>B tertarik bergabung dan mendaftar melalui link yang dibagikan A.</li>
                <li><i class="fa fa-arrow-right" aria-hidden="true"></i></li>
                <li>A akan mendapatkan 1% komisi berkelanjutan dari komisi affiliate uang didapat oleh B.</li>
            </ul>
        </div>
        <div class="commision">
        	<h1>COMMISION</h1>
            <ul>
            	<li><img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/commision.gif" alt=""></li>
                <li>Besar komisi yang Anda dapatkan sebagai referring dalam program referral affiliate ini adalah 1% dari persentasi komisi affiliate yang didapat downline Anda, atau 10% dari total komisi (payout) yang didapat downline Anda dari transaksi yang datang melalui link affiliate yang downline dibagikan.</li>
            </ul>
        </div>
        <div class="assist">
        	<h1>NEED ASSISTANT ?</h1>
            <p>Tertarik dapat komisi lebih? Klik <a href="https://blogaffiliateberrybenka.wordpress.com/"><strong><u>disini</u></strong></a> untuk mengetahui informasi mengenai program referral affiliate Berrybenka lebih detail.</p>
            <p>Jika ada hal lain yang ingin ditanyakan, silahkan langsung hubungi tim support kami melalui email ke <a href="mailto:affiliate@berrybenka.com"><strong><u>affiliate@berrybenka.com</u></strong></a></p>
        </div>
    </div>
</div>
</div>
<!--e::affiliate-->

@endsection



