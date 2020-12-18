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
<table class='linedFormTable'>
  <tr>
    <th colspan='2' style='vertical-align:bottom;'>
      <span style='float:left;vertical-align:bottom;'><?php echo _("Cataloging");?></span>

      <?php if ($user->canEdit()){ ?>
      	<span style='float:right;vertical-align:bottom;'><a href='resources/cataloging_edit.php?height=300&width=730&modal=true&resourceID=<?php echo $resourceID; ?>' class='thickbox' id='editOrder'><img src='images/edit.gif' alt='edit' title='<?php echo _("edit order information");?>'></a></span>
      <?php } ?>

    </th>
  </tr>
  <?php if ($resourceAcquisition->hasCatalogingInformation()) { ?>
    <?php if ($resourceAcquisition->recordSetIdentifier) { ?>
  	<tr>
    	<td style='vertical-align:top;width:130px;'><?php echo _("Identifier:");?></td>
    	<td style='width:350px;'><?php echo $resourceAcquisition->recordSetIdentifier ?></td>
  	</tr>
  	<?php } ?>
  	<?php if ($resourceAcquisition->bibSourceURL) { ?>
  	<tr>
    	<td style='vertical-align:top;width:130px;'><?php echo _("Source URL:");?></td>
    	<td style='width:350px;'><?php echo $resourceAcquisition->bibSourceURL ?><?php if ($resourceAcquisition->bibSourceURL) { ?> &nbsp;&nbsp;<a href='<?php echo $resourceAcquisition->bibSourceURL; ?>' target='_blank'><img src='images/arrow-up-right.gif' alt='Visit Source URL' title='<?php echo _("Visit Source URL");?>' style='vertical-align:top;'></a><?php } ?></td>
  	</tr>
  	<?php } ?>
  	<?php if ($resourceAcquisition->catalogingTypeID) {
      $catalogingType = new CatalogingType(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingTypeID)));
      ?>
  	<tr>
    	<td style='vertical-align:top;width:130px;'><?php echo _("Cataloging Type:");?></td>
    	<td style='width:350px;'><?php echo $catalogingType->shortName ?></td>
  	</tr>
  	<?php } ?>
  	<?php if ($resourceAcquisition->catalogingStatusID) {
      $catalogingStatus = new CatalogingStatus(new NamedArguments(array('primaryKey' => $resourceAcquisition->catalogingStatusID)));
      ?>
  	<tr>
    	<td style='vertical-align:top;width:130px;'><?php echo _("Cataloging Status:");?></td>
    	<td style='width:350px;'><?php echo $catalogingStatus->shortName ?></td>
  	</tr>
  	<?php } ?>
  	<?php if ($resourceAcquisition->numberRecordsAvailable) { ?>
  	<tr title="<?php echo _("Number of Records Available");?>">
    	<td style='vertical-align:top;width:130px;'><?php echo _("# Records Available:");?></td>
    	<td style='width:350px;'><?php echo $resourceAcquisition->numberRecordsAvailable ?></td>
  	</tr>
  	<?php } ?>
  	<?php if ($resourceAcquisition->numberRecordsLoaded) { ?>
  	<tr title="<?php echo _("Number of Records Loaded");?>">
    	<td style='vertical-align:top;width:130px;'><?php echo _("# Records Loaded:");?></td>
    	<td style='width:350px;'><?php echo $resourceAcquisition->numberRecordsLoaded ?></td>
  	</tr>
  	<?php } ?>
  	<tr>
    	<td style='vertical-align:top;width:130px;'><?php echo _("OCLC Holdings:");?></td>
    	<td style='width:350px;'><?php echo $resourceAcquisition->hasOclcHoldings ? _('Yes') : _('No') ?></td>
  	</tr>
  <?php } else { ?>
    <tr>
      <td colspan="2">
        <em><?php echo _("No cataloging information available.");?></em>
      </td>
    </tr>
  <?php } ?>
</table>
<?php if ($user->canEdit()){ ?>
<a href='resources/cataloging_edit.php?height=300&width=730&modal=true&resourceID=<?php echo $resourceID; ?>&resourceAcquisitionID=<?php echo $resourceAcquisitionID; ?>' class='thickbox'><?php echo _("edit cataloging details");?></a><br />
<?php } ?>

<br />

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
<table class='linedFormTable'>
	<tr>
	<th><?php echo _("Additional Notes");?></th>
	<th>

	<?php if ($user->canEdit()){?>
	<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo "<div class='addIconTab'><img id='Add' class='addIcon' src='images/plus.gif' title= '"._("Add")."' /></div>";?></a>
	<?php } ?>
	</th>
	</tr>
	<?php foreach ($noteArray as $resourceNote){ ?>
		<tr>
		<td style='width:130px;'><?php echo $resourceNote['noteTypeName']; ?><br />
			<?php if ($user->canEdit()){?>
			<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=<?php echo $resourceNote['resourceNoteID']; ?>&modal=true' class='thickbox'><img src='images/edit.gif' alt='edit' title='<?php echo _("edit note");?>'></a>
			<a href='javascript:void(0);' class='removeNote' id='<?php echo $resourceNote['resourceNoteID']; ?>' tab='Cataloging'><img src='images/cross.gif'  alt='remove note' title='<?php echo _("remove note");?>'></a>
			<?php } ?>
		</td>
		<td><?php echo nl2br($resourceNote['noteText']); ?><br /><i><?php echo format_date($resourceNote['updateDate']) . _(" by ") . $resourceNote['updateUser']; ?></i></td>
		</tr>
	<?php } ?>
</table>
<?php
}else{
if ($user->canEdit()){
?>
	<a href='ajax_forms.php?action=getNoteForm&height=233&width=410&tab=Cataloging&entityID=<?php echo $resourceAcquisitionID; ?>&resourceNoteID=&modal=true' class='thickbox'><?php echo _("add note");?></a>
<?php
}
}
?>
