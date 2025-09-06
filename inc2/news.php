<?php
function rtf_newshead($rta_view='', $rta_alt='') {
global $rta_alt;
$rta_ret='';
$rta_currdate=rtf_datetime(rts_currtime,'dt');
$rta_newshead=rtd_selcol(rts_db_keys,'name',"category='news_head'");
$rta_news=rtd_selcol(rts_db_keys,'name',"category='news'");
$rta_author=rtf_acct2char(rtd_selcol(rts_db_keys,'name',"category='news_author'"));
$rta_time=rtf_datetime(rtd_selcol(rts_db_keys,'name',"category='news_time'"),'dt');
if ($rta_newshead && $rta_news) {
	$rta_ret=group($rta_newshead,'h1').NL;
	$rta_ret.=div($rta_news);
	$rta_ret.=div("News brought to you by ".rtf_character($rta_author,0)." on $rta_time",'newsdetails');
	$rta_ret=div($rta_ret,'','rt news');
//	$rta_ret="<div class=\"rt news\">\n<h1>$rta_newshead</h1>\n$rta_news</div>\n";
}
$rta_ret.="<h1>$rta_view</h1>\n";
$rta_ret.="Current time is:  $rta_currdate".br();
if (!rts_override)
	if ($rta_view) $rta_ret.="You can also see our <a href=\".?view=$rta_alt\">alternate view</a>.  Set your default view in your settings.".br();
return $rta_ret;
}
if (rts_news==2 || (rts_news==1 && ($rtv_view=='main' || $rtv_view=='calendar' || $rtv_view=='history'))) {
$rta_ret='';
$rta_currdate=rtf_datetime(rts_currtime,'dt');
$rta_newshead=rtd_selcol(rts_db_keys,'name',"category='news_head'");
$rta_news=rtd_selcol(rts_db_keys,'name',"category='news'");
$rta_author=rtf_acct2char(rtd_selcol(rts_db_keys,'name',"category='news_author'"));
$rta_time=rtf_datetime(rtd_selcol(rts_db_keys,'name',"category='news_time'"),'dt');
if ($rta_newshead && $rta_news) {
	$rta_ret=group($rta_newshead,'h1').NL;
	$rta_ret.=div($rta_news);
	$rta_ret.=div("News brought to you by ".rtf_character($rta_author,0)." on $rta_time",'newsdetails');
	$rta_ret=div($rta_ret,'','rt news');
//	$rta_ret="<div class=\"rt news\">\n<h1>$rta_newshead</h1>\n$rta_news</div>\n";
}
$rta_ret.="<h1>$rtv_view</h1>\n"; // for ver 2.50.1
$rta_ret.="Current time is:  $rta_currdate".br();
if (!rts_override)
	if ($rta_view) $rta_ret.="You can also see our <a href=\".?view=$rta_alt\">alternate view</a>.  Set your default view in your settings.".br();
$rts_internal['news']=$rta_ret;
}	

//	$rts_internal['news']=rtf_newshead($rta_currview, $rta_altview);

?>
