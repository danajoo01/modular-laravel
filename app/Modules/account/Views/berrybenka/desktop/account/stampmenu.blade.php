<ul>
    <li><a href="{{ URL::to('/user/account_dashboard') }}" @if ($page=="index") class="active" @endif>Berrybenka Stamp</a></li>
    <li><a href="{{ URL::to('/user/stamp/deals') }}" @if ($page=="deals") class="active" @endif>Deals</a></li>
    <li><a href="{{ URL::to('/user/stamp/terms') }}" @if ($page=="terms") class="active" @endif>Syarat dan Ketentuan</a></li>
    <li><a href="{{ URL::to('/user/stamp/faq') }}" @if ($page=="faq") class="active" @endif>FAQ</a></li>
</ul>