<?php
/******************************************************************************
 *  rti-style.php
 ******************************************************************************
 * Version 2.10   Last updated: 2009-08-25
 * ----------------------------------------------------------------------------
 * style management system
 * For output to web page
 * Must be the last file called (not counting template.php)
 **************************************************************/

if (!$rtv_style) $rtv_style=rts_style;  //sets style to default;
if (!file_exists(_RT_SYS_HOME ."style/$rtv_style/template.php")) { //desired style not found
	if (!file_exists(_RT_SYS_HOME ."style/".rts_style."/template.php")) { //default style not found
		if (!file_exists(_RT_SYS_HOME ."style/default/template.php")) { //RT default dir deleted
			$rta_style_err="Style not found, and default styles have problems.  Using internal style";
			$rta_style_path="main";  //file to the default here
		} else {
			$rta_style_err="Style not found, using RT default style.";
			$rta_style_path="style/default";
		}
	} else {
		$rta_style_err="Requested style not found, using current style.";
		$rta_style_path="style/".rts_style;
	}
} else {
	$rta_style_err=null;
	$rta_style_path="style/$rtv_style";
}
if ($rta_style_err) {
	if ($rts_internal['message']) $rts_internal['message'].=br();
	$rts_internal['message'].=$rta_style_err;
}
$rta_style_path_web=_RT_SYS_WEB .$rta_style_path;
//$rta_style_path2=rts_domain . rts_syspath ."/$rta_style_path";
define ('rtw_style', "$rta_style_path_web");
$rta_stylesheet=_RT_SYS_WEB ."$rta_style_path/rt-style";
if ($rts_internal['message']) define ('rtw_message',"<div id=\"message\">$rts_internal[message]</div>");
if ($rts_internal['menu']) define ('rtw_menu',"<div id=\"menu\">$rts_internal[menu]</div>");
define ('rtw_main',"<div id=\"rt-main\">$rts_internal[main]</div>");
if ($rts_internal['time']) define ('rtw_time',"<div id=\"time\">$rts_internal[time]</div>");
if ($rts_internal['news']) define ('rtw_news',"<div id=\"menu\">$rts_internal[news]</div>");
if ($rts_internal['help']) define ('rtw_help',"<div id=\"help\">$rts_internal[help]</div>");
//include head
//need to load optional features
include _RT_SYS_MAIN ."head.php";
//include main
include _RT_SYS_HOME ."$rta_style_path/template.php";
//include bottom
$content="RaidTracker version $rts_version. &copy;2007-2013 Frank Spychaj (Martie, Quel'Thalas EU). "
.url("http://raidtracker.druidlove.com",'About RaidTracker');
echo "<div id=\"bottomside\">\n$content\n</div>\n";
echo "</body>\n</html>\n";
?>