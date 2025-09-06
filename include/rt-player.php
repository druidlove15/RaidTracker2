<?php
/******************************************
 * rt-player.php
 *-----------------------------------------
 * Shows a table containing players.
 * If permissions are set, will show hidden
 * fields, and a form to change
 ******************************************/
// ------------- Query here --------------
$t=GetRec("SELECT * FROM raid_account ORDER BY `main`");
//-------------- Start page --------------
echo "<h1>Guild Roster</h1>\n";
if (rtf_p('player_alter')) // if alter privs, show form rights
	echo '<form method="post" action="/rtracker/admin/">'
	."\n<input type=\"hidden\" name=\"guildroster\" value=\"change\" />\n";
echo "<table style=\"width: 100%\">\n";
echo "<tr>";
if (rtf_p('player_alter'))  //alter privs-checkbox
	echo "<th style=\"width: 1%\">&nbsp;</th>";
echo "<th>Main character</th>";
if (rtf_p('show_email')) echo "<th>e-mail</th>";
if (rtf_p('show_grank')) echo "<th>Guild rank</th>";
if (rtf_p('show_rtrank')) echo "<th>RT Rank</th>";
if (rtf_p('show_stats')) {
	echo "<th><img src=\"/rtracker/images/1.png\" alt=\"Raid List\" /></th>"
	. "<th><img src=\"/rtracker/images/2.png\" alt=\"Signup\" /></th>"
	. "<th><img src=\"/rtracker/images/3.png\" alt=\"Reserve\" /></th>"
	. "<th><img src=\"/rtracker/images/4.png\" alt=\"Withdraw\" /></th>"
	. "<th><img src=\"/rtracker/images/5.png\" alt=\"Removed\" /></th>";
}
echo "</tr>\n";

foreach ($t as $v) {  //-------------------- show player columns
	echo "<tr>";
	if (rtf_p('player_alter')) // checkbox if alter-privs
		echo "<td><input type=\"checkbox\" name=\"guild[]\" value=\"$v[id]\" /></td>";
	echo "<td><a href=\"/rtracker/character/?charid=$v[id]\">$v[main]</a></td>";
	if (rtf_p('show_email', $v['id'])) echo "<td>$v[email]</td>";
	if (rtf_p('show_grank', $v['id'])) echo "<td>".rtf_grank($v['guildrank'])."</td>";
	if (rtf_p('show_rtrank', $v['id'])) echo "<td>".rtf_rtrank($v['rtrank'])."</td>";
	if (rtf_p('show_stats')) {
		for ($i=1; $i<8; $i++) {
			$attsql="SELECT * FROM raid_sign INNER JOIN raid_list ON raid_sign.raidid = raid_list.id
		WHERE raid_sign.charid =$v[id] AND raid_sign.status =$i
		AND (raid_list.date BETWEEN DATE_SUB( NOW( ) , INTERVAL 2 MONTH) AND NOW())";
			$stat[$i]=count(GetRec($attsql));
		}
		$stat[2]+=$stat[6];
		$stat[3]+=$stat[7];
		echo "<td>$stat[1]</td><td>$stat[2]</td><td>$stat[3]</td>"
		."<td>$stat[4]</td><td>$stat[5]</td>";
	}
	echo "</tr>\n";
}
echo "</table>\nClick on the player above to see all characters controlled by the player.\n";
if (rtf_p('show_stats'))
	echo "<div>Statistics count raids started within the last two months ending "
	. rtf_datetime(strtotime("now"),'dt')."</div>\n";
if (rtf_p('player_alter')){
	echo "<div>\n<h2>Action to selected players</h2>\n";
	if (rtf_p('grank_alter')||rtf_p('rtrank_alter')) {
		echo '<input type="radio" name="option" value="rank" checked /> Change Rank: ';
		if (rtf_p('rtrank_alter')) {
			echo "RaidTracker permissions: ";
			echo "<select name=\"rtrank\">\n";
			echo '<option selected value="0">---select---</option>'."\n";
			for ($i=1; $i<=count($rtg_rtrank);$i++)
				echo "<option value=\"$i\">".rtf_rtrank($i)."</option>\n";
			echo "</select>\n";
		}
		if (rtf_p('grank_alter')) {
			echo "Guild Rank: ";
			echo "<select name=\"guildrank\">\n";
			echo '<option selected value="0">---select---</option>'."\n";
			for ($i=1; $i<=count($rtg_grank);$i++)
				echo "<option value=\"$i\">".rtf_grank($i)."</option>\n";
			echo "</select>\n";
		}
		echo br();
	}
	if (rtf_p('player_delete'))
		echo '<input type="radio" name="option" value="delete" /> Delete players'.br();
	echo '<input type="submit" name="submit" value="Submit" />'."\n"
	."</div>\n</form>";
}
?>