<?php
/**********************************************************************
 * rt-raidhead.php
 **********************************************************************
 * Ver. 2.0 style of displaying top head
 **********************************************************************/
$rta_raidinfo=rtd_select(rts_db_list,'*',"id=$rtv_raidid");
//-- calculate timestamps
$rta_tstamp['start']=strtotime($rta_raidinfo['date']);
$rta_tstamp['end']=strtotime($rta_raidinfo['endtime']);
$rta_tstamp['inv']=strtotime($rta_raidinfo['inv']);
$rta_tstamp['fnew']=strtotime($rta_raidinfo['freezenew']);
$rta_tstamp['fdel']=strtotime($rta_raidinfo['freezedel']);
$rta_tstamp['curr']=rts_currtime;
//-- create local time info from timestamps
foreach ($rta_tstamp as $k=>$v)
	$rta_time[$k]=rtf_datetime($v,'t');
//-- create local date stamp from timestamp (start)
$rta_time['date']=rtf_datetime($rta_tstamp['start'],'d');
//-- calculate roles
$rtg_roles=explode('/',$rta_raidinfo['roles']);
//******************* Formulate output now *************************************
//--- create date box
$rta_datebox=div(span("Date:",'','heading').' '.span($rta_time['date'])).NL
. div(span("Start:",'','heading').' '.span($rta_time['start'])).NL
. div(span("End:",'','heading').' '.span($rta_time['end'])).NL
. div(span("Invite:",'','heading').' '.span($rta_time['inv'])).NL
. div(span("Withdrawl close:",'','heading').' '.span($rta_time['fdel'])).NL
. div(span("Subscription close:",'','heading').' '.span($rta_time['fnew'])).NL
. div(span(url(rts_domain.rts_syspath."/","Link to raid","raidid=$rtv_raidid"))).NL;

$rts_internal['time']=div($rta_datebox);
//--- now the heading
$rta_heading="<h1>Raid information</h1>".NL
.div(span("Location:",'','heading')." ".span("<img src=\"".rts_syspath."/images/instance/"
.$rta_raidinfo['icon'].".png\" alt=\"".$rta_raidinfo['icon']."\" /> $rta_raidinfo[instance]",'','field')).NL;
if ($rta_raidinfo['note'])
	$rta_heading.=div(span("Notes:",'','heading').' '.span($rta_raidinfo['note'],'','field')).NL;
if ($rta_raidinfo['offnote']&&rtf_p('view_officernote'))
	$rta_heading.=div(span("Officer note:",'','heading').' '.span($rta_raidinfo['offnote'],'','field')).NL;
$rts_internal['main']=$rta_heading;
//--- now the form
if (!rtf_p('raid_alter')) return;  //no modify info privs so quit.
$rta_form=NL.input('raidid','hidden',$rtv_raidid)
.input('formview','hidden','raid')
.button('submit','Modify details...');
$rts_internal['main'].=NL.form(div($rta_form),'.').NL;
?>