<?php

include_once '../directory.php';
include_once '../user.php';

$resourceID = $_POST['resourceID'];

//get this resource
$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));

$resource->updateLoginID 		= $loginID;
$resource->updateDate			= date( 'Y-m-d H:i:s' );

$_POST['hasOclcHoldings'] = intval($_POST['hasOclcHoldings']);

foreach (array('bibSourceURL','catalogingStatusID','catalogingTypeID','numberRecordsAvailable','numberRecordsLoaded','recordSetIdentifier','hasOclcHoldings') as $field) {
  $resource->$field = $_POST[$field];
}
//debug($_POST);
try {
	$resource->save();

} catch (Exception $e) {
	echo $e->getMessage();
}

?>