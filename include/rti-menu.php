<?php
$rta_menu=group('RaidTracker menu','h1','head','rtmenu').nl();
$rta_restrict=array('raid'=>true, 'create'=>true);
//---- maintenance mode check
if (rts_maint) $rta_menu.=div('Maintenance mode','maint','rtmenu').nl();
// for privs
$rta_mainid=rtc_ret('id');
$rta_rank=rtc_ret('prof');
$rta_main=rtc_ret('main');
//---- show menu here
if ($rta_mainid) {
	if (!$rta_restrict[$rtv_view])
		$rta_menuchar=div(url(rts_syspath."/?login=out",'Logout'),'log','rtmenu').nl();
	if (!$rts_maint||$rta_rank==1)
		$rta_menuchar.=div(rtf_character(rtf_acct2char($rta_mainid),0,2,0),'char','rtmenu').nl();
	$rta_menu.=div($rta_menuchar,'charbox','rtmenu').nl();
	if ((!$rts_maint||$rta_rank==1)&&!$rta_restrict[$rtv_view]) {
		if ($rtv_view!='calendar' && $rtv_view!='history') $rta_menu.=div(url(rts_syspath,"Home"),'raid','rtmenu').nl();
		if ($rtv_view!='settings') $rta_menu.=div(url(rts_syspath."/?playerid=".rtc_acct(),"Settings"),'settings','rtmenu').nl();
		if ($rtv_view!='guild') $rta_menu.=div(url(rts_syspath."?view=guild","Guild list"),'guild','rtmenu').nl();
		if (rtf_p('admin_view')) {
			if ($rtv_view!='admin') $rta_menu.=div(url(rts_syspath."?view=admin","Administration"),'admin','rtmenu').nl();
			if (rtf_p('view_log') && $rtf_view!="log") $rta_menu.=div(url(rts_syspath."?view=log","Log"),'log','rtmenu').nl();
			if (rtf_p('permission_alter') && $rtf_view!="permission") $rta_menu.=div(url(rts_syspath."?view=permission","Permissions"),'permission','rtmenu').nl();
			if (rtf_p('rank_alter') && $rtf_view!="rank") $rta_menu.=div(url(rts_syspath."?view=rank","Ranks"),'rank','rtmenu').nl();
			if (rtf_p('instance') && $rtf_view!="instance") $rta_menu.=div(url(rts_syspath."?view=instance","Instances"),'instance','rtmenu').nl();
			if (rtf_p('class_list') && $rtf_view!="class") $rta_menu.=div(url(rts_syspath."?view=class","Classes"),'class','rtmenu').nl();
			}
	}
} else {
	$rta_logtext="Login".(rts_maint?'':'/Create account');
	$rta_menu.=div(div(url(rts_syspath."/?login=in",$rta_logtext),'log','rtmenu'),'charbox','rtmenu').nl();
}
$rta_menu=div($rta_menu,'container','rtmenu').nl();
$_page['menu']=$rta_menu;  //ancient
$rts_internal['menu']=$rta_menu;
?>