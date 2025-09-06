<?php
/*******************************************************************************
 * swap.php
 * -----------------------------------------------------------------------------
 * Version 2.00   Swaps out a character/role
 ******************************************************************************/
// --- test
$rta_tbl='';  //sets up table to nothing
$rta_row=td("Current character",true).NL
.td("New character",true).NL.td("New Role",true).NL;
addrow($rta_tbl,$rta_row);  //sets up header row
for ($i=0; $i<count($rtv_rec); $i++) {  //generates loop of records to change
	$rta_row='';  //blanks out current row
	$rta_record=rtd_select(rts_db_sign,'*',"id=$rtv_rec[$i]");//gets each player's signup record
	$rta_playlist=rtf_playermatrix('acctid',$rta_record['charid']);
	// sets up first column with character and a hidden link to the raid signup id
	$rta_row=td(rtf_character($rta_record['char'],0,2,0).	input("rec[$i]","hidden",$rtv_rec[$i])).NL;
	if (count($rta_playlist)<2) {
		$rta_row.=td(input("altchar[$i]",'hidden','0')."No other characters").NL;
	} else {
		$rta_class="<select name=\"altchar[$i]\">\n";
		foreach ($rta_playlist as $v){
			if ($v['charid']!=$rta_record['char'])
				$rta_class.="<option value=\"{$v['charid']}\">{$v['charname']} ({$v['class']})</option>\n";
			else
				$rta_class.="<option value=\"0\" selected=\"selected\" >{$v['charname']} ({$v['class']})</option>\n";
		}
		$rta_class.="</select>\n";
		$rta_row.=td($rta_class).NL;
	}
	$rta_row.=td(rtff_role("altrole[{$i}]",0,0)).NL;
	addrow($rta_tbl, $rta_row);
}
$rta_tbl=tbl($rta_tbl);
$rta_tbl.=button("formdata[submit]","Submit").' '.button("formdata[cancel]","Cancel");
$rta_tbl.=input('formview','hidden','swap');
$rta_tbl.=input('raidid','hidden',$rtv_raidid);
$rta_tbl=form($rta_tbl,'.');
$rts_internal['main']="<h1>Swap characters/roles</h1>\n<p>Swap chosen characters and/or roles here.</p>\n".$rta_tbl;
$rta_override=1;
include("list/rti-lists.php");
$t=rtf_listmain($rtv_raidid, $rta_override);
$rts_internal['main'].=$t;
?>