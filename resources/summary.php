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

	include_once('templates/header.php');
	?>
<main id="main-content">
	<article class='printContent'>
	
		<h2><?php echo $resource->titleText; ?></h2>

	<dl class="dl-grid dl-compact">
		<dt><?php echo _('Resource Type:'); ?></dt>
		<dd><?php echo $resourceFormat->shortName?></dd>

		<dt><?php echo _('Resource Format:'); ?></dt>
		<dd><?php echo $resourceType->shortName?></dd>

		<dt><?php echo _("Record ID:");?></dt>
		<dd><?php echo $resource->resourceID; ?></dd>

		<dt><?php echo _("Status:");?></dt>
		<dd><?php echo $status->shortName; ?></dd>

		<dt><?php echo _("Created:");?></dt>
		<dd class="byline">
				<?php
					$created_by = '';
					//since resources could be updated by other modules the user may or may not be set and may or may not have a user entry in this db
					if ($createUser->primaryKey){
						if ($createUser->firstName){
							$created_by = $createUser->firstName . " " . $createUser->lastName;
						}else{
							$created_by = $createUser->primaryKey;
						}
					}
					printf(_('%s by %s'), format_date($resource->createDate), $created_by);
				?>
		</dd>

		<?php
		if (!is_null_date($resource->updateDate)){
		?>
			<dt><?php echo _("Last Update:");?></dt>
			<dd class="byline">
				<?php
					$last_updated_by = '';
					//since resources could be updated by other modules the user may or may not be set and may or may not have a user entry in this db
					if ($updateUser->primaryKey){
						if ($updateUser->firstName){
							$last_updated_by = $updateUser->firstName . " " . $updateUser->lastName;
						}else{
							$last_updated_by = $updateUser->primaryKey;
						}
					}
					printf(_('%s by %s'), format_date($resource->updateDate), $last_updated_by);
				?>
			</dd>

		<?php
		}

		if ((count($parentResourceArray) > 0) || (count($childResourceArray) > 0)){ ?>
			<dt><?php echo _("Related Products:");?></dt>
			<dd>
			<ul class="unstyled">
			<?php

      foreach ($parentResourceArray as $parentResource){
				$parentResourceObj = new Resource(new NamedArguments(array('primaryKey' => $parentResource['relatedResourceID'])));
				echo "<li>" . sprintf(_('% (parent)'), $parentResourceObj->titleText) . "</li>";
			}

			foreach ($childResourceArray as $childResource){
				$childResourceObj = new Resource(new NamedArguments(array('primaryKey' => $childResource['resourceID'])));
				echo "<li>" . sprintf(_('% (child)'), $childResourceObj->titleText) . "</li>";
			}


			?>
			</ul>
			</dd>
		<?php }

		if ($resource->isbnOrISSN){
		?>
		<dt><?php echo _("ISSN / ISBN:");?></dt>
		<dd><?php echo $resource->isbnOrISSN; ?></dd>
		<?php
		}

		if (is_array($aliasArray) && count($aliasArray) > 0) {
		?>
		
		<dt><?php echo _("Aliases:");?></dt>
		<dd>
			<dl class='dl-grid'>
		<?php
			foreach ($aliasArray as $resourceAlias){
				echo "<dt>" . $resourceAlias['aliasTypeShortName'] . "</dt><dd>" . $resourceAlias['shortName'] . "</dd>";
			}
		?>
			</dl>
		</dd>

		<?php
		}


		if (is_array($orgArray) && count($orgArray) > 0) {
		?>


		<dt><?php echo _("Organizations:");?></dt>
		<dd>
			<dl class='dl-grid'>
			<?php
			foreach ($orgArray as $organization){
				echo "<dt>" . $organization['organizationRole'] . "</dt><dd>" . $organization['organization'] . "</dd>";
			}
			?>
			</dl>
		</dd>
		<?php
		}

		if ($resource->resourceURL) { ?>
			<dt><?php echo _("Resource URL:");?></dt>
			<dd><?php echo $resource->resourceURL; ?></dd>
		<?php
		}

		if ($resource->resourceAltURL) { ?>
			<dt><?php echo _("Alt URL:");?></dt>
			<dd><?php echo $resource->resourceAltURL; ?></dd>
		<?php
		}

		if ($resource->descriptionText){ ?>
			<dt><?php echo _("Description:");?></dt>
			<dd><?php echo nl2br($resource->descriptionText); ?></dd>
		<?php } ?>
		</dl>
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

	if (is_array($noteArray) && count($noteArray) > 0) {
	?>
			<h3><?php echo _("Additional Product Notes");?></h3>

			<?php foreach ($noteArray as $resourceNote){ ?>

				<h4><?php echo $resourceNote['noteTypeName']; ?>:</h4>
				<div>
					<?php echo nl2br($resourceNote['noteText']); ?>
					<p class="byline">
						<?php printf(_('%s by %s'),format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?>
					</p>
				</div>

			<?php }
			
	}
	?>

	<h3><?php echo _("Order");?></h3>
	<dl class="dl-grid dl-compact">
		
	<?php if ($resource->acquisitionTypeID) { ?>
		<dt><?php echo _("Acquisition Type:");?></dt>
		<dd><?php echo $acquisitionType->shortName; ?></dd>
	<?php } ?>

	<?php if ($resource->orderNumber) { ?>

		<dt><?php echo _("Order Number:");?></dt>
		<dd><?php echo $resource->orderNumber; ?></dd>

	<?php } ?>

	<?php if ($resource->systemNumber) { ?>

		<dt><?php echo _("System Number:");?></dt>
		<dd><?php echo $resource->systemNumber; ?></dd>

	<?php } ?>

	<?php if (is_array($purchaseSiteArray) && count($purchaseSiteArray) > 0) { ?>

		<dt><?php echo _("Purchasing Site:");?></dt>
		<dd><?php echo implode(", ", $purchaseSiteArray); ?></dd>

	<?php } ?>

	<?php if (is_array($authorizedSiteArray) && count($authorizedSiteArray) > 0) { ?>

		<dt><?php echo _("Authorized Sites:");?></dt>
		<dd><?php echo implode(", ", $authorizedSiteArray); ?></dd>

	<?php } ?>


	<?php if (!is_null_date($resourceAcquisition->subscriptionStartDate)) { ?>

	<dt><?php echo _("Sub Start:");?></dt>
	<dd><?php echo format_date($resourceAcquisition->subscriptionStartDate); ?></dd>

	<?php } ?>

	<?php if (!is_null_date($resourceAcquisition->subscriptionEndDate)) { ?>

	<dt><?php echo _("Current Sub End:");?></dt>
	<dd><?php echo format_date($resourceAcquisition->subscriptionEndDate); ?>&nbsp;&nbsp;
	<?php if ($resourceAcquisition->subscriptionAlertEnabledInd == "1") { echo "<i>"._("Expiration Alert Enabled")."</i>"; } ?>
	</dd>
	<?php } ?>

	</dl>

	<h3><?php echo _("Cost History");?></h3>
	<?php
	if (is_array($paymentArray) && count($paymentArray) > 0) {
		?>
		<dl class="dl-grid dl-compact">
		<?php
            foreach ($paymentArray as $payment){ ?>
            
            <?php if ($enhancedCostFlag){ ?>
                    <dt><?php echo _("Year:");?></dt>
                    <dd><?php echo $payment['year']; ?></dd>

                    <dt><?php echo _("Sub StartDate:");?></dt>
                    <dd><?php echo $payment['subscriptionStartDate']; ?></dd>

                    <dt><?php echo _("Sub EndDate:");?></dt>
                    <dd><?php echo $payment['subscriptionEndDate']; ?></dd>
       
             <?php } ?>
                    <dt><?php echo _("Fund:");?></dt>
                    <dd><?php echo $payment['fundName']; ?></dd>

            <?php if ($enhancedCostFlag){ ?>

                    <dt><?php echo _("Tax Excl.:");?></dt>
                    <dd><?php echo $payment['currencyCode'] . " " .integer_to_cost($payment['priceTaxExcluded']); ?></dd>

                    <dt><?php echo _("Tax Rate:");?></dt>
                    <dd><?php echo $payment['taxRate']/100 ."%"; ?></dd>

                    <dt><?php echo _("Tax Incl.:");?></dt>
                    <dd><?php echo $payment['currencyCode'] . " " .integer_to_cost($payment['priceTaxIncluded']); ?></dd>

            <?php } ?>
                    <dt><?php echo _("Payment:");?></dt>
                    <dd><?php echo $payment['currencyCode'] . " " . integer_to_cost($payment['paymentAmount']); ?></dd>

                    <dt><?php echo _("Order Type:");?></dt>
                    <dd><?php echo $payment['orderType']; ?></dd>
            <?php if ($enhancedCostFlag){ ?>
                    <dt><?php echo _("Details:");?></dt>
                    <dd><?php echo $payment['costDetails']; ?></dd>
             <?php } ?>

                    <dt><?php echo _("Note:");?></dt>
                    <dd><?php echo $payment['costNote']; ?></dd>

            <?php if ($enhancedCostFlag){ ?>
                    <dt><?php echo _("Invoice No.:");?></dt>
                    <dd><?php echo $payment['invoiceNum']; ?></dd>
            <?php } ?>


			</dl>
	<?php
	 }
	}else{
		echo "<p>"._("No payment information available.")."</p>";
	}
	?>


	<h3><?php echo _("License");?></h3>
	<dl class="dl-grid dl-compact">

	<dt><?php echo _("Status:");?></dt>
	

	<?php
	if (is_array($licenseStatusArray) && count($licenseStatusArray) > 0) {
		foreach ($licenseStatusArray as $licenseStatus){
			echo "<dd>";
			printf(_('% on <i>%d</i> by %s'), $licenseStatus['licenseStatus'], format_date($licenseStatus['licenseStatusChangeDate'], $licenseStatus['changeName']));
			echo "</dd>";
		}
	}else{
		echo "<dd>";
		echo "<i>"._("No license status information available.")."</i>";
		echo "</dd>";
	}

	?>
	
	<dt><?php echo _("Licenses:");?></dt>

	<?php

	if (is_array($licenseArray) && count($licenseArray) > 0) {
		foreach ($licenseArray as $license){
			echo "<dd>" . $license['license'] . "</dd>";
		}
	}else{
		echo "<dd><i>"._("No associated licenses available.")."</i></dd>";
	}

	?>
	</dl>


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

	if (is_array($noteArray) && count($noteArray) > 0) {
	?>
			<h3><?php echo _("Additional Acquisitions Notes");?></h3>
			<?php foreach ($noteArray as $resourceNote){ ?>
				
				<h4><?php echo $resourceNote['noteTypeName']; ?></h4>
				<div>
					<?php echo nl2br($resourceNote['noteText']); ?>
					<p class="byline">
						<?php 
						printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']);
						?>
					</p>
				</div>
			<?php } ?>
	<?php
	}
	?>


	<h3><?php echo _("Access Information");?></h3>

	<?php
		//If no access information is available, display that information
		if ((count($administeringSiteArray) == 0) && (!$authenticationType->shortName) && (!$resource->authenticationUserName) && (!$resource->authenticationPassword) && (!$userLimit->shortName) && (!$resource->registeredIPAddressException) && (!$storageLocation->shortName) && (!$accessMethod->shortName)){
			echo "<p><i>"._("No access information available.")."</i></p>";
		}
	?>
<dl class="dl-grid dl-compact">
	<?php if (is_array($administeringSiteArray) && count($administeringSiteArray) > 0) { ?>
		<dt><?php echo _("Administering Sites:");?></dt>
		<dd><?php echo implode(", ", $administeringSiteArray); ?></dd>
	<?php } ?>

	<?php if ($authenticationType->shortName) { ?>
		<dt><?php echo _("Authentication Type:");?></dt>
		<dd><?php echo $authenticationType->shortName; ?></dd>
	<?php } ?>

	<?php if (($resource->authenticationUserName) || ($resource->authenticationPassword)) { ?>
		<dt><?php echo _("Username / Password:");?></dt>
		<dd><?php echo $resource->authenticationUserName . " / " . $resource->authenticationPassword; ?></dd>
	<?php } ?>

	<?php if ($userLimit->shortName) { ?>
		<dt><?php echo _("Simultaneous User Limit:");?></dt>
		<dd><?php echo $userLimit->shortName; ?></dd>
	<?php } ?>


	<?php if ($resource->registeredIPAddressException){ ?>
		<dt><?php echo _("Registered IP Address:");?></dt>
		<dd><?php echo $resource->registeredIPAddressException; ?></dd>
	<?php } ?>


	<?php if ($storageLocation->shortName) { ?>
		<dt><?php echo _("Storage Location:");?></dt>
		<dd><?php echo $storageLocation->shortName; ?></dd>
	<?php } ?>

	<?php if ($accessMethod->shortName) { ?>
		<dt><?php echo _("Access Method:");?></dt>
		<dd><?php echo $accessMethod->shortName; ?></dd>
	<?php } ?>

	</dl>

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

	if (is_array($noteArray) && count($noteArray) > 0) {
	?>

			<h3><?php echo _("Additional Access Notes");?></h3>

			<?php foreach ($noteArray as $resourceNote){ ?>
				<h4><?php echo $resourceNote['noteTypeName']; ?>:</h4>
				<div>
					<?php echo nl2br($resourceNote['noteText']); ?>
					<p class="byline">
						<?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?>
					</p>
				</div>
			<?php }
			
	}
	?>

    <h3><?php echo _("Cataloging");?></h3>
    <?php if ($resourceAcquisition->hasCatalogingInformation()) { ?>
      <?php if ($resourceAcquisition->recordSetIdentifier) { ?>
      		<dt><?php echo _("Identifier:");?></dt>
      		<dd><?php echo $resourceAcquisition->recordSetIdentifier; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->bibSourceURL) { ?>
      		<dt><?php echo _("URL:");?></dt>
      		<dd><?php echo $resourceAcquisition->bibSourceURL; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->catalogingTypeID) {
    		$catalogingType = new CatalogingType(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingTypeID)));
    		?>
      		<dt><?php echo _("Cataloging Type:");?></dt>
      		<dd><?php echo $catalogingType->shortName; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->catalogingStatusID) {
    		$catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingStatusID)));
    		?>
      		<dt><?php echo _("Cataloging Status:");?></dt>
      		<dd><?php echo $catalogingStatus->shortName; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->numberRecordsAvailable) { ?>
      		<dt><?php echo _("# Records Available:");?></dt>
      		<dd><?php echo $resourceAcquisition->numberRecordsAvailable; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->numberRecordsLoaded) { ?>
      		<dt><?php echo _("# Records Loaded:");?></dt>
      		<dd><?php echo $resourceAcquisition->numberRecordsLoaded; ?></dd>
    	<?php } ?>
    	<?php if ($resourceAcquisition->hasOclcHoldings) { ?>
      		<dt><?php echo _("OCLC Holdings:");?></dt>
      		<dd><?php echo $resourceAcquisition->hasOclcHoldings ? _('Yes') : _('No'); ?></dd>
    	<?php } ?>
			</dl>
    <?php } else { ?>
        
          <p><?php echo _("No cataloging information available.");?></p>
    <?php }
		

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

	if (is_array($noteArray) && count($noteArray) > 0) {
	?>

			<h3><?php echo _("Additional Cataloging Notes");?></h3>
			<?php foreach ($noteArray as $resourceNote){ ?>

				<h4><?php echo $resourceNote['noteTypeName']; ?>:</h4>
				<div>
					<?php echo nl2br($resourceNote['noteText']); ?>
					<p class="byline">
						<?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?>
					</p>
			</div>
			<?php }
			
	}
	?>



	</div>
</article>
</main>
<?php
}
include_once('templates/footer.php');
?>
</body>
</html>