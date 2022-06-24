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
</style>
<div class="content-home">
<div class="affiliate-container  mb30">
    <div class="row" style="margin: 0 !important;">
        <div class="sixteen columns affiliate-header">
        	<a href="https://berrybenka.hasoffers.com/login" target="_blank">
                <img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/banner.jpg">
            </a>
            <p>
                Hijabenka affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner BERRYBENKA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.
            </p>
        </div>
        <div class="clear"></div>
        
<!--style faq & Ilustrasi-->
<style>
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
</style>
        <div class="aff-faq">
            <div class="full-width relative text-center mb20">
            	<div class="section-title" id="special-section" style="background:#fff;">ILUSTRASI</div>
            </div>
        </div>
        <div class="ilustrasi clearit">
        	<ul>
            	<li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/1.jpg">
                    <p>A adalah pemilik website yang ingin mendapatkan income dari websitenya</p>
                </li>
                <li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/2.jpg">
                    <p>Tertarik dengan cara yang mudah dan besar komisi 10% yang diberikan, A mendaftar sebagai partner program affiliate </p>
                </li>
                <li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/3.jpg">
                    <p>A menampilkan link  dan banner affiliate BERYBENKA pada websitenya </p>
                </li>
                <li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/4.jpg">
                    <p>B adalah pengunjung website A. B tertarik dan melakukan klik pada link affiliate yang ditampilkan dalam website A</p>
                </li>
                <li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/5.jpg">
                    <p>B diarahkan menuju website HIJABENKA dan melakukan transaksi pembelian</p>
                </li>
                <li>
                	<img src="http://m.hijabenka.com/hijabenka/desktop/img/affiliate/6.jpg">
                    <p>Maka, A sebagai partner affiliate HIJABENKA, berhak mendapatkan komisi 10% atas transaksi yang dilakukan B</p>
                </li>
            </ul>
        </div>
        <div class="ilustrasi-detail">
            <p>Ilustrasi komisi program affiliate Hijabenka,</p>
            <strong>Hijabenka.com memberikan komisi sebesar 10%  untuk setiap tranksaksi yang berasal dari link affiliate yang direfensikan partner.</strong>
            <div class="simulasi-komisi">
            	<p>Simulasi komisi</p>
                <ol>
                	<li>
                    	<span>Pageview website atau blog Anda</span>
                        <span> : </span>
                        <span>10.000 viewer /bulan</span>
                    </li>
                    <li>
                    	<span>Viewer yang membaca &amp; meng-klik post Anda</span>
                    	<span> : </span>
                        <span>5.000 viewer ( asumsi 50% )</span>    
                    </li>
                    <li>
                    	<span>Viewer yang meng-klik dan melakukan<br>transaksi di website hijabenka.com</span>
                        <span> : </span>
                        <span>500 viewer (asumsi 10% )</span>
                    </li>
                    <li>
                    	<span>Average order volume hijabenka.com</span>
                        <span> : </span>
                        <span>Rp 200.000</span>
                    </li>
                </ol>
                <p>Maka asumsi komisi yang akan Anda dapatkan dalam satu bulan adalah,</p>
                <p><strong>500</strong> (Viewer yang meng-klik dan melakukan transakasi di web hijabenka.com dalam 30 hari setelah meng-klik) <strong>x Rp 200.000</strong> (Average order Berrybenka) <strong>x 10%</strong> (Besar komisi yang diberikan Berrybenka) = <strong>Rp 10.000.000,-/bulan</strong></p>
            </div>
        </div>
    </div>
</div>
</div>
<!--e::affiliate-->

@endsection



