<?php
/*******************************************************************************
 * rofprecruit.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Addon for adding recruitment to rofp's home page
 ******************************************************************************/
//-- check for permissions
$rtv_view='admin';
if (!($rta_file=fopen("../user/recruit.php","w"))) {  //open rt.php for writing
	$rts_internal['message'].="Settings cannot be saved.  File is protected.".br();
	return;
}
	fwrite ($rta_file, "<?php\n\$rofp_class=array(\n");
	$i=0;
	foreach ($rtv_class as $k=>$v) {
		if ($i) fwrite($rta_file,",\n"); else $i++;
		fwrite ($rta_file, "\"$k\"=>\"$v");
		if ($rtv_notes[$k]) fwrite ($rta_file,$rtv_notes[$k]);
		fwrite ($rta_file,"\"");
	}
	fwrite ($rta_file, ");\n\$rofp_update=array(\n");
	fwrite ($rta_file, "'date'=>'".date("Y-m-d",rts_currtime)."',\n");
	fwrite ($rta_file, "'name'=>".rtc_acct().");\n");
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rts_internal['message'].="Settings saved.  Settings go in effect immediately.".br();
?>