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

	<table class='headerTable'>
	<tr>
	<td style='margin:0;padding:0;text-align:left;'>
		<table style='width:100%; margin:0;padding:0;'>
		<tr style='vertical-align:top'>
		<td>
		<span class="headerText"><?php echo _("Administration");?></span>
		<br />
		</td>
		</tr>
		</table>


		<table style='width:700px; text-align:left; vertical-align:top;'>
		<tr>
		<td style='width:170px;vertical-align:top;'>
			<table class='adminMenuTable' style='width:170px;'>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='UserAdminLink'><?php echo _("Users");?></a></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='WorkflowAdminLink'><?php echo _("Workflow / User Group");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AccessMethod' class='AdminLink'><?php echo _("Access Method");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AcquisitionType' class='AdminLink'><?php echo _("Acquisition Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AdministeringSite' class='AdminLink'><?php echo _("Administering Site");?></div></td></tr>
				<?php if ($config->settings->enableAlerts == 'Y'){ ?>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='AlertAdminLink'><?php echo _("Alert Settings");?></div></td></tr>
				<?php } ?>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AliasType' class='AdminLink'><?php echo _("Alias Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AttachmentType' class='AdminLink'><?php echo _("Attachment Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AuthenticationType' class='AdminLink'><?php echo _("Authentication Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='AuthorizedSite' class='AdminLink'><?php echo _("Authorized Site");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='CatalogingStatus' class='AdminLink'><?php echo _("Cataloging Status");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='CatalogingType' class='AdminLink'><?php echo _("Cataloging Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='ContactRole' class='AdminLink'><?php echo _("Contact Role");?></div></td></tr>
				<?php if ($config->settings->enhancedCostHistory == 'Y'){ ?>
					<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='CostDetails' class='AdminLink'><?php echo _("Cost Details");?></div></td></tr>
				<?php } ?>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='CurrencyLink'><?php echo _("Currency");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='AdminLink' id="DowntimeType"><?php echo _("Downtime Type");?></div></td></tr>
                <tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='EbscoKbConfigLink'><?php echo _("EBSCO Kb Config");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='ExternalLoginType' class='AdminLink'><?php echo _("External Login Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='FundLink'><?php echo _("Funds");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='ImportConfigLink'><?php echo _("Import Configuration");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='LicenseStatus' class='AdminLink'><?php echo _("License Status");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='NoteType' class='AdminLink'><?php echo _("Note Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='OrderType' class='AdminLink'><?php echo _("Order Type");?></div></td></tr>
				<?php

				//For Organizations links
				//if the org module is not installed, display provider list for updates
				if ($config->settings->organizationsModule == 'N'){ ?>

					<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='OrganizationRole' class='AdminLink'><?php echo _("Organization Role");?></div></td></tr>
					<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='Organization' class='AdminLink'><?php echo _("Organizations");?></div></td></tr>

				<?php } ?>

				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='PricingFormulaLink'><?php echo _("Pricing Formulas");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='PurchaseSite' class='AdminLink'><?php echo _("Purchasing Site");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='ResourceFormat' class='AdminLink'><?php echo _("Resource Format");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='ResourceType' class='AdminLink'><?php echo _("Resource Type");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='StorageLocation' class='AdminLink'><?php echo _("Storage Location");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' class='SubjectsAdminLink'><?php echo _("Subjects");?></div></td></tr>
				<tr><td><div class='adminMenuLink'><a href='javascript:void(0);' id='UserLimit' class='AdminLink'><?php echo _("User Limit");?></div></td></tr>
			</table>
		</td>
		<td class='adminRightPanel' style='width:530px;margin:0;'>
			<div style='margin-top:5px;' id='div_AdminContent'>
			<img src = "images/circle.gif" /><?php echo _("Loading...");?>
			</div>
			<div style='margin-top:5px;' class='smallDarkRedText' id='div_error'></div>

		</td>
		</tr>
		</table>



	</td>
	</tr>
	</table>

	<br />


	<script type="text/javascript" src="js/admin.js"></script>

<?php

//end else for admin
}else{
	echo _("You do not have permissions to access this screen.");
}

include 'templates/footer.php';
?>


