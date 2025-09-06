<?php
/******************************************
 * rt-tos.php
 *-----------------------------------------
 * Shows the TOS page if needed.
 ******************************************/
$rta_tos=true;          //sets this true
if (rts_TOS=='false') return;   //return if not required
if ($rtg_page=='create_acct' && $rtv_tos) return;  //new account agreed to TOS
if (!rtc_acct() && $rtg_page!='create_acct') return;  //exits if not logged in.
if (rts_TOS && rtc_tos()==rts_tosv) return;  //has accepted, return


if ($rtv_tos) return;     //has just accepted TOS, return  -- something new here.
$rta_tos=false;
$rta_view='t';
$incmain='rt-tosshow.php';
?>