<head>
<meta charset="utf-8">
<title>Untitled Document</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://m.berrybenka.com/assets/berrybenka/mobile/css/bb-mobile.min.css?v=1.2"/>
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('berrybenka/desktop/img/favicon.png') }}" />
<style>
html, body{margin:0;padding:0;background:url({{ asset('berrybenka/desktop/img/bg.jpg') }} ) center center no-repeat;background-size:cover;width:100%;height:100%;}
img{max-width:100%;}
.telp-wrapper{background:rgba(255,255,255,1);/*width:50%;*/float:right;position:relative;height:100%;display:table;vertical-align:middle;margin-right:10%;}
.telp-outer{display:table-cell;height:100%;vertical-align:middle;}
.telp-content{text-align:center;padding:20px;width:390px;margin:0 auto;display:table-cell;height:100%;vertical-align:middle;}
.logo-ref{border:2px solid #000;display:inline-block;overflow:hidden;border-radius:500px;padding:2px;}
.logo-ref span{border:1px solid #000;display:inline-block;border-radius:500px;}
.logo-ref img{width:80px;}
.name h3{font-family:Cambria, Hoefler Text, Liberation Serif, Times, "Times New Roman", serif;font-style:italic;}
.notelp{text-align:center;}
.notelp p{color:#f00;font-size:14px;}
.notelp input{display:inline-block;font-size:30px;background-color:#fbf390;border:1px solid #ccc;}
.notelp input[type="submit"]{width:390px;cursor:pointer;background-color:#da5550;border:none;color:#fff;font-size:16px;padding:7px 0;margin:10px 0 0 0;}
.code-area{width:60px !important;padding:7px 0 !important;}
.telp-number{padding:7px 10px !important;text-align:center;width:300px !important;}
::-webkit-input-placeholder {
   color: red;
}

:-moz-placeholder { /* Firefox 18- */
   font-size:20px;  
}

::-moz-placeholder {  /* Firefox 19+ */
   font-size:20px; 
}

:-ms-input-placeholder {  
   color: red;  
}
@media only screen and (max-width : 1000px){
	html, body{background-position:25% center;}
	.telp-wrapper{float:none;margin:none;height:auto;position:absolute;bottom:0;width:100%;}
	.telp-content{width:auto;}
	
	}
	@media only screen and (orientation : landscape){.telp-wrapper{position:relative;}}
	@media only screen and (max-width : 600px) and (orientation : portrait){
		.telp-wrapper,.telp-outer,.telp-content{display:block;}
		.notelp input[type="submit"]{width:100%;}
		.telp-wrapper{transform:scale(0.8);}
		.telp-number{width:93% !important;}
	}
	@media only screen and (max-width : 330px){.telp-wrapper{position:relative;transform:scale(1)}}
</style>
</head>
<body>
	<div class="telp-wrapper">
    	<div class="telp-outer">
        	<div class="telp-content">
            	<div class="logo-ref"><span><img src="{{ asset('berrybenka/desktop/img/logo-s2g.png') }}"></span></div>
                <div class="name">
                	<h3>Belanja Gratis di Store2Go</h3>
                    <p>Silahkan masukan nomor smartphone Anda dan Kami akan mengirimkan SMS berisi link untuk Mendownload Aplikasi Berrybenka untuk IOS atau Android Anda.</p>
                    <div class="notelp">
						<form onsubmit="sendSMS(this); return false;">
							<input class="code-area" id="area" name="area" type="tel" value="+62" readonly>
							<input class="telp-number" id="phone" name="phone" type="tel" placeholder="82XX - XXXX - XXXX"><br>
							<input type="submit" value="Kirim SMS">
						</form>
                        <p>*Pengiriman SMS tidak mengurangi pulsa Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<meta charset="UTF-8">
        <script type="text/javascript">
(function(b,r,a,n,c,h,_,s,d,k){if(!b[n]||!b[n]._q){for(;s<_.length;)c(h,_[s++]);d=r.createElement(a);d.async=1;d.src="https://cdn.branch.io/branch-v1.7.1.min.js";k=r.getElementsByTagName(a)[0];k.parentNode.insertBefore(d,k);b[n]=h}})(window,document,"script","branch",function(b,r){b[r]=function(){b._q.push([r,arguments])}},{_q:[],_v:1},"init data first addListener removeListener setIdentity logout track link sendSMS referrals credits creditHistory applyCode validateCode getCode redeem banner closeBanner".split(" "), 0);
branch.init('key_live_hepq2pnNWbgVpCwl6uHGidjesvat5Vkd');

            function sendSMS(form) {
                var phone = form.area.value + form.phone.value;
                var linkData = {
                    tags: [],
                    channel: 'Website',
                    feature: 'TextMeTheApp',
                    data: {
                        "foo": "bar"
                    }
                };
                var options = {};
                var callback = function(err, result) {
                    if (err) {
                        alert("Sorry, something went wrong.");
                    }
                    else {
                        alert("SMS sent!");
                    }
                };
                branch.sendSMS(phone, linkData, options, callback);
                form.phone.value = "";
            }
        </script>
