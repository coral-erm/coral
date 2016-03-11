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
include_once 'util.php';

function escape_csv($value) {
  // replace \n with \r\n
  $value = preg_replace("/(?<!\r)\n/", "\r\n", $value);
  // escape quotes
  $value = str_replace('"', '""', $value);
  return '"'.$value.'"';
}

function array_to_csv_row($array) {
  $escaped_array = array_map("escape_csv", $array);
  return implode(",",$escaped_array)."\r\n";
}

$queryDetails = Resource::getSearchDetails();
$whereAdd = $queryDetails["where"];
$searchDisplay = $queryDetails["display"];
$orderBy = $queryDetails["order"];

//get the results of the query into an array
$resourceObj = new Resource();
$resourceArray = array();
$resourceArray = $resourceObj->export($whereAdd, $orderBy);



$replace = array("/", "-");
$excelfile = "resources_export_" . str_replace( $replace, "_", format_date( date( 'Y-m-d' ) ) ).".csv";

header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"" . $excelfile . "\"");

$columnHeaders = array(
  "Record ID",
  "Name",
  "Type",
  "Format",
  "Date Created",
  "User Created",
  "Date Updated",
  "User Updated",
  "Status",
  "ISSN/ISBN",
  "Resource URL",
  "Alt URL",  
  "Organizations",
  "Aliases",
  "Parent Record",
  "Child Record",
  "Acquisition Type",
  "Cost",
  "Order Number",
  "System Number",
  "Purchasing Sites",
  "Sub Start",
  "Current Sub End",
  "Subscription Alert Enabled",
  "License Names",
  "License Status",
  "Authorized Sites",
  "Administering Sites",
  "Authentication Type",
  "Access Method",
  "Storage Location",
  "Simultaneous User Limit",
  "Coverage",
  "Username",
  "Password",
  "Cataloging Type",
  "Cataloging Status",
  "Catalog Record Set Identifier",
  "Catalog Record Source URL",
  "Catalog Records Available",
  "Catalog Records Loaded",
  "OCLC Holdings Updated"
);

echo array_to_csv_row(array("Resource Record Export " . format_date( date( 'Y-m-d' ))));
if (!$searchDisplay) {
  $searchDisplay = array("All Resource Records");
}
echo array_to_csv_row(array(implode('; ', $searchDisplay)));
echo array_to_csv_row($columnHeaders);

foreach($resourceArray as $resource) {

	$updateDateFormatted=normalize_date($resource['updateDate']);
  $resourceValues = array(
	  $resource['resourceID'],
    $resource['titleText'],
    $resource['resourceType'],
    $resource['resourceFormat'],
    format_date($resource['createDate']),
    $resource['createName'],
    $updateDateFormatted,
    $resource['updateName'],
    $resource['status'],
    $resource['isbnOrISSN'],
    $resource['resourceURL'],
    $resource['resourceAltURL'],	
    $resource['organizationNames'],
    $resource['aliases'],
    $resource['parentResources'],
    $resource['childResources'],
    $resource['acquisitionType'],
    $resource['payments'],
    $resource['orderNumber'],
    $resource['systemNumber'],
    $resource['purchasingSites'],
    $resource['currentStartDate'],
    $resource['currentEndDate'],
    ($resource['subscriptionAlertEnabledInd'] ? 'Y' : 'N'),
    $resource['licenseNames'],
    $resource['licenseStatuses'],
    $resource['authorizedSites'],
    $resource['administeringSites'],
    $resource['authenticationType'],
    $resource['accessMethod'],
    $resource['storageLocation'],
    $resource['userLimit'],
    $resource['coverage'],
    $resource['authenticationUserName'],
    $resource['authenticationPassword'],
    $resource['catalogingType'],
    $resource['catalogingStatus'],
    $resource['recordSetIdentifier'],
    $resource['bibSourceURL'],
    $resource['numberRecordsAvailable'],
    $resource['numberRecordsLoaded'],
    ($resource['hasOclcHoldings'] ? 'Y' : 'N')
  );
	
	echo array_to_csv_row($resourceValues);
}
?>
