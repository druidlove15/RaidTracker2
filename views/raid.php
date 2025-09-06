<?php
if ($rtv_raidid && !rtf_pcheck('raid_alter')) return;
if (!$rtv_raidid && !rtf_pcheck('raid_create')) return;
$rta_raidlist=rtd_select(rts_db_keys,'*',"category='raid_instance'",'ORDER BY `name` ASC',1);
if ($rtv_raidid) {
	$rta_raid=rtd_select (rts_db_list,'*', "id=$rtv_raidid");
	$rta_headline= "<h1>Modify raid information</h1>";
	$dtstamp=strtotime($rta_raid['date']);
	$sdate=date('Y-m-d', $dtstamp);
	$stime=date('H:i', $dtstamp);
	$etime=date('H:i', strtotime($rta_raid['endtime']));
	$fatime=date('H:i', strtotime($rta_raid['freezenew']));
	$fdtime=date('H:i', strtotime($rta_raid['freezedel']));
	$itime=date('H:i', strtotime($rta_raid['inv']));
} else {
	$rtv_raidid=0;
	$sdate=$rtv_formdata['date'];
	$rta_raid['icon']='none';
	$rta_raid['required']=25;
	$rta_raid['instance']="(To be announced)";
	$rta_raid['roles']='0/0/0/0';
//	$rta_tank=0;
//	$rta_heal=0;
//	$rta_melee=0;
//	$rta_range=0;
	$rta_headline= "<h1>Enter new raid</h1>";
}
list($rta_tank,$rta_heal,$rta_melee,$rta_range)=explode('/',$rta_raid['roles']);
$rta_ret=input('raidid','hidden',$rtv_raidid)
. input('formdata[mode]','hidden','edit')
.group ('Raid Location:','label','','heading','for="formdata[icon]"')."<select name=\"formdata[icon]\">\n";
foreach ($rta_raidlist as $v) {
$rta_ret.="<option value=\"$v[name]\" ".($rta_raid['icon']==$v['name']?"selected=\"selected\"":"").">$v[value]</option>\n";
}
$rta_ret.="</select>".br();
$rta_ret.=textfield('formdata[raid]','Raid:',$rta_raid['instance'],'line','35')
.textfield('formdata[date]','Date:',$sdate,'line','9')
.textfield('formdata[time][start]','Start time:',$stime,'line','5')
.textfield('formdata[time][end]','End time:',$etime,'line','5')
.textfield('formdata[time][fdel]','Close withdrawls:',$fdtime,'line','5')
.textfield('formdata[time][fnew]','Close subscriptions:',$fatime,'line','5')
.textfield('formdata[time][inv]','Invites time:',$itime,'line','5')
.textfield('formdata[req]','Raiders:',$rta_raid['required'],'line','1')
.textfield('formdata[role][tank]','Tanks:',$rta_tank,'line','1')
.textfield('formdata[role][heal]','Healers:',$rta_heal,'line','1')
.textfield('formdata[role][melee]','Melee DPS:',$rta_melee,'line','1')
.textfield('formdata[role][range]','Ranged DPS:',$rta_range,'line','1')
.textfield('formdata[note][public]','Public note:',$rta_raid['note'],'box',array('x'=>40, 'y'=>4))
.textfield('formdata[note][officer]','Officer\'s note:',$rta_raid['offnote'],'box',array('x'=>40, 'y'=>4));
$rta_ret.=span('&nbsp;','','spaceleft');
if (!$rtv_raidid) $rta_ret2=button('formdata[create]','Add raid');
else 	$rta_ret2=button('formdata[submit]','Modify').' '.button('formdata[delete]','Delete');
$rta_ret2.=' '.button('formdata[cancel]','Cancel');
//$rta_ret2=button('submit','Submit');
//if ($rtv_raidid) $rta_ret2.=' '.button('submit','Delete');
$rta_ret.=span($rta_ret2,'','spaceright').br();
$rta_ret.=input('formview','hidden','raid');
$rta_ret=form(div($rta_ret),'.','post','trueform');
$rta_ret="$rta_headline\n$rta_ret";
//echo $rta_ret;
$rts_internal['main']=$rta_ret;
?>