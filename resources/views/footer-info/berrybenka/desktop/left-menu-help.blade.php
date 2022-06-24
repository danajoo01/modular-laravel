<ul class="sidebar-list">
    <li {{ $page == "cod" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/cod"><i class="fa fa-question-circle"></i>Bayar di tempat</a></li>
    <li {{ $page == "faq" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/faq"><i class="fa fa-question-circle"></i>Pertanyaan Umum</a></li>
    <li {{ $page == "how_to_order" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/how_to_order"><i class="fa fa-shopping-cart"></i>Cara Pemesanan</a></li>
    <li {{ $page == "help_return" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/help_return"><i class="fa fa-puzzle-piece"></i>Ketentuan Pengembalian</a></li>
    <li {{ $page == "help_return_watch" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/help_return_watch"><i class="fa fa-compass"></i>Ketentuan Pengembalian Produk Jam Tangan</a></li>
    <li {{ $page == "shipping_handling" ? "class=help-active" :"" }}><a href="{{ url('/') }}/home/shipping_handling"><i class="fa fa-truck"></i>Ketentuan Pengiriman</a></li>
    <?php /*
    <li {{ $page == "same-day" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/same-day"><i class="fa fa-question-circle"></i>Same Day &amp; Next Day Delivery</a></li>*/?>
<!--    <li {{ $page == "kredivo" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/kredivo"><i class="fa fa-question-circle"></i>Pembayaran dengan Kredivo</a></li>-->
</ul> 