<?php
// --------------------------------------------------------------- set up menu
// step 1
$rta_mstep[1]="Welcome";
// step 2
if ($rtv_install=='upgrade')
	$rta_mstep[]="Verify account";      // if upgrade, verify account
else                                    // if not, set up DB
	$rta_mstep[]="Server/DB settings";
// step 3
$rta_mstep[]='Set up tables';           // set up or update tables
// step 4
if ($rtv_install=='install')
	$rta_mstep[]='Create account';      // if install, creates a new account
else                                    // if not, verify files
	$rta_mstep[]='Verifying files';

$rta_mstep[]='RaidTracker Settings';
$rta_mstep[]='Create Terms of Service';
// final step
$rta_mstep[]='Finish Install';
//---------------------------------------------------------------- display menu
$rts_internal['help']="<h1>Installation</h1>\n";
for ($i=1; $i<=count($rta_mstep); $i++) {
	if ($i==$rtv_step)
		$rts_internal['help'].="<strong>".$rta_mstep[$i]."</strong>";
	else $rts_internal['help'].=$rta_mstep[$i];
	$rts_internal['help'].="<br />\n";;
}

?>