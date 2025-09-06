<?php
/*******************************************************************************
 * rank.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Rank adjustment screen
 ******************************************************************************/
// no permissions to be here. 
if (!rtf_p('rank_alter')) {
	$rts_internal['main']="<h1>Error</h1><p>You do not have the priviledges to "
	. "be here.</p>";
	return;
}
if ($_POST) {
	if ($rtv_rtsubmit) $rta_cat="rt"; else $rta_cat="guild";
	if ($rtv_rtsubmit=="Delete"||$rtv_guildsubmit=="Delete") {
		$sql="DELETE FROM ".rts_db_keys." WHERE category='$rta_cat' AND `name`='".$rtv_cname[$rtv_id]."'";
		for ($i=$rtv_id+2; $i<11;$i++) {
			$rta_sql=rtd_update(rts_db_acct,array("{$rta_cat}rank"=>$i-1),"{$rta_cat}rank = $i");
		}
	}else if ($rtv_cname[$rtv_id]=="-new-")
		$sql="INSERT INTO ".rts_db_keys." SET category='$rta_cat', `name`='$rtv_name[$rtv_id]'";
	else
		$sql="UPDATE ".rts_db_keys." SET `name`='$rtv_name[$rtv_id]' WHERE category='$rta_cat' AND `name`='$rtv_cname[$rtv_id]'";
	mysql_query($sql);
	$rts_internal['message'].="Ranks have been updated".br();
}
function rt_ranksql($text) {
	$sql="SELECT `name` from ".rts_db_keys." WHERE category='$text' ORDER BY id ASC";
	$result=rtd_select (rts_db_keys,array('name'),"category='$text'","ORDER BY id ASC",1);
	for ($i=0; $i<count($result); $i++)
		$r[$i]=$result[$i]['name'];
	return $r;
}
$rta_head="<h1>Ranks</h1>\n";
$rta_rtrank_list=rt_ranksql('rt');
$rta_grank_list=rt_ranksql('guild');
for ($i=0; $i<10; $i++) {
	if ($i+1<=count($rta_rtrank_list)) 
		$rta_rtlist.=input('id','radio',$i)
		.input ("name[$i]",'text',$rta_rtrank_list[$i])
		.input ("cname[$i]",'hidden',$rta_rtrank_list[$i]).br();
	else if ($i<=count($rta_rtrank_list)) 
		$rta_rtlist.=input('id','radio',$i)
		.input ("name[$i]",'text','(new rank)')
		.input ("cname[$i]",'hidden','-new-').br();
	if ($i+1<=count($rta_grank_list))
		$rta_glist.=input('id','radio',$i)
		.input ("name[$i]",'text',$rta_grank_list[$i])
		.input ("cname[$i]",'hidden',$rta_grank_list[$i]).br();
	else if ($i<=count($rta_grank_list)) 
		$rta_glist.=input('id','radio',$i)
		.input ("name[$i]",'text','(new rank)')
		.input ("cname[$i]",'hidden','-new-').br();
}
$rta_rtlist.=button("rtsubmit","Submit");
if (count($rta_rtrank_list>1)) $rta_rtlist.=button("rtsubmit","Delete");
$rta_glist.=button("guildsubmit","Submit");
if (count($rta_grank_list>1)) $rta_glist.=button("guildsubmit","Delete");
$rta_rtlist.=input("view",'hidden','rank');
$rta_glist.=input("view",'hidden','rank');
$rta_rtlist=td(form($rta_rtlist,"."));
$rta_glist=td(form($rta_glist,"."));
addrow($rta_table,td("Guild rank",true).td("RaidTracker Rank",true));
addrow($rta_table,$rta_glist.$rta_rtlist);
$rta_table=tbl($rta_table);
$rta_head.=$rta_table;
$rts_internal['main']=$rta_head;
?>