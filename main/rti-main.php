<?php
/******************************************************************************
 * rti-main.php
 ******************************************************************************
 * Version 2.5 : last update:  2009-08-21
 * ----------------------------------------------------------------------------
 * core file to load and install RT2
 *****************************************************************************/

/**********************************************
 * note:  need to include title here (take it out of rti_settings.php
 **********************************************/
/*******************************************************************************
 * function rtf_cookie
 * -----------------------------------------------------------------------------
 * sets cookie if needed.
 ******************************************************************************/
function rtf_cookie($type, $name, $value, $expire='', $path='/') {
	if ($type=='s') {
		if (!$expire) $expire=time()+3600;
		setcookie($name, $value, $expire, $path);
	}
}
//----------------- retrieve form functions
//import_request_variables('gp','rtv_');
extract($_REQUEST, EXTR_PREFIX_ALL|EXTR_REFS, 'rtv');						// *** correct for PHP 5.4+
//------------------------------------------------ get local and web path 
include_once "rt2-system.php";
//------------------------------------------------ include system files
include_once _RT_SYS_INC ."rti-system.php";      // for version tracking
include_once _RT_SYS_INC ."rti-html.php";        // html functions
include_once _RT_SYS_INC ."rt2-time.php";        // time class functions

include_once _RT_SYS_INC ."rt-lib.php";          // general functions (inc. error)
include_once _RT_SYS_INC ."rti-db.php";          // database functions
if (!defined('_RT_SYS_INSTALL')) {               // if not install
	include_once _RT_SYS_INC ."rti-settings.php"; //first file for variable settings
	rtd_openDB($rts_dbserver, $rts_dbuser, $rts_dbpass, $rts_db);//open DB ready to go.
	include_once _RT_SYS_INC ."rt-icons.php";        // icons function
	include_once _RT_SYS_INC ."rt-raidcheck.php";    // raidcheck functions
	include_once _RT_SYS_INC ."rti-lib1.php";     //cookie and other lib functions
//------------------------------------------------ if a form has data to process
	if ($rtv_login) $rtv_formview='login';
	if ($rtv_formview) {
		require _RT_SYS_MAIN ."process/$rtv_formview.php";
	}
}
/******************************************************************************
 * Time to look for the file to load
 *****************************************************************************/
// ------------------------------------------------ check install first
if (defined('_RT_SYS_INSTALL')) {

	$rts_include_main=_RT_SYS_INSTALL . "index.php";  //file needed to load
	//$rts_file_load="install/index.php";
	$rtv_style='default';
// ----------------- Then check for maintenance mode w/out a login screen
} else if (rts_maint && $rtv_view!="login") {
		include_once _RT_SYS_MAIN ."rti-maint.php";
} // check for TOS changes here  /* ************* old code here */
//	if (rts_TOS=='true'&& !$rts_internal['main'])
//		include_once $rta_sysloc."/tos/rt-tos.php"; //TOS screen  <-- need to change this
// ----------------- Checking if the main file is not included yet from the above
if (!$rts_include_main) {
	if ($rtv_raidid) { 						// checks for raid ID passed in
		if ($rtv_submit) { 					//if raid details were submitted
				//include raidchange.php	//process file
				if (!$rtv_raidid) $rtv_view='main';  //raid deleted, go back to main
		}
		if (!$rtv_view) $rtv_view='list';
	}
	if ($rtv_playerid && !$rtv_view) $rtv_view='settings';
	if ($rtv_view=='create') {  //create character
//			if (rts_TOS) $rtv_view='tos';
//			else $rtv_view='create';
	}
	if ($rtv_view=='main' || !$rtv_view) { //main screen. checks for redirected to main, or none at all
		$rta_view=rtc_view();
		if ($rta_view=='c') $rtv_view='calendar';
		if ($rta_view=='h') $rtv_view='history';
	}
	switch ($rtv_view) {
		case 'calendar':
			$rts_include_main= _RT_SYS_HOME ."views/calendar.php";
			//include $rta_sysloc.'/views/calendar.php';  //calendar view  temp
			$rta_currview='Calendar view';
			$rta_alt='history';
			break;
		case 'history':
			$rts_include_main= _RT_SYS_HOME . "views/history.php";
			//include $rta_sysloc.'/history/rt-main.php';  //history view
			$rta_currview='History view';
			$rta_alt='calendar';
			break;
		case 'list':
		case 'guild':
		case 'login':
		case 'settings':
		case 'raid':
		case 'create':
		case 'admin':
		case 'permission':
		case 'rank':
		case 'rofp':
		case 'instance':
		case 'class':
		case 'log':
		case 'swap';
			$rts_include_main= _RT_SYS_HOME . "views/$rtv_view.php";
			break;
		case 'tos':
			$rts_include_main= _RT_SYS_HOME . "tos/rt-main.php";
			//include $rta_sysloc.'/tos/rt-main.php';  //TOS view
			break;
		case 'other':
			if (file_exists(_RT_SYS_HOME ."views/$rta_incfile")) {
				include $rta_sysloc.'/calendar/rt-main.php';  //other view
				break;
			}
		default:
			$rts_internal['main']="<h1>Fatal error</h1>\n<p>File cannot be found, or someone is messing with the address bar.  RaidTracker cannot continue.";
	}
}
if ($rts_include_main) include $rts_include_main;   //include main file here
if (!defined('_RT_SYS_INSTALL')) {                       //Only include these if not installing
	include _RT_SYS_HOME ."inc2/news.php";               //include news here
	include _RT_SYS_INC ."rti-menu.php";            //include menu here
}
include _RT_SYS_MAIN ."rti-style.php";                  //test style here
return;
?>
