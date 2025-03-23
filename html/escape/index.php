<?php
include 'buffer.inc';
include 'identity.inc';
include 'questions.inc';

##################
# Test Group
#
if ( !$groupname ) errormsg(503,"Cannot find groupname for $user!");
if ( !$team ) errormsg(503,"Cannot find team!");

##################
# Get next question
#
include 'answercheck.inc';
$Q=nextQuestionNo();
if ($Q >= count($questionOrder)) {
  echo '<h2 class="subtitle">Challenge complete</h2>';
  echo '<div class="content-container">';
  echo '<div class="scroll-content" id="scrollable">';
  echo '<p>Well done team '.$team.', you appear to have successfully completed the challenge. Now you can sit back and listen to the Longest Johns..</p>';
  echo '<iframe style="margin: 0 auto; display: block; margin-bottom: 10px;" width="560" height="315" src="https://www.youtube.com/embed/SaEXyQg7pCc?si=T862fE6xSo9bQXo9&autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';
  echo '</div></div>';
}
else {
  $qname=$questionOrder[$Q];
  echo '<h2 class="subtitle">'.$questionTransition[$qname].'</h2>';
  echo '<div class="content-container">';
  echo '<div class="scroll-content" id="scrollable">';
  echo '<p>'.$questionText[$qname].'</p>';
  echo '<p>'.$questionSpecifics[$team][$qname].'</p>';
  echo '<p><a href="'.$questionPath[$qname].'">Click here to continue</a></p>';
  echo '</div></div>';
}
?>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/">home</a></p>
    </div>
