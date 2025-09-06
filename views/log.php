<?php
/*******************************************************************************
 * log.php
 * -----------------------------------------------------------------------------
 * Version 2.00 log viewer
 ******************************************************************************/
//--------------------------------- check for permissions first
if (!rtf_p('view_log') && !rtf_p('admin_view')) {
	$rts_internal['main']="<h1>Log Viewer</h1>\n<p>Sorry, you are not authorized "
	. "to see the log.</p>\n";
	return;
}
//--------------------------------- set up variables passed in
if (!$rtv_logstart) $rtv_logstart=0; //first record
if (!$rtv_logint) $rtv_logint=100;   //interval
$rta_total=rtd_select(rts_db_hist,"COUNT(*) as 'total'",'1');
$rta_total=$rta_total['total'];      //establish total
if ($rtv_logstart<0) $rtv_logstart=0;
else if ($rtv_logstart>$rta_total) $rtv_logstart=$rta_total;


if ($rtv_clear) {
	rtd_query("TRUNCATE TABLE ". rts_db_hist);
	rtf_log ("log","Cleared the log");
}

//-------------- Submit table
$loglist=rtd_select(rts_db_hist,'*','',"ORDER BY `date` DESC LIMIT $rtv_logstart, $rtv_logint",1);

//-------------- Functions
function linkref($cat) {
	list ($rt_cat, $rt_number)=split(" ",$cat);
	switch ($rt_cat){
		case "news":
		case "character": return $rt_cat; // may need to fix that.
		case 'log':
		case 'account':
					return $rt_cat;
		case "signup":
		case "raid":
		case "raidinfo":
			$url=rts_syspath."/?raidid=$rt_number";
			break;
	}
	return "<a href=\"$url\">$rt_cat</a>";
}
function rt_readreason (&$text){
	rt_ricon($text);
	rt_plist($text);
	rt_reppl($text);
	rt_raidrole($text);
}
function rt_ricon (&$t) {
	$t=preg_replace_callback('/^(\[icon=)([1-7])(\])/', create_function('$matches','return rtf_show_icons("status",$matches[2]);'), $t);
	$t=preg_replace_callback('/(\[role=)([1-4])(\])/', create_function('$matches','return rtf_show_icons("role",$matches[2],1);'), $t);
	$t=preg_replace_callback('/(\[class=)([^]]+)(\])/', create_function('$matches','return rtf_show_icons("class",$matches[2]);'), $t);

	while ($m=preg_match('/\[raidicon\]([a-z0-9]+?)\[\/raidicon\]/',$t, $match)) {
		$replstr='<img src="'._RT_SYS_WEB.'images/instance/'.$match[1].'.png" alt="'.$match[1].'" />';
		$t=preg_replace("/(\[raidicon\]$match[1]\[\/raidicon\])/", $replstr, $t);
	}
}
//--char friendly
function rt_plist(&$t){
	$m=preg_match('/\[plist\]((([0-9]*),?)*)\[\/plist\]/',$t, $match);
	if (!$match[1]) return;
	$rta_rep=explode (",",$match[1]);
	for ($i=0; $i<count($rta_rep);$i++)
		$rta_rep[$i]=rtf_character($rta_rep[$i]);
	$rta_replist=implode (", ",$rta_rep);
	$t=preg_replace("/(\[plist\]$match[1]\[\/plist\])/", $rta_replist, $t);
}
function rt_reppl(&$t){
	while ($m=preg_match('/\[pchar\]([0-9]*)\[\/pchar\]/',$t, $match)) {
		$replstr=rtf_character($match[1]);
		$t=preg_replace("/(\[pchar\]$match[1]\[\/pchar\])/", $replstr, $t);
	}
}
function rt_raidrole(&$t) {
//while ($m=preg_match ('/(\[role=)([1-4])(\])/',$t, $match))
//	$t=preg_replace('/(\[role=)([1-4])(\])/', rtf_role($match[2]), $t);
}
//-------------- Character table
$rta_main="<h1>RaidTracker Log</h1>\n";

$rta_line="Viewing records ".($rtv_logstart+1) ." to ". ($rtv_logstart+$rtv_logint) .". ";
if ($rtv_logstart>0) {
	$rtv_logbefore=$rtv_logstart-$rtv_logint;
	if ($rtv_logbefore<0) $rtv_logbefore=0;
	$rta_line.=" ".url("./?view=log&amp;logstart=$rtv_logbefore&amp;logint=$rtv_logint","< Previous")." ";
}
if ($rtv_logstart+$rtv_logint<$rta_total) {
	$rtv_logafter=$rtv_logstart+$rtv_logint;
	$rta_line.=" ".url("./?view=log&amp;logstart=$rtv_logafter&amp;logint=$rtv_logint","Next >");
}
$rta_main.=div($rta_line);
$rta_row=td("Category",true,'','small').NL
.td("Date & Time",true,'','medium').NL
.td("Account",true,'','medium').NL
.td("Reason",true).NL;
addrow($rta_table, $rta_row);
foreach ($loglist as $v) {
	$rt_cat=linkref($v['category']);
	$rta_row=td($rt_cat).NL.td($v['date']).NL;
	if ($v['person']==0) $rta_player="Web guest";
	else {
		$rta_player=rtf_character(rtf_acct2char($v['person']));
	}
	$rta_row.=td($rta_player).NL;
	$v2=stripslashes($v['reason']);
	rt_readreason ($v2);
	$rta_row.=td($v2).NL;
	addrow($rta_table,$rta_row);
}
$rta_main.=tbl($rta_table).NL;
if (rtf_p('log_clear')) {
	$rta_form=$rta_line.' '.button('clear','Clear Log');
	$rta_form=form($rta_form, '.');
	$rta_main.=$rta_form;
}
$rts_internal['main']=$rta_main;
?>