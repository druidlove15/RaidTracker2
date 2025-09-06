<?php
/*******************************************************************************
 * news.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Update/delete news here
 ******************************************************************************/
$rta_msg='';
if ($rtv_formdata['modify']){  //submit news button
	$sql[1]="UPDATE ".rts_db_keys." SET `name`='$rtv_formdata[news]' WHERE category='news'";
	$sql[2]="UPDATE ".rts_db_keys." SET `name`='$rtv_formdata[newshead]' WHERE category='news_head'";
	$sql[3]="UPDATE ".rts_db_keys." SET `name`='".rtc_acct()."' WHERE category='news_author'";
	$sql[4]="UPDATE ".rts_db_keys." SET `name`='".rts_currtime."' WHERE category='news_time'";
	$rta_msg="Modified Breaking news";
} else if ($rtv_formdata['erase']){ // erase news button
	$sql[1]="UPDATE ".rts_db_keys." SET `name`=NULL WHERE category='news'";
	$sql[2]="UPDATE ".rts_db_keys." SET `name`=NULL WHERE category='news_head'";
	$sql[3]="UPDATE ".rts_db_keys." SET `name`='".rtc_acct()."' WHERE category='news_author'";
	$sql[4]="UPDATE ".rts_db_keys." SET `name`='".rts_currtime."' WHERE category='news_time'";
	$rta_msg="Erased Breaking news";
}
if ($rta_msg) {
	for ($i=1;$i<5;$i++)
	mysql_query($sql[$i]);
//	mysql_query ($sql1);
//	mysql_query ($sql2);
	rtf_log("news",$rta_msg);  //log the news change/delete
	$rts_internal['message'].="Breaking news updated".br();
}
