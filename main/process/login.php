<?php
/*******************************************************************************
 * login.php
 * -----------------------------------------------------------------------------
 * Version 2.00 login process
 ******************************************************************************/
//---
if (isset($_COOKIE[rts_cookie])) {  //cookie enabled, need disabled to logout
//function rtf_cookie($type, $name, $value, $expire='', $path='/') {
	rtf_cookie('s',rts_cookie,false,time()-3600,'/');
	$rtc_session['id']=0;
	$rtc_session['main']=$rts_guestname;
	$rtc_session['prof']=11;
	$rtc_session['view']='c';
	$rtc_session['charid']=0;
	$rts_internal['message'].="You have logged out.".br();
	$rtv_view="main";
	return;
}
if (!$rtv_formdata['login'] && !$rtv_formdata['create']) { // not logged in
	$rtv_view='login';
	return;
}
$rta_chardata=rtd_select(rts_db_acct,'*', "email='$rtv_formdata[email]'"); // check for e-mail
if ($rtv_formdata['create']) { // create button pressed
	if (rts_maint==1) {  // cannot create accounts during maintenance
		$rts_internal['message'].="Creation of accounts denied due to maintenance.".br();
		$rtv_view='main';
	} else if ($rta_chardata) { // account with that e-mail address already exists
		$rts_internal['message'].="Creation error.  e-mail address already exists.".br();
		$rtv_view='login';
	} else $rtv_view='create';
} else {
	if (!$rta_chardata) {  // bad e-mail address
		$rts_internal['message'].="Login error--Invalid e-mail address.  Try again.".br();
		$rtv_view='login';
	} else if (rts_maint==1 && $rta_chardata['rtrank'] !=1) { // maintenance mode
		$rts_internal['message'].="Cannot log in due to maintenance.".br();
		$rtv_view='main';
	} else {
		$rta_cookie_value=$rta_chardata['id']."$".$rta_chardata['main'].'$'.$rta_chardata['rtrank']."$".$rts_cookiev;
		rtf_cookie('s',rts_cookie,$rta_cookie_value,time()+(3600*24*30),'/');
		$rtv_view='main';
		$rts_internal['message'].="Log in successful".br();
		$rtc_session['id']=$rta_chardata['id'];
		$rtc_session['prof']=$rta_chardata['rtrank'];
		$rtc_session['settings']=$rta_chardata['settings'];
		$rtc_session['view']=$rtc_session['settings'][0];
		if (strlen($rtc_session['settings'])>1) $rtc_session['tos']=$rtc_session['settings'][1];
		else $rtc_session['tos']=null;
		$rtc_session['main']=$rta_chardata['main'];
		$rtc_session['charid']=rtd_selcol(rts_db_char,'id',"`char`='".$rtc_session['main']."'");
	}
}
?>