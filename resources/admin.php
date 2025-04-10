<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.2
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
CoralSession::set('ref_script', $currentPage = '');

$pageTitle=_('Administration');
include 'templates/header.php';

$config = new Configuration;

//ensure user has admin permissions
if ($user->isAdmin()){
	?>

<main id="main-content">
	<article>
		<h2><?php echo _("Administration");?></h2>

		<div id='div_AdminContent'>
			<img src = "images/circle.gif" /><?php echo _("Loading...");?>
		</div>
		<div class='error' id='div_error'></div>

	</article>
		<!-- TODO: WAI-ARIA Tab Panel -->
		<nav id="side" aria-label="<?php echo _('Settings'); ?>">
			<ul class="nav side">
				<li class='adminMenuLink'><a href='javascript:void(0);' id='UserAdminLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Users");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='WorkflowAdminLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Workflow / User Group");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AccessMethod' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Access Method");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AcquisitionType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Acquisition Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AdministeringSite' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Administering Site");?></a></li>
				<?php if ($config->settings->enableAlerts == 'Y'){ ?>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AlertAdminLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Alert Settings");?></a></li>
				<?php } ?>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AliasType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Alias Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AttachmentType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Attachment Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AuthenticationType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Authentication Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='AuthorizedSite' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Authorized Site");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='CatalogingStatus' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Cataloging Status");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='CatalogingType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Cataloging Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='ContactRole' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Contact Role");?></a></li>
				<?php if ($config->settings->enhancedCostHistory == 'Y'){ ?>
					<li class='adminMenuLink'><a href='javascript:void(0);' id='CostDetails' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Cost Details");?></a></li>
				<?php } ?>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='CurrencyLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Currency");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id="DowntimeType" class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Downtime Type");?></a></li>
        <li class='adminMenuLink'><a href='javascript:void(0);' id='EbscoKbConfigLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("EBSCO Kb Config");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='ExternalLoginType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("External Login Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='FundLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Funds");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='ImportConfigLink' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Import Configuration");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='LicenseStatus' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("License Status");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='NoteType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Note Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='OrderType' class='AdminLink' aria-controls="div_AdminContent"><?php echo _("Order Type");?></a></li>
				<?php

				//For Organizations links
				//if the org module is not installed, display provider list for updates
				if ($config->settings->organizationsModule == 'N'){ ?>

					<li class='adminMenuLink'><a href='javascript:void(0);' id='OrganizationRole' class='AdminLink'><?php echo _("Organization Role");?></a></li>
					<li class='adminMenuLink'><a href='javascript:void(0);' id='Organization' class='AdminLink'><?php echo _("Organizations");?></a></li>

				<?php } ?>

				<li class='adminMenuLink'><a href='javascript:void(0);' id='PurchaseSite' class='AdminLink'><?php echo _("Purchasing Site");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='ResourceFormat' class='AdminLink'><?php echo _("Resource Format");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='ResourceType' class='AdminLink'><?php echo _("Resource Type");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='StorageLocation' class='AdminLink'><?php echo _("Storage Location");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='SubjectsAdminLink' class='AdminLink'><?php echo _("Subjects");?></a></li>
				<li class='adminMenuLink'><a href='javascript:void(0);' id='UserLimit' class='AdminLink'><?php echo _("User Limit");?></a></li>
			</ul>
		</nav>

</main>

<?php

//end else for admin
}else{
	echo _("You do not have permissions to access this screen.");
}

include 'templates/footer.php';
?>
<script type="text/javascript" src="js/admin.js"></script>
</body>
</html>

