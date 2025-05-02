<?php 
$h1="502 - Mayday";
$title="502 - Mayday mayday mayday";
include 'buffer.inc'; 
http_response_code(502);
?>
    <h2 class="subtitle">Disaster lies upwind</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>We tried to sail a message through, but the server be upwind and won't carry the response! Try again when the wind shifts, savvy?</p>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
?>
