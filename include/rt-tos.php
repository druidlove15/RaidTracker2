<?php
/******************************************
 * rt-tos.php
 *-----------------------------------------
 * Shows the TOS page if needed.
 ******************************************/
$rta_tos=true; //sets this true

if ($rtg_page=='create_acct' && $rtv_tos) return;  //new account agreed to TOS
if (!rtc_acct() && $rtg_page!='create_acct') return;  //exits if not logged in.
if (rts_TOS && rtc_tos()==rts_tosv) return;  //has accepted, return
if ($rtv_tos) {  //has just accepted TOS, return  -- something new here.
	$rtc_session['tos']=rts_tosv;
	$v=$rtc_session['settings'];
	$v[1]=rts_tosv;
	$rtc_session['settings']=$v;
	rtd_update(rts_db_acct,array('settings'=>$rtc_session['settings']),"id=".rtc_acct());
	return;
}
$rta_tos=false;
$rta_view='t';
$incmain='rt-tosshow.php';
?>