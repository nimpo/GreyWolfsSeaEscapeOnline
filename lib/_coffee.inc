<?php

##################
# Where coffee is kept
#
if ( ( $coffeedir = realpath(dirname(realpath($_SERVER['DOCUMENT_ROOT']))."/coffee") ) === false ) {
  http_response_code(503);
  echo "Server does not have a coffee pot";
  exit;
}

##################
# function check for valid cookie: complete or incomplete transaction if GETted (GOT?)
# This is presented by the Scout leader's browser.
# And will be checked against the Group name that is supplied in the shop.
# ATTN: Even a slight mismatch will cause a failure to match incoming coffee to a session.
#

function checkCoffeePot($b64group) { # Do not use unsafe $b64group here !!!!!
  global $coffeedir;
  $coffeeFile=$coffeedir."/".$b64group;
  if (file_exists($coffeeFile)) { return file_get_contents($coffeeFile,FALSE, NULL, 0, 4096); }
  http_response_code(402);
  echo "No coffee has been received yet, try again later.\n";
  exit;
}

?>
