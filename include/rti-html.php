<?php
define ('NL',"\n");
function publish($t) {echo $t;}
function bold($t) {return "<strong>$t</strong>";}
function ital($t) {return "<em>$t</em>";}
function tbl($data, $class="", $id="") {return group($data, 'table',$id,$class);}
function td($data,$head=false,$id='',$class='',$other='') {return group($data,($head?'th':'td'),$id,$class,$other);}
function addcell(&$row, $data, $type="td", $class="", $id="") {
	$row.=group($data,$type,$id, $class);
}
function addrow(&$tbl, $row, $class="", $id="") {
	$tbl.=group($row,'tr',$id,$class);
}
function url($url, $text, $query="", $id="", $class="", $title="") {
	if (is_array($query)) {
		$i=0;
		$t="";
		foreach ($query as $k=>$v) {
			if ($i++) $t.="&amp;";
			$t.="$k=$v";
		}
		$query=$t;
	}
	if ($query) $url.="?$query";
	return group($text,'a',$id,$class,"href=\"$url\"");
}
function br() {return "<br />".NL;}
function nl(){ return NL;}
function group($t, $elem="div", $id="", $class="", $other="") {
	$tag="<$elem";
	if (!empty($id)) $tag.=" id=\"$id\"";
	if (!empty($class)) $tag.=" class=\"$class\"";
	if (!empty($other))  $tag.=" $other";
	return $tag.">$t</$elem>";
}
function div($text, $id='', $class='', $other='') {return group($text, 'div',$id, $class, $other);}
function span($text, $id='', $class='', $other='') {return group($text, 'span',$id, $class, $other);}
function form($body, $action="#", $method="post", $id="", $class="", $other="") {
	return group ($body, "form", $id, $class, "method=\"$method\" action=\"$action\" $other");
}
function input($name, $type="text", $value="", $other="") {
	return "<input type=\"$type\" name=\"$name\" value=\"$value\" $other />\n";
}
function sel($name, $array, $default="", $id="", $class="", $other=""){
	$t="<select name=\"$name\" id=\"$id\" class=\"class\" $other>\n";
	foreach ($array as $k=>$v) {
		$t.="  <option value=\"$k\"";
		if ($k==$default) $t.= " selected=\"selected\"";
		$t.=">$v</option>\n";
	}
	$t.="</select>\n";
	return $t;
}
function select($name, $label, $array, $default='', $id='', $class='', $other='') {
	$rta_ret=group($label,'label','','heading',"for=\"$name\"").' ';
	$rta_ret.=sel($name, $array, $default, $id, $class, $other);
	return $rta_ret;
}

function err($text, $id="") {return group("Error $text", 'div', $id, "error");}
function HTMLstrip($t) {
	return preg_replace('/(<)(.+?)(>)/', '', $t);
}
function button($nam='submit', $value='Submit', $class='', $id='') {
	return "<input id=\"$id\" class=\"$class\" type=\"submit\" name=\"$nam\" value=\"$value\" />";
}
function textfield($rtv_name, $rtv_label='', $rtv_value='', $rtv_style='line',$rtv_dim='20', $rtv_other='') {
	//for field box here
	$rta_ret=group($rtv_label,'label','','heading',"for=\"$rtv_name\"").' ';
	if ($rtv_style=='line') $rta_ret.=input ($rtv_name,'text',$rtv_value,"size=\"$rtv_dim\" id=\"$rtv_name\" $rtv_other");
	else if ($rtv_style=='box') $rta_ret.=group($rtv_value,'textarea','$rtv_name','',"name=\"$rtv_name\" rows=\"$rtv_dim[y]\" cols=\"$rtv_dim[x]\" id=\"$rtv_name\" ".$rtv_other);
	else rtf_error (101,"textfield / $rtv_style");
	$rta_ret.=br();
	return $rta_ret;
}
function passfield($rtv_name, $rtv_label='', $rtv_value='', $rtv_dim='20', $rtv_other='') {
	$rta_ret=group($rtv_label,'label','','heading',"for=\"$rtv_name\"").' ';
	$rta_ret.=input ($rtv_name,'password',$rtv_value,"size=\"$rtv_dim\" id=\"$rtv_name\" $rtv_other");
	$rta_ret.=br();
	return $rta_ret;
}
// ----------- wrapper functions for the above 
function inputtext($rtv_name, $rtv_label, $rtv_value='', $rtv_length='20', $rtv_other='') {
	return textfield($rtv_name, $rtv_label, $rtv_value, 'line', $rtv_length, $rtv_other);
}
function inputpass($rtv_name, $rtv_label, $rtv_length='20', $rtv_other='') {
	return passfield($rtv_name, $rtv_label, '', 'line', $rtv_length, $rtv_other);
}
function textbox($rtv_name, $rtv_label, $rtv_length='20', $rtv_height='4', $rtv_value='', $rtv_other='') {
	return textfield($rtv_name, $rtv_label, $rtv_value, 'box', array('x'=>$rtv_length, 'y'=>$rtv_height), $rtv_other);
}
function inputhidden($rtv_name, $rtv_value, $rtv_other='') {
	return input($rtv_name, 'hidden',$rtv_value, $rtv_other);
}

?>