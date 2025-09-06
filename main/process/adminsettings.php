<?php 
/*******************************************************************************
 * settings.php
 * -----------------------------------------------------------------------------
 * Version 2.00  save settings to file
 ******************************************************************************/
//
if (!($rta_file=fopen(_RT_SYS_HOME. "user/rt.php","w"))) {  //open rt.php for writing
	$rts_internal['message'].="Settings cannot be saved.  File is protected.".br();
	return;
}
	fwrite ($rta_file, "<?php\n");
	$rtv_format=$rtv_formdata['sys'];
	foreach ($rtv_format as $k=>$v)
		fwrite ($rta_file, 'define ("rts_'.$k.'", \''.addslashes($v)."');\n");
	if ($rtv_server['TOS']=='false' ) {
		fwrite ($rta_file, 'define ("rts_tosv", "0");'."\n");
	}
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rts_internal['message'].="Settings saved.  Settings go in effect immediately.".br();

?>