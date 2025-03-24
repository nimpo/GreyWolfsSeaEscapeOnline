<?php
include 'buffer.inc';
include 'coffee.inc';
include 'passwd.inc';
include 'cookie.inc';

##################
# How we know Coffee is supplied by a legitimate purveyor of coffee
# This is the key used to sign the header documented as "X-BMC-Signature"
# but which is actually "HTTP-X-Signature"
#
$coffeekeyfile="$coffeedir/coffeekey";
if (file_exists($coffeekeyfile)) {
  preg_match('/^\s*COFFEE\s*=\s*([a-f0-9]+)/', file_get_contents($coffeekeyfile), $matches);
  $coffeekey=$matches[1] ?? null;
  if ($coffeekey === null) { errormsg(500,"BMC key not configured.");}
}
else {errormsg(500,"BMC key not configured.");}

function newReg($u,$g) {
  global $coffeedir;
  if ( ! is_dir($coffeedir."/users/")) mkdir($coffeedir."/users/", 0750);
  file_put_contents($coffeedir."/users/$u",$g);
}

if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {

  ##################
  # The route to this needs to be from a registration click that sets a cookie
  # Because I cannot get BMC to redirect back here with a query string like I do with JG.
  #
  if (getGroupFromGroupCookie() == false) { errormsg(403,"Error missing data. Did you land here without clicking a button?"); }
  $coffeestatus=checkCoffeePot($b64group); # returns the relevant coffee that arrived in the POST
  $json = json_decode($coffeestatus,true);
  $qgroup        = $json['data']['extras']['extra_question']['question_answers'][0] ?? '';
  $transactionid = $json['data']['transaction_id'] ?? '';
  $refunded      = $json['data']['refunded'] ?? null;
  $createdat     = $json['data']['created_at'] ?? 0;
  $status        = $json['data']['status'] ?? '';
  $supporter     = $json['data']['supporter_name'] ?? 'Wassaname';

  $now=time();

  ################
  # check the check
  # Make sure waiter hasn't collected it back
  #
  if ( $refunded === true ) errormsg(402,"Can't provide service as this transaction has been refunded!");
  if ( $transactionid == '' ) errormsg(402,"Buymeacoffee didn't send all the infos"); 
  if ( $createdat+(86400*31) < $now ) errormsg(402,"This is an old coffee and it's gone cold!");

  $username=str_replace(' ','',ucwords(str_replace(['-', '_'],' ',preg_replace("/[^A-Za-z0-9 _-]/",'',$group)))); # Make Camelcase
  if (preg_match("/^[^A-Za-z0-9 _-]*[a-z]/")) {$username=lcfirst($username);}
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
    <h2 class="subtitle">Welcome <?= $group ?></h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>Thank you kindly! Just setting up your group access...</p>
        <p>The usernames for each patrol/team will be:</p>
        <ol><?php echo implode(array_map(fn($v) => "<li>&quot;$username-$v&quot;</li>", $patrols));?></ol>
        <p>The password for each will be &quot;<?= $gpasswd ?>&quot;. This will give your scouts access to the tasks.</p>
        <p>Your leaders username will be &quot;<?= $leadername ?>&quot;.
        The corresponding password will be &quot;<?= $lpasswd ?>&quot;. This will give you access to the answers pages. It will also let you know how much longer your access will last and will allow you to reset your two passwords.</p>
      </div>
    </div>
    <button id="printButton" onclick="window.print()">Print Page</button>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
}

################
# If it's a post we're goint to assume it's the callback from coffee shop BMC
#
include 'json.inc'; # strips buffer and resets 
if ( $_SERVER["REQUEST_METHOD"] != "POST" ) {
  echo "You need to send coffee by POST!";
  http_response_code(405);
  exit;
}

if ( $_SERVER["CONTENT_TYPE"] == "application/json" ) {
  if ( $_SERVER["CONTENT_LENGTH"] > 4096 ) {
    echo "Too much coffee; I don't need a bath!";
    http_response_code(413);
    exit;
  }
  if ( ($PostData = file_get_contents('php://input',FALSE, NULL, 0, 4096)) === false){
    echo "You need to actually POST the coffee!";
    http_response_code(404);
    exit;
  }
} else {
  echo "Coffee only accepted in JSON format!";
  http_response_code(404);
  exit;
}

################
# Check data authenticity
#
$sig2=$_SERVER["HTTP_X_SIGNATURE_SHA256"] ?? 'None';
$sig=$_SERVER["X-BMC-Signature"] ?? 'None';
$hash=hash_hmac('sha256', $PostData, $coffeekey);
if ( $sig !== $hash && $sig2 !== $hash ) {
  http_response_code(403);
  echo "$sig != $hash || $sig2 != $hash\n";
  exit;
}

################
# Check coffee is javanese
if ( ($json=json_decode($PostData,true)) === null ) {
  echo "Coffee needs to be a proper cup of coffee in a proper copper coffee pot!";
  http_response_code(418);
  exit;
}

################
# Extract the group name for which the coffee is being donated
#
$qgroup = $json['data']['extras']['extra_question']['question_answers'][0] ?? 'NoSuchGroup';
$b64group = base64_encode($qgroup);

################
# Check the question was answered
#
if ( $qgroup == 'NoSuchGroup' ) {
  date_default_timezone_set('UTC');
  $date=date("Y-m-d\TH:i:s\Z");
  file_put_contents($coffeedir."/NoSuchGroup-".$date, $PostData);
  http_response_code(400);
  echo "Question not answered by client";
  exit;
}

################
# If we got here, save the coffee on the system
#
if (file_put_contents($coffeedir."/".$b64group, $PostData)) {
  echo "Item Recorded";
} else {
  http_response_code(500);
  echo "Failed to save data.";
}
?>
