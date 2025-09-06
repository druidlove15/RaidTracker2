<?php
/********************************************************
 *             rt-lib.php
 ********************************************************
 * Contains all functions needed to simplify RT
 * ------------------------------------------------------
 * all main functions start with rtf_
 * all form functions start with rtff_
 * all html functions start with rthf_
 *******************************************************/
//include_once "rti-db.php";
// function role returns a role for any given place
// form is 1 to make optional selects, 2 makes it selected
function rtf_role ($role, $form=0, $sel=0) {
switch ($role) {
	case 1: return "Tank";
	case 2: return "Healer";
	case 3: return "Melee DPS";
	case 4: return "Range DPS";
	case 0: return "(default)";
}
return "UNKNOWN!";
}

//form function to print out the roles.
function rtff_role ($name, $sel=0, $req=0) {
	$s=group('Role:','label','','heading spaceleft',"for=\"$name\"")."<span class=\"spaceright\"><select name=\"$name\" id=\"$name\">\n";
//	$s="<span class=\"heading\">Role:</span> <select name=\"$name\">\n";
	if ($req) $req=1;
	for ($i=$req;$i<5;$i++) {
		$s.="<option value=\"$i\"".($sel?" selected":"").">"
		. rtf_role($i)."</option>\n";
	}
	$s.="</select>\n</span>\n";
	return $s;
}
// looks up a character and retrieves its link to character with class
// 2nd parameter takes -1 for default role, 0-hide, 1-4 for alternate role
function rtf_char($charid, $role=0) {
	if (function_exists('rt_GetAcct')){
		$sql="SELECT * FROM raid_char WHERE id=$charid";
		$t=GetRec($sql);
		$t=$t[1];
		$rts_syspath="/rtest";
	} else {
		global $rts_syspath;
		$t=rtd_select(rts_db_char,'*',"id=$charid");
	}
	$ln=rtf_classicon($t['class']). "<a href=\"".rts_syspath."/character/?charid=$t[account]\">$t[char]</a>";
	if ($role==-1) $role=$t[role];
	if ($role) $ln.="-".rtf_role($role);
	return $ln;
}

// function to create a select box for roles.
// $default allows a specific role as selected
// third allows optional choice (default)
// phasing this out for rtff_role
function rtf_rolesel($name, $default=0, $nc=0) {
return rtff_role($name, $default, $nc);
}

//rtf_class.  lengthens class name
function rtf_class ($c, $form=0, $sel=0) {
switch ($c) {
	case '0': $s="(default)"; break;
	case 'Dk': $s="Death Knight"; break;
	case 'Dr': $s="Druid"; break;
	case 'Hu': $s="Hunter"; break;
	case 'Ma': $s="Mage"; break;
	case 'Pa': $s="Paladin"; break;
	case 'Pr': $s="Priest"; break;
	case 'Ro': $s="Rogue"; break;
	case 'Sh': $s="Shaman"; break;
	case 'Wl': $s="Warlock"; break;
	case 'Wr': $s="Warrior"; break;
	default  : $s="UNKNOWN!";
}
if ($form)
	$s="<option value=\"$c\"".($sel?" selected":"").">$s</option>\n";
return $s;
}

//shows icon with alt text
function rtf_classicon($c) {
global $rts_syspath;

return "<img src=\"".rts_syspath."/images/class/$c.png\" alt=\"(".rtf_class($c).") \" />";
}
// function similar to rolesel above
function rtf_classsel($name, $default="", $nc=0) {
// ---- array for classes.  need better way.
$cl=array('0','Dk','Dr','Hu','Ma','Pa','Pr','Ro','Sh','Wl','Wr');
$s="<span class=\"heading spaceleft\">Class:</span> <select name=\"$name\" class=\"spaceright\">\n";
for ($i=1-$nc; $i<count($cl); $i++)
	$s.=rtf_class($cl[$i], 1, ($default==$cl[$i]));
$s.="</select>\n";
return $s;
}

//datetime showings
function rtf_datetime($timestamp, $dt) {
	if ($x=(function_exists('rt_GetAcct')?rt_GetAcct():rtc_acct())) {
		if (function_exists('rt_GetAcct')) {
			$dtformat=GetRec("SELECT datef, timef FROM raid_account WHERE id=$x");
			$datef=$dtformat[1]['datef'];
			$timef=$dtformat[1]['timef'];
		} else{
			$dtformat=rtd_select(rts_db_acct,'datef, timef',"id=$x");
			$datef=$dtformat['datef'];
			$timef=$dtformat['timef'];
		}
	} else {
		$datef="l Y-m-d";
		$timef="H:i";
	}
	switch ($dt) {
		case 'dt': $s="$datef $timef"; break;
		case 'td': $s="$timef $datef"; break;
		case 'd': $s="$datef"; break;
		case 't': $s="$timef"; break;
		default: echo "INVALID FORMAT: $dt"; exit;
	}
	return date($s, $timestamp);
}
function rtf_status ($st, $wb=0) {
switch ($rt) {
	case 7: if ($wb) return "WB: Reserve";
	case 3: return "Reserve";
	case 6: if ($wb) return "Whiteboard";
	case 2: return "Available";
	case 4: return "Withdrawn";
	case 5: return "Removed";
	case 1: return "Raid list";
}
}
function rtf_statusicon($st, $char=0, $short=1) {
	global $rts_syspath;
	$t="<img src=\"".rts_syspath."/images/$st.png\" alt=\"(".rtf_status($st).") \" />";	
//	$t="<img src=\"".rts_syspath."/images/status/$st.png\" alt=\"(".rtf_status($st).") \" />";
	if ($st==1 && $short) $t.=rtf_char($char);
	else if ($st==1 && $char) $t.=rtf_classicon ($char);
	return $t;
}
function rtf_p($nam, $id=0) {
	$s="SELECT `property`, `value` FROM ".rts_db_perm." WHERE `property`='$nam'";
	if (function_exists('GetRec')&& function_exists('rt_GetRank')) {
		$t=GetRec($s);
		$cv=$t[1]['value'];
		$chk=rt_GetRank();
		$rta_acctchk=rt_GetAcct();
	} else {
		$chk=rtc_priv();
		$cv=rtd_selcol(rts_db_perm,'value',"property='$nam'");
		$rta_acctchk=rtc_acct();
	}
	if ((abs($cv)>=$chk)) return true;
	if (($cv<0 && $id==$rta_acctchk)) return true;
	return false;
}
function rtf_pcheck($nam) {
	global $rts_syspath;
	if (rtf_p($nam)) return true;
	echo "<h1>Access denied</h1>\n<p>You do not have the permissions to be here."
	.br().url(rts_syspath,"Return to RaidTracker Home")."</p>\n";
	return false;
}
//Error routine for RT.
function rtf_error($code=0, $s='') {
echo "<h1>Application error</h1>\n";
echo "Code error $code: ";
switch ($code) {
	case 101:  //out of range or invalid parameter name.  100+ are stoppable
	case 1:
		echo "Parameter out of range or invalid for: $s";
		break;
	case 110:  // logged out error
	case 10:
		echo "Not logged in, or no cookies saved for: $s";
		break;
	case 102:  // Frozen time error
	case 2:
		echo "Time beyond frozen time";
		break;
	case 103:  //nothing to display
	case 3:
		echo "Nothing able to be displayed.  $s";
		break;
	case 104:
	case 4:
		echo "Invalid command: $s";
		break;
	case 5:
		echo "Invalid log-in:  $s";
		break;
	case 106:  //parameter missing
	case 6:
		echo "Missing $s";
		break;
	case 107:
	case 7:
		echo "You do not have the permissions or are logged out";
		break;
	case 108:
	case 8:    //command failed
		echo "Command failed: $s";
		break;
	case 120:  //query error
	case 20:
		echo "Query failed: $s";
		break;
	case 121:
		echo "Database connection failed";
		break;
	case 122:
	case 22:
		echo "Raid not found.";
		break;
	case 198:
		echo "Invalid parameter - possible breach in system.  You have been logged.";
		break;
	case 199:
		echo "File not found: $s";
		break;
}
if ($code/100>=1) {
	echo br()."--Stopping.  Please hit back to return.";
	exit;
}
echo br();
}
function rtf_grank($rank) {
global $rtg_grank;
if (!isset($rtg_grank)){
	$rtg_grank=rtd_select(rts_db_keys,array('name'),"category='guild'","ORDER BY id");
}
return $rtg_grank[$rank-1]['name'];
}
function rtf_rtrank($rank) {
global $rtg_rtrank;
if (!isset($rtg_rtrank)){
	$rtg_rtrank=rtd_select(rts_db_keys,array('name'),"category='rt'",'ORDER BY id');
}
return $rtg_rtrank[$rank]['name'];
}
function rtff_rtrank($nam, $rank=0, $opt=1) {
global $rtg_rtrank;
$rank=abs($rank);
if (!$rtg_rtrank) rtf_rtrank(1);
$t="<select name=\"$nam\">\n";
for ($i=0; $i<12;$i++){
	if (!$i){
		if ($opt==1)
			$t.="<option value=\"0\"".($i==$rank?' selected="selected"':'').">---select---</option>\n";
		if ($opt==0)
			$t.="<option value=\"0\"".($i==$rank?' selected="selected"':'').">None</option>\n";
	} else if ($i==11 && !$opt)
		$t.="<option value=\"11\"".($i==$rank?' selected="selected"':'').">Everyone</option>\n";
	else if ($i<=count($rtg_rtrank))
		$t.="<option value=\"$i\"".($i==$rank?' selected="selected"':'').">".rtf_rtrank($i-1)."</option>\n";
}
$t.="</select>\n";
return $t;
}
// form function for rt rank drop box
// nam=name of box, rank=default rank.
function rtff_grank($nam, $rank=0, $opt=0) {
global $rtg_grank;
if (!$rtg_grank) rtf_grank(1);
$t="<select name=\"$nam\">\n";
if ($opt==1)
	$t.="<option value=\"0\"".(0==$rank?' selected="selected"':'').">---select---</option>\n";
for ($i=0; $i<10;$i++){
	 if ($i<count($rtg_grank))
		$t.="<option value=\"".($i+1)."\"".(($i+1)==$rank?' selected="selected"':'').">".rtf_grank($i+1)."</option>\n";
}
$t.="</select>\n";
return $t;
}
function rtf_acctmain($acct) {
	if (function_exists('rt_GetAcct')) {
		$sql="SELECT raid_account.id as acctid, raid_char.id FROM raid_account, raid_char
		WHERE raid_account.main=raid_char.char AND raid_account.id=$acct";
		$t=GetRec($sql);
		return $t[1]['id'];
	} else {
		$t=rtd_select(rts_db_acct.', '.rts_db_char,rts_db_acct.'.id as acctid, '.rts_db_char.'.id',rts_db_acct.".id=$acct AND ".rts_db_char.".char=".rts_db_acct.".main");
		return $t['id'];
	}
}
function rtf_charrole($char) {
	if (function_exists('rt_GetAcct')) {
		$sql="SELECT raid_char.id, raid_char.role FROM raid_char WHERE raid_char.id=$char";
		$t=GetRec($sql);
		return $t[1]['role'];
	} else
		return rtd_selcol(rts_db_char,'role',"id=$char");

}
// this shows the popup for characters, takes the id from raid_sign
function rtf_charpopup ($id, $loop) {
$sql="SELECT * FROM ".rts_db_sign." WHERE id=$id";
if (!$t=GetRec($sql)) rtf_error (120, $sql);
$rec=$t[1];
$main=rtf_acctmain($rec['charid']);
$curr=$rec['char'];
$rank=GetRec("SELECT * FROM ".rts_db_acct." WHERE id=$rec[charid]");
$rank=rtf_grank($rank[1]['guildrank']);
$alts=GetRec("SELECT * FROM ".rts_db_char." WHERE account=$rec[charid]");
$popup ="<div id=\"sc$loop\" class=\"rtpopup\">\n";
$popup.="<div class=\"rtinst\">";
$popup.="<div class=\"statusicon\">Status moving soon</div>";
$popup.="Main/alt here</div>";
$popup.="$rec[note]".br(); //public note
$popup.="Alts:".br();
foreach ($alts as $v) {
	$popup.=rtf_char($v['id']).br();
}
$popup.="<hr />\n";
$popup.="$rec[offnote]";
$popup.="</div>";
return $popup;
}
function rtf_offnote($note, $type='') {
	if ($type=='o')
		$note="<span class=\"offonly\">$note</span>";
	else
		$note=preg_replace('/(\[o])(.*)(\[\/o])/', '<span class="officer">\2</span> ', $note);
	return "<em>$note</em>";
}
?>