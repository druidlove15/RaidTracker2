<?php
//testing
include 'rt-settings.php';
if ($rt_menu=='main') include 'libcommon.php';
else include '../libcommon.php';

//---------------------- main file
// --- interpret cookies  (always interpreted)

if ($_COOKIE['rtid']) {
	list($rt_session['id'], $rt_session['main'], $rt_session['prof'])=explode('$',$_COOKIE['rtid']);
} else {
	$rt_session['id']=0;
	$rt_session['main']='Rofp guest';
	$rt_session['prof']=-1;
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
function rt_Permissions ($req) {
if ($req<1 && $req>-1) $req=rt_IntRank($req,1);
if ($req>rt_GetRank()) return false;
return true;
}
?>