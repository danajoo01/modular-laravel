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
    <div class="error-inside error-inside-fix">
    	<div class="about-wrapper tnc-wrapper b-list">
        	<h1>DAFTAR BRAND</h1>
        	<p> Hijabenka.com merasa terhormat dapat bekerjasama dengan beberapa brand berikut ini. Kami akan selalu berusaha untuk menyediakan produk fashion terkini dengan kualitas terbaik untuk kamu.</p>
          <div class="brand-list-section">
          <div class="brand-group-wrap">
            <div class="brand-group-head">A</div>
            <ul class="brand-list">
              <li>Acheter</li>
              <li>ADEM</li>
              <li>AESTIC</li>
              <li>Aira Muslim Butik</li>
              <li>Akar</li>
              <li>ALARICE</li>
              <li>ALDIVVA</li>
              <li>ALEYDA HIJAB</li>
              <li>Almahyra Scarf</li>
              <li>Alodie Scarf</li>
              <li>ALRYASYA</li>
              <li>AM by Anisa Maulani</li>
              <li>AMAZARA MOSLEM WEAR</li>
              <li>Ameena</li>
              <li>AMITY</li>
              <li>Ammara</li>
              <li>ANDJANI</li>
              <li>Angel Lelga Scarf by Itang Yunasz</li>
              <li>Antina Hijab</li>
              <li>ARTENESIA</li>
              <li>Atrisudji</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">B</div>
            <ul class="brand-list">
              <li>BARLOW</li>
              <li>BLUE PEONY</li>
              <li>BUTTONSCARVES</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">C</div>
            <ul class="brand-list">
              <li>CAMMEO HIJAB</li>
              <li>Carramalia</li>
              <li>CEANNA</li>
              <li>Cendri</li>
              <li>COTTON BEE</li>
              <li>Covering Story</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">D</div>
            <ul class="brand-list">
              <li>Dafanya</li>
              <li>Damawa</li>
              <li>Damekrone</li>
              <li>DEJILBAB HIJAB SEHAT</li>
              <li>Delarosa for Hijabenka</li>
              <li>DIARIO</li>
              <li>DIBA SCARVES</li>
              <li>Diindrihijab</li>
              <li>DMD APPAREL</li> 
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">E</div>
            <ul class="brand-list">
              <li>E.Look</li>
              <li>EDORA SPORTSWEAR</li>
              <li>eriaoriza</li>
              <li>EVE by House of Amee</li>
              <li>Exobrooch</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">F</div>
            <ul class="brand-list">
              <li>FANCY HER</li>
              <li>FEIRISHA</li>
              <li>FELICITE HIJAB COLLECTION</li>
              <li>Femme by Caramela</li>
              <li>FENNY SAPTALIA</li>
              <li>FEY MODEST WEAR</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">G</div>
            <ul class="brand-list">
              <li>GELEE HIJAB</li>
              <li>GIGGLES</li>
              <li>Giyanthi by RZ</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">H</div>
            <ul class="brand-list">
              <li>Hanna Hijab</li>
              <li>Havva</li>
              <li>HEAVEN LIGHTS</li>
              <li>Hercasual</li>
              <li>Hijab Ellysa</li>
              <li>Hijab Valley</li>
              <li>HIJABLOOKS</li>
              <li>House of Amee</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">I</div>
            <ul class="brand-list">
              <li>I Wear Fleur</li>
              <li>Imperial</li>
              <li>INDIJ</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">J</div>
            <ul class="brand-list">
              <li>JANE'S HIJAB COLLECTION</li>
              <li>JANNAH BY WINA ANNE</li>
              <li>JV HASANAH</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">K</div>
            <ul class="brand-list">
              <li>Kalana Scarves</li>
              <li>kameamuslimah</li>
              <li>Kami Idea</li>
              <li>Kaoka Muslimah</li>
              <li>KAYNAY</li>
              <li>Kayra</li>
              <li>KAYVA</li>
              <li>KDEEZAA</li>
              <li>Kitnac Art</li>
              <li>Kklisse Moslem Wear</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">L</div>
            <ul class="brand-list">
              <li>LAFF HIJAB</li>
              <li>Lamak</li>
              <li>LiaDyLy</li>
              <li>lindaanggrea</li>
              <li>Luulu Scarf</li> 
              <li>LUVHIJAB</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">M</div>
            <ul class="brand-list">
              <li>M99</li>
              <li>MAECRIUZ</li>
              <li>MAGIC SPELL</li>
              <li>MAIN</li> 
              <li>Maja Indonesia</li>
              <li>MALAIKA</li>
              <li>MAMIGAYA</li>
              <li>MARS INDONESIA</li>
              <li>MAXIMA</li> 
              <li>Meccanism</li>
              <li>Minco Hijab</li>
              <li>Miss Sissy</li>
              <li>MOMIMA HIJAB</li>
              <li>Moss Style</li>
              <li>Mukena Aisyah</li>
              <li>MUKENA KHADEEJAH</li>
              <li>MYBAMUS</li>
              <li>Mycca Indonesia</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">N</div>
            <ul class="brand-list">
              <li>Naela Basic</li>
              <li>NAOMI</li>
              <li>NATHIJAB</li>
              <li>NAURA.</li>
              <li>NAWASANA</li>
              <li>NAYRINZ</li>
              <li>NRH X Nabilia</li>
              <li>NUMAA BY SISIE</li>
              <li>NYONYA NURSING WEAR</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">O</div>
            <ul class="brand-list">
              <li>ORL By Adara Design</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">P</div>
            <ul class="brand-list">
              <li>Paradise</li>
              <li>Play Hijab</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">R</div>
            <ul class="brand-list">
              <li>R & B by Ra</li>
              <li>RA by Restu Anggraini</li>
              <li>Radwah</li>
              <li>Rahina</li>
              <li>RauzaRauza</li>
              <li>Reimitta</li>
              <li>RVMORS BOUTIQUE</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">S</div>
            <ul class="brand-list">
              <li>Shinta Dewi</li>
              <li>Shop at Banana Hijab</li>
              <li>SIERRA by tyas winny</li>
              <li>SILMAA</li>
              <li>SIMPLE BASIC</li> 
              <li>SOKA</li>
              <li>SUNSHINE</li>
              <li>SYAHEERA</li>
              <li>SYAHIRAH</li>  
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">T</div>
            <ul class="brand-list">
              <li>Tatuis</li>
              <li>TFT</li>
              <li>Thalassa Store</li>
              <li>TOP DIM</li>
              <li>TULIP HIJAB</li>
              <li>TWENTYSIX HIJAB</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">U</div>
            <ul class="brand-list">
              <li>UMA INDONESIA</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">V</div>
            <ul class="brand-list">
              <li>Vervessa</li>
              <li>VIEN</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">X</div>
            <ul class="brand-list">
              <li>XQ Moslem Wear</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Y</div>
            <ul class="brand-list">
              <li>YARNS</li>
              <li>YIHAA</li>
            </ul>
          </div>
          <div class="brand-group-wrap">
            <div class="brand-group-head">Z</div>
            <ul class="brand-list">
              <li>Zaco Hijab</li>
              <li>Zamora</li>
              <li>ZAREEFA</li>
              <li>ZAWD Hijab</li>
            </ul>
          </div>
        </div>
        </div>
        <p>
          Jika kamu memiliki brand fashion dan ingin mengembangkan brand kamu bersama Hijabenka, silakan menuju ke halaman <a href="https://hijabenka.com/home/featured_brand">Daftakan Brand Anda</a>.</p>
        </div>
    </div>
    </div>
</div>

@endsection



