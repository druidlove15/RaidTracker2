<?php
/*******************************************************************************
 *  guild.php
 * -----------------------------------------------------------------------------
 * Ver. 2.00 2008-11-14
 * Process guild form requests (except last x days)
 ******************************************************************************/
// ----------- start
$rta_rtrank=rtc_priv();
$rta_grank=rtd_selcol(rts_db_acct,'guildrank',"id=".rtc_acct());
//---------------- check if you are setting ranks higher than yourself

if ($rtv_formdata['rtrank']>0 && $rtv_formdata['rtrank']<$rta_rtrank) {
	$rts_internal['message'].="Cannot set RaidTracker rank higher than your rank.".br();
	return;
} else if ($rtv_formdata['rtrank']) $rta_sql['rtrank']=$rtv_formdata['rtrank'];
//----- cannot set a guild rank higher than you except if you're superadmin
if (($rtv_formdata['grank']>0 && $rtv_formdata['grank']<$rta_grank) && $rta_rtrank!=1) {
	$rts_internal['message'].="Cannot set guild rank higher than your rank.".br();
	return;
} else if ($rtv_formdata['grank']) $rta_sql['guildrank']=$rtv_formdata['grank'];
//----------------------------------------- delete players
if ($rtv_formdata['delete']) {
	$sql="DELETE FROM ".rts_db_acct." WHERE `id`=";
	$sql2="DELETE FROM ".rts_db_char." WHERE `account`=";
	$sqllist="SELECT `main` FROM ".rts_db_acct." WHERE id=";
	$sqldelraid="DELETE FROM ".rts_db_sign." WHERE charid=";
	for ($i=0; $i<count($rtv_guild); $i++) {
		$n=$rtv_guild[$i];
		if ($n==rtc_acct()) {
			$rts_internal['message'].="Cannot delete self--Skipped.".br();
			continue;
		}
		$rta_sqlresult=rtd_select(rts_db_acct,'main',"id=$n",'',1);
		$rta_nlist[$i]=$rta_sqlresult[0]['main'];
		mysql_query($sql.$n);
		mysql_query($sql2.$n);
		$y=mysql_query($sqldelraid.$n);
	}
	if ($rt_nlist) {
		$rt_lst=implode(", ",$rta_nlist);
		rtf_log("account","Deleted $rt_lst from system");
		$rts_internal['message'].="Players successfully deleted.".br();
	}
	return;
}
if (!$rta_sql) return;  // no changes, get out of here.
$sql="UPDATE ".rts_db_acct." SET ".implode(", ",$rta_sql). "WHERE `id`='";
for ($i=0; $i<count($rtv_guild); $i++) {
	$t=rtd_update(rts_db_acct,$rta_sql,"id='$rtv_guild[$i]'");
//	mysql_query($sql.$rtv_guild[$i]."'");
}
$rts_internal['message']="Ranks have successfully changed.".br();
$rtv_view="guild";
?>