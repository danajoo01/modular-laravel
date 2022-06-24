@extends('layouts.hijabenka.mobile.main')

@section('css')
<!-- CSS here -->
<link rel="stylesheet" href="{{ asset('hijabenka/mobile/css/account.css') }}">
@endsection

@section('content')
<div class="content-detail">
    <div class="account-wrapper">
        {!! get_view('account', 'account.loyaltyheader', array('user'=>$user)) !!}
        <div class="account-body">
            <div class="benka-wrapper">
                <ul>
                    <li>
                        <h1 class="border-bot b-gratis">
                            <a href="#">belanja gratis hijabenka</a>
                            <i aria-hidden="true" class="fa fa-angle-down"></i>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/account_dashboard">Akun Saya</a>
                            </h1>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/order_history">Daftar Pemesanan</a>
                            </h1>
                            <h1 class="border-bot b-gratis hidden">
                                <a href="/user/change_password">Ganti Password</a>
                            </h1>
                        </h1>
                    </li>
                </ul>
                <div class="b-gratis-bb">
                    <p>Ajak teman mendownload Aplikasi hijabenka dan dapatkan Benka Poin senilai IDR 25,000!
                        <br>
                        <a href="/user/how_to_referral"><i class="fa fa-info-circle"></i> Info Lebih Lanjut</a>
                    </p>
                    <input type="text" id="link" class="link-bpoint">
                    <input type="button" value="COPY" data-clipboard-target="#link" data-clipboard-action="copy" class="rescode-btn">
                    <p class="share-by">Atau bagikan via :</p>
                    <ul class="clear">
                        <li><a href="#" id ="facebook_branch" class="fb-share"><i class="fa fa-facebook" aria-hidden="true"></i>facebook</a></li>
                        <li><a href="#" data-action="share/whatsapp/share" class="wassap-share" id="whatsapp_branch"><i class="fa fa-whatsapp" aria-hidden="true"></i>whatsapp</a></li>
                        <li><a href="#" class="line-share" id="line_branch"><img height="10" width="10" src="https://camo.githubusercontent.com/bec96d926c2bdd5200a5777245e93e4af7cec839/687474703a2f2f646c2e64726f70626f7875736572636f6e74656e742e636f6d2f732f3179617878693562737476356e35772f6c696e652e737667" data-canonical-src="http://dl.dropboxusercontent.com/s/1yaxxi5bstv5n5w/line.svg" style="max-width:100%;"> line</a></li>
                        <li><a href="#" id="twitter_branch" class="share-twit"><i class="fa fa-twitter" aria-hidden="true"></i>twitter</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- JS here -->
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
        window.open('http://m.facebook.com/sharer.php?u=' + url);
    }
</script>
<script>
    $(function(){
      // bind change event to select
      $('#dynamic_select').on('change', function () {
          var url = $(this).val(); // get selected value
          if (url) { // require a URL
              window.location = url; // redirect
          }
          return false;
      });
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
        var url_fb = "javascript:fbShare('"+ link + "', 'Fb Share', 'Facebook share popup', '{{ asset('hijabenka/desktop/img/sosmed/fb.jpg') }}', 520, 350)";
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
        var url_tw = "http://twitter.com/intent/tweet?url="+ link +"&text=Download aplikasi hijabenka dan dapatkan Benka Poin untuk berbelanja senilai IDR 25.000.";
        $('#twitter_branch').attr("href", url_tw);
    });
    
    branch.link({
    channel: 'whatsapp',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
    }, function(err, link) {
        var url_whatsapp = "whatsapp://send?text=Download aplikasi hijabenka dan dapatkan Benka Poin untuk berbelanja senilai IDR 25.000 tanpa minimum pembelian. "+ link +"";
        $('#whatsapp_branch').attr("href", url_whatsapp);
    });
    
    branch.link({
    channel: 'line',
    feature: 'referral',
    data: {
        user_id:'<?php echo $user->customer_id;?>',
        user_name: '<?php echo $user->customer_fname." ".$user->customer_lname;?>',
        user_photo:''
    }
    }, function(err, link) {
        var url_line = "http://line.me/R/msg/text/?Download aplikasi hijabenka dan dapatkan Benka Poin untuk berbelanja senilai IDR 25.000 tanpa minimum pembelian. "+ link +"";
        $('#line_branch').attr("href", url_line);
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