<?php
//testing
include "rt-settings.php";
include "rt-lib.php";

if ($rt_menu=='main') include 'libcommon.php';
else include '../libcommon.php';

//---------------------- main file
// --- interpret cookies  (always interpreted)

if ($_COOKIE['rtid']) {
	list($rt_session['id'], $rt_session['main'], $rt_session['prof'], $rt_cookie)=explode('$',$_COOKIE['rtid']);
	if (!stristr ($title, 'Login') && $rt_cookie!=$rt_cookiev) {
		echo "Old or corrupted cookie info.  Please either clear cookies or "
		. url("/rtracker/login",'Log out here')." to reset the cookie".br();
		exit;
	$tt=GetRec("SELECT rtrank FROM raid_account WHERE id=$rt_session[id]");
	$rt_session['prof']=$tt[1]['rtrank'];
	}
} else {
	$rt_session['id']=0;
	$rt_session['main']='Rofp guest';
	$rt_session['prof']=11;
}

function rt_GetAcct() {
global $rt_session;
return $rt_session['id'];
}
function rt_GetName() {
global $rt_session;
return $rt_session['main'];
}
function rt_GetRank($text=0){
global $rt_session;
if (!$text) return $rt_session['prof'];
return rt_RankName($rt_session['prof'],0);
}
function rt_IntRank($rank, $number=0) {
switch ($number) {
	case 0: //number to text
	switch ($rank){
		case 1:return "Member";
		case 2:return "Raider";
		case 3:return "Raid Leader";
		case 4:return "Officer";
		case 5:return "Developer";
		default: return "Unknown";
	}
	break;
	case 1:
	switch ($rank){
		case "Member":return 1;
		case "Raider":return 2;
		case "Raid Leader":return 3;
		case "Officer":return 4;
		case "Developer":return 5;
		default: return -1;
	}
}
}
//--------------------------------Other session related stuff
// --------- date format
function dateformat($datestr, $datets, $remain=0) {
$currdate=strtotime(date("Y-m-d H:i:s"));
$s=date($datestr, $datets);
//do something with remain
return $s;
}

function rt_log ($cat, $char, $reason, $per='11', $ref='0', $date='') {
if (!$date) $date=date("Y-m-d H:i:s");
$sql="INSERT INTO raid_hist SET `category`='$cat', ref='$ref', `date`='$date', person='$char'"
	.", reason='$reason', perm='$per'";
mysql_query ($sql);
}
?>