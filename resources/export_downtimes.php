<?php
session_start();

include_once 'directory.php';

$resourceID = $_GET['resourceID'];
$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$util = new Utility();

$organizationArray = $resource->getOrganizationArray();

$exportDowntimes = array();

if (is_array($organizationArray) && count($organizationArray) > 0) {
	$downtimedOrgs = array();
	foreach ($organizationArray as $orgData) {
		if (!in_array($orgData['organizationID'],$downtimedOrgs)) {
// todo: create downtimes repo so we don't have to initialize an organization object from the wrong module
			$organization = new Organization(new NamedArguments(array('primaryKey' => $orgData['organizationID'])));
			$exportDowntimes = array_merge($exportDowntimes,$organization->getExportableDowntimes($archivedFlag));
			$downtimedOrgs[] = $orgData['organizationID'];
		}
	}
}

$exportDowntimes = array_merge($exportDowntimes,$resource->getExportableDowntimes($archivedFlag));

header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"downtimes.csv\"");

$out = fopen('php://output', 'w');
if (isset($exportDowntimes[0])) {
  fputcsv($out, array_keys($exportDowntimes[0]));
}

foreach ($exportDowntimes as $downtime) {
	fputcsv($out, $downtime);
}
fclose($out);

?>
