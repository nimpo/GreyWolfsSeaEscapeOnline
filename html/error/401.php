<?php 
$h1="";
$title="";
include 'buffer.inc'; 
http_response_code(401);
?>
    <h2 class="subtitle">Access Denied Error 401</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <h3>Scouts</h3>
        <p>To access the Sea Escape online challenges you need to log in with your team's username and password. Ask your leader for this.</p>
        <h3>Leaders</h3>
        <p>To access the leaders' area you need to use the leaders username and password. To get a leaders username and password you need to <a href="/register/">register</a>.</p> 
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
?>
