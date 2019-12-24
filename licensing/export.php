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

$queryDetails = LicenseN::getSearchDetails();
$whereAdd = $queryDetails["where"];
$searchDisplay = $queryDetails["display"];
$orderBy = $queryDetails["order"];

//get the results of the query into an array
$resourceObj = new LicenseN();
$resourceArray = array();
$resourceArray = $resourceObj->export($whereAdd, $orderBy);


$replace = array("/", "-");
$excelfile = "license_export_" . str_replace( $replace, "_", format_date( date( 'Y-m-d' ) ) ).".csv";
header("Pragma: public");
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=\"" . $excelfile . "\"");
$columns = [
  ["header" => _("License ID"),                    "sqlColumn" => "licenseID",                   "getValueFromRow" => function($r) { return $r['licenseID']; }],
  ["header" => _("Name"),                          "sqlColumn" => "shortName",                   "getValueFromRow" => function($r) { return $r['shortName']; }],
  ["header" => _("Consortium Name"),               "sqlColumn" => "conName",                     "getValueFromRow" => function($r) { return $r['conName']; }],
  ["header" => _("Status"),                        "sqlColumn" => "staName",                     "getValueFromRow" => function($r) { return $r['staName']; }],
  ["header" => _("Organization"),                  "sqlColumn" => "orgName",                     "getValueFromRow" => function($r) { return $r['orgName']; }],
  ["header" => _("Document"),                      "sqlColumn" => "docName",                     "getValueFromRow" => function($r) { return $r['docName']; }],
  ["header" => _("Document URL"),                  "sqlColumn" => "documentURL",                 "getValueFromRow" => function($r) { return $r['documentURL']; }],
  ["header" => _("Expession Type"),                "sqlColumn" => "expName",                     "getValueFromRow" => function($r) { return $r['expName']; }],
  ["header" => _("Effective Date"),                "sqlColumn" => "effectiveDate",               "getValueFromRow" => function($r) { return format_date($r['effectiveDate']); }],
  ["header" => _("Date Created"),                  "sqlColumn" => "createDate",                  "getValueFromRow" => function($r) { return format_date($r['createDate']); }],
  ["header" => _("Expiration Date"),               "sqlColumn" => "expirationDate",              "getValueFromRow" => function($r) { return format_date($r['expirationDate']); }]];
 
$availableColumns = array_filter($columns, function($c) use ($resourceArray) {
  return array_key_exists($c["sqlColumn"], $resourceArray[0]);
});
$columnHeaders = array_map(function($c) { return $c["header"]; }, $availableColumns);

echo "# " . _("License Record Export") . " " . format_date( date( 'Y-m-d' )) . "\r\n";
if (!$searchDisplay) {
  $searchDisplay = array(_("All License Records"));
}
echo "# " . implode('; ', $searchDisplay) . "\r\n";
echo array_to_csv_row($columnHeaders);
foreach($resourceArray as $resource) {
  echo array_to_csv_row(array_map(function($column) use ($resource) {
    return $column["getValueFromRow"]($resource);
  }, $availableColumns));
}
?>
