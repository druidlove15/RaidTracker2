<?php
/*******************************************************************************
 * step4.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The fourth step of the install.
 * If upgrade, it will skip to step 5
 * If a new copy, will ask for account info, and create account.
 ******************************************************************************/
$rtv_stepnext=4;
if ($rtv_install=='upgrade') {
	$rtv_step++;
	include _RT_SYS_INSTALL . "/step5.php";
	return;
}
$rta_form=input('server[domain]','hidden',$rtv_server['domain']);
$rta_form.=input('server[syspath]','hidden',$rtv_server['syspath']);
$rta_form.=input('oldRT','hidden',$rtv_oldRT);
$rta_form.=input('install','hidden',$rtv_install);

//---------------------------------- Check for submitted data
// ********** first try. all fields required here
if ($rtv_acct['email'] && (!$rtv_char['char'] || !$rtv_char['level'])) {
	$rts_internal['message']="All fields are required, please try again";
	$rtv_acct['email']='';
} else if ($rtv_acct['confirm'] && ($rtv_acct['email']!=$rtv_acct['confirm'])) { //form submitted, email didn't match
	$rtv_acct['email']='';
	$rts_internal['message']="e-mail did not match.  Please try again";
}
if (!$rtv_acct['email']) { // no form submitted
	include_once _RT_SYS_HOME ."user/db.php";
	include_once _RT_SYS_INC ."rti-settings.php"; //first file for variable settings
	rtd_openDB($rts_dbserver, $rts_dbuser, $rts_dbpass, $rts_db);//open DB ready to go.
	include_once _RT_SYS_INC ."rt-icons.php";        // icons function

	$rta_header="<h1>Create account</h1>\n
	It's time to create your account. Please fill in the details for your main character at this time.
	After the install is completed, you may add other characters in your settings. All fields are
	required. <br />\n
	Please also remember your e-mail address, as this is how you log in to your account.</p>\n";
	$rta_form.=textfield('acct[email]','e-mail address:',$rtv_acct['email']);
	$rta_form.=textfield('char[char]','Character:',$rtv_char['char']);
	$rta_form.=textfield('char[level]','Level:',$rtv_char['level'],'line','1');
	$rta_form.=rtf_formcontrol('class','char[class]',"Class").br();
	$rta_form.=rtf_formcontrol('role','char[role]',"Main role").br();
	
	//$rta_form.=rtf_classsel('char[class]',$rtv_char['class']).br(); //need 2 fix
	
	//$rta_form.=rtff_role('char[role]',$rtv_char['role'],1).br();
	$rta_button="Continue";
	$rtv_stepnext=4;

/*
	$rta_form=form(div($rta_form2.$rta_form),'.','post','trueform');
	$rts_internal['main'].=$rta_form;
	return;
*/
} else if (!$rtv_acct['confirm']) {  // submitted, need a confirmation
	$rta_header="<h1>Confirm account</h1>\n
	<p>To avoid typographical errors, please reenter your e-mail address.</p>\n";
	foreach ($rtv_acct as $k=>$v) {
		$rta_form.=input ("acct[$k]",'hidden', $v);
	}
	foreach ($rtv_char as $k=>$v) {
		$rta_form.=input ("char[$k]",'hidden', $v);
	}
	$rta_form.=passfield('acct[confirm]','e-mail address:');

	$rta_form.=input('step','hidden',$rtv_step);
	$rta_button="Validate";
/*	
	$rta_form2.=span('&nbsp;','','spaceleft').span(button('submit'),'','spaceright');
	$rta_form=form(div($rta_form2.$rta_form),'.','post','trueform');
	$rts_internal['main'].=$rta_form;
	return;
*/
} else {
	include "user/db.php";
	rtd_openDB($rts_dbserver, $rts_dbuser, $rts_dbpass, $rts_db);
	//--- from rti-settings.php to better usage
	define ('rts_db_acct', "{$rts_dbprefix}account");
	define ('rts_db_char', "{$rts_dbprefix}char");
	unset ($rtv_acct['confirm']);

	$rtv_char['char']=preg_replace("/([\s])/","", $rtv_char['char']);
	$rtv_acct['email']=preg_replace("/([\s])/","", $rtv_acct['email']);
	$rtv_char['char']=ucfirst(strtolower($rtv_char['char']));
	$rtv_acct['main']=$rtv_char['char'];
	$rtv_acct['guildrank']=1;
	$rtv_acct['rtrank']=1;
	$rtv_acct['settings']='c0';
	$rta_temp=rtd_insert(rts_db_acct,$rtv_acct);
	$rta_acctid=rtd_selcol(rts_db_acct,'id',"email='".$rtv_acct['email']."'");
	$rtv_char['account']=$rta_acctid;
//				case 'char':
//	$rtv_char['char']=preg_replace("/([\s])/","", $rtv_char['char']);
//	$rtv_char['char']=ucfirst(strtolower($rtv_char['char']));
	$rta_temp=rtd_insert(rts_db_char, $rtv_char);
	$rta_charid=rtd_selcol(rts_db_char,'id',"`char`='$rtv_char[char]'");
	$rta_header="<h1>Account created</h1>\n
	<p>Your account is now created. Now we can proceed to the next step.</p>\n";
	$rta_button="Continue";
	$rtv_stepnext=5;
}
	//$rts_internal['main']="<h1>Account created</h1>\n<p>Your account is created.  Now we can proceed to the next step.</p>\n";
	//$rta_form.=input('install','hidden',$rtv_install);
	//$rta_form.=input('oldRT','hidden',$rtv_oldRT);
$rta_form.=input('step','hidden',$rtv_stepnext);
$rta_form.=input('server[domain]','hidden',$rtv_server['domain']);
$rta_form.=input('server[syspath]','hidden',$rtv_server['syspath']);
$rta_form.=span('&nbsp;','','spaceleft').span(button($rta_button),'','spaceright');
$rta_form=form(div($rta_form),'.','post','trueform');
$rts_internal['main']=$rta_header.$rta_form;

?>