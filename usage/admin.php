<?php
$pageTitle = _('Administration');
include 'templates/header.php';


if ($user->isAdmin()){
?>


<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Users");?></span>&nbsp;&nbsp;<span id='span_User_response' class='redText'></span>
<span id='span_newUser' class='adminAddInput'><a href='ajax_forms.php?action=getAdminUserUpdateForm&height=196&width=248&modal=true' class='thickbox' id='expression'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add user")."' />";?></a></span>
<br /><br />
<div id='div_User'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>


<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Email addresses for logs");?></span>&nbsp;&nbsp;<span id='span_EmailAddress_response'></span>
<span id='span_newEmailAddress' class='adminAddInput'><a href='ajax_forms.php?action=getLogEmailAddressForm&height=122&width=238&modal=true' class='thickbox'><?php echo "<img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add mail adress")."' />";?></a></span>
<br /><br />
<div id='div_emailAddresses'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />



<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Outlier Parameters");?></span>&nbsp;&nbsp;<span id='span_Outlier_response'></span>
<br /><br />
<div id='div_outliers'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />

<script type="text/javascript" src="js/admin.js"></script>

<?php

}

include 'templates/footer.php';

?>
