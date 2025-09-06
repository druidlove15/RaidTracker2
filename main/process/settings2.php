<?php
/*******************************************************************************
 * settings2.php
 * -----------------------------------------------------------------------------
 * Version 2.00  designed to deal with personal settings
 ******************************************************************************/
$rtv_view='settings';
$rta_playerinfo=rtd_select(rts_db_acct,'*',"id=$rtv_playerid");
if ((!$rtv_formdata['datef']) ||(!$rtv_formdata['timef']) ||(!$rtv_formdata['email'])) {
	$rts_internal['message'].="Cannot change personal info, one or more fields are blank".br();
	return;
}
$rta_sql=null;
if ($rta_playerinfo['datef'] != $rtv_formdata['datef']) //date not same
	$rta_sql['datef']=$rtv_formdata['datef'];
if ($rta_playerinfo['timef'] != $rtv_formdata['timef']) //time not same
	$rta_sql['timef']=$rtv_formdata['timef'];
if ($rta_playerinfo['email'] != $rtv_formdata['email']) { //e-mail not same
	$rta_sql['email']=$rtv_formdata['email'];
	rtf_log("character",bold("Changed e-mail address"));
}
if ($rta_playerinfo['view'] != $rtv_formdata['view'])      //view not same
	$rta_sql['view']=$rtv_formdata['view'];
if (!$rta_sql) return;
rtd_update(rts_db_acct, $rta_sql, "id=$rtv_playerid");
$rts_internal['message'].="Changed personal settings".br();	
?>