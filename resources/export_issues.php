<?php
session_start();

include_once 'directory.php';

$resourceID = $_GET['resourceID'];
$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;

$resource = new Resource(new NamedArguments(array('primaryKey' => $resourceID)));
$util = new Utility();

$organizationArray = $resource->getOrganizationArray();

$exportIssues = array();

if (is_array($organizationArray) && count($organizationArray) > 0) {
	$issuedOrgs = array();
	foreach ($organizationArray as $orgData) {
		if (!in_array($orgData['organizationID'],$issuedOrgs)) {
// todo: create issues repo so we don't have to initialize an organization object from the wrong module
			$organization = new Organization(new NamedArguments(array('primaryKey' => $orgData['organizationID'])));

			$exportIssues = array_merge($exportIssues,$organization->getExportableIssues($archivedFlag));
			$issuedOrgs[] = $orgData['organizationID'];
		}
	}
}

$exportIssues = array_merge($exportIssues,$resource->getExportableIssues($archivedFlag));

header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"issues.csv\"");

$out = fopen('php://output', 'w');
if (isset($exportIssues[0])) {
  fputcsv($out, array_keys($exportIssues[0]));
}

foreach ($exportIssues as $issue) {
	fputcsv($out, $issue);
}
fclose($out);

?>
