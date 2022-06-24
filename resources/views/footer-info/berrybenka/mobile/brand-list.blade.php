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
    <div class="error-inside">
    	<div class="about-wrapper tnc-wrapper b-list">
        	<h1>DAFTAR BRAND</h1>
        	<p> Berrybenka.com merasa terhormat dapat bekerjasama dengan beberapa brand berikut ini. Kami akan selalu berusaha untuk menyediakan produk fashion terkini dengan kualitas terbaik untuk kamu.</p>
            <div class="brand-list-section">
          <div class="brand-group-wrap">
            <div class="brand-group-head">1</div>
            <ul class="brand-list">
              <li>13thShoes</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">A</div>
            <ul class="brand-list">
              <li>Accessarie</li>
              <li>Alive</li>
              <li>Archery</li>
              <li>Areta</li>
              <li>Amary</li>
              <li>Ancient greek</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">B</div>
            <ul class="brand-list">
              <li>Bambina Closet</li>
              <li>Batikaholic</li>
              <li>Bee</li>
              <li>BIEN</li>
              <li>BIJOUX-BIJOUX</li>
              <li>BINCA</li>
              <li>Blaize</li>
              <li>Blanc Vie</li>
              <li>Blithe</li>
              <li>Bloom</li>
              <li>BlowPOP</li>
              <li>Brick</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">C</div>
            <ul class="brand-list">
              <li>Chickhorse</li>
              <li>Choncita</li>
              <li>CLEANCUT</li>
              <li>Cloth Inc</li>
              <li>CLYNS</li>
              <li>Coppelia</li>
              <li>Country Chic</li>
              <li>Cucito</li>
              <li>Cut A dash</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">D</div>
            <ul class="brand-list">
              <li>Devotee Co</li>
              <li>Dhievine</li>
              <li>Djody</li>
              <li>DNC Shoes</li>
              <li>Dutzie</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">E</div>
            <ul class="brand-list">
              <li>Ells Closet</li>
              <li>Estigi</li>
              <li>Elf clothing</li>
              <li>Ethniq couture</li>
              <li>Etique</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">F</div>
            <ul class="brand-list">
              <li>FASH</li>
              <li>Fleur</li>
              <li>Faustkura</li>
              <li>FHAB by Bonjep</li>
              <li>Five 13</li>
              <li>Fleur de Vyn</li>
              <li>Fashionistas</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">G</div>
            <ul class="brand-list">
              <li>Grace et Mercy</li>
              <li>Grey Area</li>
              <li>Gigi Estra</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">I</div>
            <ul class="brand-list">
              <li>Heirloom/li&gt;</li>
              <li>House of Emma</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">I</div>
            <ul class="brand-list">
              <li>Ibun</li>
              <li>Ignottia</li>
              <li>In her shoe</li>
              <li>Insiwi</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">J</div>
            <ul class="brand-list">
              <li>Judittie</li>
              <li>Junkiee Shoes</li>
              <li>Just Eyi</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">K</div>
            <ul class="brand-list">
              <li>Kanya</li>
              <li>Kklisse</li>
              <li>Kimi</li>
              <li>Kitty Kitz</li>
              <li>Kivee</li>
              <li>Konokeneproject</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">L</div>
            <ul class="brand-list">
              <li>Labdagatic</li>
              <li>LaFemme</li>
              <li>Le Mille</li>
              <li>Le plus</li>
              <li>Leonyevelyn</li> 
              <li>Liqueur</li> 
              <li>Little Closet ID</li> 
              <li>Look Boutique</li> 
              <li>Loveliness</li> 
              <li>Lush</li> 
              <li>Look At</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">M</div>
            <ul class="brand-list">
              <li>Mako &amp; Toshi</li>
              <li>Me Debut</li>
              <li>Mercivelle</li>
              <li>Morrine</li> 
              <li>Mosato</li>
              <li>M.Y.L</li>
              <li>Macaroons Closet</li>
              <li>Maka</li>
              <li>Maran</li> 
              <li>Misty Fox</li>
              <li>Mineola</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">N</div>
            <ul class="brand-list">
              <li>Namas Karra</li>
              <li>NU design</li>
              <li>Naeva</li>
              <li>Nefertiti</li>
              <li>NOE</li>
              <li>Norlive</li>
              <li>NSC</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">O</div>
            <ul class="brand-list">
              <li>OR project</li>
              <li>Ouwell wardrobe</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">P</div>
            <ul class="brand-list">
              <li>Pat a cake</li>
              <li>PEEK</li>
              <li>Playlust</li>
              <li>Polkadot</li>
              <li>Ponytale</li>
              <li>Pure look</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">R</div>
            <ul class="brand-list">
              <li>Ramune</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">S</div>
            <ul class="brand-list">
              <li>Sartorial Sweet</li>
              <li>Scarlet</li>
              <li>Screenshot</li>
              <li>Silhouette</li>
              <li>Sophistix</li> 
              <li>Spotlight</li>
              <li>STAS</li>
              <li>Stratto</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">T</div>
            <ul class="brand-list">
              <li>Teabag</li>
              <li>TGIFashion</li>
              <li>This is April</li>
              <li>Tinamee</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">V</div>
            <ul class="brand-list">
              <li>Vobee</li>
              <li>Vogue Premiere</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">W</div>
            <ul class="brand-list">
              <li>Wardrobes Project</li>
              <li>Wearbunch</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Z</div>
            <ul class="brand-list">
              <li>Zavica</li>
            </ul>
          </div>
        </div>
        <p>
          Jika kamu memiliki brand fashion dan ingin mengembangkan brand kamu bersama Berrybenka, silakan menuju ke halaman Daftakan Brand Anda.        </p>
        </div>
    </div>
    </div>
</div>

@endsection



