<?php
/*******************************************************************************
 * signup.php
 * -----------------------------------------------------------------------------
 * Version 2.00 signup process.  Self-signup/modify
 ******************************************************************************/
// -- check for raid validity
if (!($rta_raidinfo=rtd_select(rts_db_list,'*',"id=$rtv_raidid"))) {
	$rts_internal['message'].="Raid does not exist".br();
	$rtv_view='main';
	unset($rtv_raidid);
	return;
}
// -- check if subscription came from list
if ($rtv_formdata['listsub']) {
	switch ($rtv_formdata['listsub']) {
		case 'Comment Only': $rtv_formdata['status']=0; break;
		case 'Raid List': $rtv_formdata['status']=1; break;
		case 'Subscribe': $rtv_formdata['status']=2; break;
		case 'Reserve': $rtv_formdata['status']=3; break;
		case 'Withdraw': $rtv_formdata['status']=4; break;
	}
}
// -- check if signup is possible
if (!rtf_p('signup_override')) {  
	// -- if adding, check for freezenew, or else check for freezedel
	if ($rtv_formdata['status']<4) $rta_time=strtotime($rta_raidinfo['freezenew']);
	else 	$rta_time=strtotime($rta_raidinfo['freezedel']);
	if ($rta_time<=rts_currtime) {
		$rts_internal['message'].="Time expired to modify status".br();
		$rtv_view='list';
		return;
	}
	unset ($rta_time);
}
// -- Check if player has a signup already and retrieve it if exists
if (!$rtv_playerid) $rtv_playerid=rtc_acct();  // if no playerID passed in, use default
$rta_playerrec=rtd_select(rts_db_sign,'*',"raidid=$rtv_raidid AND charid=$rtv_playerid");
if (!$rtv_formdata['charid']) {
	if (!$rtv_playerrec['char']) $rtv_formdata['charid']=rtf_acct2char($rtv_playerid);
	else $rtv_formdata['charid']=$rtv_playerrec['char'];
}
if ($rtv_playerrec['status']==5) {  //if removed from raid, prohibit signup
	$rts_internal['message'].="Signup not allowed.  Please see your officers for information".br();
	$rtv_view='list';
	return;
} else if (!$rta_playerrec['status']) { //new rec
	//new rec
	$rta_sql['charid']=$rtv_playerid;
	$rta_sql['char']=$rtv_formdata['charid'];
	$rta_sql['raidid']=$rtv_raidid;
	//--- calculate role (default if not given)
	if ($rtv_formdata['role']) $rta_sql['role']=$rtv_formdata['role'];
	else {
		$rta_charinfo=rtf_playermatrix('charid',$rta_sql['char']);
		$rtv_formdata['role']=$rta_charinfo['role'];
		$rta_sql['role']=$rta_charinfo[0]['role'];
	}
	$rta_sql['status']=$rtv_formdata['status'];
	if ($rtv_formdata['comment']) $rta_sql['note']=$rtv_formdata['comment'];
	$rta_sql['signup']=date("Y-m-d H:i",rts_currtime);
	$rta_sql['modified']=$rta_sql['signup'];
	$rta_hist="[icon=".$rta_sql['status']."] Created subscription".($rta_sql['note']?" noting $rta_sql[note]":"").".";
	rtd_insert(rts_db_sign,$rta_sql);
	rtf_log("signup $rtv_raidid",$rta_hist);
	$rts_internal['message'].="Subscription successful".br();
	unset($rtv_playerid);
	$rtv_view='list';
	return;
}
// -- modify signup 
if ($rtv_formdata['status']){
	$rta_sql['status']=$rtv_formdata['status'];
	$rta_log_sql[]="[icon=".$rta_sql['status']."] Changed subscription";
}
if ($rtv_formdata['charid']!= $rta_playerrec['char']) {
	$rta_sql['char']=$rtv_formdata['charid'];
	$rta_log_sql[]="Changed character to [pchar]".$rta_formdata['charid']."[/pchar]";
}
if ($rtv_formdata['comment'] || $rtv_formdata['status']==0) {
	if (!$rtv_formdata['comment']) {
		$rta_sql['note']=null;
		$rta_log_sql[]="Deleted comment.";
	} else {
		$rta_sql['note']=$rtv_formdata['comment'];
		$rta_log_sql[]="Changed comment to $rtv_formdata[comment].";
	}
}
$rta_sql['modified']=date("Y-m-d H:i",rts_currtime);
rtd_update(rts_db_sign,$rta_sql, "id='$rta_playerrec[id]'");
$rta_hist=implode (", ",$rta_log_sql);
rtf_log("signup $rtv_raidid",$rta_hist);
?>