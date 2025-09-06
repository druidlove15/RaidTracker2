<?php
/*******************************************************************************
 * history.php
 *******************************************************************************
 * version 2.50
 * -----------------------------------------------------------------------------
 * intended to show history view again
 ******************************************************************************/
// --- get all intended variables
// if not passed in from a search
	$rta_future=rts_hist_future;
	$rta_limit_begin=0;
	$rta_limit_end=rts_hist_prev-1;
// else if passed in

// if future is not needed, calculate to now.
if (!$rta_future) {
	if ($rta_future>0) { //past only
		$rta_where="`date`<NOW()";
	} else { // future only
		$rta_where="`date`>NOW()";
	}
}
//--- All other variables
//--- get local format variables
	$rta_localdatef="Y-m-d";
	$rta_localtimef="h:ia";
//--- permissions for status
	$rta_list_per[1]=rtf_p('show_raidlist');
	$rta_list_per[2]=rtf_p('show_available');
	$rta_list_per[3]=rtf_p('show_reserve');
	$rta_list_per[4]=rtf_p('show_withdraw');
	$rta_list_per[5]=rtf_p('show_remove');

$rta_raid=new RAIDINFO; // entire raid class
$rta_raid->setTime('now',date('Y-m-d H:i:s',rts_currtime)); //sets current date to server offset date
$rta_player=rtc_acct();  //player info
$rta_offnote=rtf_p('view_officernote');  //if officer's note can be viewed
$rta_create_raid=(rtf_p('raid_create')); //permissions to create raid
$rta_timewarn=30; //warningcolor in minutes prior to a freeze time;

//--- get list
$rta_raidlist=rtd_select (rts_db_list,'*',$rta_where,"ORDER BY `date` DESC LIMIT $rta_limit_begin,$rta_limit_end");
//set up columns here
$rta_row=td("&nbsp;",1).td("Date",1).td("Time",1).td("Raid",1).td("Status",1).td("Req",1); //rows
for ($i=1; $i<6; $i++) 
	if ($rta_list_per[$i]) $rta_row.=td(rtf_show_icons('status',$i),true);
addrow($rta_tbl, $rta_row);
for ($i=0; $i<count($rta_raidlist);$i++) { // setting up records into columns
	//puts raid info into RAID class
	$rta_raid->load($rta_raidlist[$i]);
	//-- counts all available signups for all statuses
	for ($j=1; $j<8; $j++) {
//		$rta_ct[$j]=count(rtd_select(rts_db_sign,'*',"raidid=".$rta_raid->id." AND `status`=$j"));
		$rta_ct[$j]=(rtd_selcol2(rts_db_sign,"COUNT(`id`)","raidid=".$rta_raid->id." AND `status`=$j")); // revised selcol not using ``
	}
	//combine whiteboard status to avail/reserve
	$rta_ct[2]+=$rta_ct[6];
	$rta_ct[3]+=$rta_ct[7];
	//status icon
	if ($rta_player) {
		$rta_signstat=rtd_select(rts_db_sign,"*","raidid=".$rta_raid->id." AND charid=$rta_player"); 
		//var_dump($rta_signstat);
		//exit();
		if ($rta_signstat) {  //if signed up
			$rta_status['main']=$rta_signstat['status'];
			if ($rta_status['main']==1)   //if signed up as raid list then remember character name, otherwise skip.
				$rta_status['char']=$rta_signstat['char'];
			else
				$rta_status['char']=0;
		} else {
			$rta_status['main']=0;
		}
	} else 
		$rta_status['main']=-1;
	if ($rta_status['main']>0) $rta_t[$i]['status']=rtf_show_icons('status',$rta_status['main']);
	else $rta_t[$i]['status']="&nbsp;";	
	
	
	//figure out status here using $rta_raid['id']
	
	//columns for date and time
	$rta_t[$i]['date']=$rta_raid->showtime('start',$rta_localdatef);
	$rta_t[$i]['time']=$rta_raid->showtime('start',$rta_localtimef);
	//raid name and icon
	$rta_t[$i]['raid']=$rta_raid->getLocation("./images/instance");
	$rta_t[$i]['raid']=url('.', $rta_t[$i]['raid'],"raidid=".$rta_raid->id);

	//popup here:  taken from calendar.php
		$rta_popup='';
		$rta_raidmain='';
		if ($rta_status['main']) {  //if signup is here, create
			$rta_popup= $rta_tIcon; //icon in popup
			if ($rta_status['main']==1) $rta_popup .=rtf_character($rta_status['char'],0,2,0);
			$rta_popup=div ($rta_popup,'','right raidstatus');
		}
		$rta_popup.=div($rta_craid['instance'],'','raidtitle');
		//check for times
		$rta_signupstat=false;  //disables signup button unless allowed to
		$rta_status_text=$rta_raid->getStatus();
		if ($rta_status_text=="Open") {
			$rta_status_mode=$rta_raid->getColor($rta_timewarn);
//			if ($rta_status_mode) $rta_status_text.=" less than $rta_timewarn minutes";
			if (($rta_status['main']==4 || (!$rta_status['main'] && rtc_acct()))) {  //if not signed up or withdrawn show time left
				$rta_signupstat=true;  //turns on signup button
			}
		}
		$rta_popup.=div($rta_status_text,'','timewarn');
		//$rta_popup.=div("Start time
/*
		if (rts_currtime>$rta_raidtimes['end'])
			$rta_popup.=div("Raid concluded",'','timewarn');
		else if (rts_currtime>$rta_raidtimes['start'])
			$rta_popup.=div("Raid in progress",'','timewarn');
		else if (rts_currtime>$rta_raidtimes['inv'])
			$rta_popup.=div("Raid invites started",'','timewarn')
			.div("Start time: ".rtf_datetime($rta_raidtimes['start'],'t'),'','timenotify');
		else if (rts_currtime>$rta_raidtimes['freeze'])
			$rta_popup.=div("Signups are closed",'','timewarn')
			.div("Start time: ".rtf_datetime($rta_raidtimes['start'],'t'),'','timenotify');
		else {
			if (($rta_status['main']==4 || (!$rta_status['main'] && rtc_acct()))) {  //if not signed up or withdrawn show time left
				$rta_signupstat=true;  //turns on signup button
				if (rts_currtime+60*5>$rta_raidtimes['freeze'])
					$rta_popup.=div("Signups close within 5 minutes",'','timewarn');
				else if (rts_currtime+60*60>$rta_raidtimes['freeze']) {
					$rta_f=(int)(($rta_raidtimes['freeze']-rts_currtime)/60);
					$rta_popup.=div("Signups close in $rta_f minutes",'','timewarn');
				}else if (rts_currtime+24*60*60>$rta_raidtimes['freeze']) {
					$rta_f=(int)(($rta_raidtimes['freeze']-rts_currtime)/60/60);
					$rta_popup.=div("Signups close in $rta_f hour".($rta_f==1?'':'s'),'','timewarn');
				}
			}
			$rta_popup.=div("Start time: ".rtf_datetime($rta_raidtimes['start'],'t'),'','timenotify');
		}
*/
		$rta_popup.=div($rta_raid->public_note,'','raidnote');
		if ($rta_offnote) $rta_popup.=div($rta_raid->private_note,'','raidnote officer');
		$rta_signform='';
		if ($rta_signupstat) {  //if signups available, create button form
			$rta_signform=input('raidid','hidden',$rta_craid['id']);
			$rta_signform.=input('formview','hidden','subscribe');
			$rta_signform.=input('formdata[status]','hidden','2');
			$rta_signform.=button('submit','Subscribe');
			$rta_signform=form($rta_signform,'.');  //form created
		}
		$rta_popup.=div($rta_signform.url('.',"Lists","raidid=$rta_craid[id]"),'','Signup');
		$rta_raidmain=$rta_t[$i]['raid'];
		$rta_raidmain.="\n".div($rta_popup,'','raidpopup'.($i%7>4?' moveleft':''));
		$rta_raidmain=div($rta_raidmain.$rta_tIcon,'','raidinst');
	$rta_t[$i]['raid']=$rta_raidmain;
	
	
	//raid time deadlines here to calculate status
	$rta_t[$i]['s']=$rta_raid->getStatus();
	switch ($rta_raid->getColor($rta_timewarn)) {
		case 2: $rta_t[$i]['c']='red'; break;
		case 1: $rta_t[$i]['c']='yellow'; break;
		case 0: $rta_t[$i]['c']='green'; break;
		case -1: $rta_t[$i]['c']='error'; break;
	}

	$rta_row =td($rta_t[$i]['status']);
	$rta_row.=td($rta_t[$i]['date']);
	$rta_row.=td($rta_t[$i]['time']);
	$rta_row.=td($rta_t[$i]['raid']);
	$rta_row.=td($rta_t[$i]['s'],false,'',$rta_t[$i]['c']);
	$rta_row.=td($rta_raid->active);
	for ($j=1; $j<6; $j++) 
		if ($rta_list_per[$j]) $rta_row.=td($rta_ct[$j]);

	addrow ($rta_tbl, $rta_row);
}
$rta_tbl=tbl($rta_tbl);
$rts_internal['main']=$rta_tbl;
if ($rta_create_raid) {
	$rts_internal['main'].="\n<h2>Additional features:</h2>\n";
	$rts_internal['main'].="<form method=\"post\" action=\".\"><div>\n"
	. input('view','hidden','raid') . nl()                    // hidden for rtv_view=raid
	. input('formdata[date]','hidden','') . nl()// for raid view with date

	."<input type=\"submit\" name=\"createbutton\" value=\"Add a new raid\" />\n"
	."</div>\n</form>\n";
}
?>