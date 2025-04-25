<?php
include 'buffer.inc';
include 'coffee.inc';
include 'passwd.inc';
include 'cookie.inc';
include 'b64url.inc';

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

function generate_uuid_v4() {
  $d=random_bytes(16);
  $d[6]=chr((ord($d[6])&0x0f)|0x40);
  $d[8]=chr((ord($d[8])&0x3f)|0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s',str_split(bin2hex($d),4));
}

if ( $_SERVER["REQUEST_METHOD"] == "GET" ) {
?>
  <h2 class="subtitle">Thank you</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>Thank you kindly! If your purchase of a coffee voucher was successful then you should find a link sent to your email address. Follow this to activate access for your group. If you have not received an email soon then please contact me: <span aria-label="This email address is obfuscated to prevent abuse. Please obtain my email address by visual methods or by other out-of-band means." style="font-family: ScrambleN;">&lt;89;.$A@;:*&lt;#@:AB4D:FAB4:.A,+.B9</span> with a copy of your receipt email from buymeacoffee.com.</p>
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

# Parse and check
$name     = $json['data']['supporter_name'] ?? '';
$email    = $json['data']['supporter_email'] ?? '';
$amount   = $json['data']['extras']['ammount'] ?? '';
$currency = $json['data']['extras']['USD'] ?? '';
$on       = $json['data']['created_at'] ?? ''; // "2025-01-12 09:52:29"
#$on = strtotime($on);
$reward   = $json['data']['extras']['title'] ?? '' ; // "Grey Wolf's SeaEscape"

if ( $name === "" ) errormsg(403,'Voucher missing information.');
if ( ! preg_match('/[^@]+@[A-Za-z0-9_-]+\.[A-Za-z0-9_.-]+$/',$email)) errormsg(403,'No email how can I possibly match this up with a client, given that BMC refuses to allow tokens to be passed in any robust way?');
if ( $reward != "Grey Wolf's SeaEscape" ) errormsg(403,"Voucher for something else: '$reward'");

now=time();
$expires=$on + 2678400;
if ($now > $expires) errormsg(402,'Voucher is no longer valid. Vouchers are valid for a month.');

# Gen UUID
$uuid=generate_uuid_v4();
if (file_put_contents("$coffeedir/UUID=$uuid", $PostData)) { // 'UUID=' means no b64 clash
  echo "One coffee coming right up. User client to check their email.";
} else {
  http_response_code(500);
  errormsg(403,'Failed to record coffee event.');
}

function plaintextToHtmlParagraphs($text) {
  $text = preg_replace("/\r\n|\r/", "\n", $text);
  $paragraphs = preg_split("/\n{2,}/", trim($text));
  $html = '';
  foreach ($paragraphs as $p) {
    $escaped = htmlspecialchars($p);
    $linked = preg_replace_callback( '/(https?:\/\/[^\s<]+)/i', function ($matches) { $url = $matches[1]; return '<a href="' . $url . '">' . $url . '</a>'; }, $escaped);
    $linked = nl2br($linked, false);
    $html .= "<p>$linked</p>\n";
  }
  return $html;
}

$cid1="topleft";
$cid2="logo";
$cid3="bottomright";

$assets=realpath($_SERVER['DOCUMENT_ROOT'])."/assets";

$img1="$assets/circle-top-left-clip.png";
$img2="$assets/logo.png";
$img3="$assets/circle-bottom-right-clip.png";

$imageData1 = chunk_split(base64_encode(file_get_contents($img1)));
$imageData2 = chunk_split(base64_encode(file_get_contents($img2)));
$imageData3 = chunk_split(base64_encode(file_get_contents($img3)));

$to = $email;
$subject = "Grey Wolf's Sea Escape Access Voucher";
$boundary = uniqid('np');

$plaintext=<<<EOF
Dear $name,

Thank you for buying me a coffee. Your access to the Sea Escape will be created when you follow the voucher link below.

https://seascouts.co.uk/register/coffee/redeem?voucher=$uuid

Vouchers are redeemable for up to 1 month from donation. Access to the Sea Escape will last 1 month from when you redeem the voucher.

On redemption the site will autpmagically activate your account and you will be provided with a password to log-in. Don't lose the leader password! You will then need to set the first question up via the leaders' private interface.

I hope you enjoy your experience. If you have any issues, you can find my email address in the about section or better still raise an issue in the github repository.

Best

Mike Jones

EOF;

$htmltext = plaintextToHtmlParagraphs($plaintext);

$headers = "MIME-Version: 1.0\r\n";
$headers .= "From: Grey Wolf <grey.wolf@seascouts.co.uk>\r\n";
$headers .= "Content-Type: multipart/related; boundary=\"$boundary\"\r\n";

$body  = "--$boundary\r\n";
$body .= "Content-Type: multipart/alternative; boundary=\"alt-$boundary\"\r\n\r\n";
$body .= "--alt-$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$body .= $plaintext . "\r\n";
$body .= "--alt-$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";

$html=<<<EOF
<!DOCTYPE html>
<html>
  <body style="margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff;">
      <tr>
        <td align="center">
          <table width="600" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td align="left" valign="top" style="padding: 10px;">
                <img src="cid:$cid1" alt="Left Image" style="display:block;" width="100" height="100">
              </td>
              <td style="width:100%;" align="center"><div style="margin-left:20px; margin-right:20px; font-family:Arial, sans-serif; font-size:20px; font-weight: bold; line-height:1.5; color:#333333;">Grey Wolf's Sea Escape</div></td>
              <td align="right" valign="top" style="padding: 10px;">
                <img src="cid:$cid2" alt="Right Image" style="display:block;" width="100" height="100">
              </td>
            </tr>
            <tr>
              <td colspan="3" style="padding: 20px;">
                <div style="margin-left:20px; margin-right:20px; font-family:Arial, sans-serif; font-size:16px; line-height:1.5; color:#333333;">
$htmltext
                </div>
              </td>
            </tr>
            <tr>
              <td colspan="2"></td>
              <td align="right" style="padding: 10px;">
                <img src="cid:$cid3" alt="Footer Image" style="display:block;" width="100" height="100">
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
EOF;
$body .= $html . "\r\n";
$body .= "--alt-$boundary--\r\n";

$body .= "--$boundary\r\n";
$body .= "Content-Type: image/png; name=\"circle-top-left-clip.png\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-ID: <$cid1>\r\n";
$body .= "Content-Disposition: inline; filename=\"circle-top-left-clip.png\"\r\n\r\n";
$body .= $imageData1 . "\r\n";

$body .= "--$boundary\r\n";
$body .= "Content-Type: image/png; name=\"logo.png\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-ID: <$cid2>\r\n";
$body .= "Content-Disposition: inline; filename=\"logo.png\"\r\n\r\n";
$body .= $imageData2 . "\r\n";

$body .= "--$boundary\r\n";
$body .= "Content-Type: image/png; name=\"circle-bottom-right-clip.png\"\r\n";
$body .= "Content-Transfer-Encoding: base64\r\n";
$body .= "Content-ID: <$cid3>\r\n";
$body .= "Content-Disposition: inline; filename=\"circle-bottom-right-clip.png\"\r\n\r\n";
$body .= $imageData3 . "\r\n";

$body .= "--$boundary--";

if ( mail($to, $subject, $body, $headers) ) { print("Email Sent"); }
else { errormsg(500,"The email system didn't like to send that email!"); }
?>
