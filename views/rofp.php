<?php
/*******************************************************************************
 * rofp.php
 * -----------------------------------------------------------------------------
 * Version 2.00 extra module: To allow changing portal page
 ******************************************************************************/

if (!rtf_p('rofp_main')) {
	$rts_internal['main']="<h1>Error</h1>\n<p>You do not have the priviledges to "
	. "view this page.</p>";
	return;
}

$rta_status_type=array("o"=>"Open",'c'=>'Closed','l'=>'Limited');
include "../user/recruit.php";
$rofp_char=rtf_acct2char($rofp_update['name']);

$rts_internal['main']="<h1>Reflection of Perfection control panel</h1>\n";
$rts_internal['main'].="<p>Control panel 1.1</p>\n";
$rta_form.="Status last updated $rofp_update[date] by "
.rtf_character($rofp_char,0,2,0).br();
//. "<img src=\"".rts_syspath."/images/$rofp_update[class].gif\" alt=\"({$rofp_update['class']})\" /> "
//.$rofp_update['name'].br();
foreach($rofp_class as $k=>$v) {
	$rta_form.="<img src=\"".rts_syspath."/images/$k.gif\" alt=\"({$k})\" /> ";
	$rta_form.="<select name=\"class[$k]\">\n";
	foreach ($rta_status_type as $rta_key=>$rta_value) {
		$rta_form.="<option value=\"$rta_key\"";
		if ($rta_key==$v[0]) $rta_form.=' selected="selected"';
		$rta_form.=">$rta_value</option>".NL;
	}
	$rta_form.="</select>".NL;
	//add comments here:
	$v=substr($v,1);
	$rta_form.=textfield("notes[$k]",'',$v);
}
$rta_form.=button("submit","Submit");
$rta_form.=input('formview','hidden','rofprecruit');
$rta_form=form($rta_form,'.');
$rts_internal['main'].="<h2>Recruiting status</h2>\n".$rta_form;

include "rofp/news.php";

?>