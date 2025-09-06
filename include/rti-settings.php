<?php
/******************************************************************************
 * rt-settings.php
 ******************************************************************************
 * Version 2.10  last updated 2009-08-25
 * ----------------------------------------------------------------------------
 * Sets up database table vars and system time
 * All rt system variables go here
 * Please don't mess with this file.
 *****************************************************************************/

//-- non constant, yet system variables:
$rts_currtime=strtotime(date('l Y-m-d H:i:s'));
if (rts_server_offset<0)
	$rts_currtime=strtotime(''.rts_server_offset." hours", $rts_currtime);
else if (rts_server_offset>0)
	$rts_currtime=strtotime("+ ".rts_server_offset." hours", $rts_currtime);
define (rts_currtime, $rts_currtime);
$rts_curr=new TIME($rts_currtime);

define (NL, "\n");
$rts_title="RaidTracker2 | $title"; //needs to be moved out

//-- setup database
define ('rts_db_acct', "{$rts_dbprefix}account");
define ('rts_db_char', "{$rts_dbprefix}char");
define ('rts_db_hist', "{$rts_dbprefix}hist");
define ('rts_db_keys', "{$rts_dbprefix}keys");
define ('rts_db_list', "{$rts_dbprefix}list");
define ('rts_db_perm', "{$rts_dbprefix}permission");
define ('rts_db_sign', "{$rts_dbprefix}sign");

//-- for page title // need to check if this still applies
$_page['title']=$rts_title;
$_page['guild']=rts_guild;
$_page['realm']=rts_realm;
?>