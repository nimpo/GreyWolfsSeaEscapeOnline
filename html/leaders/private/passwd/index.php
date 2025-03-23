<?php
include 'buffer.inc';
include 'coffee.inc';
include 'identity.inc';
include 'passwd.inc';

#################
# Get Registration
#
#$user = $_SERVER['REMOTE_USER'] ?? 'None';
$since = filemtime("$coffeedir/users/$username");
$now=time();
$end=$since+(31*86400);
$dtnow = new DateTime("@$now");
$dtstart = new DateTime("@$since");
$dtstart->setTimezone(new DateTimeZone("UTC"));
$dtend = new DateTime("@$end");
$interval = $dtnow->diff($dtend);
$registered=str_replace(" ", "&nbsp;",$dtstart->format("Y-m-d H:i:s")."&nbsp;(UTC)");
$remaining=sprintf( "%d days, %d hours, %d minutes, %d seconds",$interval->days,$interval->h,$interval->i,$interval->s);

##################
# function to genrate apr1 MD5 hash
#   Saves having to exec out to htpasswd this is MD5 with many many tsp of salt iterated in.
#

if ( $_SERVER["REQUEST_METHOD"] != "GET" ) { errormsg($status=405,$str="Only GETS here!"); }

$patrols=array("black","yellow","silver","blue","pink","purple");
$c="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$gpasswd="";
while (strlen($gpasswd) < 5) { $gpasswd.=$c[random_int(0,61)]; }
foreach ($patrols as $patrol) {
  error_log("$username-$patrol-$gpasswd",0);
  newHTPass("$username-$patrol",$gpasswd,"$coffeedir/.htpasswd");
}
?>
    <h2 class="subtitle">Updated Passwords for <?= $username ?></h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>The usernames for each patrol/team will be:</p>
        <ol><?php echo implode(array_map(fn($v) => "<li>&quot;$username-$v&quot;</li>", $patrols));?></ol>
        <p>The password for each will be &quot;<?= $gpasswd ?>&quot;. This will give your scouts access to the tasks.</p>
        <p>Leaders' username &quot;<?= $user ?>&quot; and password remain unchanged.
      </div>
    </div>
    <button id="printButton" onclick="window.print()">Print Page</button>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
