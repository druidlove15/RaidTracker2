<?php
/*******************************************************************************
 * step1.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The first step of the install, to check for any upgrade/repairs
 ******************************************************************************/

$rtv_install='install'; // install is new, upgrade is old, repair is repair
$rtv_oldRT='0';         // upgrade from older version
if (file_exists(_RT_SYS_HOME.'user/db.php')) { // an older version is found
	include_once _RT_SYS_HOME ."user/rt.php";
	include_once _RT_SYS_INC ."rti-settings.php";
	include_once _RT_SYS_HOME ."user/db.php";
	// get versions
	$rts_sysver=RTS_VERSION; // system version as installed
	if (defined("rts_file_version")) $rts_filever=rts_file_version; //file version is for 2.50 or later
	else $rts_filever='0'; // not set. for versions <=2.10.1	
	// check DB version
	rtd_openDB($rts_dbserver, $rts_dbuser, $rts_dbpass, $rts_db);//open DB ready to go.
	$rts_dbver=rtd_selcol(rts_db_keys,"value","category='RT_system'");
	// compare versions
	// check if a version is too old (less than 2.10)
	$rts_oldestver="2.10";  //oldest version to run the install program
	$rts_latestver="2.50";  //last version that needs to run install program
	if ($rts_dbver<$rts_oldestver) { // too old
		$rts_internal['main']="<h1>RaidTracker Error</h1>\n"
		."<p>Sorry, this version of RaidTracker cannot upgrade your older version. The version we have detected is $rts_dbver. (If there isn't a number here, it could be unknown, or a corrupted
		database table.) In order to upgrade, please move or delete all files in your /user directory. Please also beware that you will need to create new tables, as you cannot
		use the older tables.<br />
		For more information, please check the FAQ's on ".url('http://raidtracker.fridlunds.org',"the official RaidTracker page").".<br />\n
		If you are not an administrator, please wait, or contact your RT admin for more info.</p>";
		return;
	}
	if ($rts_dbver==$rts_sysver) { //already installed. check if rt.php is not corrupted.
		if ($rts_filever==$rts_sysver) { //rt.php is found.  (rt.php 2.50+ has ver info)
			$rts_internal['main']="<h1>RaidTracker Error</h1>\n"
			."<p>We have detected that RaidTracker is already installed with this version. If you are the administrator, please remove the /install folder, and then refresh this page. If you are
			not the admin, please contact your RT admin.<br />\n
			If you are trying to repair this installation, please rename or delete the /user/rt.php file and then refresh this page.<br />\n
			For more information, please check the FAQ's on ".url('http://raidtracker.fridlunds.org',"the official RaidTracker page").".</p>";
			return;
		}
		if ($rts_filever>$rts_sysver) { //rt.php is found, version sharing?  (rt.php 2.50+ has ver info)
			$rts_internal['main']="<h1>RaidTracker Error</h1>\n"
			."<p>We have detected that RaidTracker is already installed with this version, and this file is being shared with a later version of RaidTracker. If you are trying to downgrade, and 
			are the administrator, please remove or rename the /user/rt.php file and then refresh this page. ".bold("Please also check if the tables are compatible.")
			."If you wish to reinstall RT completely, remove all files in your /user directory. Note that you cannot reuse the tables in this case.<br />\n
			For more information, please check the FAQ's on ".url('http://raidtracker.fridlunds.org',"the official RaidTracker page").".</p>";
			return;
		}
		$rtv_install='repair';
		$rtv_oldRT=$rtv_dbver;
		
		echo "Corrupt file";
		// fix a corrupted file
	} else if ($rts_dbver > $rts_sysver) { //newer version, table
		$rts_internal['main']="<h1>RaidTracker Error</h1>\n"
		."<p>We have detected that RaidTracker is already installed, and the DB tables are being shared with a later version of RaidTracker. You cannot downgrade, though you may wish to reinstall
		this version using new tables. If this is the case, please rename or remove all files in the /user directory and hit refresh.<br />\n
		If the tables are compatible, then remove the /install directory and refresh this page. To check if the tables are compatible or 
		for more information, please check ".url('http://raidtracker.fridlunds.org',"the official RaidTracker page").".</p>";
		return;
	} else if ($rts_dbver >= $rts_latestver) { //minor version, only upgrade version value
		//update rt.php
		$rtv_formdata['sys']['server_offset']=rts_server_offset;
		$rtv_formdata['sys']['style']=rts_style;
		$rtv_formdata['sys']['guild']=rts_guild;
		$rtv_formdata['sys']['realm']=rts_realm;
		$rtv_formdata['sys']['levdefault']=rts_levdefault;
		$rtv_formdata['sys']['maint']=rts_maint;
		$rtv_formdata['sys']['domain']=rts_domain;
		$rtv_formdata['sys']['syspath']=rts_syspath;
		$rtv_formdata['sys']['sort_time']=rts_sort_time;
		$rtv_formdata['sys']['TOS']=rts_TOS;
		$rtv_formdata['sys']['tosv']=rts_tosv;
		$rtv_formdata['sys']['datespan']=rts_datespan;
		$rtv_formdata['sys']['days_back']=rts_days_back;
		$rtv_formdata['sys']['days_start']=rts_days_start;
//	$rtv_formdata['sys']['days_reset']=rts_days_reset;  //reset days
		$rtv_formdata['sys']['weeks']=rts_weeks;
		$rtv_formdata['sys']['cookie']=rts_cookie;
		$rtv_formdata['sys']['news']=rts_news;
		$rtv_formdata['sys']['hist_prev']=rts_hist_prev;
		$rtv_formdata['sys']['hist_future']=rts_hist_future;
		$rtv_formdata['sys']['view']=rts_view;
		$rtv_formdata['sys']['override']=rts_override;
		$rtv_formdata['sys']['navigate']=rts_navigate;
		$rtv_formdata['sys']['file_version']=$rts_sysver;
		$rtv_formdata['sys']['datef']=rts_datef;
		$rtv_formdata['sys']['timef']=rts_timef;
		if (!($rta_file=fopen(_RT_SYS_HOME. "user/rt.php","w"))) {  //open rt.php for writing
			$rts_internal['message'].="<h1>RaidTracker Install Error</h1>\n Settings cannot be saved.  File is protected.".br();
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
		rtd_update(rts_db_keys,array('value'=>$rts_sysver),"`name`='RT_system'");
		// all minor updates complete, send ok
		$rts_internal['main']="<h1>RaidTracker Install</h1>\n"
		."<p>RaidTracker upgrade is complete. Please remove the /install directory and refresh this page.<br />
		For more information, please check ".url('http://raidtracker.fridlunds.org',"the official RaidTracker page").".</p>";
		return;
	}
	// upgrade.
	$rtv_install="upgrade";
	$rta_oldRT=$rts_dbver;
	


	//>

	
//	echo "$rts_sysver -sysver<br />\n$rts_filever -setting version<br />\n$rts_dbver -DB version<br />\n";
} else {
//	echo "no file found";
$rtv_install="install";  //sets up for a new install
$rta_oldRT='0'; //  -- RT_system in keys to find RT version

}
//exit();


/*
if (file_exists(_RT_SYS_HOME.'user/rt.php')) {
	include _RT_SYS_INC ."rti-settings.php";
//	include RTS_HOME ."user/db.php";
	//$rta_dbconn=mysql_connect($rts_dbserver,$rts_dbuser,$rts_dbpass,$rts_db);
	//$rta_q="SELECT `value` FROM ".$rts_dbprefix."keys"; // WHERE category='RT_system'";
	//$rta_vercheck=rtd_query($rta_q);
	
	//$rta_vercheck=rtd_selcol("${rts_dbprefix}keys",'value',"`category`='RT_system'");
	//echo "$rta_vercheck - ". RTS_VERSION;
	//if ($rta_vercheck==RTS_VERSION || $rta_vercheck>RTS_VERSION) {
	//	echo "equal or greater";
	//	exit;
	$rts_internal['main']="<h1>RaidTracker Install Error</h1>\n<p>RaidTracker has found another version higher or equal to this version "
   . "has been installed, and the /install "
	. "directory still exists.  Please remove or rename the /install directory to start RaidTracker.</p>\n"
	. "<p>If you continue to have problems, you should remove all files in the /user directory (making sure to back them up first) and then "
	. "reinstall RaidTracker to new database tables.</p>";
	return;
	//} else if ($rta_vercheck!="1.91.1") {
		echo "less"; 
		exit;
	$rts_internal['main']="<h1>RaidTracker Install Error</h1>\n<p>RaidTracker has found another unknown version on your server and "
   . "cannot install.  This may be caused if you are trying to install version ". RTS_VERSION 
	. "over version 1.91 or an earlier/unknown version.  Please remove all files in the /user directory (making sure to back them "
	. "up first) and then try to reinstall RaidTracker again to new database tables.</p>\n"
	. "<p>RaidTracker can be upgraded using current database tables if version 1.91.1 is installed first.  See "
	. "<a href=\"http://raidtracker.fridlunds.org\">the RaidTracker</a> site for details.</p>";
	return;
	//}
	$rtv_install='upgrade';
}
*/

$rts_internal['main']="<h1>Installation</h1>\n<p>Welcome to RaidTracker.  You're about to ".$rtv_install . ($rtv_install=='upgrade'?" to":'')." version ".RTS_VERSION.".  ";
$rts_internal['main'].="The next few pages will guide you to get RaidTracker installed easily on your server, and make it ready for your guild "
. "to use.</p><p>Before continuing, please make sure your '/user' folder and any files in this directory are write accessible.  (If you are on "
. "an Apache server, use the command CHMOD 777.)  ";
if ($rtv_install!='install') $rts_internal['main'].=" Of course, it should be write accessible since this is an upgrade.  You should back up this folder and any database tables prior to the upgrade.";
else $rts_internal['main'].="Please also have your database and server information ready in the next few steps.";
$rts_internal['main'].="</p>\n<p>Ready to continue?</p>\n";
$rta_form=button("button","Next Step");
$rta_form.=input('step','hidden',$rtv_step+1);
$rta_form.=input('install','hidden',$rtv_install);
$rta_form.=input('oldRT','hidden',$rta_oldRT);
$rts_internal['main'].=form(div($rta_form),'.');
?>
