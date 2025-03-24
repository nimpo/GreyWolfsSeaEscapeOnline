<?php
include 'buffer.inc';
include 'coffee.inc';
include 'identity.inc';

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];
$fullUrl = "https://".$_SERVER['HTTP_HOST'].$requestUri;

#################
# Get Registration
#
#$user = $_SERVER['REMOTE_USER'] ?? 'None';
$since = filemtime("$coffeedir/users/$username");
echo $since;
$now=time();
$end=$since+(31*86400);
$dtnow = new DateTime("@$now");
$dtstart = new DateTime("@$since");
$dtstart->setTimezone(new DateTimeZone("UTC"));
$dtend = new DateTime("@$end");
$interval = $dtnow->diff($dtend);
#$remaining=($now-$since+(31*86400));
$registered=str_replace(" ", "&nbsp;",$dtstart->format("Y-m-d H:i:s")."&nbsp;(UTC)");
$remaining=sprintf( "%d days, %d hours, %d minutes, %d seconds",$interval->days,$interval->h,$interval->i,$interval->s);
#$interval->days.$interval->h.$interval->i.$interval->s;  

##################
# Test Group
#
if ( $groupname == false ) {
  http_response_code(503);
  echo "Cannot find groupname";
  exit;
}

##################
# Get Jack
#
function toDMS($deg, $type) {
  $h = $type == "lat" ? ($deg >= 0 ? 'N' : 'S') : ($deg >= 0 ? 'E' : 'W');
  $d = floor(abs($deg));
  $m = floor((abs($deg) - $d) * 60);
  $s = round((abs($deg) - $d - $m/60 ) * 3600, 2);
  return sprintf("%d&deg;&nbsp;%02d'&nbsp;%02.2f&quot;&nbsp;%s", $d, $m, $s, $h);
}
function toDDM($deg, $type) {
  $h = $type == "lat" ? ($deg >= 0 ? 'N' : 'S') : ($deg >= 0 ? 'E' : 'W');
  $d = floor(abs($deg));
  $m = round((abs($deg) - $d) * 60,2);
  return sprintf("%d&deg;&nbsp;%02.2f'&nbsp;%s", $d, $m, $h);
}

$fname="$groupname.json";
$boundsjson = false;
if (file_exists($coffeedir."/bounds/$fname")) {$boundsjson=file_get_contents($coffeedir."/bounds/$fname");}
if ( $boundsjson == false ) {
  $jack="Jack's location is currently not set";
  $audio="";
}
else {
  $bounds=json_decode($boundsjson);
  if ($bounds->len >20) {
    $jack="Jack is currently at ".toDDM($bounds->lat,"lat").", ".toDDM($bounds->lng,"lon");
  } else {
    $jack="Jack is currently at ".toDMS($bounds->lat,"lat").", ".toDMS($bounds->lng,"lon");
  }
  $audio="The audio for Jack's distress message and Coasrguard response is here: <a href=\"/escape/audio/$groupname.mp3\">MP3</a>.";
}

if ( $_SERVER["REQUEST_METHOD"] != "GET" ) { errormsg($status=405,$str="Only GETS here!"); }
?>
    <h2 class="subtitle">Leaders' Setup Pages</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
      <h3>Welcome &quot;<?= $user ?>&quot;</h3>
      <p>Your registration was processed on <?= $registered ?>. You and your group have <?= $remaining ?> access time remaining. Should you need more time you can either redonate or contact me.</p>
      <p>To set-up/reset your groups starter question please <a href="placejack/">place Jack</a>.  -- You will use a google maps interface to place the Black Pearl. Scouts will use a similar interface to search for the Black Pearl. You will need to scroll and zoom until the ship is at most 100m long. You can hide the ship of size between about 100m and 30cm (depending on latitude and map tiles available). Choosing a tiny ship means you can potentially hide it on your HQ grounds for Scouts to go and physically find treasure.<br /><?= $jack ?>. <?= $audio ?></p>
      <p>You can login and reset the patrol/team passwords here <a href="passwd/"><?= $fullUrl ?>passwd/</a>. But if you forget the leaders credentials you can only reset password by contacting me</p> 
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
