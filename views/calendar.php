<?php
/*******************************************************************************
 * calendar.php
 * -----------------------------------------------------------------------------
 * Version 2.1 last edited: 03-13-2009
 * Script to create the calendar
 *******************************************************************************
 * Preset variables:  () denotes GET variables passed in
 * rts_days_back: (days_back) How many days prior to today.  -1=set day
 * rts_day_start: (day_start) Starting day of the week (only if days_back is -1)
 * rts_num_weeks: (weeks) Total weeks to show
 * rts_day_reset: (reset) array setting day reset. 0-6 Sun-Sat
 *                NB: reset requires binary 7 digit number Sun-Sat 0001000
 ******************************************************************************/

//---------  Initialize variables
$rts_days_back=(isset($rtv_days_back)?$rtv_days_back:rts_days_back);
$rts_day_start=(isset($rtv_day_start)?$rtv_day_start:rts_days_start);
//$rts_days_back=(isset($rtv_days_back)?$rtv_days_back:rts_days_back); //reset days hilight
$rts_num_weeks=(isset($rtv_weeks)?$rtv_weeks:rts_weeks); 
if (!(isset($rtv_reset)) || strlen($rtv_reset)!=7) $rtv_reset=rts_day_reset;
for ($i=0; $i<7; $i++) $rts_day_reset[$i]=$rtv_reset[$i];

//--- initialize the raid info class
$rta_raid=new RAIDINFO; // raid info class to hold a raid data at a time
$rta_raid->setTime('now',date('Y-m-d H:i:s',rts_currtime)); // putting in a timeformat for current time
$rta_timewarn=30; //warningcolor in minutes prior to a freeze time;

//--- Initialize other set variables
$rta_days=array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'); // days of week

//---------  Start with calendar settings defaults
//$rts_day_reset[3]=1; //reset days

//---------  Calculate start date, weeks, and final date
$rta_day['t']['s']=rts_currtime;             //today's timestamp
$rta_day['t']['w']=date('w', rts_currtime);  //weekday of today 0-6
$rta_day['t']['l']=date('M j',rts_currtime); //Month/Day of today i.e. Jan 4

//Calculate the first day
if ($rts_days_back>=0) {  //if using a set days back use this to figure start day
	$rts_day_start=$rta_day['t']['w']-$rts_days_back;
	if ($rts_day_start<0) $rts_day_start+=7;
}
if ($rta_day['t']['w']<=$rts_day_start ) { //&& $rts_days_back<0) {
	$rta_daymod=7;
	if ($rts_day['t']['w']==$rts_day_start && $rts_days_back) $rts_num_weeks++;  //if equal and NOT using set days back, add another week
} else $rta_daymod=0;
$rta_dayoffset=$rta_day['t']['w']-$rts_day_start+$rta_daymod;
//echo "$rta_dayoffset - $rta_daymod - $rts_day_start - ".$rta_day['t']['w'];
//exit;
$rta_day['s']['s']=mktime(0,0,0,date('m',rts_currtime),date('d',rts_currtime)-$rta_dayoffset,date('y',rts_currtime));
$rta_day['e']['s']=mktime(23,59,59,date('m',$rta_day['s']['s']),date('d',$rta_day['s']['s'])+7*$rts_num_weeks,date('y',$rta_day['s']['s']));
$rta_day['s']['q']=date('Y-m-d H:i:s',$rta_day['s']['s']);
$rta_day['e']['q']=date('Y-m-d H:i:s',$rta_day['e']['s']);

//--retrieve raid lists
$rta_raids=rtd_select(rts_db_list, '*', "`date`>'".$rta_day['s']['q'] ."' AND `date`<'".$rta_day['e']['q']."'"," ORDER BY `date` ASC",1);

//-- Other important start information
$rta_player=rtc_acct();  //player info
$rta_offnote=rtf_p('view_officernote');  //if officer's note can be viewed
$rta_create_raid=(rtf_p('raid_create')); //permissions to create raid

//--- make column heads
$rta_th='';//for th
$rta_tb='';//table
for ($i=$rts_day_start;$i<$rts_day_start+7;$i++) {
	if ($i>6) $j=$i-7;                   //calculate if day is 'beyond' sat.  if so, $j gets reduced to sunday next week
	else $j=$i;
	addcell($rta_th,$rta_days[$j],'th',($rts_day_reset[$j]?'reset':''));
}
addrow($rta_tb, $rta_th);

//To create days for raid
$rta_trow='';                            //clear row
$rta_dayloop=$rta_day['s']['s'];         //start day for loop
for ($i=0; $i<7*$rts_num_weeks; $i++) {  //walk through days
	$rta_loopday=date('M j',$rta_dayloop); //format day to print up
	$rta_loopblock=div($rta_loopday,($rta_loopday==$rta_day['t']['l']?'today':''),'date'); //starts loop block with the day
	if ($rta_create_raid) {					  //if you can create a raid
		$rta_dayform=
		div(
			form(
				input('view','hidden','raid') . nl() .                   // hidden for rtv_view=raid
				input('formdata[date]','hidden',date('Y-m-d',$rta_dayloop)) . nl().// for raid view with date
				button('submit','+')                                     // physical button
				,'.')                                                    // url should be set to RT location
			,'','date addraid');                                        // class to add raid
		$rta_loopblock=$rta_dayform.nl().$rta_loopblock.nl();          //form date heading
	}
	// check for any available raids for this date
	if ($rta_raids)  //if there are any raids, check for the date.  Needed in 2.00.3 fix
	while (date('M j',strtotime($rta_raids[0]['date']))==$rta_loopday) {
		// -------------------- set up info about raid
		$rta_craid=array_shift($rta_raids);  //take current raid off of stack
		$rta_raid->load($rta_craid); //load times to raid class
/*
		$rta_raidtimes['end']=strtotime($rta_craid['endtime']);       //end time
		$rta_raidtimes['start']=strtotime($rta_craid['date']);        //start time
		$rta_raidtimes['inv']=strtotime($rta_craid['inv']);           //invite time
		$rta_raidtimes['freeze']=strtotime($rta_craid['freezenew']);  //freeze new signups
*/
		// --------------------- Find out if character is signed up
		$rta_signstat=rtd_select (rts_db_sign,'*',"charid='$rta_player' AND raidid='".$rta_craid['id']."'");
		if ($rta_signstat) {  //if signed up
			$rta_status['main']=$rta_signstat['status'];
			if ($rta_status['main']==1)   //if signed up as raid list then remember character name, otherwise skip.
				$rta_status['char']=$rta_signstat['char'];
			else
				$rta_status['char']=0;
		} else {
			$rta_status['main']=0;
		}
		if ($rta_status['main']>0) $rta_tIcon=rtf_show_icons('status',$rta_status['main']);
		else $rta_tIcon="&nbsp;";	

				//------------------- Time to create the raid and popup
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
		$rta_popup.=div($rta_craid['note'],'','raidnote');
		if ($rta_offnote) $rta_popup.=div($rta_craid['offnote'],'','raidnote officer');
		$rta_signform='';
		if ($rta_signupstat) {  //if signups available, create button form
			$rta_signform=input('raidid','hidden',$rta_craid['id']);
			$rta_signform.=input('formview','hidden','subscribe');
			$rta_signform.=input('formdata[status]','hidden','2');
			$rta_signform.=button('submit','Subscribe');
			$rta_signform=form($rta_signform,'.');  //form created
		}
		$rta_popup.=div($rta_signform.url('.',"Lists","raidid=$rta_craid[id]"),'','Signup');
		$rta_raidmain=url('.',
		  "<img src=\"".rts_syspath."/images/instance/$rta_craid[icon].png\" alt=\"$rta_craid[icon]\" />",
		  "raidid=$rta_craid[id]");
		$rta_raidmain.="\n".div($rta_popup,'','raidpopup'.($i%7>4?' moveleft':''));
		$rta_raidmain=div($rta_raidmain.$rta_tIcon,'','raidinst');
		//$rta_raidmain.= $rta_tIcon; //add status and icon
		//end div
		$rta_loopblock.=$rta_raidmain;
	}
	addcell($rta_trow, $rta_loopblock);
	$rta_trow.=NL.NL;
	$rta_dayloop=strtotime("+1 day", $rta_dayloop);//increments day
	if (date('w',$rta_dayloop)-$rts_day_start==0) {  //if at the end of the week, make a new row
		addrow($rta_tb, $rta_trow);
		$rta_trow='';
	}
	//echo $rta_trow;
}
$rta_tb=tbl($rta_tb, 'raidcal'); //make table
$rts_internal['main']="$rta_tb";  //<-- add alternate view.
if ($rta_create_raid) {
	$rts_internal['main'].="\n<h2>Additional features:</h2>\n";
	$rts_internal['main'].="<form method=\"post\" action=\".\"><div>\n"
	. input('view','hidden','raid') . nl()                    // hidden for rtv_view=raid
	. input('formdata[date]','hidden','') . nl()// for raid view with date

	."<input type=\"submit\" name=\"createbutton\" value=\"Add a new raid\" />\n"
	."</div>\n</form>\n";
	$rts_internal['main'].="<p>You may also create a raid by clicking on the + button next to the date.</p>\n";
}
?>