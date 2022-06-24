<?php 
$domains = get_domain();
$domain = $domains['domain_name'];
?>
@extends("layouts.$domain.mobile.main")

@section('css')
<link rel="stylesheet" href="{{ asset("$domain/mobile/css/home.css") }}">
@endsection

@section('content')
<!--TNC-->
<style>
.tnc{padding:20px;text-align:center;}
.tnc h1{text-align:center;display:inline-block;border-bottom:1px solid #000;margin:20px auto;font-family:'futura',serif;padding:0 5px 5px 5px;font-size:1.5em;}
.tnc p{line-height:1.5;text-align:left;margin:10px 0;letter-spacing:1px;font-family:'futura',serif;color:#555;}
.tnc ol{padding-left:20px;}
.tnc ol li,.tnc ol li ol li{text-align:left;list-style:decimal;font-family:'futura',serif;color:#555;letter-spacing:1px;line-height:1.5;}
.tnc ol li h2{margin:0;font-family:'futura',serif;text-transform:capitalize;letter-spacing:1px;}
</style>
<div class="content-home">
<div class="static-content">
    <div class="tnc">
        <h1>PEMBAYARAN DENGAN KREDIVO</h1>
        <ol>
            <li>
                <a>Apa itu Kredivo?</a>
                <div>Kredivo adalah solusi kredit instan yang memberikan kamu kemudahan untuk beli sekarang dan bayar nanti dalam 30 hari tanpa bunga atau dengan Cicilan 3 bulan, 6 bulan, atau 12 bulan (bunga 2.95% per bulan dan uang muka 20%).</div>
            </li>
            <li>
                <a>Bagaimana cara mendaftar di Kredivo?</a>
                <div>Ada 2 cara untuk mendaftar di Kredivo, melalui aplikasi kredivo di <a href="https://play.google.com/store/apps/details?id=com.finaccel.android&amp;hl=id" target="_blank">Google Play Store</a> atau melalui website kami di <a href="https://app.kredivo.com" target="_blank">app.kredivo.com</a></div>
            </li>
            <li>
                <a>Apa syarat untuk memiliki akun Kredivo?</a>
                <div>Untuk mendaftar Kredivo, kamu harus: <br />
                    Berstatus Warga Negara Indonesia (WNI)<br />
                    Berusia antara 18 sampai 60 tahun<br />
                    Berdomisili di Jabodetabek, Surabaya, Medan, Kota Bandung, Semarang, Palembang, atau Denpasar<br />
                    Berpenghasilan minimal 3 juta Rupiah per bulan
                </div>
            </li>
            <li>
                <a>Apakah Kredivo tersedia di seluruh kota di Indonesia?</a>
                <div>
                    Saat ini Kredivo hanya bisa digunakan oleh pengguna yang berdomisili di Jabodetabek, Surabaya, Medan, Kota Bandung, Semarang, Palembang, dan Denpasar. Kami akan segera hadir di kota-kota lain dalam waktu dekat.
                </div>
            </li>
            <li>
                <a>Dokumen apa saja yang diperlukan untuk memiliki akun Kredivo?</a>
                <div>kamu harus mengunggah dokumen-dokumen berikut ini:
                    <ol>
                        <li>Kartu identitas (KTP)</li>
                        <li>Dua bukti tempat tinggal (Kartu Keluarga/STNK/tagihan air/tagihan listrik/tagihan TV kabel/tagihan kartu kredit/tagihan telpon). Mau lebih cepat? Sambungkan 2 akun digital kamu</li>
                        <li>Dua bukti penghasilan.
                            <ul>
                                <li>Apabila kamu bekerja (penuh/paruh waktu) maka kamu perlu mengunggah slip gaji dan mutasi rekening 2 bulan terakhir</li>
                                <li>Apabila kamu bekerja sebagai wirausahawan maka kamu perlu mengunggah dua di antara tiga dokumen berikut, yaitu rekening koran, bukti potong pajak, dan mutasi rekening 2 bulan terakhir</li>
                            </ul>
                            Mau lebih cepat? Sambungkan <em>internet banking</em> kamu</li>
                    </ol>
                </div>
            </li>
            <li>
                <a>Apakah ada bunga atau biaya yang terkait dengan pinjaman Kredivo?</a>
                <div>
                    Apabila kamu memilih pembayaran dalam 30 hari maka kamu tidak akan dikenakan bunga selama kamu melunasi pembayaran kamu dalam 30 hari
                    <br>
                    <br> Apabila kamu memilih pembayaran dengan cicilan, maka kamu akan dikenakan bunga sebesar 2.95% per bulannya.
                    <br>
                    <br> Apabila kamu terlambat membayar untuk pembayaran apapun ke Kredivo, akan ada denda keterlambatan sebesar 3% dan bunga sebesar 2,95% per bulannya.
                </div>
            </li>
            <li>
                <a>Berapa batas kredit yang diberikan Kredivo?</a>
                <div>kamu bisa mendapatkan batas kredit hingga Rp 1.500.000 dengan mengirimkan KTP dan menyambungkan 1 (satu) akun digital, dan batas kredit hingga Rp 3.000.000 dengan mengirimkan KTP dan menyambungkan 2 (dua) akun digital.
                    <br>
                    <br> Kamu bisa membeli barang dengan cicilan dengan harga minimal Rp 1.500.000 hingga maksimal Rp 20.000.000 dengan menyambungkan 2 (dua) akun digital (atau menyertakan 2 (dua) bukti tempat tinggal) dan menyambungkan internet banking kamu (atau 2 (dua) bukti penghasilan). Kamu bisa melihat batas transaksi cicilan yang dihitung secara terpisah di dalam aplikasi Android atau <a href="https://app.kredivo.com/" target="_blank">web app</a> Kredivo.
                </div>
            </li>
            <li>
                <a>Apakah saya bisa menaikkan batas kredit saya?</a>
                <div>Pilihan batas kredit yang ingin kamu ajukan harus kamu pilih pada saat pendaftaran. kamu tidak dapat menaikkan batas kredit kamu setelah aplikasi dikirim.</div>
            </li>
            <li>
                <a>Siapakah BFI Finance dan bagaimana cara berhubungan dengan Kredivo?</a>
                <div>PT BFI Finance Indonesia Tbk adalah perusahaan keuangan yang berbasis di Indonesia dan pemberi pinjaman untuk semua pinjaman Kredivo.</div>
            </li>
            <li>
                <a>Apakah informasi pribadi saya aman dengan Kredivo?</a>
                <div>Ya, melindungi informasi pribadi kamu adalah prioritas utama kami. Kami mengenkripsi semua data sensitif. Kami juga memberikan pengamanan fisik, elektronik, dan prosedural untuk melindungi informasi kamu. Kami tidak menjual atau menyewakan informasi kamu kepada siapapun kecuali dibutuhkan oleh BFI Finance untuk mengeluarkan pinjaman kamu atau diperlukan untuk diberikan kepada regulator.</div>
            </li>				                    
        </ol>
    </div>

</div>
</div>

@endsection



