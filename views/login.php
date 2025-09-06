<?php
/*******************************************************************************
 * login.php
 * -----------------------------------------------------------------------------
 * Version 2.00 login/out form
 ******************************************************************************/
// --
$rta_text="<h1>Log in or create account</h1>\n<p>Please log in with your e-mail"
."address.  If you are new, and want to sign up to raids for ".rts_guild.", you "
."need to create an account.  Please enter your e-mail address and click create."
."</p>";
$rta_form=passfield("formdata[email]","e-mail address:");
//$rta_form=textfield("formdata[email]","e-mail address:");
$rta_form.=button("formdata[login]","Log in");
if (rts_maint==0) $rta_form.=" ".button("formdata[create]","Create");
//else $rta_form.=" Account creation disabled during maintenance.";
$rta_form.=input("formdata[step]","hidden","2");
$rta_form.=input("formview","hidden","login");
$rta_form=form($rta_form,".",'post','','trueform');
$rta_text.=$rta_form;
$rts_internal['main']=$rta_text;

?>