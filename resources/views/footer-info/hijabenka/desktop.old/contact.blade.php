<?php
  $domains = get_domain();
  $domain = $domains['domain_name'];
?>
@extends("layouts.$domain.desktop.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/error.css") }}">
<link rel="stylesheet" href="{{ asset("$domain/desktop/css/about.css") }}">
<style>
.contact-list{display:flex;padding:10px;border-radius:4px;box-sizing:border-box;border:1px solid #ccc;height:85px;align-items:center;font-size:12px;justify-content:center;}
.contact-list i{font-size:28px;margin-right:15px;}
.contact-list p{margin:0;text-align:left;line-height:1;margin:0 0 5px 10px;width:auto;font-size:14px;}
.contact-list strong{font-size:16px;margin-bottom:5px;display:block;}
.contact-list br{display:block;height:0;margin:0;padding:0;}
.about-wrapper ul{display:flex;flex-wrap:wrap;justify-content:center;}
.about-wrapper ul li{width:30%;padding:10px;}
.social-contact{display:flex;flex-wrap:nowrap;}
.social-contact a{background:none;padding:5px;}
.social-contact a i{color:#999;font-size:16px;margin:0;}
.social-item{min-width:50%;}
.social-item p{margin:0 0 0 5px;}
.line-icon{width:15px;}
.line-icon img{padding:0;margin:0;}
.contact-title h1::affter{border:none;}
</style>
@endsection

@section('content')

<div class="error-content thx-wrapper">
  <div class="error-overlay">
    <div class="error-inside">
      <div class="about-wrapper contact-title">
        <h1>Bagaimana Kami Membantu Anda?</h1>
        <ul>
          <?php /*
          <li>
            <div class="contact-list">
              <i class="fa fa-phone" aria-hidden="true"></i>
              <p><strong>Telepon Kami</strong><br>021-2520555<br>Senin - Jumat (08.00 - 17.00)
            </div>
          </li>
          */?>
          <li>
            <div class="contact-list">
              <i class="fa fa-mobile" aria-hidden="true"></i>
              <p style="margin-top:5px;"><strong>SMS Kami</strong><br> 0812 8880 9992<br> Senin - Jumat (09.00 - 18.00) <br> Sabtu - Minggu (08.00 - 17.00)
              </p>
            </div>
          </li>
          <li>
            <div class="contact-list social-ctc-wrapper">
              <i class="fa fa-users" aria-hidden="true"></i>
              <div class="social-item">
                <p><strong>Social Media</strong><br>
                <div class="social-contact" style="margin-top:-15px;">
                  <a href="https://www.facebook.com/BerrybenkaShop/"><i class="fa fa-facebook" aria-hidden="true"></i></a>                        
                  <a href="https://twitter.com/Berrybenka"><i class="fa fa-twitter" aria-hidden="true"></i></a>
                  <a href="https://www.instagram.com/berrybenka/"><i class="fa fa-instagram" aria-hidden="true"></i></a>
                  <a href="#"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                  <a href="#" class="line-icon"><img src="{{ asset("$domain/desktop/img/line-icon.gif") }}" alt=""></a>
                </div>
                </p>
                <p>
                  Senin - Jumat (09.00 - 18.00) <br> Sabtu - Minggu (08.00 - 17.00)
                </p>
              </div>
            </div>
          </li>
          <li>
            <div class="contact-list">
              <i class="fa fa-envelope" aria-hidden="true"></i>
              <p><strong>Email Kami</strong><br>cs@berrybenka.com<br> Senin - Jumat (09.00 - 18.00) <br> Sabtu - Minggu (08.00 - 17.00)
              </p>
            </div>
          </li>
          <li>
            <div class="contact-list">
              <i class="fa fa-comments" aria-hidden="true"></i>
              <p><strong>Live Chat</strong><br> Senin - Jumat (08.00 - 20.00) <br> Sabtu - Minggu (08.00 - 17.00) 
              </p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

@endsection



