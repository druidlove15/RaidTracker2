<?php
/*********************************************
 * rti-main.php
 *********************************************
 * new main files for ver 2.00
 *********************************************/
echo "cookie set?";
include_once "rti-html.php";      //html functions
include_once "rt-lib.php";        //general functions (inc. error)
include_once "rti-system.php";    //for version tracking
import_request_variables('gp','rtv_');
//--check for install file
if (is_dir('install')) {
	include 'install/index.php';
	exit;
}
include_once "rti-settings.php";  //first file for variable settings
include_once "rti-db.php";        //for db functions

//open DB ready to go.
rtd_openDB($rts_dbserver, $rts_dbuser, $rts_dbpass, $rts_db);
//import_request_variables('gp','rtv_');
include_once "rti-lib1.php";      //cookie and other lib functions
if ($rtv_special) {
	$rta_filebase=(is_dir('create')?'':".");
	include $rta_filebase."./create/rti-create.php";
}
if (rts_TOS=='true')
include_once "rt-tos.php"; //TOS screen
$_head_parm[]="<script type=\"text/javascript\" src=\"".rts_syspath."/scripts/dropdowncontent.js\">
/***********************************************
* Drop Down/ Overlapping Content- © Dynamic Drive (www.dynamicdrive.com)
* This notice must stay intact for legal use.
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/
</script>\n";
include_once 'rti-menu.php';      //menu
$rta_style=((file_exists("style/$rtv_style/settings.php")||file_exists("../style/$rtv_style/settings.php"))?$rtv_style:rts_style);
if (rts_maint && rtc_priv()!=1 && !stristr($title,'login')) {
	$incmain='outoforder.php';
	$incside='rt-empty.php';
	$title='Maintenance';
	$rta_view='m';
}
if ($rt_menu=='main' && (!$rtv_special || $rta_createform==1)) {//to figure out right view
	if ($rtv_view) $rta_view=$rtv_view[0];
	else if (!$rta_view) $rta_view=rtc_view();
	if ($rta_view=='c') $rta_path="calendar/";
	else if ($rta_view=='h') $rta_path='history/';
	else if ($rta_view=='m') $rta_path='./';
	else if ($rta_view=='t') $rta_path='tos/';
	else rtf_error(101, "view=$rta_view");
	$incmain="$rta_path$incmain";
	include "style/$rta_style/settings.php";
}
else if ($rtv_special) {
	$incmain=$rta_filebase."./create/$incmain";
	include "style/$rta_style/settings.php";
}
else {
	if ($rta_view=='t') $incmain="../tos/$incmain";
	include "../style/$rta_style/settings.php";
}
?>
