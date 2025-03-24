<?php
include 'buffer.inc';
include 'coffee.inc';
include 'identity.inc';
include 'answercheck.inc';
include 'questions.inc';

##################
# Test Group
#
if ( $groupname == false ) {
  errormsg("503","Cannot find groupname!");
}

##################
# Get Jack
#
if ( checkJack() ) {
  echo '<h2 class="subtitle">Jack found safe and sound</h2><div class="content-container"><div class="scroll-content" id="scrollable">';
  echo "<p>".$questionCongrats['Jack'][array_rand($questionCongrats['Jack'])]."</p>";
  echo "<p>".$questionsStay[array_rand($questionsStay)]."</p>";
  echo "</div></div>";
}
else {
  errormsg("404","Ah, ye wee skallyway. Submitting those ill gotten coordinates: ".$_POST['lat'].",".$_POST['lng'].", be the tactics most unworthy of a Sea Scout. What's the first law of scouting: A scout needs be trustworthy. No you mayn't continue onward. You'll be turnin' about. Savvy!");
}
?>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/">home</a></p>
    </div>

