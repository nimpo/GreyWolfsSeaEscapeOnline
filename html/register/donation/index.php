<?php
include 'buffer.inc';
include 'coffee.inc';
include 'passwd.inc';
include 'cookie.inc';

$group=getGroupFromGroupCookie();

function newReg($u,$g) {
  global $coffeedir;
  if ( ! is_dir($coffeedir."/users/")) mkdir($coffeedir."/users/", 0750);
  file_put_contents($coffeedir."/users/$u",$g);
}

################
# If incoming is a GET then this should be a client redirected from JG
# It should have a cookie set with a base64 encoded group name
#
if ( $_SERVER["REQUEST_METHOD"] != "GET" ) errormsg(405,"Only GETS here!");

#################
# Get donation from query parameters
#
$jgDonationID='';
$dgroup='';
if ( preg_match('/^[0-9]+$/',$_GET["jgDonationId"]) && preg_match('/^([A-Za-z0-9\/+]{4})*([A-Za-z0-9\/+]{4}|[A-Za-z0-9\/+]{3}=|[A-Za-z0-9\/+]{2}==)$/',$_GET["group"]) ) { 
  $jgDonationID=$_GET["jgDonationId"]; // something JG sets and identifies the transaction
  $b64group=$_GET["group"]; // something I set in the url
  $dgroup=base64_decode($b64group);
  if ( $dgroup !== $group ) {
    errormsg(403,'Group name was changed between leaving this site and returning; did your cookie expire or did someone fiddle with the URL. Whatever, '.$_GET["group"].', is not a valid name');
  }
}
else {
  errormsg(403,'Missing donation info for group:'.$_GET["group"].", expecting Donation ID, got:".$_GET["jgDonationId"]);
}  

##################
# Use JG Donation ID to check that donation actually exists
#
$options = [ "http" => [ "method"  => "GET", "header" => "Accept: application/json\r\n" ], "ssl" => [ "verify_peer" => true, "verify_peer_name" => true ] ];

$url="https://api.justgiving.com/57d1716b/v1/donation/$jgDonationID";
if ( ( $donation = file_get_contents($url, false,stream_context_create($options)) ) !== '' ) {
  $json = json_decode($donation,true);
  $transactionid = $json['id'] ?? '';
  $createdat = $json['donationDate'] ?? '';
  if (preg_match('/\/Date\((\d+)(?:[+-]\d{4})?\)\//', $createdat, $matches)) { $createdat = (int)$matches[1]/1000; }
  $status=$json['status'] ?? '';
  $supporter = $json['donorDisplayName'] ?? 'Wassaname';
  file_put_contents($coffeedir."/".$b64group, $donation);
}


#################
# Make sure donation has been accepted.
#
$now=time();
if ( $status !== 'Accepted' )       errormsg(402,"Can't provide service as this transaction has not been Accepted according to JustGiving!");
if ($transactionid == '' )          errormsg(402,"Just Giving didn't send you here with your transaction ID"); 
if ( $createdat+(86400*31) < $now ) errormsg(402,"Stale donation. Please place another 200 pence into the slot.");
if ( ! touch ("$coffeedir/.test") ) errormsg(500,"Cannot set access."); 
unlink("$coffeedir/.test");


#################
# Make Names and Passwords
#
$username=str_replace(' ','',ucwords(str_replace(['-', '_'],' ',preg_replace("/[^A-Za-z0-9 _-]/",'',$group)))); # Make camelCase
$patrols=array("black","yellow","silver","blue","pink","purple");
$leadername=$username."-leaders";
$c="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$gpasswd="";
$lpasswd="";
while (strlen($gpasswd) < 5) { $gpasswd.=$c[random_int(0,61)]; }
while (strlen($lpasswd) < 8) { $lpasswd.=$c[random_int(0,61)]; }
foreach ($patrols as $patrol) {
  newHTPass("$username-$patrol",$gpasswd,"$coffeedir/.htpasswd");
}
newHTPass($leadername,$lpasswd,"$coffeedir/.htleaderspasswd");
newReg($username,$b64group);
?>
    <h2 class="subtitle">Welcome</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <h3>Welcome to Grey Wolf's Sea Escape <?= $group ?></h3>
        <p style="margin-block-end:2px;">The usernames for each patrol/team will be:</p>
        <ol style="margin-block-start:2px;"><?php echo implode(array_map(fn($v) => "<li>&quot;$username-$v&quot;</li>", $patrols));?></ol>
        <p>The password for each are &quot;<?= $gpasswd ?>&quot;. This will give your scouts access to the problems.</p>
        <p>Your leaders username is &quot;<?= $leadername ?>&quot;.
        The corresponding password will be &quot;<?= $lpasswd ?>&quot;. This will gives you access to the settings and answers pages.</p>
        <p>Now process to the setup page: <a href="/leaders/private/">setup</a></p>
      </div>
    </div>
    <button id="printButton" onclick="window.print()">Print Page</button>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
