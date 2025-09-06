<?php
/*******************************************************************************
 * step5.php
 *******************************************************************************
 * Version 2.50 updated 2010-05-10
 * -----------------------------------------------------------------------------
 * The fifth step of the install.
 * will create/overwrite rt.php with the settings (regardless of upgrade/install)
 ******************************************************************************/
/*
define ("rts_domain", 'http://127.0.0.1');        // 2.1 - 3  from step 2
define ("rts_syspath", '/rt-2.10.1');             // 2.1 - 4  from step 2
define ("rts_maint", '0');                        // 2.1 - 2  keep on
define ("rts_cookie", 'raidtracker');             //          set in step 5
define ("rts_server_offset", '-2');               // 2.1 - 1  set in step 5

define ("rts_guild", 'Test guild');               // 2.1 - 5  set in step 5
define ("rts_realm", 'US Quel\'Thalas');          // 2.1 - 6  set in step 5
define ("rts_levdefault", '80');                  // 2.1 - 8  set in step 5
define ("rts_style", 'default');                  // 2.1 - 7  set in step 5
define ("rts_news", '1');                         //          keep on 1 (main page only)

define ("rts_sort_time", '0');                    // 2.1 -10  keep on 0 (alphabetical)
define ("rts_TOS", '0');             // 2.1 default (keep off, not working)
define ("rts_tosv", '0');            // 2.1 default (keep off, not working)
define ("rts_days_back", '-1');      // 2.1 default //keep off -1 for week view
define ("rts_days_start", '1');      // 2.1 default set 0 for sunday

define ("rts_datespan", '30');       // 2.1 default set 30
define ("rts_weeks", '4');           // 2.1 default set 3 for 3 weeks: 2 forward, 1 back
define ("rts_datef", 'Y-m-d');       // new for 2.5 set Y-m-d ISO 8901
define ("rts_timef", 'h:ia');        // new for 2.5 set H:i ISO 8901
define ("rts_view", 'h');            // new in 2.5  set c for calendar

define ("rts_override", '0');        // new in 2.5  set 0, allow choice
define ("rts_navigate", '1');        // new in 2.5  set 1, though not working
define ("rts_hist_prev", '50');      // new for 2.5 set 25 for latest 25 events
define ("rts_hist_future", '1');     // new for 2.5 set 1 for future open

define ("rts_file_version", '2.49'); // new for 2.5 set for current version
*/
//************************************************************* rt.php constants
// load or set essential settings that MUST be set
//$rtv_step--;
//***************************************** upgrade or form data (if given)
if ($rtv_install=='upgrade' || ($rtv_server['guild'] && $rtv_server['realm'])) { //upgrade or new install with guild settings
	if ($rtv_install=='upgrade') include _RT_SYS_HOME . "/user/rt.php"; // get old file
	if ($rtv_oldRT < 2.10) { // new install only
		// fields here
		$rtv_server['maint']='0';
		$rtv_server['news']='1';
		$rtv_server['sort_time']='0';
		$rtv_server['TOS']='0';
		$rtv_server['tosv']='0';
		$rtv_server['days_back']='-1';
		$rtv_server['days_start']='1';
		$rtv_server['datespan']='30';
		$rtv_server['weeks']='3';
	} else { // get old data from 2.10
		$rtv_server['domain']=rts_domain;
		$rtv_server['syspath']=rts_syspath;
		$rtv_server['cookie']=rts_cookie;
		$rtv_server['server_offset']=rts_server_offset;
		$rtv_server['guild']=rts_guild;
		$rtv_server['realm']=rts_realm;
		$rtv_server['chardefault']=rts_chardefault;
		$rtv_server['levdefault']=rts_levdefault;
		$rtv_server['style']=rts_style;
		$rtv_server['maint']=rts_maint;
		$rtv_server['news']=rts_news;
		$rtv_server['sort_time']=rts_sort_time;
		$rtv_server['TOS']=rts_TOS;
		$rtv_server['tosv']=rts_tosv;
		$rtv_server['days_back']=rts_days_back;
		$rtv_server['days_start']=rts_days_start;
		$rtv_server['datespan']=rts_datespan;
		$rtv_server['weeks']=rts_weeks;
	}
	if ($rtv_oldRT < 2.50) { // all versions prior to 2.50 (includes 2.10)
		// fields here
		$rtv_server['datef']='Y-m-d';
		$rtv_server['timef']='H:i';
		$rtv_server['view']='c';
		$rtv_server['override']='0';
		$rtv_server['navigate']='1';
		$rtv_server['hist_prev']='25';
		$rtv_server['hist_future']='1';
	}
	// all other fields here like file_version
	$rtv_server['file_version']=RTS_VERSION; 
	if (!($rta_file=fopen("user/rt.php","w"))) {  //open rt.php for writing
		$rts_internal['main'].="<h1>Install error</h1>\n<p>File could not be created:  Please check that the /user folder is write accessible, and then hit Refresh to continue</p>";
		return;
	}
	// write database info in db.php
	fwrite ($rta_file, "<?php\n");
	foreach ($rtv_server as $k=>$v)
		fwrite ($rta_file, 'define ("rts_'.$k."\", \"$v\");\n");
//	if ($rtv_server['TOS']=='false' ) {
//		fwrite ($rta_file, 'define ("rts_tosv", "0");'."\n");
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rtv_stepnext=6;
	$rta_header="<h1>Saving settings</h1>\n
	<p>RaidTracker ".RTS_VERSION." basic settings now saved.* <br />\n
	* You may check in the Administration menu after install for the new settings</p>\n";
	$rta_button="Almost done";
	
//	$rts_internal['main'].=form(div($rta_form),'.');
} else { //new install, no data
	$rta_header="<h1>RaidTracker Default Settings</h1>\n
	<p>These settings are needed to make sure RaidTracker is functioning correctly. Please check to make sure these settings
	are correct. You may change these settings and others in the Administration menu once installation is complete.</p>";
	if (!isset($rtv_server['server_offset'])) $rtv_server['server_offset']='0';
	if (!isset($rtv_server['style'])) $rtv_server['style']='default';
	if (!isset($rtv_server['levdefault'])) $rtv_server['levdefault']='0';
	if (!isset($rtv_server['chardefault'])) $rtv_server['chardefault']='0';
	if (!isset($rtv_server['cookie'])) $rtv_server['cookie']='raidtracker';
	$rta_time=date("H:i");
	$rta_form=textfield('sct',"Current time:", $rta_time,'line','5','disabled="disabled"');
	$rta_form.=textfield('server[server_offset]',"Offset in hours to realm time:", $rtv_server['server_offset']);
	$rta_form.=textfield('server[guild]',"Guild Name:",$rtv_server['guild']);
	$rta_form.=textfield('server[realm]',"Realm and region:",$rtv_server['realm']);
	$rta_form.="<div>If you have created or installed another style, please enter folder name below.  Otherwise leave it as default.</div>\n";
	$rta_form.=textfield('server[style]',"Style:",$rtv_server['style']);
	$rta_form.=textfield('server[levdefault]',"Default level:",$rtv_server['levdefault']);
	$rta_form.=textfield('server[chardefault]',"Default group size:",$rtv_server['chardefault']);
	$rta_form.=textfield('server[cookie]','Cookie name:',$rtv_server['cookie'],'line','10');
	$rta_button="Continue";
	$rtv_stepnext=5;
}
$rta_form.=span('&nbsp;','','spaceleft').span(button($rta_button),'','spaceright');
$rta_form.=input('server[domain]','hidden',$rtv_server['domain']);
$rta_form.=input('server[syspath]','hidden',$rtv_server['syspath']);
$rta_form.=input('install','hidden',$rtv_install);
$rta_form.=input('oldRT','hidden',$rtv_oldRT);
$rta_form.=input('step','hidden',$rtv_stepnext);
$rta_form=form(div($rta_form),'.','post','trueform');
$rts_internal['main']=$rta_header.$rta_form.br();
return;

/*
if ($rtv_install=='upgrade') {  // if this is an upgrade, include rt.php
	include RTS_HOME . "/user/rt.php";
}
if ($rtv_oldRT < 2.10) {        // new install, start with essentials
	define ('rts_domain',"$rtv_server[domain]");    // set from step 2
	define ('rts_syspath',"$rtv_server[path]");     // set from step 2
	$rta_header="<h1>Initial RaidTracker Settings</h1>\n
	<p>Please check these settings </p>
}

 else {                        // new install, set essentials here for form


	define ('rts_server_offset','0');               // server offset is assumed 0
	define ('rts_maint','0');                       // maintenance mode off
	define ('rts_domain',"$rtv_server[domain]");    // set from step 2
	define ('rts_syspath',"$rtv_server[path]");     // set from step 2
	define ('rts_guild',"");                        // no guild name
	define ('rts_realm',"");                        // no realm name
	define ('rts_style',"default");                 // default style used
	define ('rts_levdefault',"0");                  // level 0  
	define ('rts_chardefault',"");  // ???
	define ('rts_sort_time',"false");

 	define ('rts_tosv',"false");
}
// -- Introduced 2.1 system user variables
define ("rts_datespan", '30');
define ("rts_days_back", '-1');
define ("rts_days_start", '1');
define ("rts_weeks", '2');
define ("rts_cookie", 'raidtracker');
define ("rts_news", '1');
// -- introduced 2.5 system variables

// -- end 2.1 system settings
$rta_form=input('server[domain]','hidden',rts_domain);           // set in step 2,or as upgrade
$rta_form.=input('server[syspath]','hidden',rts_syspath);        // set in step 2 or as upgrade
$rta_form.=input('server[maint]','hidden',rts_maint);            // set off by default or as upgrade
// -- initial settings for RT 2.1.  Introduced 2.1
$rta_form.=input('server[datespan]','hidden','30');
$rta_form.=input('server[days_back]','hidden','-1');
$rta_form.=input('server[days_start]','hidden','1');
$rta_form.=input('server[weeks]','hidden','2');
$rta_form.=input('server[news]','hidden','1');
// -- end 2.1 settings
$rts_internal['main']="<h1>RaidTracker Settings</h1>\n<p>Please enter or check that"
." these settings are correct. Once this is set up, you can change these and other "
." display preferences in the Administration menu.</p>\n";
$rta_time=date("H:i");
$rta_f2=textfield('sct',"Current time:", $rta_time,'line','5','disabled="disabled"');
$rta_f2.=textfield('server[server_offset]',"Offset in hours to realm time:", rts_server_offset);
$rta_f2.=textfield('server[guild]',"Guild Name:",rts_guild);
$rta_f2.=textfield('server[realm]',"Realm and region:",rts_realm);
$rta_f2.="<div>If you have created or installed another style, please enter folder name below.  Otherwise leave it as default.</div>\n";
$rta_f2.=textfield('server[style]',"Style:",rts_style);
$rta_f2.=textfield('server[levdefault]',"Default Level:",rts_levdefault);
$rta_f2.=textfield('server[chardefault]',"Default raiders:",rts_chardefault);
$rta_f2.=group('Sort lists by:','label','','heading spaceleft',"for=\"server[sort_time]\"").
  sel('server[sort_time]',array ('false'=>'Character', 'true'=>'Time created'),rts_sort_time,'','spaceright').br();
//$rta_f2.=group('Separate closure times:','label','','heading spaceleft',"for=\"server[separate_close]\"").
//  sel('server[separate_close]',array ('false'=>'No', 'true'=>'Yes'),'false','','spaceright').br();
//insert game here
$rta_f2.=group('Require TOS?','label','','heading spaceleft',"for=\"server[TOS]\"").
  sel('server[TOS]',array ('false'=>'No', 'true'=>'Yes'),rts_tosv,'','spaceright').br();
$rta_f2.=textfield('server[cookie]','Cookie name:',rts_cookie,'line','10');
$rta_f2.=span('&nbsp;','','spaceleft').span(button('continue'),'','spaceright');
$rta_form.=input('install','hidden',$rtv_install);
$rta_form.=input('step','hidden','5');
$rta_form=form(div($rta_f2.$rta_form),'.','post','trueform');
$rts_internal['main'].=$rta_form.br();
*/
?>