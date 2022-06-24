<div class="loyalty-header">
    <img src='{!! GenerateQRCodeIMG($user->customer_email, 75, "png") !!}' alt=""/>
    <h1>{{ $user->customer_fname.' '.$user->customer_lname }}</h1>
    <p>{{ $user->customer_email }}</p>
    <p><br />
	<?php /*<a href="#">Benka Point Anda : IDR {{ number_format($user->customer_credit,0,".",".") }}</a></p>*/?>
<?php /*    
<div class="loyalty-type">
        <ul>
            <li>
                <img src="/berrybenka/mobile/img/bb-stamp/bb-stamp-large.png" alt="">
                <p>Kamu Memiliki {{ isset($user->stamp_active) ? $user->stamp_active : 0}} Active Benka Stamp</p>
            </li>
            <li>
                <img src="/berrybenka/mobile/img/bb-stamp/pending-stamp.png" alt="">
                <p>Kamu Memiliki {{ isset($user->stamp_pending) ? $user->stamp_pending : 0}} Pending Benka Stamp</p>
            </li>
        </ul>
    </div>
*/?>
</div>
