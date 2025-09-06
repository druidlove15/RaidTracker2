<?php
/*******************************************************************************
 * class.php
 * -----------------------------------------------------------------------------
 * for version 2.50 date: 05-05-2010
 * -----------------------------------------------------------------------------
 * to show and allow editing of the instances used in this RT
 ******************************************************************************/
if (!rtf_p('class_list')) {
	$rts_internal['main']="<h1>Error</h1><p>You do not have the priviledges to "
	. "be here.</p>";
	return;
}
if ($_POST) { // -- received post data
	foreach ($rtv_inst_name as $v) { // go through each submitted change
		if ($v=='-new-') { // special for new
			if ($rtv_submit=='Delete') { // hit delete button, cannot submit
				$rts_internal['message'].="Cannot delete a class that does not exist.".br();
			} else { // 'modify here = new class
				$arr['category']='class';
				$arr['name']=$rtv_name[$v];
				$arr['value']=addslashes($rtv_value[$v]);
				if ($arr['name'] && $arr['value']) { // all names entered (required)
					rtd_insert (rts_db_keys, $arr);
				} else $rts_internal['message'].="All name fields are required. Record not updated.".br();
			}
		} else { // for other class
			if ($rtv_submit=='Delete') { //delete
				rtd_query("DELETE FROM ".rts_db_keys." WHERE category='class' AND `name`='$v'");
			} else { // modify
				$arr['category']='class';
				$arr['name']=$rtv_name[$v];
				$arr['value']=addslashes($rtv_value[$v]);
				if ($arr['name'] && $arr['value']) { // all names entered (required)
					rtd_update (rts_db_keys, $arr, "category='class' AND `name`='$v'");
				}else $rts_internal['message'].="All name fields are required. Record not updated.".br();
			}
		}
	}
	$rts_internal['message'].="Classes have been updated".br();
}
//--- get list of classes
$rta_instances=rtd_select (rts_db_keys,'*',"category='class'");
//--- form the header of the table
$rta_head="<h1>Instances</h1>\n";
addrow($rta_table,td('&nbsp;',true).td('Short name*',true).td('Full name**',true));
//--- create rows for each instance
foreach ($rta_instances as $rta_rec) {
	$rta_row='';
	$rta_inst=$rta_rec['name'];
	$rta_desc=$rta_rec['value'];
	if ($rta_inst=='none')  // -- cannot modify 'none' instance
		$rta_row=td('&nbsp;').td($rta_inst).td($rta_desc);
	else {
		$rta_row=td(input("inst_name[$rta_inst]",'checkbox',$rta_inst));
		$rta_row.=td(inputtext("name[$rta_inst]",'',$rta_inst,2));
		$rta_row.=td(inputtext("value[$rta_inst]",'',$rta_desc,30));
	}
	addrow ($rta_table, $rta_row);
}
$rta_row=td(input('inst_name[-new-]','checkbox','-new-'));
$rta_row.=td(inputtext("name['-new-]",'','',2));
$rta_row.=td(inputtext("value[-new-]",'','New class',30));
addrow ($rta_table, $rta_row);
//--- table and form the whole list
$rta_table=tbl($rta_table);
$rta_table=$rta_table. button("submit","Modify");
if (count($rta_instances>1)) $rta_table.=button("submit","Delete");
$rta_table.=input("view",'hidden','class');
$rta_table=form($rta_table,".");
//--- final touches on page, list location of files
$rta_head.=$rta_table;
$rta_head.="<p>* This is the name of the class icon name (not including the .png extension.) This file is in /images/class of your RT install.<br />
** This is the name that will be shown in any select box.</p>";
$rts_internal['main']=$rta_head;
?>