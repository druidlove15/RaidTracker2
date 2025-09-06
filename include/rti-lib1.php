<?php
/*********************************************
 * rti-lib1.php
 *********************************************
 * ver. 2.00 library files (cookie, etc)
 *********************************************/
//------------------ Cookie data
//------------------ Process Cookie
$rtc_session['tos']=null;
if ($_COOKIE[rts_cookie]) {
	list($rtc_session['id'], $rtc_session['main'], $rtc_session['prof'], $rtc_cookie)=explode('$',$_COOKIE[rts_cookie]);
//	if (!stristr ($title, 'Login') && $rtc_cookie!=$rts_cookiev) {
	if ($rtc_cookie!=$rts_cookiev) {
		echo "Old or corrupted cookie info.  Please either clear cookies or "
		. url(rts_syspath."/login",'Log out here')." to reset the cookie".br();
		exit;

//	$tt=GetRec("SELECT rtrank FROM raid_account WHERE id=$rt_session[id]");
//	$rt_session['prof']=$tt[1]['rtrank'];
}
	$rtc_session['prof']=rtd_selcol(rts_db_acct,'rtrank',"id=$rtc_session[id]");
//	$rtc_session['settings']=rtd_selcol(rts_db_acct,'settings',"id=$rtc_session[id]");
	$rtc_session['view']=rtd_selcol(rts_db_acct,'view',"id=$rtc_session[id]");
//	$rtc_session['view']=$rtc_session['settings'][0];
	if (strlen($rtc_session['settings'])>1) $rtc_session['tos']=$rtc_session['settings'][1];
	$rtc_session['charid']=rtd_selcol(rts_db_char,'id',"`char`='".$rtc_session['main']."'");

} else {  //------------------------ logged out/guest
	$rtc_session['id']=0;
	$rtc_session['main']=$rts_guestname;
	$rtc_session['prof']=11;
	$rtc_session['view']=rts_view;
	$rtc_session['charid']=0;
}
if (rts_override) $rtc_session['view']=rts_view; //override here

function rtc_ret($field){
	global $rtc_session;
	return $rtc_session[$field];
}
//------- functions to retrieve cookie data
function rtc_acct(){return rtc_ret('id');}   // account id  rt_GetAcct
function rtc_name(){return rtc_ret('main');} // main name   rt_GetName
function rtc_priv(){return rtc_ret('prof');} // RT rank/priv rt_GetRank but not same rank
function rtc_id(){return rtc_ret('charid');} // main char id
function rtc_view(){return rtc_ret('view');} // view mode
function rtc_tos(){return rtc_ret('tos');}   // 2.0 only: TOS acceptance.
/**************************** Other functions ********************/
// date format  copied from 1.x
// not sure if I want to keep remain as countdown
function rtf_dateformat($datestr, $datets='', $remain=0) {
$currdate=strtotime(date("Y-m-d H:i:s"));
if (!$datets) $datets=$currdate;
$s=date($datestr, $datets);
//do something with remain
return $s;
}
//--Insert into log.  ver. 2.0
function rtf_log ($cat, $reason='No reason given', $per='11', $ref='0') {
$date=date("Y-m-d H:i:s");
$char=rtc_acct();
$set['category']=$cat;
$set['ref']=$ref;
$set['date']=$date;
$set['person']=$char;
$set['reason']=addslashes($reason);
$set['perm']=$per;
$rec=rtd_insrec(rts_db_hist,$set, array('date','person'),'id');
return $rec;
}
/**************************************************
 * rtf_playermatrix
 **************************************************
 * presents a list of characters for a player
 * allows search by one field
 **************************************************/
function rtf_playermatrix($rta_field, $rta_query) {
	if ($rta_field=='acctid') $rta_field=rts_db_acct.".id";
	else if ($rta_field=='charid') $rta_field=rts_db_char.".id";
	else if ($rta_field=='char') $rta_field=rts_db_char.".char";
	return rtd_select (rts_db_acct.", ".rts_db_char,
	  array (rts_db_acct.".id as playerid", rts_db_acct.".guildrank", rts_db_acct.".main",
		  rts_db_char.".id AS charid", rts_db_char.".char AS charname", rts_db_char.".level",
		  rts_db_char.".class", rts_db_char.".role"),
	  rts_db_char.".account = ".rts_db_acct.".id AND $rta_field='$rta_query' ",
	  "ORDER BY `char` ASC",1);
}
/*******************************************************************************
 * rtf_acct2char
 *******************************************************************************
 * Ver 2.0, takes account id and converts it to the main char
 ******************************************************************************/
function rtf_acct2char($id) {
$rta_main=rtd_selcol(rts_db_acct,'main',"id=$id");  //gets main character name
return rtd_selcol(rts_db_char,'id',"`char`='$rta_main'");
}

/**************************************************
 * rtf_character
 **************************************************
 * Version 2.0. 'displays' a character name
 * Takes in charid  (required)
 * Optional: popup 0-none, (default) -1-regular, positive-raid select (number being rec id)
 *           icon: 0-none, 1-icon only, (default) 2-name + icon
 *           link: 0-no, 1-yes (default.  required for popup)
 **************************************************/
function rtf_character($rta_charid, $rta_popup=-1, $rta_icon=2, $rta_link=1) {
	$rta_charinfo=rtf_playermatrix('charid',$rta_charid);
	if (!$rta_charinfo) return "(unknown character)";
	$rta_charinfo=$rta_charinfo[0];
	$rta_return='';  //used for return
     $rta_return="<span>";
	if ($rta_link) $rta_return.="<a href=\"".rts_syspath."/?playerid=$rta_charinfo[playerid]\">";
	if ($rta_icon) $rta_return.=rtf_classicon($rta_charinfo['class'])."&nbsp;";
	if ($rta_icon != 1) $rta_return.=$rta_charinfo['charname'];
	if ($rta_link) $rta_return .="</a>";
	// ------------ insert popup if needed
	if ($rta_popup<0) $rta_return .=rtf_charpop($rta_charinfo['charid']);
	else if ($rta_popup>0) $rta_return .=rtf_charpop($rta_popup, 'raid');
	return $rta_return."</span>";
}
/**************************************************
 * rtf_charpop
 **************************************************
 * Version 2.0. 'displays' a popup box with character
 * information
 * Takes in a parameter defined by $rtv_mode below
 * $rtv_mode= 'raid' (raid record)
 *            'charid' (default, character ID)
 *            'char' (character name)
 **************************************************/
function rtf_charpop($rtv_param, $rtv_mode='charid') {
switch ($rtv_mode) {
	case 'raid':
		$rta_raid=rtd_select(rts_db_sign,'*',"id=$rtv_param");
		$rta_charid=$rta_raid['char'];
		break;
	case 'char':
		$rta_charid=rtd_selcol(rts_db_char,'id',"`char`='$rtv_param'");
		break;
	case 'charid':
		$rta_charid=$rtv_param;
		break;
	default: rtf_error(106,$rtv_mode);
}
$rta_char=rtf_playermatrix('charid',$rta_charid);
$rta_char=$rta_char[0];
$rta_charlist=rtf_playermatrix('acctid',$rta_char['playerid']);
$j=count($rta_charlist);  //needed so the count is 'static' for the loop.
for ($i=0; $i<$j; $i++) {
	if ($rta_charlist[$i]['main']==$rta_charlist[$i]['charname']) {
		$rta_main=$rta_charlist[$i];
		unset($rta_charlist[$i]);
	} else if ($rta_charlist[$i]['charid']==$rta_charid) {
		unset ($rta_charlist[$i]);
	}
}
if ($rta_char!=$rta_main) $rta_alt='Alt'; else $rta_alt='Main';
$rta_guildrank=rtf_grank($rta_char['guildrank']);

//----------------- format return here
$rta_return=div($rta_guildrank,'','floatleft');  //add div, class or other info here (in that order)
$rta_return.=div($rta_alt);      //non-float, compared to above
$rta_return=div($rta_return,'','chartitle');    //puts the top two in its own hilight div

//--------------- formats player character list
$rta_list='';                    //starts a character list
$rta_chgalt=rtf_p('change_alt');
if ($rta_alt=='Alt'){
	$rta_list="Main: ".rtf_character($rta_main['charid'],0,2,0);
//	if ($rta_raid && $rta_chgalt) $rta_list.=" select";  //need to change this to add button
	$rta_list=div($rta_list);  //main char on own line
}
if ($rta_raid && $rta_chgalt) {
	foreach ($rta_charlist as $v) {
		$rta_list.=div("Alt: ".rtf_character($v['charid'],0,2,0) /*.' select' */);
	}
}
$rta_return.=div($rta_list);
if ($rta_raid) { // section applies only if there is a raid
//---------------------- format for notes
$rta_list='';  //resets for notes
if (rtf_p('view_playernote')) $rta_list=div(rtf_offnote($rta_raid['note']));
if (rtf_p('view_officernote')) $rta_list.=div($rta_raid['offnote']);
$rta_return.=div($rta_list);
if ($rta_list) $rta_return.="<hr />\n";
//----------------------- format for time
$rta_return.=div(ital("Created: ".rtf_datetime(strtotime($rta_raid['signup']),'dt')));
$rta_return.=div(ital("Updated: ".rtf_datetime(strtotime($rta_raid['modified']),'dt')));

//----------------------- format for moves
/*
$rta_list=''; //resets for move
if (rtf_p('list_raidlist') && $rta_raid['status']!=1) $rta_list.='RL ';
if (rtf_p('list_raidlist') && $rta_raid['status']<6) $rta_list.='WB ';
if (rtf_p('list_raidlist') && $rta_raid['status']!=2) $rta_list.='A ';
if (rtf_p('list_raidlist') && $rta_raid['status']!=3) $rta_list.='R ';
if (rtf_p('list_raidlist') && $rta_raid['status']!=4) $rta_list.='W ';
if (rtf_p('list_raidlist') && $rta_raid['status']!=5) $rta_list.='XX ';
if ($rta_list) $rta_list=div("<hr />\nMove to: $rta_list");  //format for RL
$rta_return.=div($rta_list);
*/
}
$rta_return=span($rta_return,'','charpop'); //puts the box into a float div
return $rta_return;
}
?>
