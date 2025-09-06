<?php
/******************************************************************************
 * rt-icons.php
 ******************************************************************************
 * Version 2.20 last update: 2009-09-20
 * ----------------------------------------------------------------------------
 * File to get and publish icons and statuses (class, role, etc)
 *****************************************************************************/
$rtg_status=array(
	-1=>"Not logged in",
	0=>"Not subscribed",
	1=>"Raid list",
	2=>"Subscribed",
	3=>"Reserve",
	4=>"Withdrawn",
	5=>"Removed",
	6=>"Whiteboard",
	7=>"Whiteboard",
	8=>"Delete");
$rtg_status_special=array(6=>2, 7=>3); //redirect wb specials
$rtg_role=array(
	1=>"Tank",
	2=>"Healer",
	3=>"Melee DPS",
	4=>"Range DPS");
if (!$rtg_class) {
	$rta_get_class=rtd_select (rts_db_keys,array('name','value'), '`category`="class"');
	foreach ($rta_get_class as $v) {
		$rta_abbv=$v['name'];
		$rta_fullname=trim($v['value']);
		$rtg_class[$rta_abbv]=$rta_fullname;
		}
}
$rtg_icons['status']=$rtg_status;
$rtg_icons['role']=$rtg_role;
$rtg_icons['class']=$rtg_class;

/******************************************************************************
 * rtf_show_icons
 * ----------------------------------------------------------------------------
 * Takes in a type, the value, and optional format and returns the text/img tag
 * type:  'status'/'role'/'class'
 * Format: 0- icon only (default)
 *         1- icon/long name
 *         2- long name only
 *         3- abbreviation (recommended for class only)
 *****************************************************************************/
function rtf_show_icons ($type, $value, $format=0) {
global $rtg_icons, $rtg_status_special;
	if ($type=='status' && $value>5)  //figure if permissions are set for WB:
		if (!rtf_p('show_whiteboard'))     // no permissions, substitue below
			$type=$rtg_status_special[$type];
	if ($format==3) return $value;
	$rta_ret='';  //blank return value
	if ($format<2) //icons here
		$rta_ret="<img src=\""._RT_SYS_WEB."images/$type/$value.png\" alt=\""
		. $rtg_icons[$type][$value] ."\" />";
	if ($format) $rta_ret.= $rtg_icons[$type][$value]; //text here
	return $rta_ret;
}
/******************************************************************************
 * rtf_formcontrol
 * ----------------------------------------------------------------------------
 * Minimal function to return a dropdown box of $type
 * $type: one of status / class / role
 * $name: name of control
 * $label: label name if any (required, can be null)
 * $defaults: true/false for a no-change value. default false
 * $defval: default value if any
 * $id, $class, $other: for control values
 *****************************************************************************/
function rtf_formcontrol($type, $name, $label, $defaults=false, $defval='', $id='', $class='', $other='') {
	global $rtg_icons;
	if ($defaults) 
		$rta_arr[0]='Use default';
	$rta_test=$rtg_icons[$type]; //foreach only works on 1d arrays
	foreach ($rta_test as $k=>$v) { //goes through $rta_test
		if ($type!='status') $rta_arr[$k]=$v;
		else {
			switch ($k) {
				case 1: $chk='list_raidlist'; break;
				case 2: $chk='list_available'; break;
				case 3: $chk='list_reserve'; break;
				case 4: $chk='list_withdraw'; break;
				case 5: $chk='list_remove'; break;
				case 6: $chk='list_wb';
						$k=8;
						break;
				case 8: $chk="list_delete";
						$k=-1;
						break;
				default: $chk=false;
						break;
			}
			if (!($chk===false) && rtf_p($chk)) $rta_arr[$k]=$v;
		}
	}
	return select($name, $label, $rta_arr, $defval, $id, $class, $class, $other);
}
/******************************************************************************
 * rtf_icon_character
 * ----------------------------------------------------------------------------
 * Shows the character name in the format required
 * charid = ID of character
 * format: can include the following in your order (default 'i n')
 *         i-icon
 *         n-name
 *         s-short "Dr"
 *         S-short in paren "(Dr)"
 *         c-class name "Druid"
 *         C-class name in paren "(Druid)"
 *         /-next char is a literal
 *****************************************************************************/
function rtf_icon_character($charid, $format='i n') {
	$rta_charrec=rtd_select(rts_db_char,'*',"`id`='$charid'");
	if (!$rta_charrec) die ("Character does not exist: id $charid");
	$rta_str='';
	for ($i=0;$i< strlen($format); $i++) {
		$rta_c=$format[$i];
		switch ($rta_c) {
			case 'i': $rta_callval=0; break;
			case 'n': $rta_str.=$rta_charrec['char']; break;
			case 's':
			case 'S': $rta_callval=3; break;
			case 'c':
			case 'C': $rta_callval=2; break;
			case ' ': $rta_str.='&nbsp;'; break;
			case '/': if ($i!= strlen($format)-1) 
							$rta_str.=$format[++$i]; break;
			default: $rta_str.=$rta_c;
		}
		if (isset($rta_callval)) {
			$rta_hold=rtf_show_icons ('class',$rta_charrec['class'],$rta_callval);
			if ($rta_c==strtoupper($rta_c)) $rta_str.="($rta_hold)";
			else $rta_str.=$rta_hold;
			unset ($rta_callval);
		}
	}
	return $rta_str;
}
?>