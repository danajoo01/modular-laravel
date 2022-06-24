<?php $time = microtime(true); 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
@endsection

@section('content')

<div class="error-content thx-wrapper">
	<div class="error-overlay">
        <div class="error-inside error-inside-fix ref-wrap" style="position:static;">
            <div class="about-wrapper affi-wrapper">
            	<div class="sixteen columns affiliate-header">
                    <a class="affi-head-img" target="_blank" href="https://berrybenka.hasoffers.com/login">
                        <img src="/berrybenka/desktop/img/affiliate/banner.jpg">
                    </a>
                    <p>BERRYBENKA affiliate program adalah program partnership yang menguntungkan untuk para pemilik situs melalui link afiliasi . Tampilkan link/banner BERRYBENKA.com pada website atau blog Anda dan bersiaplah mendapatkan komisi dari setiap transaksi yang Anda referensikan.</p>
                    <div class="login-sign">
                        <a href="https://berrybenka.hasoffers.com/login">LOGIN</a>
                        <a href="http://berrybenka.hasoffers.com/signup">SIGN UP</a>
                    </div>
                </div>
                <div class="affiliate-content">
                    <h1>HOW IT WORKS</h1>
                    <ul>
                        <li><img src="/berrybenka/desktop/img/affiliate/list1.gif"><p>Daftar menjadi partner affiliate BERRYBENKA</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/list2.gif"><p>Partner menampilkan banner/link berrybenka.com</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/list3.gif"><p>Pengunjung website/blog partner meng-klik banner/link berrybenka.com</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/list4.gif"><p>Partner mendapat komisi  (IDR) dari transaksi pengunjung</p></li>
                    </ul>
                </div>
                <div class="half-page-wrapper">
                    <div class="half-page clearit">
                        <div class="half-page-left">
                            <div class="hp-item">
                            	<img src="/berrybenka/desktop/img/affiliate/commission-icon.gif">
                                <h1>COMMISSION</h1>
                                <p>Kami memberikan komisi setiap bulannya, dengan besar komisi 10% untuk setiap transaksi yang direferensikan dari link afiliasi BERRYBENKA.com yang ada pada website atau blog Anda.</p>
                            </div>
                            <div class="hp-item">
                            	<img src="/berrybenka/desktop/img/affiliate/report.gif">
                                <h1>REPORT</h1>
                                <p>Anda dapat dengan mudah mengakses laporan pendapatan, besar komisi yang sudah Anda dapatkan, dan laporan lainnya dalam panel affiliate yang kami sediakan.</p>
                            </div>
                            <div class="hp-item">
                            	<img src="/berrybenka/desktop/img/affiliate/support.gif">
                                <h1>SUPPORT</h1>
                                <p>Anda dapat menghubungi kami  untuk pertanyaan, kritik, saran, dan berbagai informasi  tentang BERRYBENKA.com affiliate program, melalui form disamping ini. </p>
                            </div>
                            <div class="hp-item">
                            	<img style="margin:0 20px 50px 20px;" src="/berrybenka/desktop/img/affiliate/networking-group.png">
                                <h1>REFERRAL</h1>
                                <p>Mau dapat komisi lebih banyak dari affiliate Berrybenka?</p>
                                <p>Ajak orang-orang terdekat Anda untuk mendaftar jadi partner affiliate Berrybenka, dan dapatkan tambahan komisi 10%. Tahu lebih banyak, <a href="http://berrybenka.com/affiliate/referral"><strong><u>klik disini</u></strong></a></p>
                            </div>
                        </div>
                        <div class="affiliate-form">
                            <span class="form-wrapper">
                                <p>Jika ada pertanyaan silahkan hubungi kami melalui email ke <a href="emailto:affiliate@berrybenka.com"><strong>affiliate@berrybenka.com</strong></a> atau silahkan isi form dibawah ini.</p>
                                <form method="post" action="">
                                    <div class="nama">
                                        <input type="text" required="" value="" placeholder="Nama" title="Hanya huruf yang diperbolehkan" pattern="[a-zA-Z ]+" name="name">
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label>Nama</label>
                                    </div>
                                    <div class="nama">
                                        <input type="email" value="" placeholder="email" required="" name="guest_email">
                                        <span class="highlight"></span>
                                        <span class="bar"></span>
                                        <label>Email</label>
                                    </div>
                                    <textarea value="" placeholder="Pesan" required="" style="resize:vertical;height:170px;" name="message"></textarea>
                                    <div class="nama">
                                        <div data-sitekey="6Lf-shwTAAAAAIXgGA2LKTrMMKNM64flCSkWzzLj" class="g-recaptcha"><div style="width: 304px; height: 78px;"><div><iframe width="304" height="78" frameborder="0" src="https://www.google.com/recaptcha/api2/anchor?k=6Lf-shwTAAAAAIXgGA2LKTrMMKNM64flCSkWzzLj&amp;co=aHR0cDovL2JlcnJ5YmVua2EuY29tOjgw&amp;hl=en&amp;v=r20160830132105&amp;size=normal&amp;cb=vr9ko3dhbhek" title="recaptcha widget" role="presentation" scrolling="no" name="undefined"></iframe></div><textarea style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px; padding: 0px; resize: none;  display: none; " class="g-recaptcha-response" name="g-recaptcha-response" id="g-recaptcha-response"></textarea></div></div>
                                    </div>
                                    <input type="submit" value="Kirim">
                                </form>
                            </span>
                        </div>
                    </div>
                </div>
                <div style="margin:0;" class="affiliate-content affiliate-content2">
                    <h1>MENGAPA MEMILIH AFFILIATE BERRYBENKA ?</h1>
                    <ul>
                        <li><img src="/berrybenka/desktop/img/affiliate/free.gif"><p>GRATIS<br>dan siapapun bisa bergabung</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/sale.gif"><p>BERRYBENKA <br>menjual lebih dari 1000 brand lokal dan internasional</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/list44.gif"><p>Komisi <br><strong>10%</strong><br> Untuk setiap transaksi</p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/networking-group.png"><p style="font-size:14px;">Tambahan komisi dengan<br><strong style="font-size:16px;">Affiliate Referral</strong></p></li>
                        <li><img src="/berrybenka/desktop/img/affiliate/timsupport.gif"><p><strong style="font-size:14px;line-height:1.2;">Realtime Report &amp; Tim Support</strong><br> yang siap membantu</p></li>
                    </ul>
                </div>
                <a class="sign-log" href="https://berrybenka.hasoffers.com/signup">daftar sekarang</a>
                <p class="reg-text">Atau belum menjadi member BERRYBENKA.com, registrasi <a href="https://berrybenka.com/customer/account/auth"><strong>disini</strong></a> dan dapatkan voucher belanja</p>
                <div class="keterangan">
                    <ul>
                        <li><a href="https://blogaffiliateberrybenka.wordpress.com/">Blog</a></li>
                        <li>|</li>
                        <li><a href="http://berrybenka.com/affiliate/faq">FAQ</a></li>
                        <li>|</li>
                        <li><a href="http://berrybenka.com/affiliate/illustration">Ilustrasi Kerja</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



