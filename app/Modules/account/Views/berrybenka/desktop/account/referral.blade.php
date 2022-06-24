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
        	{!! get_view('account', 'account.leftmenu', array('page'=>'referral','user'=>$user)) !!}
            <div class="user-right right">
                <div class="user-dashboard clearfix">
                    <h1 class="clearfix">
                        <i class="fa fa-dashboard"></i>Belanja Gratis
                    </h1>
                    <div class="referral-code">
                        <h1><i class="fa fa-link ring-outer"></i><span>Referral Code</span></h1>
                        <div class="referral-body">
                            <p>Ajak teman mendownload Aplikasi Berrybenka dan dapatkan <br>Benka Poin senilai IDR 25,000!</p>
                            <div class="code-ref">
								<input type="text" id="link">
                                <input type="button" value="copy" data-clipboard-target="#link" data-clipboard-action="copy" class="rescode-btn">
                                <div class="clear"></div>
                            </div>
                            <p>Atau Bagikan Via :</p>
                            <ul>
                                <li class="share-fb"><a id="facebook_branch" href="javascript:void(0);"><i class="fa fa-facebook"></i></a></li>
                                <li class="share-mail"><a id="email_branch" href="javascript:void(0);"><i class="fa fa-envelope"></i></a></li>
                                <li class="share-twit"><a id="twitter_branch" href="#" class="twitter popup"><i class="fa fa-twitter"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="benka-wrapping">
                    	<div class="half-ref-code left pl0">
                            <div class="nobl half-ref-code-content referral-code mt0">
                                <h5><i class="fa fa-info-circle"></i> Informasi Benka Poin</h5>
                                <div class="termcon">
                                    <ul>
                                        <li>1 Benka Poin senilai dengan IDR 1</li>
                                        <li>Benka Poin dapat digunakan untuk berbelanja tanpa minimum pembelian</li>
                                        <li>Benka Poin senilai IDR 25,000 akan ditambahkan kepada teman yang anda undang setelah men-download aplikasi Berrybenka dan register akun baru</li>
                                        <li>Benka Poin senilai IDR 25,000 akan ditambahkan ke dalam akun anda setelah teman yang anda undang melakukan pembelanjaan dan menerima barang pesanannya.</li>
                                        <li>Berrybenka berhak menolak segala transaksi apabila ditemukan indikasi   kecurangan yang dilakukan oleh pengguna dalam referral program</li>
                                        <!--<li>Info lebih lanjut klik <a href="http://berrybenka.com/special-promo/referral-program">disini</a></li>-->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="half-ref-code left pr0">
                            <div class="nobl half-ref-code-content referral-code mt0">
                                <h5><i class="fa fa-question-circle"></i> Cara Reedem Benka Poin</h5>
                                <div class="termcon">
                                    <ul>
                                        <li>Belanja di Berrybenka</li>
                                        <li>Masukkan poin yang ingin anda gunakan sebagai diskon pada saat checkout</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<!-- JS here -->
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.11";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
</script>

<script>
    var clipboard = new Clipboard('.rescode-btn');

    clipboard.on('success', function(e) {
        console.log(e);
    });

    clipboard.on('error', function(e) {
        console.log(e);
    });
    </script>
	<script>
    function fbShare(url, title, descr, image, winWidth, winHeight) {
        var winTop = (screen.height / 2) - (winHeight / 2);
        var winLeft = (screen.width / 2) - (winWidth / 2);
        var images = image.replace(/\./g,'%2E');
        window.open('http://www.facebook.com/sharer.php?s=100&p[title]=' + title + '&p[summary]=' + descr + '&p[url]=' + url + '&p[images][0]=' + images, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
    }
</script>
<script>
  $('.popup').click(function(event) {
    var width  = 575,
        height = 400,
        left   = ($(window).width()  - width)  / 2,
        top    = ($(window).height() - height) / 2,
        url    = this.href,
        opts   = 'status=1' +
                 ',width='  + width  +
                 ',height=' + height +
                 ',top='    + top    +
                 ',left='   + left;
    
    window.open(url, 'twitter', opts);
 
    return false;
  });
</script>

<script type="text/javascript">
(function(b,r,a,n,c,h,_,s,d,k){if(!b[n]||!b[n]._q){for(;s<_.length;)c(h,_[s++]);d=r.createElement(a);d.async=1;d.src="https://cdn.branch.io/branch-v1.7.1.min.js";k=r.getElementsByTagName(a)[0];k.parentNode.insertBefore(d,k);b[n]=h}})(window,document,"script","branch",function(b,r){b[r]=function(){b._q.push([r,arguments])}},{_q:[],_v:1},"init data first addListener removeListener setIdentity logout track link sendSMS referrals credits creditHistory applyCode validateCode getCode redeem banner closeBanner".split(" "), 0);
branch.init('key_live_hepq2pnNWbgVpCwl6uHGidjesvat5Vkd');

	function setIdentity() {
		var identity = "<?php echo $user->customer_id?>";
		var callback = function(err, result) {
			if (err) {
				
			}
		};
		branch.setIdentity(identity, callback);
	}
	
	setIdentity();
	
	branch.link({
    channel: 'facebook',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
	}, function(err, link) {
		var url_fb = "javascript:fbShare('"+ link + "','Fb Share','Facebook share popup','{{ asset('berrybenka/desktop/img/sosmed/fb.jpg') }}',520,350)";
		$('#facebook_branch').attr("href", url_fb);
	});
	
	branch.link({
    channel: 'twitter',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
	}, function(err, link) {
		var url_tw = "http://twitter.com/intent/tweet?url="+ link +"&text=Download aplikasi Berrybenka dan dapatkan Benka Poin untuk berbelanja senilai IDR 25.000.";
		$('#twitter_branch').attr("href", url_tw);
	});
	
	branch.link({
    channel: 'email',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
	}, function(err, link) {
		var url_email = "mailto:?subject=Belanja Gratis Di Berrybenka&body=Download aplikasi Berrybenka dan dapatkan Benka Poin untuk berbelanja senilai IDR 25.000 tanpa minimum pembelian. " + link +"";
	$('#email_branch').attr("href", url_email);
	});
	
	branch.link({
    channel: 'general',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
	}, function(err, link) {
		$('#link').val(link);
	});
	
</script>
@endsection