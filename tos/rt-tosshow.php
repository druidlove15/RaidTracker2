<h1>Terms and Service</h1>
<?php
echo "<p>Please read these rules carefully.  In order to sign up for "
. rts_guild ."'s raids, you need to acknowledge and approve of these rules.</p>\n"
. '<div id="tosbox">'."\n";
if (!file_exists('user/tos.php')) $rta_prefix=".";
include $rta_prefix.'./user/tos.php';
echo "</div>\n";
echo "<form method=\"post\" action=\".\">\n";
echo button("tos","I agree")." ".button("false", "I don't agree")
. "</form>\n";

?>