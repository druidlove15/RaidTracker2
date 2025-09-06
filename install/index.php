<?php
if (!$rtv_step) $rtv_step=1; // Install run : set up 1st step
$rta_style="default";
include "step$rtv_step.php";
include "rti-installmenu.php";
$_page['title']="RaidTracker: Install step ".($rtv_step);
?>