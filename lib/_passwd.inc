<?php
include 'identity.inc';

##################
# function to genrate apr1 MD5 hash
#   Saves having to exec out to htpasswd this is MD5 with many many tsp of salt iterated in.
#
function newHTPass($u,$p,$f) { # does no checking; Takes  username, password, .htpasswd file.
  $o="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
  $n="./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  $s="";while(strlen($s)<8){$s.=$o[random_int(0,61)];}
  $d=pack("H*",md5($p.'$apr1$'.$s.substr(str_repeat(substr(pack("H*",md5($p.$s.$p)),0,16),ceil(strlen($p)/16)),0,strlen($p)).strtr(strrev(decbin(strlen($p))),['1'=>"\0",'0'=>$p[0]])));
  for($i=0;$i<1000;$i++){$d=pack("H*",md5((($i%2)?$p:$d).(($i%3)?$s:"").(($i%7)?$p:"").(($i%2)?$d:$p)));}
  $l=$u.':$apr1$'.$s.'$'.strtr(strrev(substr(base64_encode("\0\0".implode('',array_map(fn($i)=>$d[$i],[11,4,10,5,3,9,15,2,8,14,1,7,13,0,6,12]))),2)),$o,$n);
  $r=@file_get_contents($f);
  $w=preg_replace("/^$u:.*$/m",$l,$r);
  if ($w == $r) {$w.="$l\n";}
  file_put_contents($f, $w, LOCK_EX);
  return $f;
}

function newPass($len=5) { # Creates a simple password
  $c="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $pwd="";
  while (strlen($pwd) < $len) { $pwd.=$c[random_int(0,61)]; }
  return $pwd;
}

function encrypt($data, $key) {
    $iv = random_bytes(16); // Generate a random IV
    $cipherText = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $cipherText); // Store IV with encrypted data
}

function decrypt($data, $key) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 16); // Extract IV
    $cipherText = substr($data, 16);
    return openssl_decrypt($cipherText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

function getApr1($user,$type) {  # Use a user's digested material as a noddy password for basic encrypting stuff for e.g. cookies; saves having to store another pwd
  global $coffeedir;
  $file="$coffeedir/".(($type=="leader")?".htleaderspasswd":".htpasswd");
  if (!is_readable($file)) return null;
  $pattern = "/^".preg_quote($user,'/').':\\$apr1\\$(.*?)\$/';
  $handle = fopen($file, "r");
  if (!$handle) return null;
  while (($line = fgets($handle)) !== false) {
    if (preg_match($pattern, $line, $matches)) {
      fclose($handle);
      return $matches[1] ?? null; 
    }
  }
  fclose($handle);
  return null; 
}

?>
