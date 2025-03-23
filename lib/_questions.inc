<?php

# The purpose of this file is to define question content such that questions are extensible. With the exception of Jack and other as yet unwritten logic extensibility is limited to 3 digit answers
#
# Define $teams --- perhaps this will be extensible
# Define Questions as:
#  $questiontext -- the question proper
#  $questionhints -- an array of hints [0] being available immediately in presentation of task [1].. later (perhaps the array elements 1.. are dicts [[ 'delay' => seconds, 'text' => hint], ...]) 
#  $questionSpecifics -- Where a question needs a specific content dependent on team. So the Question would be e.g. $questiontext[$question] . $questionSpecifics[$team][$question] -- use on generic next question page

$teams=['leaders' => 0, 'black' => 1, 'yellow' => 2, 'silver' => 3, 'blue' => 4, 'purple' => 5, 'pink' => 6];

$questionOrder=['Jack','Sirens','Bearings','Transits','Chess'];

$questionPath=[ 
  'Jack' => 'radio/', 
  'Sirens' => 'sirens/',
  'Bearings'=> 'bearings/',
  'Transits'=> 'transits',
  'Chess'=> 'chess/'
];

# Used in transition page
$questionTransition=[ 
  'Jack' => 'Someone in distress',
  'Sirens' => 'Jack found safe and sound',
  'Bearings'=> 'You&apos;ve escaped the sirens enchantment',
  'Transits'=> 'You&apos;ve got your bearings',
  'Chess'=> 'You have your fix'
];

$questionCongrats=[
  'Jack' => [
    "Arrr, ye clever seadogs! Ye've found The Black Pearl, and a finer crew there ne'er was!",
    "Shiver me timbers! Ye've rescued Captain Jack &ndash; break out the rum, er well a'hem, break out the fizzy beverages.",
    "By the powers! Ye've done it! The Black Pearl be back in safe hands!",
    "Avast, ye sharp-eyed scalawags! Ye've set Captain Jack free from a most dire fate!",
    "Blow me down! Ye've cracked the code and found The Black Pearl &ndash; a feat fit for legends!",
    "Yo ho ho! Ye've done the impossible and saved Captain Jack! A fine crew indeed!",
    "By Neptune's beard! Ye've uncovered The Black Pearl! The seas be yers to command!",
    "Great barnacles! Ye've set sail straight to Captain Jack's rescue &ndash; grand work, mates!",
    "Ye salty dogs! Ye've proven yer mettle and reclaimed The Black Pearl from its peril!",
    "Hoist the colours, ye triumphant rogues! Captain Jack be free thanks to yer cunning ways!"
  ],
  'Sirens' => [
    "Blimey! Ye've slipped the sirens' clutches&mdash;no watery grave for ye today!",
    "Avast! Ye've outwitted those wailin' wenches and lived to sail another day!",
    "By Davy Jones' beard! Ye've dodged the sirens' deadly song&mdash;fine work, me hearties!",
    "Shiver me timbers! Ye didn't fall for their sweet-sung trickery&mdash;ye be true seadogs!",
    "Well blow me down! Ye've broken free of the sirens' spell&mdash;tis a feat worthy o' the cheerin'!",
    "Yo ho ho! Ye've escaped their cursed melody&mdash;no foolin' this clever crew!",
    "Ha-ha! The sirens tried their tricks, but ye proved too smart for 'em&mdash;sail on, ye legends!",
    "Thunderin' typhoons! Ye've bested those beguilin' beasties&mdash;ye've got salt in yer veins, indeed!",
    "Arrr, no song can trap a true buccaneer&mdash;ye've escaped with yer wits and yer ship intact!",
    "Ye crafty corsairs! Ye've silenced the sirens' song and made it back to safe waters!"
  ],
  "Bearings" => [
    "Aye! Ye can read a chart and hold a steady course like a true buccaneer!",
    "Shiver me timbers! Ye've got the bearings of a seasoned sea dog!",
    "Arrr! Ye've proven ye can plot a course fit for a captain!",
    "Blimey! Ye can handle a map better than a treasure-huntin' rogue!",
    "Well done, matey! Ye steer true and read the seas like an old salt!",
    "Yo ho ho! Ye've mastered the art of navigation &ndash; no lost ships here!",
    "Avast! Ye've got the skills to sail straight and true!",
    "By thunder! Ye can take a bearing better than a compass-wieldin' corsair!",
    "A fine bit o' chartwork, matey! The sea be no mystery to ye!",
    "No driftin' into Davy Jones' locker for ye &ndash; yer navigation be sharp as a cutlass!"
  ],
  "Transits" => [
    "Aye! Ye've fixed our position &ndash; no driftin' into the unknown for this crew!",
    "Shiver me timbers! With yer sharp reckonin', we be safe from gettin' lost!",
    "Arrr! Ye've got our bearings true &ndash; no wanderin' the briny deep today!",
    "Blimey! Thanks to ye, we ain't sailin' blind &ndash; a fine navigator, ye be!",
    "Well done, matey! Ye've set us right on course &ndash; the sea be ours to command!",
    "Yo ho ho! Ye've charted our way true &ndash; no siren's trickery will lead us astray!",
    "Avast! With yer keen eye, we be steer'n straight as a cannon shot!",
    "By thunder! Ye've nailed our position &ndash; no need for guesswork on these seas!",
    "A fine bit o' navigation, matey! We'll not be vanishing like a ghost ship!",
    "No driftin' off to Davy Jones' locker &ndash; yer course be as steady as a captain's hand!"
  ],
  "Chess" => [
    "Aye! Ye've got a keen eye for tactics and know yer buoys &ndash; we'll sail home safe and sound!",
    "Shiver me timbers! Ye've cracked the puzzle and read the buoys true &ndash; no runnin' aground for us!",
    "Arrr! Ye've the brains of a navigator and the wits of a pirate &ndash; our ship be in good hands!",
    "Blimey! Ye know yer marks and tactics well &ndash; we'll be homeward bound in no time!",
    "Well done, matey! With yer skills, we'll be pilotin' our vessel safe through any waters!",
    "Yo ho ho! Ye be a master of the sea's secrets &ndash; no reef nor rock shall trouble us!",
    "Avast! Ye read the buoys like a true sea dog &ndash; the safest course be ours to take!",
    "By thunder! Yer sharp mind and steady course will see us home with nary a scratch!",
    "A fine bit o' navigatin', matey! No fog nor foul water will keep us from port now!",
    "No driftin' off course with ye aboard &ndash; our ship be safe under yer watchful eye!"
  ]
];

$questionsStay=[ # This is not the end Array
  'But alas, the true treasure lies not within this chest, but further ahead on your journey. Take this as a sign that you are on the right path, and may your compass always point you true. The next clue awaits you where the stars kiss the horizon and the waves whisper secrets of the deep. <a href="/escape/">Click here!</a>',
  'Yet, ye&apos;ll find no true riches here, for the grandest booty lies still ahead on yer voyage. Take this as a sign that yer course be true, and may yer compass ne&apos;er lead ye astray. The next clue waits where the stars meet the sea, and the waves whisper their secrets to the moon. <a href="/escape/">Click here</a>, ye daring seadog!',
  'But don&apos;t be thinkin&apos; this be the grand prize&mdash;nay, the real plunder lies yet beyond the horizon. Take this as proof ye sail the right tides, and let not false harbours lead ye astray.
The next hint be waitin&apos; where the sky kisses the brine, and the ocean hums its ancient shanty. <a href="/escape/">Click here</a>, ye bold buccaneer!',
  'But don&apos;t be celebratin&apos; too soon&mdash;this be naught but a marker on yer path. The grandest hoard be still out yonder. Stay the course, and trust yer bearings. The next clue be where the heavens meet the deep blue, and the waves whisper secrets only the bravest dare hear. <a href="/escape/">Click here</a>, ye gallant adventurer!',
  'Yet, the finest treasure remains just beyond yer grasp. This be but a waymark on yer grand adventure. Hold fast, and follow the signs! Yer next clue lies where the sky doth meet the sea, and the waves murmur their mysteries. <a href="/escape/">Click here</a>, ye keen-eyed navigator!',
  'But the real prize ain&apos;t here, oh no&mdash;yer greatest haul lies further on. Take this as proof yer course be set true! The next clue waits where the stars meet the brine, and the tide sings of ancient lore. <a href="/escape/">Click here</a>, ye daring rogue!',
  'But keep yer spyglass sharp, for the true bounty lies ahead, not here. This be but a signpost on yer quest for glory. Yer next clue be where the firmament meets the foam, and the sea hums its ageless ballad. <a href="/escape/">Click here</a>, ye dauntless explorer!',
  'But this ain&apos;t the end of the voyage, matey&mdash;nay, the real riches lie further ahead! Stay true to yer heading, and ne&apos;er let doubt cloud yer course. Seek yer next clue where the night&apos;s lanterns kiss the tide, and the ocean whispers its oldest tales. <a href="/escape/">Click here</a>, ye steadfast mariner!',
  'But don&apos;t be thinkin&apos; this be the prize, for the true fortune be still on the horizon. Let this be a sign ye sail the right waters! The next hint awaits where the heavens embrace the sea, and the waves speak of hidden wonders. <a href="/escape/">Click here</a>, ye fearless wayfarer!',
  'But the finest gold ain&apos;t here&mdash;nay, it be further along yer journey. Take this as proof yer course be just! The next clue be hid where the sky weds the briny deep, and the waves tell tales of old. <a href="/escape/">Click here</a>, ye bold-hearted pirate!',
  'But keep yer eyes on the horizon, for the grandest prize lies still ahead! Stay true to the course, and ne&apos;er let doubt be the storm that wrecks ye. Yer next clue be where the stars embrace the deep, and the waves murmur their ancient secrets. <a href="/escape/">Click here</a>, ye daring rogue!'
];

$questionsEnd=['You have successfully navigated the narrow straights of this Sea Escape. Now go and nag your leader for the plunder ;-).','I hope ye&apos;ve had a rollickin&apos; good time navigatin&apos; these acursed waters, and, as ye set yer sails for new horizons, keep yer wits sharp and yer compass true, for grander adventures always lie ahead on the high seas! Now hoist that there anchor, and away with ye, and may ye always find a fair wind and following seas!','Fare ye well,', 'Yours in sea-scouting,', 'Cptn. Grey Wolf.','<a href="/escape/">Click here...</a>'];


$questionFortune=[ # Good luck we're all counting on you array
  'May the winds be ever at yer back, and fortune smile upon yer sails...',
  'May fair winds ever fill your sails, ...',
  'May the winds be forever in yer favour, and the tide at yer stern...',
  'May fair winds and full sails guide ye true...',
  'May the wind fill yer sails and the stars light yer way...',
  'May the seas be ever kind, and the breeze forever strong at yer back...',
  'May Neptune favour ye, and may fortune smile on yer journey...',
  'May the trade winds be ever in yer favour, and the tides ever in yer favour...',
  'May yer sails stay full, and yer anchor ne&apos;er weigh ye down...',
  'May fair winds guide ye, and may fortune favour yer plunder...',
  'May the seas be ever gentle, and the winds forever at yer back...'
];

$questionHints=[];
$questionHints['Jack']     = [ 'To turn the radio on rotate the Volume knob.', 'Turn the squelch to just remove the static.','Write down Jack&apos; position from his radio distress message.' ];
$questionHints['FindJack'] = [ 'Scrollin&apos; this way that way, forward and backward, to make the numbers match.', 'Zoom in and out for finer and coarser, then you&apos;ll get yer catch', 'Closer and farther, smaller or larger, where do you think he be','A bottle o rum to fill his tum, Yo! that&apos;s the life for he.' ];
$questionHints['Sirens']   = [ 'Each team has their; own bar.', 'Notes = numbers',
                               '1, a deer, a female deer; 2 a drop of golden sun; 3 a name I call myself; 4 a long long way to run; 5, a needle pulling thread; 6 a note to follow five; 7, a drink with jam and bread; which brings us back to 1?',
                               'If you were team 7, look at bar 7 "Do Re Me", Your code would come from notes G F&#9839; E (in the key of C) => 543'];
$questionHints['Bearings'] = [ 'Drag the plotter alongside the points. Turn the Compass rose to align with North on the chart. Read off the bearing (assuming magnetic variation of 0&deg;).', 'Stuck?<br /><a target="_blank" href="/help/bearings/">Watch this video</a>.' ];
$questionHints['Transits'] = [ 'Select the draw icon and draw lines to mark the transits.', 'Stuck? <a target="_blank" href="/videos/transit/">watch this video.</a>.', 'Stuck?<br /><a target="_blank" href="/help/transits/">Watch this video</a>.' ];
$questionHints['Chess']    = [ 'Move the Queen Of The Seas like the black queen in chess. Clicking on the squares might help.', 'Avoid danger markers.', 'Pass only to the N,E,S,W of corresponding Cardinal markers.', 'Fairway markers are a good buoy to aim for.', 'Keep within Lateral marked Channels.'];

$questionText=[];
$questionText['Jack']     = 'You are on radio watch. Turn the radio on, make sure to monitor channel 16 (the emergency channel). Turn squelch so that the noise is just gone. Listen and record Jack&apos;s position.';
$questionText['Sirens']   = 'As you head back to shore, you hear a witchy song floating in on the sea breeze. You are inexplicably drawn towards the haunting cacophony.';
$questionText['Bearings'] = 'Examine your chart and use the plotter to take the chart bearing to unlock the next chest.';
$questionText['Transits'] = 'You can get a very accurate fix on your position when you line up two visible distant objects by eye, especially if they are distant to each other. This is called a &apos;transit&apos;. Drawing a straight line through those objects&apos; symbols on the chart together with a bearing, another transit or say a depth sounding can be used to fix your position reasonably accurately.';
$questionText['Chess']    = 'Your move! Guide the Queen of the Sea safely to port to unlock your next chest...';

$answerText=[];
$answerText['Jack']     = 'Well done shipmates! Jack is safe and is recovering in sick bay. You have access to a chart and have instructions. Now turn about and return to harbour.';
$answerText['Sirens']   = '';
$answerText['Bearings'] = 'Now you are back, you will need to examine those charts to get your bearings.';
$answerText['Transits'] = 'You can get a very acurate position line when you line up two visible objects. This is called a &apos;transit&apos;.';
$answerText['Chess']    = 'Your move! Guide the Queen of the Sea safely to port to unlock your next chest...';

$questionSpecifics=[];
$questionSpecifics['black']=[];
$questionSpecifics['black']['Jack']     = 'Rescue Jack';
$questionSpecifics['black']['Sirens']   = 'Remember you are team number 1.';
$questionSpecifics['black']['Bearings'] = 'Find the bearing from North Chorltonden Major Light to the Sting of the North Chorltonden Bee.';
$questionSpecifics['black']['Transits'] = 'Your code is the Longitude of Position at Southern coincidence of Transit of N. Chorltonden Major Light through Radio Mast, and 20m depth contour.';
$questionSpecifics['black']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['yellow']=[];
$questionSpecifics['yellow']['Jack']     = 'Rescue Jack';
$questionSpecifics['yellow']['Sirens']   = 'Remember you are team number 2.';
$questionSpecifics['yellow']['Bearings'] = 'Find the bearing from North Chorltonden Radio Mast to the Sting of North Chorltonden Bee.';
$questionSpecifics['yellow']['Transits'] = 'Your code is the Depth at coincidence of Transit North Chorltonden Major light through Squiffy Woggle Inn and Radio Mast through Church Spire.';
$questionSpecifics['yellow']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['silver']=[];
$questionSpecifics['silver']['Jack']     = 'Rescue Jack';
$questionSpecifics['silver']['Sirens']   = 'Remember you are team number 3.';
$questionSpecifics['silver']['Bearings'] = 'Find the bearing from Reef Island Major Light to the top of the Ship&apos;s Mainmast';
$questionSpecifics['silver']['Transits'] = 'Your code is the Depth at the coincidence of the transit line of N. Chorltonden Radio mast through Squiffy Woggle Inn and S. Chorltonden Water Tower and Lateral Starboard Buoy at 53&deg;23&apos;.6N,2&deg;25&apos;.2W';
$questionSpecifics['silver']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['blue']=[];
$questionSpecifics['blue']['Jack']     = 'Rescue Jack';
$questionSpecifics['blue']['Sirens']   = 'Remember you are team number 4.';
$questionSpecifics['blue']['Bearings'] = 'Find the bearing from North Chorltonden Major Light to the top of the Ship&apos;s Mainmast';
$questionSpecifics['blue']['Transits'] = 'North of the isle of North Chorltonden, your code is the at the point marked by the transit from N. Chorltonden Wind Turbine and Water Tower crossing and 30m depth contour.';
$questionSpecifics['blue']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['purple']=[];
$questionSpecifics['purple']['Jack']     = 'Rescue Jack';
$questionSpecifics['purple']['Sirens']   = 'Remember you are team number 5.';
$questionSpecifics['purple']['Bearings'] = 'Find the bearing from Reef Island Major Light to Kraken (between eyes)';
$questionSpecifics['purple']['Transits'] = 'Your code is the Longitude at coincidence of Transit from N. Chorltonden Wind Turbine and Hough End building with 30m depth contour.';
$questionSpecifics['purple']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['pink']=[];
$questionSpecifics['pink']['Jack']     = 'Rescue Jack';
$questionSpecifics['pink']['Sirens']   = 'Remember you are team number 1.';
$questionSpecifics['pink']['Bearings'] = 'Find the bearing from North Chorltonden Major Light to Kraken (between eyes)';
$questionSpecifics['pink']['Transits'] = 'Find the Chart Datum (a number on the chart marking the depth of the sea at that point.) at coincidence of transit from N Chorltonden through Fairway Buoy, and transit of North and South Cardinal Buoys Between Reef Island and Brownsea Island.';
$questionSpecifics['pink']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';

$questionSpecifics['leaders']=[];
$questionSpecifics['leaders']['Jack']     = 'Rescue Jack';
$questionSpecifics['leaders']['Sirens']   = 'Arrr, esteemed masters o&apos; the ship&mdash;venerable leaders&mdash;while atempting this challenge you should assume membership of team 7.';
$questionSpecifics['leaders']['Bearings'] = 'Find the Bearing from the Wind Farm to the Prow of the ship Queen of the Seas.';
$questionSpecifics['leaders']['Transits'] = 'Find your longitude _&deg;__&apos;: You have a depth sounding of 20m and you are on the transit of the two cardinal buoys (&apos;East&apos; and &apos;South&apos;) marking the drying areas to the South East of North Chorltonden.';
$questionSpecifics['leaders']['Chess']    = 'Think Chess; Think QUEEN of the Seas.';
?>
