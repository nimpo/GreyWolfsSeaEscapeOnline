<?php
include 'coffee.inc';
include 'identity.inc';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];
$fullUrl = "https://".$_SERVER['HTTP_HOST'].$requestUri;

if ( $groupname == 'none' ) {
  http_response_code(503);
  echo "Cannot find groupname";
  exit;
}

$fname="$groupname.json";
$bounds = new stdClass();
$bounds->lat=(float)$_GET['lat'] ?? 9999;
$bounds->lng=(float)$_GET['lng'] ?? 9999;
$bounds->len=(float)$_GET['len'] ?? -1;

if ($bounds->lat > -90 && $bounds->lat < 90 && 
    $bounds->lng >= -180 && $bounds->lng <= 180 &&
    $bounds->len > 0.1 && $bounds->len <= 100 ) {
  if ( file_put_contents($coffeedir."/bounds/$fname", json_encode($bounds)) ) {
    header("Location: https://$host/leaders/private/placejack/audio/");
  }
  else { 
    http_response_code(503);
    echo "Failed to write $fname";
    exit;
  }
}
else {
  http_response_code(503);
  echo "Params Not Valid";
  exit;
}
?>
