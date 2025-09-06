<?php
/*********************************************
 *   rt-subscribe.php
 *********************************************
 * Used for editing/adding subscriptions
 *********************************************/
//Check to see if raidid is even valid
function rtf_raidcheck($id) {
$rt_info=rtd_select(rts_db_list,'*',"id=$id");
if (!$rt_info){  //not a valid raid id
	rtf_error(1,"raid id= $id");
	return false;
}
return $rt_info;
}
//Check to see if time is not frozen (or officer overrides)
function rtf_timecheck($raidinfo, $status=2) {
$rt_curr=rts_currtime;
$rti_freezenew=strtotime($raidinfo['freezenew']);
$rti_freezedel=strtotime($raidinfo['freezedel']);
if ($status==4){
	if ($rt_curr<$rti_freezedel || rtf_p('signup_override')) return true;
}else {
	if ($rt_curr<$rti_freezenew || rtf_p('signup_override')) return true;
}
rtf_error(2);
return false;
}
//Check to see if signup exists for player
function rtf_signupck($raidid, $player) {
$rts=rtd_select(rts_db_sign,'*',"raidid=$raidid AND charid=$player");
if (!$rts) return false;
return $rts['id'];
}

//-- new subscription, taking in ID as raid
function rtf_new($id, $msg='', $stat=2, $note='', $acctid=0, $charid=0, $role=0) {
//--------- Set up variables not passed in
if (!$acctid)  {//not passed in
	$acctid=rtc_acct();
	if (!$acctid){ // not logged in
		rtf_error(10);
		return;
	}
}
if (!$charid) $charid=rtf_acctmain($acctid);
if (!$role) $role=rtf_charrole($charid);
//--------- Checks for valid raid, time, and new signup
if (!$rt_info=rtf_raidcheck ($id)) return false;
if (!rtf_timecheck($rt_info, $stat)) return false;
if (rtf_signupck($id, $acctid)) return false;
//--------- Ver 2.0 insert record
$arr['charid']=$acctid;
$arr['char']=$charid;
$arr['raidid']=$id;
$arr['role']=$role;
$arr['status']=$stat;
if ($note) $arr['note']=$note;
$arr['signup']=date('Y-m-d H:i:s',rts_currtime);
$arr['modified']=$arr['signup'];
$r=rtd_insrec(rts_db_sign,$arr,array('charid','raidid'),'id');
//--------- Setting up raid_log query
if (!$msg) $msg="New subscription";
$msg="[icon=".$stat."] $msg";
//--------- Updating raid_sign with ref id and quitting.
$rt_ref=rtf_log("signup $id",$msg,11,$r);
return true;
}

function rtf_subscribe($raidid, $msg='', $stat=2, $note='', $signid=0, $acctid=0, $charid=0, $role=0) {
if (!$acctid)  {//    --- account not passed in
	$acctid=rtc_acct();
	if (!$charid) $charid=rtc_id();
	if (!$acctid){ // not logged in
		rtf_error(10);
		return;
	}
}
if (!$charid) $charid=rtf_acctmain($acctid);  //-- get charid
if (!$role) $role=rtf_charrole($charid);      //-- get role
if (!$signid) $signid=rtf_signupck($raidid, $acctid); // get signup id
// check if alt is being used
//if ($charid!=rtf_acctmain($acctid)) $msg2=' using [pchar]'.$charid.'[/pchar].';
//--------- Checks for valid raid, time, and new signup
if (!$rt_info=rtf_raidcheck ($raidid)) return false;
if (!rtf_timecheck($rt_info, $stat)) return false;
//--------- insert record
	$arr['charid']=$acctid;
	$arr['char']=$charid;
	$arr['raidid']=$raidid;
	$arr['role']=$role;
	if ($stat) $arr['status']=$stat;
	if ($note) $arr['note']=$note;
	else if (!$note && $stat==0) $arr['note']='';
	$arr['modified']=date('Y-m-d H:i:s',rts_currtime);
	if (!$signid) $arr['signup']=$arr['modified'];
	if (!$signid) $r=rtd_insrec(rts_db_sign,$arr,array('charid','raidid'),'id');
	else {
		$r=rtd_update (rts_db_sign,$arr,"id=$signid") or rtf_error (120, mysql_error());
		$r=$signid;
	}
//--------- Setting up raid_log query
if (!$msg) $msg="New subscription";
if ($stat) $msg="[icon=".$stat."] $msg";
//--------- Updating raid_sign with ref id and quitting.
$rt_ref=rtf_log("signup $raidid",$msg.$msg2,11,$r);
return true;
}
?>