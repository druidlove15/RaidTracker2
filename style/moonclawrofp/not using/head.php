<?php
function headers($_head_parm) {
global $_page, $rts_syspath;
$tl="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
<title>$_page[title] | $_page[guild] - $_page[realm]</title>
<meta http-equiv=\"content-type\" content=\"text/html;charset=iso-8859-1\" />
<meta http-equiv=\"expires\" content=\"Thu, 16 Mar 2000 11:00:00 GMT\" />
<meta http-equiv=\"pragma\" content=\"no-cache\" />
<meta http-equiv=\"cache-control\" content=\"no-cache\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"".rts_syspath."/include/raidtracker.css\" media=\"all\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"$_page[stylesheet].css\" media=\"all\" />
<!--[if IE 6]>
<link rel=\"stylesheet\" type=\"text/css\" href=\"$_page[stylesheet]-ie6.css\" media=\"all\" />
<![endif]-->
<!--[if IE 7]>
<link rel=\"stylesheet\" type=\"text/css\" href=\"$_page[stylesheet]-ie7.css\" media=\"all\" />
<![endif]-->\n";

if (is_array($_head_parm))$_head_extra=implode("\n",$_head_parm); else $_head_extra=$_head_parm;
$tl.=$_head_extra."\n</head>\n<body>\n";
return $tl;
}
function cookietest($type, $name, $value, $expire='', $path='/') {
	if ($type=='s') {
		if (!$expire) $expire=time()+3600;
		setcookie($name, $value, $expire, $path);
	}
}
if (!isset($cookie_send)) {
	$cookie_send="";
	$cookie_name="";
	$cookie_value="";
}
cookietest($cookie_send, $cookie_name, $cookie_value, $cookie_expire, $cookie_path);

echo headers ($_head_parm);
?>