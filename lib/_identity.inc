<?php
include 'coffee.inc';

#################
# Identity of participant
#
# Should set
# $user       == logged in user camelGroup-(team|'leader')
# $username   == camelGroup
# $groupname  b64EncodedGroupname # read from file
# $team       team || admin

##################
# Get Group Name
#
$user = $_SERVER['REMOTE_USER'] ?? 'None';
preg_match('/^([^-]*)(?:-(leaders|black|yellow|silver|blue|pink|purple))?$/',$user,$matches);
$username = $matches[1] ?? 'none';
$team = $matches[2] ?? 'none';
if (file_exists($coffeedir."/users/$username")) {
  $groupname = file_get_contents($coffeedir."/users/$username") ?? "none";
}  
function lookupNo($name) {
  $lookupDict = [ "leaders" => 0, "black" => 1, "yellow" => 2, "silver"  => 3, "blue"  => 4, "pink"  => 5, "purple" => 6 ];
  return $lookupDict[$name] ?? null;
}
$teamNo=lookupNo($team);
?>
