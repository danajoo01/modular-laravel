<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
<script type="text/javascript">
//var dataLayer = dataLayer || []; 
dataLayer.push({ 
    'PageType': 'Homepage', 
    'HashedEmail': '{{ $user_email }}', 
});    
</script>

