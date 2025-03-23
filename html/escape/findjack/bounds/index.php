<?php
include 'coffee.inc';
include 'identity.inc';

##################
# Test Group
#
if ( $groupname == false ) {
  http_response_code(503);
  header('Content-Type: application/json; charset=utf-8');
  echo '{"error": "Cannot find groupname!"}';
  exit;
}

##################
# Get Jack
#
$fname="$groupname.json";
$boundsjson=file_get_contents($coffeedir."/bounds/$fname");
if ( $boundsjson == false ) {
  http_response_code(404);
  header('Content-Type: application/json; charset=utf-8');
  echo '{"error": "Jack is not placed!"}';
  $jack="Jack's location is currently not set";
}
else {
  $bounds=json_decode($boundsjson);
  if ($bounds->len >0) {
    header('Content-Type: application/json; charset=utf-8');
    echo "$boundsjson";
  }
  else {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo '{"error": "Something went wrong."}';
  }


}
?>
