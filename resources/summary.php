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

$util = new Utility();

$resourceID = $_GET['resourceID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

//used to get default currency
		$config = new Configuration();
		$enhancedCostFlag = ($config->settings->enhancedCostHistory == 'Y') ? 1 : 0;

//if this is a valid resource
if ($resource->titleText){


	//set this to turn off displaying the title header in header.php
	$pageTitle=$resource->titleText . _(" Summary");


	$resourceFormat = new ResourceFormat(new NamedArguments(array('primaryKey' => $resource->resourceFormatID)));
	$resourceType = new ResourceType(new NamedArguments(array('primaryKey' => $resource->resourceTypeID)));
	$status = new Status(new NamedArguments(array('primaryKey' => $resource->statusID)));

	$createUser = new User(new NamedArguments(array('primaryKey' => $resource->createLoginID)));
	$updateUser = new User(new NamedArguments(array('primaryKey' => $resource->updateLoginID)));

	//get parents resources
	$sanitizedInstance = array();
	$instance = new Resource();
	$parentResourceArray = array();
  foreach ($resource->getParentResources() as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;
		array_push($parentResourceArray, $sanitizedInstance);
	}



	//get children resources
	$childResourceArray = array();
	foreach ($resource->getChildResources() as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		array_push($childResourceArray, $sanitizedInstance);
	}


	//get aliases
	$sanitizedInstance = array();
	$instance = new Alias();
	$aliasArray = array();
	foreach ($resource->getAliases() as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		$aliasType = new AliasType(new NamedArguments(array('primaryKey' => $instance->aliasTypeID)));
		$sanitizedInstance['aliasTypeShortName'] = $aliasType->shortName;

		array_push($aliasArray, $sanitizedInstance);
	}

	//get organizations (already returned in an array)
	$orgArray = $resource->getOrganizationArray();

	$orderType = new OrderType(new NamedArguments(array('primaryKey' => $resource->orderTypeID)));
	$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resource->acquisitionTypeID)));

	//get purchase sites
	$sanitizedInstance = array();
	$instance = new PurchaseSite();
	$purchaseSiteArray = array();
	foreach ($resourceAcquisition->getPurchaseSites() as $instance) {
		$purchaseSiteArray[]=$instance->shortName;
	}

	//get authorized sites
	$sanitizedInstance = array();
	$instance = new PurchaseSite();
	$authorizedSiteArray = array();
	foreach ($resourceAcquisition->getAuthorizedSites() as $instance) {
		$authorizedSiteArray[]=$instance->shortName;
	}

	//get payments
	$sanitizedInstance = array();
	$instance = new ResourcePayment();
	$paymentArray = array();
	foreach ($resourceAcquisition->getResourcePayments() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$selector = new User(new NamedArguments(array('primaryKey' => $instance->selectorLoginID)));
			$sanitizedInstance['selectorName'] = $selector->firstName . " " . $selector->lastName;

			$orderType = new OrderType(new NamedArguments(array('primaryKey' => $instance->orderTypeID)));
			$sanitizedInstance['orderType'] = $orderType->shortName;

                        $costDetails = new CostDetails(new NamedArguments(array('primaryKey' => $instance->costDetailsID)));
                        $sanitizedInstance['costDetails'] = $costDetails->shortName;

                        $fund=new Fund(new NamedArguments(array('primaryKey' => $instance->fundID)));
                        $sanitizedInstance['fundName'] = $fund->shortName;

                        array_push($paymentArray, $sanitizedInstance);

	}


	//get license statuses
	$sanitizedInstance = array();
	$instance = new ResourceLicenseStatus();
	$licenseStatusArray = array();
	foreach ($resourceAcquisition->getResourceLicenseStatuses() as $instance) {
			foreach (array_keys($instance->attributeNames) as $attributeName) {
				$sanitizedInstance[$attributeName] = $instance->$attributeName;
			}

			$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

			$changeUser = new User(new NamedArguments(array('primaryKey' => $instance->licenseStatusChangeLoginID)));
			if (($changeUser->firstName) || ($changeUser->lastName)) {
				$sanitizedInstance['changeName'] = $changeUser->firstName . " " . $changeUser->lastName;
			}else{
				$sanitizedInstance['changeName'] = $instance->licenseStatusChangeLoginID;
			}

			$licenseStatus = new LicenseStatus(new NamedArguments(array('primaryKey' => $instance->licenseStatusID)));
			$sanitizedInstance['licenseStatus'] = $licenseStatus->shortName;


			array_push($licenseStatusArray, $sanitizedInstance);

	}



	//get licenses (already returned in array)
	$licenseArray = $resourceAcquisition->getLicenseArray();

	$userLimit = new UserLimit(new NamedArguments(array('primaryKey' => $resource->userLimitID)));
	$storageLocation = new StorageLocation(new NamedArguments(array('primaryKey' => $resource->storageLocationID)));
	$accessMethod = new AccessMethod(new NamedArguments(array('primaryKey' => $resource->accessMethodID)));
	$authenticationType = new AuthenticationType(new NamedArguments(array('primaryKey' => $resource->authenticationTypeID)));

	//get administering sites
	$sanitizedInstance = array();
	$instance = new AdministeringSite();
	$administeringSiteArray = array();
	foreach ($resourceAcquisition->getAdministeringSites() as $instance) {
		$administeringSiteArray[]=$instance->shortName;
	}

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="public">

	<head>
	<title>Resources Module - <?php echo $pageTitle; ?></title>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="print" />
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
	<link rel="SHORTCUT ICON" href="images/favicon.ico" />

	</head>
	<body>


	<div class='printContent'>
	<table class='linedFormTable' style='width:510px;'>
		<tr>
		<th colspan='2' style='margin-top: 7px; margin-bottom: 5px;'>
		<span style='float:left; vertical-align:top; margin-left:3px;'><span style='font-weight:bold;font-size:120%;margin-right:8px;'><?php echo $resource->titleText; ?></span><span style='font-weight:normal;font-size:100%;'><?php echo $resourceFormat->shortName . " " . $resourceType->shortName; ?></span></span>
		</th>
		</tr>

		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Record ID:");?></td>
		<td style='width:335px;'><?php echo $resource->resourceID; ?></td>
		</tr>


		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Status:");?></td>
		<td style='width:335px;'><?php echo $status->shortName; ?></td>
		</tr>

		<tr>
		<td style='vertical-align:top;width:150px;'>
		<?php echo _("Created:");?>
		</td>
		<td>
		<i>
		<?php
			echo format_date($resource->createDate);
			//since resources could be created by other modules the user may or may not be set and may or may not have a user entry in this db
			if ($createUser->primaryKey){
				echo _(" by ");
				if ($createUser->firstName){
					echo $createUser->firstName . " " . $createUser->lastName;
				}else{
					echo $createUser->primaryKey;
				}
			}
		?>
		</i>
		</td>
		</tr>

		<?php
		if (!is_null_date($resource->updateDate)){
		?>

			<tr>
			<td style='vertical-align:top;width:150px;'>
			<?php echo _("Last Update:");?>
			</td>
			<td>
			<i>
			<?php
				echo format_date($resource->updateDate);
				//since resources could be updated by other modules the user may or may not be set and may or may not have a user entry in this db
				if ($updateUser->primaryKey){
					echo _(" by ");
					if ($updateUser->firstName){
						echo $updateUser->firstName . " " . $updateUser->lastName;
					}else{
						echo $updateUser->primaryKey;
					}
				}
			?>
			</i>
			</td>
			</tr>

		<?php
		}

		if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
			<tr>
			<td style='vertical-align:top;width:150px;'><?php echo _("Related Products:");?></td>
			<td>
			<?php

      foreach ($parentResourceArray as $parentResource){
				$parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
				echo $parentResourceObj->titleText . "&nbsp;&nbsp;"._("(Parent)")."<br/>";
			}

			foreach ($childResourceArray as $childResource){
				$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
				echo "<span style='float: left;'>" . $childResourceObj->titleText . "&nbsp;&nbsp;(child)</span>";
			}


			?>
			</td>
			</tr>
		<?php }

		if ($resource->isbnOrISSN){
		?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("ISSN / ISBN:");?></td>
		<td style='width:335px;'><?php echo $resource->isbnOrISSN; ?></td>
		</tr>
		<?php
		}

		if (count($aliasArray) > 0){
		?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Aliases:");?></td>
		<td>
		<?php
			foreach ($aliasArray as $resourceAlias){
				echo "\n<span style='float: left; width:95px;'>" . $resourceAlias['aliasTypeShortName'] . ":</span><span style='width:270px;'>" . $resourceAlias['shortName'] . "</span><br />";
			}
		?>
		</td>
		</tr>
		<?php
		}


		if (count($orgArray) > 0){
		?>

		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Organizations:");?></td>
		<td>

			<?php
			foreach ($orgArray as $organization){
				//if organizations is installed provide a link
				if ($config->settings->organizationsModule == 'Y'){
					echo "<span style='float:left; width:70px;'>" . $organization['organizationRole'] . ":</span><span style='width:270px;'>" . $organization['organization'] . "</span><br />";
				}else{
					echo "<span style='float:left; width:70px;'>" . $organization['organizationRole'] . ":</span><span style='width:270px;'>" . $organization['organization'] . "</span><br />";
				}
			}
			?>
		</td>
		</tr>

		<?php
		}

		if ($resource->resourceURL) { ?>
			<tr>
			<td style='vertical-align:top;width:150px;'><?php echo _("Resource URL:");?></td>
			<td><?php echo $resource->resourceURL; ?></td>
			</tr>
		<?php
		}

		if ($resource->resourceAltURL) { ?>
			<tr>
			<td style='vertical-align:top;width:150px;'><?php echo _("Alt URL:");?></td>
			<td><?php echo $resource->resourceAltURL; ?></td>
			</tr>
		<?php
		}

		if ($resource->descriptionText){ ?>
			<tr>
			<td style='vertical-align:top;width:150px;'><?php echo _("Description:");?></td>
			<td><?php echo nl2br($resource->descriptionText); ?></td>
			</tr>
		<?php } ?>


	</table>

	<br />

	<?php

	//get notes for this tab
	$sanitizedInstance = array();
	$noteArray = array();

	foreach ($resource->getNotes('Product') as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		$updateUser = new User(new NamedArguments(array('primaryKey' => $instance->updateLoginID)));

		//in case this user doesn't have a first / last name set up
		if (($updateUser->firstName != '') || ($updateUser->lastName != '')){
			$sanitizedInstance['updateUser'] = $updateUser->firstName . " " . $updateUser->lastName;
		}else{
			$sanitizedInstance['updateUser'] = $instance->updateLoginID;
		}

		$noteType = new NoteType(new NamedArguments(array('primaryKey' => $instance->noteTypeID)));

		if (!$noteType->shortName){
			$sanitizedInstance['noteTypeName'] = 'General Note';
		}else{
			$sanitizedInstance['noteTypeName'] = $noteType->shortName;
		}

		array_push($noteArray, $sanitizedInstance);
	}

	if (count($noteArray) > 0){
	?>
		<table class='linedFormTable' style='width:510px;'>
			<tr>
			<th colspan='2'><?php echo _("Additional Product Notes");?></th>
			</tr>
			<?php foreach ($noteArray as $resourceNote){ ?>
				<tr>
				<td style='width:150px;'><?php echo $resourceNote['noteTypeName']; ?>:</td>
				<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}
	?>


	<br />


	<table class='linedFormTable' style='width:510px;'>
	<tr>
	<th colspan='2' style='vertical-align:bottom;'>
	<span style='float:left;vertical-align:bottom;'><?php echo _("Order");?></span>

	</th>
	</tr>

	<?php if ($resource->acquisitionTypeID) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Acquisition Type:");?></td>
		<td><?php echo $acquisitionType->shortName; ?></td>
		</tr>
	<?php } ?>

	<?php if ($resource->orderNumber) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Order Number:");?></td>
		<td><?php echo $resource->orderNumber; ?></td>
		</tr>
	<?php } ?>

	<?php if ($resource->systemNumber) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("System Number:");?></td>
		<td><?php echo $resource->systemNumber; ?></td>
		</tr>
	<?php } ?>

	<?php if (count($purchaseSiteArray) > 0) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Purchasing Site:");?></td>
		<td><?php echo implode(", ", $purchaseSiteArray); ?></td>
		</tr>
	<?php } ?>

	<?php if (count($authorizedSiteArray) > 0) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Authorized Sites:");?></td>
		<td><?php echo implode(", ", $authorizedSiteArray); ?></td>
		</tr>
	<?php } ?>


	<?php if (!is_null_date($resourceAcquisition->subscriptionStartDate)) { ?>
	<tr>
	<td style='vertical-align:top;width:150px;'><?php echo _("Sub Start:");?></td>
	<td><?php echo format_date($resourceAcquisition->subscriptionStartDate); ?></td>
	</tr>
	<?php } ?>

	<?php if (!is_null_date($resourceAcquisition->subscriptionEndDate)) { ?>
	<tr>
	<td style='vertical-align:top;width:150px;'><?php echo _("Current Sub End:");?></td>
	<td><?php echo format_date($resourceAcquisition->subscriptionEndDate); ?>&nbsp;&nbsp;
	<?php if ($resourceAcquisition->subscriptionAlertEnabledInd == "1") { echo "<i>"._("Expiration Alert Enabled")."</i>"; } ?>
	</td>
	</tr>
	<?php } ?>

	</table>
	<br />

	<table class='linedFormTable' style='width:510px;'>
	<tr>
        <th colspan='2' style='vertical-align:bottom;'>
	<span style='float:left;vertical-align:bottom;'><?php echo _("Cost History");?></span>
	</th>
	</tr>

	<?php
	if (count($paymentArray) > 0){
            foreach ($paymentArray as $payment){ ?>
              <tr><td style='vertical-align:top;width:150px;' colspan='2'></td></tr>
            <?php if ($enhancedCostFlag){ ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Year:");?></td>
                    <td><?php echo $payment['year']; ?></td>
                </tr>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Sub StartDate:");?></td>
                    <td><?php echo $payment['subscriptionStartDate']; ?></td>
                </tr>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Sub EndDate:");?></td>
                    <td><?php echo $payment['subscriptionEndDate']; ?></td>
                </tr>
             <?php } ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Fund:");?></td>
                    <td><?php echo $payment['fundName']; ?></td>
                </tr>
            <?php if ($enhancedCostFlag){ ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Tax Excl.:");?></td>
                    <td><?php echo $payment['currencyCode'] . " " .integer_to_cost($payment['priceTaxExcluded']); ?></td>
                </tr>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Tax Rate:");?></td>
                    <td><?php echo $payment['taxRate']/100 ."%"; ?></td>
                </tr>

                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Tax Incl.:");?></td>
                    <td><?php echo $payment['currencyCode'] . " " .integer_to_cost($payment['priceTaxIncluded']); ?></td>
                </tr>
            <?php } ?>

                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Payment:");?></td>
                    <td><?php echo $payment['currencyCode'] . " " . integer_to_cost($payment['paymentAmount']); ?></td>
                </tr>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Order Type:");?></td>
                    <td><?php echo $payment['orderType']; ?></td>
                </tr>
            <?php if ($enhancedCostFlag){ ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Details:");?></td>
                    <td><?php echo $payment['costDetails']; ?></td>
		</tr>
             <?php } ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Note:");?></td>
                    <td><?php echo $payment['costNote']; ?></td>
		</tr>
            <?php if ($enhancedCostFlag){ ?>
                <tr>
                    <td style='vertical-align:top;width:150px;'><?php echo _("Invoice No.:");?></td>
                    <td><?php echo $payment['invoiceNum']; ?></td>
                </tr>
            <?php } ?>


	<?php
	 }
	}else{
		echo "<tr><td colspan='3'><i>"._("No payment information available.")."</i></td></tr>";
	}
	?>

	</table>
	<br />

	<table class='linedFormTable' style='width:510px;'>
	<tr>
	<th colspan='2'>
	<span style='float:left;vertical-align:bottom;'><?php echo _("License");?></span>
	</th>
	</tr>

	<tr>
	<td style='vertical-align:top;width:150px;'><?php echo _("Status:");?></td>
	<td>

	<?php
	if (count($licenseStatusArray) > 0){
		foreach ($licenseStatusArray as $licenseStatus){
			echo $licenseStatus['licenseStatus'] . _(" on ")."<i>" . format_date($licenseStatus['licenseStatusChangeDate']) . _(" by ") . $licenseStatus['changeName'] . "</i><br />";
		}
	}else{
		echo "<i>"._("No license status information available.")."</i>";
	}

	?>
	</td>
	</tr>

	<tr>
	<td style='vertical-align:top;width:150px;'><?php echo _("Licenses:");?></td>
	<td>
	<?php

	if (count($licenseArray) > 0){
		foreach ($licenseArray as $license){
			echo $license['license'] . "<br />";
		}
	}else{
		echo "<i>"._("No associated licenses available.")."</i>";
	}

	?>


	</td>
	</tr>

	</table>


	<?php

	//get notes for this tab
	$sanitizedInstance = array();
	$noteArray = array();
	foreach ($resourceAcquisition->getNotes('Acquisitions') as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		$updateUser = new User(new NamedArguments(array('primaryKey' => $instance->updateLoginID)));

		//in case this user doesn't have a first / last name set up
		if (($updateUser->firstName != '') || ($updateUser->lastName != '')){
			$sanitizedInstance['updateUser'] = $updateUser->firstName . " " . $updateUser->lastName;
		}else{
			$sanitizedInstance['updateUser'] = $instance->updateLoginID;
		}

		$noteType = new NoteType(new NamedArguments(array('primaryKey' => $instance->noteTypeID)));
		if (!$noteType->shortName){
			$sanitizedInstance['noteTypeName'] = 'General Note';
		}else{
			$sanitizedInstance['noteTypeName'] = $noteType->shortName;
		}

		array_push($noteArray, $sanitizedInstance);
	}

	if (count($noteArray) > 0){
	?>
		<table class='linedFormTable' style='width:510px;'>
			<tr>
			<th colspan='2'><?php echo _("Additional Acquisitions Notes");?></th>
			</th>&nbsp;
			</tr>
			<?php foreach ($noteArray as $resourceNote){ ?>
				<tr>
				<td style='width:150px;'><?php echo $resourceNote['noteTypeName']; ?></td>

				</td>
				<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}
	?>

	<br />


	<table class='linedFormTable' style='width:510px;'>
	<tr>
	<th colspan='2'>
	<span style='float:left;vertical-align:bottom;'><?php echo _("Access Information");?></span>
	</th>
	</tr>


	<?php
		//If no access information is available, display that information
		if ((count($administeringSiteArray) == 0) && (!$authenticationType->shortName) && (!$resource->authenticationUserName) && (!$resource->authenticationPassword) && (!$userLimit->shortName) && (!$resource->registeredIPAddressException) && (!$storageLocation->shortName) && (!$accessMethod->shortName)){
			echo "<tr><td colspan='2'><i>"._("No access information available.")."</i></td></tr>";
		}
	?>

	<?php if (count($administeringSiteArray) > 0) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Administering Sites:");?></td>
		<td><?php echo implode(", ", $administeringSiteArray); ?></td>
		</tr>
	<?php } ?>

	<?php if ($authenticationType->shortName) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Authentication Type:");?></td>
		<td><?php echo $authenticationType->shortName; ?></td>
		</tr>
	<?php } ?>

	<?php if (($resource->authenticationUserName) || ($resource->authenticationPassword)) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Username / Password:");?></td>
		<td><?php echo $resource->authenticationUserName . " / " . $resource->authenticationPassword; ?></td>
		</tr>
	<?php } ?>

	<?php if ($userLimit->shortName) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Simultaneous User Limit:");?></td>
		<td><?php echo $userLimit->shortName; ?></td>
		</tr>
	<?php } ?>


	<?php if ($resource->registeredIPAddressException){ ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Registered IP Address:");?></td>
		<td style='width:310px;'><?php echo $resource->registeredIPAddressException; ?></td>
		</tr>
	<?php } ?>


	<?php if ($storageLocation->shortName) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Storage Location:");?></td>
		<td><?php echo $storageLocation->shortName; ?></td>
		</tr>
	<?php } ?>

	<?php if ($accessMethod->shortName) { ?>
		<tr>
		<td style='vertical-align:top;width:150px;'><?php echo _("Access Method:");?></td>
		<td><?php echo $accessMethod->shortName; ?></td>
		</tr>
	<?php } ?>

	</table>

	<br />

	<?php


	//get notes for this tab
	$sanitizedInstance = array();
	$noteArray = array();
	foreach ($resourceAcquisition->getNotes('Access') as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		$updateUser = new User(new NamedArguments(array('primaryKey' => $instance->updateLoginID)));

		//in case this user doesn't have a first / last name set up
		if (($updateUser->firstName != '') || ($updateUser->lastName != '')){
			$sanitizedInstance['updateUser'] = $updateUser->firstName . " " . $updateUser->lastName;
		}else{
			$sanitizedInstance['updateUser'] = $instance->updateLoginID;
		}

		$noteType = new NoteType(new NamedArguments(array('primaryKey' => $instance->noteTypeID)));
		if (!$noteType->shortName){
			$sanitizedInstance['noteTypeName'] = 'General Note';
		}else{
			$sanitizedInstance['noteTypeName'] = $noteType->shortName;
		}

		array_push($noteArray, $sanitizedInstance);
	}

	if (count($noteArray) > 0){
	?>
		<table class='linedFormTable' style='width:510px;'>
			<tr>
			<th colspan='2'><?php echo _("Additional Access Notes");?></th>
			</tr>
			<?php foreach ($noteArray as $resourceNote){ ?>
				<tr>
				<td style='width:150px;'><?php echo $resourceNote['noteTypeName']; ?>:</td>
				<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}
	?>

  <table class='linedFormTable' style='width:510px;'>
    <tr>
      <th colspan='2' style='vertical-align:bottom;'>
        <span style='float:left;vertical-align:bottom;'><?php echo _("Cataloging");?></span>
      </th>
    </tr>
    <?php if ($resourceAcquisition->hasCatalogingInformation()) { ?>
      <?php if ($resourceAcquisition->recordSetIdentifier) { ?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("Identifier:");?></td>
      		<td><?php echo $resourceAcquisition->recordSetIdentifier; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->bibSourceURL) { ?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("URL:");?></td>
      		<td><?php echo $resourceAcquisition->bibSourceURL; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->catalogingTypeID) {
    		$catalogingType = new CatalogingType(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingTypeID)));
    		?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("Cataloging Type:");?></td>
      		<td><?php echo $catalogingType->shortName; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->catalogingStatusID) {
    		$catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingStatusID)));
    		?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("Cataloging Status:");?></td>
      		<td><?php echo $catalogingStatus->shortName; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->numberRecordsAvailable) { ?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("# Records Available:");?></td>
      		<td><?php echo $resourceAcquisition->numberRecordsAvailable; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->numberRecordsLoaded) { ?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("# Records Loaded:");?></td>
      		<td><?php echo $resourceAcquisition->numberRecordsLoaded; ?></td>
    		</tr>
    	<?php } ?>
    	<?php if ($resourceAcquisition->hasOclcHoldings) { ?>
    		<tr>
      		<td style='vertical-align:top;width:150px;'><?php echo _("OCLC Holdings:");?></td>
      		<td><?php echo $resourceAcquisition->hasOclcHoldings ? _('Yes') : _('No'); ?></td>
    		</tr>
    	<?php } ?>
    <?php } else { ?>
      <tr>
        <td colspan="2">
          <em><?php echo _("No cataloging information available.");?></em>
        </td>
      </tr>
    <?php } ?>
  </table>

  <br />

	<?php


	//get notes for this tab
	$sanitizedInstance = array();
	$noteArray = array();
	foreach ($resourceAcquisition->getNotes('Cataloging') as $instance) {
		foreach (array_keys($instance->attributeNames) as $attributeName) {
			$sanitizedInstance[$attributeName] = $instance->$attributeName;
		}

		$sanitizedInstance[$instance->primaryKeyName] = $instance->primaryKey;

		$updateUser = new User(new NamedArguments(array('primaryKey' => $instance->updateLoginID)));

		//in case this user doesn't have a first / last name set up
		if (($updateUser->firstName != '') || ($updateUser->lastName != '')){
			$sanitizedInstance['updateUser'] = $updateUser->firstName . " " . $updateUser->lastName;
		}else{
			$sanitizedInstance['updateUser'] = $instance->updateLoginID;
		}

		$noteType = new NoteType(new NamedArguments(array('primaryKey' => $instance->noteTypeID)));
		if (!$noteType->shortName){
			$sanitizedInstance['noteTypeName'] = 'General Note';
		}else{
			$sanitizedInstance['noteTypeName'] = $noteType->shortName;
		}

		array_push($noteArray, $sanitizedInstance);
	}

	if (count($noteArray) > 0){
	?>
		<table class='linedFormTable' style='width:510px;'>
			<tr>
			<th colspan='2'><?php echo _("Additional Cataloging Notes");?></th>
			</tr>
			<?php foreach ($noteArray as $resourceNote){ ?>
				<tr>
				<td style='width:150px;'><?php echo $resourceNote['noteTypeName']; ?>:</td>
				<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
				</tr>
			<?php } ?>
		</table>
	<?php
	}
	?>



	</div>
	</body>
	</html>

<?php
}
?>
