<?php
/*******************************************************************************
 * rofpaddnews.php
 * -----------------------------------------------------------------------------
 * Version 2.00  Addon for adding recruitment to rofp's home page
 ******************************************************************************/
//-- function required before use
function implode_with_keys($glue, $array) {
        $output = array();
        foreach( $array as $key => $item )
                $output[] = "\t'$key'" . "=>" . '"'.str_replace ( "\"", "&quot;", $item ).'"';

        return implode($glue, $output);
}
//-- check for permissions
$rtv_view='admin';
include "../user/news.php";
if (!($rta_file=fopen("../user/news.php","w"))) {  //open rt.php for writing
	$rts_internal['message'].="Settings cannot be saved.  File is protected.".br();
	return;
}
for ($i=$rofp_article;$i>0;$i--)
	$rofp_nnews[$i]=$rofp_news[$i-1];
$rofp_nnews[0]=array(
	'headline'=>$rtv_heading,
	'pic'=>	$rtv_pic,
	'picalt'=>$rtv_picalt,
	'article'=>$rtv_article,
	'author'=>"(".rtc_acct().")".rtc_name(),
	'createtime'=>date("Y-m-d H:i",rts_currtime),
	'category'=>$rtv_cat
);
$rofp_article++;

	fwrite ($rta_file, "<?php\n");
	fwrite ($rta_file, "\$rofp_article=$rofp_article"."; //number of articles\n");
	fwrite ($rta_file, "\$rofp_newsedit=$rofp_newsedit"."; //1-all edit, 0-no edit, -1-perm decides\n");

	$i=0;
	for ($i=0; $i<$rofp_article;$i++) {
		fwrite ($rta_file,'$rofp_news['.$i."]=array(\n");
		$rofp_testw=$rofp_nnews[$i];
		fwrite ($rta_file,implode_with_keys(",\n",$rofp_testw));
		fwrite ($rta_file,");\n");
	}
	fwrite ($rta_file, "?>\n");
	fclose ($rta_file);
	$rts_internal['message'].="Settings saved.  Settings go in effect immediately.".br();
?>