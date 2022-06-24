<?php
$user = \Auth::user();
$user_email = !empty($user->customer_email) ? $user->customer_email : "";
?>
