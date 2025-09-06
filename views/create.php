<?php 
/*******************************************************************************
 * create.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Create character/account
 ******************************************************************************/
//-- test
if ($rtv_formdata['email']) {
	if ($rtv_formdata['tosv']) 
		$rta_form.=input('formdata[tos]','hidden',$rtv_formdata['tosv']);
	else
		$rta_form.=input('formdata[tos]','hidden','0');
	$rta_form.=input('formdata[email]','hidden',$rtv_formdata['email'])
	.passfield('formdata[confirm]','Confirm e-mail address:');
	$rta_head="<h1>Create account</h1>\n<p>Step 2.  Enter your main character now. "
	."After your account is created, you can come back and enter your remaining "
	."characters.</p>\n";
} else if (!$rtv_playerid) {
	$rts_internal['main']="<h1>Fatal error</h1>\n<p>Invalid or missing parameter "
	."passed in.</p>";
	return;
} else {
	$rta_form=input('playerid','hidden',$rtv_playerid);
	$rta_head="<h1>Create alt</h1><p>Please enter your alt information here.</p>\n";
}
$rta_form.=textfield('formdata[name]',"Character name:",$rtv_formdata['name'])
 .textfield('formdata[level]',"Level",($rtv_formdata['level']?$rtv_formdata['level']:rts_levdefault),'line','1');
$rta_form.=rtf_classsel('formdata[class]',$rtv_formdata['class']).br();
$rta_form.=rtff_role('formdata[role]',$rtv_formdata['role'],1).br();
$rta_form.=span('&nbsp;','','spaceleft')
.span(button('submit')." ".button('formdata[cancel]','Cancel'),'','spaceright');
$rta_form.=input('formview','hidden','create');
$rta_form=form($rta_form,'.','post','trueform');
$rts_internal['main']=$rta_head.$rta_form;
?>