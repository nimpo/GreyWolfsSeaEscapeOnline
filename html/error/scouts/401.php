<?php 
$h1="";
$title="";
include 'buffer.inc'; 
http_response_code(401);
?>
    <h2 class="subtitle">Access Denied Error 401</h2>
    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <h3>Permission to come aboard Cap'n?</h3>
        <p>To set sail on the Sea Escape challenges, ye'll need to log in with yer crew's secret codename and passphrase. If ye ain't got 'em, best have a word with yer trusty leader&mdash;lest ye fancy driftin' in the doldrums before ye even leave port!</p>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/">home</a></p>
    </div>
<?php
  exit;
?>
