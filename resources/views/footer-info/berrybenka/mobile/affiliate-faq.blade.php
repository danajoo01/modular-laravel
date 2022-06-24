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
.section-title {
    height: 40px;
    line-height: 40px;
    padding: 0 20px;
    text-align: center;
    display: block;
    margin: 0 auto;
    color: #111;
    background-color: #f3f3f3;
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
                <img src="http://m.berrybenka.com/berrybenka/desktop/img/affiliate/banner.jpg">
            </a>
            <p>
                BERRYBENKA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner BERRYBENKA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.
            </p>
        </div>
        <div class="clear"></div>
        
        
        <div class="aff-faq">
            <div class="full-width relative text-center mb20">
            	<div class="section-title" id="special-section">FAQ</div>
            </div>
            <ol>
                <li>
                    <p>Apa itu program affiliate Berrybenka ?</p>
                    <span>BERRYBENKA affiliate program adalah program kerjasama yang menguntungkan untuk para pemilik blog atau website melalui link afiliasi. Anda sebagai partner hanya  perlu menampilkan link afiliasi yang telah kami sediakan dan Anda akan mendapatkan keuntungan berupa komisi untuk setiap transaksi dari link yang Anda referensikan.</span>
                </li>
                <li>
                	<p>Mengapa harus bergabung dalam program affiliate Berrybenka ?</p>
                    <span>Pertama Berrybenka.com adalah situs belanja online fesyen dan kecantikan ternama di Indonesia. Berrybenka menjual lebih dari 1000 merek lokal dan internasional, termasuk produk in-house label yang update mengikuti trend terkini,  memiliki lebih dari 20 juta page views dan  1,5 juta unique visitors perbulannya, komisi  hinga 10 % yang tentunya dapat memberikan keuntungan lebih bagi Anda, dan berbagai affiliate program yang menarik yang mendukung kinerja Anda.</span>
                </li>
                <li>
                	<p>Bagaimana cara bergabung menjadi partner affiliate Berrybenka ?</p>
                    <span>Untuk bergabung menjadi partner affiliate Berrybenka, Anda hanya harus melakukan sign up sebagai partner affiliate dalam website ini. Untuk mendaftar silahkan klik <a href="http://berrybenka.hasoffers.com/signup" target="_blank">disini</a>.</span>
                </li>
                <li>
                	<p>Siapa saja yang dapat bergabung dalam program affiliate Berrybenka ?</p>
                    <span>Siapapun yang memiliki blog atau website dapat bergabung  menjadi partner affiliate.</span>
                </li>
                <li>
                	<p>Bagaimana cara masuk ke dashboard akun affiliate saya ?</p>
                    <span>Login dengan menggunakan email dan password yang Anda gunakan untuk mendaftar, maka Anda akan masuk ke dalam dashboard affiliate Anda. Untuk masuk ke halaman login, silahkan klik<a href="https://berrybenka.hasoffers.com/login" target="_blank"> disini</a>.</span>
                </li>
                <li>
                	<p>Berapa besar komisi yang diberikan Berrybenka ?</p>
                    <span>Komisi merupakan hak Anda sebagai partner affiliate ketika terjadi transaksi dari link afiliasi yang direfensikan, adapun besar komisi yang Anda dapatkan dalam program affiliate Berrybenka adalah sebesar 10% dari setiap transaksi (sesuai ketentuan pajak yang berlaku).</span>
                </li>
                <li>
                	<p>Bagaimana dan kapan saya akan mendapatkan komisi ?</p>
                    <span>Pembayaran komisi akan dilakukan secara periodik setiap bulannya dan akan dibayarkan melalui transfer ke rekening aktif yang telah Anda daftarkan.<br>Jika ada perubahan nomor  rekening bank silahkan hubungi kami melalui <a href="mailto:affiliate@berrybenka.com">affiliate@berrybenka.com</a></span>
                </li>
                <li>
                	<p>Bagaimana cara melihat pendapatan dan performa saya ?</p>
                    <span>Login ke dalam dashboard akun affiliate Anda dan lihat pada menu Report. Untuk masuk ke halaman login, silahkan klik <a href="https://berrybenka.hasoffers.com/login">disini</a>.</span>
                </li>
            </ol>
        </div>
    </div>
</div>
</div>
<!--e::affiliate-->

@endsection



