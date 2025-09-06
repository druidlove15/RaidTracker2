<?php
/*******************************************************************************
 * admin/settings.php
 * -----------------------------------------------------------------------------
 * Version 2.50: Change settings
 ******************************************************************************/
//---- 
if (!$rtv_formdata['sys']) {
	$rtv_formdata['sys']['server_offset']=rts_server_offset;
	$rtv_formdata['sys']['style']=rts_style;
	$rtv_formdata['sys']['guild']=rts_guild;
	$rtv_formdata['sys']['realm']=rts_realm;
	$rtv_formdata['sys']['levdefault']=rts_levdefault;
	$rtv_formdata['sys']['maint']=rts_maint;
	$rtv_formdata['sys']['domain']=rts_domain;
	$rtv_formdata['sys']['syspath']=rts_syspath;
	$rtv_formdata['sys']['sort_time']=rts_sort_time;
	$rtv_formdata['sys']['TOS']=rts_TOS;
	$rtv_formdata['sys']['tosv']=rts_tosv;
	$rtv_formdata['sys']['datespan']=rts_datespan;
	$rtv_formdata['sys']['days_back']=rts_days_back;
	$rtv_formdata['sys']['days_start']=rts_days_start;
//	$rtv_formdata['sys']['days_reset']=rts_days_reset;  //reset days
	$rtv_formdata['sys']['weeks']=rts_weeks;
	$rtv_formdata['sys']['cookie']=rts_cookie;
	$rtv_formdata['sys']['news']=rts_news;
	$rtv_formdata['sys']['hist_prev']=rts_hist_prev;
	$rtv_formdata['sys']['hist_future']=rts_hist_future;
	$rtv_formdata['sys']['view']=rts_view;
	$rtv_formdata['sys']['override']=rts_override;
	$rtv_formdata['sys']['navigate']=rts_navigate;
	$rtv_formdata['sys']['file_version']=rts_file_version;
	$rtv_formdata['sys']['datef']=rts_datef;
	$rtv_formdata['sys']['timef']=rts_timef;
	
	
} else {
	$rts_internal['main'].="<h1>System Settings</h1>\n<p>Settings have been saved."
	. "To see your settings live, click here to ".url('.','refresh');
	return;
}
$rta_form="<h1>RaidTracker Settings</h1>\n";
$rta_form2="<h2>System Settings</h2>\n"
.textfield('formdata[sys][domain]','Domain name',$rtv_formdata['sys']['domain'])
.textfield('formdata[sys][syspath]','Path to RaidTracker',$rtv_formdata['sys']['syspath'])
.select ('formdata[sys][maint]','Maintenance mode:',
  array(0=>'Off',1=>'On'),$rtv_formdata['sys']['maint']).br()
.textfield('formdata[sys][cookie]','Cookie name:',$rtv_formdata['sys']['cookie'],'line','10')
.textfield('formdata[sys][server_offset]','Server offset',$rtv_formdata['sys']['server_offset'],'line','1')

."<h2>Guild Settings</h2>\n"
.textfield('formdata[sys][guild]','Guild name',$rtv_formdata['sys']['guild'])
.textfield('formdata[sys][realm]','Realm name',$rtv_formdata['sys']['realm'])
.textfield('formdata[sys][levdefault]','Default Level',$rtv_formdata['sys']['levdefault'],'line','1')

."<h2>Display defaults</h2>\n"
.textfield('formdata[sys][style]','RaidTracker Style',$rtv_formdata['sys']['style'],'line','10')
.select ('formdata[sys][news]','Show news:',
  array(2=>'On all views',1=>'Main view only',0=>'Always hide'),$rtv_formdata['sys']['news']).br()
.select ('formdata[sys][sort_time]','Sort lists by:',
  array(0=>'Class and Name',1=>'Time'),$rtv_formdata['sys']['sort_time']).br()
.select ('formdata[sys][TOS]','TOS required?',array(0=>'No',1=>'Yes'),$rtv_formdata['sys']['TOS']).br()
//.textfield('formdata[sys][TOS]','Require TOS?',$rtv_formdata['sys']['TOS'],'line','1')
.textfield('formdata[sys][tosv]','TOS Version',$rtv_formdata['sys']['tosv'],'line','1')
.textfield('formdata[sys][datef]','Date format',$rtv_formdata['sys']['datef'],'line','5')
.textfield('formdata[sys][timef]','Time format',$rtv_formdata['sys']['timef'],'line','5')
.select ('formdata[sys][datespan]','Tracking days:',
  array(7=>'7',14=>'14',30=>'30',60=>'60',90=>'90',-1=>'Life'),$rtv_formdata['sys']['datespan']).br()
.select ('formdata[sys][view]','Default view:',array('c'=>'Calendar','h'=>'History'),$rtv_formdata['sys']['view']).br()
.select ('formdata[sys][override]','View Override:',array(0=>'None',1=>'Partial',2=>"Full"),$rtv_formdata['sys']['override']).br()
.select ('formdata[sys][navigate]','Navigation:',array(1=>'Yes',0=>'No'),$rtv_formdata['sys']['navigate']).br()


."<h2>Calendar defaults</h2>\n"
.select ('formdata[sys][days_back]','Days revealed:',
  array('-2',"Month",'-1'=>'Week','0'=>'0','1'=>'1 day','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6'),$rtv_formdata['sys']['days_back']).br()
.select ('formdata[sys][days_start]','Calendar starts on:',
  array(0=>'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'),$rtv_formdata['sys']['days_start']).br()
.select ('formdata[sys][weeks]','Number of Weeks:',array(1=>'1','2','3','4','5','6'),$rtv_formdata['sys']['weeks']).br()

."<h2>History settings</h2>\n"
.textfield('formdata[sys][hist_prev]','Events/page',$rtv_formdata['sys']['hist_prev'],'line','1')
.select ('formdata[sys][hist_future]','Future events:',array(1=>'Yes',0=>'No'),$rtv_formdata['sys']['hist_future']).br()
.input('formdata[sys][version]','hidden',$rtv_formdata['sys']['version'])
.button('formdata[submit]','Submit');
$rta_form2.=input('formview','hidden','adminsettings');

$rta_form.=form($rta_form2,'.','post','','trueform');
$rts_internal['main'].=$rta_form;
$rts_internal['help'].="<h1>Adjusting settings</h1>"
.bold("System Settings")."-Recommended not to change anything here unless this RT copy is moved, or another copy is installed. For advanced uses only".br()
.bold("Server Offset")."-Use this to adjust the difference in hours between the server time where RT is and "
."Game server time".br()
.bold("RaidTracker Style")."-The default style for all of RT".br()
.bold("Sort by")."-Sorting order only in list view.  Sorting only occurs within each "
."role group (i.e. healers)".br()
.bold("TOS required / version")."-If you require users to read a Term of Use page before starting, "
."set required to 1, and version to any number.  Changing a version number will "
."cause everyone to be prompted with the TOS screen again before continuing. ".br()
.bold("Date/Time format")."Uses format from the PHP ".url("http://php.net/date",'date()')." format.".br()
.bold("Tracking days")."- For counting stats only, how many recent days should be counted by default. "
."Life holds no limitations.  You can change this temporary in each view. ".br()
.bold("Default view")."Calendar or history to be shown immediately on the website."
.bold("View override")."What settings here will override users settings. Navigate will override in full. Default view will be overridden if not set to 'None'."
.bold("Navigate")."Whether the previous/next will be shown on views."
.bold("Days revealed")."If this is set to a number, the calendar will float, with these many days revealed.
Week and month will show current week and month first.".br()
.bold("Calendar starts on")."-Ignored if 'Days back in Calendar' is not set to 'Use Fixed Day' ".br()
.bold("Number of weeks")."-This will show the number of 7-day weeks in the calendar "
." from the first day. Ignored if days back is set to Month".br()
;
?>