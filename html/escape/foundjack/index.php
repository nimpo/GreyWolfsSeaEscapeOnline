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
  http_response_code(503);
  echo '<h2 class="subtitle">Error 503</h2><div class="content-container"><p>Cannot find groupname!</p></div>';
  exit;
}

##################
# Get Jack
#
if ( checkJack() ) {
  echo '<h2 class="subtitle">Jack found safe and sound</h2><div class="content-container"><div class="scroll-content" id="scrollable">';
  echo "<p>".$questionCongrats['Jack'][array_rand($questionCongrats['Jack'])]."</p>";
  echo "<p>".$questionsStay[array_rand($questionsStay)]."</p>";
  echo "</div></div>";
#  echo '<h2 class="subtitle">Jack found safe and sound</h2><div class="content-container"><div class="scroll-content" id="scrollable"><p>Well done shipmates! Jack is safe and is recovering in sick bay. You have access to a chart and have instructions to return to harbour.</p><p>However, as you head back, you hear a witchy song on the air. You are inexplicably drawn towards some haunting music. <br /><a href="/escape/sirens/">Click here and escape the sirens song</a></p></div></div>';
}
else {
  errormsg("404","Ah, ye wee skallyway. Those by the tactics most unworthy of a Sea Scout. What's the first law of scouting: A scout needs be trustworthy. No you mayn't continue onward. You'll be turnin' about. Savvy!");
}
?>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/">home</a></p>
    </div>

