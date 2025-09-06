<?php
// ***** step 6a to create TOS ***
$rts_internal['main']="<h1>Create TOS statement</h1>\n";
$rts_internal['main'].="<p>Some guilds like a Terms of Service (TOS) so that their members know the raid rules, or what-not.  This will appear once, only after accounts are created, but before players are allowed to select raids.  At any time, these rules may be changed, and which will prompt players to reacknowlege before continuing.</p>\n<p>Enter your TOS statement below.  HTML is allowed</p>\n";
$rta_form=group(null,'textarea',null,null,"rows=\"20\" cols=\"70\" name=\"tos\"").br();
$rta_form.=button('submit','Continue').br();
$rta_form.=input('step','hidden','5');
$rta_form.=input('tosv','hidden','1');
$rts_internal['main'].=form(div($rta_form),'.','post');

?>