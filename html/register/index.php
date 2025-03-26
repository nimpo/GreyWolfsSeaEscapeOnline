<?php
include 'buffer.inc';
include 'cookie.inc';

if (getGroupFromGroupCookie()) {
  header("Location: /leaders/private");
  exit;
}

if (file_exists("$coffeedir/coffeekey")) {
  $coffeekeyfile=file_get_contents("$coffeedir/coffeekey");
  if ( preg_match('/^\s*COFFEE\s*=\s*([a-f0-9]+)/', $coffeekeyfile) && preg_match('/^\s*COFFEEPOT\s*=\s*(https:\/\/\S+)/', $coffeepot) ) {
    $coffeelink=$coffeepot[1] ?? ""; // falsy
  }
}
    

$host=$_SERVER['HTTP_HOST'];

?>
    <h2 class="subtitle">Registration</h2>

    <div class="content-container">
      <div class="scroll-content" id="scrollable">
        <p>Firstly, we need your <span style="text-wrap:nowrap;">group's name: <input type="text" name="Group" size="32" id="Group" onchange="checkInput();" onkeyup="checkInput();" /></span><span id="infos" style="color:#ff0000"> (too&nbsp;short)</span></p>
        <p>You may autoregister by making a donation via one of the following. Alternatively, if you are a scout group on a tight budget, <a href="/about/#complementary">ask me for complementary access</a>.</p>
        <div class="button-container">
          <button id="imageButton1" class="buttons buttons-no" disabled onclick="handleClick('23')"><img id="i23" src="/assets/jg23.svg" alt="Donate to 23rd Manchester"></button>
          <button id="imageButton2" class="buttons buttons-no" disabled onclick="handleClick('123')"><img id="i123" src="/assets/jg123.svg" alt="Donate to Chorlton Sea Scouts"></button>
<?php
if ($coffeelink) {
?>
          <button id="imageButton3" class="buttons buttons-no" disabled onclick="handleClick('me')"><img id= "ime" src="/assets/bmc.svg" alt="Buy Grey Wolf a coffee"></button>
<?php
}
?>
        </div>
      </div>
    </div>
    <div class="bottom-links">
      <p style="margin-block-start:2px"><a href="/about/">about</a> | <a href="/notes/">leaders-notes</a> | <a href="/leaders/private/">leader-login&nbsp;&#128274;</a> | <a href="/register/">register</a> | <a href="/">home</a></p>
    </div>
    <script>
      function checkInput() {
        const input = document.getElementById("Group");

        const buttons = [];
        let i=0;
        while ( (let b = document.getElementById("imageButton"+i)) !== null; ) { i++; button.push(b); }

        const value = input.value;
        const infos = document.getElementById("infos");

        if (value.replace(/ +/g,'').length > 4 && value.length < 65 && /^[A-Za-z0-9_. -]+$/.test(value)) { //enable/disble button and change src img /path/file.ext /path/FILE.ext to uppercase s|^(.*)/(.*)\.([^.]*)$|\1/\U\2.\3|
          for (const button of buttons) {
            button.removeAttribute("disabled")
            button.classList.remove("buttons-no");
            button.classList.add("buttons-yes")
            button.firstChild.src=button.firstChild.src.replace(/^(.*)\/([^\/]+)\.([^.]+)$/, (all, path, name, ext) => {return path+'/'+name.toUpperCase()+'.'+ext; });
          }
	        input.style.backgroundColor = ""; // Reset background color
	        infos.innerHTML = "";
          infos.style.color = "#000000";
          return true;
        } else {
          for (const button of [buttons]) {
            button1.setAttribute("disabled", "true");
            button.classList.remove("buttons-yes");
            button.classList.add("buttons-no");
            button.firstChild.src=button.firstChild.src.replace(/^(.*)\/([^\/]+)\.([^.]+)$/, (all, path, name, ext) => {return path+'/'+name.toLowerCase()+'.'+ext; });
          }
        }
        if (/[^A-Za-z0-9_. -]$/.test(value)) {
          input.style.backgroundColor = "#ffcccc";
          infos.innerHTML='please avoid using unreasonable chars:&nbsp;"'+value.replace(/[A-Za-z0-9_. -]/g, '')+'".';
          infos.style.color="#ff0000";
        } else if (value.replace(/ +/g,'').length < 5) {
          input.style.backgroundColor = "";
          infos.innerHTML="(too&nbsp;short)";
          infos.style.color="#ff0000";
        } else if (value.length > 64) {
          input.style.backgroundColor = "#ffcccc";
          infos.innerHTML="(too&nbsp;long)";
          infos.style.color="#ff0000";
        }
      }

      function handleClick(where) {
        const input = document.getElementById("Group");
        const value = input.value;
        if (!checkInput()) { return; }

        const bgroup = btoa(value);
        document.cookie = "group="+bgroup+"; max-age=1200; path=/; Secure; SameSite=Lax;";

        if (where=="23")       { 
          window.location.href = "https://link.justgiving.com/v1/charity/donate/charityId/3118763?amount=2.00&currency=GBP&tipScheme=TipJar2.1&reference=Escape&exitUrl=https%3A%2F%2F<?=$host?>%2Fregister%2Fdonation%2F%3Fgroup%3D"+bgroup+"%26jgDonationId%3DJUSTGIVING-DONATION-ID&message=Donation%20to%20help%20with%20the%20running%20costs%20of%20Grey%20Wolf%27s%20Sea%20Escape";
        }
        else if (where=="123") {
          window.location.href = "https://link.justgiving.com/v1/charity/donate/charityId/3500590?amount=2.00&currency=GBP&tipScheme=TipJar2.1&reference=Escape&exitUrl=https%3A%2F%2F<?=$host?>%2Fregister%2Fdonation%2f%3Fgroup%3D"+bgroup+"%26jgDonationId%3DJUSTGIVING-DONATION-ID&message=Donation%20to%20help%20with%20the%20running%20costs%20of%20Grey%20Wolf%27s%20Sea%20Escape";
        }
        else if (where=="me")  { // This is looser than the JG route as BMC has no token exchange hence relying on cookies and questions!
          window.location.href = "<?=$coffeelink?>"; //https://buymeacoffee.com/EmceeArsey/e/356685
        }
      }
    </script>
