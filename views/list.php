<?php 
/*******************************************************************************
 *  lists.php
 *******************************************************************************
 * Version 2.0.
 * List view generator.
 * Shows the list view for a raid
 ******************************************************************************/

//---------- include raid heading
include "list/rt-raidhead.php";

//----------- include self status
if (!$rta_override) {
	include "list/rt-status.php";
	$t=rtf_selfstat($rtv_raidid);
	$rts_internal['main'].="<!-- ********************* rt-status.php ******************  -->\n";
	$rts_internal['main'].=$t;
}

//----------- include tables
if (rtf_p('show_tables')) {
	include "list/rti-lists.php";
	$t=rtf_listmain($rtv_raidid, $rta_override);
	$rts_internal['main'].="<!-- ********************* rti-lists.php ******************  -->\n";
	$rts_internal['main'].=$t;
}
?>
 