<?php
/*******************************************************************************
 * swap.php
 * -----------------------------------------------------------------------------
 * Version 2.00    Swap characters roles here
 ******************************************************************************/
//---
$rtv_view="list";
if ($rtv_formdata['cancel']) return; //cancel was pressed
//var_dump ($_POST);
//exit;
for ($i=0; $i<count($rtv_rec);$i++) {
	if ($rtv_altchar[$i] || $rtv_altrole[$i]) {  //char or role set
		$rta_rec=rtd_select (rts_db_sign,'*', "id=$rtv_rec[$i]"); //get old records
		if ($rtv_altrole[$i]) {  //role selected, change that;
			$rta_sql['role']=$rtv_altrole[$i];
			$rta_newchg="role to [role=".$rtv_altrole[$i].']';
		}
		if ($rtv_altchar[$i]) {  //character selected
			$rta_sql['char']=$rtv_altchar[$i];
			if (!$rta_sql['role']) { //no role, look up default
				$rta_charinfo=rtf_playermatrix('charid',$rta_sql['char']);
				$rta_sql['role']=$rta_charinfo[0]['role'];
				$rta_newchg='';
			} else $rta_newchg.="and $rta_newchg";
			$rta_logsql.=" [pchar]".$rta_rec['char']."[/pchar] to [pchar]".$rta_sql['role']."[/pchar]";
		} else 
			$rta_newchg="[pchar]".$rta_rec['char']."[/pchar]'s $rta_newchg";
		rtd_update(rts_db_sign,$rta_sql,"id=".$rtv_rec[$i]);
		if ($rta_logsql) $rta_logsql.=",";
		$rta_logsql.=" $rta_newchg.";
		unset ($rta_sql);
		unset ($rta_newchg);
	}
}
$rta_logsql="Swapped $rta_logsql";
rtf_log("signup $rtv_raidid",$rta_logsql);
$rts_internal['message'].="Swaps completed.".br();
?>