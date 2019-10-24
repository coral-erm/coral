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

function escape_csv($fields)
{
  $f = fopen('php://memory', 'r+');
  if (fputcsv($f, $fields) === false) {
    $failure_string = "Failed to generate csv with row\n".var_export($fields, true);
    throw new Exception($failure_string, 1);
  }
  rewind($f);
  $csv_line = stream_get_contents($f);
  return rtrim($csv_line);
}

function array_to_csv_row($array) {
  return escape_csv($array)."\r\n";
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

$columns = [
  ["header" => _("Record ID"),                     "sqlColumn" => "resourceID",                  "getValueFromRow" => function($r) { return $r['resourceID']; }],
  ["header" => _("Name"),                          "sqlColumn" => "titleText",                   "getValueFromRow" => function($r) { return $r['titleText']; }],
  ["header" => _("Type"),                          "sqlColumn" => "resourceType",                "getValueFromRow" => function($r) { return $r['resourceType']; }],
  ["header" => _("Format"),                        "sqlColumn" => "resourceFormat",              "getValueFromRow" => function($r) { return $r['resourceFormat']; }],
  ["header" => _("Date Created"),                  "sqlColumn" => "createDate",                  "getValueFromRow" => function($r) { return format_date($r['createDate']); }],
  ["header" => _("User Created"),                  "sqlColumn" => "createName",                  "getValueFromRow" => function($r) { return $r['createName']; }],
  ["header" => _("Date Updated"),                  "sqlColumn" => "updateDate",                  "getValueFromRow" => function($r) { return normalize_date($r['updateDate']); }],
  ["header" => _("User Updated"),                  "sqlColumn" => "updateName",                  "getValueFromRow" => function($r) { return $r['updateName']; }],
  ["header" => _("Status"),                        "sqlColumn" => "status",                      "getValueFromRow" => function($r) { return $r['status']; }],
  ["header" => _("ISSN/ISBN"),                     "sqlColumn" => "isbnOrIssn",                  "getValueFromRow" => function($r) { return $r['isbnOrIssn']; }],
  ["header" => _("Resource URL"),                  "sqlColumn" => "resourceURL",                 "getValueFromRow" => function($r) { return $r['resourceURL']; }],
  ["header" => _("Alt URL"),                       "sqlColumn" => "resourceAltURL",              "getValueFromRow" => function($r) { return $r['resourceAltURL']; }],
  ["header" => _("Organizations"),                 "sqlColumn" => "organizationNames",           "getValueFromRow" => function($r) { return $r['organizationNames']; }],
  ["header" => _("Year"),                          "sqlColumn" => "year",                        "getValueFromRow" => function($r) { return $r['year']; }],
  ["header" => _("Fund Name"),                     "sqlColumn" => "fundName",                    "getValueFromRow" => function($r) { return $r['fundName']; }],
  ["header" => _("Fund Code"),                     "sqlColumn" => "fundCode",                    "getValueFromRow" => function($r) { return $r['fundCode']; }],
  ["header" => _("Tax excluded"),                  "sqlColumn" => "priceTaxExcluded",            "getValueFromRow" => function($r) { return $r['priceTaxExcluded']; }],
  ["header" => _("Rate"),                          "sqlColumn" => "taxRate",                     "getValueFromRow" => function($r) { return $r['taxRate']; }],
  ["header" => _("Tax included"),                  "sqlColumn" => "priceTaxIncluded",            "getValueFromRow" => function($r) { return $r['priceTaxIncluded']; }],
  ["header" => _("Payment"),                       "sqlColumn" => "paymentAmount",               "getValueFromRow" => function($r) { return $r['paymentAmount']; }],
  ["header" => _("Currency"),                      "sqlColumn" => "currencyCode",                "getValueFromRow" => function($r) { return $r['currencyCode']; }],
  ["header" => _("Details"),                       "sqlColumn" => "costDetails",                 "getValueFromRow" => function($r) { return $r['costDetails']; }],
  ["header" => _("Order Type"),                    "sqlColumn" => "orderType",                   "getValueFromRow" => function($r) { return $r['orderType']; }],
  ["header" => _("Cost Note"),                     "sqlColumn" => "costNote",                    "getValueFromRow" => function($r) { return $r['costNote']; }],
  ["header" => _("Invoice"),                       "sqlColumn" => "invoiceNum",                  "getValueFromRow" => function($r) { return $r['invoiceNum']; }],
  ["header" => _("Aliases"),                       "sqlColumn" => "aliases",                     "getValueFromRow" => function($r) { return $r['aliases']; }],
  ["header" => _("Parent Record"),                 "sqlColumn" => "parentResources",             "getValueFromRow" => function($r) { return $r['parentResources']; }],
  ["header" => _("Child Record"),                  "sqlColumn" => "childResources",              "getValueFromRow" => function($r) { return $r['childResources']; }],
  ["header" => _("Acquisition Type"),              "sqlColumn" => "acquisitionType",             "getValueFromRow" => function($r) { return $r['acquisitionType']; }],
  ["header" => _("Order Number"),                  "sqlColumn" => "orderNumber",                 "getValueFromRow" => function($r) { return $r['orderNumber']; }],
  ["header" => _("System Number"),                 "sqlColumn" => "systemNumber",                "getValueFromRow" => function($r) { return $r['systemNumber']; }],
  ["header" => _("Purchasing Sites"),              "sqlColumn" => "purchasingSites",             "getValueFromRow" => function($r) { return $r['purchasingSites']; }],
  ["header" => _("Sub Start"),                     "sqlColumn" => "subscriptionStartDate",       "getValueFromRow" => function($r) { return $r['subscriptionStartDate']; }],
  ["header" => _("Current Sub End"),               "sqlColumn" => "subscriptionEndDate",         "getValueFromRow" => function($r) { return $r['subscriptionEndDate']; }],
  ["header" => _("Subscription Alert Enabled"),    "sqlColumn" => "subscriptionAlertEnabledInd", "getValueFromRow" => function($r) { return ($r['subscriptionAlertEnabledInd'] ? 'Y' : 'N'); }],
  ["header" => _("License Names"),                 "sqlColumn" => "licenseNames",                "getValueFromRow" => function($r) { return $r['licenseNames']; }],
  ["header" => _("License Status"),                "sqlColumn" => "licenseStatuses",             "getValueFromRow" => function($r) { return $r['licenseStatuses']; }],
  ["header" => _("Authorized Sites"),              "sqlColumn" => "authorizedSites",             "getValueFromRow" => function($r) { return $r['authorizedSites']; }],
  ["header" => _("Administering Sites"),           "sqlColumn" => "administeringSites",          "getValueFromRow" => function($r) { return $r['administeringSites']; }],
  ["header" => _("Authentication Type"),           "sqlColumn" => "authenticationType",          "getValueFromRow" => function($r) { return $r['authenticationType']; }],
  ["header" => _("Access Method"),                 "sqlColumn" => "accessMethod",                "getValueFromRow" => function($r) { return $r['accessMethod']; }],
  ["header" => _("Storage Location"),              "sqlColumn" => "storageLocation",             "getValueFromRow" => function($r) { return $r['storageLocation']; }],
  ["header" => _("Simultaneous User Limit"),       "sqlColumn" => "userLimit",                   "getValueFromRow" => function($r) { return $r['userLimit']; }],
  ["header" => _("Coverage"),                      "sqlColumn" => "coverageText",                "getValueFromRow" => function($r) { return $r['coverageText']; }],
  ["header" => _("Username"),                      "sqlColumn" => "authenticationUserName",      "getValueFromRow" => function($r) { return $r['authenticationUserName']; }],
  ["header" => _("Password"),                      "sqlColumn" => "authenticationPassword",      "getValueFromRow" => function($r) { return $r['authenticationPassword']; }],
  ["header" => _("Cataloging Type"),               "sqlColumn" => "catalogingType",              "getValueFromRow" => function($r) { return $r['catalogingType']; }],
  ["header" => _("Cataloging Status"),             "sqlColumn" => "catalogingStatus",            "getValueFromRow" => function($r) { return $r['catalogingStatus']; }],
  ["header" => _("Catalog Record Set Identifier"), "sqlColumn" => "recordSetIdentifier",         "getValueFromRow" => function($r) { return $r['recordSetIdentifier']; }],
  ["header" => _("Catalog Record Source URL"),     "sqlColumn" => "bibSourceURL",                "getValueFromRow" => function($r) { return $r['bibSourceURL']; }],
  ["header" => _("Catalog Records Available"),     "sqlColumn" => "numberRecordsAvailable",      "getValueFromRow" => function($r) { return $r['numberRecordsAvailable']; }],
  ["header" => _("Catalog Records Loaded"),        "sqlColumn" => "numberRecordsLoaded",         "getValueFromRow" => function($r) { return $r['numberRecordsLoaded']; }],
  ["header" => _("OCLC Holdings Updated"),         "sqlColumn" => "hasOclcHoldings",             "getValueFromRow" => function($r) { return ($r['hasOclcHoldings'] ? 'Y' : 'N'); }],
  ["header" => _("Resource Notes"),                "sqlColumn" => "resourceNotes",               "getValueFromRow" => function($r) { return $r['resourceNotes']; }],
  ["header" => _("Acquisition Notes"),             "sqlColumn" => "acquisitionNotes",            "getValueFromRow" => function($r) { return $r['acquisitionNotes']; }]
];
$availableColumns = array_filter($columns, function($c) use ($resourceArray) {
  return array_key_exists($c["sqlColumn"], $resourceArray[0]);
});
$columnHeaders = array_map(function($c) { return $c["header"]; }, $availableColumns);

echo "# " . _("Resource Record Export") . " " . format_date( date( 'Y-m-d' )) . "\r\n";
if (!$searchDisplay) {
  $searchDisplay = array(_("All Resource Records"));
}
echo "# " . implode('; ', $searchDisplay) . "\r\n";
echo array_to_csv_row($columnHeaders);

foreach($resourceArray as $resource) {
  echo array_to_csv_row(array_map(function($column) use ($resource) {
    return $column["getValueFromRow"]($resource);
  }, $availableColumns));
}
?>
