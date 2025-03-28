<?php

include "buffer.inc";
include "identity.inc";

$audiodir=realpath($_SERVER['DOCUMENT_ROOT'])."/escape/audio";

if (file_exists("$audiodir/$groupname.mp3")) {
  header("Location: $groupname.mp3");
  return;
}

errormsg(404,"No Audio For You Here!<br />Ask your leader to placejack at https://".$_SERVER['HTTP_HOST']."/leaders/private/placejack/<br />They can find the link under the <b>leader-login</b> menu.");
?>
