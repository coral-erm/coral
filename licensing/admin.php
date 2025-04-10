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

//set referring page
$_SESSION['ref_script']=$currentPage;



$pageTitle=_('Administration');
include 'templates/header.php';

if ($user->isAdmin()){

?>

<main id="main-content">
	<article>
	<h2><?php echo _('Administration'); ?></h2>
	<div class="header">
		<h3><?php echo _("Users");?></h3>
		<span id='span_newUser' class='adminAddInput addElement'><?php echo "<button type='button' onclick='myDialog(\"ajax_forms.php?action=getAdminUserUpdateForm\",225,350)' class='thickbox addElement btn' id='expression'><img id='addUser class='AdminAddIcon' src='images/plus.gif' title='"._("add User")."' /></button>";?></span>
	</div>

		<p id='span_User_response' class='error'></p>

		<div id='div_User'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>

		<div class="header">
			<h3><?php echo _("Document Types");?></h3>
			<span id='span_newDocumentType' class='adminAddInput'><button type="button" class="btn" onclick='showAdd("DocumentType");'><?php echo "<img id='addDocument' class='AdminAddIcon' src='images/plus.gif' title='"._("add document type")."' />";?></button></span>
		</div>

		<p id='span_DocumentType_response' class='error'></p>

		<div id='div_DocumentType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>
		
		<div class="header">
			<h3><?php echo _("Expression Types");?></h3>
			<span id='span_newExpressionType' class='adminAddInput addElement'><button type="button" onclick='myDialog("ajax_forms.php?action=getExpressionTypeForm",225,350)' class='btn thickbox' id='expressionType'><?php echo "<img id='addExpressionType' src='images/plus.gif' title='"._("add expression type")."' />";?></button></span>
		</div>

		<p id='span_ExpressionType_response'></p>

		<div id='div_ExpressionType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>
		<div class="header">
			<h3><?php echo _("Qualifiers");?></h3>
			<span id='span_newQualifier' class='adminAddInput addElement'><button type="button" class="btn" onclick='myDialog("ajax_forms.php?action=getQualifierForm",225,350)' class='thickbox'><?php echo "<img id='addQualifier' src='images/plus.gif' title='"._("add qualifier")."' />";?></button></span>
		</div>

		<span id='span_Qualifier_response'></span>

		<div id='div_Qualifier'>
		<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>

		<div class="header">
			<h3><?php echo _("Signature Types");?></h3>
			<span id='span_newSignatureType' class='adminAddInput'><button type="button" class="btn" onclick='showAdd("SignatureType");'><?php echo "<img id='addSignature' src='images/plus.gif' title='"._("add signature")."' />";?></button></span>
		</div>

		<p id='span_SignatureType_response'></p>

		<div id='div_SignatureType'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>
		<div class="header">
			<h3><?php echo _("License Statuses");?></h3>
			<span id='span_newStatus' class='adminAddInput addElement'><button type="button" class="btn" onclick='showAdd("Status");'><?php echo "<img id='addLicenseStatuses' src='images/plus.gif' title='"._("add License statuses")."' />";?></button></span>
		</div>
		
		<p id='span_Status_response'></p>
		<div id='div_Status'>
		<img src = "images/circle.gif"><?php echo _("Loading...");?>
		</div>

<?php

$config = new Configuration;

//if the Resources module is not installed, do not display calendar options
if (($config->settings->resourcesModule == 'Y') && (strlen($config->settings->resourcesDatabaseName) > 0)) { ?>

<h3><?php echo _("Calendar Settings");?></h3>
<p id='span_CalendarSettings_response'></p>

<div id='div_CalendarSettings'>
<img src = "images/circle.gif"><?php echo _("Loading...");?>
</div>

<?php
}

//if the org module is not installed, display provider list for updates
if ($config->settings->organizationsModule != 'Y'){ ?>

	<div class="header">
	<h3><?php echo _("Consortia");?></h3>
		<span id='span_newConsortium' class='adminAddInput addElement'><button type="button" class="btn" onclick='showAdd("Consortium");'><?php echo _("add consortium");?></button></span>
	</div>

	<p id='span_Consortium_response'></p>
	
	<div id='div_Consortium'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>
	<div class="header">
		<h3><?php echo _("Providers");?></h3>
		<span id='span_newOrganization' class='adminAddInput addElement'><button type="button" class="btn" onclick='showAdd("Organization");'><?php echo _("add provider");?></button></span>
	</div>
	
	<p id='span_Organization_response'></p>
	<div id='div_Organization'>
	<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>

<?php } ?>

<?php

//if the Terms Tool is used, display options
if ($config->settings->useTermsToolFunctionality == 'Y') { ?>

	<div class="header">
		<h3><?php echo _("Terms Tool Settings");?></h3>
		<button type="button" class="btn" onclick='myDialog("ajax_forms.php?action=getTermsToolSettingsForm&",225,350)' class="thickbox"><?php echo _("edit"); ?></button>
	</div>

	<span id='span_TermsTool_response'></span>
	
	<div id='div_TermsTool'>
			<img src = "images/circle.gif"><?php echo _("Loading...");?>
	</div>

<?php } ?>
</article>
</main>

<script src="js/admin.js"></script>
<?php
}else{
	echo _("You don't have permission to access this page");
}

include 'templates/footer.php';
?>
</body>
</html>