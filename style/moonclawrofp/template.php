<?php
$_page['logo']=rtw_style."/logo2.png";
$_page['logo2']=rtw_style."/logo.png";

// **************** top of the page
echo "<div id=\"wrapcentre\">\n";
echo "<div id=\"topside\">\n";
echo "<div id=\"logo\"><img src=\"$_page[logo]\" alt=\"RaidTracker2\" /></div>\n";
echo "<img src=\"$_page[logo2]\" alt=\"".rts_guild ."\" />\n";
echo "<div id=\"menubar\"><a href=\"http://forum.rofp.org/viewforum.php?f=4\"><img src=\"".rtw_style."/about.png\" alt=\"About\" /></a>
<a href=\"http://forum.rofp.org/\"><img src=\"".rtw_style."/forum.png\" alt=\"Forum\" /></a>
<a href=\"http://dkp.rofp.org\"><img src=\"".rtw_style."/dkp.png\" alt=\"DKP\" /></a>
<a href=\"http://raid.rofp.org\"><img src=\"".rtw_style."/signup.png\" alt=\"RaidTracker\" /></a>
<a href=\"http://forum.rofp.org/viewforum.php?f=5\"><img src=\"".rtw_style."/apply.png\" alt=\"Apply\" /></a>
</div>";
echo "</div>\n";

echo "<div id=\"rtmenu\">".rtw_menu."</div>\n<div class=\"clear\">&nbsp;</div>";
if (defined ('rtw_time')) echo rtw_time;
if (defined('rtw_message')) echo "<div>\n<h1 id=\"message\">Message</h1>\n".rtw_message."\n</div>\n";
if (defined ('rtw_news')) echo rtw_news;
if (defined ('rtw_help')) echo "<div id=\"helpbox\">".rtw_help."</div>\n";
echo "<div id=\"main\">\n";
echo rtw_main;
echo "\n</div>\n";
echo "<div class=\"clear\">&nbsp;</div>
<div id=\"bottom\">Style: MoonclawRofp version 0.1, based off of WoWMoonclaw by Maevah.  Modified for RaidTracker2</div>\n";
echo "</div>\n";
?>