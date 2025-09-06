<?php
/*******************************************************************************
 * settings.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Allow character settings to be modified
 ******************************************************************************/
//--
if ($rtv_formdata['addchar']){
	$rtv_view='create';
	return;
}
$rtv_view='settings';
if (!$rtv_formdata['char']) return;  //no character selected
$rta_playerrec=rtf_playermatrix('charid',$rtv_formdata['char']); //gets info on char selected
if ($rtv_formdata['charmanage']=='update') {  //update character info
	$rta_sql=null; //sets up sql table
	if ($rtv_formdata['role']) {
		$rta_sql['role']=$rtv_formdata['role'];
		$rta_logsql[]='role to [role='.$rtv_formdata['role'].']';
	}
	if ($rtv_formdata['class']) {
		$rta_sql['class']=$rtv_formdata['class'];
		$rta_logsql[]='class to [class='.$rtv_formdata['class'].']';
	}
	if ($rtv_formdata['level']) {
		$rta_sql['level']=$rtv_formdata['level'];
		$rta_logsql[]='level to '.$rtv_formdata['level'];
	}
	if ($rtv_formdata['charname']) {
		if ($rta_playerrec[0]['main']==$rta_playerrec[0]['charname']) //main char
			rtd_update(rts_db_acct,array('main'=>$rtv_formdata['charname']),"id=".$rta_playerrec[0]['playerid']); //change main name
		$rta_sql['char']=$rtv_formdata['charname'];
		$rta_logsql[]='name from '.$rta_playerrec[0]['charname'];
	}
	if (!$rta_sql) return;  //no changes
	rtd_update(rts_db_char,$rta_sql,"id=$rtv_formdata[char]");
	$rta_log="Changed [pchar]".$rta_playerrec[0]['charid']."[/pchar]: ".implode(", ",$rta_logsql);
	rtf_log("character",$rta_log);
	$rts_internal['message'].="Character updated".br();
	return;
}
if ($rtv_formdata['charmanage']=='main') {  //update character info
	if ($rta_playerrec[0]['main']==$rta_playerrec[0]['charname']) { //already main char
		$rts_internal['message']="Character already set as your main.".br();
		return;
	}
	rtd_update(rts_db_acct,array('main'=>$rta_playerrec[0]['charname']),"id=".$rta_playerrec[0]['playerid']); //change main name
	rtf_log("character","Changed main to [pchar]".$rtv_formdata['char']."[/pchar]");
	$rts_internal['message']="Successfully switched main character.".br();
	return;
}
if ($rtv_formdata['charmanage']=='delete') {  //delete character info
	if ($rta_playerrec[0]['main']==$rta_playerrec[0]['charname']) { //already main char
		$rts_internal['message']="Cannot delete your main character.".br();
		return;
	}
	mysql_query("DELETE FROM ".rts_db_char." WHERE id=".$rtv_formdata['char']);
	mysql_query("DELETE FROM ".rts_db_sign." WHERE `char`=".$rtv_formdata['char']);
	rtf_log("character","Deleted ".$rta_playerrec[0]['charname']." from system.");
	$rts_internal['message']="Character deleted.".br();
	return;
}
?>