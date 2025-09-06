<?php
function rtf_selfstat($id) {
//------------------ retrieve times from db
$rta_info=rtd_select(rts_db_list,'*',"id=$id");
$rta_stimes=strtotime($rta_info['date']);
$rta_itimes=strtotime($rta_info['inv']);
$rta_fntime=strtotime($rta_info['freezenew']);
$rta_fdtime=strtotime($rta_info['freezedel']);
$rta_ntimes=rts_currtime;

//---- Calculate if able to modify subscriptions
$rta_sign=0;  //starts at 0.  1/add.  2/remove.  3/both.
if ($rta_ntimes<$rta_fntime) $rta_sign+=1;  // add time threshold check.
if ($rta_ntimes<$rta_fdtime) $rta_sign+=2;  // remove time threshold check.
if (rtf_p('signup_override')) $rta_sign=3;  // if officer override.

//---- Get Character info about signup (if any)
$rta_char=rtc_acct();
$rta_charlist=rtf_playermatrix('acctid',$rta_char);
if ($rta_char) $rta_info=rtd_select(rts_db_sign,'*',"charid=$rta_char AND raidid=$id");
else $rta_char=0;
if (!$rta_info) {
	$rta_info['id']=0;
	$rta_info['status']=0;
}
//---- Set bottom line if unable to modify subscriptions
$rta_subline=''; //initially set to nothing.
if (!$rta_sign) $rta_subline="Sign up is now closed.  Please see an officer";
//---- Set up status
$rta_icon=$rta_info['status'];
switch ($rta_icon) {
	case 0:  // not signed up or logged in
		if (!$rta_char){
			$rta_statline="You are not logged in.";
			if ($rta_sign==1 || $rta_sign==3)
				/*  edit it out for the moment
				$rta_subline="Quick signup is not available.  But you can use the alternative signup.".br()
				. button ('alt','Alternative signup');
				*/
				$rta_subline="You must be logged in to sign up for raids.";
			else if ($rta_sign==2) {
				$rta_subline="New subscriptions are closed.  Please log in if you want to withdraw.";
				$rta_sign=0;
			}
		} else {
			$rta_statline="You are not subscribed for this raid";
			if ($rta_sign==2) {
				$rta_subline="New subscriptions are closed.  Please log in if you want to withdraw.";
				$rta_sign=0;
			}
		}
		break;
	case 5:
		$rta_statline="You have been removed from this raid";
		$rta_subline="Please see an officer for details.";
		$rta_sign=0;
		break;
	case 4:
		$rta_statline="You have withdrawn from this raid";
		if ($rta_sign==2) {
			$rta_subline="New subscriptions have closed";
			$rta_sign=0;
		}
		break;
	case 6:  //reserve/WB
		$rta_icon=2;
		if (rtf_p('show_whiteboard')) $rta_statline="You are on the whiteboard";
	case 2:
		if (!$rta_statline) $rta_statline="You are signed up as available to this raid";
		if ($rta_sign==1) {
			$rta_subline="Withdraw subscriptions have closed.  Please see an officer if you need to withdraw.";
			$rta_sign=0;
		}
		break;
	case 7:  //reserve/WB
		$rta_icon=3;
		if (rtf_p('show_whiteboard')) $rta_statline="You are on the whiteboard";
	case 3:
		if (!$rta_statline) $rta_statline="You are signed up as a reserve to this raid";
		if ($rta_sign==1) {
			$rta_subline="Withdraw subscriptions have closed.  Please see an officer if you need to withdraw.";
			$rta_sign=0;
		}
		break;
	case 1:
		$rta_statline="You are on the raid list as ".rtf_character($rta_info['char'],0,2,0)
		.' - '. rtf_role($rta_info['role']);
		if ($rta_sign==1) {
			$rta_subline="Withdraw subscriptions have closed.  Please see an officer if you need to withdraw.";
			$rta_sign=0;
		}
		break;
	default: rtf_error(101, "$rta_icon-status.  Please see an admin immediately");
}
//---- Icon and grouping it as one.
if ($rta_icon) $rta_statline=rtf_statusicon($rta_icon,0,0)." $rta_statline";
//$rta_statline=div($rta_statline);
//---- Processing notes
if ($rta_info['note'])
	$rta_statline.=div(span("Player note:",'','heading').' '.rtf_offnote($rta_info['note']));
if ($rta_info['offnote'] && rtf_p('view_officernote'))
	$rta_statline.=div(span("Officer's note:",'','heading').' '.rtf_offnote($rta_info['offnote'],'o'));
//---- Preparing block and return if can't sign up
$rta_ret="<h1>Raid Status</h1>\n".div($rta_statline).NL."<h2>Signing up</h2>\n";
if (!$rta_sign) return $rta_ret.div($rta_subline);  //returns if signup is no longer possible
//---- Preparing signup option buttons
if (!$rta_subline) {
	$rta_subline=span("Comment:",'','heading').' '.input('formdata[comment]','text');
	if ($rta_icon!=0) $rta_subline.=' '.input('formdata[listsub]','submit','Comment Only');
	if (rtf_p('sign_raidlist') && $rta_icon!=1) $rta_subline.=' '.input('formdata[listsub]','submit','Raid List');
	if ($rta_icon!=1 && $rta_icon!=2 && $rta_icon!=6) $rta_subline.=' '.input('formdata[listsub]','submit','Subscribe');
	if ($rta_icon!=1 && $rta_icon!=3 && $rta_icon!=7) $rta_subline.=' '.input('formdata[listsub]','submit','Reserve');
	if ($rta_icon!=4 && $rta_icon!=0) $rta_subline.=' '.input('formdata[listsub]','submit','Withdraw');
	if (rtf_p('sign_alt') && count($rta_charlist)>1) {
		$rta_subline.="<select name=\"formdata[charid]\">\n";
		if (!$rta_info['char']) $rta_info['char']=rtc_id();
		foreach ($rta_charlist as $v) {
			$rta_subline.="<option value=\"".$v['charid']."\" ";
			if ($v['charid']==$rta_info['char']) $rta_subline.='selected="selected"';
			$rta_subline.=">".$v['charname']."</option>\n";
		}
		$rta_subline.= "</select>\n";
	}
}
//---- Preparing form
$rta_ret.=input('raidid','hidden',$id);
$rta_ret.=input('formview','hidden','subscribe');
$rta_ret.=div($rta_subline);
$rta_ret=form(NL.div($rta_ret),'.');
return $rta_ret.NL;
}
?>