<ul class="sidebar-list">
    <li {{ $page == "faq" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/faq"><i class="fa fa-question-circle"></i>FAQ</a></li>
    <li {{ $page == "how_to_order" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/how_to_order"><i class="fa fa-shopping-cart"></i>How To Order</a></li>
    <li {{ $page == "help_return" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/help_return"><i class="fa fa-puzzle-piece"></i>Return Policy</a></li>
    <li {{ $page == "shipping_handling" ? "class=help-active" :"" }}><a href="{{ url('/') }}/home/shipping_handling"><i class="fa fa-truck"></i>Shipping and Handling</a></li>
<!--    <li {{ $page == "kredivo" ? "class=help-active" : "" }}><a href="{{ url('/') }}/home/kredivo"><i class="fa fa-question-circle"></i>Payment with Kredivo</a></li>-->
</ul> 