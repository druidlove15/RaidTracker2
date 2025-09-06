<?php
$rta_step=7;
include _RT_SYS_HOME . "/user/rt.php"; // get old file
// ***************** Step 7, done
$rts_internal['main']="<h1>Installation complete</h1>\n<p>RaidTracker is now installed on your server.  You must now remove or rename the /install folder before your guild can use RaidTracker.  
Then enjoy RaidTracker.</p>\n<p>For further support, please check out "
. url('http://raidtracker.druidlove.com','the Official RaidTracker site').".</p>\n";
$rts_internal['main'].="<p>Once the /install folder is removed, you can go to ".url('.','RaidTracker home').".</p>\n";
?>