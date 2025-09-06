<?php
/*******************************************************************************
 * news.php
 * -----------------------------------------------------------------------------
 * Version 2.00 extra module: To add news to portal
 ******************************************************************************/
//if (!rtf_p('rofp_news')) return;  // check for news privs
//if (rtf_p('rofp_newsadd')) {
	// ---------------------------------- add news here
	$rta_rofpnews="<h2>Add news article</h2>\n";
	$rta_rofpform=inputtext('heading','Headline','','50').
					  textbox('article','Content','40','4').
					  inputtext('pic','URL to picture','','50').
					  inputtext('picalt','Alt text for picture','','50').
					  select('cat','Category',array(
					          'guild'=>"Guild news",
					          'recruiting'=>"Recruitment",
					          'progress'=>"Raid Progress",
					          'Blizzard'=>"Blizzard/WoW General",
					          'other'=>"Other")
							  ).br();
	$rta_rofpform.=button();
	$rta_rofpform.=input('formview','hidden','rofpaddnews');	
	$rta_rofpform=form($rta_rofpform,'.','post','','trueform');
	$rta_rofpnews.=$rta_rofpform;
	$rts_internal['main'].=$rta_rofpnews;
// }

?>