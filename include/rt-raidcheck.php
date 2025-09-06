<?php
/******************************************************************************
 * rt-raidcheck.php
 ******************************************************************************
 * Version 2.20   last update  2009-09-19
 * ----------------------------------------------------------------------------
 * This file checks for raid records or gives output for raid status
 *****************************************************************************/

/******************************************************************************
 * rtf_raidcheck
 * ----------------------------------------------------------------------------
 * Takes a raid id, and player id (not charid) and returns the record
 * Returns -1 if no playerid is passed, 0 if not exists.
 * Otherwise, returns the record of the raid signup
 *****************************************************************************/
function rtf_raidcheck($raidid, $playerid) {
	if (!$playerid) return -1;
	$rta_rec=rtd_select(rts_db_sign,'*', "raidid='$raidid' AND charid='$playerid'");
	if (!$rta_rec) return 0;
	return $rta_rec;
}

// playercheck here. raidrec gets return from above.
// format: 1-short, 2-medium (w/ name) 3-long
/******************************************************************************
 * rtf_playercheck
 * ----------------------------------------------------------------------------
 * Requires value from rtf_raidcheck, and a format.  Will return the string result
 * format: uses rtf_icon_character format, with a couple extensions:
 *         l-status list (icon)
 *         L-status list (name)
 *         r-role (icon)
 *         R-role (name)
 * (shortcuts): short: l or li (latter on raid list only)
 *              med: l or li n (latter on raid list only)
 *              long: lL or lL in rR (latter on raid list only)
 *****************************************************************************/
function rtf_playercheck($raidrec, $format="short") {
	if (!is_array($raidrec)) {
		if ($format!='long' && !strstr($format,'L')) return null;
		return rtf_show_icons('status',$raidrec,2);
	}
	$rta_sub1=array('/lL/','/l/','/L/','/rR/','@r@','@R@');
	$rta_sub2=array("\x02","\x01","\x03","\x05","\x04","\x06");
	if ($raidrec['status']==1) {
		if ($format=='short') $format='li';
		if ($format=='med') $format='li n';
		if ($format=='long') $format='lL in rR';
	} else {
		if ($format=='short') $format='l';
		if ($format=='med') $format='l';
		if ($format=='long') $format='lL';
	}
	
	
	$rta_perm=rtf_p("show_whiteboard");  //if whiteboard can be seen
	$rta_status="<img src=\""._RT_SYS_WEB."images/status/".$raidrec['status'].".png\" alt=\"";
	switch ($raidrec['status']) {
		case 1: $rta_status.="On raid list"; break;
	}
}
?>