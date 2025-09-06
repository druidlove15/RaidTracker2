<?php 
/*************************************************
 * rti-maint.php
 *************************************************
 * Version 2.0 maintenance check
 *************************************************/
if ($rtv_view=='login' || rtc_priv()==1) return;
$rts_internal['main']="<h1>RaidTracker Maintenance</h1>
<p>RaidTracker is currently undergoing maintenance and is unavailable at the moment.  This may take a few minutes.  We are working to improve RaidTracker.  Should you have any immediate issues, please use your guild resources, which may include forums or contacting your officers until RaidTracker is back online  Thanks for your patience.</p>"
?>