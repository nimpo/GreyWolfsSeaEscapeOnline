<?php

include "buffer.inc";
include "identity.inc";

$audiodir=realpath($_SERVER['DOCUMENT_ROOT'])."/escape/audio";

if (file_exists("$audiodir/$groupname.mp3")) {
  header("Location: $groupname.mp3");
  return;
}

errormsg($status=404,$str="No Audio For You Here! $audiodir/$groupname.mp3");

?>
