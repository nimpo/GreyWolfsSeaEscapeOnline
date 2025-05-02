<?php 
$h1="";
$title="";
include 'buffer.inc'; 
http_response_code(401);
?>
    <h2 class="subtitle">Access Denied Error 401</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <h3>Access to Wheelhouse denied.</h3>
        <p>Ship's mate, accompany this rapscallion to the Brig! To access the Bridge and Wheelhouse you need a leaders' username and password.</p>
        <p>To gain the captains favour and admitance here you need to have <a href="/register/">registered</a>.</p> 
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
<?php
  exit;
?>
