<?php
$_page['logo']=rts_syspath.rtw_style."/logo.png";
// **************** top of the page
echo "<div id=\"topside\">\n";
echo "<div id=\"logo\"><img src=\"$_page[logo]\" alt=\"RaidTracker2\" /></div>\n";
echo "</div>\n";
// **************** left of the page (menu, help, etc)
echo "<div id=\"leftside\">\n";
echo rtw_menu;
if (defined('rtw_message')) echo "<div>\n<h1>Message</h1>\n".rtw_message."\n</div>\n";
if(defined ('rtw_help')) echo rtw_help;
echo "</div>\n";
// **************** main part of page
echo "<div id=\"main\">\n";
if (defined ('rtw_news')) echo rtw_news;
if(defined ('rtw_time')) echo rtw_time;
echo rtw_main;
echo "\n</div>\n";
?>