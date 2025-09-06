<?php
if (!rtf_pcheck('update_news')) return;
$rta_newshead=rtd_selcol(rts_db_keys,'name',"category='news_head'");
$rta_news=rtd_selcol(rts_db_keys,'name',"category='news'");
$rta_echo="<h1>Breaking news</h1>\n<p>To add or change the news, please fill in both fields."
 . "To erase, click on the Erase button.</p>\n";
$rta_form=textfield('formdata[newshead]',"News heading:",$rta_newshead,'line','60');
$rta_form.=textfield('formdata[news]',"News:",$rta_news,"box",array('x'=>50,'y'=>6))
. button('formdata[modify]','Submit').button('formdata[erase]','Erase')
. input('view','hidden','admin')
. input('formview','hidden','news');
$rta_form=form(NL.$rta_form,'.','post','','trueform');
$rts_internal['main'].=$rta_echo.NL.$rta_form.NL;
?>