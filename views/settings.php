<?php
/*******************************************************************************
 * settings.php
 *******************************************************************************
 * Version 2.0  2008-10-12
 * Outputs the settings screen
 ******************************************************************************/
//--------------------------------------------- Main variables
$rta_mainchar='*';

//--------------------------------------------- Set up player info
if (!$rtv_playerid) $rtv_playerid=rtc_acct();  //gets logged in player info if not passed in
if (!$rtv_playerid) {                          //not logged in
	$rts_internal['main']="<h1>Fatal error</h1>\n<p>Please log in to view settings</p>\n";
	return;
}
$rta_playerlist=rtf_playermatrix('acctid',$rtv_playerid);  //retrieve list of char for player
if (!rta_playerlist) {                         //no list: error
	$rts_internal['main']="<h1>Fatal Error</h1>\n<p>No characters for this account, please see an admin</p>\n";
	return;
}
//--------------------------------------------- set up permissions
$rta_perm['alter']=rtf_p('player_alter',$rtv_playerid);  // can alter player
$rta_perm['stats']=rtf_p('show_stats',$rtv_playerid);  // can alter player
$rta_form_var=input('formview','hidden','settings'); // form setup

//--------------------------------------------- create main table
$rta_table='';
$rta_row='';
//------------------ create header
$rta_row.=td('',true,'','xsmall');
$rta_row.=td('Class and name',true,'','medium');
$rta_row.=td('Level',true,'','small');
$rta_row.=td('Main role',true,'','medium');
$rta_row.=td('Notes',true);
addrow($rta_table,$rta_row);
$rta_table.=NL;
$rta_row='';
//------------------ create each row
for ($i=0; $i<count($rta_playerlist); $i++) {
	if ($rta_playerlist[$i]['main']==$rta_playerlist[$i]['charname'])   // if main is same as character name
		$rta_row.=" $rta_mainchar ";
	$rta_row.=($rta_perm['alter']?input('formdata[char]','radio',$rta_playerlist[$i]['charid']):'');  //if can alter, add radio box
	$rta_row=td($rta_row,false,'','right').NL;  // make a cell for it.
	$rta_row.=td(rtf_character($rta_playerlist[$i]['charid'],0,2,0)).NL;  //adds player class and name
	$rta_row.=td($rta_playerlist[$i]['level']).NL;
	$rta_row.=td(rtf_role($rta_playerlist[$i]['role'])).NL;
	$rta_row.=td('').NL;
	addrow($rta_table, $rta_row);  // adds a row to table
	$rta_table.=NL;
	$rta_row='';
}
$rta_table=tbl($rta_table);
$rta_table="<h1>Character details</h1>\n$rta_table";
$rta_table.=div(" $rta_mainchar denotes main. ");
if ($rta_perm['alter']) {  //---- if one can alter information, add the following
	$rta_table.=input('action','hidden','modify').NL;
	if (rtf_p('character_add')) $rta_table.=div(button('formdata[addchar]','Add character')).br();
	$rta_table.="<h2>Character Management</h2>\n";
	$rta_table.='<div class="trueform">'.NL;
	$rta_updateline='';  //set updateline to null in case no update permissions set.
	if (rtf_p('character_name',$rtv_playerid)) $rta_updateline=textfield('formdata[charname]','Name:');
	if (rtf_p('character_level',$rtv_playerid)) $rta_updateline.=' '.textfield('formdata[level]','Level:');
	if (rtf_p('character_class',$rtv_playerid)) $rta_updateline.=' '.rtf_classsel('formdata[class]','','1');
	if (rtf_p('character_role',$rtv_playerid)) $rta_updateline.=' '.rtff_role('formdata[role]',0,0);
	if ($rta_updateline) $rta_table.=div(input('formdata[charmanage]','radio','update')." Update with:").br().$rta_updateline.br();
	if (count($rta_playerlist)>1) {  //if there is more than one character
		$rta_table.=div(input('formdata[charmanage]','radio','main','','','',(rtf_p('character_main')?'':"disabled=\"disabled\""))."Establish as main character").br();
		$rta_table.=div(input('formdata[charmanage]','radio','delete','','','',(rtf_p('character_delete')?'':"disabled=\"disabled\""))."Delete character").br();
	}
	$rta_table.=div('&nbsp;','','spaceleft');
	$rta_table.=div(button('submit','Submit','spaceright')).br();
	$rta_table=$rta_form_var.NL.input('playerid','hidden',$rtv_playerid).NL.$rta_table;
	$rta_table.="\n</div>\n";
	$rta_table=form($rta_table,'.','post');
}
//----------------------------------------- Account settings
if ($rta_perm['alter']) {
$rta_info=rtd_select(rts_db_acct,'*',"id=$rtv_playerid");
$rta_table.= "<h1>Account Settings</h1>\n"
. "<form method=\"post\" action=\".\" class=\"trueform\">\n"
. "<input type=\"hidden\" name=\"playerid\" value=\"$rtv_playerid\" />\n"
. textfield('formdata[email]','e-mail address:',"$rta_info[email]")
. textfield('formdata[datef]','Date format*:',"$rta_info[datef]")
. textfield('formdata[timef]','Time format*:',"$rta_info[timef]")
;
$rta_table.= "<span class=\"spaceleft heading\">Default view:</span> ".
 sel('formdata[view]',array('c'=>'Calendar', 'h'=>'History'),$rta_info['view'][0]).br();
$rta_table.= "<div class=\"spaceleft\">&nbsp;</div>\n<input class=\"btn\" type=\"submit\" name=\"button\" value=\"Change\" class=\"spaceright\" />".br()
. "* Date and Time format uses the standard PHP <a href=\"http://se2.php.net/manual/en/function.date.php\">date</a> rules.".br();
$rta_table.=input('formview','hidden','settings2');
$rta_table.= "</form>\n";
}
//------------------------------------------- Show stats
if ($rta_perm['stats']) {
	if (!$rtv_span) $rtv_span=30;
	if ($rtv_span>0) $rta_tmp=" AND (".rts_db_list.".date BETWEEN DATE_SUB( NOW() , INTERVAL $rtv_span DAY) AND NOW())";
	else $rta_tmp='';
	for ($i=1; $i<=7; $i++) {
		$rta_count[$i]=count(rtd_select(rts_db_sign.' INNER JOIN '.rts_db_list .' ON ' .rts_db_sign .'.raidid = '.rts_db_list.'.id',
		'*',rts_db_sign.".charid=$rtv_playerid AND ".rts_db_sign.".status =$i".$rta_tmp,'',1));
	}

	$rta_raidsign=rtd_select(rts_db_sign.', '.rts_db_list,rts_db_sign.'.*, '.rts_db_list.'.date, '.rts_db_list.'.icon, '.rts_db_list.'.instance',rts_db_list.".id=".rts_db_sign.".raidid AND ".rts_db_sign.".charid =$rtv_playerid","ORDER BY ".rts_db_list.".date DESC LIMIT 30",1);
	$rta_table.= "<h1>Player Stats</h1>\n<h2>Raid signup counts</h2>\n";
	$rta_form=sel('span',array(7=>'7',14=>'14',30=>'30',60=>'60',90=>'90',-1=>'Life'),$rtv_span);
	$rta_form="Show last $rta_form days";
	$rta_form.=button('submit','Go');
	$rta_form.=input('playerid','hidden',"$rtv_playerid");
	$rta_form=form($rta_form,'.','post');
	$rta_table.=$rta_form;
	for ($i=1; $i<6; $i++) {
		if ($i==2 || $i==3) $rta_raidcount=$rta_count[$i]+$rta_count[$i+4];
		else $rta_raidcount=$rta_count[$i];
		$rta_table.= rtf_show_icons ('status',$i,1)." - $rta_raidcount<br />\n";
	}
	$rta_table.= br();
	$rta_table.= "<h2>Raid history</h2>\n";
	if ($rta_raidsign) {

	$rta_table.= "Last 30 raid history".br();
		$rta_row=td("Date",1).td("Time",1).td("Status",1).td("Location",1).td("Character",1).td("Role",1).td("Notes",1);
		addrow($rta_tbl, $rta_row);
		foreach ($rta_raidsign as $v){
			$rta_table.= "<tr><td>";
			$rta_row=td(rtf_datetime(strtotime($v['date']),d)) //date
			.td(rtf_datetime(strtotime($v['date']),t)) //time
			.td(rtf_show_icons('status',$v['status'])) //status
			.td(url('.',$v['instance'],"raidid=$v[raidid]")) //instance name  <-- need to add icon
			.td(rtf_character($v['char'],0,2,0)) //character
			.td(rtf_show_icons('role',$v['role'],1)) //role
			.td($v['note']?"-".rtf_offnote($v[note]):""); //notes
			addrow($rta_tbl, $rta_row);
		}
		$rta_table.=tbl($rta_tbl);
		$rta_table.= "</table>\n";
	} else {
		$rta_table.= "No raid history".br();
	}
}

$rts_internal['main']=$rta_table;


?>