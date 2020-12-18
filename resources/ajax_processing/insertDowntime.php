<?php

$newDowntime = new Downtime();

if ($_POST['sourceOrganizationID']) {
	$newDowntime->entityID = $_POST['sourceOrganizationID'];
	$newDowntime->entityTypeID = 1;
} else {
	$newDowntime->entityID = $_POST['sourceResourceID'];
	$newDowntime->resourceAcquisitionID = $_POST['sourceResourceAcquisitionID'];
	$newDowntime->entityTypeID = 2;
}

$newDowntime->creatorID = $user->loginID;
$newDowntime->downtimeTypeID = $_POST['downtimeType'];
$newDowntime->issueID = $_POST['issueID'];

$newDowntime->startDate = date('Y-m-d H:i:s', strtotime(create_date_from_js_format($_POST['startDate'])->format('Y-m-d')." ".$_POST['startTime']['hour'].":".$_POST['startTime']['minute'].$_POST['startTime']['meridian']));
$newDowntime->endDate = ($_POST['endDate']) ?  date('Y-m-d H:i:s',  strtotime(create_date_from_js_format($_POST['endDate'])->format('Y-m-d')." ".$_POST['endTime']['hour'].":".$_POST['endTime']['minute'].$_POST['endTime']['meridian'])) : null;

$newDowntime->dateCreated = date( 'Y-m-d H:i:s');
$newDowntime->note = ($_POST['note']) ? $_POST['note']:null;

$newDowntime->save();

?>
