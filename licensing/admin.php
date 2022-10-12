<?php

/*
**************************************************************************************************************************
** CORAL Licensing Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
*/

include_once 'directory.php';

$pageTitle=_('Administration');
include 'templates/header.php';

//set referring page
$_SESSION['ref_script']=$currentPage;

if ($user->isAdmin()){

?>


<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Users");?></span>&nbsp;&nbsp;<span id='span_User_response' class='redText'></span><span id='span_newUser' class='adminAddInput'><?php echo "<a href='javascript:void(0)' onclick='javascript:myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm\",225,350)' class='thickbox' id='expression'><img id='addUser class='AdminAddIcon' src='images/plus.gif' title='"._("add User")."' /></a>";?></span>

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
<span class="headerText"><?php echo _("Document Types");?></span>&nbsp;&nbsp;<span id='span_DocumentType_response'></span><span id='span_newDocumentType' class='adminAddInput'><a href='javascript:showAdd("DocumentType");'><?php echo "<img id='addDocument' class='AdminAddIcon' src='images/plus.gif' title='"._("add document type")."' />";?></a></span>
<br /><br />
<div id='div_DocumentType'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Expression Types");?></span>&nbsp;&nbsp;<span id='span_ExpressionType_response'></span><span id='span_newExpressionType' class='adminAddInput'><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getExpressionTypeForm",225,350)' class='thickbox' id='expressionType'><?php echo "<img id='addExpressionType' src='images/plus.gif' title='"._("add expression type")."' />";?></a></span>

<br /><br />
<div id='div_ExpressionType'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />
<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Qualifiers");?></span>&nbsp;&nbsp;<span id='span_Qualifier_response'></span><span id='span_newQualifier' class='adminAddInput'><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getQualifierForm",225,350)' class='thickbox'><?php echo "<img id='addQualifier' src='images/plus.gif' title='"._("add qualifier")."' />";?></a></span>

<br /><br />
<div id='div_Qualifier'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Signature Types");?></span>&nbsp;&nbsp;<span id='span_SignatureType_response'></span><span id='span_newSignatureType' class='adminAddInput'><a href='javascript:showAdd("SignatureType");'><?php echo "<img id='addSignature' src='images/plus.gif' title='"._("add signature")."' />";?></a></span>
<br /><br />
<div id='div_SignatureType'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<br />
<br />


<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("License Statuses");?></span>&nbsp;&nbsp;<span id='span_Status_response'></span><span id='span_newStatus' class='adminAddInput'><a href='javascript:showAdd("Status");'><?php echo "<img id='addLicenseStatuses' src='images/plus.gif' title='"._("add License statuses")."' />";?></a></span>
<br /><br />
<div id='div_Status'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<?php

$config = new Configuration;

//if the Resources module is not installed, do not display calendar options
if (($config->settings->resourcesModule == 'Y') && (strlen($config->settings->resourcesDatabaseName) > 0)) { ?>

<br />
<br />

<table class="headerTable">
<tr><td>
<span class="headerText"><?php echo _("Calendar Settings");?></span>&nbsp;&nbsp;<span id='span_CalendarSettings_response'></span>
<br /><br />
<div id='div_CalendarSettings'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>
</td></tr>
</table>

<?php
}

//if the org module is not installed, display provider list for updates
if ($config->settings->organizationsModule != 'Y'){ ?>


	<br />
	<br />

	<table class="headerTable">
	<tr><td>
	<span class="headerText"><?php echo _("Consortia");?></span>&nbsp;&nbsp;<span id='span_Consortium_response'></span>
	<br /><span id='span_newConsortium' class='adminAddInput'><a href='javascript:showAdd("Consortium");'><?php echo _("add consortium");?></a></span>
	<br /><br />
	<div id='div_Consortium'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

	<br />
	<br />

	<table class="headerTable">
	<tr><td>
	<span class="headerText"><?php echo _("Providers");?></span>&nbsp;&nbsp;<span id='span_Organization_response'></span>
	<br /><span id='span_newOrganization' class='adminAddInput'><a href='javascript:showAdd("Organization");'><?php echo _("add provider");?></a></span>
	<br /><br />
	<div id='div_Organization'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	</td></tr>
	</table>

<?php } ?>

<?php

//if the Terms Tool is used, display options
if ($config->settings->useTermsToolFunctionality == 'Y') { ?>

    <br />
    <br />

    <table class="headerTable">
        <tr><td>
                <span class="headerText"><?php echo _("Terms Tool Settings");?></span>&nbsp;&nbsp;<span id='span_TermsTool_response'></span>
                <br /><a href="javascript:void(0)" onclick='javascript:myDialog("ajax_forms.php?action=getTermsToolSettingsForm&",225,350)' class="thickbox"><?php echo _("edit"); ?></a>
                <br />
                <br />
                <div id='div_TermsTool'>
                    <img src = "images/circle.gif"><?php echo _("Loading...");?>
                </div>
            </td></tr>
    </table>

<?php } ?>

<br />

<script type="text/javascript" src="js/admin.js"></script>

<?php
}else{
	echo _("You don't have permission to access this page");
}

include 'templates/footer.php';
?>
