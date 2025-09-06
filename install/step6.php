<?php
/*******************************************************************************
 * step6.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The sixth step of the install.
 * To check and create a TOS agreement if used.
 * (Not useful, as it goes straight to step 7
 ******************************************************************************/
include _RT_SYS_HOME . "/user/rt.php"; // get old file
if (rts_TOS==0) {
	$rtv_step++;
	include _RT_SYS_INSTALL ."step7.php";
	return;
}
if ($rtv_server) {
	if (!($rta_file=fopen("user/rt.php","w"))) {  //open rt.php for writing
		$rts_internal['main'].="<h1>Install error</h1>\n<p>File could not be created:  Please check that the /user folder is write accessible, and then hit Refresh to continue</p>";
		return;
	}
	// write database info in db.php
	fwrite ($rta_file, "<?php\n");
	foreach ($rtv_server as $k=>$v)
		fwrite ($rta_file, 'define ("rts_'.$k."\", \"$v\");\n");
	if ($rtv_server['TOS']=='false' ) {
		fwrite ($rta_file, 'define ("rts_tosv", "0");'."\n");
		fwrite ($rta_file, "?>\n");
		$rts_internal['main'].="<h1>Saving RaidTracker Settings</h1>\n<p>Settings successfully saved.</p>\n";
		$rta_form=button("button","Almost done");
		$rta_form.=input('step','hidden',$rta_step);
		$rts_internal['main'].=form(div($rta_form),'.');
	}
	fclose ($rta_file);
}
if ($rtv_server['TOS']=='true') {
	include "install/step6a.php";
	return;
}
if ($rtv_tosv) {
	if (!($rta_file=fopen("user/rt.php","a"))) {  //open rt.php for writing
		$rts_internal['main'].="<h1>Install error</h1>\n<p>File could not be created:  Please check that the /user folder is write accessible, and then hit Refresh to continue</p>";
		return;
	}
	fwrite ($rta_file, 'define ("rts_tosv", "'.$rtv_tosv."\");\n");
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	if (!($rta_file=fopen("user/tos.php","w"))) {  //open tos.php for writing
		$rts_internal['main'].="<h1>Install error</h1>\n<p>File could not be created:  Please check that the /user folder is write accessible, and then hit Refresh to continue</p>";
		return;
	}
	fwrite ($rta_file, $rtv_tos);
	fclose ($rta_file);
	$rts_internal['main'].="<h1>Saving RaidTracker Settings</h1>\n<p>Terms of Service has been saved</p>\n";
	$rta_form=button("button","Almost done");
	$rta_form.=input('step','hidden',$rta_step);
	$rts_internal['main'].=form(div($rta_form),'.');
}

?>