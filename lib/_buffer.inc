<?php

##################
# Buffer a nice html responses
#
if (! str_contains(ob_get_contents(),"Grey Wolf's Sea Escape")) { # Only define if not defined

header('Content-Type: text/html; charset=utf-8',true);
$pagetitle=$title ?? "Grey Wolf's Sea Escape";
$pagehead=$h1 ?? "Grey Wolf's Sea Escape";
#if (!isset($pagehead)) {$pagehead="Grey Wolf's Sea Escape";}
function errormsg($status=500,$str="An uncaught error occurred.") {
  http_response_code($status);
  echo '<h2 class="subtitle">Error '."$status".'</h2><div class="content-container"><p>'."$str".'</p></div>';
  echo <<<EOF
  <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
EOF;
  exit;
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="description" content="Official site for Grey Wolf's Sea Escape: an escape room activity for Sea Scouts introducing some key navigational concepts and marine radio basics, with a little whimsy thrown in." />
    <meta name="keywords" content="Sea Scouts, Navigation, Escape Room, Mayday, Marine Radio, Sirens, Bearings, Transits, Buoys" />
    <meta name="author" content="Dr Grey Wolf" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pagetitle ?></title>
    <link rel="stylesheet" type="text/css" href="/assets/base.css" />
    <script type="text/javascript" src="/assets/scroll.js"></script>
    <link rel="icon" href="/assets/favicon/favicon.ico" sizes="48x48" >
    <link rel="icon" href="/assets/favicon/favicon.svg" sizes="any" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/assets/favicon/apple-touch-icon.png"/>
    <link rel="manifest" href="/assets/favicon/site.webmanifest" />
    <meta property="og:title" content="Grey Wolf's Sea Escape" />
    <meta property="og:description" content="An escape room with a twist. The name o' the game be navigation—charts, compasses, rules of the high sea and a fair bit o' cunning will be yer best mates as ye work to unravel riddles, dodge dangers, and outwit the sea's many mysteries." />
    <meta property="og:image" content="<?= 'https://'.$_SERVER['HTTP_HOST'].'/assets/favicon/circle-bottom-right.png' ?>" />
    <meta property="og:url" content="<?= 'https://'.$_SERVER['HTTP_HOST'].'/' ?>" />
    <meta property="og:type" content="website" />
  </head>
  <body onload="scrollability()">
    <img class="circle-top-left" src="/assets/circle-top-left.svg" alt="Circle with coloured stripes decoration in Scout Brand colours."/>
    <img class="circle-bottom-right" src="/assets/circle-bottom-right.svg" alt="Black Pearl: White on a Blue and Yellow Scout Navigation Badge background."/>
    <img src="/assets/logo.svg" alt="Logo" class="svg-logo">
    <h1 class="pagetitle"><?= $pagehead ?></h1>
<?php
# Set foot as global variable because cannot unregister shutdown functions once defined
$foot="</body></html>";
register_shutdown_function(function() { global $foot; echo $foot; ob_end_flush(); });
}
include('spam.inc');
?>
