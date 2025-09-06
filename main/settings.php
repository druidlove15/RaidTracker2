<?php
if (!defined(rts_syspath))
	define (rts_syspath, ".");
$path=rts_syspath."/style/default";
$_page['stylesheet']="$path/style";  //do not include .css ending
$_page['logo']="$path/logo.png";

include 'top.php';
include 'left.php';
include 'main.php';
include 'bottom.php';
?>