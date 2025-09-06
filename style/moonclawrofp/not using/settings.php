<?php
$path=rts_syspath."/style/$rta_style";
$_page['stylesheet']="$path/style";  //stylesheet without .css ending

$_page['logo']="$path/logo.png";

include 'head.php';
include 'top.php';
include 'main.php';
include 'left.php';
include 'bottom.php';
?>