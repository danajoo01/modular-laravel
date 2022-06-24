<div class="loyalty-menu">
    <ul>
        <li @if ($page=="history") class="active" @endif><a href="{{ URL::to('/user/stamp/history') }}">history</a></li>
        <li @if ($page=="deals") class="active" @endif><a href="{{ URL::to('/user/stamp/deals') }}">deals</a></li>
        <li @if ($page=="terms") class="active" @endif><a href="{{ URL::to('/user/stamp/terms') }}">Syarat & Ketentuan</a></li>
        <li @if ($page=="faq") class="active" @endif><a href="{{ URL::to('/user/stamp/faq') }}">FAQ</a></li>
    </ul>
</div>