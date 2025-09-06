<?php
/*******************************************************************************
 * step3.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The third step of the install.
 * If upgrade, it will check if the account is an admin or with rights.
 * If a new copy, will create the database tables, and preload it with info.
 ******************************************************************************/

/******************************************* New install, create db.php */
if ($rtv_install=='install') { 
	// precheck settings to make sure DB settings are right.
	if ($rtv_dbpass!=$rtv_dbpassc) { // checking for proper password
		$rts_internal['message'].="Database passwords didn't match";
		$rtv_step--;
		include "install/step2.php";
		return;
	} else if (!$rtv_server || !$rtv_dbuser || !$rtv_dbname || !$rtv_dbprefix) { // cannot run with all blanks
		$rts_internal['message'].="Database fields missing data";
		$rtv_step--;
		include "install/step2.php";
		return;
	} else if (!$rtv_sdomain || !$rtv_spath) { //cannot run without no server data
		$rts_internal['message'].="Server fields missing data";
		$rtv_step--;
		include "install/step2.php";
		return;
	}
	$rts_internal['main']="<h1>Setting up database</h1>\n<p>Please wait while we set up the database...</p>\n";
	// put in critical db info to file db.php
	$rta_file=fopen(_RT_SYS_HOME ."user/db.php","w");
	fwrite ($rta_file, "<?php\n");
	fwrite ($rta_file, '$rts_dbserver="'.$rtv_server."\";\n");
	fwrite ($rta_file, '$rts_dbuser="'.$rtv_dbuser."\";\n");
	fwrite ($rta_file, '$rts_dbpass="'.$rtv_dbpass."\";\n");
	fwrite ($rta_file, '$rts_db="'.$rtv_dbname."\";\n");
	fwrite ($rta_file, '$rts_dbprefix="'.$rtv_dbprefix."\";\n");
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rts_internal['main'].="<p>Database settings successful</p>\n";
}

// include files to create DB table
include_once _RT_SYS_HOME ."user/db.php";
require_once _RT_SYS_INC ."rti-db.php";
if ($rtv_install=='upgrade')
	include_once _RT_SYS_INC ."rti-settings.php";
// doing it manually this one time. required for new install
$rta_dbconn=mysql_connect($rts_server,$rts_dbuser,$rts_dbpass);
if (!$rta_dbconn) {
	$rts_internal['main'].="<p>Could not open database server.  Please check your settings and then try again</p>";
	return;
}
if (!mysql_select_db($rts_db)) {
	$rts_internal['main'].="<p>Database not found or cannot be opened.  Please check your settings and try again</p>";
	return;
}
if ($rtv_install=='upgrade') { // Upgrade here
	$rta_rec=rtd_select (rts_db_acct,'*',"email='$rtv_email'");
	if (!$rta_rec) { // no record of user
		$rts_internal['main']="<h1>RaidTracker Error</h1>\n
		<p>No such user exists. Please check your settings, and try again</p>";
		return;
	} else if ($rta_rec['rtrank']!=1) {  // not RT admin
		$rts_internal['main']="<h1>RaidTracker Fatal Error</h1>\n
		<p>Sorry, that account is not a RaidTracker administrator. Install cannot continue.</p>";
		return;
	}
	$rts_internal['main']="<h1>Setting up database</h1>\n<p>Please wait while we check the database...</p>\n";
	// admin here. time to fix DB here
} //else {// new copy
// set up database here
//---- get game pack details
if (!$rtv_pack) {
	$rts_internal['message']='Not installing any new instances/classes.';
} else {
	$rta_gamepack=file(_RT_SYS_INSTALL."/$rtv_pack.rtp");
	if (!$rta_gamepack && $rtv_install=='install') $rts_internal['message']="Could not find game pack.  Not installing any instances/classes"; // needs to update at least for one class
}
//------ set up database or update it
rtlf_update($rtv_install, $rtv_oldRT);

/*
if ($rtv_install=="upgrade") {
	//bypass checks for upgrade
} else {
	if ($rtv_dbpass!=$rtv_dbpassc) {
		$rts_internal['message'].="Database passwords didn't match";
		include "install/step2.php";
		return;
	} else if (!$rtv_server || !$rtv_dbuser || !$rtv_dbname || !$rtv_dbprefix) {
		$rts_internal['message'].="Database fields missing data";
		include "install/step2.php";
		return;
	} else if (!$rtv_sdomain || !$rtv_spath) {
		$rts_internal['message'].="Server fields missing data";
		include "install/step2.php";
		return;
	}
	$rts_internal['main']="<h1>Setting up database</h1>\n<p>Please wait while we set up the database...</p>\n";
	if (!($rta_file=fopen(_RT_SYS_HOME ."user/db.php","w"))) {  //open db.php for writing
		$rts_internal['main'].="<p>File could not be created:  Please check that the /user folder is write accessible, and then hit Refresh to continue</p>";
		return;
	}
	// write database info in db.php
	fwrite ($rta_file, "<?php\n");
	fwrite ($rta_file, '$rts_dbserver="'.$rtv_server."\";\n");
	fwrite ($rta_file, '$rts_dbuser="'.$rtv_dbuser."\";\n");
	fwrite ($rta_file, '$rts_dbpass="'.$rtv_dbpass."\";\n");
	fwrite ($rta_file, '$rts_db="'.$rtv_dbname."\";\n");
	fwrite ($rta_file, '$rts_dbprefix="'.$rtv_dbprefix."\";\n");
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rts_internal['main'].="<p>Database settings successful</p>\n";
}
// now to include files to create DB table
include_once _RT_SYS_HOME ."user/db.php";
require_once _RT_SYS_INC ."rti-db.php";

$rta_dbconn=mysql_connect($rts_server,$rts_dbuser,$rts_dbpass);
if (!$rta_dbconn) {
	$rts_internal['main'].="<p>Could not open database server.  Please check your settings and then try again</p>";
	return;
}
if (!mysql_select_db($rts_db)) {
	$rts_internal['main'].="<p>Database not found or cannot be opened.  Please check your settings and try again</p>";
	return;
}
if ($rtv_install=='upgrade') {
	$rta_privs=rtd_selcol($rts_dbprefix."account",'rtrank',"email='$rtv_email'");
	if ($rta_privs!='1') {
		$rts_internal['message'].="Invalid account info or does not have privileges to upgrade.";
		include RTS_INSTALL ."/step2.php";
		return;
	}
}
//if ($rtv_install=='install') {
//------ set up tables
//raid_account
// ****** table structures
// field, added version, modified version, deleted version (0 for not)
$rta_ts['account']=array(
['id']        =>array('2.10','2.10','0',"smallint(1) unsigned NOT NULL auto_increment    COMMENT 'account id'"),
['email']     =>array('2.10','2.10','0',"varchar(40)          NOT NULL                   COMMENT 'email address'"),
['guildrank'] =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL                   COMMENT 'rank in guild'"),
['rtrank']    =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL                   COMMENT 'rank in rtracker'"),
['main']      =>array('2.10','2.10','0',"varchar(30)          NOT NULL                   COMMENT 'name of main char'"),
['datef']     =>array('2.10','2.10','0',"varchar(10)          NOT NULL default 'l Y-m-d' COMMENT 'date format'"),
['timef']     =>array('2.10','2.10','0',"varchar(10)          NOT NULL default 'H:i'     COMMENT 'time format'"),
['settings']  =>array('2.10','2.10','0',"varchar(20)          NOT NULL default 'c0'      COMMENT 'Settings'"),
['view']      =>array('2.10','2.10','0',"   char(1)           NOT NULL default 'c'       COMMENT 'Calendar/history view'"),
['tos']       =>array('2.10','2.10','0',"   char(1)           NOT NULL default '1'       COMMENT 'Version of TOS accepted'"),
['theme']     =>array('2.10','2.10','0',"varchar(20)              NULL                   COMMENT 'RaidTracker personal theme'"),
['tsoffset']  =>array('2.10','2.10','0',"float                NOT NULL default '0'       COMMENT 'Personal time offset from server'"),
['dayoffset'] =>array('2.10','2.10','0',"int(1)               NOT NULL default '0'       COMMENT 'Personal day offset in calendar'"),
[100]=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"UNIQUE KEY `email` (`email`)"),
array('2.10','2.10','0',"KEY `main` (`main`)")
);
$rta_ts['char']=array(
['id']      =>array('2.10','2.10','0',"smallint(5) unsigned NOT NULL auto_increment COMMENT 'character id'"),
['char']    =>array('2.10','2.10','0',"varchar(30)          NOT NULL COMMENT 'character name'"),
['account'] =>array('2.10','2.10','0',"smallint(6)          NOT NULL COMMENT 'link to account'"),
['level']   =>array('2.10','2.10','0',"smallint(6)              NULL COMMENT 'character level'"),
['class']   =>array('2.10','2.10','0',"varchar(5)           NOT NULL COMMENT 'character class'"),
['role']    =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL COMMENT 'primary role of character'"),
[100]=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"UNIQUE KEY `char` (`char`)")
);
$rta_ts['hist']=array(
['id']       =>array('2.10','2.10','0',"    int(10)  unsigned NOT NULL auto_increment COMMENT 'transaction id'"),
['category'] =>array('2.10','2.10','0',"varchar(20)           NOT NULL COMMENT 'category of change'"),
['ref']      =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL default '0' COMMENT 'reference to change'"),
['date']     =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time of this occurance'"),
['person']   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'Who did change (id)'"),
['reason']   =>array('2.10','2.10','0',"varchar(200)          NOT NULL COMMENT 'what reason for the change'"),
['perm']     =>array('2.10','2.10','0',"tinyint(3)   unsigned NOT NULL COMMENT 'Permissions to see this'"),
[100]=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `category` (`category`,`person`)")
);
$rta_ts['keys']=array(
['id']       =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL auto_increment"),
['category'] =>array('2.10','2.10','0',"varchar(20)           NOT NULL COMMENT 'category for set of keys'"),
['name']     =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'name'"),
['value']    =>array('2.10','2.10','0',"varchar(25)  default      NULL COMMENT 'value for item'"),
[100]=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)")
);
$rta_ts['list']=array(
['id']       =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL auto_increment COMMENT 'id of raid"),
['date']     =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date and time raid starts"),
['endtime']  =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'ending time"),
['freezenew']=>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'close time for new subscriptions"),
['freezedel']=>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'close time for withdrawing"),
['inv']      =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'invite time"),
['icon']     =>array('2.10','2.10','0',"varchar(5)            NOT NULL COMMENT 'raid instance icon"),
['note']     =>array('2.10','2.10','0',"varchar(35)           NOT NULL COMMENT 'instance"),
['required'] =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'any extra notes"),
['roles']    =>array('2.10','2.10','0',"varchar(15)  default      NULL COMMENT 'different roles separated by /"),
['offnote']  =>array('2.10','2.10','0',"varchar(225) default      NULL COMMENT 'officers note on raid"),
[100]=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `date` (`date`)")
);
$rta_ts['perm']=array(
['class']    =>array('2.10','2.10','0',"varchar(20) NOT NULL COMMENT 'RaidTracker class"),
['property'] =>array('2.10','2.10','0',"varchar(50) NOT NULL COMMENT 'property in RaidTracker"),
['value']    =>array('2.10','2.10','0',"varchar(20) NOT NULL COMMENT 'yes/no to property"),
['desc']     =>array('2.10','2.10','0',"varchar(100) NOT NULL COMMENT 'description of property")
[100] =>array('2.10','2.10','0',"UNIQUE KEY `property` (`property`)")
);
$rta_ts['sign']=array(
['id']       =>array('2.10','2.10','0',"int(10)      unsigned NOT NULL auto_increment COMMENT 'entry id"),
['charid']   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'id to account"),
['char']     =>array('2.10','2.10','0',"smallint(4)           NOT NULL COMMENT 'id to character name"),
['raidid']   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'id to raid"),
['role']     =>array('2.10','2.10','0',"varchar(30)           NOT NULL COMMENT 'role played in this raid"),
['status']   =>array('2.10','2.10','0',"tinyint(4)            NOT NULL COMMENT 'status with this raid"),
['note']     =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'any notes here"),
['offnote']  =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'officers note on signup"),
['signup']   =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time signed up"),
['modified'] =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time last modified"),
[100] =>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `charid` (`charid`,`raidid`)"),
array('2.10','2.10','0',"KEY `char` (`char`)"),

// ****** table data
// field, added version, modified version, deleted version (0 for not)
$rta_td['keys']=array(
array('2.10','0','0','category','name','value'),
array('2.10','0','0','guild','Guildmaster',''),
array('2.10','0','0','guild','Officer',''),
array('2.10','0','0','guild','Raider',''),
array('2.10','0','0','guild','Member',''),
array('2.10','0','0','guild','Trialist',''),
array('2.10','0','0','rt','Administrator',''),
array('2.10','0','0','rt','Raid planner',''),
array('2.10','0','0','rt','Officer',''),
array('2.10','0','0','rt','Member',''),
array('2.10','0','0','rt','Trial',''),
array('2.10','0','0','news_head',NULL,'headline for news'),
array('2.10','0','0','news',NULL,'news text'),
array('2.10','0','0','news_author',NULL,'Author of news'),
array('2.10','0','0','news_time',NULL,''),
array('2.10',RTS_VERSION,'0','RT_system','version',RTS_VERSION),
array('2.10','0','0','rt','Nobody',NULL),
array('2.10','0','0','raid_instance','none','Other'),
//next line to account for no game pack, no class
//array('2.10','0','0','class','none','Other'),
);
$rta_td['permission']=array(
array('2.10','0','0','property','class','value','desc'),
array('2.10','0','0','rtrank_alter','account','1','Promotes/Demotes players with RT permissions'),
array('2.10','0','0','grank_alter','account','1','Promotes/Demotes players with guild rank'),
array('2.10','0','0','player_delete','account','1','Deletes players'),
array('2.10','0','0','guildrank_start','account','5','New accounts: starting guild rank'),
array('2.10','0','0','rtrank_start','account','5','New accounts: starting guild rank'),
array('2.10','0','0','player_alter','account','-1','Who can modify a player\'s character and account info'),
array('2.10','0','0','character_add','account','-2','Add a character to an account'),
array('2.10','0','0','character_name','account','-1','Change a character\'s name'),
array('2.10','0','0','character_role','account','-3','Change default role for a character'),
array('2.10','0','0','character_level','account','-2','Allows level to be altered'),
array('2.10','0','0','character_main','account','1','Allows change of main character'),
array('2.10','0','0','character_delete','account','-2','Deletes characters'),
array('2.10','0','0','character_class','account','-2','Allows character class to be modified'),
array('2.10','2.50','0','rank_alter','admin','2','Who can alter the <em>names</em> of ranks'),
array('2.10','2.50','0','permission_alter','admin','1','Who can alter the permissions (this screen)'),
array('2.10','2.50','0','admin_view','admin','3','Who can view the administration menu'),
array('2.10','2.50','0','update_news','admin','3','Updating the newsbox'),
array('2.10','2.50','0','view_log','admin','3','Viewing the log'),
array('2.10','2.50','0','log_clear','admin','1','Allows clearing of the log'),
array('2.10','2.50','0','show_settings','admin','1','Allows system wide settings to be modified'),
array('2.10','2.10','0','raid_alter','manage','3','Altering the raid info (not the lists)'),
array('2.10','2.10','0','wb_publish','manage','2','Publishing the whiteboard list'),
array('2.10','2.10','0','wb_erase','manage','2','Clearing the whiteboard list'),
array('2.10','2.10','0','list_delete','manage','1','Deleting players from signups'),
array('2.10','2.10','0','list_raidlist','manage','2','Moving players to the raid list'),
array('2.10','2.10','0','list_wb','manage','3','Moving players to the whiteboard'),
array('2.10','2.10','0','list_available','manage','3','Moving players to Available status'),
array('2.10','2.10','0','list_reserve','manage','3','Moving players to Reserve status'),
array('2.10','2.10','0','list_withdraw','manage','3','Moving players to Withdraw status'),
array('2.10','2.10','0','list_remove','manage','1','Moving players to \'remove\' status'),
array('2.10','2.10','0','list_move','manage','3','Moving players permission'),
array('2.10','2.10','0','edit_publicnote','manage','2','Editing the note of players'),
array('2.10','2.10','0','edit_officernote','manage','3','Editing the officer\'s note'),
array('2.10','2.10','0','change_alt','manage','3','Changing player\'s alt'),
array('2.10','2.10','0','change_role','manage','3','Changing the role of the character'),
array('2.10','2.10','0','subscription_alter','manage','3','Allow altercations with players\' subscriptions'),
array('2.10','2.10','0','sign_raidlist','signup','0','Subscribe directly to the raid list'),
array('2.10','2.10','0','signup_override','signup','3','Can override the freeze cutoff'),
array('2.10','2.50','0','list_addchar','manage','3','Allows a character to be added to list'),
array('2.10','2.10','0','sign_alt','signup','3','Allows signup with alts'),
array('2.10','2.10','0','show_grank','view','5','shows guild rank on the guild list'),
array('2.10','2.10','0','show_rtrank','view','3','Shows RT rank on the guild list'),
array('2.10','2.10','0','show_stats','view','-3','Shows stats on the player page (and the guild list if not overriden by self)'),
array('2.10','2.10','0','show_email','view','1','Shows e-mail addresses on the guild list'),
array('2.10','2.50','0','raid_create','raid','3','Who can create raids'),
array('2.10','2.50','0','view_officernote','raid','3','Viewing the officer note in raid info and for players (in signup lists)'),
array('2.10','2.50','0','show_raidlist','raid','11','Reveals the Raid list'),
array('2.10','2.50','0','show_available','raid','11','Reveals the available list'),
array('2.10','2.50','0','show_reserve','raid','11','Reveals the reserve list'),
array('2.10','2.50','0','show_withdraw','raid','11','Reveals the withdraw list'),
array('2.10','2.50','0','show_remove','raid','3','Reveals the removed list'),
array('2.10','2.50','0','show_whiteboard','raid','3','Reveals the whiteboard'),
array('2.10','2.50','0','show_tables','raid','11','Reveals the signup tables'),
array('2.10','2.10','0','view_playernote','view','5','Displays the (public) player note'),
array('2.10','2.10','0','rofp_main','custom','0','View and edit custom control panel'),
array('2.50','2.50','0','instance','admin','1','Allows access to alter instance list'),
array('2.50','2.50','0','class_list','admin','1','Allows access to alter class list'),
array('2.50','2.50','0','role_list','admin','1','Allows access to alter role list')
);


//******************************************************* OLD DATA

$rta_table['acct']="CREATE TABLE IF NOT EXISTS `".$rtv_dbprefix."account` (
  `id` smallint(1) unsigned NOT NULL auto_increment COMMENT 'account id',
  `email` varchar(40) NOT NULL COMMENT 'email address',
  `guildrank` tinyint(3) unsigned NOT NULL COMMENT 'rank in guild',
  `rtrank` tinyint(3) unsigned NOT NULL COMMENT 'rank in rtracker',
  `main` varchar(30) NOT NULL,
  `datef` varchar(10) NOT NULL default 'l Y-m-d' COMMENT 'date format',
  `timef` varchar(10) NOT NULL default 'H:i' COMMENT 'time format',
  `settings` varchar(20) NOT NULL default 'c0' COMMENT 'Settings',
  `view` char(1) NOT NULL default 'c' COMMENT 'Calendar/history view',
  `tos` char(1) NOT NULL default '1' COMMENT 'Version of TOS accepted',
  `theme` varchar(20) NULL COMMENT 'RaidTracker personal theme',
  `tsoffset` float NOT NULL default '0' COMMENT 'Personal time offset from server',
  `dayoffset` int(1) NOT NULL default '0' COMMENT 'Personal day offset in calendar',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `main` (`main`)
)";
$rta_table['char']="CREATE TABLE `".$rtv_dbprefix."char` (
  `id`      smallint(5) unsigned NOT NULL auto_increment COMMENT 'character id',
  `char`    varchar(30)          NOT NULL COMMENT 'character name',
  `account` smallint(6)          NOT NULL COMMENT 'link to account',
  `level`   smallint(6)              NULL COMMENT 'character level',
  `class`   varchar(5)           NOT NULL COMMENT 'character class',
  `role`    tinyint(3)  unsigned NOT NULL COMMENT 'primary role of character',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `char` (`char`)
)";
$rta_table['hist']="CREATE TABLE `".$rtv_dbprefix."hist` (
  `id`           int(10)  unsigned NOT NULL auto_increment COMMENT 'transaction id',
  `category` varchar(20)           NOT NULL COMMENT 'category of change',
  `ref`      smallint(5)  unsigned NOT NULL default '0' COMMENT 'reference to change',
  `date`     datetime              NOT NULL COMMENT 'date/time of this occurance',
  `person`   smallint(5)  unsigned NOT NULL COMMENT 'Who did change (id)',
  `reason`   varchar(200)          NOT NULL COMMENT 'what reason for the change',
  `perm`     tinyint(3)   unsigned NOT NULL COMMENT 'Permissions to see this',
  PRIMARY KEY  (`id`),
  KEY `category` (`category`,`person`)
)";
$rta_table['keys']="CREATE TABLE IF NOT EXISTS `".$rtv_dbprefix."keys` (
  `id`       smallint(5)  unsigned NOT NULL auto_increment,
  `category` varchar(20)           NOT NULL COMMENT 'category for set of keys',
  `name`     varchar(200) default      NULL COMMENT 'name',
  `value`    varchar(25)  default      NULL COMMENT 'value for item',
  PRIMARY KEY  (`id`)
)";
$rta_table['list']="CREATE TABLE `".$rtv_dbprefix."list` (
  `id`        smallint(5)  unsigned NOT NULL auto_increment COMMENT 'id of raid',
  `date`      datetime              NOT NULL COMMENT 'date and time raid starts',
  `endtime`   datetime              NOT NULL COMMENT 'ending time',
  `freezenew` datetime              NOT NULL COMMENT 'close time for new subscriptions',
  `freezedel` datetime              NOT NULL COMMENT 'close time for withdrawing',
  `inv`       datetime              NOT NULL COMMENT 'invite time',
  `icon`      varchar(5)            NOT NULL COMMENT 'raid instance icon',
  `instance`  varchar(35)           NOT NULL COMMENT 'instance',
  `note`      varchar(200) default      NULL COMMENT 'any extra notes',
  `required`  tinyint(3)   unsigned NOT NULL COMMENT 'how many required',
  `roles`     varchar(15)  default      NULL COMMENT 'different roles separated by /',
  `offnote`   varchar(225) default      NULL COMMENT 'officers note on raid',
  PRIMARY KEY  (`id`),
  KEY `date` (`date`)
)";
$rta_table['perm']="CREATE TABLE IF NOT EXISTS `".$rtv_dbprefix."permission` (
  `class`    varchar(20) NOT NULL COMMENT 'RaidTracker class',
  `property` varchar(50) NOT NULL COMMENT 'property in RaidTracker',
  `value`    varchar(20) NOT NULL COMMENT 'yes/no to property',
  `desc`     varchar(100) NOT NULL COMMENT 'description of property',
  UNIQUE KEY `property` (`property`)
)";
$rta_table['sign']="CREATE TABLE `".$rtv_dbprefix."sign` (
  `id`       int(10)      unsigned NOT NULL auto_increment COMMENT 'entry id',
  `charid`   smallint(5)  unsigned NOT NULL COMMENT 'id to account',
  `char`     smallint(4)           NOT NULL COMMENT 'id to character name',
  `raidid`   smallint(5)  unsigned NOT NULL COMMENT 'id to raid',
  `role`     varchar(30)           NOT NULL COMMENT 'role played in this raid',
  `status`   tinyint(4)            NOT NULL COMMENT 'status with this raid',
  `note`     varchar(200) default      NULL COMMENT 'any notes here',
  `offnote`  varchar(200) default      NULL COMMENT 'officers note on signup',
  `signup`   datetime              NOT NULL COMMENT 'date/time signed up',
  `modified` datetime              NOT NULL COMMENT 'date/time last modified',
  PRIMARY KEY  (`id`),
  KEY `charid` (`charid`,`raidid`),
  KEY `char` (`char`)
)";

$rta_table_ins['keys']="INSERT INTO `".$rtv_dbprefix."keys` (`category`, `name`, `value`) VALUES
('guild', 'Guildmaster',''),
('guild', 'Officer',''),
('guild', 'Raider',''),
('guild', 'Member',''),
('guild', 'Trialist',''),
('rt', 'Administrator',''),
('rt', 'Raid planner',''),
('rt', 'Officer',''),
('rt', 'Member',''),
('rt', 'Trial',''),
('news_head', NULL, 'headline for news'),
('news', NULL, 'news text'),
('news_author', 1, 'Author of news'),
('news_time', 0, 'Unix time of news'),
('RT_system', 'version', '". RTS_VERSION ."')," // note for version here
."('rt', 'Nobody', null),"
//--- class list needs to be somewhere
//--- instance list
."('raid_instance', 'none','Other')";


$rta_table_ins['perm']="INSERT INTO `".$rtv_dbprefix."permission` (`class`, `property`, `value`, `desc`) VALUES"
."('account', 'grank_alter', '1', 'Promotes/Demotes players with guild rank'),"
."('account', 'rtrank_alter', '1', 'Promote/Demotes player''s with RT permissions'),"
."('account', 'player_delete', '1', 'Deletes players'),"
."('account', 'guildrank_start', '5', 'New accounts: starting guild rank'),"
."('account', 'rtrank_start', '5', 'New accounts: starting RT rank'),"
."('account', 'player_alter', '-1', 'Who can modify a player''s character and account info'),"
."('account', 'character_add', '-2', 'Add a character to an account'),"
."('account', 'character_name', '-1', 'Change a character''s name'),"
."('account', 'character_role', '-3', 'Change default role for a character'),"
."('account', 'character_level', '-2', 'Allows level to be altered'),"
."('account', 'character_main', '1', 'Allows change of main character'),"
."('account', 'character_delete', '-2', 'Deletes characters'),"
."('account', 'character_class', '-2', 'Allows character class to be modified'),"
."('admin', 'rank_alter', '2', 'Who can alter the <em>names</em> of ranks')," //general > raid - 2.5
."('admin', 'permission_alter', '1', 'Who can alter the permissions (this screen)')," //general > raid - 2.5
."('admin', 'admin_view', '3', 'Who can view the administration menu')," //general > raid - 2.5
."('admin', 'update_news', '3', 'Updating the newsbox')," //general > raid - 2.5
."('admin', 'view_log', '3', 'Viewing the log')," //general > raid - 2.5
."('admin', 'log_clear', '1', 'Allows clearing of the log')," //general > raid - 2.5
."('admin', 'show_settings', '1', 'Allows system wide settings to be modified')," //general > raid - 2.5
."('manage', 'raid_alter', '3', 'Altering the raid info (not the lists)'),"
."('manage', 'wb_publish', '2', 'Publishing the whiteboard list'),"
."('manage', 'wb_erase', '2', 'Clearing the whiteboard list'),"
."('manage', 'list_delete', '1', 'Deleting players from signups'),"
."('manage', 'list_raidlist', '2', 'Moving players to the raid list'),"
."('manage', 'list_wb', '3', 'Moving players to the whiteboard'),"
."('manage', 'list_available', '3', 'Moving players to ''Available'' status'),"
."('manage', 'list_reserve', '3', 'Moving players to ''Reserve'' status'),"
."('manage', 'list_withdraw', '3', 'Moving players to withdraw status'),"
."('manage', 'list_remove', '1', 'Moving players to ''remove'' status'),"
."('manage', 'list_move', '3', 'Moving players permission'),"
."('manage', 'edit_publicnote', '2', 'Editing the note of players'),"
."('manage', 'edit_officernote', '3', 'Editing the officer''s note'),"
."('manage', 'change_alt', '3', 'Changing player''s alt'),"
."('manage', 'change_role', '3', 'Changing the role of the character'),"
."('manage', 'subscription_alter', '3', 'Allow altercations with players'' subscriptions'),"
."('signup', 'sign_raidlist', '0', 'Subscribe directly to the raid list'),"
."('signup', 'signup_override', '3', 'Can override the freeze cutoff'),"
."('manage', 'list_addchar', '3', 'Allows a character to be added to list')," //signup > manage - 2.5
."('signup', 'sign_alt', '3', 'Allows signup with alts'),"
."('view', 'show_grank', '5', 'shows guild rank on the guild list'),"
."('view', 'show_rtrank', '3', 'Shows RT rank on the guild list'),"
."('view', 'show_stats', '-3', 'Shows stats on the player page (and the guild list if not overriden by self)'),"
."('view', 'show_email', '1', 'Shows e-mail addresses on the guild list'),"
."('raid', 'raid_create', '3', 'Who can create raids')," //general > raid - 2.5
."('raid', 'view_officernote', '3', 'Viewing the officer note in raid info and for players (in signup lists)')," //view > raid - 2.5
."('raid', 'show_raidlist', '11', 'Reveals the Raid list')," //view > raid - 2.5
."('raid', 'show_available', '11', 'Reveals the available list')," //view > raid - 2.5
."('raid', 'show_reserve', '11', 'Reveals the reserve list')," //view > raid - 2.5
."('raid', 'show_withdraw', '11', 'Reveals the withdraw list')," //view > raid - 2.5
."('raid', 'show_remove', '3', 'Reveals the removed list')," //view > raid - 2.5
."('raid', 'show_whiteboard', '3', 'Reveals the whiteboard')," //view > raid - 2.5
."('raid', 'show_tables', '11', 'Reveals the signup tables')," //view > raid - 2.5
."('view', 'view_playernote', '5', 'Displays the (public) player note')," 
."('custom', 'rofp_main', '0', 'View and edit custom control panel')"
//--- new in 2.50
."('admin', 'instance', '1', 'Allows access to alter instance list'),"
."('admin', 'class_list', '1', 'Allows access to alter class list'),"
."('admin', 'role_list', '1', 'Allows access to alter role list'),"

;

//include file for instances.

// ******************** have not updated below for 2.50

} else {  //upgrade only:
 // ---- update table structure
$rta_table[]="ALTER TABLE `". $rts_dbprefix."account` ADD (
`view` char(1) NOT NULL DEFAULT 'c' COMMENT 'Calendar or history view',
`tos` char(1) NOT NULL DEFAULT '1' COMMENT 'version of TOS accepted',
`theme` varchar(20) NULL COMMENT 'Personal RT theme',
`tsoffset` float NOT NULL COMMENT 'individual timezone offset from server',
`dayoffset` tinyint(4) NOT NULL COMMENT 'individual day offset in calendar mode')";
$rta_table[]="ALTER TABLE `".$rts_dbprefix."keys` MODIFY `value` (varchar(25) default NULL COMMENT 'value for name')";
$rta_table[]="ALTER TABLE `".$rts_dbprefix."sign` MODIFY `char` (smallint(4) NOT NULL COMMENT 'id to character name')";
 // ---- update table info
 // -- fix key table
$rta_table_ins[]="DELETE FROM `".$rts_dbprefix."keys` WHERE `category`='raid_instance'"; //remove instances to repop with wow.txt
$rta_table_ins[]="DELETE FROM `".$rts_dbprefix."keys` WHERE `category`='class'"; //remove classes
$rta_table_ins[]="INSERT INTO `".$rts_dbprefix."keys` (`category`,`name`,`value`) 'raid_instance','none','Other'"; //Adds in none
$rta_table_ins[]="UPDATE `".$rts_dbprefix."keys` SET `value`='2.1' WHERE `category`='RT_system'"; // new ver of RT
 // -- fix categories in permissions
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='view' WHERE `property` LIKE 'view_%' OR `property` LIKE 'show_%'";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='manage' WHERE `property` LIKE 'list_%' or `property` LIKE 'wb_%'";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='account' WHERE `property` LIKE 'player_%' OR `property` LIKE 'character_%'";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='general' WHERE `property` IN 
('raid_create','rank_alter','permissions_alter','admin_view','update_news','view_log','log_clear','show_settings')";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='manage' WHERE `property` IN 
('edit_publicnote','edit_officernote','change_alt','change_role','raid_alter','subscription_alter')";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='signup' WHERE `property` IN 
('sign_raidlist','signup_override')";
$rta_table_ins[]="UPDATE `".$rts_dbprefix."permission` SET `class`='account' WHERE `property` IN 
('grank_alter','rtrank_alter', 'player_delete','guildrank_start','rtrank_start')";
 // -- add 2.1 specific permissions
$rta_table_ins[]="INSERT INTO `".$rts_dbprefix."permission` (`class`,`property`,`value`,`desc`) "
."('custom','rofp_main','0','To edit/view custom control panel')," //Adds in custom panel option
."('account','character_class','-2','Allows character class to be modified')," 
."('general','show_setttings','1','Allow system-wide settings to be modified')"
//--version 2.5
."('admin', 'instance', '1', 'Allows access to alter instance list'),"
."('admin', 'class_list', '1', 'Allows access to alter class list'),"
."('admin', 'role_list', '1', 'Allows access to alter role list'),"

; 
*/
//}
//---- apply game pack
if ($rtv_install=='install' || $rtv_gamepack) {
	if ($rta_gamepack) {
		while ($rta_gamepack) {
			$rta_packinfo=trim(addslashes(array_shift($rta_gamepack)));
			$rta_instancelist.=($rta_instancelist?",":'')."\n(\"".implode('","',explode(",",$rta_packinfo))."\")";
		}
		//$rta_instancelist[0]="\0";
		$rta_instancelist=trim($rta_instancelist);
	} else {
		$rta_instancelist="('class','Hu','Human')";
	}
	$rta_tresult=rtd_query("INSERT INTO ".$rts_dbprefix."keys (`category`,`name`,`value`) VALUES \n".$rta_instancelist);
	//---- put values in tables
/*
	foreach ($rta_table as $v) 
		$a=rtd_query($v);
	foreach ($rta_table_ins as $v) {
		$a=rtd_query($v);
		if (!$a) die ("Failed on $v");
	}
*/
}
//exit();
//------------------------------------------------------------------------------end of the page
$rta_form=input('server[domain]','hidden',$rtv_sdomain);
$rta_form.=input('server[syspath]','hidden',$rtv_spath);
$rts_internal['main'].="<p>Database tables created</p>\n";
$rta_form.=button('button','Continue');
$rta_form.=input('install','hidden',$rtv_install);
$rta_form.=input('oldRT','hidden',$rtv_oldRT);
$rta_form.=input('step','hidden',$rtv_step+1);
$rts_internal['main'].=form(div($rta_form),'.','post');


function rtlf_update($rta_update, $rta_oldver) {
/*******************************************************************************
 * update function
 * record of the database, and create db tables
 ******************************************************************************/
// data

$rta_ts['account']=array(
'id'        =>array('2.10','2.10','0',"smallint(1) unsigned NOT NULL auto_increment    COMMENT 'account id'"),
'email'     =>array('2.10','2.10','0',"varchar(40)          NOT NULL                   COMMENT 'email address'"),
'guildrank' =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL                   COMMENT 'rank in guild'"),
'rtrank'    =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL                   COMMENT 'rank in rtracker'"),
'main'      =>array('2.10','2.10','0',"varchar(30)          NOT NULL                   COMMENT 'name of main char'"),
'datef'     =>array('2.10','2.10','0',"varchar(10)          NOT NULL default 'l Y-m-d' COMMENT 'date format'"),
'timef'     =>array('2.10','2.10','0',"varchar(10)          NOT NULL default 'H:i'     COMMENT 'time format'"),
'settings'  =>array('2.10','2.10','0',"varchar(20)          NOT NULL default 'c0'      COMMENT 'Settings'"),
'view'      =>array('2.10','2.10','0',"   char(1)           NOT NULL default 'c'       COMMENT 'Calendar/history view'"),
'tos'       =>array('2.10','2.10','0',"   char(1)           NOT NULL default '1'       COMMENT 'Version of TOS accepted'"),
'theme'     =>array('2.10','2.10','0',"varchar(20)              NULL                   COMMENT 'RaidTracker personal theme'"),
'tsoffset'  =>array('2.10','2.10','0',"float                NOT NULL default '0'       COMMENT 'Personal time offset from server'"),
'dayoffset' =>array('2.10','2.10','0',"int(1)               NOT NULL default '0'       COMMENT 'Personal day offset in calendar'"),
100=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"UNIQUE KEY `email` (`email`)"),
array('2.10','2.10','0',"KEY `main` (`main`)")
);
$rta_ts['char']=array(
'id'      =>array('2.10','2.10','0',"smallint(5) unsigned NOT NULL auto_increment COMMENT 'character id'"),
'char'    =>array('2.10','2.10','0',"varchar(30)          NOT NULL COMMENT 'character name'"),
'account' =>array('2.10','2.10','0',"smallint(6)          NOT NULL COMMENT 'link to account'"),
'level'   =>array('2.10','2.10','0',"smallint(6)              NULL COMMENT 'character level'"),
'class'   =>array('2.10','2.10','0',"varchar(5)           NOT NULL COMMENT 'character class'"),
'role'    =>array('2.10','2.10','0',"tinyint(3)  unsigned NOT NULL COMMENT 'primary role of character'"),
100=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"UNIQUE KEY `char` (`char`)")
);
$rta_ts['hist']=array(
'id'       =>array('2.10','2.10','0',"    int(10)  unsigned NOT NULL auto_increment COMMENT 'transaction id'"),
'category' =>array('2.10','2.10','0',"varchar(20)           NOT NULL COMMENT 'category of change'"),
'ref'      =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL default '0' COMMENT 'reference to change'"),
'date'     =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time of this occurance'"),
'person'   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'Who did change (id)'"),
'reason'   =>array('2.10','2.10','0',"varchar(200)          NOT NULL COMMENT 'what reason for the change'"),
'perm'     =>array('2.10','2.10','0',"tinyint(3)   unsigned NOT NULL COMMENT 'Permissions to see this'"),
100=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `category` (`category`,`person`)")
);
$rta_ts['keys']=array(
'id'       =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL auto_increment"),
'category' =>array('2.10','2.10','0',"varchar(20)           NOT NULL COMMENT 'category for set of keys'"),
'name'     =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'name'"),
'value'    =>array('2.10','2.10','0',"varchar(25)  default      NULL COMMENT 'value for item'"),
100=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)")
);
$rta_ts['list']=array(
'id'       =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL auto_increment COMMENT 'id of raid'"),
'date'     =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date and time raid starts'"),
'endtime'  =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'ending time'"),
'freezenew'=>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'close time for new subscriptions'"),
'freezedel'=>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'close time for withdrawing'"),
'inv'      =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'invite time'"),
'icon'     =>array('2.10','2.10','0',"varchar(5)            NOT NULL COMMENT 'raid instance icon'"),
'instance' =>array('2.10','2.10','0',"varchar(35)           NOT NULL COMMENT 'instance'"),
'note'     =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'any extra notes'"),
'required' =>array('2.10','2.10','0',"tinyint(3)   unsigned NOT NULL COMMENT 'how many required'"),
'roles'    =>array('2.10','2.10','0',"varchar(15)  default      NULL COMMENT 'different roles separated by /'"),
'offnote'  =>array('2.10','2.10','0',"varchar(225) default      NULL COMMENT 'officers note on raid'"),
100=>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `date` (`date`)")
);
$rta_ts['permission']=array(
'class'    =>array('2.10','2.10','0',"varchar(20) NOT NULL COMMENT 'RaidTracker class'"),
'property' =>array('2.10','2.10','0',"varchar(50) NOT NULL COMMENT 'property in RaidTracker'"),
'value'    =>array('2.10','2.10','0',"varchar(20) NOT NULL COMMENT 'yes/no to property'"),
'desc'     =>array('2.10','2.10','0',"varchar(100) NOT NULL COMMENT 'description of property'"),
100 =>array('2.10','2.10','0',"UNIQUE KEY `property` (`property`)")
);
$rta_ts['sign']=array(
'id'       =>array('2.10','2.10','0',"int(10)      unsigned NOT NULL auto_increment COMMENT 'entry id'"),
'charid'   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'id to account'"),
'char'     =>array('2.10','2.10','0',"smallint(4)           NOT NULL COMMENT 'id to character name'"),
'raidid'   =>array('2.10','2.10','0',"smallint(5)  unsigned NOT NULL COMMENT 'id to raid'"),
'role'     =>array('2.10','2.10','0',"varchar(30)           NOT NULL COMMENT 'role played in this raid'"),
'status'   =>array('2.10','2.10','0',"tinyint(4)            NOT NULL COMMENT 'status with this raid'"),
'note'     =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'any notes here'"),
'offnote'  =>array('2.10','2.10','0',"varchar(200) default      NULL COMMENT 'officers note on signup'"),
'signup'   =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time signed up'"),
'modified' =>array('2.10','2.10','0',"datetime              NOT NULL COMMENT 'date/time last modified'"),
100 =>array('2.10','2.10','0',"PRIMARY KEY  (`id`)"),
array('2.10','2.10','0',"KEY `charid` (`charid`,`raidid`)"),
array('2.10','2.10','0',"KEY `char` (`char`)")
);

// ****** table data
// field, added version, modified version, deleted version (0 for not)
$rta_td['keys']=array(
array('2.10','2.10','0','category','name','value'),
array('2.10','2.10','0','guild','Guildmaster',''),
array('2.10','2.10','0','guild','Officer',''),
array('2.10','2.10','0','guild','Raider',''),
array('2.10','2.10','0','guild','Member',''),
array('2.10','2.10','0','guild','Trialist',''),
array('2.10','2.10','0','rt','Administrator',''),
array('2.10','2.10','0','rt','Raid planner',''),
array('2.10','2.10','0','rt','Officer',''),
array('2.10','2.10','0','rt','Member',''),
array('2.10','2.10','0','rt','Trial',''),
array('2.10','2.10','0','news_head',NULL,'headline for news'),
array('2.10','2.10','0','news',NULL,'news text'),
array('2.10','2.10','0','news_author','1','Author of news'),
array('2.10','2.10','0','news_time','2',''),
array('2.10',RTS_VERSION,'0','RT_system','version',RTS_VERSION),
array('2.10','2.10','0','rt','Nobody',NULL),
array('2.10','2.10','0','raid_instance','none','Other')
);
$rta_td['permission']=array(
array('2.10','2.10','0','property','class','value','desc'),
array('2.10','2.10','0','rtrank_alter','account','1','Promotes/Demotes players with RT permissions'),
array('2.10','2.10','0','grank_alter','account','1','Promotes/Demotes players with guild rank'),
array('2.10','2.10','0','player_delete','account','1','Deletes players'),
array('2.10','2.10','0','guildrank_start','account','5','New accounts: starting guild rank'),
array('2.10','2.10','0','rtrank_start','account','5','New accounts: starting guild rank'),
array('2.10','2.10','0','player_alter','account','-1','Who can modify an account character and account info'),
array('2.10','2.10','0','character_add','account','-2','Add a character to an account'),
array('2.10','2.10','0','character_name','account','-1','Edit character name'),
array('2.10','2.10','0','character_role','account','-3','Change default role for a character'),
array('2.10','2.10','0','character_level','account','-2','Allows level to be altered'),
array('2.10','2.10','0','character_main','account','1','Allows change of main character'),
array('2.10','2.10','0','character_delete','account','-2','Deletes characters'),
array('2.10','2.10','0','character_class','account','-2','Allows character class to be modified'),
array('2.10','2.50','0','rank_alter','admin','2','Who can alter the <em>names</em> of ranks'),
array('2.10','2.50','0','permission_alter','admin','1','Who can alter the permissions (this screen)'),
array('2.10','2.50','0','admin_view','admin','3','Who can view the administration menu'),
array('2.10','2.50','0','update_news','admin','3','Updating the newsbox'),
array('2.10','2.50','0','view_log','admin','3','Viewing the log'),
array('2.10','2.50','0','log_clear','admin','1','Allows clearing of the log'),
array('2.10','2.50','0','show_settings','admin','1','Allows system wide settings to be modified'),
array('2.10','2.10','0','raid_alter','manage','3','Altering the raid info (not the lists)'),
array('2.10','2.10','0','wb_publish','manage','2','Publishing the whiteboard list'),
array('2.10','2.10','0','wb_erase','manage','2','Clearing the whiteboard list'),
array('2.10','2.10','0','list_delete','manage','1','Deleting players from signups'),
array('2.10','2.10','0','list_raidlist','manage','2','Moving players to the raid list'),
array('2.10','2.10','0','list_wb','manage','3','Moving players to the whiteboard'),
array('2.10','2.10','0','list_available','manage','3','Moving players to Available status'),
array('2.10','2.10','0','list_reserve','manage','3','Moving players to Reserve status'),
array('2.10','2.10','0','list_withdraw','manage','3','Moving players to Withdraw status'),
array('2.10','2.10','0','list_remove','manage','1','Moving players to "remove" status'),
array('2.10','2.10','0','list_move','manage','3','Moving players permission'),
array('2.10','2.10','0','edit_publicnote','manage','2','Editing the note of players'),
array('2.10','2.10','0','edit_officernote','manage','3','Editing the private note'),
array('2.10','2.10','0','change_alt','manage','3','Changing player characters in lists'),
array('2.10','2.10','0','change_role','manage','3','Changing the role of the character'),
array('2.10','2.10','0','subscription_alter','manage','3','Allow altercations with players subscriptions'),
array('2.10','2.10','0','sign_raidlist','signup','0','Subscribe directly to the raid list'),
array('2.10','2.10','0','signup_override','signup','3','Can override the freeze cutoff'),
array('2.10','2.50','0','list_addchar','manage','3','Allows a character to be added to list'),
array('2.10','2.10','0','sign_alt','signup','3','Allows signup with alts'),
array('2.10','2.10','0','show_grank','view','5','shows guild rank on the guild list'),
array('2.10','2.10','0','show_rtrank','view','3','Shows RT rank on the guild list'),
array('2.10','2.10','0','show_stats','view','-3','Shows stats on the player page (and the guild list if not overriden by self)'),
array('2.10','2.10','0','show_email','view','1','Shows e-mail addresses on the guild list'),
array('2.10','2.50','0','raid_create','raid','3','Who can create raids'),
array('2.10','2.50','0','view_officernote','raid','3','Viewing the officer note in raid info and for players (in signup lists)'),
array('2.10','2.50','0','show_raidlist','raid','11','Reveals the Raid list'),
array('2.10','2.50','0','show_available','raid','11','Reveals the available list'),
array('2.10','2.50','0','show_reserve','raid','11','Reveals the reserve list'),
array('2.10','2.50','0','show_withdraw','raid','11','Reveals the withdraw list'),
array('2.10','2.50','0','show_remove','raid','3','Reveals the removed list'),
array('2.10','2.50','0','show_whiteboard','raid','3','Reveals the whiteboard'),
array('2.10','2.50','0','show_tables','raid','11','Reveals the signup tables'),
array('2.10','2.10','0','view_playernote','view','5','Displays the (public) player note'),
array('2.10','2.10','0','rofp_main','custom','0','View and edit custom control panel'),
array('2.50','2.50','0','instance','admin','1','Allows access to alter instance list'),
array('2.50','2.50','0','class_list','admin','1','Allows access to alter class list'),
array('2.50','2.50','0','role_list','admin','1','Allows access to alter role list')
);

// ****** table structures
// field, added version, modified version, deleted version (0 for not)
global $rts_dbprefix;
$rtv_dbprefix=$rts_dbprefix;
//var_dump ($rta_ts);
foreach ($rta_ts as $rta_tbl=>$rta_data) {
	$rta_tbl=$rtv_dbprefix.$rta_tbl;
	//$rta_tbl is table name
	foreach ($rta_data as $rta_field=>$rta_info) {
		//$rta_field is fieldname
		if ($rta_update=="install") { //new install here
			if ($rta_info[2]=='0') { //field is not deleted
				$rts_add[]=($rta_field<99?"`$rta_field` ":'').$rta_info[3];
				//add field using $rta_field, $rta_info[4]
			}
		} else if ($rta_upgrade=="upgrade") { //upgrade here
			if ($rta_info[0]>$rta_oldver)       //check for additions
				$rts_add[]="`$rta_field`  (".$rta_info[3].")";
			else if ($rta_info[1]>$rta_oldver)  //check for modifications
				$rts_modify[]="`$rta_field` ".$rta_info[3];
			else if ($rta_info[2]>=$rta_oldver) //check for deletions
				$rts_delete[]="`$rta_field`";
		}
		if ($rts_add) {
			if ($rta_upgrade=="upgrade") {
				$rts_aq=" ADD ".implode(",\n ADD ",$rts_add);
			} else {
				$rts_aq=implode (",\n ",$rts_add);
			}
		}
		if ($rts_modify) {
			if ($rts_aq) $rts_aq.=", ";
			$rts_aq.= "MODIFY ".implode(", MODIFY ",$rts_modify);
		}
		if ($rts_delete) {
			if ($rts_aq) $rts_aq.=", ";
			$rts_aq.= "DROP ".implode(", DROP ",$rts_modify);
		}
	}
	if ($rts_aq) {
		if ($rta_update=='install') $rts_aq="CREATE TABLE $rta_tbl ($rts_aq)";
		else if ($rta_update=='upgrade') "ALTER TABLE $rta_tbl $rts_aq";
		$rta_result=rtd_query ($rts_aq);
		if (!$rta_result) {
			echo $rts_aq."<br />\n";
			echo "Cannot update database"; exit();
		} else {
			// passed
		}
		unset ($rts_aq,$rts_add,$rts_modify, $rts_delete);
	}
}

// ******************* table data
foreach ($rta_td as $rta_tbl=>$rta_data) {
	$rta_tbl=$rtv_dbprefix.$rta_tbl;
	//$rta_tbl is table name
	foreach ($rta_data as $rta_rec=>$rta_info) {
		//$rta_rec is record. 0=definitions
		if ($rta_rec==0) {
			for ($i=3; $i<count($rta_info); $i++) 
				$rta_fieldlist[]=$rta_info[$i];  //get fields list
			//dunno what to put here for col def
		} else { //data
			for ($i=3;$i<count($rta_info);$i++)  // get data
				$rta_nd[]=$rta_info[$i];
			if ($rta_info[0]>$rta_oldver) { // add new record
				//echo "$rta_info[0]  =  $rta_oldver"; exit();
				$rta_addrec.=(($rta_addrec)?",":"")."('".implode('\', \'',$rta_nd)."')\n";
				//skip insert to the end
			} else if ($rta_info[1]>$rta_oldver) { // modified
				$rta_sql="UPDATE $rta_tbl SET ";
				for ($i=1;$i<count($rta_nd);$i++){
					if ($i>1) $rta_sql.=", ";
					$rta_sql.= "`$rta_fieldlist[$i]`='$rta_nd[$i]'";
				}
				$rta_sql.=" WHERE `$rta_fieldlist[0]`='$rta_nd[0]'";
				$rta_result=rtd_query($rta_sql);
				//echo $rta_sql;
				unset ($rta_sql);
				if (!$rta_result) die("Database Error 816");
			} else if ($rta_info[2]>$rta_oldver) {
				$rta_sql="DELETE FROM $rta_tbl ";
				$rta_sql.=" WHERE `$rta_fieldlist[0]`='$rta_nd[0]'";
				$rta_result=rtd_query($rta_sql);
				if (!$rta_result) die("Database Error 821");
				}
			}
			unset ($rta_nd);
		}
	if ($rta_addrec) {
		$rta_sql="INSERT INTO $rta_tbl (`".implode("`, `",$rta_fieldlist)."`) VALUES \n".$rta_addrec;
		$rta_result=rtd_query($rta_sql);
		if (!$rta_result) {
			//var_dump( $rta_sql);
			die("Database Error 829");
		}
		unset ($rta_tbl, $rta_fieldlist, $rta_addrec);
	}
	unset ($rta_fieldlist);
}
return;
}
?>
