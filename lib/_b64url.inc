<?php
function base64_url_encode($input) { return strtr(base64_encode($input), '+/=', '-_.');}
function base64_url_decode($input) { return base64_decode(strtr($input, '-_.', '+/='));}
?>

