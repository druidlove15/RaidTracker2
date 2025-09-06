<?php
/*******************************************************
 *   rt-admin.php
 *------------------------------------------------------
 * Provides the admin menu
 *******************************************************/
$rt_menu='admin';  //sets admin menu option
//$rt_per['roster']=rt_Permissions(5); //check if user has enough permissions to view admin
//$rt_per['news']=rt_Permissions(4);   //check for news (officer+)
//------------------------ Received input
import_request_variables ('p','rt_');
if ($rt_guildroster) {
	if ($rt_option=='rank') {
		if (!$rt_rtrank && !$rt_guildrank) {
			echo "<h1>Error</h1>\n<p>Cannot update ranks.  Please specify a guild rank or RaidTracker Rank</p>\n";
		} else {
			$s="";
			if ($rt_rtrank) $s[]="rtrank='$rt_rtrank'";
			if ($rt_guildrank) $s[]="guildrank='$rt_guildrank'";
			$sql="UPDATE raid_account SET ".implode(", ",$s). "WHERE `id`='";
			for ($i=0; $i<count($rt_guild); $i++) {
				mysql_query($sql.$rt_guild[$i]."'");
			}
			echo "<h1>Ranks updated</h1>\n<p>Ranks are successfully assigned</p>\n";
		}
	} else if ($rt_option=='delete') {
		echo "<h1>Deletion request</h1>";
		$sql="DELETE FROM raid_account WHERE `id`=";
		$sql2="DELETE FROM raid_char WHERE `account`=";
		$sqllist="SELECT `main` FROM raid_account WHERE id=";
		$sqldelraid="DELETE FROM raid_sign WHERE charid=";
		for ($i=0; $i<count($rt_guild); $i++) {
			$n=$rt_guild[$i];
			if ($n==rt_GetAcct()) {
				echo "<p>Error: Cannot delete self.  Skipping.</p>";
				continue;
			}
			$rt_sqlresult=GetRec($sqllist.$n);
			$rt_nlist[$i]=$rt_sqlresult[1]['main'];
			mysql_query($sql.$n);
			mysql_query($sql2.$n);
			$y=mysql_query($sqldelraid.$n);
		}
		if ($rt_nlist) {
			$rt_lst=implode(", ",$rt_nlist);
			rt_log("account",rt_GetAcct(), "Deleted $rt_lst from system",'5');
			echo "<p>Players are successfully deleted</p>\n";
		}
	}
}
if (rtf_p('admin_view')){
	echo "<h1>Administration menu</h1>\n";
	if (rtf_p('update_news')) echo url("/rtracker/news",'Update the news').br();
	if (rtf_p('view_log')) echo url("/rtracker/log",'View the log').br();
	if (rtf_p('rank_alter')) echo url('/rtracker/rank','Change rank names').br();
	if (rtf_p('permission_alter')) echo url('/rtracker/perm','Change permissions').br();
}

//------------------------ Show table
include 'rt-player.php';
?>