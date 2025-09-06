<?php
/*******************************************************************************
 * admin.php
 * -----------------------------------------------------------------------------
 * Version 2.x admin menu
 ******************************************************************************/
//test
if (rtf_p('admin_view')){
	if (rtf_p('update_news')) include "admin/news.php";
	if (rtf_p('show_settings')) include "admin/settings.php";
} else {
	$rts_internal['main']="<h1>Error</h1>\n<p>You do not have priviledges to view
	the administration screen.</p>\n";
}
?>