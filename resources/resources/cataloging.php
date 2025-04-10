<?php

/*
**************************************************************************************************************************
** CORAL Resources Module v. 1.0
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

include_once '../directory.php';
include_once '../user.php';

$config = new Configuration();
$util = new Utility();


$config = new Configuration();
$resourceID = $_GET['resourceID'];
$resourceAcquisitionID = $_GET['resourceAcquisitionID'];
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$resourceAcquisition = new ResourceAcquisition(new NamedArguments(array('primaryKey' => $resourceAcquisitionID)));

$orderType = new OrderType(new NamedArguments(array('primaryKey' => $resourceAcquisition->orderTypeID)));
$acquisitionType = new AcquisitionType(new NamedArguments(array('primaryKey' => $resourceAcquisition->acquisitionTypeID)));

//get purchase sites
$sanitizedInstance = array();
$instance = new PurchaseSite();
$purchaseSiteArray = array();
foreach ($resourceAcquisition->getPurchaseSites() as $instance) {
$purchaseSiteArray[]=$instance->shortName;
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

?>
	<div class="header">
		<h3><?php echo _("Cataloging");?></h3>

		<?php if ($user->canEdit()){ ?>
			<span class="addElement"><a href='javascript:void(0)' onclick='javascript:myDialog("resources/cataloging_edit.php?resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>", 400,800)' class='thickbox' id='editOrder'><img src='images/edit.gif' alt='edit' title='<?php echo _("edit cataloging details");?>'></a></span>
		<?php } ?>
	</div>
  
  <?php if ($resourceAcquisition->hasCatalogingInformation()) { ?>
		<dl class='dl-grid'>
    <?php if ($resourceAcquisition->recordSetIdentifier) { ?>
  	
    	<dt><?php echo _("Identifier:");?></dt>
    	<dd><?php echo $resourceAcquisition->recordSetIdentifier ?></dd>
  	
  	<?php } ?>
  	<?php if ($resourceAcquisition->bibSourceURL) { ?>
  	
    	<dt><?php echo _("Source URL:");?></dt>
    	<dd><?php echo $resourceAcquisition->bibSourceURL ?><?php if ($resourceAcquisition->bibSourceURL) { ?> &nbsp;&nbsp;<a href='<?php echo $resourceAcquisition->bibSourceURL; ?>' <?php echo getTarget(); ?>><img src='images/arrow-up-right.gif' alt='Visit Source URL' title='<?php echo _("Visit Source URL");?>' style='vertical-align:top;'></a><?php } ?></dd>
  	
  	<?php } ?>
  	<?php if ($resourceAcquisition->catalogingTypeID) {
      $catalogingType = new CatalogingType(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingTypeID)));
      ?>

    	<dt><?php echo _("Cataloging Type:");?></dt>
    	<dd><?php echo $catalogingType->shortName ?></dd>
  	
  	<?php } ?>
  	<?php if ($resourceAcquisition->catalogingStatusID) {
      $catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingStatusID)));
      ?>
  	
    	<dt><?php echo _("Cataloging Status:");?></dt>
    	<dd><?php echo $catalogingStatus->shortName ?></dd>
  	
  	<?php } ?>
  	<?php if ($resourceAcquisition->numberRecordsAvailable) { ?>
    	<dt><?php echo _("# Records Available:");?></dt>
    	<dd><?php echo $resourceAcquisition->numberRecordsAvailable ?></dd>
  	
  	<?php } ?>
  	<?php if ($resourceAcquisition->numberRecordsLoaded) { ?>
  	
    	<dt><?php echo _("# Records Loaded:");?></dt>
    	<dd><?php echo $resourceAcquisition->numberRecordsLoaded ?></dd>
  	
  	<?php } ?>
  	
    	<dt><?php echo _("OCLC Holdings:");?></dt>
    	<dd><?php echo $resourceAcquisition->hasOclcHoldings ? _('Yes') : _('No') ?></dd>
	</dl>
  <?php } else { ?>
    
      <p colspan="2">
        <em><?php echo _("No cataloging information available.");?></em>
      </p>
  <?php } ?>

<?php if ($user->canEdit()){ ?>
<p><a href='javascript:void(0)' onclick='javascript:myDialog("resources/cataloging_edit.php?resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>",400,800)' class='thickbox'><?php echo _("edit cataloging details");?></a></p>
<?php } ?>

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

if (is_array($noteArray) && count($noteArray) > 0) {
?>
	<h3>
		<?php echo _("Additional Notes");?>

			<span class="addElement">

		<?php if ($user->canEdit()){?>
		<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=",300,500)' class='thickbox'><?php echo "<div class='addIconTab'><img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' /></div>";?></a>
		<?php } ?>
		</span>
	</h3>
	
	<dl class='dl-grid'>
	<?php foreach ($noteArray as $resourceNote){ ?>
		
		<dt><?php echo $resourceNote['noteTypeName']; ?>
			<?php if ($user->canEdit()){?>
			<p class="actions">
				<a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>",300,500)' class='thickbox'><img src='images/edit.gif' alt='edit' title='<?php echo _("edit note");?>'></a>
				<a href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Cataloging'><img src='images/cross.gif'  alt='remove note' title='<?php echo _("remove note");?>'></a>
			</p>
			<?php } ?>
		</dt>
		<dd><?php echo nl2br($resourceNote['noteText']); ?>
			<p class="byline"><?php printf(_("%s by %s"), format_date($resourceNote['updateDate']), $resourceNote['updateUser']); ?></p>
		</dd>
		
	<?php } ?>
</dl>
<?php
}else{
if ($user->canEdit()){
?>
	<p><a href='javascript:void(0)' onclick='javascript:myDialog("ajax_forms.php?action=getNoteForm&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=", 300,500)' class='thickbox'><?php echo _("add note");?></a></p>
<?php
}
}
?>
