<?php
/*******************************************************************************
 * raid.php
 *******************************************************************************
 * Version 2.00 : Processes raid form data
 ******************************************************************************/
//-------------- if no submit button given load form
if (!$rtv_formdata['mode']) {
	$rtv_view='raid';
	return;
}
if ($rtv_formdata['cancel']) {
	if ($rtv_raidid) $rtv_view='list';
	else $rtv_view='main';
	return;
}
//-------------- if delete was pushed, delete raid
if ($rtv_formdata['delete']) {
	$rta_q=rtd_select (rts_db_list,'*',"id=$rtv_raidid");          //get record of raid for log
	$sql="DELETE FROM ".rts_db_list." WHERE id=$rtv_raidid";       //delete raid listing
	$sql2="DELETE FROM ".rts_db_sign." WHERE raidid=$rtv_raidid";  //delete raid signups
	$alt_sql="Deleted raid ($rtv_raidid): $rta_q[date]/$rta_q[instance]";        
	//$rt_raidlink=$rtv_raidid;
	$result=(mysql_query($sql) && mysql_query($sql2));
	rtf_log('raid',$alt_sql);
	$rts_internal['message'].='Raid deleted'.br();
	$rtv_view='main';
	unset($rtv_raidid);  //stops trying to find raid id.
	return;
}
//------------ add or modify raid.  Calculate time stamps
foreach ($rtv_formdata['time'] as $k=>$v)
	$rta_timestamp[$k]=strtotime($rtv_formdata['date'].' '.$v);
//--- adjust timestamps if 'invalid' due to midnight.
if ($rta_timestamp['end']<$rta_timestamp['start']) 
	$rta_timestamp['end']=strtotime("+1 day",$rta_timestamp['end']);
if ($rta_timestamp['fnew']>$rta_timestamp['start']) 
	$rta_timestamp['fnew']=strtotime("-1 day",$rta_timestamp['fnew']);
if ($rta_timestamp['fdel']>$rta_timestamp['start']) 
	$rta_timestamp['fdel']=strtotime("-1 day",$rta_timestamp['fdel']);
if ($rta_timestamp['inv']>$rta_timestamp['start']) 
	$rta_timestamp['inv']=strtotime("-1 day",$rta_timestamp['inv']);
//--- generate SQL times for timestamps
foreach ($rta_timestamp as $k=>$v) 
	$rta_sqltemp[$k]=date("Y-m-d H:i:s", $v);
//--- adjust fields to match db table:
$rta_temp=$rta_sqltemp['end'];
$rta_sql['date']=$rta_sqltemp['start'];
$rta_sql['inv']=$rta_sqltemp['inv'];
$rta_sql['endtime']=$rta_sqltemp['endtime'];
$rta_sql['freezenew']=$rta_sqltemp['fnew'];
$rta_sql['freezedel']=$rta_sqltemp['fdel'];
$rta_sql['endtime']=$rta_sqltemp['end'];
//--- put other data in table:
$rta_sql['icon']=$rtv_formdata['icon'];
$rta_sql['instance']=$rtv_formdata['raid'];
$rta_sql['required']=$rtv_formdata['req'];
$rta_sql['note']=$rtv_formdata['note']['public'];
$rta_sql['offnote']=$rtv_formdata['note']['officer'];
$rta_sql['roles']=implode('/',$rtv_formdata['role']);
$rta_sql['id']=$rtv_raidid;
//--- if new raid
if ($rtv_formdata['create']) {
	$result=rtd_insert(rts_db_list, $rta_sql);
	if (!$result) {  //error in form
		$rtv_view='raid'; 
		$rts_internal['message'].="Error in information, please try again".br();
		return;
	}
	$alt_sql="New raid: [raidicon]".$rta_sql['icon']."[/raidicon]".$rta_sql['instance']
	." / ".$rta_sqltemp['start'];
	$rta_raidid=rtd_selcol(rts_db_list,'id',"instance='$rta_sql[instance]' AND `date`='$rta_sql[date]'",'LIMIT 1');
	rtf_log("raid $rta_raidid",$alt_sql);
	$rts_internal['message'].="New raid created".br();
	$rtv_view='main';
	unset($rtv_raidid);
	return;
}
//--- modify raid, retrieve old data
$rta_olddata=rtd_select(rts_db_list,'*',"id=$rtv_raidid");
// -- compare each key, if it's different, note change
foreach ($rta_sql as $k=>$v) {
	if ($rta_olddata["$k"]!=$v) { //different values:
		if ($k=='roles') { //different in roles, figure out which one
			list ($rta_oldrole['tank'],$rta_oldrole['heal'],$rta_oldrole['melee'],$rta_oldrole['range'])=explode('/',$rta_raid['roles']);
			foreach ($rta_oldrole as $k2=>$v2) {
				if ($v2!=$rta_formdata['role'][$k2])
					$rta_chg[]="$k2 to ".$rta_formdata['role'][$k2];
			}
		}else if ($k=='icon') {
			$rta_chg[]="location to [raidicon]".$v."[/raidicon]";
		}else {
			$rta_chg[]="$k to $v";
		}
	}
}
if (!$rta_chg) { // no changes at all
	$rtv_view='list';
	$rts_internal['message'].="No change in raid information".br();
	return;  //stop this, and return
}
$alt_sql="Changed ".implode(', ',$rta_chg);
$temp=rtd_update(rts_db_list,$rta_sql,"id=$rtv_raidid");
if ($temp){
	$rts_internal['message'].="Raid has been modified".br();
	rtf_log("raid $rtv_raidid",$alt_sql);
	$rtv_view='list';
} else {
	$rts_internal['message'].="Error in information, please try again".br();
	$rtv_view='raid';
}
?>