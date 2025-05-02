<?php

$allowed_params = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'fbclid', 'gclid', 'lat', 'lng', 'len', 'voucher', 'jgDonationId', 'group'];

foreach ($_GET as $param => $value) {
  if (!in_array($param, $allowed_params)) {
    include $_SERVER['DOCUMENT_ROOT'] . '/error/404.php';
  }
}
?>
