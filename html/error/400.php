<?php 
$h1="400 - Message not understood";
$title="400 - Message not understood";
include 'buffer.inc'; 
http_response_code(400);
?>
    <h2 class="subtitle">Eh?</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>Station calling, this is <?=$_SERVER['HTTP_HOST']?>, <?=$_SERVER['HTTP_HOST']?>, <?=$_SERVER['HTTP_HOST']?>. Repeat all after &apos;<?=$_SERVER['REQUEST_METHOD']?>&apos;.</p>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
?>
