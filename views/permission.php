<?php
/*******************************************************************************
 * permission.php
 * -----------------------------------------------------------------------------
 * Version 2.00 Alter permissions screen
 ******************************************************************************/
if (!rtf_p('permission_alter')) { // no permission to view this
	$rts_internal['main']="<h1>Error</h1>\n<p>You do not have the permission to "
	. "view this page.</p>";
	return;
}
$rta_list=rtd_select(rts_db_perm,'*','','ORDER BY `class`, `property`');
function subform() {
	import_request_variables ("p","rt_");
	global $rta_list, $rt_f, $rt_or;
	foreach ($rta_list as $k=>$v){
		$vartest=$v['property'];
		$varval=$v['value'];
		$varin=$rt_f[$vartest];
		$varor=$rt_or[$vartest];
		if ($varor) $varin=-$varin;
		if ($varval!=$varin) {
			$sql="UPDATE ".rts_db_perm." SET `value`=$varin WHERE `property`='$vartest'";
			mysql_query($sql);
			$rta_list[$k]['value']=$varin;
		}
	}
}
$rta_main="<h1>Permissions</h1>\n";
if ($_POST) { subform(); $rts_internal['message'].="Permissions updated.".br(); }
$rta_headrow=td("Level",true).td("Self".br()."Override",true).td("Description",true);
$rta_cat='';
$rta_tbl='';
foreach ($rta_list as $k) {
	if ($k['class']!=$rta_cat) { //-- new category, new heading
		$rta_cat=$k['class'];
		$rta_row=td($rta_cat.br()."Property",true).$rta_headrow;
		addrow($rta_tbl,$rta_row);
	}
	$rta_row=td($k['property']);
	switch ($k['property']){
		case 'guildrank_start':
			$rta_row.=td(rtff_grank('f['.$k['property']."]", $k['value']));
			break;
		case 'rtrank_start':
		case 'permission_alter':
			$rta_row.=td(rtff_rtrank('f['.$k['property']."]", $k['value'],-1));
			break;
		default:
			$rta_row.= td(rtff_rtrank('f['.$k['property']."]", $k['value'],0));
	}
	$rta_chk="<input type=\"checkbox\" name=\"or[".$k['property']."]\" ";
	if ($k['value']<0) $rta_chk.='checked="checked" ';
	$rta_chk.="/>";
	$rta_row.=td($rta_chk).td($k['desc']);
	addrow($rta_tbl,$rta_row);
}
// --- finalize table, add button and return
$rta_table=tbl($rta_tbl);
$rta_table.=button('submit','Submit changes');
$rta_table.=input("view",'hidden','permission');
$rta_main.=form($rta_table,'.');
$rts_internal['main']=$rta_main;
?>