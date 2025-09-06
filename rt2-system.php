<?php
/******************************************************************************
 * rt2-system.php
 ******************************************************************************
 * Version 2.10:  last updated 2009-08-25
 * ----------------------------------------------------------------------------
 * Stays in root folder.  Checks for system paths
 *****************************************************************************/
$rts_home_path=dirname(__FILE__);
define ('_RT_SYS_HOME', "$rts_home_path/");
define ('_RT_SYS_MAIN', "$rts_home_path/main/");
define ('_RT_SYS_INC',  "$rts_home_path/include/");
define ('_RT_SYS_STYLE',"$rts_home_path/style/");
if (is_dir(_RT_SYS_HOME ."install")) {  // install found
	define ('_RT_SYS_INSTALL', "$rts_home_path/install/");
}
if (is_file(_RT_SYS_HOME ."user/rt.php")) { //already installed, include files and set vars
	include _RT_SYS_HOME ."user/rt.php";
	include _RT_SYS_HOME ."user/db.php";
	$rta_websys=rts_domain . rts_syspath;
	define ('_RT_SYS_WEB', "$rta_websys/");
} else if ($rtv_sdomain) {  // install in order, set const with variable
	define ('_RT_SYS_WEB', "$rtv_sdomain$rtv_spath/");
} else {                    // install, steps 1, 2
	$rtv_sdomain="http://".$_SERVER['SERVER_NAME'];
	$rtv_spath=$_SERVER['REQUEST_URI'];
	if ($rtv_spath[(strlen($rtv_spath)-1)]=='/')
		$rtv_spath=rtrim($_SERVER['REQUEST_URI'],'/');
	$rtv_spath=str_replace ("rt2-system.php", '', $rtv_spath);
	define ('_RT_SYS_WEB',"$rtv_sdomain$rtv_spath/");
}
?>