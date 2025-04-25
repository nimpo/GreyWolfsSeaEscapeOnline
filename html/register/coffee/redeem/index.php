<?php
include 'buffer.inc';
include 'coffee.inc';
include 'passwd.inc';
include 'cookie.inc';
include 'b64url.inc';

#$group=getGroupFromGroupCookie();

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
$uuid=$_GET["voucher"] ?? "";
$group=$_GET["group"] ?? getGroupFromGroupCookie() ?? "";
if ( preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',$uuid) ) {
  $voucher  = file_get_contents("$coffeedir/UUID=$uuid"); // Should be a JSON file
  $json     = json_decode($voucher,true);
  $name     = $json['data']['supporter_name'] ?? '';
  $email    = $json['data']['supporter_email'] ?? '';
  $amount   = $json['data']['extras']['ammount'] ?? '';
  $currency = $json['data']['extras']['USD'] ?? '';
  $on       = $json['data']['created_at'] ?? ''; // "2025-01-12 09:52:29"
  #$on = strtotime($on);
  $reward   = $json['data']['extras'][0]['title'] ?? '' ; // "Grey Wolf's SeaEscape"
}
else {
  errormsg(403,'Missing voucher ID:');
}

if ( $voucher === false ) errormsg(500,'No such voucher '. $voucher);
if ( $name === "" ) errormsg(500,'Found a voucher but it is missing information.');
if ( $reward != "Grey Wolf's SeaEscape" ) errormsg(403,"BMC says this voucher was for something else: '$reward'");

$now=time();
$expires=$on + 2678400;
if ($now > $expires) errormsg(402,'Your voucher is no longer valid. Vouchers are valid for a month.');

#################
#
$bgroup=base64_url_encode($group);
$username=str_replace(' ','',ucwords(str_replace(['-', '_'],' ',preg_replace("/[^A-Za-z0-9 _-]/",'',$group)))); # Make camelCase
$gexists=file_exists("$coffeedir/users/$username");
if ($group == "" || $gexists) {
?>
    <h2 class="subtitle">Welcome <?= $name ?></h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <h3>Welcome to Grey Wolf's Sea Escape <?= $name ?></h3>
        <p><?= ($group != "")?"That group name is already taken.<br />Please choose a different":"Please tell us your" ?> <span style="text-wrap:nowrap;">group name: <input type="text" name="Group" size="32" id="Group" onchange="checkInput();" onkeyup="checkInput();" /></span><span id="infos" style="color:#ff0000"> (too&nbsp;short)</span></p>
        <div class="button-container">
          <button id="redeem" class="fbuttons buttons-no" disabled onclick="handleClick()" value="Redeem">Redeem</button>
        </div>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
    <script>
      function checkInput() {
        const input = document.getElementById("Group");
        const value = input.value;
        const infos = document.getElementById("infos");
        const button = document.getElementById("redeem");

        if (value.replace(/ +/g,'').length > 4 && value.length < 65 && /^[A-Za-z0-9_. -]+$/.test(value)) { //enable/disable button and change src img /path/file.ext /path/FILE.ext to uppercase s|^(.*)/(.*)\.([^.]*)$|\1/\U\2.\3|
            button.removeAttribute("disabled")
            button.classList.remove("buttons-no");
            button.classList.add("buttons-yes")
            input.style.backgroundColor = ""; // Reset background color
            infos.innerHTML = "";
            infos.style.color = "#000000";
          return true;
        } else {
            button.setAttribute("disabled", "true");
            button.classList.remove("buttons-yes");
            button.classList.add("buttons-no");
        }
        if (/[^A-Za-z0-9_. -]$/.test(value)) {
          input.style.backgroundColor = "#ffcccc";
          infos.innerHTML='please avoid using unreasonable chars:&nbsp;"'+value.replace(/[A-Za-z0-9_. -]/g, '')+'".';
          infos.style.color="#ff0000";
        } else if (value.replace(/ +/g,'').length < 5) {
          input.style.backgroundColor = "";
          infos.innerHTML="(too&nbsp;short)";
          infos.style.color="#ff0000";
        } else if (value.length > 64) {
          input.style.backgroundColor = "#ffcccc";
          infos.innerHTML="(too&nbsp;long)";
          infos.style.color="#ff0000";
        }
      }

      function handleClick(where) {
        const input = document.getElementById("Group");
        const value = input.value;
        if (!checkInput()) { return; }
        const bgroup = btoa(value);
        document.cookie = "group="+bgroup+"; max-age=1200; path=/; Secure; SameSite=Lax;";
        window.location.href = window.location.pathname+"?"+"voucher=<?= $uuid ?>&group="+value;
      }
    </script>
<?php
  exit();
}

rename("$coffeedir/UUID=$uuid","$coffeedir/$bgroup");
#################
# Make Names and Passwords
#
#$username=str_replace(' ','',ucwords(str_replace(['-', '_'],' ',preg_replace("/[^A-Za-z0-9 _-]/",'',$group)))); # Make camelCase
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
newReg($username,$bgroup);
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
