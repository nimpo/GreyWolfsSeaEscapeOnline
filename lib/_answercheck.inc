<?php
include 'coffee.inc';
include 'identity.inc';
include 'cookie.inc';
include 'answers.inc';

function checkJack() {
  # First check jack is within bounds
  global $coffeedir;
  global $groupname;
  global $user;

  $fname="$coffeedir/bounds/$groupname.json";
  if (!file_exists($fname)) { return null; }
  $boundsjson=file_get_contents($fname);
  if ( $boundsjson == false ) {
    return null;
  }
  else {
    $bounds=json_decode($boundsjson);
    if ($bounds->len >0) {
      $latrad = ($bounds->len)/6378137;
      $minLat = $bounds->lat - rad2deg($latrad);
      $maxLat = $bounds->lat + rad2deg($latrad);
      $deltaLng = rad2deg($latrad / cos(deg2rad($bounds->lat)));
      $minLng = $bounds->lng - $deltaLng;
      $maxLng = $bounds->lng + $deltaLng;
      if ( isset($_POST['lat']) && isset($_POST['lng']) ) { # Posted
        $ansLat = $_POST['lat'];
        $ansLng = $_POST['lng'];
        if ($ansLat > $minLat && $ansLat < $maxLat && $ansLng > $minLng && $ansLng < $maxLng) {
          setJackCookie($user,$ansLat,$ansLng);
          return true;
        }
      }
      elseif (isset($_COOKIE['jacklat']) && isset($_COOKIE['jacklng'])) {
        $ansLat = getJackCookie($user,"lat");
        $ansLng = getJackCookie($user,"lng");
        if ($ansLat > $minLat && $ansLat < $maxLat && $ansLng > $minLng && $ansLng < $maxLng) {
          setJackCookie($user,$ansLat,$ansLng);
          return true;
        }
      } 
      else {
        return false;
      }
    }
    else {
      return null;
    }
  }
}

function checkAnswer(string $Q) { #use post||cookie     
# if Q is 0 checkjack Returns true if jack has been found
# if Q is != 0: First check if there's a Post variable matching this Q's Question Check if there is set code to it's answer
# if Q cookie is set ignore Post and set code = cookie value
# if Q derrived code obtained matches lookup in answers return true elongate, cookie time
  global $answers;
  global $team;
  $ans=$answers[$team][$Q] ?? false;
  if ($ans === false) return;
  if ($ans == "Jack") return checkJack(); # special case!
  $code="";
  if (isset($_POST['Q']) && $_POST['Q']==$Q) $code=($_POST['code1'] ?? "").($_POST['code2'] ?? "").($_POST['code3'] ?? "");
  if(strlen($code)!=3) $code=$_COOKIE['Q'.$Q] ?? "";
  if ("$ans" == "$code") {
    setcookie("Q".$Q, "$code", time() + (7200), "/");
    return true;
  }
  return false;
}

function nextQuestionNo() {
  $n=0;
  while ( checkAnswer($n) ) {$n++;}
  return $n;
}

?>
