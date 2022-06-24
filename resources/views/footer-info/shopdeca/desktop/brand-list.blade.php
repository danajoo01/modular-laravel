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
        	<h1>BRAND LIST</h1>
        	<div class="brand-list-section">
          <div class="brand-group-wrap">
            <div class="brand-group-head">A</div>
            <ul class="brand-list">
              <li>Adidas</li>
              <li>Alice’s Place</li>
              <li>Audio Technica</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">B</div>
            <ul class="brand-list">
              <li>Bellroy</li>
              <li>Beranda</li>
              <li>Bluelounge</li>
              <li>Bodytalk</li>
              <li>BWGH</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">C</div>
            <ul class="brand-list">
              <li>Canvas Living</li>
              <li>Cheap Monday</li>
              <li>Cheveux</li>
              <li>Converse</li>
              <li>Coppelia</li>
              <li>Cortica</li>
              <li>Cote and Ciel</li>
              <li>COUCOU CO</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">D</div>
            <ul class="brand-list">
              <li>Daniel Wellington</li>
              <li>Desa Boneka</li>
              <li>Diadora</li>
              <li>Divoom</li>
              <li>DOIY</li>
              <li>DOUJ</li>
              <li>Duft and Chandelle</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">E</div>
            <ul class="brand-list">
              <li>EASTPAK</li>
              <li>Esgotado</li>
              <li>ETSA</li>
              <li>Eucalie</li>
              <li>Eye Candle</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">F</div>
            <ul class="brand-list">
              <li>Fashionary</li>
              <li>FITBIT</li>
              <li>FitFlop</li>
              <li>FjaalRaven</li>
              <li>Flat Out of Heels</li>
              <li>Foldaway</li>
              <li>Franc Nobel</li>
              <li>Fred and Friends</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">G</div>
            <ul class="brand-list">
              <li>Glush</li>
              <li>Griffin</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">H</div>
            <ul class="brand-list">
              <li>Halvey</li>
              <li>Happy Socks</li>
              <li>Harman/Kardon</li>
              <li>Heimlo Studio</li>
              <li>Hellolulu</li>
              <li>Herschel</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">I</div>
            <ul class="brand-list">
              <li>I’m in</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">J</div>
            <ul class="brand-list">
              <li>Jabra</li>
              <li>Jaybird</li>
              <li>JBL</li>
              <li>Joseph & Co</li>
              <li>June</li>
              <li>Just Mobile</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">K</div>
            <ul class="brand-list">
              <li>Kenneth Cole</li>
              <li>Kikkerland</li>
              <li>Kinto</li>
              <li>Komono</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">L</div>
            <ul class="brand-list">
              <li>La Perle</li>
              <li>Le Specs</li>
              <li>Leef</li>
              <li>Lifeproof</li>
              <li>Lilzebra</li> 
              <li>Little Big</li> 
              <li>Lust Tres</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">M</div>
            <ul class="brand-list">
              <li>Made</li>
              <li>Magnifico</li>
              <li>Manikan</li>
              <li>Marshall</li> 
              <li>Matador</li>
              <li>Matahari</li>
              <li>Miku</li>
              <li>Mimesa</li>
              <li>Minkpink</li> 
              <li>Minkpink Sun Glasses</li>
              <li>Mischa</li>
              <li>Moleskine</li>
              <li>Monday to Sunday</li>
              <li>Moshi</li>
              <li>Movie Poster</li>
              <li>Mujjo</li>
              <li>Mustard</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">N</div>
            <ul class="brand-list">
              <li>Naked & Famous Denim</li>
              <li>Nanette</li>
              <li>Nike</li>
              <li>Nixon</li>
              <li>North Star</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">O</div>
            <ul class="brand-list">
              <li>OAKSVA</li>
              <li>One Less</li>
              <li>Onel</li>
              <li>Orbitkey</li>
              <li>Organic Supply Co</li>
              <li>Otterbox</li>
              <li>Ozaki</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">P</div>
            <ul class="brand-list">
              <li>Pantone</li>
              <li>Parkland</li>
              <li>Paul Frank</li>
              <li>Pop-Pilot</li>
              <li>Power</li>
              <li>Publish</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Q</div>
            <ul class="brand-list">
              <li>Quill & Fox</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">R</div>
            <ul class="brand-list">
              <li>Rastaclat</li>
              <li>Reebok</li>
              <li>RI By CARRIE</li>
              <li>Ridgebake</li>
              <li>Rivieras</li>
              <li>Ruang Rusa</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">S</div>
            <ul class="brand-list">
              <li>SAHLGOODS</li>
              <li>Saint Peter</li>
              <li>Seenheiser</li>
              <li>Shadowplaynyc</li>
              <li>Sinau Socks</li> 
              <li>Sister Margaritta</li>
              <li>Sphero</li>
              <li>SPIGEN</li>
              <li>Square</li>
              <li>Stella Rissa</li>
              <li>Sunday Somewhere</li>
              <li>Sunpocket</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">T</div>
            <ul class="brand-list">
              <li>Taylor Fine Goods</li>
              <li>The Art Recipe</li>
              <li>The Jacks</li>
              <li>TIMBUK2</li>
              <li>TKEES</li>
              <li>TOIMOI</li>
              <li>Tre Studio</li>
              <li>Tropical Potion</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">U</div>
            <ul class="brand-list">
              <li>Ubersuave</li>
              <li>Uddo</li>
              <li>Urban Armor Gear</li>
              <li>Urbanears</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">V</div>
            <ul class="brand-list">
              <li>Vertique</li>
              <li>Vest</li>
              <li>Vimala</li>
              <li>Vionic</li>
              <li>VIVERE</li>
              <li>VOID</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">W</div>
            <ul class="brand-list">
              <li>WESC</li>
              <li>Wooden Heart</li>
              <li>Written in the Stars Co</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Y</div>
            <ul class="brand-list">
              <li>Your Hands</li>
              <li>Yuna </li>
            </ul>
          </div>
        </div>
    </div>
    </div>
</div>
</div>

@endsection



