<?php
/***************************************************
 *  commondb.php
 ***************************************************
 * RT functions for database features.
 * All db functions start with rtd_
 ***************************************************/
//----------------------  opens DB here
function rtd_openDB($server, $user, $pass, $db) {
static $dbconn='';
if (!$dbconn) $dbconn=mysql_connect($server,$user,$pass) or rtf_error(121);
mysql_select_db($db);
}
//----------------------  Processes the query
function rtd_query($query){
	mysql_free_result;
	return mysql_query($query);
}
//----------------------  Formats a SET statement
function rtd_set($arr) {
$t="SET ";
$i=0;
foreach ($arr as $k=>$v){
	if ($i++) $t.=", ";
	if ($v==='null') $t.="`$k`=NULL";
	else $t.="`$k`='$v'";
}
return $t;
}
//----------------------  Process a INSERT statement
function rtd_insert($table, $arr) {
$sql="INSERT INTO $table ".rtd_set($arr);
$r=rtd_query($sql);
if ($r) return true;
rtf_error(20,$sql);
return false;
}
//----------------------  Process a UPDATE statement
function rtd_update($table, $arr, $cond='') {
$sql="UPDATE $table ".rtd_set($arr);
if ($cond) $sql.=" WHERE $cond";
$r=rtd_query($sql);
if ($r) return true;
rtf_error(20,$sql);
return false;
}
//----------------------  Process a SELECT statement
function rtd_select ($table, $field="*", $cond='', $other='', $expand=0) {
if (is_array($field)) {
	$i=0;
	$temp='';
	foreach ($field as $v) {
		if ($i++) $temp.=", ";
		if (strpos($v, '`')===false || strpos($v,'.')===false) $temp.="$v";
		else $temp.="`$v`";
	}
	$field=$temp;
} //else if ($field !='*' && !strpos(',',$field)) $field ="`$field`";
if (is_array($table)) $table=implode(", ",$table);
$sql="SELECT $field FROM $table";
if ($cond) $sql.=" WHERE $cond";
if ($other) $sql.=" $other";
//echo "$sql".br();
$result=rtd_query($sql) or rtf_error (120, $sql);
$sqlresult=null;
$i=0;
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$j=0;
	foreach ($line as $col_value) {
		$sqlresult[$i][mysql_field_name($result,$j)]=$col_value;
		$j++;
	}
	$i++;
}
if (count($sqlresult)==1 && !$expand) $sqlresult=$sqlresult[0];
return $sqlresult;
}
//----------------------  Process a SELECT statement on one field
function rtd_selcol($table, $field, $cond='', $other='') {
if (is_array($field)) rtf_error(101,"rtd_selcol".implode(", ", $field));
$result=rtd_select($table, "`$field`", $cond, $other);
if (count($result)==1) return $result[$field];
$t='';
for ($i=0; $i<count($result);$i++)
	$t[$i]=$result[$i][$field];
return $t;
}
function rtd_selcol2($table, $field, $cond='', $other='') {
if (is_array($field)) rtf_error(101,"rtd_selcol".implode(", ", $field));
$result=rtd_select($table, "$field", $cond, $other);
if (count($result)==1) return $result[$field];
$t='';
for ($i=0; $i<count($result);$i++)
	$t[$i]=$result[$i][$field];
return $t;
}
//----------------------  Process a INSERT statement returning e.g. ID field value
function rtd_insrec($table, $arr, $search, $ret) {
$i=0;
foreach ($search as $v) {
	if ($i++) $sqlwhere.=" AND ";
	$sqlwhere.="`$v`='$arr[$v]'";
}
$t=rtd_insert($table, $arr);
$t=rtd_selcol($table, $ret, $sqlwhere, "LIMIT 1");
return $t;
}
?>