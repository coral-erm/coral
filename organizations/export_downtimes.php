<?php

session_start();

include_once 'directory.php';

$organizationID = $_GET['organizationID'];
$archivedFlag = (!empty($_GET['archived']) && $_GET['archived'] == 1) ? true:false;

$organization = new Organization(new NamedArguments(array('primaryKey' => $organizationID)));

$exportDowntimes = array();


$exportDowntimes = $organization->getExportableDowntimes($archivedFlag);

header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"downtimes.csv\"");

if (is_array($exportDowntimes) && count($exportDowntimes) > 0) {
	$out = fopen('php://output', 'w');

	fputcsv($out,array_keys($exportDowntimes[0]));

	foreach ($exportDowntimes as $issue) {
		fputcsv($out, $issue);
	}
	fclose($out);
}

?>