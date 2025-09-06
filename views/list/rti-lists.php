<?php
/**********************************************************************
 * rt-lists.php
 **********************************************************************
 * Ver. 2.0 style of displaying the lists
 **********************************************************************/
// Listchar:
// charid = id of char (char from raid_sign)
// role = role chosen (0=hide)
// chkbox = if checkbox is shown, the record in raid_sign (0=none)
// stat = show signup status (0=hide)

function rtf_listchar($charid, $chkbox, $stat) {
	$rta_html='';
	if ($chkbox) $rta_html="<input type=\"checkbox\" name=\"rec[]\" value=\"$chkbox\" />".NL;
	if ($stat) $rta_html.=rtf_statusicon($stat,0,0)."&nbsp;";
	$rta_html.=rtf_character ($charid,$chkbox);  //raid style popup
	return $rta_html;
}
function rtf_showchar($rowid, $hidecheckbox=0, $showstatus=1) {
	$rta_info=rtd_select(rts_db_sign,'*',"id=$rowid");
	if ($showstatus===true) $showstatus=$rta_info['status'];
	if ($hidecheckbox) $rta_row=0; else $rta_row=$rowid;
	$rta_html=rtf_listchar($rta_info['char'], $rta_row, $showstatus);
	if (rtf_p('view_playernote',$rta_info['charid']) && $rta_info['note'])
		$rta_html.=' '. rtf_offnote('[o]*[/o]');
	if (rtf_p('view_officernote',$rta_info['charid']) && $rta_info['offnote'])
		$rta_html.=' '. rtf_offnote('o','o');
	return $rta_html;
}
function rtf_statuslist($raid, $status) {
	if (is_array($status)) $status=rts_db_sign.".status IN (".implode(',', $status).")";
	else $status=rts_db_sign.".status=$status";
	if (rts_sort_time)
		$rta_sortorder=rts_db_sign.'.signup ASC, '.rts_db_char.".char ASC";
	else
		$rta_sortorder=rts_db_char.".class ASC, ".rts_db_char.".char ASC";
	$rta_info=rtd_select(rts_db_sign.', '.rts_db_char,rts_db_sign.'.*, '
	.rts_db_char.'.char, '.rts_db_char.'.class',
	rts_db_char.".id=".rts_db_sign.".char AND $status AND ".rts_db_sign.".raidid=$raid",
	"ORDER BY ".rts_db_sign.".role ASC, ".$rta_sortorder,1);
	return $rta_info;
}
function rtf_tableopt($wb) {
	$rta_htm='';
	//------------------------------- Whiteboard
	if (rtf_p('wb_publish') && $wb)
		$rta_htm.='<input type="submit" name="formdata[wb]" value="Publish" />';
	if (rtf_p('wb_erase') && $wb)
		$rta_htm.=' <input type="submit" name="formdata[wb]" value="Clear" />';
	if ($rta_htm) $rta_htm="<h3>Whiteboard:</h3> $rta_htm".br();
	$rta_htm.="Selected members will be modified.  ";
	if (rtf_p('list_addchar')) {
		$rta_htm.= //"<input type=\"checkbox\" name=\"apply[name]\" value=\"new\" />"
		"Add a new <strong>player</strong>: ";
		$rta_htm.="<select name=\"formdata[newplayer]\">\n";
		$t=rtd_select(rts_db_acct,'id, main','','ORDER BY main',1);
		$rta_htm.="<option value=\"0\">---select---</option>\n";
		foreach ($t as $v) {
			$rta_htm.="<option value=\"$v[id]\">$v[main]</option>\n";
		}
		$rta_htm.="</select>".br();
	}
	if (rtf_p('list_move')) {
		$rta_htm.= //"<input type=\"hidden\" name=\"apply[move]\" value=\"move\" />"
		"Move to: <select name=\"formdata[status]\">\n";
		$rta_htm.='<option value="0">--select--</option>'."\n";
		if (rtf_p('list_raidlist')) $rta_htm.= '<option value="1">Raid list</option>'."\n";
		if (rtf_p('list_wb')) $rta_htm.= '<option value="8">Whiteboard</option>'."\n";
		if (rtf_p('list_available')) $rta_htm.= '<option value="2">Available</option>'."\n";
		if (rtf_p('list_reserve')) $rta_htm.= '<option value="3">Reserve</option>'."\n";
		if (rtf_p('list_withdraw')) $rta_htm.= '<option value="4">Withdraw</option>'."\n";
		if (rtf_p('list_remove')) $rta_htm.= '<option value="5">Remove</option>'."\n";
		if (rtf_p('list_delete')) $rta_htm.= '<option value="-1">Delete</option>'."\n";
		$rta_htm.= "</select>\n".br();
	}
	if (rtf_p('change_alt')||rtf_p('change_role'))
		$rta_htm.="<input type=\"checkbox\" name=\"formdata[change]\" value=\"role\" />"
		. "Change raid role or use an alt (on next screen) *".br();
	if (rtf_p('edit_publicnote'))
		$rta_htm.= "<input type=\"checkbox\" name=\"formdata[cpnote]\" value=\"move\" />"
		. 'Replace public note with:  <input type="text" name="formdata[pnote]" />'.br();
	if (rtf_p('edit_officernote'))
		$rta_htm.= "<input type=\"checkbox\" name=\"formdata[conote]\" value=\"move\" />"
		. 'Replace officer note with: <input type="text" name="formdata[onote]" />'.br();
	$rta_htm.= '<input class="btn" type="submit" name="edit" value="Submit" />'.br();
	$rta_htm="<h2>Table options</h2>\n$rta_htm</form>\n";
	return $rta_htm;

}
function rtf_listmain($raid, $override) {
	global $rtg_roles;    // 1.90, roles
	//----------- get permissions to show various lists.
	$rta_list_per[1]=rtf_p('show_raidlist');
	$rta_list_per[2]=rtf_p('show_available');
	$rta_list_per[3]=rtf_p('show_reserve');
	$rta_list_per[4]=rtf_p('show_withdraw');
	$rta_list_per[5]=rtf_p('show_remove');
	$rta_list_per[6]=rtf_p('show_whiteboard');
	$rta_wb=$rta_list_per[6];  //--shortcut for rta_list_per[6]
	$rta_edit=rtf_p('subscription_alter');  //???
	$rta_maxraiders=rtd_selcol(rts_db_list,'required',"id=$raid");  //gets required players for raid
	for ($i=1; $i<7; $i++) {
		if (($i==2||$i==3) && !$rta_wb)  // if not have wb rights and either avail/reserve
			$rta_list[$i]=rtf_statuslist($raid, array ($i, $i+4)); // adds the wb counterpart
		elseif ($i==6 && $rta_wb)        // if has wb rights and looking for wb
			$rta_list[$i]=rtf_statuslist($raid, array ($i, $i+1));  //adds wb/avail and wb/reserve
		else                             // normal list
			$rta_list[$i]=rtf_statuslist($raid, $i);
		$rta_count[$i]=count($rta_list[$i]);  //keeps total count here
	}
	//----------------------- format column heads
	$rta_list_head[1]=rtf_statusicon(1,0,0)."(".$rta_count[1]."/$rta_maxraiders)";
	$rta_list_head[2]=rtf_statusicon(2,0,0)." - ".$rta_count[2];
	$rta_list_head[3]=rtf_statusicon(3,0,0)." - ".$rta_count[3];
	$rta_list_head[4]=rtf_statusicon(4,0,0)." - ".$rta_count[4];
	$rta_list_head[5]=rtf_statusicon(5,0,0)." - ".$rta_count[5];
	$rta_list_head[6]='Whiteboard '."(".$rta_count[6]."/$rta_maxraiders)";
	//----------------------- format column characters (main part of table)
	for ($i=1;$i<7;$i++) { // each permission
		if ($rta_list_per[$i] && $rta_count[$i]) { //continue only if permission + people present
			unset ($rta_rolelist);  //clears role lists
			unset ($rta_rolect);    //clears role counts
			$rta_role=0; //resets role
			$rta_list_main[$i]=''; //initializes column data
			if ($i>1 && $i<6) $rta_flag=true; else $rta_flag=false; // 2-5 need own indicator due to combining columns
			foreach ($rta_list[$i] as $v) {  //count and separete players into role lists.
				$rta_rolelist[$v['role']].=div(rtf_showchar($v['id'],$override,$rta_flag,0));
				$rta_rolect[$v['role']]++;
			}
			for ($j=1; $j<5; $j++) { // compiling role lists into 1 and adding counts.
				$rta_list_temp='';
				if ($rta_rolect[$j]>0){
					$rta_list_temp.=rtf_role($j).' ('.$rta_rolect[$j];
					//---- 1.90 max roles only for raid list/whiteboard \/
					if (($i==1 || $i==6) && $rtg_roles[$j-1]>0 ) $rta_list_temp.="/".$rtg_roles[$j-1];
					$rta_list_main[$i].=div($rta_list_temp.')','','role').NL.$rta_rolelist[$j];
				}

			}
		}
	}
	//----------------- combine 2 lists together
	if ($rta_list_main[3]) {
		if ($rta_list_main[2]){
			$rta_list_main[2].="<hr />\n";
			$rta_list_head[2].=' '.$rta_list_head[3];
		} else $rta_list_head[2]=$rta_list_head[3];
		$rta_list_main[2].=$rta_list_main[3];
	}
	if ($rta_list_main[5]) {
		if ($rta_list_main[4]){
			$rta_list_main[4].="<hr />\n";
			$rta_list_head[4].=' '.$rta_list_head[5];
		} else $rta_list_head[4]=$rta_list_head[5];
		$rta_list_main[4].=$rta_list_main[5];
	}
	$rta_order=array(1,6,2,4);  //-- order of the cells
	//---------------- initialize cells
	$rta_th='';
	$rta_td='';
	foreach ($rta_order as $v) {
		if ($rta_list_main[$v]) {
			addcell($rta_th,$rta_list_head[$v],'th');
			addcell($rta_td,$rta_list_main[$v]);
		}
	}
	$rta_main='<form method="post" action=".">'."\n<div>\n"
	. input("formview","hidden","manage")
	. "<input type=\"hidden\" name=\"raidid\" value=\"$raid\" />\n"
	. "<h1>Lists</h1>\n";
	if (!$rta_th) {
		$rta_main.= "No lists available.  This can be a new raid with no signups, "
		."or there may be lists you don't have permissions to see.".br();
	} else {
		addrow($rta_tbl, NL.$rta_th.NL);
		addrow($rta_tbl, NL.$rta_td.NL);
		$rta_tbl=tbl(NL.$rta_tbl.NL);
		$rta_main.= $rta_tbl;
	}
	if (rtf_p('subscription_alter') && !$override) $rta_options=rtf_tableopt($rta_count[6]);
	$rta_main.= $rta_options
	. "</div>\n</form>\n";
	return $rta_main;
}
?>