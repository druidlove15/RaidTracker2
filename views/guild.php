<?php 
/*******************************************************************************
 * guild.php
 *******************************************************************************
 * Version 2.0  2008-10-10
 * -----------------------------------------------------------------------------
 * shows the guild list with template format.
 ******************************************************************************/
// -------------- set up permissions
$rta_perm['email']=rtf_p('show_email');
$rta_perm['guild']=rtf_p('show_grank');
$rta_perm['rt']=rtf_p('show_rtrank');
$rta_perm['stats']=rtf_p('show_stats');
$rta_perm['alter']=(rtf_p('player_delete')||rtf_p('grank_alter')||rtf_p('rtrank_alter')); //permission to alter
//--------------- set up guild list
$rta_guild=rtd_select(rts_db_acct,'*','','ORDER BY `main`',1);

//--------------- other setups
$title="Guild List view";
if (!$rtv_span) $rtv_span=rts_datespan;  //if no span set, sets it to 30 days by default.
//--------------- set up heading
$rta_table='';
$rta_row='';
addcell($rta_row, '','th');   // checkbox
addcell($rta_row,'Main character','th'); //main character
if ($rta_perm['email'])
	addcell ($rta_row,'e-mail address','th');  //e-mail
if ($rta_perm['guild'])
	addcell ($rta_row,'Guild Rank','th');  //guild rank
if ($rta_perm['rt'])
	addcell ($rta_row,'RT Rank','th');  //rt rank
if ($rta_perm['stats']){               //add stats
	for ($i=1;$i<6; $i++) {
		addcell ($rta_row,rtf_show_icons('status',$i),'th');
	}
}
addrow($rta_table, $rta_row);
$rta_table.=NL;
$rta_row='';

//------------- establish each row
for ($i=0; $i<count($rta_guild);$i++) {
	$rta_charid=rtf_acct2char($rta_guild[$i]['id']);
	addcell($rta_row,($rta_perm['alter']?"<input type=\"checkbox\" name=\"guild[]\" value=\"".$rta_guild[$i][id]."\" />":'')); //adds checkbox
	$rta_row.=NL;
	addcell($rta_row,rtf_character($rta_charid,0)); //adds character
	$rta_row.=NL;
	if ($rta_perm['email'])
		addcell($rta_row,$rta_guild[$i]['email']);  //adds email
	if ($rta_perm['guild'])
		addcell($rta_row,rtf_grank($rta_guild[$i]['guildrank']));  //adds guildrank
	if ($rta_perm['rt'])
		addcell($rta_row,rtf_rtrank($rta_guild[$i]['rtrank']-1));  //adds rtrank  need to fix that -1 rule
	if ($rta_perm['stats']) {  //adds stats
		for ($j=1; $j<8; $j++) {
			$attsql=rts_db_sign.".charid=".$rta_guild[$i]['id']." AND ".rts_db_sign.".status=$j";
			if ($rtv_span>0)
			$attsql.=" AND (".rts_db_list.".date BETWEEN DATE_SUB(NOW(), INTERVAL $rtv_span DAY) AND NOW())";
			$rta_stat[$j]=count(rtd_select(rts_db_sign." INNER JOIN ".rts_db_list." ON ".rts_db_sign.".raidid=".rts_db_list.".id",'*', $attsql,'',1));
		}
		$rta_stat[2]+=$rta_stat[6];
		$rta_stat[3]+=$rta_stat[7];
		for ($j=1;$j<6;$j++)
			addcell($rta_row,$rta_stat[$j]);
	}
	addrow($rta_table, $rta_row.NL);
	$rta_row='';
}
$rta_table=tbl($rta_table);
//--------------- put things together
$rts_internal['main']="<h1>Guild list</h1>\n";
if ($rta_perm['stats']) {
	$rta_form=sel('span',array(7=>'7',14=>'14',30=>'30',60=>'60',90=>'90',-1=>'Life'),$rtv_span);
	$rta_form="Show last $rta_form days";
	$rta_form.=button('submit','Go');
	$rta_form=form($rta_form,'.?view=guild','post');
	$rts_internal['main'].=$rta_form.NL;
}
if ($rta_perm['alter']) {
	$rta_table.="<h2>Actions to selected players</h2>\n";
	if (rtf_p('grank_alter'))
		$rta_table.="Change guild rank: ".rtff_grank('formdata[grank]',0,1).br();
	if (rtf_p('rtrank_alter'))
		$rta_table.="Change RaidTracker rank: ".rtff_rtrank('formdata[rtrank]',0,1).br();
	if (rtf_p('player_delete'))
		$rta_table.='<input type="checkbox" name="formdata[delete]" value="delete" /> Delete players <em>Warning: Cannot be undone.</em>'.br();
	$rta_table.=input('formview','hidden','guild');
	$rta_table.=button('submit','Submit');
	$rta_table=form(NL.$rta_table,'.','post');
}
$rts_internal['main'].=$rta_table;
?>