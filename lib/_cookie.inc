<?php

include 'passwd.inc';

$group="";
$b64group="";

function getGroupCookie() {
  global $b64group;
  if (!isset($_COOKIE["group"])) return false;
  if (preg_match('/^([A-Za-z0-9\/+]{4})*([A-Za-z0-9\/+]{4}|[A-Za-z0-9\/+]{3}=|[A-Za-z0-9\/+]{2}==).$/',$_COOKIE["group"],$match)) { # matches single line base64 encoded string
    $b64group=$match[1];
    return $b64group;
  }
}

function getGroupFromGroupCookie() {
  global $group;
  $b64group=getGroupCookie();  # Could use global as set by function, but I chose to use local here for kicks and giggles.
  if ($b64group) {
    $g=$base64_decode($b64group);
    if (preg_match("/^([A-Za-z0-9_. -]{5,64})$/",$g,$match)) {
      $group=$match[1];
      return $group;
    } 
  }
  return false;
}

function setJackCookie($user,$lat,$lng) {
  if ( ( $key = getApr1($user) ) == null ) return false;
  $key=getApr1($user,"user") ?? getApr1($user,"leader");
  if ( $key == null ) { return false; }
  $jacklat=encrypt("$lat",$key);
  $jacklng=encrypt("$lng",$key);
  setcookie("jacklat", $jacklat, time() + (7200), "/");
  setcookie("jacklng", $jacklng, time() + (7200), "/");
}

function getJackCookie($user,$ll) {
  if ($ll == "lat") $var = $_COOKIE['jacklat'] ?? false;
  elseif ($ll == "lng") $var = $_COOKIE['jacklng'] ?? false;
  else return null;
  if ( $var === false ) return null;
  if ( $key == false ) { return false; }
  $key=getApr1($user,"user") ?? getApr1($user,"leader");
  return decrypt($var,$key);
}
?>
