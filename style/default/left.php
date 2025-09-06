<?php
$content="Left side";
echo "<div id=\"leftside\">\n";
echo "$_page[menu]\n";
if ($_page['message'])
	echo "<div>\n<h1>Message</h1>\n$_page[message]\n</div>\n";
if ($incside) {
	echo "<div class=\"otherbox\">";
	include $incside;
	echo "</div>\n";
}
echo "</div>\n";
