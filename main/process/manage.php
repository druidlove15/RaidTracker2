<?php
/*******************************************************************************
 * manage.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Officer modify raid controls
 ******************************************************************************/
$rtv_view='list';
if ($rtv_formdata['wb']) { //Whiteboard publishing/clearing
	//$rta_list=rtd_selcol(rts_db_sign,"char","id IN ($rta_idlist)",'',1);  //want to track both 6 and 7 statuses
	if (is_array($rta_list)) $rta_list=implode(",",$rta_list); //if more than one player, implode array.
	if ($rtv_formdata['wb']=='Clear') {  //clear WB
		rtd_update (rts_db_sign,array('status'=>'2'),"raidid=$rtv_raidid AND `status`=6");
		rtd_update (rts_db_sign,array('status'=>'3'),"raidid=$rtv_raidid AND `status`=7");
		rtf_log("signup $rtv_raidid","Cleared Whiteboard");
		$rts_internal['message'].="Whiteboard cleared".br();
	} else {  //Publish WBs
		rtd_update (rts_db_sign,array('status'=>'1'),"raidid=$rtv_raidid AND (`status`=6 OR `status`=7)");
		rtf_log("signup $rtv_raidid","[icon=1] Published Whiteboard");
		$rts_internal['message'].="Whiteboard published".br();
	}
	return;
}
$rta_test=rtd_select (rts_db_sign,'*',"raidid=$rtv_raidid AND `charid`=".$rtv_formdata['newplayer']);
if ($rtv_formdata['newplayer'] && $rta_test) {  // new player already in list
	$rts_internal['message']="Cannot add an existing player--aborting request.".br();
	return;
}
if (($rtv_formdata['status']==4||$rtv_formdata['status']==0||$rtv_formdata['status']==-1)
     && $rtv_formdata['newplayer']) { //invalid status for a new person
	$rts_internal['message'].="Invalid status for a new player.  Aborting request.".br();
	return;
} else if ($rtv_formdata['newplayer']) {  // new player, add him.
	$rta_logsql="Added subscription for ";  // start log line
	$rta_sql['charid']=$rtv_formdata['newplayer']; // adds playerid to sql array
	$rta_sql['char']=rtf_acct2char($rta_sql['charid']); //looks up main char -> sql array
	$rta_logsql.='[pchar]'.$rta_sql['char'].'[/pchar]'; // adds main char to log line
	$rta_sql['raidid']=$rtv_raidid;  //adds raid id to sql array
	$rta_charinfo=rtf_playermatrix('charid',$rta_sql['char']); //looks up char info
	$rta_sql['role']=$rta_charinfo[0]['role'];  //assigns primary role
	if ($rtv_formdata['status']==8) {  //if status was WB then make notes
		$rta_sql['status']=6;
		$rta_logsql="[icon=2] $rta_logsql on Whiteboard";
	} else {
		$rta_sql['status']=$rtv_formdata['status'];
		$rta_logsql="[icon=".$rta_sql['status']."] $rta_logsql";
	}
	if ($rtv_formdata['pnote']&& $rtv_formdata['cpnote']){ // if public note assigned and entered
		$rta_sql['note']=$rtv_formdata['pnote'];
		$rta_logsql.=" with public note: $rta_sql[note]";
	}
	if ($rtv_formdata['onote']&& $rtv_formdata['conote']) { // if officer note assigned and entered
		$rta_sql['offnote']=$rtv_formdata['onote'];
		$rta_logsql.=" with officer note: $rta_sql[offnote]";
	}
	$rta_sql['signup']=date("Y-m-d H:i",rts_currtime);
	$rta_sql['modified']=date("Y-m-d H:i",rts_currtime);
	rtd_insert(rts_db_sign,$rta_sql);
	rtf_log("signup $rtv_raidid",$rta_logsql);
	if (!$rtv_formdata['change']) {
		$rts_internal['message'].="Player added.".br();
		return;
	}
	$rta_id=rtd_selcol(rts_db_sign,"id","raidid=$rtv_raidid AND `charid`=$rtv_formdata[newplayer]");
	$rtv_rec[]=$rta_id;
} else if ($rtv_formdata['status']==-1) { // delete status chosen
	$rta_idlist=implode(", ", $rtv_rec);
	$rta_list=rtd_selcol(rts_db_sign,"char","id IN ($rta_idlist)",'',1);
	if (is_array($rta_list)) $rta_list=implode(",",$rta_list); //if more than one player, implode array.
	$rta_sql="DELETE FROM ".rts_db_sign." WHERE id IN ($rta_idlist)";
	mysql_query($rta_sql);
	$rta_logsql="Deleted [plist]".$rta_list."[/plist] from raid signup.";
	rtf_log("signup $rtv_raidid",$rta_logsql);
	return;
}
// move players
unset($rta_sql);// unset sql array if necessary
$rta_log='';     // unset log line if necessary
$rta_sql['modified']=date('Y-m-d H:i',rts_currtime);
if ($rtv_formdata['cpnote']) { //change/delete player note
	if (!$rtv_formdata['pnote']) { //if player note is null
		$rta_sql['note']=NULL;  //null the pnote property
		$rta_log=', deleting public note';
	} else {
		$rta_sql['note']=$rtv_formdata['pnote'];  //null the pnote property
		$rta_log=', with public note '.$rta_sql['note'];
	}
}
if ($rtv_formdata['conote']) { //change/delete officer note
	if (!$rtv_formdata['onote']) { //if player note is null
		$rta_sql['offnote']=NULL;  //null the pnote property
		$rta_log=', deleting officer note';
	} else {
		$rta_sql['offnote']=$rtv_formdata['onote'];  //null the pnote property
		$rta_log=', with officer note '.$rta_sql['offnote'];
	}
}
$rta_playerlist=rtd_selcol(rts_db_sign,'char',"id IN (".implode(",",$rtv_rec).")");
if (is_array($rta_playerlist)) $rta_playerlist=implode(",",$rta_playerlist);
if ($rtv_formdata['status']!=8) {  //not Whiteboard status
	if ($rtv_formdata['status']) {  //but with a move
		$rta_log="[icon=".$rtv_formdata['status']."] Moved [plist]".$rta_playerlist."[/plist]".$rta_log;
		$rta_sql['status']=$rtv_formdata['status'];
	} else { // no moving status
		if (!$rta_log && !$rtv_formdata['change']) return;  //no move or change at all
		if ($rta_log) $rta_log="Changed [plist]".$rta_playerlist."[/plist]".$rta_log;
	}
	if ($rta_log) rtd_update(rts_db_sign,$rta_sql, "id IN (".implode(",",$rtv_rec).")"); // update list
} else {	
	$rta_playerlist2=rtd_select(rts_db_sign,'*', "id IN (".implode(",",$rtv_rec).")",'',1); //WB list
	for($i=0;$i<count($rtv_rec);$i++) {
		if ($rta_playerlist2[$i]['status']==3) 
			rtd_update(rts_db_sign, array('status'=>'7'),"id=".$rta_playerlist2[$i]['id']);
		else if ($rta_playerlist2[$i]['status']<6) 
			rtd_update(rts_db_sign, array('status'=>'6'),"id=".$rta_playerlist2[$i]['id']);
	}
	$rta_log="Moved [plist]".$rta_playerlist."[/plist] to whiteboard".$rta_log;
}
if ($rta_log) rtf_log("raid $rtv_raidid",$rta_log);
if ($rtv_formdata['change']) $rtv_view='swap';
// add character changes
?>