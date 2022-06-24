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
    <div class="error-inside error-inside-fix" style="position:static;">
    	<div class="about-wrapper tnc-wrapper b-list">
        	<h1>DAFTAR BRAND</h1>
        	<p> Berrybenka.com merasa terhormat dapat bekerjasama dengan beberapa brand berikut ini. Kami akan selalu berusaha untuk menyediakan produk fashion terkini dengan kualitas terbaik untuk kamu.</p>
            <div class="brand-list-section">
          <div class="brand-group-wrap">
            <div class="brand-group-head">Others</div>
            <ul class="brand-list">
              <li>910</li>
              <li>13thShoes</li>
              <li>1901 Jewelry</li>
              <li>3 Second</li>
              <li>3 SECOND MEN</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">A</div>
            <ul class="brand-list">
              <li>A&D</li>
              <li>A.C.C.E.P.T.</li>
              <li>Aamour</li>
              <li>Adidas</li>
              <li>Airwalk</li>
              <li>Alera</li>
              <li>Alibi Paris</li>
              <li>Alius</li>
              <li>Alive</li>
              <li>Almoda Defranco</li>
              <li>AMARILYS</li>
              <li>Amazara</li>
              <li>AMAZARA CLOTHING</li>
              <li>Anakara</li>
              <li>ANDALUSIA SLEEPWEAR</li>
              <li>Anova</li>
              <li>Aoli</li>
              <li>Arjuna Weda</li>
              <li>ART.INI</li>
              <li>Artsential</li>
              <li>Asiro</li>
              <li>Asiro Clothing</li>
              <li>Aston Fashion</li>
              <li>Austin</li>
              <li>Authentic by NoonaKu</li>
              <li>Avgal Collection</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">B</div>
            <ul class="brand-list">
              <li>B L A N C</li>
              <li>Babushka</li>
              <li>Batik Arthesian</li>
              <li>Batik Bodronoyo By Batik Semar</li>
              <li>Batik Nulaba</li>
              <li>Batik Putra Bengawan</li>
              <li>Batik Waskito</li>
              <li>Beatrice Shoes</li>
              <li>Beauty Shoes</li>
              <li>Belle Ivy</li>
              <li>Berrybenka Curve</li>
              <li>Berrybenka Label</li>
              <li>Best Bag Inc</li>
              <li>Bestoni</li>
              <li>Bettina</li>
              <li>Beyounique</li>
              <li>Bhatara Batik</li>
              <li>BIG BEAUTY</li>
              <li>BlairsBazaar</li>
              <li>Blanc Accessories</li>
              <li>Blanc Vie</li>
              <li>Bless&Bliss</li>
              <li>Boontie</li>
              <li>Bootless Ellen</li>
              <li>B-Queen</li>
              <li>Bracini</li>
              <li>Brand Revolution</li>
              <li>Bronco</li>
              <li>Bungas Bags</li>
              <li>Bunny Feet</li>
              <li>Byudele</li>
              <li>BYUDELE CLOTHING</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">C</div>
            <ul class="brand-list">
              <li>Canting Hijau</li>
              <li>CAPSLOCK CLUB</li>
              <li>Cartexblanche</li>
              <li>Carvil</li>
              <li>Cavalier</li>
              <li>CDE</li>
              <li>Cerelia Shoes</li>
              <li>Ceviro</li>
              <li>Chandelier</li>
              <li>Cheapood.OS</li>
              <li>Chic and Darling</li>
              <li>Chic.Moll</li>
              <li>Chickhorse</li>
              <li>Claire</li>
              <li>Cleson bag</li>
              <li>Cleva</li>
              <li>CLOUWNY</li>
              <li>CMD by Mirna</li>
              <li>Cocolyn</li>
              <li>Come and Wear</li>
              <li>Confidential Vintage</li>
              <li>Contempo</li>
              <li>Converse</li>
              <li>Cosmo Label</li>
              <li>Cottonology</li>
              <li>Cotty</li>
              <li>Coup Belle</li>
              <li>Crows Denim</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">D</div>
            <ul class="brand-list">
              <li>D Arcadia Footwear</li>
              <li>DASTISY</li>
              <li>DEA SHOES</li>
              <li>Delicious Shoes</li>
              <li>DEMAU</li>
              <li>Deyahomade</li>
              <li>Dhievine Batik</li>
              <li>Diadora</li>
              <li>DIARIO BASIC</li>
              <li>Diba Accessories</li>
              <li>DJOEMAT GEMBIRA</li>
              <li>Dline</li>
              <li>Dr.Kevin</li>
              <li>Dress Me</li>
              <li>DSVN</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">E</div>
            <ul class="brand-list">
              <li>Eagle Leather Goods</li>
              <li>Edberth</li>
              <li>Edberth Women</li>
              <li>EFESU</li>
              <li>ELLYSA QUEEN</li>
              <li>EM</li>
              <li>Emba Classic</li>
              <li>Emba Jeans</li>
              <li>EN-JI by Palomino</li>
              <li>Environmental Jewelry</li>
              <li>EN-ZY Men</li>
              <li>Evriz</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">F</div>
            <ul class="brand-list">
              <li>Famo</li>
              <li>FAMO MEN</li>
              <li>FAMO SPEED SUPPLY</li>
              <li>FASTER</li>
              <li>Felicite</li>
              <li>Femmineo</li>
              <li>FILA</li>
              <li>Flo Accessories</li>
              <li>Follos</li>
              <li>Freya</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">G</div>
            <ul class="brand-list">
              <li>GAFF</li>
              <li>Ganbaro</li>
              <li>GARUCCI</li>
              <li>GatsuOne</li>
              <li>GA-YE Collection</li>
              <li>Ggoodstuff</li>
              <li>Ghaia By Livi</li>
              <li>GIA</li>
              <li>Giwang</li>
              <li>Godiya</li>
              <li>Grace et Mercy</li>
              <li>Grass&Dalz</li>
              <li>Greenlight</li>
              <li>GREENLIGHT MEN</li>
              <li>GYC</li>
              <li>Gykaco</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">H</div>
            <ul class="brand-list">
              <li>H.Ashbury</li>
              <li>Hello Friday</li>
              <li>Henmate</li>
              <li>HER</li>
              <li>Hersbags</li>
              <li>HotStyle</li>
              <li>HR</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">I</div>
            <ul class="brand-list">
              <li>I WEAR 22</li>
              <li>INNARA FAIRY</li>
              <li>Inside</li>
              <li>Insiwi</li>
              <li>instyle by Suri</li>
              <li>Istafada</li>
              <li>ISVARA BATIK</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">J</div>
            <ul class="brand-list">
              <li>JanSport</li>
              <li>Jayashree Batik</li>
              <li>JEBROOMS</li>
              <li>Jening Batik</li>
              <li>JOKA JOKA SHOES</li>
              <li>Joy of Sewing</li>
              <li>Julia'r</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">K</div>
            <ul class="brand-list">
              <li>Kaleido House</li>
              <li>Kanefa</li>
              <li>Kaninna</li>
              <li>Kaoka Socks</li>
              <li>KAYI STORE</li>
              <li>Keds</li>
              <li>Khamarani Indonesia</li>
              <li>KIANDRA BATIK</li>
              <li>Kikas Project</li>
              <li>KISMIS SHOES</li>
              <li>Kklisse</li>
              <li>Kulo</li>
              <li>Kyaaa</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">L</div>
            <ul class="brand-list">
              <li>LACHRYMOSE INC</li>
              <li>Lady Alesha</li>
              <li>Laguna Seca</li>
              <li>LAVABRA CLOTHING</li>
              <li>Lazy Fruitss</li> 
              <li>LEMONE.ID</li> 
              <li>len Ross</li> 
              <li>Les Catino</li> 
              <li>Levigis</li> 
              <li>Lexxa</li> 
              <li>Lilioco</li>
              <li>Lind Italy</li>
              <li>Lissette</li>
              <li>LnC</li>
              <li>LOIS GIRL</li>
              <li>LOLLO&BRIGIDA</li>
              <li>London Berry by HUER</li>
              <li>Louvre Paris</li>
              <li>Lovadova</li>
              <li>Love To Love Accessories</li>
              <li>LUELLA</li>
              <li>Luna Etoile</li>
              <li>Lunar</li>
              <li>Lutece</li>
              <li>LUXE Boutique</li>
              <li>LYLAS</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">M</div>
            <ul class="brand-list">
              <li>Made by Jane</li>
              <li>Madeleine</li>
              <li>Madeleine Clothing</li>
              <li>Majiida</li> 
              <li>Marc & Stuart</li>
              <li>MASCOTTE</li>
              <li>Melissa</li>
              <li>MICA SHOES</li>
              <li>Miina</li> 
              <li>Minarno</li>
              <li>Minco</li>
              <li>Mireya Project</li>
              <li>Misyelle</li>
              <li>MKS</li>
              <li>MKY Bags</li>
              <li>MKY Clothing</li>
              <li>Mollinic</li>
              <li>Monomolly</li>
              <li>Moose Believer</li>
              <li>Morning Sunshine</li>
              <li>Morphidae</li>
              <li>Moutley</li>
              <li>MOUTLEY MEN</li>
              <li>Murba Scarf</li>
              <li>MXGR WEAR</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">N</div>
            <ul class="brand-list">
              <li>Naiyacalya</li>
              <li>NAKED & FAMOUS DENIM</li>
              <li>Neclove</li>
              <li>New Balance</li>
              <li>New Justine</li>
              <li>NICHOLAS EDISON</li>
              <li>Nike</li>
              <li>NOD DOCTRINE</li>
              <li>Nokha</li>
              <li>Nuber Supply</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">O</div>
            <ul class="brand-list">
              <li>Ocluise</li>
              <li>OCTAV</li>
              <li>OCTO</li>
              <li>Office Hours</li>
              <li>Oldies Company</li>
              <li>Olica</li>
              <li>Omiles</li>
              <li>ORE</li>
              <li>Origo</li>
              <li>Our Clutches</li>
              <li>OUTANDWEAR</li>
              <li>Outdoor Footwear</li>
              <li>Ownfitters</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">P</div>
            <ul class="brand-list">
              <li>Palomino</li>
              <li>PALOMINO MAN</li>
              <li>Passo</li>
              <li>Patrobas</li>
              <li>Pearluxo</li>
              <li>Peponi</li>
              <li>PHASE</li>
              <li>Phillipe Jourdan</li>
              <li>PIERO</li>
              <li>Piyaboo</li>
              <li>Playbook-id</li>
              <li>Ploose Outfit</li>
              <li>Point One</li>
              <li>Polo Regio</li>
              <li>Poshboy</li>
              <li>PPYONG</li>
              <li>Pramez</li>
              <li>Pretty Rown</li>
              <li>Prima Classe</li>
              <li>Primrose</li>
              <li>PROMESA</li>
              <li>Puppy</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Q</div>
            <ul class="brand-list">
              <li>Quincy</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">R</div>
            <ul class="brand-list">
              <li>REDOVODCA</li>
              <li>Reebok</li>
              <li>REGINA by REGINA FOOT WEAR</li>
              <li>REVAMP</li>
              <li>Revamp Accessories</li>
              <li>Richelle</li>
              <li>Ridgebake</li>
              <li>RO$EGOLD</li>
              <li>Rodeo</li>
              <li>ropasupasu</li>
              <li>Rown Division</li>
              <li>Rubylicious</li>
              <li>Russ</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">S</div>
            <ul class="brand-list">
              <li>Sakroots</li>
              <li>Salt n Pepper</li>
              <li>San Marc</li>
              <li>Seruni Batik</li>
              <li>Shoelabel</li> 
              <li>SHOEPPLE</li>
              <li>Shoes by Oletnik</li>
              <li>Shop at Banana</li>
              <li>SHOP INC</li>
              <li>Silvertote</li>
              <li>Simpliboutique</li>
              <li>Skypper</li>
              <li>Stratto</li>
              <li>Stratto Men</li>
              <li>Superga</li>
              <li>Svare</li>
              <li>SVGGEST</li>
              <li>Sweap Knitwear</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">T</div>
            <ul class="brand-list">
              <li>T For Tassel</li>
              <li>TAKATO</li>
              <li>TALITHA KUM</li>
              <li>Tam illi</li>
              <li>Tamanara</li>
              <li>Tancha</li>
              <li>Tangan Manis</li>
              <li>Tee House</li>
              <li>The Fifth</li>
              <li>The Rumors</li>
              <li>Third Day</li>
              <li>This and That Things</li>
              <li>THROOX ORIGINAL</li>
              <li>Tinamee</li>
              <li>TOTALLY</li>
              <li>TURI ACCESSOIRES</li>
              <li>TYAS WINNY</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">U</div>
            <ul class="brand-list">
              <li>UNICO</li>
              <li>Urban Looks</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">V</div>
            <ul class="brand-list">
              <li>V Ross</li>
              <li>Vans</li>
              <li>Vencedor</li>
              <li>VENTE</li>
              <li>VERZONI</li>
              <li>VEYL</li>
              <li>VEYL Pajamas</li>
              <li>Victoria</li>
              <li>VIMEMO</li>
              <li>ViVo</li>
              <li>Vivo Pajamas</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">W</div>
            <ul class="brand-list">
              <li>Wearbunch Clothing</li>
              <li>White Mode</li>
              <li>Winggo Etniq</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">X</div>
            <ul class="brand-list">
              <li>X8</li>
              <li>X8 MEN</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Y</div>
            <ul class="brand-list">
              <li>Yellow Pony</li>
              <li>Yeye Bags</li>
              <li>Yihaa Project</li>
              <li>Yoenik</li>
              <li>Yongki Komaladi</li>
              <li>Your Hands</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Z</div>
            <ul class="brand-list">
              <li>Zahra Signature</li>
              <li>ZANETA</li>
            </ul>
          </div>
        </div>
        <p>
          Jika kamu memiliki brand fashion dan ingin mengembangkan brand kamu bersama Berrybenka, silakan menuju ke halaman <a href="https://berrybenka.com/home/featured_brand">Daftakan Brand Anda</a>.</p>
        </div>
    </div>
    </div>
</div>

@endsection



