<?php
/*******************************************************************************
 * create.php
 * -----------------------------------------------------------------------------
 * Version 2.00 Process creation character/alt
 ******************************************************************************/
if ($rtv_formdata['cancel']) {
	if ($rtv_playerid) $rtv_view='settings'; else $rtv_view='main';
	return;
}
$rta_charval=rtd_select (rts_db_char,'*',"`char`='$rtv_formdata[name]'"); //character check
if ($rta_charval) { //duplicate character name
	$rts_internal['message'].="Character already exists";
	$rtv_view='create';  // go back to form view
	return;
} else if (!$rta_formdata['name'] || !$rta_formdata['level'])
if (!$rtv_playerid) {
	if ($rtv_formdata['email']!=$rtv_formdata['confirm']) {
		$rts_internal['message'].="Your e-mail addresses don't match.  Please try again.";
		$rtv_view='main';
		return;
	}
	$rtv_char['char']=preg_replace("/([\s])/","", $rtv_formdata['name']);
	$rtv_acct['email']=preg_replace("/([\s])/","", $rtv_formdata['email']);
	$rtv_char['char']=ucfirst(strtolower($rtv_char['char']));
	$rtv_acct['main']=$rtv_char['char'];
	$rtv_acct['guildrank']=rtd_selcol(rts_db_perm,'value',"property='guildrank_start'",'',1);
	$rtv_acct['rtrank']=rtd_selcol(rts_db_perm,'value',"property = 'rtrank_start'",'',1);
	$rtv_acct['settings']='c'.$rtv_formdata['tos'];
//	var_dump ($rtv_acct);
//	var_dump ($_POST);
//	exit;
	$rta_temp=rtd_insert(rts_db_acct,$rtv_acct);
	$rta_acctid=rtd_selcol(rts_db_acct,'id',"email='".$rtv_acct['email']."'");
	$rtv_playerid=$rta_acctid;
	$rtv_char['account']=$rta_acctid;
}
$rtv_char['char']=preg_replace("/([\s])/","", $rtv_formdata['name']);
$rtv_char['char']=ucfirst(strtolower($rtv_char['char']));
$rtv_char['level']=$rtv_formdata['level'];
$rtv_char['class']=$rtv_formdata['class'];
$rtv_char['account']=$rtv_playerid;
$rtv_char['role']=$rtv_formdata['role'];
$rta_temp=rtd_insert(rts_db_char, $rtv_char);
$rta_charid=rtd_selcol(rts_db_char,'id',"`char`='$rtv_char[char]'");
// --------------------- log actions
if ($rtv_formdata['email']) {
	$rts_internal['message'].="Account created.  Please log in to access your account.".br();
	rtf_log('account','Account created for [pchar]'.$rta_charid.'[/pchar]');
	$rtv_view='main';
} else {
	$rtv_view='settings';
	$rts_internal['message'].="New character created".br();
	rtf_log('character',"Character created: [pchar]".$rta_charid."[/pchar]");
}
?>