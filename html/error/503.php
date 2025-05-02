<?php 
$h1="503 - Mayday";
$title="503 - Mayday mayday mayday";
include 'buffer.inc'; 
http_response_code(503);
?>
    <h2 class="subtitle">All the rum is gone</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>The rum is gone. Absolute catastrophe. The ship's crew refuses to work.</p>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
?>
