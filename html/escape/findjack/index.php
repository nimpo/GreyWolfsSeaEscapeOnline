<?php
include 'questions.inc';
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Where's the Black Pearl?</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKMZ97r_bRIfL8wL4_aZwWX9tj_wd5rfw"></script>
    <script>
      function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: 51.4778941, lng: -0.0014700 }, // Initial centre (Grenwich Meridian)
          zoom: 2,
          restriction: {
                    latLngBounds: {
                        north: 90,
                        south: -90,
                        west: -180,
                        east: 180,
                    },
                    strictBounds: false, // Set to true if you want to strictly prevent moving beyond bounds
                },
          mapTypeId: "satellite",
          tilt: 0,
          styles: [ { featureType: "poi", stylers: [{ visibility: "off" }],},],
          disableDefaultUI: true, // Disable default controls
          zoomControl: true, // Keep zoom
        });

        map.addListener("zoom_changed", function () { checkSatelliteTiles(); });
        map.addListener("tilesloaded", function () { checkSatelliteTiles(); });

        let satelliteFailed = false;
        function checkSatelliteTiles() {
          let zoom = map.getZoom();
          let center = map.getCenter();
          let tileCoord = latLngToTile(center.lat(), center.lng(), zoom);
          let tileUrl = "https://mt1.google.com/vt/lyrs=s&x="+tileCoord.x+"&y="+tileCoord.y+"&z="+zoom+"&hl=en";
          let testTile = new Image();
          testTile.src = tileUrl;
          testTile.onerror = function () { if (!satelliteFailed) { map.setMapTypeId("terrain"); satelliteFailed = true; } };
          testTile.onload = function () { if (satelliteFailed) { map.setMapTypeId("satellite"); satelliteFailed = false; } };
        }

        function mod(n,m,o=m) {  // remainder function "%" != "mod()" function, because negative remainders.
          let r=n%m;
          if (r==-o) {return o;}
          if (r>o)   {return r-m;}
          if (r+m<o && r!=m) {return r+m;}
          return r
        }

        function latLngToTile(lat, lng, zoom) {
            let scale = 1 << zoom;
            let x = Math.floor((mod((lng + 180),360) / 360) * scale);
            // Mercator y = ln (tan(phi) + sec(phi)) ; scaled such that y=0 at 85.0511 and 1 at -85.0511 (web mercator)
            let y = Math.floor(((1-Math.log(Math.tan(lat*Math.PI/180)+1/Math.cos(lat*Math.PI/180))/Math.PI)/2)*scale);
            return { x, y };
        }

        const dmsDisplay = document.getElementById("dms");
        
        // Function to convert decimal degrees to DMS format
        function toDMS(deg, type) {
          let d = Math.floor(Math.abs(deg)); // Degrees
          let m = Math.floor((Math.abs(deg) - d) * 60); // Minutes
          let s = ((Math.abs(deg) - d - m / 60) * 3600).toFixed(2); // Seconds
          if (s >= 59.995) {s=0;m+=1}
          if (m > 59) {m=0;d+=1}
          let h = deg >= 0 ? (type === "lat" ? "N" : "E") : (type === "lat" ? "S" : "W");
          return ""+pad(d,(type==="lat"?2:3))+"&deg&nbsp;"+pad(m,2)+"'&nbsp;"+pad(s,2,2)+"&quot;&nbsp;"+h;
        }

        // Function to convert decimal degrees to DMS format
        function toDDM(deg, type) {
          let d = Math.floor(Math.abs(deg)); // Degrees
          let m = ((Math.abs(deg) - d) * 60).toFixed(2); // Minutes
          if (m >= 59.995) {m=0;d+=1}
          let h = deg >= 0 ? (type === "lat" ? "N" : "E") : (type === "lat" ? "S" : "W");
          return ""+pad(d,(type==="lat"?2:3))+"&deg&nbsp;"+pad(m,2,2)+"'&nbsp;"+h;
        }

        class CustomOverlay extends google.maps.OverlayView {
          constructor(bounds, svgContent, map) {
            super();
            this.bounds = bounds;
            this.svgContent = svgContent;
            this.div = null;
            this.setMap(map);
          }

          onAdd() {
            const div = document.createElement("div");
            div.style.border = "none";
            div.style.borderWidth = "0px";
            div.style.position = "absolute";
            div.style.pointerEvents = "auto";
            div.innerHTML = this.svgContent;

            div.addEventListener("click", (e) => {
              e.stopPropagation();
              this.showPopup(e);
            });

            this.div = div;
            this.getPanes().overlayMouseTarget.appendChild(div);
          }

          draw() {
            const overlayProjection = this.getProjection();
            const sw = overlayProjection.fromLatLngToDivPixel( this.bounds.getSouthWest());
            const ne = overlayProjection.fromLatLngToDivPixel( this.bounds.getNorthEast());

            const width = (ne.x - sw.x);
            if (this.div) {
              const height = (sw.y - ne.y);
              if (width<11) {this.div.style.visibility="hidden";}  // Change to be based on ship size!
              else {this.div.style.visibility="visible";}
              this.div.style.left = sw.x + "px";
              this.div.style.top = ne.y + "px";
              this.div.style.width = width + "px";
              this.div.style.height = height + "px";
              this.div.style.display = "block";
              this.div.style.margin = "auto";
              const svg = this.div.querySelector("svg");
              if (svg) {
              svg.setAttribute("width", ""+width+"px");
              svg.setAttribute("height", ""+height+"px");
              }
            }
            const centre = map.getCenter();
            if(this.bounds.contains(centre) && width>20) { this.showPopup(); }
            else if (document.body.contains(popup)) { popup.remove(); }
          }

          onRemove() {
            if (this.div) {
              this.div.parentNode.removeChild(this.div);
              this.div = null;
            }
          }

          updateBounds(newBounds) {
            this.bounds = newBounds; // Update the bounds
            this.draw(); // Redraw the overlay
          }

          showPopup() {
            if(popup === undefined) {return;}
            const overlayProjection = this.getProjection();
            const overlayPosition = this.div.getBoundingClientRect();

            // Map the pixel coordinates to LatLng
            const sw = overlayProjection.fromLatLngToDivPixel(this.bounds.getSouthWest());
            const ne = overlayProjection.fromLatLngToDivPixel(this.bounds.getNorthEast());
            let ctr=this.bounds.getCenter();

          // Calculate popup position and create the popup
            const popupWidth = window.innerWidth*0.8; // Fixed width of the popup
            const popupHeight = window.innerHeight*0.8; // Fixed width of the popup
            popup.style.background = "white";
            const centre = map.getCenter();
            let form='<form action="/escape/foundjack/" method="POST" style="display: inline;"><input type="hidden" name="team" value="1"><input type="hidden" name="Q" value="0"><input type="hidden" name="lat" value="'+centre.lat().toFixed(6)+'" /><input type="hidden" name="lng" value="'+centre.lng().toFixed(6)+'" /><button>here</button>';
            popup.innerHTML=`<div style="margin: auto;"><div>Ah! Marvelous! You've found me. I was getting worried; nearly all the rum is gone! I had a backup plan though; course I did! </div><div style="text-align:right">Now, do us both a favour and click `+form+`. Your quest awaits, savvy?...</div></div>`;
            popup.style.position = "absolute";
            popup.style.width = ""+popupWidth+"px";
            popup.style.left = ""+((window.innerWidth-popupWidth)/2)+"px";
            popup.style.top = ""+(window.innerHeight/4)+"px";
            popup.style.padding = "10px";
            popup.style.border = "1px solid black";
            popup.style.borderRadius = "5px";
            popup.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.3)";
            // Add the popup to the body
            if (!document.body.contains(popup)) { document.body.appendChild(popup); }
          }
        }

        const popup=document.createElement("div");
        popup.id = "popup";

        const bounds = new google.maps.LatLngBounds({lat:0,lng:0},{lat:0,lng:0});
        const svgContent='<img src="/assets/circle-bottom-right.svg" width="22px" height="22px"/>';
        const overlay=new CustomOverlay(bounds, svgContent, map);
        var size=100;
        setTimeout(() => {
          const xhr = new XMLHttpRequest();
          xhr.open("GET", "bounds/", true);
          xhr.onload = function () {
            if (xhr.status === 200) {
              const response = JSON.parse(xhr.responseText);
              const cosLat=Math.cos(response.lat*Math.PI/180);
              if ( response.hasOwnProperty("len") ) { size=response.len; }
              size= response.len ?? 1;  // default to 1 metre!
              const LatNE=response.lat+(size/222640); // metres per deg lat
              const LngNE=response.lng+(size/222640/cosLat); // metres per deg lon
              const LatSW=response.lat-(size/222640);
              const LngSW=response.lng-(size/222640/cosLat);
              const newBounds = new google.maps.LatLngBounds(
                new google.maps.LatLng(LatSW, LngSW),
                new google.maps.LatLng(LatNE, LngNE)
              );
              // Update the overlay with the new bounds
              overlay.updateBounds(newBounds);
              updateDMS();
            }
          };
          xhr.send();
        }, 500);

        function positionCrosshairs() {
          const mapBounds = document.getElementById("map").getBoundingClientRect();
          crosshairs.style.top = ""+((mapBounds.top+mapBounds.height/2)-20)+"px";
          crosshairs.style.left = ""+((mapBounds.left+mapBounds.width/2)-20)+"px";
        }

        function pad(nstr,b=1,a=0) {
          let n=Number(nstr);
          let p="0".repeat(b);
          let s=p+n.toFixed(a);
          return s.substr(s.length-(b+(a>0?a+1:0)));
        }

        // Function to update the DMS display
        function updateDMS() {
          const center = map.getCenter();
          const lat = center.lat();
          const lng = center.lng();
          if (size>20) {
            dmsDisplay.innerHTML = "Find Cptn Jack! Cross-hairs are currently trained on: "+toDDM(lat, "lat")+", "+toDDM(lng, "lng");
          } else
            dmsDisplay.innerHTML = "Find Cptn Jack! Cross-hairs are currently trained on: "+toDMS(lat, "lat")+", "+toDMS(lng, "lng");
          }

        // Listen to map events to update DMS
        map.addListener("center_changed", updateExtraView);
        map.addListener("idle", updateExtraView);

        function keepWithinBounds() {
          //return;
          let center = map.getCenter();
          let lat = center.lat();
          let lng = mod(center.lng(),360,180);
          if (lat > 85.0511) lat = 85.0511;
          if (lat < -85.0511) lat = -85.0511;
          if (lat !== center.lat()) { map.panTo(new google.maps.LatLng(lat, lng)); }
          if (lng !== center.lng()) { map.setCenter(new google.maps.LatLng(lat, lng)); }
        }

        function updateExtraView() {
          keepWithinBounds();
          updateDMS();
          positionCrosshairs();
        }
        const polarOverlay = new google.maps.ImageMapType({
          getTileUrl: function (coord, zoom) {
            const maxTileIndex = (1 << zoom) - 1;
            const maxGoogleTileY = 0;
            if (coord.y == -1 ) { return "/assets/pb3.png"; } 
            else if ( coord.y == (maxTileIndex + 1 )  ) { return "/assets/pg1.png"; }
            return null;
          },
          tileSize: new google.maps.Size(256, 256),
          minZoom: 0,
          maxZoom: 22,
          name: "Polar Overlay"
        });
        map.overlayMapTypes.push(polarOverlay);

        const animatedDiv = document.getElementById("animatedDiv");
        const helpDiv = document.getElementById("HelpText");
        const toggleButton = document.getElementById("toggleButton");

        let toggleHelp=showHelp;
        function hideHelp() {
          animatedDiv.style.left = ""+(25-animatedDiv.offsetWidth)+"px";
          toggleButton.innerHTML = "&#9656;";
          toggleHelp=showHelp;
        }
        function showHelp() {
          animatedDiv.style.left = "10px";
          toggleButton.innerHTML = "&#9666;";
          toggleHelp=hideHelp;
        }
        function resetHelp(text="") {
          animatedDiv.style.left = "-200px";
          helpDiv.innerHTML=text;
          toggleHelp=showHelp;
        }
        toggleButton.addEventListener("click", function () {toggleHelp();});

        function resetTimedHint(text,delay) {
          resetHelp();
          return setTimeout( () => { resetHelp(text); showHelp() },delay);
        }

<?php
  echo "        const otherhinttimers=[];";
  for ($i = 0; $i < count($questionHints['Sirens']); $i++) {
    echo "        otherhinttimers.push(otherhinttimers,setTimeout( () => { resetTimedHint('".$questionHints['FindJack'][$i]."',500); },".(($i+1)*20000)."));";
  }
?>
      }

      window.addEventListener("resize", () => {
        const popup = document.getElementById("popup");
        if (popup) {
          popup.style.width = ""+(window.innerWidth*0.8)+"px";
          popup.style.left = ""+((window.innerWidth - popup.offsetWidth)/2)+"px";
        }
      });

      document.addEventListener("keydown", (event) => { // Bring back focus to map when the relevant key shortcuts are pressed (bit of a hack!)
        const keys = ["ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", "+", "-", "Equal", "Minus", "Home", "End", "PageUp", "PageDown"];
        if (keys.includes(event.key)) {
          const mapDiv = document.querySelector('[aria-roledescription="map"]');
          if (mapDiv && mapDiv !== document.activeElement) {
            mapDiv.focus();
            setTimeout(() => { // Replay the keystroke (as if map was already focussed when clicked -- avoids missed click )
                const newEvent = new KeyboardEvent("keydown", {
                    key: event.key,
                    code: event.code,
                    keyCode: event.keyCode,
                    which: event.which,
                    bubbles: true
                });
                mapDiv.dispatchEvent(newEvent);
            }, 50);
          }
        }
      });
    </script>
    <style>
      #map {
        height: 100%;
        position: relative;
      }
      #dms {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.8);
        padding: 5px 10px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        z-index: 1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }
      #crosshairs {
        position: absolute;
        width: 40px;
        height: 40px;
        z-index: 3;
        pointer-events: none;
      }
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
            #Instructions {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.8);
        padding: 5px 10px;
        border-radius: 5px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        z-index: 1;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
      }
      #animatedDiv {
        position: fixed;
        top: 50px;
        left: -200px;
        width: fit-content;
        max-width: 200px;
        background: rgba(255, 255, 255, 0.8);
        padding: 5px 2px 5px 10px;;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        border-radius: 5px;
        font-family: Arial, sans-serif;
        font-size: 16px;
        z-index: 1;
        transition: left 1s ease-in-out;
        overflow-wrap: break-word;
        display: flex;
        align-items: center;
      }
      #toggleButton {
        margin-left: 10px;
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: black;
      }
      #toggleButton:focus {
        outline: none;
      }
    </style>
  </head>
  <body onload="initMap()">
    <div id="map">
    </div>
    <div id="dms"></div>
    <div id="crosshairs">
      <!-- Crosshairs SVG -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40">
        <circle cx="20" cy="20" r="15" fill="none" stroke="red" stroke-width="1" />
        <line x1="20" y1="0" x2="20" y2="40" stroke="red" stroke-width="1" />
        <line x1="0" y1="20" x2="40" y2="20" stroke="red" stroke-width="1" />
      </svg>
    </div>
    <div id="animatedDiv"><div id="HelpText" style="display: inline-block">Hints placed here.</div><button id="toggleButton">&#9656;</button></div>
  </body>
</html>

