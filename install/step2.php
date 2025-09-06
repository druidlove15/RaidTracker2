<?php
/*******************************************************************************
 * step2.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The second step of the install.
 * Checks for authorization to upgrade. If a new copy, asks for server info
 ******************************************************************************/
if ($rtv_install=='install') $rtv_pack='wow';
if (!($rta_test=fopen ("user/db.php","a"))) { // write properties not possible
	$rta_msg="<h1>Install error</h1>\n
	<p>The /user directory is not found or not writeable. Installation cannot continue unless this is writable. Please fix this and try again.</p>";
	$rtv_step--;
	$rta_button="Try again";
	$rta_form='';
} else {
	fclose ($rta_test);
	if ($rtv_install=='install') { // install here
		if (!$rtv_dbprefix) $rtv_dbprefix='rt_';
		$rta_msg="<h1>Server and Database information</h1>\n
		<p>In order to continue, we need information about your server and database. Keep in mind you need a MySQL database, and
		the rights to create tables.</p>";
		$rta_form="<h2>Database information</h2>\n";
		$rta_form.=textfield('server',"Server:", $rtv_server);
		$rta_form.=textfield('dbuser',"Username:", $rtv_dbuser);
		$rta_form.=passfield('dbpass',"Password:");
		$rta_form.=passfield('dbpassc',"Confirm Password:");
		$rta_form.=textfield('dbname',"Database name:", $rtv_dbname);
		$rta_form.=textfield('dbprefix',"Prefix for new tables:", $rtv_dbprefix);
//		$rtv_step--;
		$rta_form.="<h2>Server information</h2>\n";
		if (!$rtv_sdomain) {
			$rtv_sdomain="http://".$_SERVER['SERVER_NAME'];
			$rtv_spath=$_SERVER['REQUEST_URI'];
			if ($rtv_spath[(strlen($rtv_spath)-1)]=='/')
				$rtv_spath=rtrim($_SERVER['REQUEST_URI'],'/');
		}
		$rta_form.=textfield('sdomain',"Domain name:", $rtv_sdomain);
		$rta_form.=textfield('spath',"Path to RaidTracker:", $rtv_spath);
	} else { // if upgrade or repair
		$rta_msg="<h1>Verify upgrade</h1>\n
		<p>Only administrators may have the priviledges to upgrade. Please verify your account to continue. If you have a game pack, please enter this as well.</p>";
		$rta_form=passfield('email',"Account log-in:");
	}
	$rta_form.=textfield('pack',"Game pack to install:",$rtv_pack);
	$rta_form.="Do not include the .rtp extension here<br />\n";
	$rta_button="Submit";
}
$rta_form.=input('step','hidden',$rtv_step+1);
$rta_form.=input('install','hidden',$rtv_install);
$rta_form.=input('oldRT','hidden',$rtv_oldRT);
$rta_form.=button("button",$rta_button);
$rts_internal['main']=$rta_msg;
$rts_internal['main'].=form(div($rta_form),'.','post','trueform');
/* old data
if ($rta_test=fopen ("user/db.php","a")) {
	fclose ($rta_test);  //close file
	if ($rtv_install=="upgrade") {  // upgrade
		$rtv_pack='wow';
		$rts_internal['main']="<h1>Verify upgrade</h1>\n<p>Please verify your account to "
		. "continue.  Also, please make sure your game pack file is in your install "
		. "directory before continuing (if necessary).</p>";
		$rta_form.=passfield('email',"Account log-in:");
		$rta_form.=input('install','hidden',$rtv_install);
		$rta_form.=textfield('pack',"Game pack to install:"); //,$rtv_pack
	} else {
		// ----------------- write properties possible
		if (!$rtv_dbprefix) $rtv_dbprefix="rt_";
		$rts_internal['main']="<h1>Server and Database information</h1>\n<p>In order to continue, we need to know your server "
		."and database information.  Keep in mind that you need a MySQL database, with rights to create tables.</p>";
		$rta_form=textfield('server',"Server:", $rtv_server);
		$rta_form.=textfield('dbuser',"Username:", $rtv_dbuser);
		$rta_form.=passfield('dbpass',"Password:");
		$rta_form.=passfield('dbpassc',"Confirm Password:");
		$rta_form.=textfield('dbname',"Database name:", $rtv_dbname);
		$rta_form.=textfield('dbprefix',"Prefix for new tables:", $rtv_dbprefix);
		$rta_form.="<h2>Server information</h2>\n";
		if (!$rtv_sdomain) {
			$rtv_sdomain="http://".$_SERVER['SERVER_NAME'];
			$rtv_spath=$_SERVER['REQUEST_URI'];
			if ($rtv_spath[(strlen($rtv_spath)-1)]=='/')
				$rtv_spath=rtrim($_SERVER['REQUEST_URI'],'/');
		}
		$rta_form.=textfield('sdomain',"Domain name:", $rtv_sdomain);
		$rta_form.=textfield('spath',"Path to RaidTracker:", $rtv_spath);
		$rta_form.=textfield('pack',"Game pack to install:",$rtv_pack);
		$rta_form.="Do not include the .rtp extension here<br />\n";
		$rta_form.=input('install','hidden',$rtv_install);
		$rta_form.=button("button","Continue");
		$rta_form.=input('step','hidden','3');
		$rta_form="<h2>Database information</h2>\n".$rta_form;
	}
} else{
// ------------------ write properties not possible
	$rts_internal['main']="<h1>Install error</h1>\n<p>Installation is not possible.  Please make sure the '/user' directory where you "
	."installed RaidTracker is writable, and then try again.</p>";
	$rta_form=button("button","Try again");
	$rta_form.=input('step','hidden','2');
	$rta_form.=input('install','hidden',$rtv_install);
	$rta_form.=input('oldRT','hidden',$rtv_oldRT);
}
$rts_internal['main'].=form(div($rta_form),'.','post','trueform');
*/

?>